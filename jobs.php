<?php
// This file is part of a 108design source-available software product.
//
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>
//
// @package    local_moveactivities
// @copyright  2026 Andreas Giesen <andreas@108design.com>
// @license    See LICENSE.md for the full terms.

require_once(__DIR__ . '/../../config.php');

use local_moveactivities\manager;

require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/moveactivities/jobs.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('jobs', 'local_moveactivities'));
$PAGE->set_heading(get_string('jobs', 'local_moveactivities'));

$retryjobid = optional_param('retryjobid', 0, PARAM_INT);
$showjobid = optional_param('showjobid', 0, PARAM_INT);

if ($retryjobid && confirm_sesskey()) {
    manager::retry_failed_items($retryjobid, $USER->id);
    redirect($PAGE->url);
}

$jobs = manager::get_jobs_for_user($USER->id);
$hasrunningjobs = false;

$statuslabel = static function(string $status): string {
    $key = 'jobstatus_' . $status;
    try {
        return get_string($key, 'local_moveactivities');
    } catch (Throwable $e) {
        return $status;
    }
};

echo $OUTPUT->header();

echo html_writer::start_div('d-flex justify-content-between align-items-center mb-3');
echo html_writer::tag('h3', get_string('jobs', 'local_moveactivities'), ['class' => 'm-0']);
echo $OUTPUT->single_button(new moodle_url('/local/moveactivities/index.php'), get_string('backtoselection', 'local_moveactivities'), 'get');
echo html_writer::end_div();

if (empty($jobs)) {
    echo html_writer::div(get_string('nojobsyet', 'local_moveactivities'), 'alert alert-info');
    echo $OUTPUT->footer();
    exit;
}

$table = new html_table();
$table->head = [
    get_string('jobid', 'local_moveactivities'),
    get_string('source', 'local_moveactivities'),
    get_string('target', 'local_moveactivities'),
    get_string('action', 'local_moveactivities'),
    get_string('status', 'local_moveactivities'),
    get_string('progress', 'local_moveactivities'),
    get_string('created', 'local_moveactivities'),
    get_string('modified', 'local_moveactivities'),
    get_string('details', 'local_moveactivities'),
];

foreach ($jobs as $job) {
    if (in_array((string)$job->status, ['queued', 'running'], true)) {
        $hasrunningjobs = true;
    }
    $items = $DB->get_records('local_moveactivities_item', ['jobid' => $job->id], 'id ASC', 'id,status');
    $total = count($items);
    $done = 0;
    $failed = 0;
    foreach ($items as $item) {
        if ($item->status === 'done') {
            $done++;
        } else if ($item->status === 'failed') {
            $failed++;
        }
    }

    $source = $DB->get_field('course', 'fullname', ['id' => $job->sourcecourseid]) ?: ('#' . $job->sourcecourseid);
    $target = $DB->get_field('course', 'fullname', ['id' => $job->targetcourseid]) ?: ('#' . $job->targetcourseid);
    $action = ((int)$job->deletesource === 1) ? get_string('actionmode_move', 'local_moveactivities') : get_string('actionmode_copy', 'local_moveactivities');

    $detailsurl = new moodle_url('/local/moveactivities/jobs.php', ['showjobid' => $job->id]);
    $detailstext = ($showjobid === (int)$job->id) ? '-' : get_string('details', 'local_moveactivities');

    $table->data[] = [
        '#' . (int)$job->id,
        format_string($source),
        format_string($target),
        $action,
        $statuslabel((string)$job->status),
        $done . '/' . $total . ($failed > 0 ? (' (' . get_string('failed', 'local_moveactivities') . ': ' . $failed . ')') : ''),
        userdate($job->timecreated),
        userdate($job->timemodified),
        html_writer::link($detailsurl, $detailstext),
    ];
}

echo html_writer::table($table);

echo html_writer::start_div('d-flex gap-2 align-items-center mt-2 mb-3');
echo $OUTPUT->single_button($PAGE->url, get_string('refreshnow', 'local_moveactivities'), 'get');
if ($hasrunningjobs) {
    echo html_writer::div(get_string('autorefreshhint', 'local_moveactivities'), 'text-muted');
}
echo html_writer::end_div();

if ($hasrunningjobs) {
    $PAGE->requires->js_amd_inline("setTimeout(function(){ window.location.reload(); }, 10000);");
}

if ($showjobid > 0) {
    $job = $DB->get_record('local_moveactivities_job', ['id' => $showjobid, 'userid' => $USER->id]);
    if ($job) {
        $items = $DB->get_records('local_moveactivities_item', ['jobid' => $job->id], 'id ASC');
        echo html_writer::tag('h4', get_string('details', 'local_moveactivities') . ' #' . (int)$job->id, ['class' => 'mt-4']);

        if ((string)$job->status === 'done_with_errors') {
            $retryurl = new moodle_url('/local/moveactivities/jobs.php', ['retryjobid' => $job->id, 'sesskey' => sesskey()]);
            echo $OUTPUT->single_button($retryurl, get_string('retryfailed', 'local_moveactivities'));
        }

        echo html_writer::start_tag('ul', ['class' => 'list-group']);
        foreach ($items as $item) {
            $line = '[' . $item->status . '] ' . s((string)$item->activityname);
            if (!empty($item->message)) {
                $line .= ' — ' . s((string)$item->message);
            }
            echo html_writer::tag('li', $line, ['class' => 'list-group-item']);
        }
        echo html_writer::end_tag('ul');
    }
}

echo $OUTPUT->footer();
