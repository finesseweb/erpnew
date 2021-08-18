<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */

/**
 * Get Max Tour Numeric Id
 * @return int
 */
function getMaxTourRequestId() {
    $sql = "SELECT MAX(tr_id) as tr_max_id FROM " . TB_PREF . "kv_tour_request";
    // display_error($sql);
    $result = db_query($sql, "could not get empl leave request");
    $rs = db_fetch($result);
    if ($rs) {
        $max_tour_id = $rs['tr_max_id'];
    } else {
        $max_tour_id = 0;
    }
    return $max_tour_id;
}

/**
 * Get New Tour Request Id
 * @return string
 */
function getNewTourRequestId($tour_num_id) {
    $month = date('m'); //getting month number
    $year = date('Y');
    $tr_id_pattern = 'TR-' . $month . '-' . $year . '-';
    $max_tr_id = $tour_num_id + 1; //getting max numeric id
    $invID = str_pad($max_tr_id, 3, '0', STR_PAD_LEFT); //format should be 001 - always 3 digit
    $new_tour_request_id = $tr_id_pattern . $invID;
    return $new_tour_request_id;
}

function getNewTourRequestIdnewf($tour_num_id) {
    $month1 = date('m'); //getting month number
    $year1 = date('Y');
    $tr_id_pattern1 = 'TRD-' . $month1 . '-' . $year1 . '-';
    $max_tr_id1 = $tour_num_id + 1; //getting max numeric id
    $invID1 = str_pad($max_tr_id1, 3, '0', STR_PAD_LEFT); //format should be 001 - always 3 digit
    $new_tour_request_id1 = $tr_id_pattern1 . $invID1;
    return $new_tour_request_id1;
}

