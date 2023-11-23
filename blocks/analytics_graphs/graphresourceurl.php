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
require('../../config.php');
require('lib.php');
$course = htmlspecialchars(required_param('id', PARAM_INT));
$startdate = optional_param('from', '***', PARAM_TEXT);
$hidden = optional_param('hidden', false, PARAM_TEXT);
$topic = optional_param('topic', '', PARAM_TEXT);
$state = optional_param('userstate', false, PARAM_TEXT);
$dataformat = optional_param('dataformat', false, PARAM_TEXT);

// YATH Customization : Code for download the user records.
if (!strlen($topic) || !$state || !$dataformat) {
    require('javascriptfunctions.php');
}
// YATH Customization End.

global $DB, $CFG;
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
/* Access control */

require_login($course);
$context = context_course::instance($course);
require_capability('block/analytics_graphs:viewpages', $context);
$courseparams = get_course($course);
if ($startdate === '***') {
    $startdate = $courseparams->startdate;
} else {
    $datetoarray = explode('-', $startdate);
    $starttime = new DateTime("now", core_date::get_server_timezone_object());
    $starttime->setDate((int) $datetoarray[0], (int) $datetoarray[1], (int) $datetoarray[2]);
    $starttime->setTime(0, 0, 0);
    $startdate = $starttime->getTimestamp();
}

$coursename = get_string('course', 'block_analytics_graphs') . ": " . $courseparams->fullname;
$students = block_analytics_graphs_get_students($COURSE);
$numberofstudents = count($students);
if ($numberofstudents == 0) {
    echo (get_string('no_students', 'block_analytics_graphs'));
    exit;
}
foreach ($students as $tuple) {
    $arrayofstudents[] = array('userid' => $tuple->id, 'nome' => $tuple->firstname . ' ' . $tuple->lastname, 'email' => $tuple->email);
}
/* Get accesses to resources and urls */


$requestedtypes = array();
foreach ($_GET as $querystringvariable => $value) {
    if (substr($querystringvariable, 0, strlen("mod")) !== "mod") {
        continue;
    }
    $temp = $value;
    if (!in_array($temp, $requestedtypes)) { // Prevent duplicates.
        array_push($requestedtypes, $temp);
    }
}

if (count($requestedtypes) < 1) {
    echo "<html style=\"background-color: #f4f4f4;\">";
    echo "<div style=\"width: 200px;height: 100px;position:absolute;left:0; right:0;top:0;
        bottom:0;margin:auto;max-width:100%;max-height:100%;
        overflow:auto;background-color: white;border-radius: 25px;padding: 20px;border: 2px solid darkgray;text-align: center;\">";
    echo "<h3>" . (get_string('no_types_requested', 'block_analytics_graphs')) . "</h3>";
    echo "</div>";
    echo "</html>";
    exit;
}

$result = block_analytics_graphs_get_resource_url_access($course, $students, $requestedtypes, $startdate, $hidden);
$modinfo = get_fast_modinfo($course);

$numberofresources = count($result);
if ($numberofresources == 0) {
    echo "<html style=\"background-color: #f4f4f4;\">";
    echo "<div style=\"width: 200px;height: 100px;position:absolute;left:0; right:0;top:0;
        bottom:0;margin:auto;max-width:100%;max-height:100%;
        overflow:auto;background-color: white;border-radius: 25px;padding: 20px;border: 2px solid darkgray;text-align: center;\">";
    echo "<h3>" . (get_string('no_graph', 'block_analytics_graphs')) . "</h3>";
    echo "</div>";
    echo "</html>";
    exit;
}
$counter = 0;
$numberofaccesses = 0;
$numberofresourcesintopic = 0;
$resourceid = 0;
$numberofresourcesintopic = array();
$test = array();

