# local_moveactivities (Moodle 4.5 LTS)

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

The UI now queues a background job and returns quickly (to avoid request timeouts).

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

## Notes

- This is an MVP implementation with a simple GUI.
- Includes false-positive delete handling (some module delete flows may return false although module is already gone).
- Uses ad-hoc background tasks with chunked processing to avoid 504 timeouts.
- Job page shows status per item and allows retry of failed items.
- Ensure Moodle cron runs regularly so queued jobs are processed.
