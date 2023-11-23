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

function render_reshorts() {
    global $PAGE, $CFG, $DB, $OUTPUT;
    $reshorts = $DB->get_records('reshorts', ['approve' => 0]);
    $output = '';
    $data = array();
    if (!empty($reshorts)) {
        foreach ($reshorts as $key => $reshort) {
            $row = array();
            $id = $reshort->id;
            $row[] = $reshort->title;
            $row[] = $reshort->content;
            $row[] = $reshort->tags;
            $row[] = "<a target='_blank' href='".$reshort->source."'>".$reshort->source."</a>";
            $actionicons = '';
            $actionicons .= html_writer::link("#",
                                            html_writer::empty_tag('img', array('src' => $OUTPUT->image_url('i/edit'),
                                            'title' => 'Edit', 'onclick' => "edit($id)",'class' => 'iconsmall')));

            $deleteurl = new moodle_url($CFG->wwwroot.'/local/admin_dashboard/reshort_opration.php', array('id' => $id, 'delete' => 1));
            $approveurl = new moodle_url($CFG->wwwroot.'/local/admin_dashboard/reshort_opration.php', ['id' => $id, 'approve' => 1]);
            $actionicons .= html_writer::link($approveurl,
                html_writer::empty_tag('img', array('src' => $OUTPUT->image_url('i/checked'),
                    'title' => 'Check', 'class' => 'iconsmall', 'width' => '16', 'height' => '16')));
            $actionicons .= html_writer::link($deleteurl,
                html_writer::empty_tag('img', array('src' => $OUTPUT->image_url('i/delete'),
                'title' => 'Delete', 'class' => 'iconsmall', 'width' => '16', 'onclick' => "delete($id)",'height' => '16')));

            if ($actionicons) {
                $row[] = $actionicons;
            }

            $data[] = $row;
        }

        $tableheader = array("Title",
                            "Content",
                            "Tags",
                            "Source",
                            "Action"
                            );
        $table = new html_table();
        $table->head = $tableheader;
        $table->size = array('25%', '25%', '20%', '15', '10%');
        $table->align = array('center', 'center', 'center', 'center', 'center');
        $table->width = '100%';
        $table->data = $data;
        $table->id = 'reshort-list';
        $out = html_writer::table($table);
        $output .= '
        <style>
        input#id_submitbutton {
            background: #904e8f;
            border: none;
            /* padding: 5px; */
            border-radius: 7px;
            color: white;
            padding: 6px 15px;
        }
        .generaltable thead th {
            background: #904e8e !important;
            color: #ffffff;
        }
        </style>
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
              <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Update Successfully!</strong>
            </div>
            <div class="alert alert-danger alert-dismissible" id="notsuccess" style="display:none;">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Something is wrong!</strong>
            </div>

                <form>
                  <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Title:</label>
                    <input type="text" class="form-control" id="title">
                    <input type="hidden" class="form-control" id="contentid">
                  </div>
                  <div class="form-group">
                    <label for="message-text" class="col-form-label">Tags:</label>
                    <input type="text" class="form-control" id="tags" placeholder="tags....">
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
     
                <input type="button" class="btn btn-primary" id="title" value="Update" onclick="update()">
              </div>
            </div>
          </div>
        </div>';
        $output .= '<div class="table-responsive">'.$out.'</div>';
        $output .= html_writer::script("
        
        function edit(id) {
            $.ajax({
                url: 'reshort_opration.php',
                type: 'GET',
                data: { id: id, edit: 'true' }, // Added a comma here
                success: function(res) {
                    // Parse the JSON response using JSON.parse
                    var data = JSON.parse(res);
                    $('#title').val(data.title);
                    $('#tags').val(data.tags);
                    $('#contentid').val(id);
        
                    $('#exampleModal').modal('toggle');
                },
                error: function(xhr, status, error) {
                    // Handle any errors here
                    console.error('Error: ' + error);
                }
            });
        }

        function update() {
            var titleupdate = $('#title').val();
            var tagsupdate = $('#tags').val();
            var id = $('#contentid').val();
            $.ajax({
                url: 'reshort_opration.php',
                type: 'GET',
                data: { id: id, title: titleupdate, tags:tagsupdate }, // Added a comma here
                success: function(res) {
                    if(res) {
                        $('#success').show();
                        window.location.reload();
                    } else {
                        $('#notsuccess').show();
                    }
                },
                error: function(xhr, status, error) {
                    // Handle any errors here
                    console.error('Error: ' + error);
                }
            });
        }
        
        function approve(id){
            $.ajax({
                url: 'reshort_opration.php',
                type: 'GET',
                data: { id: id, approve: true}, // Added a comma here
                success: function(res) {
                    if(res) {
                        $('#success').show();
                        window.location.reload();
                    } else {
                        $('#notsuccess').show();
                    }
                },
                error: function(xhr, status, error) {
                    // Handle any errors here
                    console.error('Error: ' + error);
                }
            });
        }
  ");
    } else {
        $output = '<div class="alert alert-info w-100 float-left">No Data</div>';
    }

    return $output;
}

