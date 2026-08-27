<?php
// This file is part of a 108design source-available software product.
//
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>
//
// @package    local_moveactivities
// @copyright  2026 Andreas Giesen <andreas@108design.com>
// @license    See LICENSE.md for the full terms.

namespace local_moveactivities\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Ad-hoc task to process move/copy job chunks.
 */
class process_job extends \core\task\adhoc_task {
    /**
     * Execute task.
     */
    public function execute(): void {
        $data = (array)$this->get_custom_data();
        if (empty($data['jobid'])) {
            return;
        }
        \local_moveactivities\manager::process_job((int)$data['jobid']);
    }
}
