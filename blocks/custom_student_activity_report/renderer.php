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
 * User custom_student_detail
 * @package    block_custom_student_detail
 */
defined('MOODLE_INTERNAL') || die();

function render_LeaderBoard($limit = 5) {
    global $DB, $CFG, $OUTPUT, $USER;
    $alltable = $DB->get_records_sql("SELECT u.id, @rownum:=@rownum+1 AS num, u.username, uid.data, sum(l.points) AS anacoins FROM {user} u 
    JOIN (SELECT @rownum:=0) r 
    JOIN {user_info_data} uid ON u.id = uid.userid
    JOIN {user_info_field} uif ON uif.id = uid.fieldid 
    JOIN {l_points_distribution} lpd ON lpd.userid = u.id 
    JOIN {leaderboard_points} l ON l.id = lpd.fieldid WHERE uif.shortname = 'BusinessType' GROUP by u.id ORDER BY anacoins DESC LIMIT $limit");
    $myancoins = $DB->get_record_sql("SELECT sum(l.points) as sum FROM {l_points_distribution} lpd JOIN {leaderboard_points} l ON l.id = lpd.fieldid WHERE lpd.userid = $USER->id");

    $html = '
    <style>
        a.d-inline-block.aabtn {
    width: 49px;
}
    </style>
    
    <main class="table">
    <section class="table__header">
        <div class="Course_Pogress">
            <h3> <b>ANACOIN Leaderboard – Employees with highest score</b> </h3>
        </div>
        <div class="">
            <h4> <b>Your Anacoins : ' . $myancoins->sum . '</b> </h4>
            
        </div>

        ';
        if (is_siteadmin()) {
            $html .= '<div class="LeaderBoar_select_area">
            <button class="viewAll" ><a href="'.$CFG->wwwroot."/blocks/custom_student_activity_report/".'">View More</a></button>

        </div>';
        }

    $html.= '</section>
    <section class="table__body">
        <table>
            <thead>
                <tr>
                    <th> Rank </th>
                    <th> Employee name</th>
                    <th> Business </th>
                    <th> ANACOINs collected</th>
                </tr>
            </thead>
            <tbody>';
            $i = 1;
            foreach($alltable as $table) {
                $user = $DB->get_record('user', ['id'=>$table->id]);
                $html .='<tr>
                    <td>'.$i.'</td>
                    <td>'.$OUTPUT->user_picture($user, [
                        'size' => 100, // Set the desired size
                    ]).''.$table->username.' </td>
                    <td> '.$table->data.' </td>
              
                    <td> '.$table->anacoins.' </td>
                </tr>';
                $i++;
            }
            $html .= '</tbody>
        </table>
    </section>
</main>';
    return $html;
}