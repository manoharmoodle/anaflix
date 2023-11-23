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
 * Plugin administration pages are defined here.
 *
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ .'/../../config.php');
require_once('renderer.php');
global $OUTPUT, $PAGE, $CFG;
require_login();
$update = optional_param('edit', 0, PARAM_RAW);
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Textsticker Form');
$PAGE->set_url($CFG->wwwroot . '/local/admin_dashboard/textsticker_form.php');
// Set page context.
$context = context_system::instance();
$PAGE->set_context($context);
echo $OUTPUT->header();
if ($update) {
    $textstciker = $DB->get_record('textstickers', ['id' => $update]);
    $from = date('Y-m-d', $textstciker->fromdate);
    $to = date('Y-m-d', $textstciker->todate);
    $image = $CFG->wwwroot . '/local/admin_dashboard/textsticker_files/' . $textstciker->imagepath;
    $text = $textstciker->text;
}

echo render_textstickers_form($text, $image, $from, $to, $update);
echo $OUTPUT->footer();
