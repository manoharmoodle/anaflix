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
 * @package   block_custom_courseprogress
 * @copyright (c) 2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Gourav Govande
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;

function block_custom_courseprogress_get_config($instanceid) {
    global $DB;
    if (is_null($instanceid)  || !is_numeric($instanceid)) {
        return null;
    }

    $blockrecord = $DB->get_record('block_instances', ['id' => $instanceid]);
    if (!$blockrecord) {
        return null;
    }

    $instance = block_instance($blockrecord->blockname, $blockrecord);

    return $instance->config;
}

function custom_get_wrapped_css( $css, $instid ) {
    global $CFG;
    if ($css == "") {
        return "";
    }

    $css = str_replace(".m-0.p-50.editingbody", ".blockcontent", $css);

    try {
        require_once($CFG->libdir . "/classes/scss.php");

        $scss = "#inst" . $instid . "{" . htmlspecialchars_decode($css) . "}";
        $scssprocessor = new core_scss();
        $scssprocessor->append_raw_scss($scss);
        $css = $scssprocessor->to_css();

    } catch (Exception $e) {
        return "";
    }

    return $css;
}

function custom_pre_process_html($html, $instanceid) {

    // Replacing the CDN URL;
    $html = custom_replace_cdn_url($html);
    $html = custom_replace_instance_id($html, $instanceid);
    return $html;
}

function custom_pre_process_css($css, $instid) {

    $css = custom_replace_cdn_url($css);

    return custom_get_wrapped_css($css, $instid);
}
function custom_replace_instance_id($content, $instanceid) {
    return str_replace("[[inst]]", $instanceid, $content);
}

function custom_replace_cdn_url($content) {
    // Replacing the CDN URL;
    return str_replace("{{>cdnurl}}", CDNIMAGES, $content);
}

function analyze_course_progress($content) {
    // Replacing the CDN URL;
    return str_replace("{{>cdnurl}}", CDNIMAGES, $content);
}

function custom_course_completion_percentage($courseid) {
    global $CFG, $DB, $USER;
    $sql = "
    SELECT COUNT(DISTINCT userid) AS completion_count
    FROM {course_completions} cc JOIN {user} u on u.id = cc.userid
    WHERE u.deleted = 0 and u.confirmed = 1 and cc.course = :courseid
    ";
    $params = ['courseid' => $courseid];

    $completionCount = $DB->get_record_sql($sql, $params)->completion_count;

    $contextid = context_course::instance($courseid);
    $enrollusercount = count(get_enrolled_users($contextid));

    return [
        'coursecompleteuser' => $completionCount,
        'totaluserenrolled' => $enrollusercount
    ];
}

function custom_get_maximum_enrolment_in_course($limit = 0) {
    global $DB;
    $sql = "
    SELECT c.id AS course_id, c.fullname AS course_name, COUNT(DISTINCT u.id) AS enrollment_count
    FROM {course} c
    JOIN {enrol} en ON en.courseid = c.id
    JOIN {user_enrolments} ue ON ue.enrolid = en.id
    JOIN {user} u ON u.id = ue.userid
    WHERE u.deleted = 0 and u.confirmed = 1
    GROUP BY c.id, c.fullname
    ORDER BY enrollment_count DESC
    ";

    if ($limit) {
        $sql .= " LIMIT $limit";
    }
    $topEnrolledCourses = $DB->get_records_sql($sql);

    $all = [];
    foreach ($topEnrolledCourses as $course) {
        // Retrieve the course record
        $coursedetail = $DB->get_record('course', array('id' => $course->course_id), '*', MUST_EXIST);
        $imageurl = \core_course\external\course_summary_exporter::get_course_image($coursedetail);
        $all[] = [
            'coursefullname' => $course->course_name,
            'courseenrollment_count' => $course->enrollment_count,
            'courseimageurl' => $imageurl,
            'courseurl' => new moodle_url('/course/view.php', ['id' => $course->course_id]),
            'courseid' => $course->course_id
        ];
    }
    return $all;
}

