<?php
// This file is part of a 108design source-available software product.
//
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>
//
// Use and modification are permitted only under the Software License included
// with this distribution. Redistribution and circumvention of Pro feature or
// licensing restrictions are prohibited. See LICENSE.md for the full terms.

namespace local_moveactivities\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

defined('MOODLE_INTERNAL') || die();

/**
 * Minimal, authenticated course-search response for the transfer form.
 *
 * @package    local_moveactivities
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    See LICENSE.md for the full terms.
 */
final class search_courses extends external_api {
    /** @return external_function_parameters Search input. */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'query' => new external_value(PARAM_TEXT, 'Course search text'),
            'purpose' => new external_value(PARAM_ALPHA, 'source or target'),
        ]);
    }

    /**
     * Search eligible courses. Native AJAX handles the session key.
     *
     * @param string $query Search text.
     * @param string $purpose Source or target.
     * @return array Minimal course results.
     */
    public static function execute(string $query, string $purpose): array {
        $params = self::validate_parameters(self::execute_parameters(), ['query' => $query, 'purpose' => $purpose]);
        require_login();
        self::validate_context(\context_system::instance());
        $courses = [];
        foreach (\local_moveactivities\local\course_selector::search($params['query'], $params['purpose']) as $id => $label) {
            $courses[] = ['id' => $id, 'label' => $label];
        }
        return $courses;
    }

    /** @return external_multiple_structure Minimal course result schema. */
    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Course ID'),
            'label' => new external_value(PARAM_TEXT, 'Plain-text course label'),
        ]));
    }
}
