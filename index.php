<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
// @package    local_moveactivities
// @copyright  2026 Andreas Giesen <info@108design.com>
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

require_once(__DIR__ . '/../../config.php');

use local_moveactivities\manager;

require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/moveactivities/index.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('moveactivities', 'local_moveactivities'));
$PAGE->set_heading(get_string('moveactivities', 'local_moveactivities'));

$sourceid = optional_param('sourcecourseid', 0, PARAM_INT);
$targetid = optional_param('targetcourseid', 0, PARAM_INT);
$selectedcmids = optional_param_array('cmids', [], PARAM_INT);
$targetsectionmode = optional_param('targetsectionmode', 'sourcecoursename', PARAM_ALPHA);
$actionmode = optional_param('actionmode', 'copy', PARAM_ALPHA);
$selectedmodtypes = optional_param_array('modtypes', [], PARAM_ALPHANUMEXT);
$selectedsections = optional_param_array('sections', [], PARAM_INT);
$load = optional_param('load', '', PARAM_RAW_TRIMMED);
$enqueue = optional_param('enqueue', '', PARAM_RAW_TRIMMED);
$resetfilters = optional_param('resetfilters', '', PARAM_RAW_TRIMMED);
$courseoptions = manager::get_course_autocomplete_options();

$activities = [];
$activitylabels = [];
$showsourcehint = false;
$modtypeoptions = [];
$sectionoptions = [];

if (!empty($resetfilters)) {
    $selectedmodtypes = [];
    $selectedsections = [];
}

if ($sourceid > 0) {
    require_capability('local/moveactivities:move', context_course::instance($sourceid));
    $modtypeoptions = manager::get_source_modtype_options($sourceid);
    $sectionoptions = manager::get_source_section_options($sourceid);
    $activities = manager::get_source_activities_filtered($sourceid, $selectedmodtypes, $selectedsections);
    $activitylabels = $activities;
} else {
    $showsourcehint = true;
}

if (!empty($enqueue) && confirm_sesskey()) {
    if (!$sourceid || !$targetid || empty($selectedcmids) || $sourceid === $targetid) {
        throw new moodle_exception('invalidparameter');
    }

    require_capability('local/moveactivities:move', context_course::instance($sourceid));
    require_capability('local/moveactivities:move', context_course::instance($targetid));

    $deletesource = ($actionmode !== 'copy');
    $cmids = [];
    foreach ($selectedcmids as $cmid) {
        if (isset($activities[$cmid])) {
            $cmids[] = (int)$cmid;
        }
    }
    if (!empty($cmids)) {
        manager::create_job($USER->id, $sourceid, $targetid, $targetsectionmode, $deletesource, $cmids, $activitylabels);
        redirect(new moodle_url('/local/moveactivities/jobs.php'), get_string('jobqueued', 'local_moveactivities'));
    }
}

echo $OUTPUT->header();

echo html_writer::start_div('d-flex justify-content-between align-items-center mb-3');
echo html_writer::tag('h3', get_string('moveactivities', 'local_moveactivities'), ['class' => 'm-0']);
echo $OUTPUT->single_button(new moodle_url('/local/moveactivities/jobs.php'), get_string('viewjobs', 'local_moveactivities'), 'get');
echo html_writer::end_div();

echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url->out(false), 'class' => 'card p-3', 'id' => 'moveactivities-form']);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

echo html_writer::start_div('row g-3 mb-3');
echo html_writer::start_div('col-lg-6');
echo html_writer::tag('label', get_string('sourcecoursesearch', 'local_moveactivities'), ['for' => 'sourcecourseid', 'class' => 'form-label mb-1 font-weight-bold']);
echo html_writer::select(
    $courseoptions,
    'sourcecourseid',
    $sourceid,
    ['' => ''],
    [
        'id' => 'sourcecourseid',
        'class' => 'form-select',
        'data-fieldtype' => 'autocomplete',
        'data-placeholder' => get_string('sourcecoursesearch', 'local_moveactivities'),
    ]
);
echo html_writer::end_div();
echo html_writer::start_div('col-lg-6');
echo html_writer::tag('label', get_string('targetcoursesearch', 'local_moveactivities'), ['for' => 'targetcourseid', 'class' => 'form-label mb-1 font-weight-bold']);
echo html_writer::select(
    $courseoptions,
    'targetcourseid',
    $targetid,
    ['' => ''],
    [
        'id' => 'targetcourseid',
        'class' => 'form-select',
        'data-fieldtype' => 'autocomplete',
        'data-placeholder' => get_string('targetcoursesearch', 'local_moveactivities'),
    ]
);
echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::div(get_string('coursehint', 'local_moveactivities'), 'form-text mb-3');

