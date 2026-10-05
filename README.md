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

- Select source and target course
- Select multiple activities from source course
- Choose action mode: **Move** or **Copy only**
- Process in background job:
  1. Backup selected activity
  2. Restore into target course
  3. Place in section named as source course (or first section)
  4. Optionally delete original activity from source course (move mode)

The UI queues a background job and returns quickly to avoid request timeouts.

## Compatibility

- Moodle 4.5 through 5.2 inclusive
- PHP versions supported by the selected Moodle release


## URL

After installation:

`/local/moveactivities/index.php`

`/local/moveactivities/jobs.php`

## Required capabilities

- `local/moveactivities:move`
- `moodle/backup:backupactivity`
- `moodle/restore:restoreactivity`

Default role grants in this plugin:

- Editing teacher
- Manager

## Privacy

The plugin stores the initiating user ID and job history, including source and
target course IDs, activity names, status messages, and timestamps. Its Moodle
Privacy API provider supports metadata declaration, export, and deletion.

## Following a job

Moodle cron must run regularly to process queued jobs. Open the job page to follow
each activity's progress and retry failed items. Jobs use the permissions of the
person who started them; that person must retain access to both courses.

## License

**This release is available free of charge under the 108design Software License.**

See [LICENSE.md](https://github.com/108design/moodle-local_moveactivities/blob/main/LICENSE.md) for the full terms.
