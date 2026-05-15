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
 * Site-level admin settings for assignsubmission_byblos.
 *
 * These provide defaults that can be overridden per-assignment via the
 * submission-types form (see locallib::get_settings()).
 *
 * @package    assignsubmission_byblos
 * @copyright  2026 South African Theological Seminary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Whether the byblos submission type is enabled by default on new assignments.
$settings->add(new admin_setting_configcheckbox(
    'assignsubmission_byblos/default',
    new lang_string('default', 'assignsubmission_byblos'),
    new lang_string('default_help', 'assignsubmission_byblos'),
    0
));

// Default snapshot mode for new assignments.
$snapshotmodes = [
    'snapshot_on_submit' => new lang_string('mode_snapshot_on_submit', 'assignsubmission_byblos'),
    'live'               => new lang_string('mode_live', 'assignsubmission_byblos'),
    'live_until_locked'  => new lang_string('mode_live_until_locked', 'assignsubmission_byblos'),
];
$settings->add(new admin_setting_configselect(
    'assignsubmission_byblos/default_snapshot_mode',
    new lang_string('snapshotmode', 'assignsubmission_byblos'),
    new lang_string('snapshotmode_help', 'assignsubmission_byblos'),
    'snapshot_on_submit',
    $snapshotmodes
));

// Default: enable peer review on new assignments.
$settings->add(new admin_setting_configcheckbox(
    'assignsubmission_byblos/peer_default_enabled',
    new lang_string('peerenabled', 'assignsubmission_byblos'),
    new lang_string('peerenabled_help', 'assignsubmission_byblos'),
    0
));

// Default peer-assignment mode.
$peermodes = [
    'manual' => new lang_string('peermode_manual', 'assignsubmission_byblos'),
    'random' => new lang_string('peermode_random', 'assignsubmission_byblos'),
    'group'  => new lang_string('peermode_group', 'assignsubmission_byblos'),
];
$settings->add(new admin_setting_configselect(
    'assignsubmission_byblos/peer_default_mode',
    new lang_string('peermode', 'assignsubmission_byblos'),
    new lang_string('peermode_help', 'assignsubmission_byblos'),
    'manual',
    $peermodes
));

// Default reviewer count (random mode).
$settings->add(new admin_setting_configtext(
    'assignsubmission_byblos/peer_default_count',
    new lang_string('peercount', 'assignsubmission_byblos'),
    new lang_string('peercount_help', 'assignsubmission_byblos'),
    2,
    PARAM_INT,
    3
));

// Default peer-comment visibility.
$visibilities = [
    'after_submit'     => new lang_string('visibility_after_submit', 'assignsubmission_byblos'),
    'on_grade_release' => new lang_string('visibility_on_grade_release', 'assignsubmission_byblos'),
    'teacher_only'     => new lang_string('visibility_teacher_only', 'assignsubmission_byblos'),
];
$settings->add(new admin_setting_configselect(
    'assignsubmission_byblos/peer_default_visibility',
    new lang_string('peervisibility', 'assignsubmission_byblos'),
    new lang_string('peervisibility_help', 'assignsubmission_byblos'),
    'after_submit',
    $visibilities
));
