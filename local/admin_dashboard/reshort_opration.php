<?php
require_once(__DIR__ .'/../../config.php');
$edit = optional_param('edit', '', PARAM_RAW);
$title = optional_param('title', '', PARAM_RAW);
$tags = optional_param('tags', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);
$approve = optional_param('approve', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);
$message = optional_param('message', '', PARAM_RAW);
if (!empty($edit) && !empty($id)) {
    $data = $DB->get_record('reshorts', ['id' => $id]);
    $response = [];
    $response['title'] = $data->title;
    $response['tags'] = $data->tags;
    $response['timemodified'] = time();
    echo json_encode($response);
    
}

if((!empty($title) || !empty($tags)) && !empty($id)) {
    $dataobject = [];
    $dataobject['id'] = $id;
    $dataobject['title'] = $title;
    $dataobject['tags'] = $tags;
    $dataobject['timemodified'] = time();
    $upid = $DB->update_record('reshorts', $dataobject, $bulk=false);
    if($upid) {
        echo 'update successfully';
    }
}
if(!empty($approve) && !empty($id)) {
    $dataobject = [];
    $dataobject['id'] = $id;
    $dataobject['approve'] = $approve;
    $dataobject['timemodified'] = time();
    $upid = $DB->update_record('reshorts', $dataobject, $bulk=false);
    if($upid) {
        redirect("reshort.php", "Approve Successfully");
    }
}

if(!empty($delete) && !empty($id)) {
    $dataobject = [];
    $dataobject['id'] = $id;
    $deleteid = $DB->delete_records('reshorts', ['id' => $id]);
    if($deleteid) {
        redirect("reshort.php", "delete Successfully", null, core\output\notification::NOTIFY_ERROR);
    }
}