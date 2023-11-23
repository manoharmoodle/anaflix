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
 * Theme functions.
 *
 * @package    local_remuihomepage
 * @copyright  (c) 2019 WisdmLabs (https://wisdmlabs.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 function local_admin_dashboard_extend_navigation(global_navigation  $nav) {
    global $USER,$DB, $CFG;
    $role_id = $DB->get_field('role', 'id', ['shortname' => 'admin_dashboarduser']);
    if (user_has_role_assignment($USER->id, $role_id)){
          $nav->add('Admin Dashboard',
          new moodle_url($CFG->wwwroot . '/local/admin_dashboard'),
          navigation_node::TYPE_SYSTEM,
          null,
          'local_admin_dashboard',
          new pix_icon('i/dashboard', '')
          )->showinflatnavigation = true;
      }

      $nav->add('My Anacoins',
      new moodle_url($CFG->wwwroot . '/blocks/custom_reshorts/myanacoins.php'),
      navigation_node::TYPE_SYSTEM,
      null,
      'local_admin_dashboard',
      new pix_icon('i/dashboard', '')
      )->showinflatnavigation = true;

      $hrcourse = $DB->get_record('course', ['idnumber' => 'HR_Section']);
      $coursesections = $DB->get_records('course_sections', ['course' => $hrcourse->id]);

      $report = $nav->add(
        'HR Section',
        new moodle_url($CFG->wwwroot . '/course/view.php', ['id' => $hrcourse->id]),
        navigation_node::TYPE_SYSTEM,
        null,
        'local_admin_dashboard',
        new pix_icon('i/section', '')
      );
      $report->showinflatnavigation = true;
      $report->isexpandable = true;
      $i = 0;
      foreach($coursesections as $section) {
        if ($section->name) {
          $name = $section->name;
        } else {
          $name = 'topic'. $i;
        }
        $i++;
        $submenu = $report->add(
          $name,
          $CFG->wwwroot . '/course/view.php?id='.$hrcourse->id.'#section-' . $i,
          navigation_node::TYPE_SYSTEM,
          null,
          'local_admin_dashboard',
          new pix_icon('i/manual_item', '')
        );
        $submenu->expanded = false;
      }

    if(is_siteadmin()) {
      $nav->add(
        'Add Reshorts',
        new moodle_url($CFG->wwwroot . '/local/admin_dashboard/reshort.php'),
        navigation_node::TYPE_SYSTEM,
        null,
        'local_admin_dashboard',
        new pix_icon('i/dashboard', '')
      )->showinflatnavigation = true;
  
      $nav->add(
        'Add Textsticker',
        new moodle_url($CFG->wwwroot . '/local/admin_dashboard/textsticker_form.php'),
        navigation_node::TYPE_SYSTEM,
        null,
        'local_admin_dashboard',
        new pix_icon('i/addblock', '')
      )->showinflatnavigation = true;
    }
}

function get_course_completion_only($course, $userid = null) {
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
    if ($userid) {
      $onlystudents = $userid;
    } else {
      $onlystudents = implode(",", array_keys($enrolledusers));
    }
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
          $coursepoint = $DB->get_record_sql("SELECT id FROM {leaderboard_points} WHERE name = 'course'");
          $alldata = [];
          foreach ($completions as $user) {
              if (!is_enrolled($context, $user->userid, '', true)) {
                  continue;
              }
              if ($user->total == $count) {
                $singlerec = [];
                $singlerec['instanceid'] = $course->id;
                $singlerec['userid'] = $user->userid;
                $checkcoursecompletiontime = $DB->get_record("course_completions", ['userid' => $user->userid, 'course' => $course->id]);
                $singlerec["timecreated"] = $checkcoursecompletiontime->timecompleted;
                $singlerec['fieldid'] = $coursepoint->id;
                $alldata[] = (object) $singlerec;
              }
          }
      }
  }
  return $alldata;
}

