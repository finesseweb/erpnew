<?php
$page_security = 'SA_ATTENDANCELISTING';
if (!@$_GET['popup'])
    $path_to_root = "..";
else
    $path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/attendance/function/function.php");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/modules/ExtendedHRM/includes/ui/leave_acural.inc" );

if (!@$_GET['popup']) {
    $js = "";
    //if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
   // if ($use_date_picker)
        $js .= get_js_date_picker();
    page(("User Attendance List"), @$_GET['popup'], false, "", $js);
}


simple_page_mode(true);
$short_time_arr = array();
$short_hr = '';

if(get_post('empl_id') && $Mode != 'Uploadedit'){
       //  $_POST['type_leave'] =  0;
       /* $_POST['leave_amount'] = 0;
            $_POST['adjust_leave_days'] = 0;
            $_POST['deducted_days'] = 0;
            $_POST['d_remark'] = ''; */
    $_POST['selected_id'] = 1;
    
}




if ($Mode == 'UPDATE_ITEM') {
    $update_pager = false;
    
    if ($Mode == 'UPDATE_ITEM') {
        $check = true;
        if(isset($_POST['type_leave'])){
             $result = getValueFromManualSalary($selected_id);
            $row = db_fetch($result);
             if($row['status'] ==0){
                $data['leave_type'] = $_POST['type_leave'];
               $data['leave_count'] = $_POST['leave_amount'];
               $data['leave_adjusted'] = !empty($_POST['adjust_leave_days'])?$_POST['adjust_leave_days']:'00';
               $data['days_deducted'] = !empty($_POST['deducted_days'])?$_POST['deducted_days']:'00';
               $data['updated_date'] = date('Y-m-d');
               $data['remarks'] = $_POST['d_remark'];
               $data['empl_id'] = $_POST['empl_id'];
               Update('manual_sal_deduction', array('id', $selected_id), $data);
              
               if(!empty($data['leave_adjusted'])){
                   $leave_data[$_POST['leave_field']] = ($_POST['leave_amount']+$row['leave_adjusted']) - $data['leave_adjusted'];
               Update('kv_leave_master',array('empl_id',get_post('empl_id')),$leave_data);
               }
               $check = false;
             }
             else
             {
                 display_error('Salary already processed  couldn\'t update !');
             }
        }
        
        
        $result = '';
        $date = $_POST['a_in_time'];
        $time = $_POST['time_a_in_time'];
        $date1 = $_POST['a_out_time'];
        $time1 = $_POST['time_a_out_time'];

        $in_date_time = date('Y-m-d H:i:s', strtotime("$date $time"));
        $out_date_time = date('Y-m-d H:i:s', strtotime("$date1 $time1"));

        $Wor_In = strtotime("$date $time");
        $Wor_OUT = strtotime("$date1 $time1");
        $working_hours = $Wor_OUT - $Wor_In;
        if ($working_hours < 0) {
            display_error(_('Please check your LOGOUT TIME'));
        } else {
            if(isset($_POST['w_id']) && !empty($_POST['w_id']) && $check){
            update_UserattendanceAdmin($working_hours, $in_date_time, $out_date_time, $_POST['empl_id'], $_POST['w_id']);}
        }

        $_SESSION['login_attendance'] == 'FALSE';


        $Mode = 'RESET';
        $update_pager = true;
      
      display_notification(_('Record has been updated'));
    }
    $Ajax->activate('details');
}

function can_delete($selected_id) {
    if (key_in_foreign_table($selected_id, 'debtors_master', 'credit_status')) {
        display_error(_("Cannot delete this credit status because customer accounts have been created referring to it."));
        return false;
    }

    return true;
}

//-----------------------------------------------------------------------------------