foreach ($result as $tuple) {
    if ($resourceid == 0) { /* First time in loop -> get topic and content name */
        $numberofresourcesintopic[$tuple->section] = 1;
        $statistics[$counter]['topico'] = $tuple->section;
        $statistics[$counter]['tipo'] = $tuple->tipo;
        $cm = $modinfo->get_cm($tuple->cmid);
        $statistics[$counter]['material'] = $cm->name;

        if ($tuple->userid) { /* If a user accessed -> get name */
            $statistics[$counter]['studentswithaccess'][] = array(
                'userid' => $tuple->userid,
                'nome' => $tuple->firstname . " " . $tuple->lastname,
                'email' => $tuple->email,
                'access' => $tuple->timecreated
            );
            $numberofaccesses++;
        }
        $resourceid = $tuple->ident;
    } else { // Not first time in loop.
        if ($resourceid == $tuple->ident and $tuple->userid) {
            // If same resource and someone accessed, add student.
            $statistics[$counter]['studentswithaccess'][] = array(
                'userid' => $tuple->userid,
                'nome' => $tuple->firstname . " " . $tuple->lastname,
                'email' => $tuple->email,
                'access' => $tuple->timecreated
            );
            $numberofaccesses++;
            $test[] = $tuple->userid;

        }
        if ($resourceid != $tuple->ident) {
            // If new resource, finish previous and create new.
            if ($statistics[$counter]['topico'] == $tuple->section) {
                $numberofresourcesintopic[$tuple->section]++;
            } else {
                $numberofresourcesintopic[$tuple->section] = 1;
            }
            $statistics[$counter]['numberofaccesses'] = $numberofaccesses;
            $statistics[$counter]['numberofnoaccess'] = $numberofstudents - $numberofaccesses;
            if ($numberofaccesses == 0) {
                $statistics[$counter]['studentswithnoaccess'] = $arrayofstudents;
            } else if ($statistics[$counter]['numberofnoaccess'] > 0) {
                $statistics[$counter]['studentswithnoaccess'] = block_analytics_graphs_subtract_student_arrays(
                    $arrayofstudents,
                    $statistics[$counter]['studentswithaccess']
                );
            }
            $counter++;
            $statistics[$counter]['topico'] = $tuple->section;
            $statistics[$counter]['tipo'] = $tuple->tipo;
            $resourceid = $tuple->ident;
            $cm = $modinfo->get_cm($tuple->cmid);
            $statistics[$counter]['material'] = $cm->name;

            if ($tuple->userid) {
                $statistics[$counter]['studentswithaccess'][] = array(
                    'userid' => $tuple->userid,
                    'nome' => $tuple->firstname . " " . $tuple->lastname,
                    'email' => $tuple->email,
                    'access' => $tuple->timecreated
                );
                $numberofaccesses = 1;
            } else {
                $numberofaccesses = 0;
            }
        }
    }
}

/* Adjust last access  */
$statistics[$counter]['numberofaccesses'] = $numberofaccesses;
$statistics[$counter]['numberofnoaccess'] = $numberofstudents - $numberofaccesses;
if ($numberofaccesses == 0) {
    $statistics[$counter]['studentswithnoaccess'] = $arrayofstudents;
} else if ($statistics[$counter]['numberofnoaccess'] > 0) {
    $statistics[$counter]['studentswithnoaccess'] = block_analytics_graphs_subtract_student_arrays(
        $arrayofstudents,
        $statistics[$counter]['studentswithaccess']
    );
}

// YATH Customization : Code for download the user records.
if (strlen($topic) && $state && $dataformat) {
    $topicname = $statistics[$topic]['material'];

    $accessusers = $statistics[$topic][$state];

    if ($dataformat) {
        $originfields = array('id' => 'id',
                        'username'  => 'username',
                        'email'     => 'email',
                        'firstname' => 'firstname',
                        'lastname'  => 'lastname',
                        'idnumber'  => 'idnumber',
                        'institution' => 'institution',
                        'department' => 'department',
                        'phone1'    => 'phone1',
                        'phone2'    => 'phone2',
                        'city'      => 'city',
                        'country'   => 'country',
                        'access' => 'access'
                    );
    
        $extrafields = profile_get_user_fields_with_data(0);
        $profilefields = [];
        foreach ($extrafields as $formfield) {
            $profilefields[$formfield->get_shortname()] = $formfield->get_shortname();
        }
    
        $filename = clean_filename('test');
    
        $downloadusers = new ArrayObject($accessusers);

        $iterator = $downloadusers->getIterator();

        \core\dataformat::download_data($filename, $dataformat, array_merge($originfields, $profilefields), $iterator,
                function($downloadusers, $supportshtml) use ($originfields) {
    
            global $DB;
    
            if (!$user = $DB->get_record('user', array('id' => $downloadusers['userid']))) {
                return null;
            }

            $userprofiledata = array();
            foreach ($originfields as $field) {
                // Custom user profile textarea fields come in an array
                // The first element is the text and the second is the format.
                // We only take the text.
                if($field == 'access') {
                    if ($downloadusers[$field]) {
                        $userprofiledata[$field] = date("Y-m-d h:i:sa", $downloadusers[$field]);
                    } else {
                        $userprofiledata[$field] = 'no access';
                    }
                    continue;
                }
                if (is_array($user->$field)) {
                    $userprofiledata[$field] = reset($user->$field);
                } else if ($supportshtml) {
                    $userprofiledata[$field] = s($user->$field);
                } else {
                    $userprofiledata[$field] = $user->$field;
                }
            }

            // Formatting extra field if transform is true.
            $extrafields = profile_get_user_fields_with_data($downloadusers['userid']);
            foreach ($extrafields as $field) {
                $fieldkey = $field->get_shortname();
                if ($field->is_transform_supported()) {
                    $userprofiledata[$fieldkey] = $field->display_data();
                } else {
                    $userprofiledata[$fieldkey] = $field->data;
                }
            }

            return $userprofiledata;
        });
        exit;
    }
    
}
// Yath Custumization End.

