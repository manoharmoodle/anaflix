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
 * User custom_trendingcourses
 * @package    block_custom_trendingcourses
 */
defined('MOODLE_INTERNAL') || die();

function renderer_trending_courses($trendingcourses) {
    $html = '
    <div class="Course_Pogress">
      <h3><b> Trending Courses</b></h3>
    </div>
    <div class="wrapper">
    <i id="left" class="fa-solid fa-angle-left"></i>
    <ul class="carousels">';
    $no = 1;
    foreach ($trendingcourses as $value) {
      $html .= '
      <a class="card-a" href="' . $value['courseurl'] . '">
        <li class="card" onmouseover="showData(this)" onmouseout="hideData(this)">
       
          <div class="img"><img src="' .$value['courseimageurl']. '" alt="img" draggable="false">
            <div class="dataa">
              <div class="d"> <div class="f"> <div class=""><p>Active users : ' .$value['courseusercount']. '</p> </div> <div class=""><p>View All</p></div></div></div>
             
            </div>
          </div>
          <h2 class="course_title" title="' .$value['coursefullname']. '" >' .$value['coursefullname']. '</h2>
          <span>Trending No. ' .$no. '</span>
        </li></a>';
      $no++;
    }
    $html .= '</ul>
    <i id="right" class="fa-solid fa-angle-right"></i>
    </div>';
    return $html;
}