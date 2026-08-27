<?php
// This file is part of a 108design source-available software product.
//
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>
//
// Use and modification are permitted only under the Software License included
// with this distribution. See LICENSE.md for the full terms.

/**
 * Privacy provider for local_moveactivities.
 *
 * @package   local_moveactivities
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license   See LICENSE.md for the full terms.
 */

namespace local_moveactivities\privacy;

use context;
use context_system;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Describes and manages personal data stored in move/copy job history.
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider {

    /**
     * Describe stored personal data.
     *
     * @param collection $collection Metadata collection.
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('local_moveactivities_job', [
            'userid' => 'privacy:metadata:job:userid',
            'sourcecourseid' => 'privacy:metadata:job:sourcecourseid',
            'targetcourseid' => 'privacy:metadata:job:targetcourseid',
            'deletesource' => 'privacy:metadata:job:deletesource',
            'status' => 'privacy:metadata:job:status',
            'timecreated' => 'privacy:metadata:job:timecreated',
            'timemodified' => 'privacy:metadata:job:timemodified',
        ], 'privacy:metadata:job');
        $collection->add_database_table('local_moveactivities_item', [
            'cmid' => 'privacy:metadata:item:cmid',
            'activityname' => 'privacy:metadata:item:activityname',
            'status' => 'privacy:metadata:item:status',
            'message' => 'privacy:metadata:item:message',
            'timecreated' => 'privacy:metadata:item:timecreated',
            'timemodified' => 'privacy:metadata:item:timemodified',
        ], 'privacy:metadata:item');
        return $collection;
    }

    /**
     * Return the system context when the user owns stored jobs.
     *
     * @param int $userid User ID.
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        $contextlist->add_from_sql(
            'SELECT :contextid FROM {local_moveactivities_job} WHERE userid = :userid',
            ['contextid' => context_system::instance()->id, 'userid' => $userid]
        );
        return $contextlist;
    }

    /**
     * Export a user's move/copy job history.
     *
     * @param approved_contextlist $contextlist Approved contexts.
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        if (!in_array(context_system::instance()->id, $contextlist->get_contextids(), true)) {
            return;
        }

        $jobs = $DB->get_records('local_moveactivities_job', ['userid' => $contextlist->get_user()->id], 'id ASC');
        foreach ($jobs as $job) {
            $items = $DB->get_records('local_moveactivities_item', ['jobid' => $job->id], 'id ASC');
            foreach ($items as $item) {
                unset($item->id, $item->jobid);
                $item->timecreated = transform::datetime($item->timecreated);
                $item->timemodified = transform::datetime($item->timemodified);
            }

            $data = clone $job;
            unset($data->id, $data->userid);
            $data->timecreated = transform::datetime($data->timecreated);
            $data->timemodified = transform::datetime($data->timemodified);
            $data->items = array_values($items);
            writer::with_context(context_system::instance())->export_data(
                [get_string('privacy:path', 'local_moveactivities'), (string)$job->id],
                $data
            );
        }
    }

    /**
     * Delete all plugin data in the system context.
     *
     * @param context $context Context to delete.
     */
    public static function delete_data_for_all_users_in_context(context $context): void {
        global $DB;

        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }
        $DB->delete_records('local_moveactivities_item');
        $DB->delete_records('local_moveactivities_job');
    }

    /**
     * Delete the approved user's job history.
     *
     * @param approved_contextlist $contextlist Approved contexts.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        if (!in_array(context_system::instance()->id, $contextlist->get_contextids(), true)) {
            return;
        }
        $jobids = $DB->get_fieldset_select('local_moveactivities_job', 'id', 'userid = :userid',
            ['userid' => $contextlist->get_user()->id]);
        if ($jobids) {
            [$insql, $params] = $DB->get_in_or_equal($jobids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('local_moveactivities_item', "jobid $insql", $params);
        }
        $DB->delete_records('local_moveactivities_job', ['userid' => $contextlist->get_user()->id]);
    }
}
