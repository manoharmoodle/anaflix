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
 * User custom_courseprogress
 * @package    block_custom_courseprogress
 */
defined('MOODLE_INTERNAL') || die();

function render_welcome_admin() {
    global $USER, $CFG;
    return ' <div class="admin_text">
    <h1>Welcome '.strtoupper($USER->firstname). ' '.$USER->lastname.'</h1>

    <img class="svg_admin" width="273" height="206"  src="'.$CFG->wwwroot . "/blocks/custom_courseprogress/".'welcome.png">

</div>';
}

function render_course_access_report () {
    global $DB ,$CFG;
    $courses = $DB->get_records_sql('Select id, fullname FROM {course} WHERE format != "site"');
    $location = $DB->get_records_sql_menu('Select distinct(uid.data) as location FROM {user_info_data} uid JOIN {user_info_field} uif ON uid.fieldid=uif.id WHERE uif.shortname = "BaseLocation"');

    $html = '
    <div class="graph">
        <h3> <b>Course Access Report</b></h3>
        <div class="graph_top">
       
        <div class="Course_Pogress">
 
            <div class="main-class-div">
            
                <div class="day-select">
                
                <label for="">Month Wise</label>
                <select class="custom-select1" id="select_month">
                            <option width="200" value="30">30 Days</option>
                            <option width="200" value="60">60 Days</option>
                            <option width="200" value="90">90 Days</option>
                </select>
            </div>
        
            </div>
        </div>
      
      
            <section class="filter-aling" >
                <label for="">Select Course</label>
            <select class="custom-select1" id="select1" onchange = filtercohorts()>
                ';
                foreach ($courses as $course) {
                    $html .= '
                        <option width="200" value="' . $course->id . '">' .$course->fullname. '</option>
                    ';
                }
                $html .= '
            </select>
            </section>
            <div class="location-select">
                <label for="">Location</label>
                <select class="custom-select1" id="location" onchange = filtercohorts()>
                            <option width="200" value="0">All</option>';
                        foreach (array_keys($location) as $loc) {
                            $html .= '
                                <option width="200" value="' . $loc . '">' . $loc . '</option>
                            ';
                        }
                $html .= '</select>
            </div>
            <div class="Donwload-section">
                <label for="">Donwload</label>
            <button class="Download-more" onclick = course_access_report_download()><svg width="18" height="18" viewBox="0 0 47 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M47 25.2857V39C47 39.7956 46.6849 40.5587 46.124 41.1213C45.5631 41.6839 44.8023 42 44.0091 42H2.99091C2.19767 42 1.43692 41.6839 0.876017 41.1213C0.315113 40.5587 0 39.7956 0 39V25.2857C0 24.4901 0.315113 23.727 0.876017 23.1644C1.43692 22.6018 2.19767 22.2857 2.99091 22.2857H11.5364C11.8763 22.2857 12.2024 22.4212 12.4427 22.6623C12.6831 22.9034 12.8182 23.2304 12.8182 23.5714C12.8182 23.9124 12.6831 24.2394 12.4427 24.4806C12.2024 24.7217 11.8763 24.8571 11.5364 24.8571H2.99091C2.87759 24.8571 2.76891 24.9023 2.68878 24.9827C2.60865 25.063 2.56364 25.1721 2.56364 25.2857V39C2.56364 39.1137 2.60865 39.2227 2.68878 39.303C2.76891 39.3834 2.87759 39.4286 2.99091 39.4286H44.0091C44.1224 39.4286 44.2311 39.3834 44.3112 39.303C44.3913 39.2227 44.4364 39.1137 44.4364 39V25.2857C44.4364 25.1721 44.3913 25.063 44.3112 24.9827C44.2311 24.9023 44.1224 24.8571 44.0091 24.8571H35.4636C35.1237 24.8571 34.7976 24.7217 34.5573 24.4806C34.3169 24.2394 34.1818 23.9124 34.1818 23.5714C34.1818 23.2304 34.3169 22.9034 34.5573 22.6623C34.7976 22.4212 35.1237 22.2857 35.4636 22.2857H44.0091C44.8023 22.2857 45.5631 22.6018 46.124 23.1644C46.6849 23.727 47 24.4901 47 25.2857ZM22.5942 24.48C22.8345 24.7208 23.1603 24.856 23.5 24.856C23.8397 24.856 24.1655 24.7208 24.4058 24.48L34.6604 14.1943C34.8868 13.9506 35.01 13.6282 35.0042 13.2951C34.9983 12.962 34.8638 12.6442 34.629 12.4086C34.3941 12.1731 34.0773 12.0381 33.7452 12.0323C33.4131 12.0264 33.0917 12.15 32.8487 12.3771L24.7818 20.4664V1.28571C24.7818 0.944722 24.6468 0.617695 24.4064 0.376577C24.166 0.135459 23.84 0 23.5 0C23.16 0 22.834 0.135459 22.5936 0.376577C22.3532 0.617695 22.2182 0.944722 22.2182 1.28571V20.4664L14.1513 12.3771C13.9083 12.15 13.5869 12.0264 13.2548 12.0323C12.9227 12.0381 12.6059 12.1731 12.371 12.4086C12.1362 12.6442 12.0017 12.962 11.9958 13.2951C11.99 13.6282 12.1132 13.9506 12.3396 14.1943L22.5942 24.48ZM38.4545 32.1429C38.4545 31.719 38.3293 31.3047 38.0945 30.9523C37.8598 30.6 37.5261 30.3253 37.1357 30.1631C36.7454 30.0009 36.3158 29.9585 35.9014 30.0412C35.487 30.1239 35.1063 30.3279 34.8075 30.6276C34.5088 30.9273 34.3053 31.3091 34.2229 31.7248C34.1404 32.1405 34.1827 32.5713 34.3444 32.9629C34.5061 33.3544 34.78 33.6891 35.1313 33.9246C35.4826 34.16 35.8956 34.2857 36.3182 34.2857C36.8848 34.2857 37.4282 34.06 37.8288 33.6581C38.2295 33.2562 38.4545 32.7112 38.4545 32.1429Z" fill="white"/>
                </svg>
                
                </button>
            </div>
       
   
        </div>
        <div class="graph_line">
        <p class="Active_user">Number of Users Access</p>
        <div class="chartjs-size-monitor"></div>
        <canvas
            id="canvas"
            style="display: block"
            class="chartjs-render-monitor"
        ></canvas>
        </div>
        <p class="Active_user1">Business Lines</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    $html .= html_writer::script('

    ');
    return $html;
}

function render_login_activity() {
    global $DB;
    $dates = last30daysstr();
    $from = $dates['from'];
    $to = $dates['to'];
    $activeuser = count(user_lastaccess($from, $to , 'active'));
    $inactiveuser = count(user_lastaccess($from, $to , 'inactive'));
    $activeuserbusinesslines = array_count_values(user_lastaccess($from, $to , 'active'));

    $inactiveuserbusinesslines = array_count_values(user_lastaccess($from, $to , 'inactive'));
    $totaluser = $activeuser + $inactiveuser;
    $html ='<div class="pie_chart_body">


    <div class="graph_top1">
        <div class="Course_Pogress">
            <span class="login_activity_span">
            <h3> <b>Login Activity</b></h3><span id="loginActivity" >(Last 30 days)</span>
        </span>
        </div>
        <div class="date-filterr">
        <div class="accordien">
        <input type="hidden" id="activeuser" value=' .$activeuser. '>
        <input type="hidden" id="inactiveuser" value=' .$inactiveuser. '>
            <section>
            <label for="dateFrom">Date From:</label>
            <input type="date" id="dateFrom" name="dateFrom" onchange = "setMinToDate()"><br><br>
              </section>

        </div>
        <div class="accordien">
            <section>
            <label for="dateTo">Date To:</label>
            <input type="date" id="dateTo" name="dateTo"><br><br>
            </section>
        </div>
        <div class="accordien">
        <section>
            <button class="filter_btn" id ="login filter" onclick = filter_loginuser()>Filter</button>
        </section>

    </div>
</div>
    </div>
    <div class="active_inactive">
        <div class="list">
            <h4 class="active-title" >Active user:</h4>
            <ul class="active-user" >';
    foreach ($activeuserbusinesslines as $activeuserbusinessline => $usercount) {
        $html .= '<li class="list-style" >'.$activeuserbusinessline.'('.$usercount.') / '.number_format((($usercount/($activeuser + $inactiveuser))*100),2).'%</li>';
    }
    $html .= '</ul>
        </div>
        <div class="list">
            <h4>Inactive user:</h4>
            <ul class="inactive-user" >';
            foreach ($inactiveuserbusinesslines as $inactiveuserbusinessline => $usercount) {
                $html .= '<li class="list-style" >'.$inactiveuserbusinessline.'('.$usercount.') / '.number_format((($usercount/($inactiveuser + $activeuser))*100), 2).'%</li>';
            }

            $html .= '</ul>
        </div>
    </div>

    <div class="flexWrapper">
        <canvas id="donut" width="300" height="300"></canvas>
    </div>

    <hr>
    <figcaption class="login_details" >
        <div class="blue_text_section">
        <p class="blue_text" > <span class="blue"></span> Active Users <span id="activeUserCount" >('.$activeuser.'/'.$totaluser.')</span> </p>
        <button class="Download-more-details" onclick = userdownload("active")>Download</button>
    </div>
    <div class="blue_text_section">
  
    <p class="blue_text" ><span class="red"></span> Inactive Users <span id="inactiveUserCount" >('.$inactiveuser.'/'. $totaluser.')</span></p>
    <button class="Download-more-details" onclick = userdownload("inactive")>Download</button>
</div>

</figcaption>
</div>
<script>
function setMinToDate() {
    // Get the selected "from" date
    const fromDate = new Date(document.getElementById("dateFrom").value);
    const toDate = new Date(document.getElementById("dateTo").value);
    if (fromDate > toDate) {
        document.getElementById("dateTo").value = "";
    }
    // Set the minimum allowed date for the "to" date input
    document.getElementById("dateTo").min = fromDate.toISOString().split("T")[0];
    document.getElementById("dateTo").disabled = false;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
';
return $html;
}
