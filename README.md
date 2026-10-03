# local_moveactivities

Bulk move activities from one course to another with fewer clicks.

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
- Database access uses Moodle's DML API and is intended to work with both MySQL/MariaDB and PostgreSQL

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

## Notes

- Includes false-positive delete handling (some module delete flows may return false although module is already gone).
- Uses ad-hoc background tasks with chunked processing to avoid 504 timeouts.
- Runs each background job as the user who created it and re-checks permissions during processing.
- Serialises processing per job to prevent duplicate work from overlapping tasks.
- Job page shows status per item and allows retry of failed items.
- Ensure Moodle cron runs regularly so queued jobs are processed.

Moodle itself and its APIs remain subject to their respective licences.

## License

This is a 108design source-available commercial software license, not an open-source license. See [LICENSE.md](https://github.com/108design/moodle-local_moveactivities/blob/main/LICENSE.md) for the full terms.
