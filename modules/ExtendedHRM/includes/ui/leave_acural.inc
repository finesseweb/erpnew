<?php
function kv_create_leave_days() {

    $sql = "CREATE TABLE IF NOT EXISTS `fa_kv_leave_days` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `employee_type` tinyint(1) NOT NULL DEFAULT '0',
  `leave_type` tinyint (1) NOT NULL DEFAULT '0',
  `accural_days` smallint(6) unsigned,
  `weekend_status` tinyint(1) NOT NULL DEFAULT '0',
  `max_accumulation` smallint(6) unsigned,
  `avail_leaves` smallint(6) unsigned ,
  `max_days` smallint(6) unsigned, 
  `min_days` smallint(6) unsigned,
  `max_times_in_cal_year` int(10) unsigned,
  `max_encash` smallint(6) unsigned,
  `min_encash` smallint(6) unsigned,
  `cal_year` int(10) unsigned,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

    return db_query($sql, "Db Table creation failed, Kv Manufacturer table");
}

function get_leave_days($showinactive) {
    kv_create_leave_days();
    $sql = "select * from fa_kv_leave_days as leaves";
    if ($showinactive != 1)
        $sql .= " where inactive = 0";
    return db_query($sql, "unable to get leave days");
}
function get_leave_encash($showinactive) {
    $sql = "select * from fa_kv_leave_encash";
    if ($showinactive != 1)
        $sql .= " where inactive = 0";
    return db_query($sql, "unable to get leave days");
}

/*function get_occasion_master_by_id($id,$field = 'yes_no'){
    
    $sql  = "SELECT `$field` from fa_kv_occasion_master where id = $id AND !inactive";
    $result = db_query($sql, 'unable to get from occasion_master from page leave_Accural.inc');
    
    $row = db_fetch_row($result);
    return $row[0]; 
}*/


function get_leave_encash_request($showinactive) {
    $sql = "select * from fa_kv_encashment_request where is_paid = 0 ";
    if(!empty($_SESSION['wa_current_user']->empl_id))
        $sql .= " AND empl_id = ". db_escape($_SESSION['wa_current_user']->empl_id);
    if ($showinactive != 1)
        $sql .= " AND inactive = 0";
    return db_query($sql, "unable to get leave days");
}


function get_occasion_master($showinactive) {
    $sql = "select * from fa_kv_occasion_master";
    if ($showinactive != 1)
        $sql .= " where inactive = 0";
    return db_query($sql, "unable to get occasion master");
}


function get_leave_days_for_current_year(){
        kv_create_leave_days();
    $sql = "select * from fa_kv_leave_days as leaves where inactive =0 AND cal_year =".Date("Y");
    return db_query($sql, "unable to get leave days");   
}
function get_Active_leave_days($emp_type, $leave_type) {
    $sql = "select * from fa_kv_days as leaves where employee_type = '" . $emp_type . "' AND leave_type ='" . $leave_type . "' AND inactive = 0";
    $result = db_query($sql, "unable to get the value from the database");
    return db_fetch($result);
}

function add_leave_days($employee_type, $leave_type, $accural_days, $weekend_status, $max_accumulation, $avail_leaves, $max_days, $min_days, $max_times_in_cal_year, $max_encash, $min_encash, $cal_year,$cf,$merg_status,$merg_to,$merg_date) {
    if($merg_date)
    $merg_date = date2sql($merg_date);
    else
        $merg_date = '0000-00-00';
    $record_no = checking_if_leave_exist($employee_type, $leave_type, $cal_year);
    if ($record_no == 0) {
        $sql = "INSERT INTO `fa_kv_leave_days` (`employee_type`, `leave_type`, `accural_days`, `weekend_status`, `max_accumulation`, `avail_leaves`, `max_days`, `min_days`, `max_times_in_cal_year`, `max_encash`, `min_encash`, `cal_year`,`c_f`,`merg_status`,`merg_to`,`merg_date`) VALUES (" . db_escape($employee_type) . "," . db_escape($leave_type) . "," . db_escape($accural_days) . "," . db_escape($weekend_status) . "," . db_escape($max_accumulation) . "," . db_escape($avail_leaves) . "," . db_escape($max_days) . "," . db_escape($min_days) . "," . db_escape($max_times_in_cal_year) . "," . db_escape($max_encash) . "," . db_escape($min_encash) . "," . db_escape($cal_year) .  "," . db_escape($cf) . "," . db_escape($merg_status) . "," . db_escape($merg_to) . "," . db_escape($merg_date) . ")";
        return db_query($sql, "unable to add leave days");
    } else
        display_error("sorry you cannot add value again in the same calendar year");
}


