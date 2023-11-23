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
 * User custom_pastview
 * @package    block_custom_pastview
 */
defined('MOODLE_INTERNAL') || die();

function render_custom_pastview() {
    global $DB, $CFG, $USER;
    $html = '';
    $oneMonthAgoTimestamp = strtotime("-1 month");
    $pastviewcourse = $DB->get_records_sql("SELECT * FROM {logstore_standard_log} lsl JOIN {course} c ON c.id = lsl.courseid WHERE lsl.action IN ('viewed','view') AND (lsl.target IN ('course')) AND lsl.timecreated > $oneMonthAgoTimestamp AND lsl.userid = $USER->id group by lsl.userid, lsl.contextinstanceid");
    if ($pastviewcourse) {
        $html = '<div class="Course_Pogress">
        <h3><b> Courses based on Past views</b></h3>
      </div>
    
      <div class="custom-wrapper">
        <i id="custom-left" class="">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_608_1219)">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.94015 13.06C7.65924 12.7788 7.50146 12.3975 7.50146 12C7.50146 11.6025 7.65924 11.2213 7.94015 10.94L13.5961 5.28202C13.8775 5.00076 14.2591 4.8428 14.657 4.8429C14.854 4.84294 15.0491 4.88179 15.231 4.95722C15.413 5.03265 15.5784 5.14319 15.7176 5.28252C15.8569 5.42185 15.9674 5.58725 16.0427 5.76928C16.1181 5.9513 16.1568 6.14638 16.1568 6.34338C16.1567 6.54038 16.1179 6.73544 16.0424 6.91742C15.967 7.09941 15.8565 7.26476 15.7171 7.40402L11.1211 12L15.7171 16.596C15.8605 16.7343 15.9748 16.8998 16.0535 17.0828C16.1322 17.2657 16.1737 17.4625 16.1755 17.6617C16.1773 17.8609 16.1395 18.0584 16.0641 18.2428C15.9888 18.4272 15.8775 18.5947 15.7367 18.7356C15.596 18.8765 15.4285 18.988 15.2442 19.0635C15.0599 19.139 14.8624 19.177 14.6633 19.1754C14.4641 19.1738 14.2672 19.1325 14.0842 19.0539C13.9012 18.9754 13.7356 18.8612 13.5971 18.718L7.93815 13.06H7.94015Z" fill="#F8F8F8"/>
                </g>
                <defs>
                <clipPath id="clip0_608_1219">
                <rect width="24" height="24" fill="white"/>
                </clipPath>
                </defs>
                </svg>
                
        </i>
        <ul class="custom-carousel">';
        foreach($pastviewcourse as $course) {
          $coursedata = $DB->get_record('course', ['id' => $course->courseid]);
          $imageurl = \core_course\external\course_summary_exporter::get_course_image($coursedata);
          $courseurl = $CFG->wwwroot . '/course/view.php?id='.$course->courseid;
          $html .= '<li class="custom-card">
          <a href="'.$courseurl.'">
            <div class="custom-img"><img src="'.$imageurl.'" alt="no" draggable="false"></div>
        </a>
        <h2>'.$coursedata->fullname.'</h2>
       
          </li>';
        }
        $html .= '</ul>
        <i id="custom-right" class="">
            <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M17.0428 11.8767C17.328 12.1535 17.4917 12.5322 17.498 12.9297C17.5042 13.3271 17.3524 13.7108 17.0759 13.9964L11.5091 19.7422C11.2321 20.0278 10.853 20.1917 10.4552 20.1978C10.2582 20.2008 10.0626 20.1651 9.87944 20.0925C9.6963 20.0199 9.52924 19.912 9.38781 19.7748C9.24639 19.6377 9.13335 19.474 9.05517 19.2932C8.97699 19.1124 8.93519 18.9179 8.93215 18.721C8.92912 18.524 8.96491 18.3283 9.03749 18.1452C9.11007 17.9621 9.21801 17.795 9.35515 17.6536L13.8787 12.9863L9.21143 8.46269C9.06596 8.32665 8.94904 8.16299 8.86749 7.98128C8.78594 7.79956 8.74139 7.60343 8.73646 7.40431C8.73152 7.2052 8.76628 7.00709 8.83873 6.82156C8.91117 6.63602 9.01984 6.46678 9.15839 6.32369C9.29694 6.1806 9.4626 6.06654 9.64571 5.98816C9.82881 5.90977 10.0257 5.86865 10.2249 5.86717C10.424 5.86569 10.6215 5.90389 10.8058 5.97955C10.99 6.0552 11.1573 6.16679 11.298 6.30781L17.0448 11.8766L17.0428 11.8767Z" fill="#F8F8F8"/>
                </svg>
                
        </i>
        <script>
            const customWrapper = document.querySelector(".custom-wrapper");
    const customCarousel = document.querySelector(".custom-carousel");
    const firstCustomCardWidth = customCarousel.querySelector(".custom-card").offsetWidth;
    const customArrowBtns = document.querySelectorAll(".custom-wrapper i");
    const customCarouselChildrens = [...customCarousel.children];
    
    let isDragging = false, isAutoPlay = true, startX, startScrollLeft, timeoutId;
    
    // Get the number of cards that can fit in the carousel at once
    let cardPerView = Math.round(customCarousel.offsetWidth / firstCustomCardWidth);
    
    // Insert copies of the last few cards to the beginning of the carousel for infinite scrolling
    customCarouselChildrens.slice(-cardPerView).reverse().forEach(card => {
        customCarousel.insertAdjacentHTML("afterbegin", card.outerHTML);
    });
    
    // Insert copies of the first few cards to the end of the carousel for infinite scrolling
    customCarouselChildrens.slice(0, cardPerView).forEach(card => {
        customCarousel.insertAdjacentHTML("beforeend", card.outerHTML);
    });
    
    // Scroll the carousel at the appropriate position to hide the first few duplicate cards on Firefox
    customCarousel.classList.add("no-transition");
    customCarousel.scrollLeft = customCarousel.offsetWidth;
    customCarousel.classList.remove("no-transition");
    
    // Add event listeners for the arrow buttons to scroll the carousel left and right
    customArrowBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            customCarousel.scrollLeft += btn.id == "custom-left" ? -firstCustomCardWidth : firstCustomCardWidth;
        });
    });
    
    const dragStart = (e) => {
        isDragging = true;
        customCarousel.classList.add("dragging");
        // Records the initial cursor and scroll position of the carousel
        startX = e.pageX;
        startScrollLeft = customCarousel.scrollLeft;
    }
    
    const dragging = (e) => {
        if (!isDragging) return; // if isDragging is false return from here
        // Updates the scroll position of the carousel based on the cursor movement
        customCarousel.scrollLeft = startScrollLeft - (e.pageX - startX);
    }
    
    const dragStop = () => {
        isDragging = false;
        customCarousel.classList.remove("dragging");
    }
    
    const infiniteScroll = () => {
        // If the carousel is at the beginning, scroll to the end
        if (customCarousel.scrollLeft === 0) {
            customCarousel.classList.add("no-transition");
            customCarousel.scrollLeft = customCarousel.scrollWidth - (2 * customCarousel.offsetWidth);
            customCarousel.classList.remove("no-transition");
        }
        // If the carousel is at the end, scroll to the beginning
        else if (Math.ceil(customCarousel.scrollLeft) === customCarousel.scrollWidth - customCarousel.offsetWidth) {
            customCarousel.classList.add("no-transition");
            customCarousel.scrollLeft = customCarousel.offsetWidth;
            customCarousel.classList.remove("no-transition");
        }
    
        // Clear the existing timeout & start autoplay if the mouse is not hovering over the carousel
        clearTimeout(timeoutId);
        if (!customWrapper.matches(":hover")) autoPlay();
    }
    
    const autoPlay = () => {
        if (window.innerWidth < 800 || !isAutoPlay) return; // Return if the window is smaller than 800 or isAutoPlay is false
        // Autoplay the carousel after every 2500 ms
        timeoutId = setTimeout(() => customCarousel.scrollLeft += firstCustomCardWidth, 2500);
    }
    autoPlay();
    
    customCarousel.addEventListener("mousedown", dragStart);
    customCarousel.addEventListener("mousemove", dragging);
    document.addEventListener("mouseup", dragStop);
    customCarousel.addEventListener("scroll", infiniteScroll);
    customWrapper.addEventListener("mouseenter", () => clearTimeout(timeoutId));
    customWrapper.addEventListener("mouseleave", autoPlay);
    
        </script>
      </div>
        ';
    }
    
    return $html;
}