echo html_writer::start_div('row g-3');
echo html_writer::start_div('col-lg-7');
echo html_writer::tag('label', get_string('activities', 'local_moveactivities'), ['class' => 'form-label mb-2 font-weight-bold']);
if ($showsourcehint) {
    echo html_writer::div(get_string('selectsourcefirst', 'local_moveactivities'), 'alert alert-info');
} else if (empty($activities)) {
    echo html_writer::div(get_string('noactivities', 'local_moveactivities'), 'alert alert-warning');
} else {
    echo html_writer::start_div('d-flex gap-2 mb-2 flex-wrap');
    echo html_writer::tag('button', get_string('selectallvisible', 'local_moveactivities'), ['type' => 'button', 'class' => 'btn btn-outline-secondary btn-sm', 'id' => 'select-all-visible']);
    echo html_writer::tag('button', get_string('clearselection', 'local_moveactivities'), ['type' => 'button', 'class' => 'btn btn-outline-secondary btn-sm', 'id' => 'clear-selection']);
    echo html_writer::end_div();

    echo html_writer::start_div('border rounded p-2', ['style' => 'max-height:420px;overflow:auto;']);
    foreach ($activities as $cmid => $label) {
        $attrs = ['type' => 'checkbox', 'name' => 'cmids[]', 'value' => (int)$cmid, 'class' => 'form-check-input activity-checkbox', 'id' => 'cmid_' . $cmid];
        if (in_array((int)$cmid, $selectedcmids, true)) {
            $attrs['checked'] = 'checked';
        }
        echo html_writer::start_div('form-check mb-2');
        echo html_writer::empty_tag('input', $attrs);
        echo html_writer::tag('label', s($label), ['class' => 'form-check-label ms-2', 'for' => 'cmid_' . $cmid]);
        echo html_writer::end_div();
    }
    echo html_writer::end_div();
}

// Load/reset actions should be directly under source hint/activity list.
echo html_writer::start_div('d-flex gap-2 flex-wrap mt-3');
echo html_writer::empty_tag('input', ['type' => 'submit', 'name' => 'load', 'class' => 'btn btn-secondary', 'value' => get_string('loadactivities', 'local_moveactivities')]);
if (!$showsourcehint) {
    echo html_writer::empty_tag('input', ['type' => 'submit', 'name' => 'resetfilters', 'class' => 'btn btn-outline-secondary', 'value' => get_string('resetfilters', 'local_moveactivities')]);
}
echo html_writer::end_div();

echo html_writer::end_div();

echo html_writer::start_div('col-lg-5');
echo html_writer::start_div('border rounded p-3 h-100 d-flex flex-column');

if (!$showsourcehint) {
    echo html_writer::tag('label', get_string('activitytypefilter', 'local_moveactivities'), ['for' => 'modtypes', 'class' => 'form-label mb-1']);
    echo html_writer::select($modtypeoptions, 'modtypes[]', $selectedmodtypes, null, ['multiple' => 'multiple', 'id' => 'modtypes', 'class' => 'form-select mb-3', 'size' => 6]);

    echo html_writer::tag('label', get_string('sectionfilter', 'local_moveactivities'), ['for' => 'sections', 'class' => 'form-label mb-1']);
    echo html_writer::select($sectionoptions, 'sections[]', $selectedsections, null, ['multiple' => 'multiple', 'id' => 'sections', 'class' => 'form-select mb-3', 'size' => 6]);
}

