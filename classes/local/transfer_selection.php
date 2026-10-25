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
 * Authoritative validation shared by confirmation and queue creation.
 *
 * @package    local_moveactivities
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    See LICENSE.md for the full terms.
 */
final class transfer_selection {
    /**
     * Validate the complete selection before showing confirmation or creating a job.
     * @param int $sourceid Source course.
     * @param int $targetid Destination course.
     * @param array $cmids Selected course module IDs.
     * @param bool $deletesource Whether originals will be deleted.
     * @return array<int,string> Authoritative plain-text activity labels.
     */
    public static function validate(int $sourceid, int $targetid, array $cmids, bool $deletesource): array {
        if ($sourceid <= 0 || $targetid <= 0 || $sourceid === $targetid
                || $sourceid === (int)SITEID || $targetid === (int)SITEID || !$cmids) {
            throw new \invalid_parameter_exception('Invalid activity transfer selection');
        }
        require_capability('local/moveactivities:move', \context_course::instance($sourceid));
        require_capability('local/moveactivities:move', \context_course::instance($targetid));
        require_capability('moodle/restore:restoreactivity', \context_course::instance($targetid));
        $available = \local_moveactivities\manager::get_source_activities($sourceid);
        $labels = [];
        foreach (array_unique(array_map('intval', $cmids)) as $cmid) {
            if (!isset($available[$cmid])) {
                throw new \invalid_parameter_exception('Activity is not available in the selected source course');
            }
            $context = \context_module::instance($cmid);
            require_capability('moodle/backup:backupactivity', $context);
            if ($deletesource) {
                require_capability('moodle/course:manageactivities', $context);
            }
            $labels[$cmid] = html_to_text($available[$cmid], 0, false);
        }
        return $labels;
    }
}
