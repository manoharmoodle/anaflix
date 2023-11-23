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
 * Local stuff for show rehorts points dashboard.
 *
 * @package    local_admin_dashboard
 */

namespace local_admin_dashboard;
defined('MOODLE_INTERNAL') || die();

class observers {
    public static function dashboard(\core\event\dashboard_viewed $event) {
        global $CFG;
        if (empty($event)) {
            return;
        } else {
            $url = $CFG->wwwroot . '/local/admin_dashboard/';
            $studenturl = $CFG->wwwroot . '/local/admin_dashboard/student_dashboard.php';
            if (is_siteadmin()) {
                echo '<script>
                window.location.replace("'.$url.'");
                </script>';
            } else {
                echo '<script>
                window.location.replace("'.$studenturl.'");
                </script>';
            }
        }
    }

    /**
     * Summary of courseviewevent
     * @param \core\event\course_viewed $courseevent
     * @return void
     */
    public static function courseviewevent(\core\event\course_viewed $courseevent) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/local/admin_dashboard/lib.php");
        if (empty($courseevent)) {
            return;
        } else {
            $courseid = $courseevent->get_data()['courseid'];
            $userid = $courseevent->get_data()['userid'];
            $sql = 
            <<<SQL
            SELECT *
            FROM {l_points_distribution} lpd
            JOIN {leaderboard_points} lp ON lp.id = lpd.fieldid
            WHERE lpd.instanceid = :instance
            AND lpd.userid = :userid
            AND lp.name = :name
            SQL;
            $params = ['instance' => $courseid,'userid' => $userid, 'name' => 'course'];
            if (!$DB->record_exists_sql($sql, $params)) {
                $points = $DB->get_record_sql("SELECT lp.id, lp.points FROM {leaderboard_points} lp WHERE lp.name = 'course'");
                $coursedata = [];
                $coursedata['fieldid'] = $points->id;
                $coursedata['instanceid'] = $courseid;
                $coursedata['userid'] = $userid;
                $check = get_course_completion_only($courseid, $userid);
                if ($check) {
                    showpoints($points->points, (object)$coursedata);
                }
            }

        }
    }

    /**
     * Summary of policyaccept
     * @param \tool_policy\event\acceptance_created $policyaccept
     * @return void
     */
    public static function policyaccept(\tool_policy\event\acceptance_created $policyaccept) {
        global $CFG, $DB;

        require_once($CFG->dirroot . "/local/admin_dashboard/lib.php");
        if (empty($policyaccept)) {
            return;
        } else {
            $policyid = $policyaccept->get_data()["objectid"];
            $userid = $policyaccept->get_data()["userid"];
            $sql =
            <<<SQL
            SELECT *
            FROM {l_points_distribution} lpd
            JOIN {leaderboard_points} lp ON lp.id = lpd.fieldid
            WHERE lpd.instanceid = :instance
            AND lpd.userid = :userid
            AND lp.name = :name
            SQL;
            $params = ['instance' => $policyid,'userid' => $userid, 'name' => 'policy'];

            if (!$DB->record_exists_sql($sql, $params)) {
                $points = $DB->get_record_sql("SELECT lp.id, lp.points FROM {leaderboard_points} lp WHERE lp.name = 'policy'");
                $policydata = [];
                $policydata['fieldid'] = $points->id;
                $policydata['instanceid'] = $policyid;
                $policydata['timecreated'] = time();
                $policydata['userid'] = $userid;
                showpoints($points->points, $policydata);
            }
        }
    }

    /**
     * Summary of reshortview
     * @param \local_admin_dashboard\event\reshort_view $reshortview
     * @return void
     */
    public static function reshortview(\local_admin_dashboard\event\reshort_view $reshortview){
        global $CFG, $DB;

        require_once($CFG->dirroot . "/local/admin_dashboard/lib.php");
        if (empty($reshortview)) {
            return;
        } else {
            $reshortid = $reshortview->get_data()["objectid"];
            $userid = $reshortview->get_data()["userid"];
            $sql =
            <<<SQL
            SELECT *
            FROM {l_points_distribution} lpd
            JOIN {leaderboard_points} lp ON lp.id = lpd.fieldid
            WHERE lpd.instanceid = :instance
            AND lpd.userid = :userid
            AND lp.name = :name
            SQL;
            $params = ['instance' => $reshortid,'userid' => $userid, 'name' => 'ReShorts'];

            if (!$DB->record_exists_sql($sql, $params)) {
                $points = $DB->get_record_sql("SELECT lp.id, lp.points FROM {leaderboard_points} lp WHERE lp.name = 'ReShorts'");
                $reshortdata = [];
                $reshortdata['fieldid'] = $points->id;
                $reshortdata['instanceid'] = $reshortid;
                $reshortdata['timecreated'] = time();
                $reshortdata['userid'] = $userid;
                showpoints($points->points, $reshortdata);
            }
        }
    }
}
