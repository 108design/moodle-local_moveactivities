# Move Activities for Moodle

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

This is a 108design source-available commercial software license, not an open-source license. See [LICENSE.md](https://github.com/108design/moodle-local_moveactivities/blob/main/LICENSE.md) for the full terms.
