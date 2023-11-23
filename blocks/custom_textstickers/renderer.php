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
 * @package    block_custom_textstickers
 */
defined('MOODLE_INTERNAL') || die();

function render_textstickers() {
    global $DB, $CFG;
    $output = '
    <style>
 
    .carousel {
    position: relative;
    width: 100%;
    overflow: hidden;
}

.carousel-container {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.carousel-post {
    flex: 0 0 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: white !important;
    font-size: 2rem;
}



#prevBtn,
#nextBtn {
    font-size: 1.5rem;
    background: none;
    border: none;
    cursor: pointer;
}

.carousel-dots {
    display: flex;
    justify-content: center;
    align-items: center;
}

.dot {
    width: 10px;
    height: 10px;
    background-color: gray;
    border-radius: 50%;
    margin: 0 5px;
    cursor: pointer;
}

.active-dot {
    background-color: black;
}
img.imgs {
    width: 100%;
}
#prevBtn {
    font-size: 1.5rem;
    background: none;
    border: none;
    position: absolute;
    background: white;
    cursor: pointer;
    width: 23px;
    top: 50%;
    border-radius: 10px;
    left: 2px;
    transform: translateY(-50%);
    margin-top: -0.75rem;
    /* height: 33px; */
}
#nextBtn {
    font-size: 1.5rem;
    background: none;
    border: none;
    position: absolute;
    background: white;
    cursor: pointer;
    width: 23px;
    top: 50%;
    border-radius: 10px;
    right:2px;
    transform: translateY(-50%);
    margin-top: -0.75rem;
    /* height: 33px; */
}
img.imgs {
    height: 216px;
    width: 100%;
    object-fit: contain;
}

</style>
    </style>
    <div class="carousel">
        <div class="carousel-container">';
        $time = time();
        $textstrickers = $DB->get_records_sql('SELECT * FROM {textstickers} t WHERE '.$time.' BETWEEN t.fromdate AND t.todate');

        foreach ($textstrickers as $textstricker) {
          if ($textstricker->imagepath) {            
            $path = $CFG->wwwroot . '/local/admin_dashboard/textsticker_files/' . $textstricker->imagepath;
            $output .= '  <div class="carousel-post"><img class="imgs" src="' . $path . '" alt=""></div>';
          }
        }
        $output .= ' </div>
        <div class="carousel-controls">
            <button id="prevBtn">&lt;</button>
            <div class="carousel-dots"></div>
            <button id="nextBtn">&gt;</button>
        </div>
    </div>
    <script>
        const carouselContainer = document.querySelector(".carousel-container");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");
        const dotsContainer = document.querySelector(".carousel-dots");
        const posts = document.querySelectorAll(".carousel-post");
      
        let currentIndex = 0;
      
        function updateCarousel() {
          carouselContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
      
          const activeDot = dotsContainer.querySelector(".active-dot");
          if (activeDot) {
            activeDot.classList.remove("active-dot");
          }
          dotsContainer.children[currentIndex].classList.add("active-dot");
        }
      
        function moveToNext() {
          currentIndex = (currentIndex + 1) % posts.length;
          updateCarousel();
        }
      
        function moveToPrev() {
          currentIndex = (currentIndex - 1 + posts.length) % posts.length;
          updateCarousel();
        }
      
        function createDots() {
          for (let i = 0; i < posts.length; i++) {
            const dot = document.createElement("div");
            dot.classList.add("dot");
            if (i === currentIndex) {
              dot.classList.add("active-dot");
            }
            dot.addEventListener("click", () => {
              currentIndex = i;
              updateCarousel();
            });
            dotsContainer.appendChild(dot);
          }
        }
      
        nextBtn.addEventListener("click", moveToNext);
        prevBtn.addEventListener("click", moveToPrev);
        createDots();
      
        function autoMove() {
          moveToNext();
        }
      
        let autoMoveInterval = setInterval(autoMove, 3000);
      
        carouselContainer.addEventListener("mouseenter", () => {
          clearInterval(autoMoveInterval);
        });
      
        carouselContainer.addEventListener("mouseleave", () => {
          autoMoveInterval = setInterval(autoMove, 3000);
        });
      </script>
      
    ';
    return $output;
}