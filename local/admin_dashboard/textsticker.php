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
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ .'/../../config.php');
global $OUTPUT, $PAGE, $CFG, $DB, $USER;
require_login();

$textsticker = optional_param('textsticker', 0, PARAM_RAW);
$textstickerlink = optional_param('textstickerlink', 0, PARAM_RAW);
$from = optional_param('from', 0, PARAM_RAW);
$to = optional_param('to', '', PARAM_RAW);
$image = optional_param('image', '', PARAM_RAW);
$delete = optional_param('delete', 0, PARAM_RAW);
$update = optional_param('edit', 0, PARAM_RAW);
$agree = optional_param('agree', 0, PARAM_RAW);

$PAGE->set_pagelayout('standard');
$PAGE->set_heading('TextSticker List');
$PAGE->set_title('Textsticker');
$PAGE->set_url($CFG->wwwroot . '/local/admin_dashboard/textsticker.php');
// Set page context.
$context = context_system::instance();
$PAGE->set_context($context);

$success = false;
if ($textsticker && basename($_FILES["image"]["name"])) {
    $error = [];

    $targetDirectory = $CFG->dirroot . "/local/admin_dashboard/textsticker_files/"; // The folder where you want to save the uploaded file
    $targetFile = $targetDirectory . basename($_FILES["image"]["name"]);

    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check file size (optional)
    if ($_FILES["image"]["size"] > 500000) {
        $error[] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow only certain file formats (e.g., jpg, png)
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $error[] = "Sorry, only JPG, JPEG, and PNG files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        $uploadOk = 0;
    } else {

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $success = "The file " . basename($_FILES["image"]["name"]) . " has been uploaded.";
        } else {
            $error[] = "Sorry, there was an error uploading your file.";
        }
    }
    if (!empty($error)) {
        redirect('textsticker_form.php', implode('<br> ', $error) . basename($_FILES["image"]["name"]), null, \core\output\notification::NOTIFY_ERROR);
    }
}

if ($success || ($from && $to)) {
    $time = time();
    $from = strtotime($from);
    $to = strtotime($to);
    if ($textstickerlink) {
        $textsticker = '<a href = "'.$textstickerlink.'"> '.$textsticker .' </a>';
    }
    $textstickersrecord = ['imagepath' => basename($_FILES["image"]["name"]), 'text' => $textsticker, 'fromdate' => $from, 'todate' => $to, 'usermodified' => intval($USER->id), 'timecreated' => $time, 'timemodified' => $time];
    if ($update) {
        $textstickersrecord['id'] = $update;
        $DB->update_record('textstickers', $textstickersrecord);
        redirect('textsticker_form.php', 'Successfully Updated ' . basename($_FILES["image"]["name"]), null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        $DB->insert_record('textstickers', $textstickersrecord);
        redirect('textsticker_form.php', 'Successfully Inserted ' . basename($_FILES["image"]["name"]), null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

if ($delete) {
    if ($delete && $agree) {
        $deleteid = $DB->delete_records('textstickers', ['id' => $delete]);
        redirect('alltextsticker.php', 'Delete Successfully', null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        $deletecoursetype = 'Are you sure you want to delete';
        $formcontinue = new single_button(new moodle_url($CFG->wwwroot.'/local/admin_dashboard/textsticker.php',
                                            array('agree' => 1, 'delete' => $delete)), get_string('yes'));
        $formcancel = new single_button(new moodle_url($CFG->wwwroot.'/local/admin_dashboard/alltextsticker.php',
                                       array('agree' => 0, 'delete' => $delete)), get_string('no'));
        echo $OUTPUT->header();
        echo $OUTPUT->confirm($deletecoursetype, $formcontinue, $formcancel);
        echo $OUTPUT->footer();
    }

}
