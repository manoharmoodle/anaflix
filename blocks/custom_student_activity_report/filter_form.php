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
 * block custom_student_activity_report.
 *
 * @package    block_custom_student_activity_report
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class custom_student_activity_report_filter_form extends moodleform {
    /**
     * Form definition
     */
    public function definition () {
        global $DB;
        $mform = $this->_form;
        $data = $this->_customdata['data'];

        $mform->addElement('header', 'custom_student_activity_report', get_string('filter', 'block_custom_student_activity_report'), '');

        $mform->addElement('text', 'keyword', get_string('search', 'block_custom_student_activity_report'));
        $mform->setDefault('keyword', $data->keyword);

        $options = array('multiple' => false, 'noselectionstring' => get_string('select'));
        $trainingdateoptions = $DB->get_records_sql_menu("SELECT uid.data as id, uid.data FROM {user_info_data} uid JOIN {user_info_field} uif ON uif.id = uid.fieldid WHERE uif.shortname = 'BusinessType' GROUP BY uid.data");
        $businesstype = get_string('businesstype', 'block_custom_student_activity_report');

        $mform->addElement('autocomplete', 'businesstype', $businesstype, array_filter($trainingdateoptions), $options);
        $mform->setDefault('businesstype', $data->businesstype);
        $mform->addElement('html', '<style>
            #id_submitbutton {
                background: #904e8e !important;
                color: white;
            }
        </style>');

        $mform->addElement('date_selector', 'startdate', get_string('fromdate', 'block_custom_student_activity_report'), '');
        if ($data->startdate) {
            $mform->setDefault('startdate', $data->startdate);
        } else {
            $mform->setDefault('startdate', date('U', strtotime("-3 month")));
        }
        $mform->addElement('advcheckbox', 'startdate_enabled', get_string('enable'), '', ['class' => 'enable-date']);
        $mform->disabledIf('startdate', 'startdate_enabled', 'unchecked');

        $mform->addElement('date_selector', 'enddate', get_string('todate', 'block_custom_student_activity_report'), '' );
        $mform->addElement('advcheckbox', 'enddate_enabled', get_string('enable'), '', ['class' => 'enable-date']);
        $mform->disabledIf('enddate', 'enddate_enabled', 'unchecked');
        if ($data->enddate) {
            $mform->setDefault('enddate', $data->enddate);
        }

        $this->add_action_buttons(true, get_string('filter', 'block_custom_student_activity_report'));
    }
}
