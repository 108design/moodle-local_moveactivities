<?php
// This file is part of a 108design source-available software product.
//
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>
//
// @package    local_moveactivities
// @copyright  2026 Andreas Giesen <andreas@108design.com>
// @license    See LICENSE.md for the full terms.

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Bulk Activity Transfer';
$string['moveactivities'] = 'Bulk Activity Transfer';
$string['moveactivities:move'] = 'Copy/move activities between courses';
$string['sourcecourse'] = 'Source course';
$string['targetcourse'] = 'Target course';
$string['sourcecoursesearch'] = 'Source course search';
$string['targetcoursesearch'] = 'Target course search';
$string['coursehint'] = 'Enter at least two characters of the name, short name or ID number, or a numeric course ID. Up to 20 matching courses are shown.';
$string['activities'] = 'Activities to copy/move';
$string['activitytypefilter'] = 'Filter by activity type';
$string['sectionfilter'] = 'Filter by section';
$string['selectallvisible'] = 'Select all visible';
$string['clearselection'] = 'Clear selection';
$string['resetfilters'] = 'Reset filters';
$string['targetsectionmode'] = 'Target section handling';
$string['targetsectionmode_sourcecoursename'] = 'Use/create section named as source course';
$string['targetsectionmode_first'] = 'Use first section';
$string['execute'] = 'Copy/move selected activities';
$string['enqueue'] = 'Start background job';
$string['actionmode'] = 'Action';
$string['actionmode_move'] = 'Move (copy and delete source)';
$string['actionmode_copy'] = 'Copy only (keep source)';
$string['moveconfirmwarning'] = 'This will delete the selected activities in the source course.';
$string['moveconfirmquestion'] = 'Moving deletes the selected originals after restoring them in the target course. The restored activities have new IDs. Copying them back later does not restore the original activity IDs or their learner records. Check the source, destination and activities before confirming.';
$string['noactivities'] = 'No movable activities found in this source course.';
$string['resultheader'] = 'Copy/move result';
$string['resultok'] = 'Moved successfully';
$string['resultokcopy'] = 'Copied successfully';
$string['resulterror'] = 'Failed';
$string['jobqueued'] = 'Job queued successfully.';
$string['jobstatus'] = 'Job status';
$string['jobs'] = 'Background jobs';
$string['status'] = 'Status';
$string['created'] = 'Created';
$string['modified'] = 'Last update';
$string['items'] = 'Items';
$string['done'] = 'Done';
$string['failed'] = 'Failed';
$string['queued'] = 'Queued';
$string['running'] = 'Running';
$string['done_with_errors'] = 'Done with errors';
$string['retryfailed'] = 'Retry failed items';
$string['viewjobs'] = 'View jobs';
$string['backtoselection'] = 'Back to copy/move selection';
$string['loadactivities'] = 'Load activities';
$string['selectsourcefirst'] = 'Please select a source course and click "Load activities".';
$string['nojobsyet'] = 'No jobs yet. Create one from the selection page.';
$string['jobid'] = 'Job ID';
$string['source'] = 'Source';
$string['target'] = 'Target';
$string['action'] = 'Action';
$string['progress'] = 'Progress';
$string['details'] = 'Details';
$string['autorefreshhint'] = 'Auto-refresh every 10 seconds while jobs are running.';
$string['refreshnow'] = 'Refresh now';
$string['jobstatus_queued'] = 'Queued';
$string['jobstatus_running'] = 'Running';
$string['jobstatus_done'] = 'Done';
$string['jobstatus_done_with_errors'] = 'Done with errors';
$string['privacy:path'] = 'Move/copy jobs';
$string['privacy:metadata:job'] = 'A move or copy job created by a user.';
$string['privacy:metadata:job:userid'] = 'The user who created the job.';
$string['privacy:metadata:job:sourcecourseid'] = 'The source course ID.';
$string['privacy:metadata:job:targetcourseid'] = 'The target course ID.';
$string['privacy:metadata:job:deletesource'] = 'Whether the source activity is deleted after copying.';
$string['privacy:metadata:job:status'] = 'The processing status of the job.';
$string['privacy:metadata:job:timecreated'] = 'When the job was created.';
$string['privacy:metadata:job:timemodified'] = 'When the job was last updated.';
$string['privacy:metadata:item'] = 'An activity processed as part of a move or copy job.';
$string['privacy:metadata:item:cmid'] = 'The source course module ID.';
$string['privacy:metadata:item:activityname'] = 'The activity name recorded for the job.';
$string['privacy:metadata:item:status'] = 'The processing status of the activity.';
$string['privacy:metadata:item:message'] = 'The processing result or error message.';
$string['privacy:metadata:item:timecreated'] = 'When the job item was created.';
$string['privacy:metadata:item:timemodified'] = 'When the job item was last updated.';

// Contextual administration help.
$string['adminhelp'] = 'Administration help';
$string['adminhelpmore'] = 'More in the help panel';
$string['adminhelptopics'] = 'Help Topics';
$string['adminhelp_overview'] = 'Transferring activities';
$string['adminhelp_overview_article'] = '<p>Select the source course, target course and activities. Before starting, check whether the selected action copies or moves content and whether the intended target section is selected.</p><p>You need the appropriate source, target and Moodle backup or restore permissions. Back up important content before larger transfers.</p>';
$string['adminhelp_results'] = 'Jobs and results';
$string['adminhelp_results_article'] = '<p>Check the job status, then inspect the transferred activities in the target course. Starting a job does not confirm that the transfer is complete.</p><p>If an error occurs, review the existing job\'s result before starting the same selection again. This helps avoid unintended duplicate copies.</p>';

$string['coursesearch'] = 'Search by name, short name or course ID';
$string['confirmmove'] = 'Confirm move';
$string['sourcecourse_help'] = 'Search for a course where you can transfer activities. Enter at least two characters of its name, short name or ID number, or its numeric course ID. Up to 20 matching courses are shown.';
$string['targetcourse_help'] = 'Select a different destination course. You need transfer and activity restore permissions there. Course selection does not change your permissions.';
$string['activities_help'] = 'Load the source activities, then select the ones to transfer. Selection buttons affect only the activities currently listed. Changing the source or filters requires loading the list again.';
$string['activitytypefilter_help'] = 'Select one or more activity types, then load the activities again. Leave this filter empty to include all types.';
$string['sectionfilter_help'] = 'Select one or more source sections, then load the activities again. Reset filters keeps the selected courses.';
$string['targetsectionmode_help'] = 'Use an existing section with the source course name, or create one if needed. Alternatively use the first section. Creating a section requires permission to update the target course.';
$string['actionmode_help'] = 'Copy keeps the originals. Move restores copies in the target course and then deletes each successfully restored original. A separate confirmation page appears before a move is queued.';
$string['jobs_help'] = 'This page shows your own jobs and their per-activity results. Queued and running jobs need Moodle cron. Review failures and the target course before retrying to avoid duplicate copies.';
$string['adminhelp_overview_help'] = 'Check the source, destination, activity selection and copy or move action. The help panel explains permissions and confirmation.';
$string['adminhelp_results_help'] = 'Starting a job does not mean the transfer has finished. The help panel explains progress, results and retries.';
