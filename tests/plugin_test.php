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

namespace assignsubmission_byblos;

/**
 * Sanity tests for assignsubmission_byblos plugin registration and metadata.
 *
 * These tests intentionally avoid calling into the local_byblos service layer
 * so the plugin under test can be CI-tested in isolation when desired. A
 * companion integration suite lives inside local_byblos once both plugins are
 * installed together.
 *
 * @package    assignsubmission_byblos
 * @copyright  2026 South African Theological Seminary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class plugin_test extends \advanced_testcase {

    /**
     * Plugin version metadata is well-formed.
     */
    public function test_plugin_version_metadata(): void {
        global $CFG;
        $plugin = new \stdClass();
        require($CFG->dirroot . '/mod/assign/submission/byblos/version.php');

        $this->assertSame('assignsubmission_byblos', $plugin->component);
        $this->assertGreaterThanOrEqual(2024100700, (int) $plugin->requires);
        $this->assertMatchesRegularExpression('/^\d{10}$/', (string) $plugin->version);
    }

    /**
     * The plugin declares all the lang strings mod_assign enumerates from a
     * subplugin (at minimum: pluginname, default, default_help).
     */
    public function test_required_language_strings_exist(): void {
        $this->assertNotEmpty(get_string('pluginname', 'assignsubmission_byblos'));
        $this->assertNotEmpty(get_string('default', 'assignsubmission_byblos'));
    }
}
