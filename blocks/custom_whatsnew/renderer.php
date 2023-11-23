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
 * User custom_whatsnew
 * @package    block_custom_whatsnew
 */
defined('MOODLE_INTERNAL') || die();


function render_custom_whatsnew($trendingcourses) {
    global $DB, $CFG;
    $lastThirdMonthTimestamp = strtotime("-3 months");
    $whatsnews = $DB->get_records_sql("SELECT mlsl.*
    FROM {logstore_standard_log} mlsl
    JOIN (
        SELECT courseid, MAX(timecreated) AS max_timecreated
        FROM {logstore_standard_log}
        WHERE action IN ('created', 'added')
          AND target IN ('course', 'course_module')
          AND timecreated > $lastThirdMonthTimestamp
        GROUP BY courseid
    ) max_timestamps
    ON mlsl.courseid = max_timestamps.courseid AND mlsl.timecreated = max_timestamps.max_timecreated
    JOIN mdl_course mc ON mc.id = mlsl.courseid
    WHERE mlsl.action IN ('created', 'added')
          AND mlsl.target IN ('course', 'course_module')
          AND mlsl.timecreated > $lastThirdMonthTimestamp");

    $html = '
    <style>
.popup-hover {
    position: absolute;
    background: rgba(255, 255, 255, 0.4);
    border-radius: 16px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    left: 0;
    right: 0;
    display: flex;
    top: 0;
    height: 0%;
    overflow: hidden;
 
    transition: 0.3s;
    align-items: center;
    justify-content: center;
}
.updated-custom-wrapper i, .new-custom-wrapper i, .custom-wrapper i {
    top: 50%;
    height: 50px;
    z-index: 99;
    width: 50px;
    display: flex;
    cursor: pointer;
    font-size: 1.25rem;
    align-items: center;
    position: absolute;
    text-align: center;
    line-height: 50px;
    background: #fff;
    border-radius: 50%;
    justify-content: center;
    box-shadow: 0 3px 6px rgba(0,0,0,0.23);
    transform: translateY(-50%);
    transition: transform 0.1s linear;
}
.pop-upBox {
    background: white;
    width: 72%;
    height: 43%;
    border-radius: 15px;
    padding: 10px;
    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
}
.updated-custom-card{
    position: relative;
    overflow: hidden;
}
.updated-custom-carousel :where(.updated-custom-card, .updated-custom-img) {
    display: flex;
    width: 100%;
    justify-content: center;
    overflow: hidden;
    align-items: center;
    height: 74%;
    border-radius: 42px;
}
.updated-custom-card .updated-custom-img img {
    width: 100%;
    object-fit: cover;
    border: 4px solid #fff;

    height: 238px;
    object-fit: contain;
    border: 4px solid #fff;
}
h2.h2 {
    font-size: 20px;
    margin-top: 14px;
}
    </style>
    <div class="Course_Pogress">
    <h3><b> What’s  New</b></h3>
  </div>
  
  <div class="updated-custom-wrapper">
    <i id="updated-custom-left" class="">
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
    <ul class="updated-custom-carousel">';
    foreach($whatsnews as $news) {
        $fullcourse = $DB->get_record('course', ['id' => $news->courseid]);
        $courseurl = \core_course\external\course_summary_exporter::get_course_image($fullcourse);
        $coursefullname = $fullcourse->fullname;
        if ($news->target == 'course') {
            $coursedetail = json_decode($news->other);
            $text = 'New Course is added Which name is ' . $coursedetail->fullname;

        } elseif ($news->target == 'course_module') {
            $coursemodule = json_decode($news->other);
            $text = 'New '.$coursemodule->modulename.' is added Which name is ' . $coursemodule->name;
        }
        $courseviewurl = new moodle_url("/course/view.php", ['id' => $news->courseid]);
        $html .= '<li onmouseover="showpopup(this)" onmouseleave="removepopup(this)" class="updated-custom-card">
            <div class="updated-custom-img"><img src="' . $courseurl . '" alt="No img" draggable="false"></div>
            <h2 class="h2" >'.$coursefullname.'</h2>
            <a href="'.$courseviewurl.'"><div class="popup-hover"><div class="pop-upBox">'.$text.'</div>
            </div></a>
        </li>';
    }

    $html .= '</ul>
    <i id="updated-custom-right" class="">
      <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M17.0428 11.8767C17.328 12.1535 17.4917 12.5322 17.498 12.9297C17.5042 13.3271 17.3524 13.7108 17.0759 13.9964L11.5091 19.7422C11.2321 20.0278 10.853 20.1917 10.4552 20.1978C10.2582 20.2008 10.0626 20.1651 9.87944 20.0925C9.6963 20.0199 9.52924 19.912 9.38781 19.7748C9.24639 19.6377 9.13335 19.474 9.05517 19.2932C8.97699 19.1124 8.93519 18.9179 8.93215 18.721C8.92912 18.524 8.96491 18.3283 9.03749 18.1452C9.11007 17.9621 9.21801 17.795 9.35515 17.6536L13.8787 12.9863L9.21143 8.46269C9.06596 8.32665 8.94904 8.16299 8.86749 7.98128C8.78594 7.79956 8.74139 7.60343 8.73646 7.40431C8.73152 7.2052 8.76628 7.00709 8.83873 6.82156C8.91117 6.63602 9.01984 6.46678 9.15839 6.32369C9.29694 6.1806 9.4626 6.06654 9.64571 5.98816C9.82881 5.90977 10.0257 5.86865 10.2249 5.86717C10.424 5.86569 10.6215 5.90389 10.8058 5.97955C10.99 6.0552 11.1573 6.16679 11.298 6.30781L17.0448 11.8766L17.0428 11.8767Z" fill="#F8F8F8"/>
        </svg>
    </i>
</div>
<script>
function showpopup(element){
const data = element.querySelector(".popup-hover")

data.style.height = "100%"
}
function removepopup(element){
const data = element.querySelector(".popup-hover")
data.style.height = "0%"
}

const anaflixWrapper = document.querySelector(".updated-custom-wrapper");
const anaflixCarousel = document.querySelector(".updated-custom-carousel");
const anaflixFirstCardWidth = anaflixCarousel.querySelector(".updated-custom-card").offsetWidth;
const anaflixArrowButtons = document.querySelectorAll(".updated-custom-wrapper i");
const anaflixCarouselChildren = [...anaflixCarousel.children];

let anaflixIsDragging = false, anaflixIsAutoPlay = true, anaflixStartX, anaflixStartScrollLeft, anaflixTimeoutId;

// Get the number of cards that can fit in the carousel at once
let anaflixCardsPerView = Math.round(anaflixCarousel.offsetWidth / anaflixFirstCardWidth);

// Insert copies of the last few cards to the beginning of the carousel for infinite scrolling
anaflixCarouselChildren.slice(-anaflixCardsPerView).reverse().forEach(card => {
    anaflixCarousel.insertAdjacentHTML("afterbegin", card.outerHTML);
});

// Insert copies of the first few cards to the end of the carousel for infinite scrolling
anaflixCarouselChildren.slice(0, anaflixCardsPerView).forEach(card => {
    anaflixCarousel.insertAdjacentHTML("beforeend", card.outerHTML);
});

// Scroll the carousel at the appropriate position to hide the first few duplicate cards on Firefox
anaflixCarousel.classList.add("no-transition");
anaflixCarousel.scrollLeft = anaflixCarousel.offsetWidth;
anaflixCarousel.classList.remove("no-transition");

// Add event listeners for the arrow buttons to scroll the carousel left and right
anaflixArrowButtons.forEach(btn => {
    btn.addEventListener("click", () => {
        anaflixCarousel.scrollLeft += btn.id == "updated-custom-left" ? -anaflixFirstCardWidth : anaflixFirstCardWidth;
    });
});

const anaflixStartDragging = (e) => {
    anaflixIsDragging = true;
    anaflixCarousel.classList.add("dragging");
    // Records the initial cursor and scroll position of the carousel
    anaflixStartX = e.pageX;
    anaflixStartScrollLeft = anaflixCarousel.scrollLeft;
};

const anaflixDragMove = (e) => {
    if (!anaflixIsDragging) return; // if isDragging is false return from here
    // Updates the scroll position of the carousel based on the cursor movement
    anaflixCarousel.scrollLeft = anaflixStartScrollLeft - (e.pageX - anaflixStartX);
};

const anaflixStopDragging = () => {
    anaflixIsDragging = false;
    anaflixCarousel.classList.remove("dragging");
};

const anaflixHandleInfiniteScroll = () => {
    // If the carousel is at the beginning, scroll to the end
    if (anaflixCarousel.scrollLeft === 0) {
        anaflixCarousel.classList.add("no-transition");
        anaflixCarousel.scrollLeft = anaflixCarousel.scrollWidth - (2 * anaflixCarousel.offsetWidth);
        anaflixCarousel.classList.remove("no-transition");
    }
    // If the carousel is at the end, scroll to the beginning
    else if (Math.ceil(anaflixCarousel.scrollLeft) === anaflixCarousel.scrollWidth - anaflixCarousel.offsetWidth) {
        anaflixCarousel.classList.add("no-transition");
        anaflixCarousel.scrollLeft = anaflixCarousel.offsetWidth;
        anaflixCarousel.classList.remove("no-transition");
    }

    // Clear the existing timeout & start autoplay if the mouse is not hovering over the carousel
    clearTimeout(anaflixTimeoutId);
    if (!anaflixWrapper.matches(":hover")) initiateAutoPlay();
};

const initiateAutoPlay = () => {
    if (window.innerWidth < 800 || !anaflixIsAutoPlay) return; // Return if the window is smaller than 800 or isAutoPlay is false
    // Autoplay the carousel after every 2500 ms
    anaflixTimeoutId = setTimeout(() => anaflixCarousel.scrollLeft += anaflixFirstCardWidth, 2500);
};
initiateAutoPlay();

anaflixCarousel.addEventListener("mousedown", anaflixStartDragging);
anaflixCarousel.addEventListener("mousemove", anaflixDragMove);
document.addEventListener("mouseup", anaflixStopDragging);
anaflixCarousel.addEventListener("scroll", anaflixHandleInfiniteScroll);
anaflixWrapper.addEventListener("mouseenter", () => clearTimeout(anaflixTimeoutId));
anaflixWrapper.addEventListener("mouseleave", initiateAutoPlay);

</script>


    ';
    return $html;
}
