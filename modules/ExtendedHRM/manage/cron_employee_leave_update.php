<?php
$page_security = 'SA_OPEN';
$path_to_root = "../../..";
$base = dirname(dirname(__FILE__));
require_once $base . '/../../config_db.php';
require_once $base . '/../../config.php';
$cl = $vl = $ml = $el = array();
$error_arra = array();
$second_array = array();

function get_leave_days($showinactive){
    kv_create_leave_days();
    $sql = "select * from fa_kv_leave_days as leaves";
    if ($showinactive != 1)
        $sql .= " where inactive = 0";
    return db_query($sql, "unable to get leave days");
}

function get_leave_days_for_current_year(){

    $sql = "select * from fa_kv_leave_days as leaves where inactive =0 AND cal_year =" . Date("Y");
    return db_query($sql, "unable to get leave days");
}

function get_Active_leave_days($emp_type, $leave_type){
    $sql = "select * from fa_kv_days as leaves where employee_type = '" . $emp_type . "' AND leave_type ='" . $leave_type . "' AND inactive = 0";
    $result = db_query($sql, "unable to get the value from the database");
    return db_fetch($result);
}



function updatekvLeaveDays(){
$sql = "update fa_kv_leave_days set cal_year = ".date('Y');
db_query($sql,"unable to update this kv_leave_days");
}



