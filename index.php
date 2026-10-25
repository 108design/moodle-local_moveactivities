<?php
// This file is part of a 108design source-available software product.
//
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>
//
// Use and modification are permitted only under the Software License included
// with this distribution. Redistribution and circumvention of Pro feature or
// licensing restrictions are prohibited. See LICENSE.md for the full terms.

/**
 * Selection and server-side confirmation controller for activity transfers.
 *
 * @package    local_moveactivities
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    See LICENSE.md for the full terms.
 */
require_once(__DIR__ . '/../../config.php');

use local_moveactivities\manager;
use local_moveactivities\local\admin_help;
use local_moveactivities\local\course_selector;
use local_moveactivities\local\transfer_selection;

require_login();
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/moveactivities/index.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('moveactivities', 'local_moveactivities'));
$PAGE->set_heading(get_string('moveactivities', 'local_moveactivities'));
if (!course_selector::can_use()) {
    throw new required_capability_exception($context, 'local/moveactivities:move', 'nopermissions', '');
}

$sourceid = optional_param('sourcecourseid', 0, PARAM_INT);
$targetid = optional_param('targetcourseid', 0, PARAM_INT);
$selectedcmids = array_values(array_unique(optional_param_array('cmids', [], PARAM_INT)));
$targetsectionmode = optional_param('targetsectionmode', 'sourcecoursename', PARAM_ALPHA);
$actionmode = optional_param('actionmode', 'copy', PARAM_ALPHA);
$selectedmodtypes = optional_param_array('modtypes', [], PARAM_ALPHANUMEXT);
$selectedsections = optional_param_array('sections', [], PARAM_INT);
$enqueue = optional_param('enqueue', '', PARAM_RAW_TRIMMED);
$confirmed = optional_param('confirmmove', false, PARAM_BOOL);
if (!in_array($actionmode, ['copy', 'move'], true)
        || !in_array($targetsectionmode, ['sourcecoursename', 'first'], true)) {
    throw new invalid_parameter_exception('Invalid transfer action or section mode');
}
if (optional_param('resetfilters', false, PARAM_BOOL)) {
    $selectedmodtypes = [];
    $selectedsections = [];
}
$sourceoptions = manager::get_course_autocomplete_options([$sourceid], 'source');
$targetoptions = manager::get_course_autocomplete_options([$targetid], 'target');
$activities = $modtypeoptions = $sectionoptions = [];
if ($sourceid > 0) {
    if (!$sourceoptions) {
        throw new invalid_parameter_exception('Source course is not available');
    }
    $modtypeoptions = manager::get_source_modtype_options($sourceid);
    $sectionoptions = manager::get_source_section_options($sourceid);
    $activities = manager::get_source_activities_filtered($sourceid, $selectedmodtypes, $selectedsections);
}

$confirmation = null;
if ($enqueue !== '') {
    if (!data_submitted()) {
        throw new invalid_parameter_exception('Job creation requires POST');
    }
    require_sesskey();
    if (array_diff($selectedcmids, array_keys($activities))) {
        throw new invalid_parameter_exception('Selection is outside the visible activity list');
    }
    $labels = transfer_selection::validate($sourceid, $targetid, $selectedcmids, $actionmode === 'move');
    if ($actionmode === 'move' && !$confirmed) {
        $hidden = [
            ['name' => 'sesskey', 'value' => sesskey()],
            ['name' => 'sourcecourseid', 'value' => $sourceid],
            ['name' => 'targetcourseid', 'value' => $targetid],
            ['name' => 'targetsectionmode', 'value' => $targetsectionmode],
            ['name' => 'actionmode', 'value' => $actionmode],
        ];
        foreach (['cmids' => array_keys($labels), 'modtypes' => $selectedmodtypes, 'sections' => $selectedsections] as $name => $values) {
            foreach ($values as $value) {
                $hidden[] = ['name' => $name . '[]', 'value' => $value];
            }
        }
        $confirmation = [
            'url' => $PAGE->url->out(false), 'hidden' => $hidden,
            'source' => reset($sourceoptions), 'target' => reset($targetoptions),
            'activities' => array_map(static fn($label) => ['label' => $label], array_values($labels)),
            'actionhelp' => $OUTPUT->help_icon('actionmode', 'local_moveactivities'),
            'transferinfo' => admin_help::icon('overview'),
        ];
    } else {
        manager::create_job($USER->id, $sourceid, $targetid, $targetsectionmode, $actionmode === 'move',
            array_keys($labels), $labels);
        redirect(new moodle_url('/local/moveactivities/jobs.php'), get_string('jobqueued', 'local_moveactivities'));
    }
}

$optiondata = static function(array $values, array $selected): array {
    $options = [];
    foreach ($values as $value => $label) {
        $options[] = ['value' => $value, 'label' => $label,
            'selected' => in_array((string)$value, array_map('strval', $selected), true)];
    }
    return $options;
};
$templatedata = [
    'url' => $PAGE->url->out(false), 'sesskey' => sesskey(),
    'jobsurl' => (new moodle_url('/local/moveactivities/jobs.php'))->out(false),
    'sourceoptions' => $optiondata($sourceoptions, [$sourceid]),
    'targetoptions' => $optiondata($targetoptions, [$targetid]),
    'showsourcehint' => !$sourceid, 'hasactivities' => (bool)$activities,
    'activities' => $optiondata(array_map(static fn($label) => html_to_text($label, 0, false), $activities), $selectedcmids),
    'modtypeoptions' => $optiondata($modtypeoptions, $selectedmodtypes),
    'sectionoptions' => $optiondata($sectionoptions, $selectedsections),
    'sectionmodes' => $optiondata([
        'sourcecoursename' => get_string('targetsectionmode_sourcecoursename', 'local_moveactivities'),
        'first' => get_string('targetsectionmode_first', 'local_moveactivities'),
    ], [$targetsectionmode]),
    'actionmodes' => $optiondata([
        'copy' => get_string('actionmode_copy', 'local_moveactivities'),
        'move' => get_string('actionmode_move', 'local_moveactivities'),
    ], [$actionmode]),
    'ismove' => $actionmode === 'move',
];
foreach (['sourcecourse', 'targetcourse', 'activities', 'activitytypefilter', 'sectionfilter', 'targetsectionmode', 'actionmode'] as $key) {
    $templatedata[$key . 'help'] = $OUTPUT->help_icon($key, 'local_moveactivities');
}
$templatedata['transferinfo'] = admin_help::icon('overview');
$templatedata['resultsinfo'] = admin_help::icon('results');
if (!$confirmation) {
    $PAGE->requires->js_call_amd('local_moveactivities/selection', 'init');
}
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_moveactivities/' . ($confirmation ? 'confirm_move' : 'selection'),
    $confirmation ?? $templatedata);
echo $OUTPUT->footer();