function canprocess() {
     if (strlen($_POST['deducted_days']) == 0 && strlen($_POST['adjust_leave_days']) == 0 ) {
        display_error(_("Days deduct and Leave adjust cannot be empty!"));
        set_focus('deducted_days');
        return false;
    }
    
  if (strlen($_POST['d_remark']) == 0  ) {
        display_error(_("Remarks column should not be empty !"));
        set_focus('d_remark');
        return false;
    }
    
    if (!empty($_POST['deducted_days'])) {

        $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
        $regex2  = "/^\d+\.\d+/";
        if (preg_match($regex, get_post('deducted_days')) != 0 && preg_match($regex2, get_post('deducted_days')) == 0) {
            $input_error = 1;
            display_error(_("Only Numericals are allowed in Days Deduct."));

            set_focus('deducted_days');
            return false;
        }
    }
    if (!empty($_POST['adjust_leave_days'])) {
        $regex2  = "/^\d+\.\d+/";
        $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
        if (preg_match($regex, get_post('adjust_leave_days')) != 0 && preg_match($regex2, get_post('deducted_days')) == 0) {
            $input_error = 1;
            display_error(_("Only Numericals are allowed in Adjust Leave Days."));

            set_focus('adjust_leave_days');
            return false;
        }
        
    }
    
 
    if(isset($_POST['leave_amount']) && ((float)$_POST['adjust_leave_days'] > (float)$_POST['leave_amount']))
    {
        display_error(_("Employee doesn't have sufficent leave !"));
         set_focus('adjust_leave_days');
            return false; 
    }
    
    
    return true;
    
}



if($Mode == 'ADD_ITEM' && canprocess()){
    $data['leave_type'] = $_POST['type_leave'];
    $data['leave_count'] = $_POST['leave_amount'];
    $data['leave_adjusted'] = !empty($_POST['adjust_leave_days'])?$_POST['adjust_leave_days']:'00';
    $data['days_deducted'] = !empty($_POST['deducted_days'])?$_POST['deducted_days']:'00';
    $data['added_date'] = date2sql($_POST['in_time']);
    $data['remarks'] = $_POST['d_remark'];
    $data['empl_id'] = $_POST['empl_id'];
    
   
    $id = getManualSalaryId(get_post('empl_id'), date('Y-m',strtotime(get_post('in_time'))));
    if(empty($id)){
   $last_id =  Insert('manual_sal_deduction', $data);
    if($last_id)
        display_notification ('Added successfull');
      if(!empty($data['leave_adjusted']) && (int)$data['leave_adjusted'] != 0){
                   $leave_data[$_POST['leave_field']] = ($data['leave_count']) - $data['leave_adjusted'];
               Update('kv_leave_master',array('empl_id',get_post('empl_id')),$leave_data);
               }
    }
    else
    {
        display_error('Employee already exists in this month !');
    }
}


if ($Mode == 'Delete') {

    //if (can_delete($selected_id))
    //{
    delete_guest($selected_id);
    //display_notification(_(' '));
    //}
    $Mode = 'RESET';
}

if ($Mode == 'RESET') {
    $selected_id = -1;
   // $sav = get_post('show_inactive');
  //  unset($_POST);
   // $_POST['show_inactive'] = $sav;
}
//-----------------------------------------------------------------------------------
//===[Working]=====//

$ids = '';
if (get_post('Search')) {
    $Ajax->activate('_page_body');
}
//--------------------------------------------------------------------------------------
if (!isset($_POST['filterType']))
    $_POST['filterType'] = -1;
date_default_timezone_set('Asia/Kolkata');
start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
//ref_cells(_("Empl Id:"), 'empl_id', '',null, _('Enter Employee id fragment or leave empty'));
employee_list_cells(_("Select an Employee: "), 'empl_id', null, _('Select'), true, check_value('show_inactive'));
date_row(_("Log In Date") . "", 'in_time');
//date_row(_("Log Out Date") . "", 'out_time');
//ref_cells(_("Working Hours:"), 'working_hours', '',null, _('Enter Email ID fragment or leave empty'));

check_cells(_("Show Monthly Attendance:"), 'monthly_val', null, true);
submit_cells('Search', _("Search"), '', '', 'default');


end_row();

end_table();

/////////////////////////////////////////User Attendance List/////////////////////////////
global $Ajax;
start_form();
if(get_post('type_leave')){
$leave_field_name = leave_master_table_name($_POST['type_leave']);
$_POST['leave_field'] = $leave_field_name;
$leave_amount = getLeaveMasterValue($leave_field_name,get_post('empl_id'));
$_POST['leave_amount'] = $leave_amount;  
}