function render_pending_reshorts() {
    global $PAGE, $CFG, $DB, $OUTPUT;
    $reshorts = $DB->get_records('reshorts',['approve' => 1]);
    $output = '';
    $data = array();
    if (!empty($reshorts)) {
        foreach ($reshorts as $key => $reshort) {
            $row = array();
            $id = $reshort->id;
            $row[] = $reshort->title;
            $row[] = $reshort->content;
            $row[] = $reshort->tags;
            $row[] = "<a target='_blank' href='".$reshort->source."'>".$reshort->source."</a>";
            $row[] = date('Y-m-d', $reshort->timemodified);
            
            $data[] = $row;
        }

        $tableheader = array("Title",
                            "Content",
                            "Tags",
                            "Source",
                            "Last edited date"
                            );
        $table = new html_table();
        $table->head = $tableheader;
        $table->size = array('25%', '25%', '20%', '15', '10%');
        $table->align = array('center', 'center', 'center', 'center', 'center');
        $table->width = '100%';
        $table->data = $data;
        $table->id = 'reshort-list';
        $out = html_writer::table($table);
        $output .= '
      ';
        $output .= '
        <style>
        input#id_submitbutton {
            background: #904e8f;
            border: none;
            /* padding: 5px; */
            border-radius: 7px;
            color: white;
            padding: 6px 15px;
        }
        .generaltable thead th {
            background: #904e8e !important;
            color: #ffffff;
        }
        </style>
        <div class="table-responsive">'.$out.'</div>';
        $output .= html_writer::script("

                        ");
    } else {
        $output = '<div class="alert alert-info w-100 float-left">No Data</div>';
    }

    return $output;
}

