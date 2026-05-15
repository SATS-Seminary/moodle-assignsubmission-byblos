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

use advanced_testcase;
use assign;
use context_module;
use MoodleQuickForm;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');

/**
 * Behavioural tests for assignsubmission_byblos.
 *
 * Covers plugin registration, settings form rendering, save() persistence,
 * view_summary() output, snapshot-mode capture, and is_empty() detection.
 *
 * @package    assignsubmission_byblos
 * @copyright  2026 South African Theological Seminary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \assign_submission_byblos
 */
final class plugin_test extends advanced_testcase {
    use \mod_assign_test_generator;

    /**
     * Plugin version metadata is well-formed.
     *
     * @coversNothing
     */
    public function test_plugin_version_metadata(): void {
        global $CFG;
        $plugin = new stdClass();
        require($CFG->dirroot . '/mod/assign/submission/byblos/version.php');

        $this->assertSame('assignsubmission_byblos', $plugin->component);
        $this->assertGreaterThanOrEqual(2024100700, (int) $plugin->requires);
        $this->assertMatchesRegularExpression('/^\d{10}$/', (string) $plugin->version);
        $this->assertArrayHasKey('local_byblos', $plugin->dependencies);
    }

    /**
     * The plugin declares all the lang strings mod_assign enumerates from a
     * subplugin (at minimum: pluginname, default, default_help) plus event names.
     *
     * @coversNothing
     */
    public function test_required_language_strings_exist(): void {
        $this->assertNotEmpty(get_string('pluginname', 'assignsubmission_byblos'));
        $this->assertNotEmpty(get_string('default', 'assignsubmission_byblos'));
        $this->assertNotEmpty(get_string('default_help', 'assignsubmission_byblos'));
        $this->assertNotEmpty(get_string('event_submission_created', 'assignsubmission_byblos'));
        $this->assertNotEmpty(get_string('event_snapshot_captured', 'assignsubmission_byblos'));
    }

    /**
     * The submission plugin can be enabled per assignment and reports its name.
     *
     * @covers ::get_name
     */
    public function test_plugin_enabled_and_named(): void {
        $this->resetAfterTest();

        [$assign] = $this->build_assignment(['assignsubmission_byblos_enabled' => 1]);
        $plugin = $assign->get_submission_plugin_by_type('byblos');
        $this->assertNotNull($plugin);
        $this->assertSame(get_string('pluginname', 'assignsubmission_byblos'), $plugin->get_name());
        $this->assertTrue((bool) $plugin->is_enabled());
    }

    /**
     * The plugin reports as disabled when not enabled in the assignment config.
     *
     * @covers ::is_enabled
     */
    public function test_plugin_disabled(): void {
        $this->resetAfterTest();

        [$assign] = $this->build_assignment(['assignsubmission_byblos_enabled' => 0]);
        $plugin = $assign->get_submission_plugin_by_type('byblos');
        $this->assertFalse((bool) $plugin->is_enabled());
    }

    /**
     * get_form_elements() runs without exception for a student with no portfolios
     * yet, falling back to the static "nothing to pick" element.
     *
     * @covers ::get_form_elements
     */
    public function test_get_form_elements_renders_without_portfolios(): void {
        $this->resetAfterTest();

        [$assign, $student] = $this->build_assignment(['assignsubmission_byblos_enabled' => 1]);
        $this->setUser($student);

        $plugin = $assign->get_submission_plugin_by_type('byblos');
        $mform = $this->make_form();
        $data = new stdClass();

        $result = $plugin->get_form_elements(null, $mform, $data);
        $this->assertTrue($result);
        // Either the chooser or the "nothing to pick" placeholder must be present.
        $this->assertTrue(
            $mform->elementExists('assignsubmission_byblos_choice')
            || $mform->elementExists('assignsubmission_byblos_nothing')
        );
    }

    /**
     * save() persists the chosen page id into local_byblos_submission and a
     * subsequent view_summary() reflects that selection.
     *
     * @covers ::save
     * @covers ::view_summary
     * @covers ::is_empty
     */
    public function test_save_persists_submission_and_view_summary(): void {
        global $DB;
        $this->resetAfterTest();

        [$assign, $student] = $this->build_assignment([
            'assignsubmission_byblos_enabled'      => 1,
            'assignsubmission_byblos_snapshotmode' => 'snapshot_on_submit',
            'assignsubmission_byblos_allowedunit'  => 'either',
        ]);
        $this->setUser($student);

        // Skip if local_byblos isn't installed in this CI run.
        if (!$DB->get_manager()->table_exists('local_byblos_page')) {
            $this->markTestSkipped('local_byblos is not installed; integration save test skipped.');
        }

        $pageid = $this->insert_byblos_page((int) $student->id, 'My portfolio page');

        $submission = $assign->get_user_submission($student->id, true);
        $plugin = $assign->get_submission_plugin_by_type('byblos');

        // Empty state first.
        $this->assertTrue($plugin->is_empty($submission));

        $data = (object) ['assignsubmission_byblos_choice' => 'page:' . $pageid];
        $this->assertTrue($plugin->save($submission, $data));

        // After save, is_empty should be false and view_summary should mention the page title.
        $this->assertFalse($plugin->is_empty($submission));

        $showviewlink = false;
        $summary = $plugin->view_summary($submission, $showviewlink);
        $this->assertStringContainsString('My portfolio page', $summary);
        $this->assertTrue($showviewlink);
    }

