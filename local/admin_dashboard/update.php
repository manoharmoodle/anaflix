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

require_once('../../config.php');
global $CFG, $DB;
require_once('lib.php');
require_once($CFG->libdir . '/completionlib.php');

$data = $DB->get_records_sql("SELECT tpa.* FROM {tool_policy_acceptances} tpa JOIN {tool_policy} tp ON tp.currentversionid = tpa.policyversionid");
$policy = $DB->get_record_sql("SELECT id FROM {leaderboard_points} WHERE name = 'policy'");
$alldata = [];
foreach ($data as $dataitem) {
    $datapoint = [];
    $datapoint["instanceid"] = $dataitem->id;
    $datapoint["userid"] = $dataitem->userid;
    $datapoint["timecreated"] = $dataitem->timecreated;
    $datapoint["fieldid"] = $policy->id;
    $alldata[] = (object)$datapoint;
}
// $DB->execute("truncate table {l_points_distribution}");
$allcourse = $DB->get_records('course', []);
foreach ($allcourse as $course) {
    $coursedata = get_course_completion_only($course);
    if ($coursedata) {
        $DB->insert_records("l_points_distribution", $coursedata);
    }
}
$id = $DB->insert_records("l_points_distribution", $alldata);