function custom_get_maximum_active_user_in_course($limit = 5) {
    global $DB;
    $last30daystimestamp = strtotime('-30 days');
    $sql = "SELECT mls.courseid, count(DISTINCT mls.userid) as usercount
    FROM  {user} mu 
    JOIN {logstore_standard_log} mls ON mu.id = mls.userid JOIN {course} c ON c.id = mls.courseid WHERE mls.timecreated > $last30daystimestamp AND
    mls.`action`= 'viewed' AND mu.deleted = 0 AND mls.courseid NOT IN (0,1)
    GROUP BY mls.courseid ORDER BY usercount DESC LIMIT $limit";
    $topactiveuserscourse = $DB->get_records_sql($sql);

    $all = [];
    foreach ($topactiveuserscourse as $course) {
        // Retrieve the course record
        $coursedetail = $DB->get_record('course', array('id' => $course->courseid), '*', MUST_EXIST);
        $imageurl = \core_course\external\course_summary_exporter::get_course_image($coursedetail);
        $all[] = [
            'coursefullname' => $coursedetail->fullname,
            'courseusercount' => $course->usercount,
            'courseimageurl' => $imageurl,
            'courseurl' => new moodle_url('/course/view.php', ['id' => $course->courseid]),
            'courseid' => $course->courseid
        ];
    }
    return $all;
}
function get_course_stats($course) {
    global $DB;
    $stats = array();
    // This capability is allowed to only students - 'moodle/course:isincompletionreports'.
    $enrolledusers = get_enrolled_users(\context_course::instance($course->id), 'moodle/course:isincompletionreports');
    $stats['completed'] = 0;
    $stats['inprogress'] = 0;
    $stats['notstarted'] = 0;
    $stats['enrolledusers'] = count($enrolledusers);
    // Check if completion is enabled.
    $completion = new completion_info($course);
    if ($completion->is_enabled()) {
        $onlystudents = implode(",", array_keys($enrolledusers));
        $modules = $completion->get_activities();
        $count = count($modules);

        $inprogress = 0;
        $completed = 0;
        if ($completion->is_enabled() && $count) {

            $completions = $DB->get_records_sql(
                "SELECT cmc.userid, sum(
                    CASE
                        WHEN cmc.completionstate <> 0 THEN 1
                        ELSE 0
                    END
                ) as total
                   FROM {course_modules} cm
              LEFT JOIN {course_modules_completion} cmc ON cm.id = cmc.coursemoduleid
                  WHERE cm.course = ?
                    AND cmc.userid IS NOT NULL
                    AND cmc.userid IN ($onlystudents)
               GROUP BY cmc.userid
               ORDER BY cmc.userid DESC", array($course->id));

            $context = context_course::instance($course->id);

            foreach ($completions as $user) {
                if (!is_enrolled($context, $user->userid, '', true)) {
                    continue;
                }
                if ($user->total == $count) {
                    $completed++;
                } else if ($user->total != 0) {
                    $inprogress++;
                }
            }
        }
        $notstarted = count($enrolledusers) - $completed - $inprogress;

        $stats['completed'] = $completed;
        $stats['inprogress'] = $inprogress;
        $stats['notstarted'] = $notstarted;
    }
    return $stats;
}
function user_lastaccess($from, $to, $userstate) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/user/profile/lib.php');
    $fromtimestamp=strtotime($from);
    $totimestamp=strtotime($to);
    $state = '';
    if ($userstate == 'inactive') {
        $state = 'NOT';
    }
    $users = $DB->get_records_sql("SELECT id FROM {user} WHERE deleted = 0 and username != 'guest' AND lastaccess $state between $fromtimestamp AND $totimestamp ORDER BY lastaccess ASC");

    $userids = [];
    foreach ($users as $user) {
        $extrafields = profile_get_user_fields_with_data($user->id);
        $userprofiledata = '';
        foreach ($extrafields as $field) {
            if($field->get_shortname() == 'BusinessType') {
                if ($field->is_transform_supported()) {
                    $userprofiledata = $field->display_data();
                } else {
                    $userprofiledata = $field->data;
                }
            }
        }

        $userids[$user->id] = $userprofiledata;
    }

    return $userids;
}