//auto calculate leave going for chrone
function auto_leave_calculate() {
    $result = get_leave_days_for_current_year();
    while ($myrow = db_fetch($result)) {
        $result2 = data_from_leave_master_and_empl_job($myrow['employee_type']);
        $i = 0;
        while ($myrow2 = db_fetch($result2)) {
            //print_r($myrow2['joining']);exit;
            $updated_date = strtotime($myrow2['updated_date'], strtotime(' + 1 days'));
            $updated_date_ml = strtotime($myrow2['updated_date_ml'], strtotime(' + 1 days'));
            $updated_date_vl = strtotime($myrow2['updated_date_vl'], strtotime(' + 1 days'));
            $updated_date_el = strtotime($myrow2['updated_date_el'], strtotime(' + 1 days'));
            $date_of_joining = strtotime($myrow2['joining']);
            $current_date = strtotime(date('Y-m-d'));
            $total_day_count_since_update = ($current_date - $updated_date) / 86400;
            $total_day_count_since_update_ml = ($current_date - $updated_date_ml) / 86400;
            $total_day_count_since_update_vl = ($current_date - $updated_date_vl) / 86400;
            $total_day_count_since_update_el = ($current_date - $updated_date_el) / 86400;
            $calculated_date = ($current_date - $date_of_joining) / 86400;
            $cl_inc = $vl_inc = $el_inc = $ml_inc = 0;
            //  print_r($total_day_count_since_update."==".$total_day_count_since_update_ml."==".$total_day_count_since_update_vl."==".$total_day_count_since_update_el );exit;
            //======for casual leave

            if ($myrow['leave_type'] == 1) {
                if ($myrow2['acess_cl'] == 0) {
                    if ($total_day_count_since_update >= $myrow['accural_days'] && $myrow2['updated_date'] != '0000-00-00') {
                        $leave_inc = "" . $total_day_count_since_update / $myrow['accural_days'];
                        $arr = explode(".", $leave_inc);
                        // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                        update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0], 'updated_date', $myrow['accural_days'], $i, $myrow, $myrow2);
                    } else if ($total_day_count_since_update >= $myrow['accural_days'] || $myrow2['updated_date'] == '0000-00-00') {
                        if ($calculated_date >= $myrow['accural_days']) {
                            $leave_inc = "" . $calculated_date / $myrow['accural_days'];
                            $arr = explode(".", $leave_inc);
                            // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                            update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0], 'updated_date', $myrow['accural_days'], $i, $myrow, $myrow2);
                            $cl_inc++;
                        }
                    }
                }
            }
            //====for medical leave
            if ($myrow['leave_type'] == 2) {
                if ($myrow2['acess_ml'] == 0) {
                    if ($total_day_count_since_update_ml >= $myrow['accural_days'] && $myrow2['updated_date_ml'] != '0000-00-00') {
                        $leave_inc = "" . $total_day_count_since_update_ml / $myrow['accural_days'];
                        $arr = explode(".", $leave_inc);
                        // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                        update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0], 'updated_date_ml', $myrow['accural_days'], $i, $myrow, $myrow2);
                    } else if ($total_day_count_since_update_ml >= $myrow['accural_days'] || $myrow2['updated_date_ml'] == '0000-00-00') {
                        if ($calculated_date >= $myrow['accural_days']) {
                            $leave_inc = "" . $calculated_date / $myrow['accural_days'];
                            $arr = explode(".", $leave_inc);
                            // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                            update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0], 'updated_date_ml', $myrow['accural_days'], $i, $myrow, $myrow2);
                        }
                    }
                }
            }
            //=======for vacational leave
            if ($myrow['leave_type'] == 3) {
                if ($myrow2['acess_vl'] == 0) {
                    if ($total_day_count_since_update_vl >= $myrow['accural_days'] && $myrow2['updated_date_vl'] != '0000-00-00') {
                        $leave_inc = "" . $total_day_count_since_update_vl / $myrow['accural_days'];
                        $arr = explode(".", $leave_inc);
                        // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                        update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0], 'updated_date_vl', $myrow['accural_days'], $i, $myrow, $myrow2);
                    } else if ($total_day_count_since_update_vl >= $myrow['accural_days'] || $myrow2['updated_date_vl'] == '0000-00-00') {
                        if ($calculated_date >= $myrow['accural_days']) {
                            $leave_inc = "" . $calculated_date / $myrow['accural_days'];
                            $arr = explode(".", $leave_inc);
                            // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                            update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0], 'updated_date_vl', $myrow['accural_days'], $i, $myrow, $myrow2);
                        }
                    }
                }
            }

            //=====for el leaves  
            if ($myrow['leave_type'] == 11) {
                if ($myrow2['acess_el'] == 0) {
                    if ($total_day_count_since_update_el >= $myrow['accural_days'] && $myrow2['updated_date_el'] != '0000-00-00') {
                        $leave_inc = "" . $total_day_count_since_update_el / $myrow['accural_days'];
                        $arr = explode(".", $leave_inc);
                        // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                        update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0], 'updated_date_el', $myrow['accural_days'], $i, $myrow, $myrow2);
                    } else if ($total_day_count_since_update_el >= $myrow['accural_days'] || $myrow2['updated_date_el'] == '0000-00-00') {
                        if ($calculated_date >= $myrow['accural_days']) {
                            $leave_inc = "" . $calculated_date / $myrow['accural_days'];
                            $arr = explode(".", $leave_inc);
                            // echo $myrow2['empl_type']."=======".$myrow2['joining']."========".$calculated_date."====".$arr[0]."<br>";
                            update_kv_leave_master($myrow2['empl_id'], leave_master_table_name($myrow['leave_type']), inc_value($myrow['leave_type']), $arr[0], leave_type($myrow['leave_type'])[0], 'updated_date_el', $myrow['accural_days'], $i, $myrow, $myrow2);
                        }
                    }
                }
            }

            //======end calculating all the leaves 
        }
    }
}

