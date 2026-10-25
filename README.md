<p align="center">
  <img src="https://raw.githubusercontent.com/108design/moodle-local_moveactivities/main/docs/branding/logo.svg" alt="Bulk Activity Transfer logo" width="125" height="125">
</p>

# Bulk Activity Transfer

Copy or move multiple activities between Moodle courses in one background job.

## Screenshots

<details>
<summary>View screenshots (4)</summary>

Click a preview to open the full-size screenshot.

<table>
<tr>
<td align="center" width="50%" valign="middle">
<a href="https://raw.githubusercontent.com/108design/moodle-local_moveactivities/main/docs/screenshots/copy-move-entry.jpg"><img src="https://raw.githubusercontent.com/108design/moodle-local_moveactivities/main/docs/screenshots/copy-move-entry.jpg" width="300" height="135" alt="Select the source and target courses"></a><br>
<sub>Select the source and target courses</sub>
</td>
<td align="center" width="50%" valign="middle">
<a href="https://raw.githubusercontent.com/108design/moodle-local_moveactivities/main/docs/screenshots/copy-move-activities.jpg"><img src="https://raw.githubusercontent.com/108design/moodle-local_moveactivities/main/docs/screenshots/copy-move-activities.jpg" width="279" height="160" alt="Filter and select activities to copy or move"></a><br>
<sub>Filter and select activities to copy or move</sub>
</td>
</tr>
<tr>
<td align="center" width="50%" valign="middle">
<a href="https://raw.githubusercontent.com/108design/moodle-local_moveactivities/main/docs/screenshots/copy-move-move-alert.jpg"><img src="https://raw.githubusercontent.com/108design/moodle-local_moveactivities/main/docs/screenshots/copy-move-move-alert.jpg" width="270" height="160" alt="Review the warning before moving activities"></a><br>
<sub>Review the warning before moving activities</sub>
</td>
<td align="center" width="50%" valign="middle">
<a href="https://raw.githubusercontent.com/108design/moodle-local_moveactivities/main/docs/screenshots/copy-move-move-warning.jpg"><img src="https://raw.githubusercontent.com/108design/moodle-local_moveactivities/main/docs/screenshots/copy-move-move-warning.jpg" width="282" height="160" alt="Confirm deletion of the source activities"></a><br>
<sub>Confirm deletion of the source activities</sub>
</td>
</tr>
</table>

</details>

## What it does

- Search for source and target courses by name, short name, ID number or numeric course ID.
- Filter the source activities by type or section and select the activities to transfer.
- Choose Copy only (the default) or Move. Move shows a separate confirmation with the selected courses and activities before it queues the job.
- Process in background job:
  1. Backup selected activity
  2. Restore into target course
  3. Place in section named as source course (or first section)
  4. Optionally delete original activity from source course (move mode)

The UI queues a background job and returns quickly to avoid request timeouts.

## Compatibility

- Moodle 4.5 through 5.3 inclusive
- PHP versions supported by the selected Moodle release


## URL

After installation:

`/local/moveactivities/index.php`

`/local/moveactivities/jobs.php`

## Required capabilities

- `local/moveactivities:move` in both courses
- `moodle/backup:backupactivity` for each source activity
- `moodle/restore:restoreactivity` in the target course
- `moodle/course:manageactivities` for each source activity when moving
- `moodle/course:update` in the target course when a destination section needs to be created

Default role grants in this plugin:

- Editing teacher
- Manager

## Privacy

The plugin stores the initiating user ID and job history, including source and
target course IDs, activity names, status messages, and timestamps. Its Moodle
Privacy API provider supports metadata declaration, export, and deletion.

## Following a job

Moodle cron must run regularly to process queued jobs. Open the job page to follow
each activity's progress and retry failed items. You see only your own jobs.
Jobs run as the person who started them and check their current permissions during
processing. A move deletes the original activity only after the copy has been restored.

The question-mark buttons explain individual fields. Information icons link to the
transfer or job-results topic in the help panel. English and German interfaces are available.

## License

**This release is available free of charge under the 108design Software License.**

See [LICENSE.md](https://github.com/108design/moodle-local_moveactivities/blob/main/LICENSE.md) for the full terms.
