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
 * @package     local_admin_dashboard
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ .'/../../config.php');
$subject = optional_param('subject', '', PARAM_RAW);
$message = optional_param('message', '', PARAM_RAW);
if (!empty($subject) && !empty($message)) {
    $dummyuser = \core_user::get_user(\core_user::NOREPLY_USER);
    $dummyuser->id = -1;
    $dummyuser->email = $USER->email;
    $dummyuser->firstname = $USER->email;
    $emailuserfrom = $dummyuser;

    $emailuser = new stdClass();
    $emailuser->email = 'indiamanohar26@gmail.com';
    $emailuser->id = -99;
    ob_start();
    $success = email_to_user($emailuser, $emailuserfrom, $subject, $message);
    ob_end_clean();
    if($success) {
        echo 'sent successfully!!';
    }
}