//update leave going for chrone
function update_kv_leave_master($empl_id, $leave_type_db, $inc_days, $inc_multiple, $leave_type, $date_to_update, $accural_days, $i, $myrow, $myrow2) {
    $send_arr = array();
    global $error_arra, $second_array;
    $error_arra[$leave_type] = $empl_id;

    array_push($second_array, [$leave_type => $error_arra[$leave_type]]);

    


    if ($leave_type_db == 0 && $inc_days == 0) {
        display_error($leave_type . " is not allowed in these rules sorry.!");
    } else {
        //==============[--GETTING PREVIOUS LEAVE VALUE OF TYPE-]===============//  
        $previous_value = get_leaves_value($leave_type_db, $empl_id, $date_to_update);
        
              $is_inc = calMaxDays($myrow, $myrow2, '');
              
              //======[-- GET LEAVES FOR ALL THE PREVIOUS LEAVES --]========//
             $cl_pre = get_leaves_value('no_of_cls', $empl_id, $date_to_update);
             $vl_pre = get_leaves_value('no_of_pls', $empl_id, $date_to_update);
             $ml_pre = get_leaves_value('no_of_medical_ls', $empl_id, $date_to_update);
             $el_pre = get_leaves_value('no_of_el', $empl_id, $date_to_update);
              
              
        $is_zero = false;
        if ($is_inc) {
            //========[--THOSE LEAVES WHICH IS NOT ENABLED CARRY FORWARD THAT WILL START FROM (0) --]===========//
            if($myrow['c_f'] != 1){
                $carry_forward_inc = 0;
                $real_inc = 0;
             $cl_pre[1] = $leave_type_db=='no_of_cls'? 0 :$cl_pre[1];
               $vl_pre[1] = $leave_type_db=='no_of_pls'? 0 :$vl_pre[1];
               $ml_pre[1] = $leave_type_db=='no_of_medical_ls'? 0 :$ml_pre[1];
               $el_pre[1] = $leave_type_db=='no_of_el'? 0 :$el_pre[1];
               $is_zero = True;
            }
            else if($myrow['c_f'] == 1){
                        $sql = 'insert into fa_carry_forward_leave(no_of_cls, no_of_pls, no_of_medical_ls, no_of_el,' . $date_to_update . ',updated_date_on, empl_id)values(' . $cl_pre[1] . ','. $vl_pre[1].','.$ml_pre[1].','.$el_pre[1].',"0000-00-00","' . date('Y-m-d') . '","' . $empl_id . '")';
                        db_query($sql, "unable to update leave");
            }
        }
            //==============[--VALUE INCREMNT ACCORDING TO DAYS--]===============//     
        if($is_zero)
        {
            $carry_forward_inc = 0;
            $real_inc = 0;
        }else{
        $real_inc = $previous_value[1] + ($inc_days * (int) $inc_multiple);
        }
      
        $real_inc_virtual = $real_inc;

         // ========= [-- IF EMPLOYEE TOOK LEAVE THEN SUBTRACT VIRTUAL INC--] ======= // 
        // ========== first update kv_leave_day calendar year ======== //
                     
        $leaveCalc = getLeaveCalculated($empl_id);
       
        if($leaveCalc[$leave_type_db]!=0)   
          $real_inc_virtual = $real_inc - (float)$leaveCalc[$leave_type_db];
        
         $carry_forward_inc = $real_inc > $myrow['max_accumulation'] ? $myrow['max_accumulation'] : $real_inc;
        
        //==============[--CHECKING IF VALUE IS GREATER THAN MAXACCUMULATION THAN   TO 0--]===============//
        $real_inc = $real_inc_virtual > $myrow['max_accumulation'] ? $myrow['max_accumulation'] : $real_inc;

        
        
        $date = date_create($previous_value[0]);


        date_add($date, date_interval_create_from_date_string(($accural_days * (int) $inc_multiple) . " days"));
        $updated_date = date_format($date, "Y-m-d");
        
        $updated_date = $previous_value[0] == '0000-00-00' ? date('Y-m-d') : $updated_date;



        $sql = "UPDATE fa_kv_leave_master  SET `" . $leave_type_db . "`=" . $real_inc . ", " . $date_to_update . " = '" . $updated_date . "' where empl_id = '" . $empl_id . "'";
        db_query($sql, "unable to update leave");
        
        //====== [-- EVERY JUNE VACATIONAL LEAVE WILL GOING TO MERG TO EARNED LEAVES --] ========//
        if($myrow['merg_status'] == 1 && date('Y-m-d') == $myrow['merg_date']){
            $data  = array(
                'merg_to' => $leave_type_db,
                'merg_from' =>leave_master_table_name($myrow['merg_to']),
                'date_to_update' => $date_to_update,
                'updated_date' => $updated_date,
                'empl_id' => $empl_id,
                'max_acc' => $myrow['max_accumulation'],
                'real_inc' => $real_inc
                
            );
           $data1 =  mergLeaves($data);
         //  echo "<pre>"; print_r($data1);exit;
           //======[-- FOR EARNED LEAVES  --]===========//
               $sql = "UPDATE fa_kv_leave_master  SET `" . $data['merg_to'] . "`=" . $data1['to'] . ", " . $date_to_update . " = '" . $updated_date . "' where empl_id = '" . $empl_id . "'";
        db_query($sql, "unable to update leave");
 
        //======[-- FOR VACATIONAL LEAVES --]=======//
           $sql = "UPDATE fa_kv_leave_master  SET `" . $data['merg_from'] . "`=" . $data1['from'] . ", " . $date_to_update . " = '" . $updated_date . "' where empl_id = '" . $empl_id . "'";
        db_query($sql, "unable to update leave");
        }
    }
}

