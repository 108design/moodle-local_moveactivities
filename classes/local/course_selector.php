<?php
// This file is part of a 108design source-available software product.
//
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>
//
// Use and modification are permitted only under the Software License included
// with this distribution. Redistribution and circumvention of Pro feature or
// licensing restrictions are prohibited. See LICENSE.md for the full terms.

namespace local_moveactivities\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Native, permission-aware lookup for source and destination autocomplete controls.
 *
 * @package    local_moveactivities
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    See LICENSE.md for the full terms.
 */
final class course_selector {
    /** Maximum options sent to the browser. */
    public const LIMIT = 20;

    /**
     * Capabilities are selected on the server, never supplied by the client.
     * @param string $purpose Source or target.
     * @return array Required native capabilities.
     */
    private static function capabilities(string $purpose): array {
        if (!in_array($purpose, ['source', 'target'], true)) {
            throw new \invalid_parameter_exception('Invalid course selector purpose');
        }
        $caps = ['local/moveactivities:move'];
        if ($purpose === 'target') {
            $caps[] = 'moodle/restore:restoreactivity';
        }
        return $caps;
    }

    /**
     * Return a selected course only while current discovery and action rights permit it.
     * @param int $id Selected course ID.
     * @param string $purpose Source or target.
     * @return array<int,string> Plain labels, indexed by course ID.
     */
    public static function selected(int $id, string $purpose): array {
        global $DB;
        $caps = self::capabilities($purpose);
        if ($id <= 0 || $id === (int)SITEID) {
            return [];
        }
        $course = $DB->get_record('course', ['id' => $id], 'id,fullname,shortname,idnumber,visible,category');
        if (!$course) {
            return [];
        }
        $context = \context_course::instance($id);
        if (!has_all_capabilities($caps, $context)
                || (!\core_course_category::can_view_course_info($course) && !is_enrolled($context, null, '', true))) {
            return [];
        }
        return [$id => self::label($course)];
    }

    /**
     * Search via Moodle's course infrastructure, then recheck cached candidates.
     * @param string $query Search text or exact numeric ID.
     * @param string $purpose Source or target.
     * @return array<int,string> At most20plain labels.
     */
    public static function search(string $query, string $purpose): array {
        $caps = self::capabilities($purpose);
        $query = trim($query);
        if ($query === '' || \core_text::strlen($query) > 100) {
            return [];
        }
        $options = ctype_digit($query) ? self::selected((int)$query, $purpose) : [];
        if (\core_text::strlen($query) < 2) {
            return $options;
        }
        $offset = 0;
        do {
            $courses = \core_course_category::search_courses(['search' => $query], [
                'offset' => $offset, 'limit' => self::LIMIT, 'sort' => ['fullname' => 1, 'id' => 1],
            ], $caps);
            foreach ($courses as $course) {
                $id = (int)$course->id;
                if (!isset($options[$id])) {
                    $options += self::selected($id, $purpose);
                    if (count($options) === self::LIMIT) {
                        return $options;
                    }
                }
            }
            $offset += self::LIMIT;
        } while (count($courses) === self::LIMIT);
        return $options;
    }

    /**
     * Determine tool/help access without preloading a course list.
     * @return bool Whether a transferable non-site course exists.
     */
    public static function can_use(): bool {
        if (!isloggedin() || isguestuser()) {
            return false;
        }
        foreach (get_user_capability_course('local/moveactivities:move', null, true, '', '', 2) ?: [] as $course) {
            if ((int)$course->id !== (int)SITEID) {
                return true;
            }
        }
        return false;
    }

    /**
     * Renderers own escaping; this helper returns only plain text.
     * @param object $course Course record.
     * @return string Display label.
     */
    private static function label(object $course): string {
        $context = \context_course::instance($course->id);
        $label = html_to_text(format_string($course->fullname, true, ['context' => $context]), 0, false)
            . ' [ID:' . (int)$course->id . ']';
        if ($course->shortname !== '') {
            $label .= ' | ' . html_to_text(format_string($course->shortname, true, ['context' => $context]), 0, false);
        }
        if ($course->idnumber !== '') {
            $label .= ' | #' . $course->idnumber;
        }
        return $label;
    }
}
