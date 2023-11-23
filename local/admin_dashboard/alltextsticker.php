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
require_login();
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);
global $CFG, $OUTPUT;
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('All TextSticker');
$PAGE->set_title('All Textsticker');
$PAGE->set_url($CFG->wwwroot . '/local/admin_dashboard/alltextsticker.php');
$PAGE->navbar->ignore_active();
$PAGE->navbar->add('Add Textsticker', '/local/admin_dashboard/textsticker_form.php');
$PAGE->navbar->add('All Textsticker', '');
// Set page context.
$context = context_system::instance();
$PAGE->set_context($context);

echo $OUTPUT->header();
$offset = ($page) * $perpage;
$count = $DB->get_record_sql('SELECT count(*) as count FROM {textstickers}');
$alltextstickers = $DB->get_records_sql('SELECT * FROM {textstickers} LIMIT '.$perpage.' OFFSET '.$offset.'');
echo render_alltextstickers($alltextstickers, $offset);
$baseurl = $CFG->wwwroot . '/local/admin_dashboard/alltextsticker.php';

echo $OUTPUT->paging_bar($count->count, $page, $perpage, $baseurl);
echo $OUTPUT->footer();
