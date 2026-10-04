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
$string['coursehint'] = 'Search by full name, short name, or ID number. Results include course ID.';
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
$string['moveconfirmquestion'] = 'I confirm that I want to delete the activities in the source course. While they could be copied back easily, this would create a new ID, remove completion settings and other settings may be lost as well.';
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