/* Discover groups/groupings and members */
$groupmembers = block_analytics_graphs_get_course_group_members($COURSE);
$groupingmembers = block_analytics_graphs_get_course_grouping_members($COURSE);
$groupmembers = array_merge($groupmembers, $groupingmembers);
$groupmembersjson = json_encode($groupmembers);
$statistics = json_encode($statistics);
/* Log */
$event = \block_analytics_graphs\event\block_analytics_graphs_event_view_graph::create(
    array(
        'objectid' => $course,
        'context' => $context,
        'other' => "graphresourceurl.php",
    )
);
$event->trigger();
?>
<!--DOCTYPE HTML-->
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>
        <?php echo get_string('access_to_contents', 'block_analytics_graphs'); ?>
    </title>

    <link rel="stylesheet" href="externalref/jquery-ui-1.12.1/jquery-ui.css">
    <script src="externalref/jquery-1.12.2.js"></script>
    <script src="externalref/jquery-ui-1.12.1/jquery-ui.js"></script>
    <script src="externalref/highcharts.js"></script>
    <script src="externalref/no-data-to-display.js"></script>
    <script src="externalref/exporting.js"></script>
    <script src="externalref/export-csv-master/export-csv.js"></script>

    <style>
        .ui-dialog {
            position: fixed;
        }
        .highcharts-series-1 {
            display: none;
        }
    </style>


    <script type="text/javascript">
        var groups = <?php echo $groupmembersjson; ?>;
        var courseid = <?php echo json_encode($course); ?>;
        var coursename = <?php echo json_encode($coursename); ?>;
        var geral = <?php echo $statistics; ?>;
        var geral = parseObjToString(geral);
        var nome = "";
        var arrayofcontents = [];
        var nraccess_vet = [];
        var nrntaccess_vet = [];
        $.each(groups, function (index, group) {
            group.numberofaccesses = [];
            group.numberofnoaccess = [];
            group.studentswithaccess = [];
            group.studentswithnoaccess = [];
            group.material = [];
        });
        $.each(geral, function (index, value) {
            arrayofcontents.push(value.material);
            //default series value
            nraccess_vet.push(value.numberofaccesses);
            nrntaccess_vet.push(value.numberofnoaccess);
            $.each(groups, function (ind, group) {
                if (group.material[index] === undefined)
                    group.material[index] = value.material;
                if (value.numberofaccesses > 0) {
                    $.each(value.studentswithaccess, function (i, student) {
                        if (group.studentswithaccess[index] === undefined)
                            group.studentswithaccess[index] = [];
                        if (group.numberofaccesses[index] === undefined)
                            group.numberofaccesses[index] = 0;
                        if (group.members.indexOf(student.userid) != -1) {
                            group.numberofaccesses[index] += 1;
                            group.studentswithaccess[index].push(value.studentswithaccess[i]);
                        }
                    });

                } else {
                    if (group.studentswithaccess[index] === undefined)
                        group.studentswithaccess[index] = [];
                    if (group.numberofaccesses[index] === undefined)
                        group.numberofaccesses[index] = 0;
                }
                if (value.numberofnoaccess > 0) {
                    $.each(value.studentswithnoaccess, function (j, student) {
                        if (group.studentswithnoaccess[index] === undefined)
                            group.studentswithnoaccess[index] = [];
                        if (group.numberofnoaccess[index] === undefined)
                            group.numberofnoaccess[index] = 0;
                        if (group.members.indexOf(student.userid) != -1) {
                            group.numberofnoaccess[index] += 1;
                            group.studentswithnoaccess[index].push(value.studentswithnoaccess[j]);
                        }
                    });
                } else {
                    if (group.studentswithnoaccess[index] === undefined)
                        group.studentswithnoaccess[index] = [];
                    if (group.numberofnoaccess[index] === undefined)
                        group.numberofnoaccess[index] = 0;
                }
            });
        });
        function parseObjToString(obj) {
            var array = $.map(obj, function (value) {
                return [value];
            });
            return array;
        }
        $(function () {
            $('#container').highcharts({
                chart: {
                    type: 'bar',
                    zoomType: 'x',
                    panning: true,
                    panKey: 'shift'
                },
                title: {
                    text: ' <?php echo get_string('title_access', 'block_analytics_graphs'); ?>'
                },
                subtitle: {
                    text: ' <?php echo get_string('course', 'block_analytics_graphs') . ": "
                        . $courseparams->fullname . "<br>" .
                        get_string('begin_date', 'block_analytics_graphs') . ": "
                        . userdate($startdate); ?>'
                },
                xAxis: {
                    minRange: 1,
                    categories: arrayofcontents,
                    title: {
                        text: '<?php echo get_string('contents', 'block_analytics_graphs'); ?>'
                    },

                    plotBands: [
                        <?php
                        $inicio = -0.5;
                        $par = 2;
                        foreach ($numberofresourcesintopic as $topico => $numberoftopics) {
                            $fim = $inicio + $numberoftopics;
                            ?>
                            {
                                color: ' <?php echo ($par % 2 ? 'rgba(0, 0, 0, 0)' : 'rgba(68, 170, 213, 0.1)'); ?>',
                                label: {
                                    align: 'right',
                                    x: -10,
                                    verticalAlign: 'middle',
                                    text: '<?php echo get_string('topic', 'block_analytics_graphs') . " " . $topico; ?>',
                                    style: {
                                        fontStyle: 'italic',
                                    }
                                },
                                from: '<?php echo $inicio; ?>', // Start of the plot band
                                to: '<?php echo $fim; ?>', // End of the plot band
                            },
                            <?php
                            $inicio = $fim;
                            $par++;
                        }
                        ?>
                    ]
                },

                yAxis: {
                    min: 0,
                    maxPadding: 0.1,
                    minTickInterval: 1,
                    title: {
                        text: '<?= "No of Employees"; ?>',
                        align: 'high'
                    },
                    labels: {
                        overflow: 'justify'
                    }
                },

                tooltip: {
                    valueSuffix: ' <?php echo get_string('students', 'block_analytics_graphs'); ?>'
                },

                plotOptions: {
                    series: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function () {
                                    var nome_conteudo = this.x + "-" + this.series.name.charAt(0);
                                    $(".div_nomes").dialog("close");
                                    var group_id = $("#group_select").val();
                                    if (group_id !== undefined && group_id != "-") {//algum grupo foi selecionado
                                        $("#" + nome_conteudo + "-group-" + group_id).dialog("open");
                                        $("#" + nome_conteudo + "-group-" + group_id).dialog("option", "position", {
                                            my: "center top",
                                            at: "center top+" + 10,
                                            of: window
                                        });
                                    } else {
                                        $("#" + nome_conteudo).dialog("open");
                                        $("#" + nome_conteudo).dialog("option", "position", {
                                            my: "center top",
                                            at: "center top+" + 10,
                                            of: window
                                        });
                                        $("#" + nome_conteudo).dialog("open");
                                    }

                                }
                            }
                        }
                    },

                    bar: {
                        dataLabels: {
                            useHTML: this,
                            enabled: true
                        }
                    }
                },
                legend: {
                    layout: 'vertical',
                    align: 'left',
                    verticalAlign: 'top',
                    x: 0,
                    y: 5,
                    floating: true,
                    borderWidth: 1,
                    backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor || '#FFFFFF'),
                    shadow: true
                },
                credits: {
                    enabled: false
                },
                series: [{
                    name: '<?php echo get_string('access', 'block_analytics_graphs'); ?>',
                    data: nraccess_vet,
                    color: '#D74B8E'
                }, {
                    name: '<?php echo get_string('no_access', 'block_analytics_graphs'); ?>',
                    data: nrntaccess_vet,
                    color: '#8C62FF'
                }]
            });
        });
    </script>
