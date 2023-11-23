<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'moodle_mis';
$CFG->dbuser    = 'yatharthd';
$CFG->dbpass    = 'Ya@8980@6849';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => 3306,
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_general_ci',
);
$CFG->cachejs = false;
// @ini_set('display_errors', '1'); // NOT FOR PRODUCTION SERVERS!
// $CFG->debug = 32767;         // DEBUG_DEVELOPER // NOT FOR PRODUCTION SERVERS!
// // for Moodle 2.0 - 2.2, use:  $CFG->debug = 38911;  
// $CFG->debugdisplay = true;
$CFG->wwwroot   = 'https://yislms.com/new_anaflix/moodle';
// $CFG->dataroot  = '/home/yislms/moodledata_anaflix';
$CFG->dataroot  = '/home/yislms/anaflixdata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
