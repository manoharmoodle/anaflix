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
 * User custom_course_detail
 * @package    block_custom_course_detail
 */
defined('MOODLE_INTERNAL') || die();

function render_coursedetail($courselimit, $showviewall = true) {
    global $DB, $CFG;
    $viewall = $CFG->wwwroot . '/blocks/custom_course_detail/viewall.php';
    $output = '
    <main class="table">
    <section class="table__header">
        ';

    if ($showviewall) {
        $output .= '
        <div class="Course_Pogress">
            <h3> <b>Course Details</b> </h3>
        </div>
        <div class="">
            <button class="viewAll" ><a href = "' . $viewall . '">View all</a></button>
        </div>';
    }

    $output .= '</section>
    <section class="table__body">';
    $table = new html_table();
    $table->id = "viewCoursesTable";
    // $table->width = '100%';
    $table->head = ['Course Name', 'No. of Enrolled Users', 'Course Access Progress', 'Average Course Completion'];
    $coursedetails = custom_get_maximum_enrolment_in_course($courselimit);
    $data = [];
    foreach ($coursedetails as $coursedetail) {
        $list = [];
        $course = get_course($coursedetail['courseid']);
        $coursestats = get_course_stats($course);

        $totalmodule = $DB->get_records_sql_menu('SELECT id, name FROM {modules} WHERE visible = 1');
        $params =[];
        $sno = 1;
        foreach ($totalmodule as $module){
            $params['mod' . $sno] = $module;
            $sno++;
        }
        $params['id'] = $coursedetail['courseid'];
        $params['hidden'] = true;
        $url = new moodle_url('/blocks/analytics_graphs/graphresourceurl.php', $params);

        $courseparams = ['id' => $coursedetail['courseid']];
        $courseurl = new moodle_url('/course/view.php', $courseparams);
        $list[] = '<a href =' . $courseurl . ' >' . $coursedetail['coursefullname'] . '</a>';
        $list[] = '<p class="status delivered">' . $coursedetail['courseenrollment_count'] . '</p>';
        $list[] = '<p class="status Analytics"><a href = "' . $url. '"> View Analytics </a></p>';
        $list[] = '<p class="status Analytics1">' . round(($coursestats['completed']/$coursedetail['courseenrollment_count']) * 100 , 2) . '%</p>';
        $data[] = $list;
    }
    // $table->align = array('center', 'center', 'center', 'center');
    $table->data = ($data) ? $data : 'No records found';
    $output .= html_writer::table($table, 'center');
    $output .= '</section>
    </main>
    ';
    return $output;
}