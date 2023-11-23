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
require_once('reshort_form.php');
require_once('renderer.php');
global $OUTPUT, $PAGE, $CFG, $DB, $USER;
$PAGE->requires->jquery();
require_login();
echo $OUTPUT->header();
$mform = new reshort_form();
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {    
  $data = $fromform->introduction;

// Sample HTML content
$html = $data['text']; // Your HTML content goes here

// Create a new DOMDocument
$doc = new DOMDocument();
$doc->loadHTML($html);

// Create a new DOMXPath
$xpath = new DOMXPath($doc);

// Find the first <ul> element
$ulListall = $xpath->query('//ul');
$arraydata = [];
foreach ($ulListall as $ulList) {
    if ($ulList) {
        // Get the first <li> element within the <ul>
        $liElement = $ulList->getElementsByTagName('li')->item(0);
    
        // Check if an <li> element is found
        if ($liElement) {
            // Get the heading within the <li> element
            $heading = $liElement->getElementsByTagName('span')->item(0);
    
            // Check if a heading is found
            if ($heading) {
                // Get the text content of the heading
                $headingText = $heading->textContent;
                $arraydata[] = $headingText;
            }
        }
    }
}

// Get all headings
$arrayheading = [];
$headings = $xpath->query('//p/a/b/span');
foreach ($headings as $heading) {
    $arrayheading[] = $heading->textContent;

}
$final = array_combine($arrayheading, $arraydata);
foreach($final as $key => $item) {
    $rec = [];
    $rec['title'] = $key;
    $rec['content'] = $item;
    $rec['timestamp'] = time();
    $id = $DB->insert_record('reshorts', (object)$rec, $returnid=true, $bulk=false);
}

  //In this case you process validated data. $mform->get_data() returns data posted in form.
} else {
  //displays the form
  $mform->display();
}
$tabs = array();
if (is_siteadmin()) {
    $tabs[] = new tabobject('review', 'reshort.php', 'In Review', 'In Review', false);
    $tabs[] = new tabobject('published', 'reshort_pending.php', 'Published', 'Published', false);
}
$currenttab = 'published';
print_tabs(array($tabs), $currenttab);

echo render_pending_reshorts();
echo $OUTPUT->footer();