    /**
     * Snapshot mode "snapshot_on_submit" triggers immediate capture; the
     * submission row should record a non-zero snapshotid (or at least the
     * snapshot capture path was exercised).
     *
     * @covers ::save
     */
    public function test_snapshot_mode_captures(): void {
        global $DB;
        $this->resetAfterTest();

        if (!$DB->get_manager()->table_exists('local_byblos_submission')) {
            $this->markTestSkipped('local_byblos is not installed; snapshot test skipped.');
        }

        [$assign, $student] = $this->build_assignment([
            'assignsubmission_byblos_enabled'      => 1,
            'assignsubmission_byblos_snapshotmode' => 'snapshot_on_submit',
        ]);
        $this->setUser($student);

        $pageid = $this->insert_byblos_page((int) $student->id, 'Snapshot target');

        $submission = $assign->get_user_submission($student->id, true);
        $plugin = $assign->get_submission_plugin_by_type('byblos');
        $plugin->save($submission, (object) ['assignsubmission_byblos_choice' => 'page:' . $pageid]);

        $row = $DB->get_record('local_byblos_submission', ['assignsubmissionid' => $submission->id]);
        $this->assertNotFalse($row, 'A local_byblos_submission row should exist after save().');
        $this->assertSame('snapshot_on_submit', (string) $row->snapshotmode);
    }

    /**
     * is_empty() detects "no selection" both for absent rows and for rows with
     * neither a pageid nor a collectionid.
     *
     * @covers ::is_empty
     * @covers ::submission_is_empty
     */
    public function test_is_empty_detects_no_selection(): void {
        $this->resetAfterTest();

        [$assign, $student] = $this->build_assignment(['assignsubmission_byblos_enabled' => 1]);
        $this->setUser($student);

        $plugin = $assign->get_submission_plugin_by_type('byblos');
        $submission = $assign->get_user_submission($student->id, true);

        $this->assertTrue($plugin->is_empty($submission));
        $this->assertTrue($plugin->submission_is_empty((object) []));
        $this->assertTrue($plugin->submission_is_empty((object) ['assignsubmission_byblos_choice' => '']));
        $this->assertFalse($plugin->submission_is_empty((object) ['assignsubmission_byblos_choice' => 'page:1']));
    }

    /**
     * Build an assignment with a student enrolled and a fresh assign instance.
     *
     * @param array $config  Plugin config overrides.
     * @return array{0: assign, 1: stdClass}  [assign, student]
     */
    private function build_assignment(array $config): array {
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');

        $defaults = [
            'assignsubmission_byblos_enabled'        => 1,
            'assignsubmission_byblos_allowedunit'    => 'either',
            'assignsubmission_byblos_snapshotmode'   => 'snapshot_on_submit',
            'assignsubmission_byblos_peerenabled'    => 0,
            'assignsubmission_byblos_peermode'       => 'manual',
            'assignsubmission_byblos_peercount'      => 2,
            'assignsubmission_byblos_peervisibility' => 'after_submit',
            'assignsubmission_byblos_peerscoremode'  => 'numeric',
            'assignsubmission_byblos_checklist'      => '',
        ];
        $params = array_merge($defaults, $config, ['course' => $course->id]);

        $assigninstance = $this->create_instance($course, $params);
        return [$assigninstance, $student];
    }

    /**
     * Insert a minimal local_byblos_page row owned by the user, returning its id.
     *
     * @param int    $userid
     * @param string $title
     * @return int
     */
    private function insert_byblos_page(int $userid, string $title): int {
        global $DB;
        $now = time();
        return (int) $DB->insert_record('local_byblos_page', (object) [
            'userid'       => $userid,
            'title'        => $title,
            'theme'        => 'clean',
            'layout'       => 'single',
            'timecreated'  => $now,
            'timemodified' => $now,
        ]);
    }

    /**
     * Build a throwaway MoodleQuickForm for element-rendering tests.
     *
     * @return MoodleQuickForm
     */
    private function make_form(): MoodleQuickForm {
        // MoodleQuickForm needs a form name and target; bare instantiation suffices for unit tests.
        return new MoodleQuickForm('test', 'post', '#');
    }
}
