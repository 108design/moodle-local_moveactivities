<?php
// This file is part of a 108design source-available software product.
//
// Copyright (C) 2026 Andreas Giesen <andreas@108design.com>
//
// @package    local_moveactivities
// @copyright  2026 Andreas Giesen <andreas@108design.com>
// @license    See LICENSE.md for the full terms.

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_moveactivities',
        get_string('pluginname', 'local_moveactivities'),
        new moodle_url('/local/moveactivities/index.php'),
        'local/moveactivities:move'
    ));
}