function get_employee_whole_attendance($empl_id) {
    $sql = "SELECT * FROM fa_kv_empl_attendancee WHERE  empl_id='" . $empl_id . "' AND cal_year=" . date("Y");

    $attendance = array();
    $ret = db_query($sql, "Can't get empl attendance");

    while ($cont = db_fetch($ret))
        $attendance[] = $cont;
    return $attendance;
}

function get_fiscalyear($id) {
    $sql = "SELECT * FROM fa_fiscal_year WHERE id='" . $id . "'";

    $result = db_query($sql, "could not get fiscal year");

    return db_fetch($result);
}

function get_employee_desig($employee_id) {
    $sql = "SELECT * FROM fa_kv_empl_job WHERE empl_id='" . $employee_id . "'";

    $result = db_query($sql, "could not get employee");

    return db_fetch($result);
}

function get_employee_gender($employee_id) {
    $sql = "SELECT gender FROM fa_kv_empl_info WHERE empl_id='" . $employee_id . "'";

    $result = db_query($sql, "could not get employee");

    return db_fetch($result);
}

function get_employee_leave_count_fiscalyear($year, $empl_id) {

    $sql = "SELECT * FROM fa_kv_empl_attendancee WHERE year='" . $year . "' AND empl_id='" . $empl_id . "' AND cal_year = " . date("Y");

    $result = db_query($sql, "could not get leave count yearly");


    return $result;
}
function get_employee_leave_count_cary_for($year='', $empl_id) {

    $sql = "SELECT * FROM fa_carry_forward_leave WHERE  empl_id='" . $empl_id . "' AND updated_date_on like '".$year."%' AND status = 1";
   // echo $sql;exit;
    $result = db_query($sql, "could not get leave count yearly");

return db_num_rows($result);
    //return $result;
}


//========[-- LEAVE MERG FUNCTION --]============//
function mergLeaves(array $data) {

   $from_previous = get_leaves_value($data['merg_from'], $data['empl_id'], $data['date_to_update']);
    
   $to_previous = get_leaves_value($data['merg_to'], $data['empl_id'], $data['date_to_update']);
   // echo "<pre>";print_r($previous_value_To);exit;
    
   $add_val = $data['real_inc'] > $data['max_acc'] ? $data['max_acc'] : (float) $data['max_acc'] - (float) $data['real_inc'];
    
   $cut_rest_val  = (float)$from_previous[1] - (float)$add_val;

   $data['real_inc'] =(float) $add_val > (float)$from_previous[1] ? (float)$from_previous[1] : (float)$from_previous[1] - (float)$cut_rest_val ;
    
   $data['add_val'] = $add_val;
   $data['cut_rest'] = $cut_rest_val;
   $data['from_previous'] = $from_previous[1];
   $data['to_previous'] = $to_previous[1];
   $data['to'] = (float)$to_previous[1] + (float)$data['real_inc'];
   $data['from'] = (float)$from_previous[1] - (float)$data['real_inc'] ;

    
   return $data;
    
}

//========[-- LEAVE TAKEN BY INDIVIDUAL EMPLOYEES --]============//

