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

/**
 * @package   block_custom_reshorts
 * @copyright (c) 2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Gourav Govande
 */

require_once('../../config.php');

global $CFG, $OUTPUT;
require_once($CFG->dirroot . "/blocks/custom_reshorts/renderer.php");
require_once($CFG->dirroot . "/local/admin_dashboard/event/reshort_view.php");
$earn = optional_param('earn', 0, PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);
require_login();
echo $OUTPUT->header();

$url = $CFG->wwwroot . '/blocks/custom_reshorts/moredetails.php?id='.$id.'&earn=1';

if (!$earn) {
    echo html_writer::script('
    setTimeout(function() {
        window.location.replace("'.$url.'");
    }, 30000); // 30 seconds delay
    ');
} else {
    $systemcontext = context_system::instance();
    // Trigger your custom event
    $event = \local_admin_dashboard\event\reshort_view::create([
        'context' => $systemcontext,
        'objectid' => $id, // Make sure to specify the object ID.
        'other' => ['userid' => $USER->id]
    ]);
    $event->trigger();
}
echo render_moredetail($id);
echo $OUTPUT->footer();