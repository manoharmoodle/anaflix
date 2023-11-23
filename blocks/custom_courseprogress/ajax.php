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
 * User custom_courseprogress
 * @package    block_custom_courseprogress
 */
define('AJAX_SCRIPT', true);
require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/../../user/lib.php');
require_login();
$courseid = optional_param('courseid', 0, PARAM_INT);
$fromdate = optional_param('fromdate', 0, PARAM_RAW);
$todate = optional_param('todate', 0, PARAM_RAW);
$noofdays = optional_param('noofdays', 0, PARAM_INT);
$location = optional_param('location', '', PARAM_RAW);

global $DB, $CFG, $USER;
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot . '/blocks/custom_courseprogress/locallib.php');

if ($courseid && $noofdays) {
    $userids = get_course_access_usersid($courseid, $noofdays);

    $allbusinesstype = get_businesstype_enrolled_user_course($courseid);
    $response = [];
    foreach ($userids as $userid) {
        $user = $DB->get_record('user', ['id' => $userid]);
        $extrafields = profile_get_user_fields_with_data($userid);
        $userprofiledata = [];
        foreach ($extrafields as $field) {
            $fieldkey = $field->get_shortname();
            if ($field->is_transform_supported()) {
                $userprofiledata[$fieldkey] = $field->display_data();
            } else {
                $userprofiledata[$fieldkey] = $field->data;
            }
        }
        if ($location) {
            if ($location == $userprofiledata['BaseLocation']) {
                $response[] = $userprofiledata['BusinessType'];
            }
        } else {
            $response[] = $userprofiledata['BusinessType'];
        }

    }
    $response = array_count_values($response);
    unset($response['']);

    foreach ($allbusinesstype as $businesstype) {
        if(!array_key_exists($businesstype, $response)) {

            $response[$businesstype] = 0;
        }
    }
    $coursedetail = $DB->get_record('course', ['id' => $courseid]);
    $location = array_filter(array_unique($location));
    $alldetail = [
        'response' => $response,
        'coursename' => $coursedetail->fullname,
    ];
    echo json_encode($alldetail);
    exit;
}

if ($fromdate && $todate) {
    $activeuser = user_lastaccess($fromdate, $todate, 'active');
    $inactiveuser = user_lastaccess($fromdate, $todate, 'inactive');
    $response = json_encode([
        'activeusers' => count($activeuser),
        'inactiveusers' => count($inactiveuser),
        'activeusersbusinesslines' => array_count_values($activeuser),
        'inactiveusersbusinesslines' => array_count_values($inactiveuser),
    ]);
    echo $response;
    exit;
}