function getLeaveCalculated($empl_id) {
    $total_days = 31;
    //s$empl_id = 'EMP-F-002';

    $leaveCalc = array('no_of_cls' => 0, 'no_of_pls' => 0, 'no_of_medical_ls' => 0, 'no_of_el' => 0);
    $selected_empl = get_employee_whole_attendance($empl_id);
    //print_r($selected_empl);

    if (!empty($selected_empl)) {

        foreach ($selected_empl as $single_month) {
            $fiscal_yr = get_fiscalyear($single_month['year']);
            $leave_Day = 0;
            $month_days = date("t", strtotime($single_month['year'] . "-" . $single_month['month'] . "-01"));
            for ($kv = 5; $kv <= $total_days + 4; $kv++) {

                if ($single_month[$kv] == 'A')
                    $leave_Day += 1;
                if ($single_month[$kv] == 'HD')
                    $leave_Day += 0.5;
            }
            $Payable_days = $single_month['year'] - $leave_Day;
        }
    }

    $employee_data = get_employee_desig($empl_id);
    $employee_gender = get_employee_gender($empl_id);

    $em_lv_count_yrly = get_employee_leave_count_fiscalyear($fiscal_yr['id'], $empl_id);
    $empl_casual_leaves_count_yearly = 0;
    $empl_vacation_leaves_count_yearly = 0;
    $empl_medical_leaves_count_yearly = 0;
    $empl_Earned_leaves_count_yearly = 0;
    $empl_special_casual_leaves_count_yearly = 0;
    $empl_maternity_leaves_count_yearly = 0;
    $empl_paternity_leaves_count_yearly = 0;
    $empl_Compensatory_leaves = 0;
    $empl_Compensatory_leaves_present = 0;
    while ($empl_leave_count_yearly = db_fetch($em_lv_count_yrly)) {
        for ($k = 1; $k <= $total_days; $k++) {
            if ($empl_leave_count_yearly[$k] == 'CL') {
                $empl_casual_leaves_count_yearly ++;
            }
            if ($empl_leave_count_yearly[$k] == 'HCL') {
                $empl_casual_leaves_count_yearly += 0.5;
            }
            if ($empl_leave_count_yearly[$k] == 'VL') {
                $empl_vacation_leaves_count_yearly ++;
            }
            if ($empl_leave_count_yearly[$k] == 'ML') {

                $empl_medical_leaves_count_yearly ++;
            }
            if ($empl_leave_count_yearly[$k] == 'EL') {

                $empl_Earned_leaves_count_yearly ++;
            }
            if ($empl_leave_count_yearly[$k] == 'SCL') {

                $empl_special_casual_leaves_count_yearly ++;
            }
            if ($empl_leave_count_yearly[$k] == 'MTL') {
                $empl_maternity_leaves_count_yearly ++;
            }
            if ($empl_leave_count_yearly[$k] == 'PTL') {

                $empl_paternity_leaves_count_yearly ++;
            }
            if ($empl_leave_count_yearly[$k] == 'WO') {
                $empl_Compensatory_leaves ++;
            }
            if ($empl_leave_count_yearly[$k] == 'WOP') {
                $empl_Compensatory_leaves_present ++;
            }
        }
    }
    $leaveCalc['no_of_cls'] = $empl_casual_leaves_count_yearly;
    $leaveCalc['no_of_pls'] = $empl_vacation_leaves_count_yearly;
    $leaveCalc['no_of_medical_ls'] = $empl_medical_leaves_count_yearly;
    $leaveCalc['no_of_el'] = $empl_Earned_leaves_count_yearly;
    return $leaveCalc;
}

//=========[MAX_ACCUMULATION FOR CASUAL LEAVES, MEDICAL LEAVES, VACATIONAL LEAVES, EARNED LEAVES]============//

function calMaxDays($row, $row2, $updated_date) {
    /* ==============[--CHECKING IF MAX DAYS VALUES IS LESS THAN--]================================
      |==============THE DAY DIFFRENCE BETWEEN )01 JAN OF NEXT YEAR THAN CARRY FORWARD STOPS--]==== */
  //  echo $row2['cal_year']."==".date('Y') ; die;
    if ((int) $row2['cal_year'] < (int) date('Y')  ) {
    //===========[--MAX STRETCH DAYS NOT ALLOWED (05-02-2019)--]========//    
    //$days = getDaysDiff(date('Y') . '-01' . '-01', $updated_date);
        //return $row['max_days'] >= $days ? true : false;
      //  echo get_employee_leave_count_cary_for(date('Y'),$row2['empl_id']) ; exit;
        return get_employee_leave_count_cary_for(date('Y'),$row2['empl_id'])>0?false:true;
        
    }
    return false;
}