if($Mode == 'Uploadedit'){
  //display_error();
    if($selected_id != -1){
            //display_error($Mode);
             $result = getValueFromManualSalary($selected_id);
             $row = db_fetch($result);
                   $_POST['type_leave'] =  $row['leave_type'] ;
            // $_POST['leave_amount'] = $row['leave_count'] ;
            $_POST['adjust_leave_days'] = $row['leave_adjusted'];
            $_POST['deducted_days'] = $row['days_deducted'];
            $_POST['d_remark'] = $row['remarks']; 
    } 
      $_POST['selected_id'] = '';
}

if(get_post('empl_id')){
//$selected_id = $_POST['selected_id'];
   //display_error($selected_id);
 
    if($selected_id==-1){
           // $_POST['leave_amount'] = 0;
          $_POST['adjust_leave_days'] = 0;
           $_POST['deducted_days'] = 0;
           $_POST['d_remark'] = ''; 
    }
    
 br(2);
    start_table(TABLESTYLE2,'name = "sal_tbl"');
        
        text_row('Lop Days<br><span style="color:red;font-size:.8em;">(Manual Lop for Employees)<strong></strong></span>', 'deducted_days', $_POST['deducted_days'],'',null,'center');
        leavetype_list_row( 'Leave Type', 'type_leave', false, false,true);
        text_row('Leaves in Account', 'show_amount', $_POST['leave_amount'], 20, '', null, '', '', false, true);
      $Ajax->activate('show_amount');
        hidden('leave_field',$_POST['leave_field']);
        hidden('leave_amount', $_POST['leave_amount']);
        text_row('Adjust Leave No.', 'adjust_leave_days', $_POST['adjust_leave_days'],'',null,'center');
        textarea_row(_("Deduction Remarks:"), 'd_remark',null, 22,4);
   
    end_table();
    
    br();
    submit_add_or_update_center($selected_id == -1, '', 'both');br();

}
function display_rows() {
    if (get_post('empl_id')) {
		totalCompanyHour();
        totalhours(get_post('empl_id'));
        //working_difference(get_post('empl_id'));
    }

    start_table(TABLESTYLE, "width=90%");
    $th1 = array(_("Id"), _("Empl Id"), _("User Name"), _("Log In (Date &amp; Time)"), _("Log Out (Date &amp; Time)"), _("Working Hours"), _("Short Time"));
    table_header($th1, 'style="width:75px;"');

    $sql = kv_get_attendance_list(get_post('empl_id'), get_post('in_time'), get_post('out_time'), get_post('working_hours'), get_post('monthly_val'));
    $sql_query = db_query($sql);
    $i = 1;
    while ($row = db_fetch($sql_query)) {

        start_row();
        label_cell($i++);
        label_cell($row['empl_id']);

        if ($row['real_name']) {
            $real_name = $row['real_name'];
        } else {
            $real_name = $row['user_id'];
        }
        label_cell($real_name);
        if ($row['a_in_time']) {
            $date = date('Y-m-d H:i:s', strtotime($row['a_in_time']));
        }else {
            $date = '---';
        }
        label_cell($date);
        if ($row['a_out_time']) {
            $date1 = date('Y-m-d H:i:s', strtotime($row['a_out_time']));
        } else {
            $date1 = '---';
        }
        label_cell($date1);
     label_cell(working_hours($row));
        label_cell(working_difference($row));
        end_row();
    }

     
	start_row("style='background-color:#e0db98'");
        label_cell('<span style="color:Red; font-size:1.4em; background-color:#e0db98"><strong>Total Short Duration :</strong> </span>','colspan = 6');
	label_cell('<span style="color:Red; font-size:1.4em; background-color:#e0db98"><strong>'.$_POST['short_hr'].'</strong> </span>','colspan = 0');
	end_row();
       
}