/*
 * Add for leave encashment
 * master 22-04-2019
 * 
 * 
 */


function add_leave_encash($employee_type, $leave_type, $occas_encash, $permitted_days, $max_encash, $min_encash, $min_bal,$ecash_status,$encash_based) {
 
        $sql = "INSERT INTO `fa_kv_leave_encash` (`employee_type`, `leave_type`, `occas_encash`, `freq`,`max_encash`, `min_encash`, `min_bal`,`encash_based`,`cal_year`) VALUES (" . db_escape($employee_type) . "," . db_escape($leave_type) . "," . db_escape($occas_encash) . "," . db_escape($permitted_days) . "," . db_escape($max_encash) . "," . db_escape($min_encash) . "," . db_escape($min_bal)  ."," . db_escape(implode(",",$encash_based)) .",".date('Y').")";
        return db_query($sql, "unable to add leave days");
   
}
 
    
function add_leave_encash_request($request_id, $leave_type, $reason, $encash_days, $encash_amt, $encash_request_date, $remarks) {
 $encash_request_date = date2sql($encash_request_date);
        $sql = "INSERT INTO `fa_kv_encashment_request` (`request_id`, `empl_id`,`leave_type`, `reason`, `encash_days`,`encash_amt`, `encash_request_date`, `remarks`,`cal_year`) VALUES (" . db_escape($request_id)."," . db_escape($_SESSION['wa_current_user']->empl_id) . "," . db_escape($leave_type) . "," . db_escape($reason) . "," . db_escape($encash_days) . "," . db_escape($encash_amt) . "," . db_escape($encash_request_date) . "," . db_escape($remarks)  .",".date('Y').")";
        return db_query($sql, "unable to add leave days");
   
}

function add_occasion_master($occ_name, $yes_no) {
        $sql = "INSERT INTO `fa_kv_occasion_master` (`occ_name`,`yes_no`)VALUES (" . db_escape($occ_name)."," . db_escape($yes_no) .")";
        return db_query($sql, "unable to add into occasion master");
}

function update_occasion_master($id, $occ_name, $yes_no){
   $sql = "update `fa_kv_occasion_master`  set `occ_name` = ".db_escape($occ_name)." , `yes_no` = $yes_no where id = $id";
   return db_query($sql,"unable to update fa_kv_ocassion_master from leave_accural.inc");
}


function isLeaveAlreadyCreated($empl_type,$leave_type,$selected_id = ''){
    
    $sql = "select * from `fa_kv_leave_days` where employee_type =  $empl_type AND leave_type = $leave_type";
    if(!empty($selected_id))
            $sql .= " AND id != $selected_id";
    $result = db_query($sql,"unable to fetch result");
    return db_num_rows($result);
    
}
function isLeaveEncashAlreadyCreated($empl_type,$leave_type,$selected_id = '',$occ_name = ''){
    
    $sql = "select * from `fa_kv_leave_encash` where employee_type =  $empl_type AND leave_type = $leave_type AND occas_encash = $occ_name";
    if(!empty($selected_id))
            $sql .= " AND id != $selected_id";
    $result = db_query($sql,"unable to fetch result");
    return db_num_rows($result);
    
}

function checking_if_leave_exist($empl_type, $leave_type, $cal_year) {
    $sql = "select * from fa_kv_leave_days  where employee_type=" . db_escape($empl_type) . " AND leave_type =" . db_escape($leave_type) . " AND cal_year=" . db_escape($cal_year);
    $result = db_query($sql, "unable to fetch file");
    return db_num_rows($result);
}

