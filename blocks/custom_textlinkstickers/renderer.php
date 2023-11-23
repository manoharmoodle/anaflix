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
 * User custom_textstickers
 * @package    block_custom_textlinkstickers
 */
defined('MOODLE_INTERNAL') || die();

function render_textlinkstickers() {
    global $DB, $CFG;
    $time = time();
    $textstrickers = $DB->get_records_sql('SELECT * FROM {textstickers} t WHERE '.$time.' BETWEEN t.fromdate AND t.todate');
    $text = "";
    foreach ($textstrickers as $textstricker) {
        $text .= $textstricker->text . "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp;";
    }
    $output = '


<div class="marque_tag">
  <marquee behavior="scroll" direction="left" scrollamount="10" onmouseover="this.stop();" onmouseout="this.start();">'.$text.'</marquee>
</div>



    ';
    return $output;
}