</head>

<body>
    <?php if (count($groupmembers) > 0) { ?>
        <div style="margin: 20px;">
            <select id="group_select">
                <option value="-">
                    <?php echo json_encode(get_string('all_groups', 'block_analytics_graphs')); ?>
                </option>
                <?php
                foreach ($groupmembers as $key => $value) {
                    ?>
                    <option value="<?php echo $key; ?>"><?php echo $value["name"]; ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <?php
    }
    $totalmodule = $DB->get_records_sql_menu('SELECT id, name FROM {modules} WHERE visible = 1');
    $params =[];
    $sno = 1;
    foreach ($totalmodule as $module){
        $params['mod' . $sno] = $module;
        $sno++;
    }
    $params['id'] = $course;
    $params['hidden'] = true;

    ?>
    <div id="container" style="min-width: 800px; height:<?php echo ($counter + 1) * 50 + 180; ?>; margin: 0 auto"></div>
    <script>
        var count = 0;
        $.each(geral, function (index, value) {
            var nome = value.material;
            <?php
                $url = new moodle_url('/blocks/analytics_graphs/graphresourceurl.php', $params);
                $prefix = new moodle_url('/blocks/analytics_graphs/');
                $url = str_replace($prefix, "", $url);
            ?>
            var topic = value.topico;
            div = "";
            if (typeof value.studentswithaccess != 'undefined') {
                var titulo = "<h3>" + coursename + "</h3>" +
                            <?php echo json_encode(get_string('access', 'block_analytics_graphs')); ?> + " - " +
                    nome;
                div += "<div class='div_nomes' id='" + index + "-" +
                    "<?php echo substr(get_string('access', 'block_analytics_graphs'), 0, 1); ?>" +
                    "'>"+titulo+" <br><button onclick = 'window.location.href = \"<?=$url?>&topic="+topic+"&dataformat=csv&userstate=studentswithaccess\"'> Download CSV</button> <br> <button onclick = 'window.location.href = \"<?=$url?>&topic="+topic+"&dataformat=excel&userstate=studentswithaccess\"'> Download Excel</button></div>";
            }
            if (typeof value.studentswithnoaccess != 'undefined') {
                var titulo = coursename + "</h3>" +
                            <?php echo json_encode(get_string('no_access', 'block_analytics_graphs')); ?> + " - " +
                    nome;
                div += "<div class='div_nomes' id='" + index + "-" +
                    "<?php echo substr(get_string('no_access', 'block_analytics_graphs'), 0, 1); ?>" +
                    "'>"+titulo+" <br><button onclick = 'window.location.href = \"<?=$url?>&topic="+topic+"&dataformat=csv&userstate=studentswithnoaccess\"'> Download CSV</button> <br> <button onclick = 'window.location.href = \"<?=$url?>&topic="+topic+"&dataformat=excel&userstate=studentswithnoaccess\"'> Download Excel</button></div>";
            }
            count++;
            document.write(div);
        });
        $.each(groups, function (index, value) {
            div = "";
            if (typeof value.studentswithaccess != 'undefined') {
                $.each(value.studentswithaccess, function (ind, student) {
                    var titulo = coursename + "</h3>" +
                        <?php echo json_encode(get_string('access', 'block_analytics_graphs')); ?> + " - " +
                        value.material[ind];

                    if (student !== undefined)
                        div += "<div class='div_nomes' id='" + ind + "-" +
                            "<?php echo substr(get_string('access', 'block_analytics_graphs'), 0, 1); ?>" +
                            "-group-" + index + "'>" + createEmailForm(titulo, student, courseid, 'graphResourceUrl.php',
                                    <?php echo json_encode(get_string('info_coursetype', 'block_analytics_graphs') . ': ' .
                                        block_analytics_graphs_get_course_name($course)); ?> + ', ' + nome) + "</div>";
            });
                }
        if (typeof value.studentswithnoaccess != 'undefined') {
            $.each(value.studentswithnoaccess, function (ind, student) {
                var titulo = coursename + "</h3>" +
                            <?php echo json_encode(get_string('no_access', 'block_analytics_graphs')); ?> + " - " +
                    value.material[ind];

                if (student !== undefined)
                    div += "<div class='div_nomes' id='" + ind + "-" +
                        "<?php echo substr(get_string('no_access', 'block_analytics_graphs'), 0, 1); ?>" +
                        "-group-" + index + "'>" + createEmailForm(titulo, student, courseid, 'graphResourceUrl.php',
                                    <?php echo json_encode(get_string('info_coursetype', 'block_analytics_graphs') . ': ' .
                                        block_analytics_graphs_get_course_name($course)); ?> + ', ' + nome) + "</div>";
        });
                }
        document.write(div);
            });
        sendEmail();
        $("#group_select").change(function () {
            console.log($(this).val());
            convert_series_to_group($(this).val(), groups, geral, '#container');
        });
    </script>
    <style>
        text.highcharts-subtitle {
            font-size: 14px;

            fill: black !important;
            font-family: sans-serif !important;
            margin: 24px;
            line-height: 34px;
        }
    </style>
</body>

</html>