//------------------------------This funtion calculate total company hrs and set into post and total short duration ----------------------------//
function totalCompanyHour(){
	
	
	
	//-------------------------------------------this is for total company hors---------------//
		 $sq_w = get_defined_working_hours();
                $val_w = db_query($sq_w);
                $total_w_d = db_fetch_row($val_w);
	 $_POST['company_working_hour'] = $total_w_d[0];
	 //-------------------------------------END ----------------------------------------------
	
	 $sql = kv_get_attendance_list(get_post('empl_id'), get_post('in_time'), get_post('out_time'), get_post('working_hours'), get_post('monthly_val'));
	 

	 
	 $sql_query = db_query($sql);
	  while ($row = db_fetch($sql_query)) {
		   if ($row['a_in_time']) {
		   if (empty($_POST['last_date']))
                $_POST['last_date'] = date('Y-m-d', strtotime($row['a_in_time']));
            $_POST['month_time_arr'][] = date('Y-m-d', strtotime($row['a_in_time']));
		   }
		   working_difference($row);
	  }
	 
	 $total_company_work_hours = count(array_unique($_POST['month_time_arr'])) * $_POST['company_working_hour'];
         
         $total_company_work_hours = convertTime($total_company_work_hours);
         


          

          
   
       //  echo date('h:i:s',$sum3);
         
        /*
         * cal shortage hr
         * cal shortage min
         * cal shortage sec
         * calculating array of timing
         */
         
         
           $total_arr = array();
            foreach($_POST['short_time_arr'] as $key => $value_arr){
                foreach($value_arr as $key => $value){
                    $total_arr[] = $value;    
                }
            }
             $total_arr = array_unique($total_arr);

        $sum = strtotime('00:00:00');
        $sum2 = 0;
        foreach ($total_arr as $v) {

            $sum1 = strtotime($v) - $sum;

            $sum2 = $sum2 + $sum1;
        }

        $sum3 = $sum + $sum2;
        if(get_post('empl_id')){
         $_POST['short_hr'] = date("H:i:s", $sum3);
         $_POST['total_work_hour'] = $total_company_work_hours;
        }
        else{
            $_POST['total_work_hour'] = '';
         $_POST['short_hr'] = '';
        }
}
//-------------------------------------------------------------------------------------------------------------------------------------------------//





/* function display_rows(){
  if(!isset($_POST['fa_user_attendance_page_next']))

  $_SESSION['fun']['id'] = 0;
  $sql = kv_get_attendance_list(get_post('empl_id'),get_post('in_time'),get_post('out_time'), get_post('working_hours'), get_post('monthly_val'));

  $cols = array(
  _("Id") => array('name'=>'id','fun'=>'id'),
  _("Empl Id") => array('name'=>'empl_id', 'fun'=>'empId'),
  _("User Name") => array('name'=>'empl_id', 'fun'=>'realname'),
  _("Log In (Date &amp; Time)") => array('align' => 'center', 'fun' => 'checking'),
  _("Log Out (Date &amp; Time)") => array('align' => 'center', 'fun' => 'checkout'),
  _("Working Hours") => array('align' => 'center', 'fun' => 'working_hours'),
  //_("Edit") => array('align' => 'center', 'fun' => 'edit'),
  //array('')
  );
  $table =& new_db_pager('fa_user_attendance', $sql, $cols);
  $table->width = "90%";
  if(get_post('empl_id')){
  totalhours(get_post('empl_id'));
  }
  //$table->set_marker('check_contractperiod', _("List of employees whoose contract period is going to be completed before one month."));
  display_db_pager($table);
  } */

function id($row) {
    return ++$_SESSION['fun']['id'];
}

function checking($row) {
    if ($row['a_in_time']) {
        $date = date('Y-m-d H:i:s', strtotime($row['a_in_time']));
    } else {
        $date = '---';
    }
    return $date;
}

function checkout($row) {
    if ($row['a_out_time']) {
        $date = date('Y-m-d H:i:s', strtotime($row['a_out_time']));
    } else {
        $date = '---';
    }
    //$date = date('Y-m-d h:i A',strtotime($row['tr_todate']));
    return $date;
}

function empId($row) {
    $empl_id = $row['empl_id'];
    return $empl_id;
}

function working_hours($row) {
    $logout = strtotime($row['a_out_time']);
    $login = strtotime($row['a_in_time']);
   
    $diff = $logout - $login;
    $h_v =  explode(':', timeDiffrence($row['a_out_time'], $row['a_in_time']));
  /* $h = round($diff / 3600, 1, PHP_ROUND_HALF_UP);
    $h_v = explode('.', $h);
    if ($logout) {
        $working_hours = $h_v[0] . " Hours " . round(($diff / 60) % 60) . " Minutes " . round(($diff) % 60) . " Second";
    } else {
        $working_hours = '----';
    }
    return $working_hours;
   * */
    if ($logout) {
        $working_hours = $h_v[0] . " Hours " . $h_v[1] . " Minutes " . $h_v[2]. " Second";
    } else {
        $working_hours = '----';
    }
    return $working_hours;
   
}

