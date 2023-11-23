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
require_once('deleteuser_form.php');
global $OUTPUT, $PAGE, $CFG, $DB;
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_login();
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Admin Dashboard');
$PAGE->set_title('Delete User');
$PAGE->set_url($CFG->wwwroot . '/local/deleteusers/');
// Set page context.
$context = context_system::instance();
$PAGE->set_context($context);
echo $OUTPUT->header();
$mform = new deleteuser_form();

if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {
  $iid = csv_import_reader::get_new_iid('uploaduser');
  $cir = new csv_import_reader($iid, 'uploaduser');
  $content = $mform->get_file_content('deleteuser');

  $readcount = $cir->load_csv_content($content, '', ',');
  $cir->init();
  while ($line = $cir->next()) {
    if (!empty($line[20]) && isset($line[20])) {
      $user = $DB->get_record('user', ['id' => $line[0]]);
      echo fullname($user) . ' Successfully deleted';
      echo '</br>';
      echo '</br>';
      user_delete_user($user);
    }
  }
  //In this case you process validated data. $mform->get_data() returns data posted in form.
} else {
  //displays the form
  $mform->display();
}
$pagee = $DB->get_record('page', ['id' => 124]);

$plaintext = format_text($pagee->content, FORMAT_PLAIN, array('context' => context_system::instance()));

$test = explode("<p>", $pagee->content);

foreach($test as $cont){
  echo strip_tags($cont) . "";
}

die;
echo $OUTPUT->footer();
