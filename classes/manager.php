<?php
// This file is part of a 108design source-available software product.
//
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>
//
// @package    local_moveactivities
// @copyright  2026 Andreas Giesen <andreas@108design.com>
// @license    See LICENSE.md for the full terms.

namespace local_moveactivities;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

use backup;
use backup_controller;
use core\task\manager as task_manager;
use context_course;
use context_module;
use core\lock\lock_config;
use restore_controller;

/**
 * Activity move manager.
 */
class manager {
    /** @var int Items processed per task run. */
    public const CHUNK_SIZE = 3;

    /**
     * Return course options user can access.
     *
     * @return array<int,string>
     */
    public static function get_course_options(): array {
        global $DB;

        $courses = $DB->get_records_select('course', 'id <> :siteid', ['siteid' => SITEID], 'fullname ASC', 'id, fullname');
        $options = [];
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            if (has_capability('local/moveactivities:move', $context)) {
                $options[(int)$course->id] = format_string($course->fullname, true, ['context' => $context]);
            }
        }

        return $options;
    }

    /**
     * Return labelled course options incl. id/shortname/idnumber for autocomplete display.
     *
     * @return array<int,string>
     */
    public static function get_course_autocomplete_options(): array {
        global $DB;

        $courses = $DB->get_records_select('course', 'id <> :siteid', ['siteid' => SITEID], 'fullname ASC',
            'id, fullname, shortname, idnumber');
        $options = [];
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            if (!has_capability('local/moveactivities:move', $context)) {
                continue;
            }
            $label = format_string($course->fullname, true, ['context' => $context]) . ' [ID:' . (int)$course->id . ']';
            if (!empty($course->shortname)) {
                $label .= ' | ' . $course->shortname;
            }
            if (!empty($course->idnumber)) {
                $label .= ' | #' . $course->idnumber;
            }
            $options[(int)$course->id] = $label;
        }

        return $options;
    }

    /**
     * Return movable activities from source course.
     *
     * @param int $sourcecourseid
     * @return array<int,string>
     */
    public static function get_source_activities(int $sourcecourseid): array {
        return self::get_source_activities_filtered($sourcecourseid, [], []);
    }

    /**
     * Return movable activities from source course with optional filters.
     *
     * @param int $sourcecourseid
     * @param array<int,string> $modtypes
     * @param array<int,int> $sectionnums
     * @return array<int,string>
     */
    public static function get_source_activities_filtered(int $sourcecourseid, array $modtypes = [], array $sectionnums = []): array {
        $modinfo = get_fast_modinfo($sourcecourseid);
        $options = [];
        foreach ($modinfo->get_cms() as $cm) {
            if ($cm->deletioninprogress || !$cm->uservisible) {
                continue;
            }
            if (!empty($modtypes) && !in_array($cm->modname, $modtypes, true)) {
                continue;
            }
            if (!empty($sectionnums) && !in_array((int)$cm->sectionnum, $sectionnums, true)) {
                continue;
            }
            $options[(int)$cm->id] = '[' . $cm->modname . '] ' . format_string($cm->name);
        }
        return $options;
    }

    /**
     * Get available module type filter options for a source course.
     *
     * @param int $sourcecourseid
     * @return array<string,string>
     */
    public static function get_source_modtype_options(int $sourcecourseid): array {
        $modinfo = get_fast_modinfo($sourcecourseid);
        $types = [];
        foreach ($modinfo->get_cms() as $cm) {
            if ($cm->deletioninprogress) {
                continue;
            }
            // Keep module types explicitly distinct (e.g. hvp vs h5pactivity).
            $types[$cm->modname] = $cm->modname . ' — ' . $cm->modplural;
        }
        asort($types);
        return $types;
    }

    /**
     * Get available section filter options for a source course.
     *
     * @param int $sourcecourseid
     * @return array<int,string>
     */
    public static function get_source_section_options(int $sourcecourseid): array {
        $modinfo = get_fast_modinfo($sourcecourseid);
        $sections = [];
        foreach ($modinfo->get_section_info_all() as $section) {
            if ((int)$section->section < 0) {
                continue;
            }
            $label = trim((string)$section->name);
            if ($label === '') {
                $label = get_string('section') . ' ' . (int)$section->section;
            }
            $sections[(int)$section->section] = $label;
        }
        return $sections;
    }

    /**
     * Ensure target section exists and return section number.
     *
     * @param int $targetcourseid
     * @param string $sourcename
     * @param bool $usesourcename
     * @return int
     */
    public static function resolve_target_sectionnum(int $targetcourseid, string $sourcename, bool $usesourcename): int {
        global $DB;

        if (!$usesourcename) {
            course_create_sections_if_missing($targetcourseid, [1]);
            return 1;
        }

        $sections = $DB->get_records('course_sections', ['course' => $targetcourseid], 'section ASC', 'id, section, name');
        foreach ($sections as $section) {
            if (trim((string)$section->name) !== '' && trim($section->name) === trim($sourcename)) {
                return (int)$section->section;
            }
        }

        $maxsection = 0;
        foreach ($sections as $section) {
            $maxsection = max($maxsection, (int)$section->section);
        }

        $newsectionnum = $maxsection + 1;
        course_create_sections_if_missing($targetcourseid, [$newsectionnum]);
        rebuild_course_cache($targetcourseid, true);

        $sectionrecord = $DB->get_record('course_sections', ['course' => $targetcourseid, 'section' => $newsectionnum], '*', MUST_EXIST);
        $sectionrecord->name = $sourcename;
        $DB->update_record('course_sections', $sectionrecord);

        return $newsectionnum;
    }

    /**
     * Move one activity by backup/restore then delete original.
     *
     * @param int $cmid
     * @param int $targetcourseid
     * @param int $targetsectionnum
     * @return array{success:bool,message:string}
     */
    public static function move_activity(int $cmid, int $targetcourseid, int $targetsectionnum, bool $deletesource = true): array {
        global $USER;

        try {
            $cm = get_coursemodule_from_id(null, $cmid, 0, false, MUST_EXIST);
            $sourcecourse = get_course($cm->course);

            require_capability('local/moveactivities:move', context_course::instance($sourcecourse->id));
            require_capability('local/moveactivities:move', context_course::instance($targetcourseid));
            require_capability('moodle/backup:backupactivity', context_module::instance($cmid));
            require_capability('moodle/restore:restoreactivity', context_course::instance($targetcourseid));

            $before = array_keys(get_fast_modinfo($targetcourseid)->get_cms());

            $bc = new backup_controller(
                backup::TYPE_1ACTIVITY,
                $cmid,
                backup::FORMAT_MOODLE,
                backup::INTERACTIVE_NO,
                backup::MODE_IMPORT,
                $USER->id
            );
            $bc->execute_plan();
            $backupid = $bc->get_backupid();
            $bc->destroy();

            $rc = new restore_controller(
                $backupid,
                $targetcourseid,
                backup::INTERACTIVE_NO,
                backup::MODE_IMPORT,
                $USER->id,
                backup::TARGET_CURRENT_ADDING
            );
            if (!$rc->execute_precheck()) {
                $rc->destroy();
                return ['success' => false, 'message' => get_string('resulterror', 'local_moveactivities') . ': precheck failed'];
            }
            $rc->execute_plan();
            $rc->destroy();

            $after = array_keys(get_fast_modinfo($targetcourseid)->get_cms());
            $newcmids = array_values(array_diff($after, $before));

            if (empty($newcmids)) {
                return ['success' => false, 'message' => get_string('resulterror', 'local_moveactivities') . ': no restored module found'];
            }

            $newcm = get_coursemodule_from_id(null, (int)$newcmids[0], 0, false, MUST_EXIST);
            $targetmodinfo = get_fast_modinfo($targetcourseid);
            $targetsection = $targetmodinfo->get_section_info($targetsectionnum);
            if (!$targetsection) {
                return ['success' => false, 'message' => get_string('resulterror', 'local_moveactivities') . ': target section not found'];
            }
            moveto_module($newcm, $targetsection);

            if ($deletesource) {
                $deleted = course_delete_module($cmid);
                if (!$deleted) {
                    // Some module flows may return false even when deletion already happened.
                    if (get_coursemodule_from_id(null, $cmid, 0, false, IGNORE_MISSING)) {
                        return ['success' => false, 'message' => get_string('resulterror', 'local_moveactivities') . ': source delete failed'];
                    }
                }
                return ['success' => true, 'message' => get_string('resultok', 'local_moveactivities')];
            }

            return ['success' => true, 'message' => get_string('resultokcopy', 'local_moveactivities')];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => get_string('resulterror', 'local_moveactivities') . ': ' . $e->getMessage()];
        }
    }

    /**
     * Create job with items and queue task.
     *
     * @param int $userid
     * @param int $sourcecourseid
     * @param int $targetcourseid
     * @param string $targetsectionmode
     * @param bool $deletesource
     * @param array<int,int> $cmids
     * @param array<int,string> $activitylabels
     * @return int
     */
    public static function create_job(
        int $userid,
        int $sourcecourseid,
        int $targetcourseid,
        string $targetsectionmode,
        bool $deletesource,
        array $cmids,
        array $activitylabels
    ): int {
        global $DB;

        $targetsectionmode = $targetsectionmode === 'sourcecoursename' ? 'sourcecoursename' : 'first';
        $now = time();
        $job = (object)[
            'userid' => $userid,
            'sourcecourseid' => $sourcecourseid,
            'targetcourseid' => $targetcourseid,
            'targetsectionmode' => $targetsectionmode,
            'deletesource' => $deletesource ? 1 : 0,
            'status' => 'queued',
            'timecreated' => $now,
            'timemodified' => $now,
        ];
        $jobid = (int)$DB->insert_record('local_moveactivities_job', $job);

        foreach ($cmids as $cmid) {
            $item = (object)[
                'jobid' => $jobid,
                'cmid' => (int)$cmid,
                'activityname' => $activitylabels[$cmid] ?? ('CMID ' . $cmid),
                'status' => 'queued',
                'message' => null,
                'timecreated' => $now,
                'timemodified' => $now,
            ];
            $DB->insert_record('local_moveactivities_item', $item);
        }

        self::queue_job_task($jobid, $userid);
        return $jobid;
    }

    /** Queue processor task. */
    public static function queue_job_task(int $jobid, int $userid): void {
        $task = new \local_moveactivities\task\process_job();
        $task->set_custom_data(['jobid' => $jobid]);
        $task->set_userid($userid);
        task_manager::queue_adhoc_task($task);
    }

    /** Process one chunk of queued items. */
    public static function process_job(int $jobid): void {
        global $DB;

        $lockfactory = lock_config::get_lock_factory('local_moveactivities');
        $lock = $lockfactory->get_lock('job_' . $jobid, 0);
        if (!$lock) {
            return;
        }

        try {
            $job = $DB->get_record('local_moveactivities_job', ['id' => $jobid], '*', MUST_EXIST);
            if (in_array($job->status, ['done', 'done_with_errors'], true)) {
                return;
            }
            $job->status = 'running';
            $job->timemodified = time();
            $DB->update_record('local_moveactivities_job', $job);

            $sourcecourse = get_course((int)$job->sourcecourseid);
            $targetsectionnum = self::resolve_target_sectionnum(
                (int)$job->targetcourseid,
                $sourcecourse->fullname,
                $job->targetsectionmode === 'sourcecoursename'
            );

            $items = $DB->get_records('local_moveactivities_item', ['jobid' => $jobid, 'status' => 'queued'],
                'id ASC', '*', 0, self::CHUNK_SIZE);

            foreach ($items as $item) {
                $result = self::move_activity(
                    (int)$item->cmid,
                    (int)$job->targetcourseid,
                    $targetsectionnum,
                    (bool)$job->deletesource
                );
                $item->status = $result['success'] ? 'done' : 'failed';
                $item->message = $result['message'];
                $item->timemodified = time();
                $DB->update_record('local_moveactivities_item', $item);
            }

            rebuild_course_cache((int)$job->targetcourseid, true);
            if ((bool)$job->deletesource) {
                rebuild_course_cache((int)$job->sourcecourseid, true);
            }

            $remaining = $DB->count_records('local_moveactivities_item', ['jobid' => $jobid, 'status' => 'queued']);
            $failed = $DB->count_records('local_moveactivities_item', ['jobid' => $jobid, 'status' => 'failed']);

            $job->status = $remaining > 0 ? 'running' : ($failed > 0 ? 'done_with_errors' : 'done');
            $job->timemodified = time();
            $DB->update_record('local_moveactivities_job', $job);

            if ($remaining > 0) {
                self::queue_job_task($jobid, (int)$job->userid);
            }
        } finally {
            $lock->release();
        }
    }

    /**
     * Get jobs for user.
     * @return array<int,\stdClass>
     */
    public static function get_jobs_for_user(int $userid): array {
        global $DB;
        return $DB->get_records('local_moveactivities_job', ['userid' => $userid], 'id DESC');
    }

    /** Retry failed items in a job. */
    public static function retry_failed_items(int $jobid, int $userid): void {
        global $DB;
        $job = $DB->get_record('local_moveactivities_job', ['id' => $jobid, 'userid' => $userid], '*', MUST_EXIST);
        $faileditems = $DB->get_records('local_moveactivities_item', ['jobid' => $jobid, 'status' => 'failed']);
        foreach ($faileditems as $item) {
            $item->status = 'queued';
            $item->message = null;
            $item->timemodified = time();
            $DB->update_record('local_moveactivities_item', $item);
        }
        $job->status = 'queued';
        $job->timemodified = time();
        $DB->update_record('local_moveactivities_job', $job);
        self::queue_job_task($jobid, $userid);
    }
}