/*
 * update for leave 
 * encshment 22-04-2019
 * 
 */

function update_leave_encash($employee_type, $leave_type, $occas_encash, $freq, $max_encash, $min_encash, $min_bal, $id , $ecash_status,$encash_based) {
 
    $sql = "UPDATE `fa_kv_leave_encash` SET `employee_type`=".db_escape($employee_type).",`leave_type`=".$leave_type.",`occas_encash`=" . db_escape($occas_encash) . ",`freq`=" . db_escape($freq) .  ",`max_encash`=" . db_escape($max_encash) .",`min_encash`=" . db_escape($min_encash) .",`min_bal`=" . db_escape($min_bal)  .",`encash_based`=" . db_escape(implode(',',$encash_based))  ." where id =" . db_escape($id);
   // display_error($sql);
    return db_query($sql, "unable to update this  file!");
}




function update_leave_encash_request($id,$request_id, $leave_type, $reason, $encash_days, $encash_amt, $encash_request_date, $remarks) {
 $encash_request_date = date2sql($encash_request_date);
    $sql = "UPDATE `fa_kv_encashment_request` SET `encash_days`=".db_escape($encash_days).", `leave_type`=".db_escape($leave_type).",`reason`=".$reason.",`encash_amt`=" . db_escape($encash_amt) . ",`remarks`=" . db_escape($remarks)  ." where id =" . db_escape($id);
    return db_query($sql, "unable to update this  file!");
}
function update_leave_encash_request_status($id,$status) {
 $encash_request_date = date2sql($encash_request_date);
    $sql = "UPDATE `fa_kv_encashment_request` SET `status`=".db_escape($status)." where id =" . db_escape($id)."  AND status != 5";
db_query($sql, "unable to update this  file!");
   
   $sql = "select status from fa_kv_encashment_request  where id  = $id";
   $result = db_query($sql,'cannot get leave encashment request');
   $row = db_fetch_row($result);
   return $row[0];
   
}



function update_leave_days($employee_type, $leave_type, $accural_days, $weekend_status, $max_accumulation, $avail_leaves, $max_days, $min_days, $max_times_in_cal_year, $max_encash, $min_encash, $cal_year, $id,$cf,$merg_status,$merg_to,$merg_date) {
    if($merg_date)
    $merg_date = date2sql($merg_date);
    else
        $merg_date = '0000-00-00';
    $sql = "UPDATE `fa_kv_leave_days` SET `employee_type`=".db_escape($employee_type).", `accural_days`=" . db_escape($accural_days) . ",`leave_type`=".$leave_type.",`weekend_status`=" . db_escape($weekend_status) . ",`max_accumulation`=" . db_escape($max_accumulation) . ",`avail_leaves`=" . db_escape($avail_leaves) . ",`max_days`=" . db_escape($max_days) . ",`min_days`=" . db_escape($min_days) . ",`max_times_in_cal_year`=" . db_escape($max_times_in_cal_year) . ",`max_encash`=" . db_escape($max_encash) . ",`min_encash`=" . db_escape($min_encash) . ",`cal_year`=" . db_escape($cal_year). ",`c_f`=" . db_escape($cf).",`merg_status`=" . db_escape($merg_status). ",`merg_to`=" . db_escape($merg_to).",`merg_date`=" . db_escape($merg_date)." where id =" . db_escape($id);
    //display_error($sql);
    return db_query($sql, "unable to update this  file!");
}

function delete_leave_days($selected_id) {
    $sql = "delete from fa_kv_leave_days where id = " . db_escape($selected_id);
    return db_query($sql, "unable to delete this  file!");
}

function leaves_employee_type($id) {
    for ($i = 1; $i <= count(list_of_employee_types()); $i++) {
        if ($i == $id)
            return list_of_employee_types()[$i];
    }
}