function totalhours($empl_id,$shortage_h) {

    $sql1 = get_working_hours($empl_id, get_post('in_time'), get_post('monthly_val'));
    $val_hours = db_query($sql1);
    $total_hours = db_fetch_row($val_hours);
    $total_hours[0] / 3600;
    $h = round($total_hours[0] / 3600, 0, PHP_ROUND_HALF_UP);
    $h_v = explode('.', $h);
    //$userWorkhours= $h." Hours ".round(($h / 60) % 60)." Minutes".round(($h / 60*60) % 60);


    $init = $total_hours[0];

    $hours = floor($init / 3600);
    $minutes = floor(($init / 60) % 60);
    $seconds = $init % 60;

    if ($hours != "") {
        $hour_val = $hours;
    } else {
        $hour_val = '00';
    }
    if ($minutes != "") {
        $minutes_val = $minutes;
    } else {
        $minutes_val = '00';
    }
    if ($seconds != "") {
        $seconds_val = $seconds;
    } else {
        $seconds_val = '00';
    }
    $userWorkhours = "$hour_val Hours : $minutes_val Minutes : $seconds_val Seconds";

    //////////////////////////

    /*  $sq_w=get_defined_working_hours();
      $val_w = db_query($sq_w);
      $total_w_d = db_fetch_row($val_w);
      $today=date('Y-m-d');
      $input_date=date('Y-m-d', strtotime(get_post('in_time')));
      // $final_time=$total_w_d[0]-$total_hours21[0];
      $userWorkhours122 = floor($total_w_d[0] * 3600) - $total_hours[0];
      $init1 =$userWorkhours122;

      $hours1 = floor($init1 / 3600);
      $minutes1 = floor(($init1 / 60) % 60);
      $seconds1 = $init1 % 60;
      if($hours1 !=""){
      $hour_val1=$hours1;
      }else{
      $hour_val1='00';
      }
      if($minutes1 !=""){
      $minutes_val1=$minutes1;
      }else{
      $minutes_val1='00';
      }
      if($seconds1 !=""){
      $seconds_val1=$seconds1;
      }else{
      $seconds_val1='00';
      }


      if($input_date != $today && get_post('monthly_val')==""){
      $userWorkhours12 = "$hour_val1 Hours : $minutes_val1 Minutes : $seconds_val1 Seconds";
      } else {
      $userWorkhours12 = ' ';
      } */
    /////////////////////

    
    $short_hr = $_POST['short_hr'];
    $shortage_hr = '00 Hours : 00 Minutes : 00 Seconds';
     $style = "font-size:.9em; padding:.3em; background:#bbee1a; ";
     $style_full = "font-size:.9em; padding:.3em; background:#bbee1a; ";
       $hr_min_sec = explode(':',$_POST['total_work_hour']);
    if(!empty($short_hr)){
        $shortage_hr_arr = explode(':',$short_hr);
        $hour_val1 = $hour_val;
      
        $hour_val1-=$hr_min_sec[0];
         if($hour_val.':'.$minutes_val.':'.$seconds_val >= $_POST['total_work_hour']){
            
            $hour_min = false;
			if($minutes_val > 0){
                            $min_val1 = $hr_min_sec[1];
                             $min_val1= $min_val1 == 00?60:$min_val1;
			$minutes_val = abs((int)$min_val1 - $minutes_val); 
			$hour_min = true;
			}
		/*if($seconds_val > 0){
			$hour_min = true;
                        $seconds_val1 = $hr_min_sec[2];
                        $seconds_val1= $seconds_val1 == 00?60:$seconds_val1;
			$seconds_val = ((int)$seconds_val1 - (int)$seconds_val);
		}*/
		$hour_val1 = $hour_min == true?abs($hour_val1):abs($hour_val1);
		
        $shortage_hr = "$hour_val1"." Hours : {$minutes_val} Minutes : {$seconds_val} Seconds";
        $text = 'Extra';
        }
        else{
            
			$hour_min = false;
			if($minutes_val > 0){
                            $min_val1 = $hr_min_sec[1];
                             $min_val1 = $min_val1 == 00?60:$min_val1;
			$minutes_val = (int)$min_val1 - $minutes_val; 
			$hour_min = true;
			}
		if($seconds_val > 0){
			$hour_min = true;
                        $seconds_val1 = $hr_min_sec[2];
                        $seconds_val1= $seconds_val1 == 00?60:$seconds_val1;
			$seconds_val = (int)$seconds_val1 - (int)$seconds_val;
		}
		$hour_val1 = $hour_min == true?abs($hour_val1) - 1:abs($hour_val1);
		
        $shortage_hr = $hour_val1." Hours : {$minutes_val} Minutes : {$seconds_val} Seconds";
        $text = 'Short';
         // $shortage_hr = "{$shortage_hr_arr[0]} Hours : {$shortage_hr_arr[1]} Minutes : {$shortage_hr_arr[2]} Seconds";
        }
   //---------------------[To change color or short attendance HOur]---------------------//
          if($hour_val.':'.$minutes_val.':'.$seconds_val < $_POST['total_work_hour']){
            $style = "font-size:.9em; padding:.3em; background:#eeca1d; ";
        }
    }
    echo '<div class="total-hours">'
    . '<span> Total Working Hours: ' . $userWorkhours . '</span>'
    . '<br/><br>';
        echo'<span style="'.$style_full.'">Actual Working Hours: '. $hr_min_sec[0].' Hours :'. $hr_min_sec[1].' Minutes :'.$hr_min_sec[2].' Seconds</span>' ; br(2);

        echo '<span style="'.$style.'"> '.$text.' Attendance Hours: '.$shortage_hr.'</span>';
        if( !empty($short_hr)){
            $id = getManualSalaryId(get_post('empl_id'), date('Y-m',strtotime(get_post('in_time'))));
            if(!empty($id)){
        label_cell(button('Uploadedit'.$id, _("Uploadedit"), '', ICON_EDIT));
            }
            else
            {
                $_POST['selected_id'] = -1;
            }
     }
     
   echo '</div>';
    $_POST['total_workhours']['hr'] = $hour_val;
    $_POST['total_workhours']['min'] = $minutes_val;
    $_POST['total_workhours']['sec'] = $seconds_val;
    return $userWorkhours;
}




