<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Byblos portfolio submission plugin.
 *
 * Lets students attach a Byblos portfolio page or collection to an assignment
 * submission. Per-assignment settings control what they may submit, whether
 * the submission is frozen at submit time, and whether peer review is enabled.
 *
 * @package    assignsubmission_byblos
 * @copyright  2026 South African Theological Seminary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_byblos\page;
use local_byblos\collection;
use local_byblos\submission as byblos_submission;

/**
 * Concrete submission plugin class for Byblos portfolios.
 */
class assign_submission_byblos extends assign_submission_plugin {

    /**
     * Human-readable name (used in the submission types admin + UI).
     */
    public function get_name() {
        return get_string('pluginname', 'assignsubmission_byblos');
    }

    /**
     * Plugin's own settings form (appears under "Submission types" when the
     * teacher edits the assignment).
     *
     * @param MoodleQuickForm $mform
     */
    public function get_settings(MoodleQuickForm $mform) {
        // Accepted submission unit.
        $units = [
            'either'     => get_string('unit_either', 'assignsubmission_byblos'),
            'page'       => get_string('unit_page', 'assignsubmission_byblos'),
            'collection' => get_string('unit_collection', 'assignsubmission_byblos'),
        ];
        $mform->addElement(
            'select',
            'assignsubmission_byblos_allowedunit',
            get_string('allowedunit', 'assignsubmission_byblos'),
            $units
        );
        $mform->setDefault('assignsubmission_byblos_allowedunit', $this->get_config('allowedunit') ?: 'either');
        $mform->addHelpButton(
            'assignsubmission_byblos_allowedunit',
            'allowedunit',
            'assignsubmission_byblos'
        );
        $mform->hideIf('assignsubmission_byblos_allowedunit', 'assignsubmission_byblos_enabled', 'notchecked');

        // Snapshot mode.
        $modes = [
            'snapshot_on_submit' => get_string('mode_snapshot_on_submit', 'assignsubmission_byblos'),
            'live'               => get_string('mode_live', 'assignsubmission_byblos'),
            'live_until_locked'  => get_string('mode_live_until_locked', 'assignsubmission_byblos'),
        ];
        $mform->addElement(
            'select',
            'assignsubmission_byblos_snapshotmode',
            get_string('snapshotmode', 'assignsubmission_byblos'),
            $modes
        );
        $mform->setDefault(
            'assignsubmission_byblos_snapshotmode',
            $this->get_config('snapshotmode') ?: 'snapshot_on_submit'
        );
        $mform->addHelpButton(
            'assignsubmission_byblos_snapshotmode',
            'snapshotmode',
            'assignsubmission_byblos'
        );
        $mform->hideIf('assignsubmission_byblos_snapshotmode', 'assignsubmission_byblos_enabled', 'notchecked');

        // Peer review enable.
        $mform->addElement(
            'advcheckbox',
            'assignsubmission_byblos_peerenabled',
            get_string('peerenabled', 'assignsubmission_byblos')
        );
        $mform->setDefault('assignsubmission_byblos_peerenabled', (int) $this->get_config('peerenabled'));
        $mform->addHelpButton(
            'assignsubmission_byblos_peerenabled',
            'peerenabled',
            'assignsubmission_byblos'
        );
        $mform->hideIf('assignsubmission_byblos_peerenabled', 'assignsubmission_byblos_enabled', 'notchecked');

        // Peer review mode.
        $peermodes = [
            'manual' => get_string('peermode_manual', 'assignsubmission_byblos'),
            'random' => get_string('peermode_random', 'assignsubmission_byblos'),
            'group'  => get_string('peermode_group', 'assignsubmission_byblos'),
        ];
        $mform->addElement(
            'select',
            'assignsubmission_byblos_peermode',
            get_string('peermode', 'assignsubmission_byblos'),
            $peermodes
        );
        $mform->setDefault('assignsubmission_byblos_peermode', $this->get_config('peermode') ?: 'manual');
        $mform->addHelpButton('assignsubmission_byblos_peermode', 'peermode', 'assignsubmission_byblos');
        $mform->hideIf('assignsubmission_byblos_peermode', 'assignsubmission_byblos_peerenabled', 'notchecked');

        // Peer reviewer count (random mode only).
        $mform->addElement(
            'text',
            'assignsubmission_byblos_peercount',
            get_string('peercount', 'assignsubmission_byblos'),
            ['size' => 4, 'maxlength' => 3]
        );
        $mform->setType('assignsubmission_byblos_peercount', PARAM_INT);
        $mform->setDefault('assignsubmission_byblos_peercount', $this->get_config('peercount') ?: 2);
        $mform->addHelpButton('assignsubmission_byblos_peercount', 'peercount', 'assignsubmission_byblos');
        $mform->hideIf('assignsubmission_byblos_peercount', 'assignsubmission_byblos_peermode', 'neq', 'random');
        $mform->hideIf('assignsubmission_byblos_peercount', 'assignsubmission_byblos_peerenabled', 'notchecked');

        // Peer comment visibility to the reviewee.
        $visibilities = [
            'after_submit'      => get_string('visibility_after_submit', 'assignsubmission_byblos'),
            'on_grade_release'  => get_string('visibility_on_grade_release', 'assignsubmission_byblos'),
            'teacher_only'      => get_string('visibility_teacher_only', 'assignsubmission_byblos'),
        ];
        $mform->addElement(
            'select',
            'assignsubmission_byblos_peervisibility',
            get_string('peervisibility', 'assignsubmission_byblos'),
            $visibilities
        );
        $mform->setDefault(
            'assignsubmission_byblos_peervisibility',
            $this->get_config('peervisibility') ?: 'after_submit'
        );
        $mform->addHelpButton(
            'assignsubmission_byblos_peervisibility',
            'peervisibility',
            'assignsubmission_byblos'
        );
        $mform->hideIf('assignsubmission_byblos_peervisibility', 'assignsubmission_byblos_peerenabled', 'notchecked');

        // Peer scoring mode.
        $scoremodes = [
            'none'    => get_string('score_none', 'assignsubmission_byblos'),
            'numeric' => get_string('score_numeric', 'assignsubmission_byblos'),
            'stars'   => get_string('score_stars', 'assignsubmission_byblos'),
            'rubric'  => get_string('score_rubric', 'assignsubmission_byblos'),
        ];
        $mform->addElement(
            'select',
            'assignsubmission_byblos_peerscoremode',
            get_string('peerscoremode', 'assignsubmission_byblos'),
            $scoremodes
        );
        $mform->setDefault(
            'assignsubmission_byblos_peerscoremode',
            $this->get_config('peerscoremode') ?: 'numeric'
        );
        $mform->hideIf('assignsubmission_byblos_peerscoremode', 'assignsubmission_byblos_peerenabled', 'notchecked');

        // Checklist text.
        $mform->addElement(
            'textarea',
            'assignsubmission_byblos_checklist',
            get_string('checklist', 'assignsubmission_byblos'),
            ['rows' => 4, 'cols' => 60]
        );
        $mform->setType('assignsubmission_byblos_checklist', PARAM_RAW);
        $mform->setDefault('assignsubmission_byblos_checklist', $this->get_config('checklist') ?: '');
        $mform->addHelpButton('assignsubmission_byblos_checklist', 'checklist', 'assignsubmission_byblos');
        $mform->hideIf('assignsubmission_byblos_checklist', 'assignsubmission_byblos_enabled', 'notchecked');
    }