function getDaysDiff($date1, $date2) {

    date_default_timezone_set('Asia/Kolkata');
    $now = strtotime($date1); // or your date as well

    $your_date = strtotime($date2);
    $datediff = $your_date - $now;
//display_error(round($datediff / (60 * 60 * 24)));
    return abs(round($datediff / (60 * 60 * 24)));
}

function get_leaves_value($leave_type, $empl_id, $leave_date) {
    $sql = "select `" . $leave_date . "`,`" . $leave_type . "` from fa_kv_leave_master where empl_id='" . $empl_id . "'";
    $result = db_query($sql, "unable to update leave");
    return db_fetch($result);
}

//getting table name going for crone
function leave_master_table_name($leavetype) {
    global $leave_field_name;
    return strlen($leave_field_name[$leavetype])>0?$leave_field_name[$leavetype]:0;
}

//get how many day should be adding in leave going for crone 
function inc_value($leave_type) {
    global $leave_inc;
    return strlen($leave_inc[$leave_type])>0?$leave_inc[$leave_type]:0;
}

function data_from_leave_master_and_empl_job($emp_type) {
    $sql = "SELECT * FROM fa_kv_leave_master as leaves,fa_kv_empl_job as job where leaves.empl_id = job.empl_id AND leaves.inactive = 0 AND job.employee_type=" . $emp_type;
    return db_query($sql, "unable to get the file!");
}

function leave_type($id) {
    $sql = "SELECT leave_type FROM fa_kv_type_leave_master where type_id=" . $id . " ";
    $result = db_query($sql, "unable to find leave");
    return db_fetch($result);
}

//db conection 
function db_query($sql, $err_msg = null) {
    global $db_connections;
    $connection = $db_connections[0];
    //echo $connection["dbpassword"];
    $db = mysqli_connect($connection["host"], $connection["dbuser"], $connection["dbpassword"]);
    mysqli_select_db($db,$connection["dbname"]);
    $result = mysqli_query($db,$sql);
    return $result;
}

function db_fetch($result) {

    return mysqli_fetch_array($result,MYSQLI_ASSOC);
}

function db_num_rows($result) {
    return mysqli_num_rows($result);
}
updatekvLeaveDays();
auto_leave_calculate();
$cl['Casual Leave'] = $vl['Vacational Leave'] = $el['Earned Leave'] = $ml['Medical Leave'] = 'No updates';
foreach ($second_array as $key => $value) {

    foreach ($value as $key1 => $value1) {
        if ($key1 == 'Casual Leave') {
            if ($cl['Casual Leave'] == 'No updates')
                $cl['Casual Leave'] = '';
            $cl['Casual Leave'] .= $value1 . ',';
        }
        if ($key1 == 'Medical Leave') {
            if ($ml['Medical Leave'] == 'No updates')
                $ml['Medical Leave'] = '';
            $ml['Medical Leave'] .= $value1 . ',';
        }
        if ($key1 == 'Vacation Leave') {
            if ($vl['Vacational Leave'] == 'No updates')
                $vl['Vacational Leave'] = '';
            $vl['Vacational Leave'] .= $value1 . ',';
        }
        if ($key1 == 'Earned Leave') {
            if ($el['Earned Leave'] == 'No updates')
                $el['Earned Leave'] = '';
            $el['Earned Leave'] .= $value1 . ',';
        }
    }
}

$to = "ash0a130@gmail,omprakashpmp@gmail.com";
$subject = "Leave Cron";
$txt = '1. Casual Leave Employees: [' . $cl['Casual Leave'] . "]" . "\r\n";
$txt .= '2. Vacational Leave Employees: [' . $vl['Vacational Leave'] . "]" . "\r\n";
$txt .= '3. Medical Leave Employees: [' . $ml['Medical Leave'] . "]" . "\r\n";
$txt .= '4. Earned Leave Employees: [' . $el['Earned Leave'] . "]" . "\r\n";
$headers = "From: no-reply@finessewebtech.com" . "\r\n";

mail($to, $subject, $html, $headers);




