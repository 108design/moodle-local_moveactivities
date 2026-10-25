/**
 * This file is part of a 108design source-available software product.
 *
 * Use and modification are permitted only under the Software License included
 * with this distribution. See LICENSE.md for the full terms.
 *
 * File documentation for amd/src/jobs.js.
 *
 * @package   local_moveactivities
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license   See LICENSE.md for the full terms.
 */
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>. See LICENSE.md.
/**
 * Refresh the current user's status page while jobs are pending.
 * @module local_moveactivities/jobs
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license See LICENSE.md for the full terms.
 */
export const init = running => {
    if (running) window.setTimeout(() => window.location.reload(), 10000);
};