    /**
     * Persist per-assignment settings.
     *
     * @param stdClass $data Submitted form data.
     * @return bool
     */
    public function save_settings(stdClass $data) {
        $this->set_config('allowedunit',  $data->assignsubmission_byblos_allowedunit ?? 'either');
        $this->set_config('snapshotmode', $data->assignsubmission_byblos_snapshotmode ?? 'snapshot_on_submit');
        $this->set_config('peerenabled',  !empty($data->assignsubmission_byblos_peerenabled) ? 1 : 0);
        $this->set_config('peermode',     $data->assignsubmission_byblos_peermode ?? 'manual');
        $this->set_config('peercount',    (int) ($data->assignsubmission_byblos_peercount ?? 2));
        $this->set_config(
            'peervisibility',
            $data->assignsubmission_byblos_peervisibility ?? 'after_submit'
        );
        $this->set_config(
            'peerscoremode',
            $data->assignsubmission_byblos_peerscoremode ?? 'numeric'
        );
        $this->set_config('checklist',    $data->assignsubmission_byblos_checklist ?? '');
        return true;
    }

    /**
     * Add the submission form elements for the student.
     *
     * @param mixed           $submission The assign_submission record (or null for first-time).
     * @param MoodleQuickForm $mform      Form to extend.
     * @param stdClass        $data       Pre-fill values.
     * @return bool true if any elements were added.
     */
    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data) {
        global $USER;

        $allowed = $this->get_config('allowedunit') ?: 'either';

        $pageopts = [];
        if ($allowed === 'page' || $allowed === 'either') {
            foreach (page::list_by_user((int) $USER->id) as $p) {
                $pageopts['page:' . $p->id] = get_string(
                    'submissiontype_page',
                    'assignsubmission_byblos',
                    format_string($p->title)
                );
            }
        }
        $collopts = [];
        if ($allowed === 'collection' || $allowed === 'either') {
            foreach (collection::list_by_user((int) $USER->id) as $c) {
                $collopts['collection:' . $c->id] = get_string(
                    'submissiontype_collection',
                    'assignsubmission_byblos',
                    format_string($c->title)
                );
            }
        }

        if (empty($pageopts) && empty($collopts)) {
            $mform->addElement(
                'static',
                'assignsubmission_byblos_nothing',
                get_string('pickpageorcollection', 'assignsubmission_byblos'),
                get_string('nothingtopick', 'assignsubmission_byblos')
            );
            return true;
        }

        $options = ['' => get_string('chooseone', 'assignsubmission_byblos')];
        if (!empty($pageopts)) {
            $options = $options + $pageopts;
        }
        if (!empty($collopts)) {
            $options = $options + $collopts;
        }

        $mform->addElement(
            'select',
            'assignsubmission_byblos_choice',
            get_string('pickpageorcollection', 'assignsubmission_byblos'),
            $options
        );

        // Prefill with existing choice if any.
        if ($submission && !empty($submission->id)) {
            $existing = byblos_submission::get_by_assign_submission((int) $submission->id);
            if ($existing) {
                if ($existing->pageid) {
                    $mform->setDefault('assignsubmission_byblos_choice', 'page:' . $existing->pageid);
                } else if ($existing->collectionid) {
                    $mform->setDefault('assignsubmission_byblos_choice', 'collection:' . $existing->collectionid);
                }
            }
        }

        return true;
    }

    /**
     * Save a student's submission.
     *
     * @param stdClass $submission The assign_submission record.
     * @param stdClass $data       Form data.
     * @return bool
     */
    public function save(stdClass $submission, stdClass $data) {
        global $USER;

        $choice = $data->assignsubmission_byblos_choice ?? '';
        if (!$choice) {
            return true; // Allow empty saves; is_empty will guard final submission.
        }

        [$kind, $idstr] = array_pad(explode(':', $choice, 2), 2, '');
        $id = (int) $idstr;

        $pageid = $kind === 'page' ? $id : null;
        $collectionid = $kind === 'collection' ? $id : null;
        if (!$pageid && !$collectionid) {
            return true;
        }

        // Ownership check for the chosen page/collection.
        if ($pageid) {
            $p = page::get($pageid);
            if (!$p || (int) $p->userid !== (int) $USER->id) {
                return false;
            }
        } else if ($collectionid) {
            $c = collection::get($collectionid);
            if (!$c || (int) $c->userid !== (int) $USER->id) {
                return false;
            }
        }

        $snapshotmode = $this->get_config('snapshotmode') ?: 'snapshot_on_submit';
        $assignid = $this->assignment->get_instance()->id;

        $subid = byblos_submission::upsert(
            (int) $assignid,
            (int) $submission->id,
            (int) $USER->id,
            $pageid,
            $collectionid,
            $snapshotmode,
        );

        // For snapshot_on_submit, capture immediately; otherwise defer.
        byblos_submission::capture_snapshot_if_needed($subid, false);

        // If the reviewee later submits and peer reviewers were pre-assigned, attach the submission id.
        \local_byblos\peer::attach_submission((int) $assignid, (int) $USER->id, $subid);

        return true;
    }

    /**
     * Short summary shown in the grading list / submission view page.
     *
     * @param stdClass $submission
     * @param bool     $showviewlink Set to true if this plugin wants a "View submission" link.
     * @return string HTML fragment.
     */
    public function view_summary(stdClass $submission, & $showviewlink) {
        $byblos = byblos_submission::get_by_assign_submission((int) $submission->id);
        if (!$byblos || (!$byblos->pageid && !$byblos->collectionid)) {
            return get_string('nosubmission', 'assignsubmission_byblos');
        }

        $showviewlink = true;

        if ($byblos->pageid) {
            $p = page::get((int) $byblos->pageid);
            $title = $p ? format_string($p->title) : '#' . $byblos->pageid;
            $label = get_string('submissiontype_page', 'assignsubmission_byblos', $title);
        } else {
            $c = collection::get((int) $byblos->collectionid);
            $title = $c ? format_string($c->title) : '#' . $byblos->collectionid;
            $label = get_string('submissiontype_collection', 'assignsubmission_byblos', $title);
        }

        $extra = '';
        if ($byblos->snapshotid) {
            $extra = ' <span class="text-muted small">(' .
                get_string('snapshottaken', 'assignsubmission_byblos', userdate((int) $byblos->timemodified)) .
                ')</span>';
        } else if ($byblos->snapshotmode === 'live') {
            $extra = ' <span class="text-muted small">(' .
                get_string('livereference', 'assignsubmission_byblos') . ')</span>';
        }

        return s($label) . $extra;
    }

    /**
     * Full view of the submission on the grading screen.
     *
     * @param stdClass $submission
     * @return string HTML fragment.
     */
    public function view(stdClass $submission) {
        $url = $this->get_portfolio_url($submission);
        if ($url === null) {
            return get_string('nosubmission', 'assignsubmission_byblos');
        }
        $html = html_writer::link(
            $url,
            get_string('viewsubmission', 'assignsubmission_byblos'),
            [
                'class'  => 'btn btn-outline-primary btn-sm',
                'target' => '_blank',
            ]
        );

        // If peer review is enabled and caller can grade, expose the management page.
        if ((int) $this->get_config('peerenabled') === 1) {
            $assignid = $this->assignment->get_instance()->id;
            $cm = $this->assignment->get_course_module();
            if ($cm) {
                $ctx = \context_module::instance($cm->id);
                if (has_capability('mod/assign:grade', $ctx)) {
                    $manageurl = new moodle_url(
                        '/local/byblos/peerassign.php',
                        ['assignmentid' => (int) $assignid]
                    );
                    $html .= ' ' . html_writer::link(
                        $manageurl,
                        get_string('manage_peer_reviewers', 'assignsubmission_byblos'),
                        ['class' => 'btn btn-outline-secondary btn-sm']
                    );
                }
            }
        }

        return html_writer::tag('p', $html);
    }

    /**
     * Return the public URL for viewing the portfolio tied to an assign submission.
     *
     * This is the canonical integration point for other plugins (e.g. grading
     * interfaces that want to embed the portfolio in an iframe). Returns null
     * when the student has not yet linked a byblos page/collection.
     *
     * @param stdClass $submission The assign_submission record.
     * @param array    $extra      Extra query params to merge into the URL. Common values:
     *                             - 'embedded' => 1  (minimal chrome, iframe-friendly)
     * @return moodle_url|null
     */
    public function get_portfolio_url(stdClass $submission, array $extra = []): ?moodle_url {
        return self::url_for_assign_submission((int) $submission->id, $extra);
    }

    /**
     * Static variant: build the portfolio URL from a raw mdl_assign_submission.id.
     *
     * Useful for callers that have the ID but no plugin instance (e.g., external
     * renderers, AJAX endpoints). Same null semantics as get_portfolio_url().
     *
     * @param int   $assignsubmissionid  mdl_assign_submission.id
     * @param array $extra               Extra query params (e.g. ['embedded' => 1]).
     * @return moodle_url|null
     */
    public static function url_for_assign_submission(int $assignsubmissionid, array $extra = []): ?moodle_url {
        $byblos = byblos_submission::get_by_assign_submission($assignsubmissionid);
        if (!$byblos || empty($byblos->id)) {
            return null;
        }
        $params = array_merge(['submissionid' => (int) $byblos->id], $extra);
        return new moodle_url('/local/byblos/review.php', $params);
    }

    /**
     * Is the submission empty (no page/collection chosen)?
     *
     * @param stdClass $submission
     * @return bool
     */
    public function is_empty(stdClass $submission) {
        $byblos = byblos_submission::get_by_assign_submission((int) $submission->id);
        return empty($byblos) || (empty($byblos->pageid) && empty($byblos->collectionid));
    }

    /**
     * Submission form "is empty" check for the student side (post data, pre-save).
     *
     * @param stdClass $data
     * @return bool
     */
    public function submission_is_empty(stdClass $data) {
        return empty($data->assignsubmission_byblos_choice);
    }

    /**
     * Called by mod_assign when a student's submission is locked (e.g. after deadline).
     * Trigger snapshot capture for live_until_locked submissions.
     *
     * @param mixed    $submission
     * @param stdClass $flags
     */
    public function lock($submission, stdClass $flags) {
        if (!$submission || empty($submission->id)) {
            return;
        }
        $byblos = byblos_submission::get_by_assign_submission((int) $submission->id);
        if ($byblos) {
            byblos_submission::capture_snapshot_if_needed((int) $byblos->id, true);
        }
    }

    /**
     * Remove byblos submission data when mod_assign deletes the submission.
     *
     * @param stdClass $submission
     */
    public function remove(stdClass $submission) {
        if (!$submission || empty($submission->id)) {
            return;
        }
        $byblos = byblos_submission::get_by_assign_submission((int) $submission->id);
        if ($byblos) {
            byblos_submission::delete((int) $byblos->id);
        }
    }

    /**
     * File areas — we manage files indirectly via local_byblos.
     *
     * @return array
     */
    public function get_file_areas() {
        return [];
    }

    /**
     * Copy submission data when a new attempt is opened.
     *
     * @param stdClass $sourcesubmission Previous attempt.
     * @param stdClass $destsubmission   New attempt.
     * @return bool
     */
    public function copy_submission(stdClass $sourcesubmission, stdClass $destsubmission) {
        $src = byblos_submission::get_by_assign_submission((int) $sourcesubmission->id);
        if (!$src) {
            return true;
        }
        byblos_submission::upsert(
            (int) $src->assignmentid,
            (int) $destsubmission->id,
            (int) $destsubmission->userid,
            $src->pageid ? (int) $src->pageid : null,
            $src->collectionid ? (int) $src->collectionid : null,
            $src->snapshotmode,
        );
        return true;
    }
}