/**
 * Summary of last30daysstr
 * @return array
 */
function last30daysstr() {
    $currentDate = new DateTime();

    // Get the date for 30 days ago
    $thirtyDaysAgo = new DateTime();
    $thirtyDaysAgo->sub(new DateInterval('P30D'));

    // Format the dates as strings
    $to = $currentDate->format('Y-m-d');
    $from = $thirtyDaysAgo->format('Y-m-d');
    return [
        'to' => $to,
        'from' => $from
    ];
}

function locationby() {
    $currentDate = new DateTime();

    // Get the date for 30 days ago
    $thirtyDaysAgo = new DateTime();
    $thirtyDaysAgo->sub(new DateInterval('P30D'));

    // Format the dates as strings
    $to = $currentDate->format('Y-m-d');
    $from = $thirtyDaysAgo->format('Y-m-d');
    return [
        'to' => $to,
        'from' => $from
    ];
}

/**
 * Summary of get_businesstype_enrolled_user_course
 * @param mixed $courseid
 * @return array
 */
function get_businesstype_enrolled_user_course($courseid) {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/user/profile/lib.php');
    $contextid = context_course::instance($courseid);
    $users = get_enrolled_users($contextid);
    $userprofiledata = [];
    foreach ($users as $user) {
        $extrafields = profile_get_user_fields_with_data($user->id);
        foreach ($extrafields as $field) {
            if($field->get_shortname() == 'BusinessType') {
                if ($field->is_transform_supported()) {
                    $userprofiledata[] = $field->display_data();
                } else {
                    $userprofiledata[] = $field->data;
                }
            }
        }
    }
    return array_filter(array_unique($userprofiledata));
}

/**
 * Summary of get_course_access_usersid
 * @param mixed $courseid
 * @param mixed $days
 * @param mixed $userid
 * @return array
 */
function get_course_access_usersid($courseid, $days, $userid = null, $location = null) {
    global $DB;
    $timestamp30DaysAgo = strtotime("-$days days");
    $filter = '';
    if ($location) {
        $filter = " uif.shortname = 'BaseLocation' AND uid.data = '$location' AND";
    }
    if ($userid) {
        $userdetail = $DB->get_record_sql('SELECT ul.id, c.fullname, ul.timeaccess, c.id as courseid FROM {user_lastaccess} ul JOIN {user} u ON u.id = ul.userid JOIN {course} c ON c.id = ul.courseid JOIN {user_info_data} uid ON uid.userid = u.id JOIN {user_info_field} uif ON uif.id = uid.fieldid WHERE'.$filter.' ul.userid = '. $userid .' AND u.deleted = 0 AND ul.timeaccess > ' . $timestamp30DaysAgo . ' and courseid = ' . $courseid . '');
        return [
            'courseid' => $userdetail->courseid,
            'courselastaccess' => $userdetail->timeaccess,
            'coursename' => $userdetail->fullname
        ];
    } else {
        $userdetail = $DB->get_records_sql('SELECT ul.id, ul.userid FROM {user_lastaccess} ul JOIN {user} u ON u.id = ul.userid JOIN {user_info_data} uid ON uid.userid = u.id JOIN {user_info_field} uif ON uif.id = uid.fieldid WHERE' . $filter . ' u.deleted = 0 AND ul.timeaccess > ' . $timestamp30DaysAgo . ' and courseid = ' . $courseid . '');
    
        $userids = [];
        foreach ($userdetail as $user) {
            $userids[] = $user->userid;
        }
        return $userids;
    }
}