echo html_writer::tag('label', get_string('targetsectionmode', 'local_moveactivities'), ['for' => 'targetsectionmode', 'class' => 'form-label mb-1 font-weight-bold']);
$modes = [
    'sourcecoursename' => get_string('targetsectionmode_sourcecoursename', 'local_moveactivities'),
    'first' => get_string('targetsectionmode_first', 'local_moveactivities'),
];
echo html_writer::select($modes, 'targetsectionmode', $targetsectionmode, false, ['id' => 'targetsectionmode', 'class' => 'form-select mb-3']);

echo html_writer::tag('label', get_string('actionmode', 'local_moveactivities'), ['for' => 'actionmode', 'class' => 'form-label mb-1 font-weight-bold']);
$actionmodes = [
    'copy' => get_string('actionmode_copy', 'local_moveactivities'),
    'move' => get_string('actionmode_move', 'local_moveactivities'),
];
echo html_writer::select($actionmodes, 'actionmode', $actionmode, false, ['id' => 'actionmode', 'class' => 'form-select mb-3']);
echo html_writer::div(
    get_string('moveconfirmwarning', 'local_moveactivities'),
    'alert alert-danger py-2 px-3 mb-3' . ($actionmode === 'move' ? '' : ' d-none'),
    ['id' => 'move-warning']
);

echo html_writer::start_div('mb-3 mt-auto');
echo html_writer::end_div();

echo html_writer::start_div('', ['style' => 'position:sticky;bottom:0;background:#fff;padding:.75rem 0 .25rem 0;border-top:1px solid #ddd;z-index:10;']);
echo html_writer::empty_tag('input', ['type' => 'submit', 'name' => 'enqueue', 'id' => 'enqueue-btn', 'class' => 'btn ' . ($actionmode === 'move' ? 'btn-danger' : 'btn-primary') . ' w-100', 'value' => get_string('enqueue', 'local_moveactivities')]);
echo html_writer::end_div();

echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_tag('form');

$moveconfirmquestionjs = json_encode(get_string('moveconfirmquestion', 'local_moveactivities'));

$js = <<<JS
(function() {
  if (window.require) {
    require(['core/form-autocomplete'], function(autocomplete) {
      if (autocomplete && autocomplete.enhance) {
        autocomplete.enhance('#sourcecourseid');
        autocomplete.enhance('#targetcourseid');
      }
    });
  }

  const selectAllBtn = document.getElementById('select-all-visible');
  const clearBtn = document.getElementById('clear-selection');
  const form = document.getElementById('moveactivities-form');
  const actionMode = document.getElementById('actionmode');
  const moveWarning = document.getElementById('move-warning');
  const enqueueBtn = document.getElementById('enqueue-btn');

  function updateMoveUi() {
    if (!actionMode) {
      return;
    }
    const isMove = actionMode.value === 'move';
    if (moveWarning) {
      moveWarning.classList.toggle('d-none', !isMove);
    }
    if (enqueueBtn) {
      enqueueBtn.classList.toggle('btn-danger', isMove);
      enqueueBtn.classList.toggle('btn-primary', !isMove);
    }
  }

  if (actionMode) {
    actionMode.addEventListener('change', updateMoveUi);
    updateMoveUi();
  }

  if (form) {
    form.addEventListener('submit', function(e) {
      const submitter = e.submitter;
      if (!submitter || submitter.name !== 'enqueue' || !actionMode) {
        return;
      }
      if (actionMode.value === 'move') {
        const ok = window.confirm({$moveconfirmquestionjs});
        if (!ok) {
          e.preventDefault();
        }
      }
    });
  }

  if (selectAllBtn) {
    selectAllBtn.addEventListener('click', function() {
      document.querySelectorAll('.activity-checkbox').forEach(cb => cb.checked = true);
    });
  }
  if (clearBtn) {
    clearBtn.addEventListener('click', function() {
      document.querySelectorAll('.activity-checkbox').forEach(cb => cb.checked = false);
    });
  }
})();
JS;
$PAGE->requires->js_init_code($js);

echo $OUTPUT->footer();
