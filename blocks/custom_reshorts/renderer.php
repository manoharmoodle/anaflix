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
 * User custom_reshorts
 * @package    block_custom_reshorts
 */
defined('MOODLE_INTERNAL') || die();

  function render_whatsnew() {
      global $DB, $CFG;
      $lastweek = strtotime('-7 days');
      $allreshorts = $DB->get_records_sql("SELECT * FROM {reshorts} WHERE approve = 1 and timemodified > $lastweek");
      $html = '
      
      <style>
      .text-popup {
          position: absolute;
          bottom: 0;
          background: white;
          width: 1;
          width: 72%;
          text-align: center;
          color: rgb(0, 0, 0);
          height: 68px;
          padding: 10px;
      }
      .text-popup p{
          color: rgb(0, 0, 0);
          font-weight: 600;
        
      }
      i#new-custom-left {
          z-index: 9;
      }
      </style>
      <div class="Course_Pogress">
      <h3><b>RE-Shorts - Your daily real estate news update</b></h3>
    </div>

    <div class="new-custom-wrapper">
      <i id="new-custom-left" class="">
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
      <ul class="new-custom-carousel">';
          foreach($allreshorts as $reshort) {
              $html .= '<li class="new-custom-card" style="position: relative;">
              <a href="'.$CFG->wwwroot.'/blocks/custom_reshorts/moredetails.php?id='.$reshort->id.'">
              <div class="new-custom-img"><img src="'.$reshort->imagepath.'" alt="img" draggable="false">

              </div>
              </a>
              <div class="text-popup">
                  <p>'.$reshort->title.'</p>
              </div>
            
          </li>';
          }

        $html .= ' <!-- Add other list items here -->
      </ul>
      <i id="new-custom-right" class="">
          <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M17.0428 11.8767C17.328 12.1535 17.4917 12.5322 17.498 12.9297C17.5042 13.3271 17.3524 13.7108 17.0759 13.9964L11.5091 19.7422C11.2321 20.0278 10.853 20.1917 10.4552 20.1978C10.2582 20.2008 10.0626 20.1651 9.87944 20.0925C9.6963 20.0199 9.52924 19.912 9.38781 19.7748C9.24639 19.6377 9.13335 19.474 9.05517 19.2932C8.97699 19.1124 8.93519 18.9179 8.93215 18.721C8.92912 18.524 8.96491 18.3283 9.03749 18.1452C9.11007 17.9621 9.21801 17.795 9.35515 17.6536L13.8787 12.9863L9.21143 8.46269C9.06596 8.32665 8.94904 8.16299 8.86749 7.98128C8.78594 7.79956 8.74139 7.60343 8.73646 7.40431C8.73152 7.2052 8.76628 7.00709 8.83873 6.82156C8.91117 6.63602 9.01984 6.46678 9.15839 6.32369C9.29694 6.1806 9.4626 6.06654 9.64571 5.98816C9.82881 5.90977 10.0257 5.86865 10.2249 5.86717C10.424 5.86569 10.6215 5.90389 10.8058 5.97955C10.99 6.0552 11.1573 6.16679 11.298 6.30781L17.0448 11.8766L17.0428 11.8767Z" fill="#F8F8F8"/>
          </svg>
      </i>
      <script>
          const newCustomWrapper = document.querySelector(".new-custom-wrapper");
          const newCustomCarousel = document.querySelector(".new-custom-carousel");
          const firstNewCustomCardWidth = newCustomCarousel.querySelector(".new-custom-card").offsetWidth;
          const newCustomArrowBtns = document.querySelectorAll(".new-custom-wrapper i");
          const newCustomCarouselChildrens = [...newCustomCarousel.children];
      
          let isNewDragging = false, isNewAutoPlay = true, startNewX, startNewScrollLeft, newTimeoutId;
      
          // Get the number of cards that can fit in the carousel at once
          let newCardPerView = Math.round(newCustomCarousel.offsetWidth / firstNewCustomCardWidth);
      
          // Insert copies of the last few cards to the beginning of the carousel for infinite scrolling
          newCustomCarouselChildrens.slice(-newCardPerView).reverse().forEach(card => {
              newCustomCarousel.insertAdjacentHTML("afterbegin", card.outerHTML);
          });
      
          // Insert copies of the first few cards to the end of the carousel for infinite scrolling
          newCustomCarouselChildrens.slice(0, newCardPerView).forEach(card => {
              newCustomCarousel.insertAdjacentHTML("beforeend", card.outerHTML);
          });
      
          // Scroll the carousel at the appropriate position to hide the first few duplicate cards on Firefox
          newCustomCarousel.classList.add("no-transition");
          newCustomCarousel.scrollLeft = newCustomCarousel.offsetWidth;
          newCustomCarousel.classList.remove("no-transition");
      
          // Add event listeners for the arrow buttons to scroll the carousel left and right
          newCustomArrowBtns.forEach(btn => {
              btn.addEventListener("click", () => {
                  newCustomCarousel.scrollLeft += btn.id == "new-custom-left" ? -firstNewCustomCardWidth : firstNewCustomCardWidth;
              });
          });
      
          const newDragStart = (e) => {
              isNewDragging = true;
              newCustomCarousel.classList.add("dragging");
              // Records the initial cursor and scroll position of the carousel
              startNewX = e.pageX;
              startNewScrollLeft = newCustomCarousel.scrollLeft;
          };
      
          const newDragging = (e) => {
              if (!isNewDragging) return; // if isDragging is false return from here
              // Updates the scroll position of the carousel based on the cursor movement
              newCustomCarousel.scrollLeft = startNewScrollLeft - (e.pageX - startNewX);
          };
      
          const newDragStop = () => {
              isNewDragging = false;
              newCustomCarousel.classList.remove("dragging");
          };
      
          const newInfiniteScroll = () => {
              // If the carousel is at the beginning, scroll to the end
              if (newCustomCarousel.scrollLeft === 0) {
                  newCustomCarousel.classList.add("no-transition");
                  newCustomCarousel.scrollLeft = newCustomCarousel.scrollWidth - (2 * newCustomCarousel.offsetWidth);
                  newCustomCarousel.classList.remove("no-transition");
              }
              // If the carousel is at the end, scroll to the beginning
              else if (Math.ceil(newCustomCarousel.scrollLeft) === newCustomCarousel.scrollWidth - newCustomCarousel.offsetWidth) {
                  newCustomCarousel.classList.add("no-transition");
                  newCustomCarousel.scrollLeft = newCustomCarousel.offsetWidth;
                  newCustomCarousel.classList.remove("no-transition");
              }
      
              // Clear the existing timeout & start autoplay if the mouse is not hovering over the carousel
              clearTimeout(newTimeoutId);
              if (!newCustomWrapper.matches(":hover")) newAutoPlay();
          };
      
          const newAutoPlay = () => {
              if (window.innerWidth < 800 || !isNewAutoPlay) return; // Return if the window is smaller than 800 or isAutoPlay is false
              // Autoplay the carousel after every 2500 ms
              newTimeoutId = setTimeout(() => newCustomCarousel.scrollLeft += firstNewCustomCardWidth, 2500);
          };
          newAutoPlay();
      
          newCustomCarousel.addEventListener("mousedown", newDragStart);
          newCustomCarousel.addEventListener("mousemove", newDragging);
          document.addEventListener("mouseup", newDragStop);
          newCustomCarousel.addEventListener("scroll", newInfiniteScroll);
          newCustomWrapper.addEventListener("mouseenter", () => clearTimeout(newTimeoutId));
          newCustomWrapper.addEventListener("mouseleave", newAutoPlay);
      </script>
      
  </div>
      ';
      return $html;
  }

  function render_moredetail($id) {
      global $DB, $CFG;
      $reshort = $DB->get_record('reshorts', ['id' => $id]);
      $html = '
      <style>

          .card-body-custom{
              width: 100%;
              height: 100vh;
            
              display: flex;
              align-items: start;
              justify-content: center;
          }
          .one-card-main{
              box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
            width: 50%!important;
            background-color: white;
            padding: 10px;
            border-radius: 10px;
          }
          .text-area-card{
              padding: 10px;
          display: flex;
          column-gap: 10px;
      
      
          }
          .text-card {
          font-family: system-ui;
      }
      .text-card p {
          color: #404040;
      }
      #copyurl{
      cursor: pointer;
      }
      p.readMore {
          font-size: 11px;
          margin-top: 11px;
          color: gray;
      }
      .pop_url {
      
      background: rgba(56, 56, 56, 0.36);
      border-radius: 16px;
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
      border: 1px solid rgba(68, 68, 68, 0.3);
      position: fixed;
      z-index: 999999;
      text-align: 0;
      top: 0;
      display: none;
      left: 0;
      right: 0;
      
      bottom: 0;
      align-items: center;
      justify-content: center;
      }
      .url_box {
        position: absolute;
        max-width: 27rem;
        position: relative;
        background-color: white;
        padding: 20px;
        left: 50%;
        transform: translate(-50%, -50%);
        top: 50%;
      }
      .card.card-body{
      background: transparent;
      }
      button#cut {
        position: absolute;
        top: 0;
        border: none;
        background-color: transparent;

        right: 10px;
        }
        
        .Share-this-course {
          color: #2d2f31;
          font-size: 19px;
          font-weight: 700;
        }
        span.input_border {
          justify-content: space-between;
          display: flex;
          border: 1px solid #2d2f31;
        }
        span.input_border {
          display: flex;
          border: 1px solid #d1d7dc;
        }
        #myInput {
          border: none;
          padding: 0px 10px;
          width: 100%;
        }
        button#copy_text {
          width: 143px;
          border: none;
          background: #904e8e;
          padding: 10px 13px;
          color: white;
          cursor: pointer;
        }
        p.copy {
          display: inline-flex;
          height: 32px;
          padding: 8px 16px;
          justify-content: center;
          align-items: center;
          gap: 4px;
          border-radius: 8px;
    
          color: #904e8e;
          flex-shrink: 0;
        }
        .display_pop {
          display: block !important;
        }
        button#cut {
        position: absolute;
        top: 9px;
        border: none;
        background-color: transparent;
    
        right: 10px;
        cursor: pointer;
      }
      
      </style>

          <div class="pop_url">
              <div class="url_box">
                <button id="cut">X</button>
                <p class="Share-this-course">Share this course</p>
                <span class="input_border">
                  <input type="text" value="" id="myInput">
                  <button id="copy_text">Copy text</button>
                </span>
                <p class="copy"></p>
              </div>
            </div>
          <div class="card-body-custom">
              <div class="one-card-main">
                <div class="one-card-img">
      <img width="100%" src="'.$reshort->imagepath.'" alt="">
                </div>
                <div class="text-area-card">
                  <div class="text-card">
                <p>'.$reshort->content.' </p>
      <p class="readMore"><a href="'.$reshort->source.'">Read more </a></p>
                  </div>
                  <div id="copyurl" class="share">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M18 22C17.1667 22 16.4583 21.7083 15.875 21.125C15.2917 20.5417 15 19.8333 15 19C15 18.8833 15.0083 18.7623 15.025 18.637C15.0417 18.5117 15.0667 18.3993 15.1 18.3L8.05 14.2C7.76667 14.45 7.45 14.646 7.1 14.788C6.75 14.93 6.38333 15.0007 6 15C5.16667 15 4.45833 14.7083 3.875 14.125C3.29167 13.5417 3 12.8333 3 12C3 11.1667 3.29167 10.4583 3.875 9.875C4.45833 9.29167 5.16667 9 6 9C6.38333 9 6.75 9.071 7.1 9.213C7.45 9.355 7.76667 9.55067 8.05 9.8L15.1 5.7C15.0667 5.6 15.0417 5.48767 15.025 5.363C15.0083 5.23833 15 5.11733 15 5C15 4.16667 15.2917 3.45833 15.875 2.875C16.4583 2.29167 17.1667 2 18 2C18.8333 2 19.5417 2.29167 20.125 2.875C20.7083 3.45833 21 4.16667 21 5C21 5.83333 20.7083 6.54167 20.125 7.125C19.5417 7.70833 18.8333 8 18 8C17.6167 8 17.25 7.92933 16.9 7.788C16.55 7.64667 16.2333 7.45067 15.95 7.2L8.9 11.3C8.93333 11.4 8.95833 11.5127 8.975 11.638C8.99167 11.7633 9 11.884 9 12C9 12.1167 8.99167 12.2377 8.975 12.363C8.95833 12.4883 8.93333 12.6007 8.9 12.7L15.95 16.8C16.2333 16.55 16.55 16.3543 16.9 16.213C17.25 16.0717 17.6167 16.0007 18 16C18.8333 16 19.5417 16.2917 20.125 16.875C20.7083 17.4583 21 18.1667 21 19C21 19.8333 20.7083 20.5417 20.125 21.125C19.5417 21.7083 18.8333 22 18 22ZM18 6C18.2833 6 18.521 5.904 18.713 5.712C18.905 5.52 19.0007 5.28267 19 5C19 4.71667 18.904 4.479 18.712 4.287C18.52 4.095 18.2827 3.99933 18 4C17.7167 4 17.479 4.096 17.287 4.288C17.095 4.48 16.9993 4.71733 17 5C17 5.28333 17.096 5.521 17.288 5.713C17.48 5.905 17.7173 6.00067 18 6ZM6 13C6.28333 13 6.521 12.904 6.713 12.712C6.905 12.52 7.00067 12.2827 7 12C7 11.7167 6.904 11.479 6.712 11.287C6.52 11.095 6.28267 10.9993 6 11C5.71667 11 5.479 11.096 5.287 11.288C5.095 11.48 4.99933 11.7173 5 12C5 12.2833 5.096 12.521 5.288 12.713C5.48 12.905 5.71733 13.0007 6 13ZM18 20C18.2833 20 18.521 19.904 18.713 19.712C18.905 19.52 19.0007 19.2827 19 19C19 18.7167 18.904 18.479 18.712 18.287C18.52 18.095 18.2827 17.9993 18 18C17.7167 18 17.479 18.096 17.287 18.288C17.095 18.48 16.9993 18.7173 17 19C17 19.2833 17.096 19.521 17.288 19.713C17.48 19.905 17.7173 20.0007 18 20Z" fill="black"/>
                          </svg>
                          
                  </div>
      
                </div>
      
              </div>
      
          </div>
        
        <script>
          const copyurl = document.querySelector("#copyurl")
          copyurl.addEventListener("click",()=>{
              var url = window.location.href;
        navigator.clipboard.writeText(url).then(function() {
          console.log("URL copied to clipboard successfully.");
        }, function(err) {
          console.error("Unable to copy URL to clipboard. Error: ", err);
        });
          })
      
          const pop_url = document.querySelector(".pop_url")
          const myInput = document.querySelector("#myInput")
          const share = document.querySelector(".share")
          const cut = document.querySelector("#cut")
          const copy = document.querySelector(".copy")
          const copy_text = document.querySelector("#copy_text")
          share.addEventListener("click", () => {
      
            pop_url.classList.add("display_pop")
            const url = window.location.href
            myInput.value = url
          })
          cut.addEventListener("click", () => {
            pop_url.classList.remove("display_pop")
          })
          copy_text.addEventListener("click", () => {
            var copyText = document.getElementById("myInput");
      
      
            myInput.select();
            myInput.setSelectionRange(0, 99999); // For mobile devices
      
      
            navigator.clipboard.writeText(myInput.value);

            copy.innerHTML = "coped"
      
      
          })
        </script>
  ';
      return $html;
  }

  function render_myanacoins() {
    global $DB, $USER;
    $table = new html_table();
    $tableheader = array('Sno.',
                        'earn anacoins',
                        'target',
                        'myaction'
                        );
    $anacoinsdistribution = $DB->get_records_sql("SELECT lpd.id, lp.name, lp.points FROM {l_points_distribution} lpd JOIN {leaderboard_points} lp ON lp.id = lpd.fieldid WHERE lpd.userid = $USER->id");

    $table->head = $tableheader;
    $data = [];
    $sno = 1;
    $point= [];
    foreach ($anacoinsdistribution as $anacoins) {
        $row = [];
        $row[] = $sno;
        $row[] = $anacoins->points;
        $row[] = $anacoins->name;
        if ($anacoins->name == "course") {
          $row[] = 'Course Complete';
        } elseif ($anacoins->name == "policy") {
          $row[] = 'Policy Accepted';
        } elseif ($anacoins->name == "ReShorts") {
          $row[] = 'Reshort View';
        }
        $data[] = $row;
        $point[] = $anacoins->points;
        $sno++;
    }
    $row = [];
    $row[] = '<b>Total earned<b>';
    $row[] = '<b>'.array_sum($point).'<b>';
    $row[] = '';
    $row[] = '';
    $data[] = $row;
    $table->size = array('30%', '30%', '30%', '10%');
    $table->align = array('left', 'left', 'left', 'center');
    $table->width = '100%';
    $table->data = $data;
    $table->id = 'anacoins-list';
    $out = html_writer::table($table);
    return $out;
  }
