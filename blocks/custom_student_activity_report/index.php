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
 * Custom Student Activity Report
 *
 * @package    block_custom_student_activity_report
 * @author     Manohar
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2023 TTMS Limited
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
global $CFG, $DB;
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/blocks/custom_student_activity_report/class.php');
require_once($CFG->dirroot . '/blocks/custom_student_activity_report/filter_form.php');
require_login();
$sort = optional_param('sort', 'num', PARAM_ALPHA);
$dir = optional_param('dir', 'ASC', PARAM_ALPHA);
$perpage = optional_param('perpage', 10, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$userfilter = optional_param('userfilter', '', PARAM_RAW);
$businessline = optional_param('businessline', '', PARAM_RAW);
$download = optional_param('download', '', PARAM_ALPHA);

$data = new stdClass();
$data->keyword = optional_param('keyword', '', PARAM_RAW);
$data->businesstype = optional_param('businesstype', '', PARAM_RAW);
$data->startdate_enabled = optional_param('startdate_enabled', 0, PARAM_INT);
$data->enddate_enabled = optional_param('enddate_enabled', 0, PARAM_INT);
$data->startdate = optional_param('startdate', '', PARAM_RAW);
$data->enddate = optional_param('enddate', '', PARAM_RAW);
$filterform = new custom_student_activity_report_filter_form('', array('data' => $data), 'get');

$table = new leaderboard_table('uniqueid');
$table->is_downloading($download, 'index', 'testing123');
if (!$table->is_downloading()) {
    $PAGE->set_title('Leaderboard');
    $PAGE->set_heading('Leaderboard');
    $PAGE->requires->jquery();
    $PAGE->navbar->add('Leaderboard', "$CFG->wwwroot/blocks/custom_student_activity_report/");
    echo $OUTPUT->header();
    $filterform->display();
}

$params = [];
$wherearray = [];

$cancelurl = new moodle_url('/blocks/custom_student_activity_report/');
if ($filterform->is_cancelled()) {
    redirect($cancelurl);
} else if ($filterformdata = $filterform->get_data()) {
    if ($filterformdata->keyword) {
        $wherearray[] = "u.username LIKE '%$filterformdata->keyword%'";
    }

    if ($filterformdata->businesstype) {
        $wherearray[] = 'uid.data in ("'.$filterformdata->businesstype.'")';
    }

    if ($filterformdata->startdate_enabled) {
        $wherearray[] = "lpd.timecreated > $filterformdata->startdate";
    }

    if ($filterformdata->enddate_enabled) {
        $wherearray[] = "lpd.timecreated < $filterformdata->enddate";
    }
    $filterform->set_data($filterformdata);
}

$wherearray = ($wherearray) ? implode(" and ", $wherearray) . " and" : '';

$fields = 'u.id, @rownum:=@rownum+1 AS num, u.username, uid.data, sum(l.points) AS anacoins';

$from = '{user} u
        JOIN (SELECT @rownum:=0) r
        JOIN {user_info_data} uid ON u.id = uid.userid
        JOIN {user_info_field} uif ON uif.id = uid.fieldid
        JOIN {l_points_distribution} lpd ON lpd.userid = u.id
        JOIN {leaderboard_points} l ON l.id = lpd.fieldid';

$where = '' . $wherearray . ' uif.shortname = "BusinessType" GROUP BY u.id';
$count = $DB->get_records_sql("SELECT $fields FROM $from WHERE $where", $params);
$table->set_sql($fields, $from, $where, $params);
$queryparams = $_GET;
$baseurl = new moodle_url('/blocks/custom_student_activity_report/index.php', ['sort' => $sort, 'dir' => $dir, 'perpage' => $perpage]);
$baseurl .= '&'. http_build_query($queryparams);

$table->define_baseurl($baseurl);
if (count($count)) {
    $table->out(10, true);
} else {
    echo '<h1>Nothing to display</h1>';
}

if (!$table->is_downloading()) {
    $queryparams = $_GET;

    echo $OUTPUT->paging_bar(count($count), $page, $perpage, $baseurl);
    echo "<style>
    .active a{
        background: #904e8e !important;
        color: #ffffff;
    }
    .collection thead th, .generaltable thead th {
        color: #fff;
        height: 50px;
        font-weight: 400;
        border: 0!important;
        padding-left: 1em!important;
        background: #904e8e !important;
        padding-right: 1em!important;
        /* border-radius: 10px; */
    }
    th.header.c0 {
        border-radius: 18px 0 0px 0px;
    }
    th.header.c3 {
        border-radius: 0px 18px 0px 0px;
    }
    .form-inline.text-xs-right {
        /* margin-top: 5px; */
        margin-bottom: 25px;
    }
    select#downloadtype_download {
    
        background: #904e8e !important;
        color: white;
    }
    button.btn.btn-secondary{
        background: #904e8e !important;
        color: white;
    }
    label.mr-1 {
        border-left: 6px solid #904e8e;
        padding-left: 10px;
        border-radius: 6px;
        font-size: 20px;
        color:black;
    }
    </style>
    <script>
    $(document).ready(function() {
        $('nav .pagination').hide();
        $('nav .pagination:last').show();
    })
    </script>
    ";
    echo $OUTPUT->footer();
}
