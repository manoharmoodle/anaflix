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

use block_custom_courseprogress\fetcher;
/**
 * @package   block_custom_courseprogress
 * @copyright (c) 2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Gourav Govande
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . "/blocks/custom_courseprogress/locallib.php");
require_once($CFG->dirroot . "/blocks/custom_courseprogress/renderer.php");

class block_custom_courseprogress extends block_base {
    public function init() {
        $this->title = get_string('custom_courseprogress', 'block_custom_courseprogress');
    }
    public function get_content() {
        global $OUTPUT, $CFG, $DB, $PAGE;
        $PAGE->requires->jquery();
        $PAGE->requires->jquery('ui');
        $this->content = new stdClass;
        $html = ' <div class="admin_area">';
        $html .= '<div class="">';
        $html .= render_welcome_admin();
        $html .= render_course_access_report();
        $html .= '</div>';
        $html .= render_login_activity();
        $html .= '</div>';

        $timetoshowusers = 300; //Seconds default
        if (isset($CFG->block_online_users_timetosee)) {
            $timetoshowusers = $CFG->block_online_users_timetosee * 60;
        }
        $to = time();

        //Calculate if we are in separate groups
        $isseparategroups = ($this->page->course->groupmode == SEPARATEGROUPS
                             && $this->page->course->groupmodeforce
                             && !has_capability('moodle/site:accessallgroups', $this->page->context));

        //Get the user current group
        $currentgroup = $isseparategroups ? groups_get_course_group($this->page->course) : NULL;
        $from = '';
        $sitelevel = $this->page->course->id == SITEID || $this->page->context->contextlevel < CONTEXT_COURSE;
        $onlineusers = new fetcher($currentgroup, $timetoshowusers, $this->page->context,
                $sitelevel, $this->page->course->id, '1690351005', $to);
                foreach ($onlineusers->get_users() as $user) {

                }
        $totalnoofactiveuser = $onlineusers->count_users();
        $totalnoofuser = $DB->get_record_sql("SELECT COUNT(u.id) as usercount FROM {user} u WHERE u.deleted = 0 and u.confirmed = 1")->usercount;
        $totalnoofinactiveuser = $totalnoofuser - $totalnoofactiveuser;


        $templatecontext = [];
        $templatecontext['blockstyle'] = $CFG->wwwroot . '/blocks/custom_courseprogress/style.css';

        $templatecontext['blockhtml'] = custom_pre_process_html(format_text($html, FORMAT_HTML, array("noclean" => true)), $this->instance->id);
        $templatecontext['blockcss'] = $CFG->wwwroot . '/blocks/custom_courseprogress/stylemain.css';
        $templatecontext['blockjs'] = $CFG->wwwroot . '/blocks/custom_courseprogress/script.js';

        $this->content->text = "";

        $this->content->text .= $OUTPUT->render_from_template('block_custom_courseprogress/blockcontent', $templatecontext);

        return $this->content;
    }
    public function instance_allow_multiple() {
        return true;
    }
    public function has_config() {
        return true;
    }
    public function hide_header() {
        return true;
    }
    public function applicable_formats() {

        $allow = [];
        $allow['all'] = true;
        return $allow;
    }
    
}