function working_difference($row) {
    $sq_w = get_defined_working_hours();
    $val_w = db_query($sq_w);
    $total_w_d = db_fetch_row($val_w);
    $_POST['company_working_hour'] = $total_w_d[0];

    
       $logout = strtotime($row['a_out_time']);
    $login = strtotime($row['a_in_time']);
   
    $diff = $logout - $login;
    $h_v =   timeDiffrence($row['a_out_time'], $row['a_in_time']);
    $tot_work_hr = convertTime($total_w_d[0]);
    if($row['a_in_time'] == '' || $row['a_out_time'] == ''){
        $_POST['short_time_arr'][date('Y-m-d', strtotime($row['a_in_time']))][] = "$tot_work_hr";
        return  "<span class='time_difference'>".$tot_work_hr."</span>";
    }
    else if($tot_work_hr >= $h_v){
        $short_hour = timeDiffrence($h_v, $tot_work_hr);
         $_POST['short_time_arr'][date('Y-m-d', strtotime($row['a_in_time']))][] = "$short_hour";
        return  "<span class='time_difference'>".$short_hour."</span>";
    }      
               
              
    
    //display_error('hello'.$total_w_d);
    //---------------[PREVIOUS FUNCTION]-----------------------//
    $today = date('Y-m-d');
    $input_date = date('Y-m-d', strtotime($row['a_in_time']));
    //-----------------[END]------------------------------------//
    /* $sql21=get_working_hours2($row['empl_id'],get_post('in_time'));
      $val_hours21 = db_query($sql21);
      $total_hours21 = db_fetch_row($val_hours21);
      $final_time=$total_w_d[0]-$total_hours21[0]; */
    //---------------------[PREVIOUS FUNCTION]----------------//
    $t_val1 = floor($total_w_d[0] * 3600);
    $t_val2 = $row['working_hours'];
   
  /*  if ($input_date != $today) {
        if ($t_val2 <= $t_val1) {
            for ($x = $input_date; $x <= $input_date; $x++) {
                $chevk = get_working_hours12($row['empl_id'], $input_date);
                $val_w12 = db_query($chevk);
                $total_w12_d = db_fetch_row($val_w12);
                $userWorkhours122 = floor($total_w_d[0] * 3600) - $total_w12_d[0];
                $init = $userWorkhours122;
                $hours = floor($init / 3600);
                $minutes = floor(($init / 60) % 60);
                $seconds = $init % 60;
                if ($hours != "" && $hours > 0) {
                    $hour_val = $hours;
                }
                if ($minutes != "" && $minutes > 0) {
                    $minutes_val = $minutes;
                }
                if ($seconds != "" && $seconds > 0) {
                    $seconds_val = $seconds;
                }



                if (!empty($minutes_val) || !empty($minutes_val)) {
                    $hour_val = strlen($hour_val) == 0 ? 00 : $hour_val;
                    $minutes_val = strlen($minutes_val) == 0 ? 00 : $minutes_val;
                    $seconds_val = strlen($seconds_val) == 0 ? 00 : $seconds_val;

                    $hour_val = strlen($hour_val) == 1 ? '0' . $hour_val : $hour_val;
                    $minutes_val = strlen($minutes_val) == 1 ? '0' . $minutes_val : $minutes_val;
                    $seconds_val = strlen($seconds_val) == 1 ? '0' . $seconds_val : $seconds_val;

                    $_POST['short_time_arr'][date('Y-m-d', strtotime($row['a_in_time']))] = "$hour_val:$minutes_val:$seconds_val";
                    $userWorkhours = "<span class='time_difference'>$hour_val:$minutes_val:$seconds_val</span>";
                }
            }
        }
    }*/
 //--------------[END]----------------------------------------//




    /* for ($x = $input_date; $x <= $input_date; $x++) {
      $work_h+=$row['working_hours'];
      $chevk=get_working_hours12($row['empl_id'],$input_date);
      $val_w12 = db_query($chevk);
      $total_w12_d = db_fetch_row($val_w12);
      } */


   /* if ($input_date != $today) {
        return $userWorkhours;
    }*/
    
    
    
    //  return  $userWorkhours;
}

