<?php
// This file is part of a 108design source-available software product.
//
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>
//
// Use and modification are permitted only under the Software License included
// with this distribution. Redistribution and circumvention of Pro feature or
// licensing restrictions are prohibited. See LICENSE.md for the full terms.

namespace local_moveactivities;

use local_moveactivities\external\search_courses;
use local_moveactivities\local\course_selector;

defined('MOODLE_INTERNAL') || die();

/**
 * Native discovery boundary and output integration regressions.
 * @package local_moveactivities
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license See LICENSE.md for the full terms.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(course_selector::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(search_courses::class)]
final class course_selector_test extends \advanced_testcase {
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    public function test_no_initial_enumeration_and_text_or_id_search(): void {
        $course = $this->getDataGenerator()->create_course([
            'fullname' => 'Transfer picker full & name', 'shortname' => 'PickerShort', 'idnumber' => 'PickerIdNumber',
        ]);
        $this->assertSame([], manager::get_course_autocomplete_options());
        foreach (['', ' ', 'P', str_repeat('x', 101)] as $query) {
            $this->assertSame([], course_selector::search($query, 'source'));
        }
        foreach (['full & name', 'PickerShort', 'PickerIdNumber', (string)$course->id] as $query) {
            $results = course_selector::search($query, 'source');
            $this->assertArrayHasKey($course->id, $results);
            $this->assertStringContainsString('full & name', $results[$course->id]);
        }
        $this->assertSame([(int)$course->id], array_keys(manager::get_course_autocomplete_options([$course->id])));
        $this->assertSame([], course_selector::selected(SITEID, 'source'));
    }

    public function test_server_owned_purpose_and_target_permissions_recheck_cached_candidates(): void {
        $course = $this->getDataGenerator()->create_course(['fullname' => 'Transfer protected']);
        $context = \context_course::instance($course->id);
        $role = create_role('Transfer picker', 'transferpicker', '');
        assign_capability('local/moveactivities:move', CAP_ALLOW, $role, $context->id, true);
        $user = $this->getDataGenerator()->create_user();
        role_assign($role, $user->id, $context->id);
        $this->setUser($user);
        $this->assertTrue(course_selector::can_use());
        $this->assertArrayHasKey($course->id, course_selector::selected($course->id, 'source'));
        $this->assertSame([], course_selector::selected($course->id, 'target'));
        assign_capability('moodle/restore:restoreactivity', CAP_ALLOW, $role, $context->id, true);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertArrayHasKey($course->id, course_selector::search('Transfer protected', 'target'));
        assign_capability('moodle/restore:restoreactivity', CAP_PROHIBIT, $role, $context->id, true);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertSame([], course_selector::search('Transfer protected', 'target'));
        $this->assertSame([], course_selector::selected($course->id, 'target'));
    }

    public function test_hidden_course_follows_native_visibility_and_minimal_external_response(): void {
        $hidden = $this->getDataGenerator()->create_course(['fullname' => 'Transfer hidden', 'visible' => 0]);
        $this->assertArrayHasKey($hidden->id, course_selector::selected($hidden->id, 'source'));
        $result = search_courses::clean_returnvalue(search_courses::execute_returns(), search_courses::execute('Transfer hidden', 'source'));
        $this->assertSame(['id', 'label'], array_keys($result[0]));
        $context = \context_course::instance($hidden->id);
        $role = create_role('Transfer hidden operator', 'transferhidden', '');
        assign_capability('local/moveactivities:move', CAP_ALLOW, $role, $context->id, true);
        $user = $this->getDataGenerator()->create_user();
        role_assign($role, $user->id, $context->id);
        $this->setUser($user);
        $this->assertSame([], course_selector::selected($hidden->id, 'source'));
        $this->assertSame([], course_selector::search((string)$hidden->id, 'source'));
        $this->assertSame([], course_selector::search('Transfer hidden', 'source'));
    }

    public function test_invalid_client_purpose_is_rejected(): void {
        $this->expectException(\invalid_parameter_exception::class);
        search_courses::execute('Transfer', 'administrator');
    }

    public function test_result_limit_and_denied_user(): void {
        for ($i = 0; $i < course_selector::LIMIT + 1; $i++) {
            $this->getDataGenerator()->create_course(['fullname' => sprintf('TransferLimit %02d', $i)]);
        }
        $this->assertCount(course_selector::LIMIT, course_selector::search('TransferLimit', 'target'));
        $this->setUser($this->getDataGenerator()->create_user());
        $this->assertSame([], course_selector::search('TransferLimit', 'source'));
        $this->assertFalse(course_selector::can_use());
    }

    public function test_native_templates_escape_labels_and_keep_post_confirmation(): void {
        global $PAGE, $OUTPUT;
        $PAGE->set_context(\context_system::instance());
        $PAGE->set_url('/local/moveactivities/index.php');
        $html = $OUTPUT->render_from_template('local_moveactivities/selection', [
            'url' => '/local/moveactivities/index.php', 'sesskey' => 'synthetic',
            'sourceoptions' => [['value' => 42, 'label' => 'Plain <b>literal</b> & name', 'selected' => true]],
        ]);
        $this->assertStringContainsString('Plain &lt;b&gt;literal&lt;/b&gt; &amp; name', $html);
        $this->assertStringNotContainsString('Plain <b>literal</b>', $html);
        $confirm = $OUTPUT->render_from_template('local_moveactivities/confirm_move', [
            'url' => '/local/moveactivities/index.php', 'hidden' => [['name' => 'cmids[]', 'value' => 42]],
        ]);
        $this->assertStringContainsString('name="confirmmove" value="1"', $confirm);
        $this->assertStringContainsString('name="load" value="1"', $confirm);
        $this->assertStringNotContainsString('<script', $confirm);
    }
}