function render_alltextstickers($alltextstickers, $offset) {
    global $DB, $OUTPUT, $CFG;
    echo("<style>

        .collection thead th, .generaltable thead th {
            background: #904e8e !important;
            color: #fff;
            height: 50px;
            font-weight: 600;
            border: 0!important;
            padding-left: 1em!important;
            padding-right: 1em!important;
        }
        th.header.c0 {
            border-radius: 12px 0px 0px 0px;
        }
        th.header.c5.lastcol {
            border-radius: 0px 12px 0px 0px;
        }
        td.lastcol {
            display: flex;
            column-gap: 44px;
            align-items: center;
            /* background: red; */
        }
    </style>");
    $table = new html_table();

    $tableheader = array(
        'S.No.',
        'Text',
        'Image',
        'Valid From',
        'Valid To',
        'Action'
    );

    $table->head = $tableheader;
    $data = [];
    $output = '';

    if (!empty($alltextstickers)) {
        $i = $offset + 1;
        foreach ($alltextstickers as $alltextsticker) {
            $statusclass = '';

            $row = array();
            $row[] = $i;
            $row[] = $alltextsticker->text ? $alltextsticker->text : 'No text';
            $row[] = $alltextsticker->imagepath ? '<a href = "' . $CFG->wwwroot . '/local/admin_dashboard/textsticker_files/' . $alltextsticker->imagepath . '"> View Image </a>' : 'Not define';
            $row[] = $alltextsticker->fromdate ? date('Y-m-d', $alltextsticker->fromdate) : 'Not define';
            $row[] = $alltextsticker->todate ? date('Y-m-d', $alltextsticker->todate) : 'Not define';
            $actionicons = "";
                $actionurl = new moodle_url($CFG->wwwroot.'/local/admin_dashboard/textsticker_form.php', array('edit' => $alltextsticker->id));
                $actionicons .= html_writer::link($actionurl,
                                                html_writer::empty_tag('img', array('src' => $OUTPUT->image_url('i/edit'),
                                                'title' => 'Edit', 'class' => 'iconsmall')));

                $deleteurl = new moodle_url($CFG->wwwroot.'/local/admin_dashboard/textsticker.php', array('delete' => $alltextsticker->id));
                $actionicons .= html_writer::link($deleteurl,
                    html_writer::empty_tag('img', array('src' => $OUTPUT->image_url('i/delete'),
                        'title' => 'Delete', 'class' => 'iconsmall', 'width' => '16', 'height' => '16')));

            if ($actionicons) {
                $row[] = $actionicons;
            }

            $data[] = $row;
            $i++;
        }

        $table->data = $data;
        $table->id = 'annual-trainincalendar-list';
        $alltextstickerhtml = html_writer::table($table);
        $alltextstickerhtml .= '
                    <div class="modal fade customized-modal" id="annualtrainingmodelpopup" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="annualtrainingmodelpopupLabel">All Textstickers</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body" id="training-detail">
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>';
        $output .= '<div class="table-responsive">'. $alltextstickerhtml .'</div>';
    } else {
        $output = '<div class="alert alert-info w-100 float-left">No data available</div>';
    }

    return $output;
}

function render_textstickers_form($text, $image, $from, $to, $update) {
    $today = date('Y-m-d', time());
    $href = '';

    // Create a new DOMDocument
    $dom = new DOMDocument();

    // Load the HTML content
    $dom->loadHTML($text);

    // Get all anchor tags (you can use more specific selectors if needed)
    $anchors = $dom->getElementsByTagName('a');

    foreach ($anchors as $anchor) {
        $href = $anchor->getAttribute('href');
        $text = $anchor->textContent;
    }

  $output = '
  <style>
    .text-image {
        display: flex;
        align-items: center;
        border: 3px dotted #bcbcbc;
        height: 96px;
        border-radius: 25px;
        flex-direction: column;
        justify-content: center;
    }

    button.submit-btn {
        border: none;
        background: #904e8e;
        color: white;
        padding: 7px 23px;
        border-radius: 10px;
        margin-top: 24px;
    }

    .inside-main {
        width: 46%;
    }

    label.footer {
        margin-top: 25px;
    }
    .inside-main {
        width: 46%;
        box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
        padding: 32px;
        border-radius: 17px;
    }
    label.top ,label.footer {
        font-size: 16px;
        font-weight: 400;
    }
    .main-body {
        display: flex;
        /* align-items: center; */
        column-gap: 113px;
        justify-content: center;
    }
    .inside-main {
        width: 67%;
        box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
        padding: 32px;
        border-radius: 17px;
    }
    label.top, label.footer {
        font-size: 19px;
        font-weight: 600;
    }
    .select-option {
        display: flex;
        flex-direction: column;
    }
    .option-div {
        margin-top: 27px;
    }
    select{
        padding: 3px 7px;
    }
    label.top{
    font-size: 19px;
    font-weight: 600;
    margin-top: 14px;
}
 #linkinputField {
    display: block;
    width: 100%;
    height: 2.573rem;
    padding: 0.429rem 1rem;
    font-size: .9375rem;
    font-weight: 400;
    line-height: 1.57143;
    color: #526069;
    background-color: #fff;
    -webkit-background-clip: padding-box;
    background-clip: padding-box;
    border: 1px solid #e4eaec;
    border-radius: 0.215rem;
}
button.filter_btn {
    background: #904e8e !important;
    border: none;
    padding: 5px 16px;
    border-radius: 5px;
}
button.filter_btn a{
    color: white !important;
}
.Edit-Text-Sticters {
    width: 48%;
}
section.section {
    display: flex;
    justify-content: end;
}
#error-message {
    color: red; /* Set text color to red */
    font-weight: bold; /* Make text bold */

    </style>
 
  <div class="main-body" >
    <div class="Edit-Text-Sticters">
    <section class="section">
        <button class="filter_btn"><a href = "alltextsticker.php">Edit Text Stickers</a></button>
    </section>
    <h2>Add TextSticker</h2>
    <div class="">
    <img width="400px" src="download.png">

    </div>
    </div>
    <div class="inside-main">
    <div id="error-message" style = "display:none"> * It is essential to include one text sticker out of the two available option.</div>

    <label class="top" for="inputField">Add TextSticker:</label>
    <form action = "textsticker.php" method = "post" enctype="multipart/form-data" id = "textstickerform">

        <input type="text" id="inputField" value = "'. $text.'" name="textsticker" placeholder="Add header textSticker">
        <label class="top" for="inputField">Add link TextSticker:</label>
        <input type="url" id="linkinputField" value = "'. $href.'" name="textstickerlink" placeholder="Add header Link textSticker">

        <label class="footer" for="inputImage">Add Footer TextSticker:</label>

        <div class="text-image">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 16V7.85L8.4 10.45L7 9L12 4L17 9L15.6 10.45L13 7.85V16H11ZM6 20C5.45 20 4.979 19.804 4.587 19.412C4.195 19.02 3.99934 18.5493 4 18V15H6V18H18V15H20V18C20 18.55 19.804 19.021 19.412 19.413C19.02 19.805 18.5493 20.0007 18 20H6Z" fill="#2D2F31"/>
                </svg>
                <input type="file" id="fileInput" class="input-file" value = "'.$image.'" name = "image" accept="image/*">
        </div>  
        <div class="select-option">

            <div class="option-div">
                <h4>Select date :</h4>
                <input type="date" id = "fromDate" onchange="setMinToDate()" name = "from" value = "' . $from . '" min="'.$today.'" required>
                <input type="hidden" name = "edit" value = "' . $update . '">

                <input type="date" id = "toDate" name = "to" value = "' . $to . '" min="'.$today.'" required disabled>
        </div>

        <div class="">
            <button class="submit-btn" >Submit</button>
        </div>
    </form>
    </div>

    </div> 
    <script>
    function setMinToDate() {
        // Get the selected "from" date
        const fromDate = new Date(document.getElementById("fromDate").value);
        const toDate = new Date(document.getElementById("toDate").value);
        if (fromDate > toDate) {
            document.getElementById("toDate").value = "";
        }
        // Set the minimum allowed date for the "to" date input
        document.getElementById("toDate").min = fromDate.toISOString().split("T")[0];
        document.getElementById("toDate").disabled = false;
    }
    // JavaScript to validate the date range
    document.getElementById("textstickerform").addEventListener("submit", function (event) {
        const fromDate = new Date(document.getElementById("fromDate").value);
        const toDate = new Date(document.getElementById("toDate").value);
        const image = document.getElementById("fileInput").value;
        const inputField = document.getElementById("inputField").value;

        if (!(image) && !(inputField)) {
            event.preventDefault(); // Prevent form submission
            $("#error-message").show();
        } else{
            $("#error-message").hide();
        }

    });
</script>
    </div>
       ';
  return $output;
}