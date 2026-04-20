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
 * Version and metadata for assignsubmission_byblos.
 *
 * @package    assignsubmission_byblos
 * @copyright  2026 South African Theological Seminary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2026041901;
$plugin->requires  = 2024100700;            // Moodle 5.0+.
$plugin->component = 'assignsubmission_byblos';
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.1.0';

// Depends on local_byblos for the submission/snapshot/comment/peer services.
$plugin->dependencies = [
    'local_byblos' => 2026041700,
];
