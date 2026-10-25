<?php
// This file is part of a 108design source-available software product.
//
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>
//
// Use and modification are permitted only under the Software License included
// with this distribution. Redistribution and circumvention of Pro feature or
// licensing restrictions are prohibited. See LICENSE.md for the full terms.

/**
 * Owner-scoped job history and retry controller.
 *
 * @package    local_moveactivities
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    See LICENSE.md for the full terms.
 */
require_once(__DIR__ . '/../../config.php');

use local_moveactivities\manager;
use local_moveactivities\local\admin_help;

require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/local/moveactivities/jobs.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('jobs', 'local_moveactivities'));
$PAGE->set_heading(get_string('jobs', 'local_moveactivities'));
$retryjobid = optional_param('retryjobid', 0, PARAM_INT);
$showjobid = optional_param('showjobid', 0, PARAM_INT);
if ($retryjobid > 0) {
    if (!data_submitted()) {
        throw new invalid_parameter_exception('Retry requires POST');
    }
    require_sesskey();
    manager::retry_failed_items($retryjobid, $USER->id);
    redirect($PAGE->url);
}
$statuslabel = static function(string $status): string {
    return in_array($status, ['queued', 'running', 'done', 'done_with_errors', 'failed'], true)
        ? get_string($status, 'local_moveactivities') : get_string('unknown');
};
$rows = [];
$hasrunningjobs = false;
$detail = null;
foreach (manager::get_jobs_for_user($USER->id) as $job) {
    $running = in_array((string)$job->status, ['queued', 'running'], true);
    $hasrunningjobs = $hasrunningjobs || $running;
    $items = $DB->get_records('local_moveactivities_item', ['jobid' => $job->id], 'id ASC');
    $done = $failed = 0;
    foreach ($items as $item) {
        $done += $item->status === 'done' ? 1 : 0;
        $failed += $item->status === 'failed' ? 1 : 0;
    }
    $source = $DB->get_field('course', 'fullname', ['id' => $job->sourcecourseid]) ?: '#' . $job->sourcecourseid;
    $target = $DB->get_field('course', 'fullname', ['id' => $job->targetcourseid]) ?: '#' . $job->targetcourseid;
    $rows[] = [
        'id' => (int)$job->id, 'source' => html_to_text(format_string($source), 0, false),
        'target' => html_to_text(format_string($target), 0, false),
        'action' => get_string((int)$job->deletesource ? 'actionmode_move' : 'actionmode_copy', 'local_moveactivities'),
        'status' => $statuslabel((string)$job->status),
        'progress' => $done . '/' . count($items) . ($failed ? ' (' . get_string('failed', 'local_moveactivities') . ': ' . $failed . ')' : ''),
        'created' => userdate($job->timecreated), 'modified' => userdate($job->timemodified),
        'detailsurl' => (new moodle_url('/local/moveactivities/jobs.php', ['showjobid' => $job->id]))->out(false),
        'shown' => $showjobid === (int)$job->id,
    ];
    if ($showjobid === (int)$job->id) {
        $canretry = false;
        if ($job->status === 'done_with_errors'
                && $DB->record_exists('course', ['id' => $job->sourcecourseid])
                && $DB->record_exists('course', ['id' => $job->targetcourseid])) {
            $canretry = has_capability('local/moveactivities:move', context_course::instance($job->sourcecourseid))
                && has_all_capabilities(['local/moveactivities:move', 'moodle/restore:restoreactivity'],
                    context_course::instance($job->targetcourseid));
        }
        $detail = ['id' => (int)$job->id, 'canretry' => $canretry, 'items' => []];
        foreach ($items as $item) {
            $detail['items'][] = ['status' => $statuslabel((string)$item->status),
                'name' => (string)$item->activityname,
                'message' => $item->status === 'done'
                    ? get_string((int)$job->deletesource ? 'resultok' : 'resultokcopy', 'local_moveactivities')
                    : (string)$item->message];
        }
    }
}
$templatedata = [
    'selectionurl' => (new moodle_url('/local/moveactivities/index.php'))->out(false),
    'url' => $PAGE->url->out(false),
    'refreshurl' => (new moodle_url('/local/moveactivities/jobs.php', ['showjobid' => $showjobid]))->out(false),
    'sesskey' => sesskey(), 'hasjobs' => (bool)$rows, 'jobs' => $rows,
    'hasrunningjobs' => $hasrunningjobs, 'detail' => $detail,
    'jobshelp' => $OUTPUT->help_icon('jobs', 'local_moveactivities'),
    'resultsinfo' => admin_help::icon('results'),
];
$PAGE->requires->js_call_amd('local_moveactivities/jobs', 'init', [$hasrunningjobs]);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_moveactivities/jobs', $templatedata);
echo $OUTPUT->footer();