function get_leave_days_edit($id) {
    $sql = "SELECT * FROM fa_kv_leave_days where id=" . db_escape($id);
    $result = db_query($sql, "unable to get value for edit");
    return db_fetch($result);
}
function get_leave_encash_edit($id) {
    $sql = "SELECT * FROM fa_kv_leave_encash where id=" . db_escape($id);
    $result = db_query($sql, "unable to get value for edit");
    return db_fetch($result);
}
function get_leave_encash_freq($leave_type) {
    $sql = "SELECT `freq` FROM fa_kv_leave_encash where leave_type=" . db_escape($leave_type)." AND cal_year =".db_escape(date('Y'));
    $result = db_query($sql, "unable to get value for edit");
    $row = db_fetch_row($result);
    return $row[0];
}  
function get_leave_encash_request_edit($id) {
    $sql = "SELECT * FROM fa_kv_encashment_request where id=" . db_escape($id);
    $result = db_query($sql, "unable to get value for edit");
    return db_fetch($result);
}
function get_occasion_master_edit($id) {
    $sql = "SELECT * FROM fa_kv_occasion_master where id=" . db_escape($id);
    $result = db_query($sql, "unable to get value for edit");
    return db_fetch($result);
}
function get_leave_encash_request_freq($leave_type) {
    $sql = "SELECT * FROM fa_kv_encashment_request where leave_type = ".db_escape($leave_type)." AND  empl_id=" . db_escape($_SESSION['wa_current_user']->empl_id)."  AND cal_year = ".db_escape(date('Y'))." AND status  = 2 ";
    $result = db_query($sql, "unable to get value for edit");
    return db_num_rows($result);
}

/*
 * Special function 
 * use to generate NEXT MAX ID
 * MAKE IT GLOBAL AND USE THIS FUNCTION
 * ON TABLE NAME ,FIELNAME, $PATTERN
 * THANKS
 * ASHUTOSH
 * 
 * for more info please contact https://ashutoshsharma.com
 */
//====START NEXT ID
function nextId($table_name, $field_name,$pattern){
    
          $pat_connect = str_replace(array('%',"'"), '', $pattern);
          $sql = "select max($field_name), case "
            . "when $field_name IS NULL THEN " .db_escape($pat_connect. 1)
            . "ELSE max($field_name) END "
            . "as next_id from ".TB_PREF."kv_encashment_request where $field_name like $pattern "; 

    $result = db_query($sql,'could not get max request id');
     $row = db_fetch_row($result);
    $nextId = fetchNewId($row[0],$pat_connect);
    return $nextId;
}



function fetchNewId($max_id,$pattern){
    $num_container = '';
    $pat_container = '';
    for($i = 0; $i < strlen($max_id); $i++){
        if(is_numeric($max_id[$i])){
            $num_container .= $max_id[$i];
        }
    }
    $num_container = $num_container == 0?0:$num_container;
    return $pattern.((int)$num_container + 1);
    
}//===END NEXT ID

/*
 * funtion to join leave encashment master
 * with user encashment request 
 * to get encashment calculate
 * base upon which is save in kv_encashment
 * 
 */


