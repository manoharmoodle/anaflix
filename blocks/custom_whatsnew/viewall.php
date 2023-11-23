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
 * User custom_whatsnew view all
 * @package block_custom_whatsnew
 */
require_once(__DIR__ .'/../../config.php');
global $OUTPUT, $PAGE, $CFG;
require_once($CFG->dirroot . "/blocks/custom_whatsnew/renderer.php");
require_once($CFG->dirroot . "/blocks/custom_courseprogress/locallib.php");

require_login();
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('View all Course Details');
$PAGE->set_title('View all Course Details');
$viewall = $CFG->wwwroot . '/blocks/custom_whatsnew/viewall.php';
$PAGE->set_url($viewall);
$PAGE->requires->css('/blocks/custom_whatsnew/stylemain.css');
// Set page context.
$context = context_system::instance();
$PAGE->set_context($context);
echo $OUTPUT->header();

echo render_coursedetail(0, false);
echo $OUTPUT->footer();
