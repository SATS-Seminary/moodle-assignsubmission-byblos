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
 * Strings for assignsubmission_byblos.
 *
 * @package    assignsubmission_byblos
 * @copyright  2026 South African Theological Seminary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Byblos portfolio';
$string['enabled'] = 'Byblos portfolio';
$string['enabled_help'] = 'If enabled, students can submit a Byblos portfolio page or collection for this assignment.';
$string['default'] = 'Enabled by default';
$string['default_help'] = 'If set, this submission method will be enabled by default for all new assignments.';

// Assignment-level settings.
$string['allowedunit'] = 'Accepted submission type';
$string['allowedunit_help'] = 'Restrict what students can submit for this assignment.';
$string['unit_page'] = 'Single page';
$string['unit_collection'] = 'Collection';
$string['unit_either'] = 'Page or collection';

$string['snapshotmode'] = 'Snapshot mode';
$string['snapshotmode_help'] = 'Controls whether the submitted portfolio is frozen at submit time or stays live.
<ul>
<li><strong>Snapshot on submit</strong>: an immutable copy is captured when the student submits. The teacher grades the frozen version, even if the student later edits the live page.</li>
<li><strong>Live</strong>: the teacher always sees the current state of the student\'s page. The student can edit during grading.</li>
<li><strong>Live until locked</strong>: behaves as live while the student can still edit. When submissions are locked (e.g. after the deadline), the system freezes a snapshot automatically.</li>
</ul>';
$string['mode_snapshot_on_submit'] = 'Snapshot on submit';
$string['mode_live'] = 'Live';
$string['mode_live_until_locked'] = 'Live until locked';

$string['peerenabled'] = 'Enable peer review';
$string['peerenabled_help'] = 'If enabled, students will review each other\'s submitted portfolios. Peer comments are advisory; faculty still award the final grade.';
$string['peermode'] = 'Peer assignment mode';
$string['peermode_help'] = 'How reviewers are allocated to submissions.';
$string['peermode_manual'] = 'Teacher assigns manually';
$string['peermode_random'] = 'Random reviewers';
$string['peermode_group'] = 'Within groups';
$string['peercount'] = 'Reviewers per student';
$string['peercount_help'] = 'Only used with random-assignment mode.';

$string['peervisibility'] = 'Peer comments visible to reviewee';
$string['peervisibility_help'] = 'Controls when students can see peer feedback on their own submission.
<ul>
<li><strong>After reviewer submits</strong>: each peer comment shows up as soon as the reviewer marks their review complete.</li>
<li><strong>When grades are released</strong>: peer comments are withheld until the teacher releases the grade.</li>
<li><strong>Teacher only</strong>: peer comments are never shown to the reviewee — only the teacher sees them.</li>
</ul>';
$string['visibility_after_submit'] = 'After reviewer submits';
$string['visibility_on_grade_release'] = 'When grades are released';
$string['visibility_teacher_only'] = 'Teacher only';

$string['peerscoremode'] = 'Peer scoring mode';
$string['score_none'] = 'No score (comments only)';
$string['score_numeric'] = 'Numeric (0–100)';
$string['score_stars'] = 'Star rating (1–5)';
$string['score_rubric'] = 'Rubric';

$string['manage_peer_reviewers'] = 'Manage peer reviewers';

$string['checklist'] = 'Assessment checklist';
$string['checklist_help'] = 'Optional guidance shown to students while editing. One item per line. Not enforced at submit time.';

// Submission form.
$string['pickpageorcollection'] = 'Choose a portfolio to submit';
$string['nothingtopick'] = 'You don\'t have any portfolios to submit yet. Create one in Byblos first.';
$string['chooseone'] = '— Choose —';
$string['submissiontype_page'] = 'Page: {$a}';
$string['submissiontype_collection'] = 'Collection: {$a}';

// Summary and view.
$string['nosubmission'] = 'No portfolio selected.';
$string['viewsubmission'] = 'Open portfolio';
$string['snapshottaken'] = 'Snapshot captured {$a}';
$string['livereference'] = 'Live reference (no snapshot)';

// Privacy.
$string['privacy:metadata'] = 'The Byblos portfolio submission plugin does not store personal data itself; it stores a pointer to a portfolio managed by the local_byblos plugin.';
