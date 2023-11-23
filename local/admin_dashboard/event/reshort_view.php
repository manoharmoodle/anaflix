<?php
namespace local_admin_dashboard\event;

defined('MOODLE_INTERNAL') || die();

class reshort_view extends \core\event\base {
    protected function init() {
        $this->data['objecttable'] = 'reshorts'; // Replace with your custom table name.
        $this->data['crud'] = 'c'; // 'c' for create, 'u' for update, 'd' for delete.
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return 'local_admin_dashboard/reshortview';
    }

    public function get_description() {
        return 'This event is triggered when a custom action occurs.';
    }

    public function get_url() {
        return new \moodle_url('/path/to/custom/page.php', array('id' => $this->objectid));
    }

    public function get_legacy_logdata() {
        return array($this->courseid, 'local_admin_dashboard', 'view', $this->get_url());
    }

    protected function get_legacy_event_mapping() {
        return array('other' => 'reshortview');
    }

    public static function get_legacy_eventname() {
        return 'reshortview';
    }
}
