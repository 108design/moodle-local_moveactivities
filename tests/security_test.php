<?php
/**
 * This file is part of a 108design source-available software product.
 *
 * Use and modification are permitted only under the Software License included
 * with this distribution. See LICENSE.md for the full terms.
 *
 */
// Copyright (C) 2026 Andreas Giesen. See LICENSE.md for the full terms.

namespace local_moveactivities;

use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;
use local_moveactivities\privacy\provider;

/**
 * Native permission and privacy regressions with synthetic courses and users.
 *
 * @package local_moveactivities
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license See LICENSE.md for the full terms.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(manager::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(provider::class)]
final class security_test extends \advanced_testcase {
    /** A copy operator may not remove an activity without Moodle's native right. */
    public function test_move_requires_native_manageactivities_before_copying(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $source = $this->getDataGenerator()->create_course();
        $target = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $source->id]);
        $operator = $this->getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher'], MUST_EXIST);
        foreach ([$source, $target] as $course) {
            $this->getDataGenerator()->enrol_user($operator->id, $course->id, $roleid);
            assign_capability('local/moveactivities:move', CAP_ALLOW, $roleid, \context_course::instance($course->id));
        }
        assign_capability('moodle/course:manageactivities', CAP_PROHIBIT, $roleid,
            \context_module::instance($page->cmid));
        $this->setUser($operator);
        $before = $DB->count_records('course_modules', ['course' => $target->id]);
        $result = manager::move_activity($page->cmid, $target->id, 1, true);
        $this->assertFalse($result['success']);
        $this->assertTrue($DB->record_exists('course_modules', ['id' => $page->cmid]));
        $this->assertSame($before, $DB->count_records('course_modules', ['course' => $target->id]));
    }

    /** Reusing an existing section needs no additional course-update privilege. */
    public function test_existing_section_can_be_reused_without_course_update(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $this->setUser($this->getDataGenerator()->create_user());
        $this->assertSame(1, manager::resolve_target_sectionnum($course->id, 'Unused', false));
    }

    /** Naming a new section must fail before any write when course:update is absent. */
    public function test_new_section_requires_native_course_update(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $before = $DB->count_records('course_sections', ['course' => $course->id]);
        $this->setUser($this->getDataGenerator()->create_user());
        try {
            manager::resolve_target_sectionnum($course->id, 'Synthetic new section', true);
            $this->fail('Section creation must require moodle/course:update.');
        } catch (\required_capability_exception $exception) {
            $this->assertSame($before, $DB->count_records('course_sections', ['course' => $course->id]));
        }
    }

    /** An authorised operator can create and then reuse the named destination. */
    public function test_authorised_section_creation_and_reuse(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $sectionnum = manager::resolve_target_sectionnum($course->id, 'Synthetic new section', true);
        $this->assertSame('Synthetic new section', $DB->get_field('course_sections', 'name',
            ['course' => $course->id, 'section' => $sectionnum]));
        $this->assertSame($sectionnum, manager::resolve_target_sectionnum($course->id, 'Synthetic new section', true));
    }

    /** Enumerate and erase only approved system-context owners and dependent items. */
    public function test_bulk_privacy_erase_is_scoped_and_removes_job_items(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $users = [$this->getDataGenerator()->create_user(), $this->getDataGenerator()->create_user()];
        $course = $this->getDataGenerator()->create_course();
        $jobs = [];
        foreach ($users as $user) {
            $jobid = $DB->insert_record('local_moveactivities_job', (object)[
                'userid' => $user->id, 'sourcecourseid' => $course->id, 'targetcourseid' => $course->id,
                'targetsectionmode' => 'first', 'deletesource' => 0, 'status' => 'queued',
                'timecreated' => time(), 'timemodified' => time(),
            ]);
            $DB->insert_record('local_moveactivities_item', (object)[
                'jobid' => $jobid, 'cmid' => 0, 'activityname' => 'Synthetic activity', 'status' => 'queued',
                'timecreated' => time(), 'timemodified' => time(),
            ]);
            $jobs[] = $jobid;
        }
        $system = \context_system::instance();
        $list = new userlist($system, 'local_moveactivities');
        provider::get_users_in_context($list);
        $this->assertEqualsCanonicalizing(array_column($users, 'id'), $list->get_userids());
        $coursecontext = \context_course::instance($course->id);
        $otherlist = new userlist($coursecontext, 'local_moveactivities');
        provider::get_users_in_context($otherlist);
        $this->assertSame([], $otherlist->get_userids());
        provider::delete_data_for_users(new approved_userlist($coursecontext, 'local_moveactivities', [$users[0]->id]));
        $this->assertTrue($DB->record_exists('local_moveactivities_job', ['id' => $jobs[0]]));
        provider::delete_data_for_users(new approved_userlist($system, 'local_moveactivities', [$users[0]->id]));
        $this->assertFalse($DB->record_exists('local_moveactivities_job', ['id' => $jobs[0]]));
        $this->assertFalse($DB->record_exists('local_moveactivities_item', ['jobid' => $jobs[0]]));
        $this->assertTrue($DB->record_exists('local_moveactivities_job', ['id' => $jobs[1]]));
        $this->assertTrue($DB->record_exists('local_moveactivities_item', ['jobid' => $jobs[1]]));
        manager::process_job($jobs[0]); // A previously queued task safely becomes a no-op after erasure.
    }
}