function showpoints($points, $data) {
  global $CFG, $USER, $DB;
  $id = $DB->insert_record("l_points_distribution",$data);
  echo"
<style>
  
  :root {
  --border-color: #D7DBE3;
  font-family: -apple-system, BlinkMacSystemFont, 'Roboto', 'Open Sans', 'Helvetica Neue', sans-serif;
  --green: #0CD977;
  --red: #FF1C1C;
  --pink: #FF93DE;
  --purple: #5767ED;
  --yellow: #FFC61C;
  --rotation: 0deg;
  }

.modalle {
  width: 300px;
  margin: 0 auto;
  border: 1px solid var(--border-color);
  box-shadow: 0px 0px 5px rgba(0,0,0,0.16);
  background-color: #fff;
  border-radius: 0.25rem;
  padding: 2rem;
  z-index: 1;
}

.emoji {
  display: block;
  text-align: center;
  font-size: 5rem;
  line-height: 5rem;
  transform: scale(0.5);
  animation: scaleCup 2s infinite alternate;

}

@keyframes scaleCup {
  0% {
    transform: scale(0.6);
  } 
  
  100% {
    transform: scale(1);
  }
}

h1 {
  text-align: center;
  font-size: 1em;
  margin-top: 20px;
  margin-bottom: 20px;
}


.modal-btn {
  display: block;
  margin: 0 -2rem -2rem -2rem;
  padding: 1rem 2rem;
  font-size: .75rem;
  text-transform: uppercase;
  text-align: center;
  color: #fff;
  font-weight: 600;
  border-radius: 0 0 .25rem .25rem;
  background-color: #904E8F;
  cursor: pointer;
  text-decoration: none;
}

@keyframes confettiRain {
  0% {
    opacity: 1;
    margin-top: -100vh;
    margin-left: -200px;
  } 
  
  100% {
    opacity: 1;
    margin-top: 100vh;
    margin-left: 200px;
  }
}

.confetti {
  opacity: 0;
  position: absolute;
  width: 1rem;
  height: 1.5rem;
  transition: 500ms ease;
  animation: confettiRain 5s infinite;
}

#confetti-wrapper {
   overflow: hidden !important;
}

.wrapperr {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    position: fixed;
  
background: rgba(255, 255, 255, 0.07);
border-radius: 16px;
box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
backdrop-filter: blur(4px);
-webkit-backdrop-filter: blur(4px);
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    z-index: 999;
   
}
.modalle {
    width: 300px;
    margin: 0 auto;
    position: absolute;
    border: 1px solid var(--border-color);
    box-shadow: 0px 0px 5px rgba(0,0,0,0.16);
    background-color: #fff;
    border-radius: 0.25rem;
    padding: 2rem;
    z-index: 1;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
}
img.img-custom {
    width: 100%;
}
.points2 {
    color: #904E8F;
    font-size: 24px;
}
</style>

  <div class='wrapperr'>
  <div class='modalle'>
    <span class='emoji round'>
      <img class='img-custom' src='$CFG->wwwroot/local/admin_dashboard/coin.gif' alt=''>
    </span>
    <h1>Congratulation, ".fullname($USER)." <br> <span class='points2' >".$points." Points.</span> </h1>
    <span class='modal-btn'>OK</span>
  </div>
  <div id='confetti-wrapper'>
  </div>
</div>

<script>
  
for(i=0; i<100; i++) {
// Random rotation
var randomRotation = Math.floor(Math.random() * 360);
// Random Scale
var randomScale = Math.random() * 1;
// Random width & height between 0 and viewport
var randomWidth = Math.floor(Math.random() * Math.max(document.documentElement.clientWidth, window.innerWidth || 0));
var randomHeight =  Math.floor(Math.random() * Math.max(document.documentElement.clientHeight, window.innerHeight || 500));

// Random animation-delay
var randomAnimationDelay = Math.floor(Math.random() * 15);
console.log(randomAnimationDelay);

// Random colors
var colors = ['#0CD977', '#FF1C1C', '#FF93DE', '#5767ED', '#FFC61C', '#8497B0']
var randomColor = colors[Math.floor(Math.random() * colors.length)];

// Create confetti piece
var confetti = document.createElement('div');
confetti.className = 'confetti';
confetti.style.top=randomHeight + 'px';
confetti.style.right=randomWidth + 'px';
confetti.style.backgroundColor=randomColor;
// confetti.style.transform='scale(' + randomScale + ')';
confetti.style.obacity=randomScale;
confetti.style.transform='skew(15deg) rotate(' + randomRotation + 'deg)';
confetti.style.animationDelay=randomAnimationDelay + 's';
document.getElementById('confetti-wrapper').appendChild(confetti);
}
const wrapperr = document.querySelector('.wrapperr')
const modalbtn = document.querySelector('.modal-btn')
setTimeout(()=>{
  wrapperr.style.display = 'none'
},10000)
modalbtn.addEventListener('click',()=>{
  wrapperr.style.display = 'none'
})
</script>
  ";
  
}

function local_admin_dashboard_before_standard_top_of_body_html() {
  global $DB, $USER, $PAGE, $CFG;

  if (!is_siteadmin()) {
    
    echo "<style>
    a[data-key='home'], a[data-key='privatefiles'] {
      display: none !important;
    }
    </style>";
  }
}