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
 * Privacy provider for assignsubmission_byblos.
 *
 * This subplugin does not own its own user-data table. The selection a student
 * makes (page or collection, snapshot mode, peer-review state) is persisted in
 * the local_byblos_submission table, which is owned by local_byblos and has
 * its own privacy provider that handles export and deletion.
 *
 * For privacy purposes we therefore:
 *   - declare local_byblos as a linked plugin for this submission data;
 *   - export a small pointer/summary on a per-submission basis, so a learner's
 *     export shows that "for this assignment, Byblos page X / collection Y was
 *     submitted" alongside the rest of mod_assign export data;
 *   - on deletion, remove the local_byblos_submission rows tied to the
 *     assignment / submission / userid being deleted. The portfolio content
 *     itself (pages, collections, artefacts) is retained — it belongs to the
 *     learner, not to the assignment, and is handled by local_byblos's own
 *     privacy provider.
 *
 * @package    assignsubmission_byblos
 * @copyright  2026 South African Theological Seminary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_byblos\privacy;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/assign/locallib.php');

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use mod_assign\privacy\assign_plugin_request_data;
use mod_assign\privacy\useridlist;

/**
 * Privacy provider for assignsubmission_byblos.
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \mod_assign\privacy\assignsubmission_provider,
        \mod_assign\privacy\assignsubmission_user_provider {

    /**
     * Describe the personal data this plugin is responsible for.
     *
     * @param collection $collection The metadata collection to add to.
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        // We do not own a database table of our own; we delegate storage of
        // each student's selected portfolio to local_byblos. Declare it as a
        // linked plugin so the user-facing summary makes that relationship
        // explicit in the privacy registry.
        $collection->link_plugin(
            'local_byblos',
            'privacy:metadata:local_byblos'
        );

        return $collection;
    }

    /**
     * Context discovery is handled by mod_assign's privacy provider, which
     * already maps users to the assignment contexts they have submissions in.
     *
     * @param int $userid
     * @param contextlist $contextlist
     */
    public static function get_context_for_userid_within_submission(int $userid, contextlist $contextlist) {
        // No-op: contexts come from mod_assign via assign_submission rows.
    }

    /**
     * mod_assign already enumerates students with submissions; nothing extra
     * to add here.
     *
     * @param useridlist $useridlist
     */
    public static function get_student_user_ids(useridlist $useridlist) {
        // No-op.
    }

    /**
     * mod_assign already enumerates the users in an assignment context via
     * assign_submission. We have no orphan rows that lack an assign_submission
     * parent, so there is nothing to add.
     *
     * @param userlist $userlist
     */
    public static function get_userids_from_context(userlist $userlist) {
        // No-op.
    }

    /**
     * Export the user's Byblos selection for a single assignment submission.
     *
     * @param assign_plugin_request_data $exportdata
     */
    public static function export_submission_user_data(assign_plugin_request_data $exportdata) {
        global $DB;

        // Only export the student's own data (skip teacher-style exports).
        if ($exportdata->get_user() != null) {
            return;
        }

        $submission = $exportdata->get_pluginobject();
        if (empty($submission) || empty($submission->id)) {
            return;
        }

        $byblos = $DB->get_record(
            'local_byblos_submission',
            ['assignsubmissionid' => $submission->id]
        );
        if (!$byblos) {
            // Fall back to the legacy column name used in some early dev rows.
            $byblos = $DB->get_record(
                'local_byblos_submission',
                ['submissionid' => $submission->id]
            );
        }
        if (!$byblos) {
            return;
        }

        $context = $exportdata->get_context();
        $currentpath = $exportdata->get_subcontext();
        $currentpath[] = get_string('privacy:path', 'assignsubmission_byblos');

        $data = (object) [
            'pageid'        => $byblos->pageid ?? null,
            'collectionid'  => $byblos->collectionid ?? null,
            'snapshotmode'  => $byblos->snapshotmode ?? null,
            'snapshotid'    => $byblos->snapshotid ?? null,
            'timecreated'   => $byblos->timecreated ?? null,
            'timemodified'  => $byblos->timemodified ?? null,
        ];

        writer::with_context($context)->export_data($currentpath, $data);
    }

    /**
     * Delete every byblos submission row tied to a context (i.e. an assignment
     * being wiped). Portfolio pages and collections themselves are retained;
     * only the assignment-pointer row is removed.
     *
     * @param assign_plugin_request_data $requestdata
     */
    public static function delete_submission_for_context(assign_plugin_request_data $requestdata) {
        global $DB;

        $assignid = $requestdata->get_assignid();
        if (empty($assignid)) {
            return;
        }

        $DB->delete_records('local_byblos_submission', ['assignmentid' => $assignid]);
    }

    /**
     * Delete a single user's byblos submission for the given assignment.
     *
     * @param assign_plugin_request_data $deletedata
     */
    public static function delete_submission_for_userid(assign_plugin_request_data $deletedata) {
        global $DB;

        $assignid = $deletedata->get_assignid();
        $user = $deletedata->get_user();
        if (empty($assignid) || empty($user) || empty($user->id)) {
            return;
        }

        $DB->delete_records(
            'local_byblos_submission',
            [
                'assignmentid' => $assignid,
                'userid'       => $user->id,
            ]
        );
    }

    /**
     * Bulk-delete byblos submission rows for a list of users / submissions.
     *
     * @param assign_plugin_request_data $deletedata
     */
    public static function delete_submissions(assign_plugin_request_data $deletedata) {
        global $DB;

        $assignid = $deletedata->get_assignid();
        if (empty($assignid)) {
            return;
        }

        $userids = $deletedata->get_userids();
        if (!empty($userids)) {
            [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
            $params['assignid'] = $assignid;
            $DB->delete_records_select(
                'local_byblos_submission',
                "assignmentid = :assignid AND userid {$insql}",
                $params
            );
        }
    }
}