function realname($row) {
    if ($row['real_name']) {
        $real_name = $row['real_name'];
    } else {
        $real_name = $row['user_id'];
    }
    return $real_name;
}

function edit($row) {
    return edit_button_cell("Edit" . $row['w_id'], _("Edit"));
}

//----------------------------------------------------------------------------------------


display_rows();

start_table(TABLESTYLE2);
if ($selected_id != -1) {
    if ($Mode == 'Edit') {

        $myrow = kv_get_attendance_list_by_id($selected_id);

        $_POST['empl_id'] = $myrow['empl_id'];
        $_POST['a_in_time'] = $myrow['a_in_time'];
        $_POST['a_out_time'] = $myrow['a_out_time'];

        /* $_POST['a_in_time'] =  date('m/d/y',strtotime($myrow['a_in_time']));
          if($myrow['a_out_time']){
          $_POST['a_out_time'] =  date('m/d/y',strtotime($myrow['a_out_time']));
          }else {
          $_POST['a_out_time'] =  date('m/d/y');
          } */

        table_section_title(_("Update user attendance"));
        text_row(_('Empl ID'), 'empl_id', $_POST['empl_id'], 30);


        datetime_row_a(_("Log In (Date &amp; Time)*") . ":", 'a_in_time', 20, null, '', '', '', null, true, false);
        datetime_row_a(_("Log Out (Date &amp; Time)*") . ":", 'a_out_time', 20, null, '', '', '', null, true, false);
        hidden('w_id', $selected_id);
    }
    hidden('selected_id', $selected_id);


    end_table(1);
   // if($Mode !='Uploadedit' )
    //submit_add_or_update_center($selected_id == -1, '', 'both');
}
end_form();
end_page();
?>

<style>
    .tablestyle_noborder tr {
        float: left;
    }
    .total-hours {
        font-size: 15px;
        margin-right: 30px;
        padding: 12px;
        float: right;
    }
    .time_difference {
        color: red;
        font-size: 14px;
    }
    /*  .tablestyle tr:nth-child(2) span {
          display: block !important;
      }*/
</style>