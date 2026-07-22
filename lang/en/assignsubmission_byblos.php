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

$string['allowedunit'] = 'Accepted submission type';
$string['allowedunit_help'] = 'Restrict what students can submit for this assignment.';
$string['byblos:configure'] = 'Configure the Byblos portfolio submission method for an assignment';
$string['byblos:peer_review'] = 'Act as a peer reviewer for another student\'s Byblos submission';
$string['byblos:submitportfolio'] = 'Submit a Byblos portfolio against an assignment';
$string['byblos:viewportfolio'] = 'View a submitted Byblos portfolio';
$string['checklist'] = 'Assessment checklist';
$string['checklist_help'] = 'Optional guidance shown to students while editing. One item per line. Not enforced at submit time.';
$string['chooseone'] = '— Choose —';
$string['default'] = 'Enabled by default';
$string['default_help'] = 'If set, this submission method will be enabled by default for all new assignments.';
$string['enabled'] = 'Byblos portfolio';
$string['enabled_help'] = 'If enabled, students can submit a Byblos portfolio page or collection for this assignment.';
$string['event_peer_comment_posted'] = 'Peer comment posted';
$string['event_peer_review_assigned'] = 'Peer reviewer assigned';
$string['event_peer_review_completed'] = 'Peer review completed';
$string['event_snapshot_captured'] = 'Snapshot captured';
$string['event_submission_created'] = 'Byblos submission created';
$string['event_submission_updated'] = 'Byblos submission updated';
$string['livereference'] = 'Live reference (no snapshot)';
$string['manage_peer_reviewers'] = 'Manage peer reviewers';
$string['mode_live'] = 'Live';
$string['mode_live_until_locked'] = 'Live until locked';
$string['mode_snapshot_on_submit'] = 'Snapshot on submit';
$string['nosubmission'] = 'No portfolio selected.';
$string['nothingtopick'] = 'You don\'t have any portfolios to submit yet. Create one in Byblos first.';
$string['peercount'] = 'Reviewers per student';
$string['peercount_help'] = 'Only used with random-assignment mode.';
$string['peerenabled'] = 'Enable peer review';
$string['peerenabled_help'] = 'If enabled, students will review each other\'s submitted portfolios. Peer comments are advisory; faculty still award the final grade.';
$string['peermode'] = 'Peer assignment mode';
$string['peermode_group'] = 'Within groups';
$string['peermode_help'] = 'How reviewers are allocated to submissions.';
$string['peermode_manual'] = 'Teacher assigns manually';
$string['peermode_random'] = 'Random reviewers';
$string['peerscoremode'] = 'Peer scoring mode';
$string['peervisibility'] = 'Peer comments visible to reviewee';
$string['peervisibility_help'] = 'Controls when students can see peer feedback on their own submission.
<ul>
<li><strong>After reviewer submits</strong>: each peer comment shows up as soon as the reviewer marks their review complete.</li>
<li><strong>When grades are released</strong>: peer comments are withheld until the teacher releases the grade.</li>
<li><strong>Teacher only</strong>: peer comments are never shown to the reviewee — only the teacher sees them.</li>
</ul>';
$string['pickpageorcollection'] = 'Choose a portfolio to submit';
$string['pluginname'] = 'Byblos portfolio';
$string['privacy:metadata'] = 'The Byblos portfolio submission plugin does not store personal data itself; it stores a pointer to a portfolio managed by the local_byblos plugin.';
$string['privacy:metadata:local_byblos'] = 'When a student submits a portfolio for an assignment, the Byblos portfolio submission plugin stores the selection (page or collection, snapshot mode, peer-review state) in the local_byblos_submission table owned by local_byblos.';
$string['privacy:metadata:local_byblos_submission:assignmentid'] = 'The assignment the portfolio was submitted to.';
$string['privacy:metadata:local_byblos_submission:collectionid'] = 'The portfolio collection the student submitted, if a collection was chosen.';
$string['privacy:metadata:local_byblos_submission:pageid'] = 'The portfolio page the student submitted, if a single page was chosen.';
$string['privacy:metadata:local_byblos_submission:snapshotid'] = 'The frozen copy of the portfolio captured for grading, if one was taken.';
$string['privacy:metadata:local_byblos_submission:snapshotmode'] = 'Whether the submitted portfolio is frozen at submit time or stays live.';
$string['privacy:metadata:local_byblos_submission:timecreated'] = 'The time the portfolio was first submitted to this assignment.';
$string['privacy:metadata:local_byblos_submission:timemodified'] = 'The time the submitted portfolio selection was last changed.';
$string['privacy:metadata:local_byblos_submission:userid'] = 'The user who submitted the portfolio.';
$string['privacy:path'] = 'Byblos portfolio submission';
$string['score_none'] = 'No score (comments only)';
$string['score_numeric'] = 'Numeric (0–100)';
$string['score_rubric'] = 'Rubric';
$string['score_stars'] = 'Star rating (1–5)';
$string['snapshotmode'] = 'Snapshot mode';
$string['snapshotmode_help'] = 'Controls whether the submitted portfolio is frozen at submit time or stays live.
<ul>
<li><strong>Snapshot on submit</strong>: an immutable copy is captured when the student submits. The teacher grades the frozen version, even if the student later edits the live page.</li>
<li><strong>Live</strong>: the teacher always sees the current state of the student\'s page. The student can edit during grading.</li>
<li><strong>Live until locked</strong>: behaves as live while the student can still edit. When submissions are locked (e.g. after the deadline), the system freezes a snapshot automatically.</li>
</ul>';
$string['snapshottaken'] = 'Snapshot captured {$a}';
$string['submissiontype_collection'] = 'Collection: {$a}';
$string['submissiontype_page'] = 'Page: {$a}';
$string['unit_collection'] = 'Collection';
$string['unit_either'] = 'Page or collection';
$string['unit_page'] = 'Single page';
$string['viewsubmission'] = 'Open portfolio';
$string['visibility_after_submit'] = 'After reviewer submits';
$string['visibility_on_grade_release'] = 'When grades are released';
$string['visibility_teacher_only'] = 'Teacher only';
