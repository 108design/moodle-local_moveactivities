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

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Copy/move activities between courses';
$string['moveactivities'] = 'Copy/move activities';
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
$string['privacy:metadata'] = 'The local_moveactivities plugin does not store personal data.';
