<?php
// This file is part of a 108design source-available software product.
//
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>
//
// Use and modification are permitted only under the Software License included
// with this distribution. Redistribution and circumvention of Pro feature or
// licensing restrictions are prohibited. See LICENSE.md for the full terms.

namespace local_moveactivities;

use local_moveactivities\local\transfer_selection;

defined('MOODLE_INTERNAL') || die();

/**
 * Real queue, authority and native backup/restore regressions with synthetic pages.
 * @package local_moveactivities
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license See LICENSE.md for the full terms.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(transfer_selection::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(manager::class)]
final class transfer_selection_test extends \advanced_testcase {
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    private function fixture(): array {
        global $DB;
        $source = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $target = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $page = $this->getDataGenerator()->create_module('page', [
            'course' => $source->id, 'name' => 'Synthetic transfer page', 'content' => '<p>Synthetic transfer content</p>',
        ]);
        $user = $this->getDataGenerator()->create_user();
        foreach ([$source, $target] as $course) {
            $this->getDataGenerator()->enrol_user($user->id, $course->id, 'editingteacher');
        }
        return [$source, $target, $page, $user, (int)$DB->get_field('role', 'id', ['shortname' => 'editingteacher'], MUST_EXIST)];
    }

    private function execute_transfer(bool $move): void {
        global $DB;
        [$source, $target, $page, $user] = $this->fixture();
        $this->setUser($user);
        $labels = transfer_selection::validate($source->id, $target->id, [$page->cmid], $move);
        $id = manager::create_job($user->id, $source->id, $target->id, 'first', $move, [$page->cmid], $labels);
        $job = $DB->get_record('local_moveactivities_job', ['id' => $id], '*', MUST_EXIST);
        $this->assertEquals($user->id, $job->userid);
        $task = new \local_moveactivities\task\process_job();
        $task->set_userid($user->id);
        $task->set_custom_data(['jobid' => $id]);
        $this->assertEquals($user->id, $task->get_userid());
        $task->execute();
        $this->assertSame('done', $DB->get_field('local_moveactivities_job', 'status', ['id' => $id]));
        $restored = $DB->get_record('page', ['course' => $target->id], '*', MUST_EXIST);
        $this->assertSame($page->name, $restored->name);
        $this->assertStringContainsString('Synthetic transfer content', $restored->content);
        $this->assertSame(!$move, $DB->record_exists('course_modules', ['id' => $page->cmid]));
        $newcm = get_coursemodule_from_instance('page', $restored->id, $target->id, false, MUST_EXIST);
        $this->assertNotEquals($page->cmid, $newcm->id);
    }

    public function test_ordinary_teacher_copy_retains_original(): void { $this->execute_transfer(false); }
    public function test_ordinary_teacher_move_removes_original_after_restore(): void { $this->execute_transfer(true); }

    public function test_move_denial_prevents_job_creation_while_copy_is_allowed(): void {
        global $DB;
        [$source, $target, $page, $user, $role] = $this->fixture();
        assign_capability('moodle/course:manageactivities', CAP_PROHIBIT, $role, \context_module::instance($page->cmid)->id, true);
        $this->setUser($user);
        $this->assertArrayHasKey($page->cmid, transfer_selection::validate($source->id, $target->id, [$page->cmid], false));
        try {
            manager::create_job($user->id, $source->id, $target->id, 'first', true, [$page->cmid], []);
            $this->fail('Native delete authority is required before queuing a move.');
        } catch (\required_capability_exception $e) {
            $this->assertSame(0, $DB->count_records('local_moveactivities_job'));
            $this->assertTrue($DB->record_exists('course_modules', ['id' => $page->cmid]));
        }
    }

    public function test_foreign_activity_rejects_whole_selection_before_queue_write(): void {
        global $DB;
        [$source, $target, $page, $user] = $this->fixture();
        $other = $this->getDataGenerator()->create_module('page', ['course' => $target->id]);
        $this->setUser($user);
        try {
            manager::create_job($user->id, $source->id, $target->id, 'first', false, [$page->cmid, $other->cmid], []);
            $this->fail('A foreign source activity must reject the complete request.');
        } catch (\invalid_parameter_exception $e) {
            $this->assertSame(0, $DB->count_records('local_moveactivities_job'));
            $this->assertSame(0, $DB->count_records('local_moveactivities_item'));
        }
    }

    public function test_forged_owner_and_invalid_section_mode_do_not_create_jobs(): void {
        global $DB;
        [$source, $target, $page, $user] = $this->fixture();
        $this->setUser($user);
        foreach ([['userid' => (int)$user->id + 999, 'mode' => 'first'], ['userid' => (int)$user->id, 'mode' => 'invalid']] as $input) {
            try {
                manager::create_job($input['userid'], $source->id, $target->id, $input['mode'], false, [$page->cmid], []);
                $this->fail('Invalid owner/mode must fail.');
            } catch (\invalid_parameter_exception $e) {
                $this->assertSame(0, $DB->count_records('local_moveactivities_job'));
            }
        }
    }

    public function test_revoked_restore_permission_prevents_retry_state_change(): void {
        global $DB;
        [$source, $target, $page, $user, $role] = $this->fixture();
        $this->setUser($user);
        $id = manager::create_job($user->id, $source->id, $target->id, 'first', false, [$page->cmid], []);
        $DB->set_field('local_moveactivities_job', 'status', 'done_with_errors', ['id' => $id]);
        $DB->set_field('local_moveactivities_item', 'status', 'failed', ['jobid' => $id]);
        assign_capability('moodle/restore:restoreactivity', CAP_PROHIBIT, $role, \context_course::instance($target->id)->id, true);
        accesslib_clear_all_caches_for_unit_testing();
        try {
            manager::retry_failed_items($id, $user->id);
            $this->fail('Retry must require current destination restore permission.');
        } catch (\required_capability_exception $e) {
            $this->assertSame('done_with_errors', $DB->get_field('local_moveactivities_job', 'status', ['id' => $id]));
            $this->assertSame('failed', $DB->get_field('local_moveactivities_item', 'status', ['jobid' => $id]));
        }
    }
}