function getEncashmentCalcBasedOn($leave_type ='',$leave_amount  = 0,$restriction = false,$encash_days = 0){
   
$sql = "select encash_based,min_bal from ".TB_PREF."kv_leave_encash where employee_type =".getEmployeeJobInfo()." AND leave_type =".$leave_type ;
$check = true ;
if($restriction){
    $sql .= " AND max_encash >=".$_POST['encashment_days']." AND min_encash <=".$_POST['encashment_days']." AND min_bal <=".$leave_amount." LIMIT 1";
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

function getEmployeeJobInfo($job_field = 'employee_type',$empl_id = ''){
    $empl_id = !empty($empl_id)?$empl_id:$_SESSION['wa_current_user']->empl_id;
    $sql = "select `$job_field`  from ".TB_PREF."kv_empl_job where empl_id  = ".db_escape($empl_id)." LIMIT 1";
    $result = db_query($sql,'could not get the getEmployeeType');
    $row = db_fetch_row($result);
    return $row[0];
}


function getLeaveMasterValue($field_name,$empl_id  = ''){
   $empl_id = !empty($empl_id)?$empl_id:$_SESSION['wa_current_user']->empl_id;
        $sql = "select `$field_name`  from ".TB_PREF."kv_leave_master where empl_id  = ".db_escape($empl_id)." LIMIT 1";
    $result = db_query($sql,'could not get the getEmployeeType');
    $row = db_fetch_row($result);
    return $row[0];
}


function getManualSalaryId($empl_id,$month){
        $sql = "select id from ".TB_PREF."manual_sal_deduction where empl_id  = ".db_escape($empl_id)." AND added_date like '$month%' LIMIT 1";
    $result = db_query($sql,'could not get the getEmployeeType');
    $row = db_fetch($result);
    return $row[id];  
}

function getValueFromManualSalary($empl_id){
    $sql = "select *  from ".TB_PREF."manual_sal_deduction where id  = ".db_escape($empl_id)." LIMIT 1";
    
    $result = db_query($sql,'could not get the getEmployeeType');
    return $result;
}



//auto calculate leave going for chrone
function auto_leave_calculate() {
    $result = get_leave_days_for_current_year();
    while ($myrow = db_fetch($result)) {
        $result2 = data_from_leave_master_and_empl_job($myrow['employee_type']);
        while ($myrow2 = db_fetch($result2)) {
            $updated_date = strtotime($myrow2['updated_date']);
            $updated_date_ml = strtotime($myrow2['updated_date_ml']);
            $updated_date_vl = strtotime($myrow2['updated_date_vl']);
            $updated_date_el = strtotime($myrow2['updated_date_el']);
            $date_of_joining = strtotime($myrow2['joining']);
            
            $current_date = strtotime(date("Y-m-d"));
            $total_day_count_since_update = ($current_date - $updated_date) / 86400;
            $total_day_count_since_update_ml = ($current_date - $updated_date_ml) / 86400;
            $total_day_count_since_update_vl = ($current_date - $updated_date_vl) / 86400;
            $total_day_count_since_update_el = ($current_date - $updated_date_el) / 86400;
            $calculated_date = ($current_date - $date_of_joining) / 86400;
            //======for casual leave
            
          if($myrow['leave_type']==1){  
            if ($total_day_count_since_update >= $myrow['accural_days'] && $myrow2['updated_date'] != '0000-00-00'){
                $leave_inc = "" . $total_day_count_since_update / $myrow['accural_days'];
                $arr = explode(".", $leave_inc);
                // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0],'updated_date');
            } else if ($total_day_count_since_update >= $myrow['accural_days'] || $myrow2['updated_date'] == '0000-00-00') {
                if ($calculated_date >= $myrow['accural_days']) {
                    $leave_inc = "" . $calculated_date / $myrow['accural_days'];
                    $arr = explode(".", $leave_inc);
                    // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                    update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0],'updated_date');
                }
            }
          }
            //====for medical leave
          if($myrow['leave_type']==2){
                 if ($total_day_count_since_update_ml >= $myrow['accural_days'] && $myrow2['updated_date_ml'] != '0000-00-00'){
                $leave_inc = "" . $total_day_count_since_update_ml / $myrow['accural_days'];
                $arr = explode(".", $leave_inc);
                // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0], 'updated_date_ml');
            } else if ($total_day_count_since_update_ml >= $myrow['accural_days'] || $myrow2['updated_date_ml'] == '0000-00-00') {
                if ($calculated_date >= $myrow['accural_days']) {
                    $leave_inc = "" . $calculated_date / $myrow['accural_days'];
                    $arr = explode(".", $leave_inc);
                    // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                    update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0],'updated_date_ml');
                }
            }
          }
            //=======for vacational leave
          if($myrow['leave_type']==3){
                   if ($total_day_count_since_update_vl >= $myrow['accural_days'] && $myrow2['updated_date_vl'] != '0000-00-00'){
                $leave_inc = "" . $total_day_count_since_update_vl / $myrow['accural_days'];
                $arr = explode(".", $leave_inc);
                // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0],'updated_date_vl');
            } else if ($total_day_count_since_update >= $myrow['accural_days'] || $myrow2['updated_date_vl'] == '0000-00-00') {
                if ($calculated_date >= $myrow['accural_days']) {
                    $leave_inc = "" . $calculated_date / $myrow['accural_days'];
                    $arr = explode(".", $leave_inc);
                    // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                    update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0],'updated_date_vl');
                }
            }
          }
      
          //=====for el leaves  
             if($myrow['leave_type']==10){
                      if ($total_day_count_since_update_el >= $myrow['accural_days'] && $myrow2['updated_date_el'] != '0000-00-00'){
                $leave_inc = "" . $total_day_count_since_update_el / $myrow['accural_days'];
                $arr = explode(".", $leave_inc);
                // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0],'updated_date_el');
            } else if ($total_day_count_since_update >= $myrow['accural_days'] || $myrow2['updated_date_el'] == '0000-00-00') {
                if ($calculated_date >= $myrow['accural_days']) {
                    $leave_inc = "" . $calculated_date / $myrow['accural_days'];
                    $arr = explode(".", $leave_inc);
                    // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                    update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0],'updated_date_el');
                }
            }
          } 
          
           //======end calculating all the leaves 
        }
    }
}

//update leave going for chrone
function update_kv_leave_master($empl_id, $leave_type_db, $inc_days, $inc_multiple, $leave_type,$date_to_update) {
    
    
    if ($leave_type_db == 0 && $inc_days == 0) {
        display_error($leave_type . " is not allowed in these rules sorry.!");
    } else {
        $previous_value = get_leaves_value($leave_type_db, $empl_id)[0];
        $real_inc = $previous_value + ($inc_days * (int) $inc_multiple);
        $sql = "UPDATE fa_kv_leave_master  SET `" . $leave_type_db . "`=" . $real_inc . ", ".$date_to_update." = '" . Date("Y-m-d") . "' where empl_id = '" . $empl_id . "'";

        db_query($sql, "unable to update leave");
    }
}

function get_leaves_value($leave_type, $empl_id) {
    $sql = "select `" . $leave_type . "` from fa_kv_leave_master where empl_id='" . $empl_id . "'";
    $result = db_query($sql, "unable to update leave");
    return db_fetch($result);
}

//getting table name going for crone
function leave_master_table_name($leavetype) {
    
  global $leave_field_name;
  return strlen($leave_field_name[$leavetype])>1?$leave_field_name[$leavetype]:0;
}

//get how many day should be adding in leave going for crone 
function inc_value($leave_type) {
    global $leave_inc;
    return $leave_inc[$leave_type]>0?$leave_inc[$leave_type]:0;
}

function data_from_leave_master_and_empl_job($emp_type) {
    $sql = "SELECT * FROM " . TB_PREF . "kv_leave_master as leaves," . TB_PREF . "kv_empl_job as job where leaves.empl_id = job.empl_id AND leaves.inactive = 0 AND job.employee_type=" . db_escape($emp_type);
    return db_query($sql, "unable to get the file!");
}

function leave_type($id) {
    $sql = "SELECT leave_type FROM " . TB_PREF . "kv_type_leave_master where type_id=" . db_escape($id) . " ";
    $result = db_query($sql, "unable to find leave");
    return db_fetch($result);
}
function leave_encash_reason($id) {
    $sql = "SELECT occas_encash FROM " . TB_PREF . "kv_leave_encash where id=" . db_escape($id) . " AND !inactive ";
    $result = db_query($sql, "unable to find leave");
    $row = db_fetch_row($result);
    return $row[0];
}
function leave_allowance($id) {
    $sql = "SELECT description FROM " . TB_PREF . "kv_allowances where id=" . db_escape($id) . " ";
    $result = db_query($sql, "unable to find leave");
    return db_fetch($result);
}

function list_of_employee_types() {
   global $kv_empl_type;
   return $kv_empl_type;
   
}

function bool($id) {
    if ($id == 0) {
        return 'YES';
    } else {
        return 'NO';
    }
    
}