function addTourRequest($employee_id,$dept_id,$desig_group_id,$desig_id, $tr_single_group, $tr_request_for, $tr_no_of_paxs,$tr_place_of_visit, $tr_request_date, $tr_fromdate, $tr_todate, $tr_no_of_days, $tr_purpose_of_visit, $tr_transport_by_company, $tr_mode_of_transport, $tr_accommodation_by, $tr_advance_required, $tr_advance_in, $tr_status) {
    //$tr_new_id = getNewTourRequestId();
    $tr_added_date = date('Y-m-d H:i:s');
    //display_error($unique_name);die;
    $sql = "INSERT INTO " . TB_PREF . "kv_tour_request (tr_employee_id,tr_emp_desig_id,tr_emp_dept_id,tr_emp_desig_group_id, tr_single_group, tr_request_for, tr_no_of_paxs,tr_place_of_visit, tr_request_date,tr_fromdate,tr_todate,tr_no_of_days,tr_purpose_of_visit,tr_transport_by_company,tr_mode_of_transport,tr_accommodation_by,tr_advance_required,tr_advance_in,tr_status,tr_added_date)
		VALUES (" . db_escape($employee_id) . ", " . db_escape($desig_id) . ", " . db_escape($dept_id) . ", ". db_escape($desig_group_id) . ", ". db_escape($tr_single_group) . ", "
            . db_escape($tr_request_for) . ", "
            . db_escape($tr_no_of_paxs) . ", "
            . db_escape($tr_place_of_visit) . ", "
            . db_escape($tr_request_date) . ", "
            . db_escape($tr_fromdate) . ", "
            . db_escape($tr_todate) . ","
            . db_escape($tr_no_of_days) . ","
            . db_escape($tr_purpose_of_visit) . ","
            . db_escape($tr_transport_by_company) . ",
		  " . db_escape($tr_mode_of_transport) . "," . db_escape($tr_accommodation_by) . "," . db_escape($tr_advance_required) .
            "," . db_escape($tr_advance_in) .
            "," . db_escape($tr_status) . "," . db_escape($tr_added_date) . ")";

    
    $ret = db_query($sql, "Can't insert employee tour request");

    $last_insert_id = db_insert_id();
    $new_tour_request_id = getNewTourRequestId($last_insert_id);
    $file_relative_path = uploadTourDocument($new_tour_request_id);
    $rs = updateTourRequestId($new_tour_request_id, $file_relative_path, $last_insert_id);
    
    if ($rs) {
        return $new_tour_request_id;
    } else {
        return FALSE;
    }
}
function updateTourRequest($tr_id,$dept_id,$desig_group_id,$desig_id, $tr_request_id, $employee_id, $tr_single_group, $tr_request_for, $tr_no_of_paxs, $tr_place_of_visit, $tr_request_date, $tr_fromdate, $tr_todate, $tr_no_of_days, $tr_purpose_of_visit, $tr_transport_by_company, $tr_mode_of_transport, $tr_accommodation_by, $tr_advance_required, $tr_advance_in, $tr_status) {
    //$tr_new_id = getNewTourRequestId();
    $logged_in_user_id = $_SESSION["wa_current_user"]->empl_id;//Getting current system user id
    
    $sql = "UPDATE " . TB_PREF . "kv_tour_request SET tr_employee_id=" . db_escape($employee_id). ", tr_emp_desig_id=".db_escape($desig_id) .", tr_emp_dept_id=".db_escape($dept_id) .", tr_emp_desig_group_id=".db_escape($desig_group_id) . ", tr_single_group=".db_escape($tr_single_group) .", tr_request_for=".db_escape($tr_request_for) .", tr_no_of_paxs=".db_escape($tr_no_of_paxs).", tr_place_of_visit=".db_escape($tr_place_of_visit)  .", tr_request_date=".db_escape($tr_request_date) .", tr_fromdate=".db_escape($tr_fromdate) .", tr_todate=".db_escape($tr_todate) .", tr_no_of_days=".db_escape($tr_no_of_days) .", tr_purpose_of_visit=".db_escape($tr_purpose_of_visit) .", tr_transport_by_company=".db_escape($tr_transport_by_company) .", tr_mode_of_transport=".db_escape($tr_mode_of_transport) .", tr_accommodation_by=".db_escape($tr_accommodation_by) .", tr_advance_required=".db_escape($tr_advance_required) .", tr_advance_in=".db_escape($tr_advance_in) .", tr_status=".db_escape($tr_status) .", tr_last_updated=".db_escape($tr_added_date) .", tr_updated_by=".db_escape($logged_in_user_id) ." WHERE tr_id = " . db_escape($tr_id);
    

    $rs = db_query($sql, "Can't update tour request id");
    
    $file_relative_path = uploadTourDocument($tr_request_id);
    $rs = updateTourRequestId($tr_request_id, $file_relative_path, $tr_id);
}

function getTourRequestsByEmployeeId($employee_id) {
    $sql = "SELECT * FROM " . TB_PREF . "kv_tour_request WHERE tr_status != 4 AND tr_employee_id='" . $employee_id . "'";
    return db_query($sql, "Can't get employee leave records");
}
function getTourRequestsByFilter($tour_status = NULL, $employee_id = NULL, $dept_id = NULL, $access_id = NULL, $req_from_date = NULL, $req_to_date = NULL) {
    $sql = "SELECT tr.* FROM " . TB_PREF . "kv_tour_request tr JOIN fa_kv_empl_job ej ON tr.tr_employee_id = ej.empl_id WHERE tr.tr_status != 4";
    $sql_part = '';
    if(!empty($employee_id)){
        $sql_part .= " AND tr.tr_employee_id='" . $employee_id . "'";
    }   
    if(!empty($dept_id)){
        $sql_part .= " AND tr.tr_emp_dept_id='" . $dept_id . "'";
    }
    if(!empty($tour_status)){
        $sql_part .= " AND tr.tr_status='" . $tour_status . "'";
    }
    if(!empty($req_from_date) && !empty($req_to_date)){
        $req_from_date = date2sql($req_from_date);
        $req_to_date = date2sql($req_to_date);
        $sql_part .= " AND tr.tr_request_date  >= '".$req_from_date."' AND tr.tr_request_date <= '".$req_to_date."'";
    }
    //Restrict Leave data based on user access id
    global $role_names;
    if(!empty($access_id) && strtolower($access_id) == $role_names['dean']){//Dean can see only staff leaves
    $sql_part .= " AND ej.employee_type = 2 ";
    }

    //Restrict Leave data based on user access id
    if(!empty($access_id) && strtolower($access_id) == $role_names['director']){//Directory can see only faculty leaves
    $sql_part .= " AND ej.employee_type = 1 ";
    }
    $sql .= $sql_part;
    
    return db_query($sql, "Can't get employee leave records");
}

function updateTourRequestId($new_tour_request_id, $file_relative_path, $tr_id) {
    $sql = "UPDATE " . TB_PREF . "kv_tour_request SET tr_request_id=" . db_escape($new_tour_request_id) . ", tr_attachment_path=".db_escape($file_relative_path) ." WHERE tr_id = " . db_escape($tr_id);

    db_query($sql, "Can't update tour request id");
}
function updateTourRequestIdDetailTb($new_tour_request_id, $tr_id, $bill_id) {
    $sql = "UPDATE " . TB_PREF . "tour_request_details SET tour_id=" . db_escape($new_tour_request_id) . ", bill_id=".db_escape($bill_id) ." WHERE id = " . db_escape($tr_id);

    db_query($sql, "Can't update tour request id");
}

function updateTourRequestIdDetailTb23($new_tour_request_id, $tr_id) {
    $sql = "UPDATE " . TB_PREF . "kv_tour_requestform SET tour_id=" . db_escape($new_tour_request_id) . " WHERE id = " . db_escape($tr_id);

    db_query($sql, "Can't update tour request id");
}

function uploadTourDocument($tour_req_id) {
    global $tour_request_document_folder;
    $upload_path = company_path() . "/" . $tour_request_document_folder;
    if (!file_exists($upload_path)) {
        mkdir($upload_path, 0777);
        $index_file = "<?php\nheader(\"Location: ../index.php\");\n?>";
        $fp = fopen($upload_path . "/index.php", "w");
        fwrite($fp, $index_file);
        fclose($fp);
    }
    $upload_path = $upload_path . '/';
    $current_year = date('Y');
    $current_month = date('m');
    if (!file_exists($upload_path . $current_year)) {
        mkdir($upload_path . $current_year);
    }
    if (!file_exists($upload_path . $current_year . '/' . $current_month)) {
        mkdir($upload_path . $current_year . '/' . $current_month);
    }
    $upload_path = $upload_path . $current_year . '/' . $current_month . '/';

    $file_name = $_FILES['tr_attachment_path']['name']; //getting user file name
    $name = $tour_req_id; //Taking Tour Request id as file name
    $extension = pathinfo($file_name, PATHINFO_EXTENSION); //getting file extension
    $file_path = $upload_path . $name.'.' . $extension;
    $cnt = 1;
    $status = FALSE;

    while (file_exists($file_path)) {
        $file_path = $upload_path . $name . '_' . $cnt . '.' . $extension;
        $cnt++;
    }
    $file_name_latest = pathinfo($file_path, PATHINFO_FILENAME). '.' . $extension;
    $file_relative_path = $current_year . '/' . $current_month . '/' . $file_name_latest;

    if (move_uploaded_file($_FILES['tr_attachment_path']["tmp_name"], $file_path)) {
        $status = TRUE;
    }
    if ($status) {
        return $file_relative_path;
    } else {
        return $status;
    }
}
function getTourRequestDetailById($tr_id, $employee_id){
    $sql = "SELECT * FROM " . TB_PREF . "kv_tour_request WHERE tr_employee_id='" . $employee_id . "' AND tr_id = $tr_id";    
    $result = db_query($sql, "Can't get employee leave records");
    return db_fetch($result);
}
function getTourRequestDetailByIdForApproval($tr_id){
    $sql = "SELECT * FROM " . TB_PREF . "kv_tour_request WHERE tr_id = $tr_id";    
    $result = db_query($sql, "Can't get employee leave records");
    return db_fetch($result);
}

function approveTourRequest($tr_id, $tr_status, $tr_comment_by_approval){
    $sql = "UPDATE " . TB_PREF . "kv_tour_request SET tr_status=" . db_escape($tr_status) . ", tr_comment_by_approval=".db_escape($tr_comment_by_approval) ." WHERE tr_id = " . db_escape($tr_id);
    return db_query($sql, "Can't update tour request id");
}
function deleteTourRequest($tr_id){
     $status = 4;
     $logged_in_user_id = $_SESSION["wa_current_user"]->empl_id;
     $tr_added_date = date('Y-m-d H:i:s');
     $sql = "UPDATE " . TB_PREF . "kv_tour_request SET tr_status=" . db_escape($status) . ", tr_last_updated='".$tr_added_date."', tr_updated_by='".$logged_in_user_id."' WHERE tr_id = " . db_escape($tr_id);
    return db_query($sql, "Can't update tour request id");
}
