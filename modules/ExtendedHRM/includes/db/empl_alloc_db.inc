<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */

function add_allocation($dept_id, $desig_group_id, $desig_id, $employees_id, $type_leave, $reason, $today_date, $from_date, $to_date, $no_of_days, $upload_file, $filesize, $filetype, $unique_name, $request_date) {
    //display_error($unique_name);die;
    $sql = "INSERT INTO " . TB_PREF . "kv_allocation_request (dept_id, desig_group_id, desig_id, employees_id, type_leave,reason,today_date,from_date,to_date,no_of_days,upload_file,filesize,filetype,unique_name,request_date,cal_year)
		VALUES (" . db_escape($dept_id) . ", "
            . db_escape($desig_group_id) . ", "
            . db_escape($desig_id) . ", "
            . db_escape($employees_id) . ", "
            . db_escape($type_leave) . ", "
            . db_escape($reason) . ","
            . db_escape($today_date) . ","
            . db_escape($from_date) . ","
            . db_escape($to_date) . ",
          " . db_escape($no_of_days) . "," . db_escape($upload_file) . "," . db_escape($filesize) .
        "," . db_escape($filetype) .
        "," . db_escape($unique_name) . ",'" . date("Y-m-d") . "',".date("Y").")";

    // begin_transaction();
    $ret = db_query($sql, "Can't insert empl leave request");

    // commit_transaction();
    return $ret;
}
function add_allocation_comp($dept_id, $desig_group_id, $desig_id, $employees_id, $type_leave, $reason, $today_date, $from_date, $to_date, $no_of_days, $upload_file, $filesize, $filetype, $unique_name, $request_date,$db_val,$wrk_time) {
    //display_error($unique_name);die;
    $sql = "INSERT INTO " . TB_PREF . "kv_comp_request (dept_id, desig_group_id, desig_id, employees_id, type_leave,reason,today_date,from_date,to_date,no_of_days,upload_file,filesize,filetype,unique_name,request_date,cal_year,working_hours)
		VALUES (" . db_escape($dept_id) . ", "
            . db_escape($desig_group_id) . ", "
            . db_escape($desig_id) . ", "
            . db_escape($employees_id) . ", "
            . db_escape($type_leave) . ", "
            . db_escape($reason) . ","
            . db_escape($today_date) . ","
            . db_escape($from_date) . ","
            . db_escape($to_date) . ",
          " . db_escape($no_of_days) . "," . db_escape($upload_file) . "," . db_escape($filesize) .
        "," . db_escape($filetype) .
        "," . db_escape($unique_name) . ",'" . date("Y-m-d") . "',".date("Y").","
            .db_escape($wrk_time).")";
    // begin_transaction();
    $ret = db_query($sql, "Can't insert empl leave request");

    // commit_transaction();
    return $ret;
}
function update_allocation($selected_id, $dept_id, $desig_group_id, $desig_id, $employees_id, $type_leave, $reason, $today_date, $from_date, $to_date, $no_of_days, $upload_file, $filesize, $filetype, $unique_name, $request_date) {
    //display_error($second_to_date);die;
    // $yr = date2sql($year);
    $sql = "UPDATE " . TB_PREF . "kv_allocation_request SET dept_id=" . db_escape($dept_id) . ", desig_id=" . db_escape($desig_id) . ",employees_id=" . db_escape($employees_id) . ", type_leave=" . db_escape($type_leave) . ",reason=" . db_escape($reason) . ",today_date=" . db_escape($today_date) . ", from_date=" . db_escape($from_date) . ",to_date=" . db_escape($to_date) . ",no_of_days=" . db_escape($no_of_days) . ",upload_file=" . db_escape($upload_file) . ",filesize=" . db_escape($filesize) . ",filetype=" . db_escape($filetype) . ",unique_name=" . db_escape($unique_name) . ",request_date='" .date("Y-m-d") ."'
	WHERE allocate_id = " . db_escape($selected_id);

    db_query($sql, "Can't update empl leave request");
}

function update_allocation_comp($selected_id, $dept_id, $desig_group_id, $desig_id, $employees_id, $type_leave, $reason, $today_date, $from_date, $to_date, $no_of_days, $upload_file, $filesize, $filetype, $unique_name, $request_date,$wrk_time) {
    //display_error($second_to_date);die;
    // $yr = date2sql($year);
    $sql = "UPDATE " . TB_PREF . "kv_comp_request SET dept_id=" . db_escape($dept_id) . ", desig_id=" . db_escape($desig_id) . ",employees_id=" . db_escape($employees_id) . ", type_leave=" . db_escape($type_leave) . ",reason=" . db_escape($reason) . ",today_date=" . db_escape($today_date) . ", from_date=" . db_escape($from_date) . ",to_date=" . db_escape($to_date) . ",no_of_days=" . db_escape($no_of_days) . ",upload_file=" . db_escape($upload_file) . ",filesize=" . db_escape($filesize) . ",filetype=" . db_escape($filetype) . ",unique_name=" . db_escape($unique_name) .
            ",request_date='" .date("Y-m-d") ."' ,working_hours=".db_escape($wrk_time)."
	WHERE allocate_id = " . db_escape($selected_id);

    db_query($sql, "Can't update empl leave request");
}

function update_leave_request($selected_id, $leave_status_type, $no_of_days_approved, $approved_from_date, $approved_to_date, $comments) {

    $sql = "UPDATE " . TB_PREF . "kv_allocation_request SET status=" . db_escape($leave_status_type) . ",no_of_days_approved=" . db_escape($no_of_days_approved) . ",approved_from_date=" . db_escape($approved_from_date) . ",approved_to_date=" . db_escape($approved_to_date) . ",comments=" . db_escape($comments) . "WHERE allocate_id = " . db_escape($selected_id);

    db_query($sql, "Can't update empl leave request");
}

function update_leave_request_comp($selected_id, $leave_status_type, $no_of_days_approved, $approved_from_date, $approved_to_date, $comments) {

    $sql = "UPDATE " . TB_PREF . "kv_comp_request SET status=" . db_escape($leave_status_type) . ",no_of_days_approved=" . db_escape($no_of_days_approved) . ",approved_from_date=" . db_escape($approved_from_date) . ",approved_to_date=" . db_escape($approved_to_date) . ",comments=" . db_escape($comments) . "WHERE allocate_id = " . db_escape($selected_id);

    db_query($sql, "Can't update empl leave request");
}
function add_employee_leave_in_atendance($empl_attend, $empl_id, $month, $year,$dept_id,$no_of_days){
	$dayy = (int)$day;
	$sql = "INSERT INTO ".TB_PREF."kv_empl_attendancee (empl_id, month, year, `{$dayy}`,dept_id,cal_year) VALUES (".db_escape($empl_id).", ".db_escape($month).", ".db_escape($year).", ".db_escape($empl_attend).",".db_escape($dept_id).",'".date("Y")."')";
        db_query($sql, "The employee could not be added");
}

function delete_empl_alloc($selected_id) { // $sql = "DELETE FROM ".TB_PREF."kv_allocation_request WHERE allocate_id=".db_escape($selected_id); 
   $sql = "DELETE FROM " . TB_PREF . "kv_allocation_request WHERE allocate_id=" . db_escape($selected_id);
        $ret = db_query($sql, "Can't delete empl leave request");
        return $ret;
}

function delete_empl_alloc_new($selected_id, $status)
{
 $startDate=strtotime($status['from_date']);
$endDate=strtotime($status['to_date']);
$today=strtotime(date('d-m-Y'));
   if(  $endDate >= $today){
    if ($status['status'] == 2) {
        $sql = "UPDATE " . TB_PREF . "kv_allocation_request SET status=4,cancel_status = 1 WHERE allocate_id=" . db_escape($selected_id);
        $ret = db_query($sql, "Can't delete empl leave request");
    } else {
        $sql = "DELETE FROM " . TB_PREF . "kv_allocation_request WHERE allocate_id=" . db_escape($selected_id);
        $ret = db_query($sql, "Can't delete empl leave request");
    }
    return $ret;
    }
    else
    {
       $sql = "DELETE FROM " . TB_PREF . "kv_allocation_request WHERE allocate_id=" . db_escape($selected_id);
        db_query($sql, "Can't delete empl leave request");
       return 0;
    }
}

/*
  Retrieve full degree data from database for selected type/action/entity or degree
 */

function get_empl_alloc_single($selected_id) {
    
    $sql = "SELECT * FROM " . TB_PREF . "kv_allocation_request WHERE allocate_id=" . db_escape($selected_id);
    // display_error($sql);
    $result = db_query($sql, "could not get empl leave request");
    return db_fetch($result);
}

function get_empl_alloc_single_comp($selected_id) {
    
    $sql = "SELECT * FROM " . TB_PREF . "kv_comp_request WHERE allocate_id=" . db_escape($selected_id);
    // display_error($sql);
    $result = db_query($sql, "could not get empl leave request");
    return db_fetch($result);
}
function get_empl_encash_single($selected_id) {
    
    $sql = "SELECT * FROM " . TB_PREF . "kv_encashment_request WHERE id=" . db_escape($selected_id);
    // display_error($sql);
    $result = db_query($sql, "could not get empl leave request");
    return db_fetch($result);
}

function get_allocation($show_inactive) {

    $sql = "SELECT req.*,des_group.name as grp_name,des.name as desig_name,dept.description as department,type.leave_type as leaves,empl.empl_firstname as employee FROM " . TB_PREF . "kv_allocation_request AS req  LEFT JOIN " . TB_PREF . "kv_desig_group AS des_group ON req.desig_group_id=des_group.id LEFT JOIN " . TB_PREF . "designation_master AS des ON req.desig_id=des.id LEFT JOIN " . TB_PREF . "kv_departments AS dept ON req.dept_id=dept.id LEFT JOIN " . TB_PREF . "kv_type_leave_master AS type ON req.type_leave=type.type_id LEFT JOIN " . TB_PREF . "kv_empl_info AS empl  ON empl.id=req.employees_id";

    if (!$show_inactive)
        $sql .= " AND !req.inactive";
//display_error($sql);
    return db_query($sql, "Can't get empl allocation");
}

function get_employee_leave_records_By_emp_id($employee_id) { 
    $sql = "SELECT * FROM " . TB_PREF . "kv_allocation_request WHERE employees_id='" .$employee_id. "'";
    return db_query($sql, "Can't get employee leave records");
}

function get_employee_leave_records_By_emp_id_comp($employee_id) { 
    $sql = "SELECT * FROM " . TB_PREF . "kv_comp_request WHERE employees_id='" .$employee_id. "'";
    return db_query($sql, "Can't get employee leave records");
}

function get_employee_leave_records_By_emp_id_carry_for($employee_id) { 
    $sql = "SELECT * FROM " . TB_PREF . "carry_forward_leave WHERE empl_id='" .$employee_id. "' AND no_of_el != 0 order by id DESC Limit 1";
  // display_error($sql);
    return db_query($sql, "Can't get employee leave records");
}
function get_employee_leave_records_By_emp_id_carry_for_cl($employee_id) { 
    $sql = "SELECT * FROM " . TB_PREF . "carry_forward_leave WHERE empl_id='" .$employee_id. "' AND no_of_cls != 0 order by id DESC Limit 1";
  //display_error($sql);
    return db_query($sql, "Can't get employee leave records");
}
function get_employee_leave_records_By_emp_id_carry_for_vl($employee_id) { 
    $sql = "SELECT * FROM " . TB_PREF . "carry_forward_leave WHERE empl_id='" .$employee_id. "' AND no_of_pls != 0 order by id DESC Limit 1";
    //display_error($sql);
    return db_query($sql, "Can't get employee leave records");
}
function get_employee_leave_records_By_emp_id_carry_for_ml($employee_id) { 
    $sql = "SELECT * FROM " . TB_PREF . "carry_forward_leave WHERE empl_id='" .$employee_id. "' AND no_of_medical_ls != 0 order by id DESC Limit 1";
   //display_error($sql);
    return db_query($sql, "Can't get employee leave records");
}

function get_employee_leave_records($employee_id, $dept_id, $desig_group_id, $desig_id) {
    $sql = "SELECT * FROM " . TB_PREF . "kv_allocation_request WHERE employees_id='" . $employee_id . "' AND dept_id = '" . $dept_id . "' AND desig_group_id = '" . $desig_group_id . "' AND desig_id = '" . $desig_id . "'";

    return db_query($sql, "Can't get employee leave records");
}

function get_employee_leave_status_records($leave_status, $department_id, $employee_id, $access_id, $from_date, $to_date) {
    $f_date = date2sql($from_date);
    $t_date = date2sql($to_date);
    $sql = "SELECT ar.* FROM " . TB_PREF . "kv_allocation_request ar JOIN fa_kv_empl_job ej ON ar.employees_id = ej.empl_id WHERE ar.status = 4 or ar.status = ".$leave_status." AND ar.request_date  >= '" . $f_date . "' AND ar.request_date <= '" . $t_date . "'";
   // print($sql);
    if (!empty($department_id)) {

        $sql .= " AND ar.dept_id = " . $department_id;
    }

    if (!empty($employee_id)) {
        $sql .= " AND employees_id = '" . $employee_id . "' ";
    }

//Restrict Leave data based on user access id
    global $role_names;
    if (!empty($access_id) && strtolower($access_id) == $role_names['dean']) {//Dean can see only staff leaves
        $sql .= " AND ej.employee_type = 2 ";
    }

//Restrict Leave data based on user access id
    if (!empty($access_id) && strtolower($access_id) == $role_names['director']) {//Directory can see only faculty leaves
        $sql .= " AND ej.employee_type = 1 ";
    }

    return db_query($sql, "Can't get employee leave status records");
}

function get_employee_leave_status_records_comp($leave_status, $department_id, $employee_id, $access_id, $from_date, $to_date) {
    $f_date = date2sql($from_date);
    $t_date = date2sql($to_date);
    $sql = "SELECT ar.* FROM " . TB_PREF . "kv_comp_request ar JOIN fa_kv_empl_job ej ON ar.employees_id = ej.empl_id WHERE ar.status = 4 or ar.status = ".$leave_status." AND ar.request_date  >= '" . $f_date . "' AND ar.request_date <= '" . $t_date . "'";
   // print($sql);
    if (!empty($department_id)) {

        $sql .= " AND ar.dept_id = " . $department_id;
    }

    if (!empty($employee_id)) {
        $sql .= " AND employees_id = '" . $employee_id . "' ";
    }

//Restrict Leave data based on user access id
    global $role_names;
    if (!empty($access_id) && strtolower($access_id) == $role_names['dean']) {//Dean can see only staff leaves
        $sql .= " AND ej.employee_type = 2 ";
    }

//Restrict Leave data based on user access id
    if (!empty($access_id) && strtolower($access_id) == $role_names['director']) {//Directory can see only faculty leaves
        $sql .= " AND ej.employee_type = 1 ";
    }

    return db_query($sql, "Can't get employee leave status records");
}

function get_employee_leave_encashment_status_records($leave_status, $department_id, $employee_id, $access_id, $from_date, $to_date) {
    
    $f_date = date2sql($from_date);
    $t_date = date2sql($to_date);
    $sql = "SELECT ar.* FROM " . TB_PREF . "kv_encashment_request ar JOIN fa_kv_empl_job ej ON ar.empl_id = ej.empl_id WHERE ar.status = 4 or ar.status = ".$leave_status." AND ar.encash_request_date  >= '" . $f_date . "' AND ar.encash_request_date <= '" . $t_date . "' AND is_paid = 0 ";
   
   // print($sql);
    if (!empty($department_id)) {

        $sql .= " AND ej.department = " . $department_id;
    }

    if (!empty($employee_id)) {
        $sql .= " AND empl_id = '" . $employee_id . "' ";
    }

//Restrict Leave data based on user access id
    global $role_names;
    if (!empty($access_id) && strtolower($access_id) == $role_names['dean']) {//Dean can see only staff leaves
        $sql .= " AND ej.employee_type = 2 ";
    }

//Restrict Leave data based on user access id
    if (!empty($access_id) && strtolower($access_id) == $role_names['director']) {//Directory can see only faculty leaves
        $sql .= " AND ej.employee_type = 1 ";
    }

    return db_query($sql, "Can't get employee leave status records");
}

function get_leave_type($type_id) {

    $sql = "SELECT leave_type,code FROM " . TB_PREF . "kv_type_leave_master WHERE type_id = '" . $type_id . "'";

    $res = db_query($sql, "Get Leave Type");

    $result = db_fetch($res);

    return $result;
}

function get_employee_record($empl_id) {

    $sql = "SELECT empl_firstname, (SELECT description FROM " . TB_PREF . "kv_departments WHERE id=(SELECT department FROM " . TB_PREF . "kv_empl_job WHERE empl_id = '" . $empl_id . "') ) as dept_name FROM " . TB_PREF . "kv_empl_info WHERE empl_id = '" . $empl_id . "' ";

    $res = db_query($sql, "Get employee record");

    $result = db_fetch($res);

    return $result;
}

function get_employees($empl_id="",$dept_desc="") {

   $sql = "SELECT empl.empl_id, empl.empl_firstname, dept.description FROM ".TB_PREF."kv_empl_info empl,".TB_PREF."kv_empl_job job, ".TB_PREF."kv_departments dept WHERE ";
   if(!$empl_id && !$dept_desc)
   $sql.="empl.empl_id=job.empl_id AND job.department=dept.id";
   else if($empl_id && $dept_desc)
       $sql.="empl.empl_id like '%$empl_id%' and  job.empl_id like '%$empl_id%' AND job.department=dept.id AND dept.description like"."'%$dept_desc%'";
   else if($empl_id)
        $sql.="empl.empl_id like '%$empl_id%' and  job.empl_id like '%$empl_id%' AND job.department=dept.id";
   else if($dept_desc)
       $sql.="empl.empl_id=job.empl_id AND dept.description like"."'%$dept_desc%' AND job.department=dept.id"; 
   
   $sql.=" group by empl.empl_id,job.department";
   

    return  db_query($sql, "fail to retrieve employees record");

}
function get_occasion_master_by_id($id,$field = 'yes_no'){
    
    $sql  = "SELECT `$field` from fa_kv_occasion_master where id = $id AND !inactive";
    $result = db_query($sql, 'unable to get from occasion_master from page leave_Accural.inc');
    
    $row = db_fetch_row($result);
    return $row[0]; 
}

function leave_encash_reason1($id) {
    $sql = "SELECT occas_encash FROM " . TB_PREF . "kv_leave_encash where id=" . db_escape($id) . " AND !inactive ";
    $result = db_query($sql, "unable to find leave");
    $row = db_fetch_row($result);
    return $row[0];
}

function get_empl_req_date($request_date, $empl_id) {
    $sql = "SELECT * FROM " . TB_PREF . "kv_allocation_request WHERE request_date=" . db_escape($request_date) . " AND employees_id=" . db_escape($empl_id);
    // display_error($sql);
    $result = db_query($sql, "could not get empl leave request");
    return db_num_rows($result);
}

function get_empl_same_fdates($from_date, $to_date, $empl_id, $request_date) {
    $sql = "SELECT * FROM " . TB_PREF . "kv_allocation_request WHERE employees_id=" . db_escape($empl_id) . " AND (from_date >=" . db_escape($from_date) . " AND to_date <=" . db_escape($to_date) . " OR '$from_date' BETWEEN from_date AND to_date OR '$to_date' BETWEEN from_date AND to_date)";

    /* 	$sql = "SELECT * FROM ".TB_PREF."kv_allocation_request WHERE employees_id=".db_escape($empl_id)." AND('$from_date' BETWEEN from_date AND to_date OR '$to_date' BETWEEN from_date AND to_date)";  */

    //display_error($sql);die;
    $result = db_query($sql, "could not get empl leave request");
    return db_num_rows($result);
}

function get_empl_same_tdates($from_date, $to_date, $empl_id, $request_date) {
    $sql = "SELECT * FROM " . TB_PREF . "kv_allocation_request WHERE employees_id=" . db_escape($empl_id) . " AND (from_date >=" . db_escape($from_date) . " AND to_date <=" . db_escape($to_date) . " OR '$from_date' BETWEEN from_date AND to_date OR '$to_date' BETWEEN from_date AND to_date)";

    /* $sql = "SELECT * FROM ".TB_PREF."kv_allocation_request WHERE employees_id=".db_escape($empl_id)." AND('$from_date' BETWEEN from_date AND to_date OR '$to_date' BETWEEN from_date AND to_date)"; */

    //display_error($sql);die;
    $result = db_query($sql, "could not get empl leave request");
    return db_num_rows($result);
}

function get_holiday_check($lfrom_date, $lto_date, $fiscal_year) {
    $sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = " . db_escape($fiscal_year) . " AND inactive = 0 AND (from_date >=" . db_escape($lfrom_date) . " AND to_date <=" . db_escape($lto_date) . " OR '$lfrom_date' BETWEEN from_date AND to_date OR '$lto_date' BETWEEN from_date AND to_date)";
    $result = db_query($sql, "could not get empl leave request");
    return db_num_rows($result);
}

//------------------------leave encashment----------------------//




function update_leave_encash_request_empl($selected_id, $leave_status_type, $no_of_days_approved, $comments,$amount,$no_of_days) {

   // display_error($no_of_days);
    $date = $amount == 0?'0000-00-00':date('Y-m-d');
    
    $sql = "UPDATE " . TB_PREF . "kv_encashment_request SET status=" . db_escape($leave_status_type) . ",no_of_days_approved=" . db_escape($no_of_days_approved) .", approved_date = ".db_escape($date).",approved_amount = ".db_escape($amount).",comments=" . db_escape($comments) ; 
    
    if($amount != 0){
        $sql .=", left_days =".($no_of_days-$no_of_days_approved);
    }
        $sql .= " WHERE id = " . db_escape($selected_id);

    db_query($sql, "Can't update empl leave request");
}


function updateLeaveMaster($empl_id, $no_of_days_approved, $leave_amount,$field,$request_date){
   
    $date = date2sql($reques_date);
    $sql = "update ".TB_PREF."kv_leave_master set $field = ".($leave_amount-$no_of_days_approved)." where empl_id = ". db_escape($empl_id) ;
    db_query($sql,'cannot able to update');
}


function updateLeaveMasterAdd($empl_id, $no_of_days_approved, $leave_amount,$field,$request_date,$leave_type,$leftDays,$selected_id){
        $empl_type = getEmployeeJobInfo1($job_field = 'employee_type',$empl_id);
        $maxAccu = getMaxAccumulation($empl_type, $leave_type);
        $total_left = $maxAccu - $leave_amount;
        $total_approved_days = $no_of_days_approved - $leftDays;
       $approve_days = $total_left >= $total_approved_days?$total_approved_days + $leave_amount:$total_left;
       
            
  //  $date = date2sql($reques_date);
       if($leftDays!=0){
    $sql = "update ".TB_PREF."kv_leave_master set $field = ".$approve_days." where empl_id = ". db_escape($empl_id) ;
    db_query($sql,'cannot able to update');
       }
    $sql = "UPDATE " . TB_PREF . "kv_encashment_request SET left_days= 0 where id = " . db_escape($selected_id);
     db_query($sql,'cannot able to update');
    
}


function getMaxAccumulation($empl_type , $leave_type){
    $sql = "select max_accumulation as max_acu from " .TB_PREF."kv_leave_days where employee_type = $empl_type AND leave_type = $leave_type"; 
    $result = db_query($sql,'cannnot get the value of maxaccumulation');
    $row = db_fetch_row($result);
    return $row[0];
}



function getEmployeeJobInfo1($job_field = '',$empl_id){
    $sql = "select `$job_field`  from ".TB_PREF."kv_empl_job where empl_id  = ".db_escape($empl_id)." LIMIT 1";
    $result = db_query($sql,'could not get the getEmployeeType');
    $row = db_fetch_row($result);
    return $row[0];
}

function getEncashmentCalcBasedOn1($leave_type ='',$leave_amount  = 0,$empl_id,$restriction = false,$encash_days = 0 ){
   
    $sql = "select encash_based,min_bal from ".TB_PREF."kv_leave_encash where employee_type =".getEmployeeJobInfo1($job_field = 'employee_type',$empl_id)." AND leave_type =".$leave_type;

$check = true ;
if($restriction){
    $sql .= " AND max_encash >=".$_POST['no_of_days_approved']." AND min_encash <=".$_POST['no_of_days_approved']." AND min_bal <=".$leave_amount." LIMIT 1";
}
else
{
    if($leave_amount < $encash_days){
        $check = false;
    }
}
   $result = db_query($sql); 
   $row = db_fetch_row($result);
$result = array();
if($check == 1){

   
   $result[0] = $row[0];
   $result[1] = $row[1];
}
 else {
    $result[0] = '';
}
   return $result;
}

function getLeaveMasterValue1($field_name,$empl_id){
        $sql = "select `$field_name`  from ".TB_PREF."kv_leave_master where empl_id  = ".db_escape($empl_id)." LIMIT 1";
    $result = db_query($sql,'could not get the getEmployeeType');
    $row = db_fetch_row($result);
    return $row[0];
}



function leave_master_table_name1($leavetype) {
    switch ($leavetype) {
        case 1:
            return 'no_of_cls';
        case 3:
            return 'no_of_pls';
        case 2:
            return 'no_of_medical_ls';
        case 4:
            return 0;
        case 5:
            return 0;
        case 6:
            return 0;
        case 11:
            return 'no_of_el';
        default:
            return 0;
    }
}

?>