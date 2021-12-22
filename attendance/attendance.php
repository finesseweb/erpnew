<?php
$page_security = 'SA_ATTENDANCE';
if (!@$_GET['popup'])
	$path_to_root = "..";
else	
	$path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/attendance/function/function.php");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );


if (!@$_GET['popup'])
	page(_($help_context = "Attendance"));

simple_page_mode(true);
?>
<body oncontextmenu="return false">
<?php      
function can_process() {
    if (strlen($_POST['password']) == 0) {
		display_error(_("password can not be empty."));
		set_focus('password');
		return false;
	}
	if (strlen($_POST['password']) < 0) {
		display_error(_("Password can not be empty."));
		set_focus('password');
		return false;
	}
    return true;
}

//-----------------------------------------------------------------------------------
date_default_timezone_set('Asia/Kolkata');
if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') {
    $empl_id=$_SESSION['wa_current_user']->empl_id;
      $emplDetails = db_fetch_assoc(getDepartment($empl_id));
       $company_year = get_company_pref('f_year');
       $fiscal_year = get_fiscalyear($company_year);
	if(can_process()){
		$update_pager = false;
                $result = get_weekly_off();
                $woarr = array();
                    while ($weekly_off = db_fetch_assoc($result)) {
                    $woarr[$weekly_off['option_name']] = $weekly_off['option_value'];
                    }
		if ($Mode == 'ADD_ITEM') {
			$result = '';
                        $password= md5($_POST['password']);
                         $current_date1=date('Y-m-d H:i:s');
                         $check_in = explode(' ',  $current_date);
                         $current_date=date('Y-m-d');
                               // if($current_date != $check_in[0]){
                                    update_attendance($_POST['username'], $password, $current_date1);
                                    $att_sql123=fetchInTimeUserTable($empl_id); 
                                    $val_attlist123 = db_query($att_sql123);
                                    $attendanceResult123 = db_fetch_row($val_attlist123);
                                    
                                    if($attendanceResult123[0]){
                                        add_userAttendance($current_date1,$empl_id );
                                        $attendance_date = strtotime($current_date1);
                                              $month = date("m", $attendance_date);
                                               $day = date("d", $attendance_date);
                                               $weekday = date('D',$attendance_date);
                                               $year = get_fiscal_year_id_from_date(date("Y-m-d",$attendance_date));
                                           $holiday_date =  get_holiday_check(date("Y-m-d",$attendance_date), date("Y-m-d",$attendance_date), $fisical['id']);
                                             
                                             if (!db_has_day_attendancee($empl_id, $month, $year)) {
                                            if(in_array($weekday, explode(',',$woarr['weekly_off']))){
                                                 add_employee_attendance('WO', $empl_id, $month, $year, $day, $emplDetails['department_id']);
                                            }
                                            else if($holiday_date){
                                                add_employee_attendance('H', $empl_id, $month, $year, $day, $emplDetails['department_id']);
                                            }
                                            else {
                                                add_employee_attendance('P', $empl_id, $month, $year, $day, $emplDetails['department_id']);
                                                 }
                                             }else {
                                                  if(in_array($weekday, explode(',',$woarr['weekly_off']))){
                                                 update_employee_attendance('WO', $empl_id, $month, $year, $day); 
                                            }
                                            else if($holiday_date){
                                               update_employee_attendance('H', $empl_id, $month, $year, $day); 
                                            }
                                            else {
                                                update_employee_attendance('P', $empl_id, $month, $year, $day); 
                                                 }
                                                }
                                            display_notification(_('Your attendance has been added'));
                                        $_SESSION['login_attendance']='TRUE';
                                    }else {
                                        display_error(_('Please check your password '));
                                    }
				$Mode = 'RESET';
				$update_pager = true;
		} 
                if ($Mode == 'UPDATE_ITEM') {
			$result = '';
			$current_Outdate=date('Y-m-d H:i:s');
                        $attendance_date = strtotime($current_Outdate);
                                              $month = date("m", $attendance_date);
                                               $day = date("d", $attendance_date);
                                               $weekday = date('D',$attendance_date);
                                               $year = get_fiscal_year_id_from_date(date("Y-m-d",$attendance_date));
                        $att_sql=fetchInTime($empl_id); 
                        $val_attlist = db_query($att_sql);
                        $attendanceResult = db_fetch_row($val_attlist);
                        $d1=strtotime($attendanceResult[0]);
                        $d2=strtotime($current_Outdate);
                        $password= md5($_POST['password']);
				logout_attendance($empl_id, $password, $current_Outdate);
                                $working_seconds=$d2-$d1;
                                $att_sql123=fetchInTimeUserTableOut($empl_id); 
                                $val_attlist123 = db_query($att_sql123);
                                $attendanceResult123 = db_fetch_row($val_attlist123);
                                if($attendanceResult123[0]){
                                    $queryres = db_query(get_hours_list(1));
                                    $rows = db_fetch_assoc($queryres);
                                    update_Userattendance($current_Outdate,$empl_id,$working_seconds);
                                     if (db_has_day_attendancee($empl_id, $month, $year)) {
                                    $working_hours = $working_seconds/3600;
                                    $code = $rows['full_day']<=$working_hours?"OTP":
                                            $rows['half_day']>=$working_hours?"HD":"P";
                                    update_employee_attendance($code, $empl_id, $month, $year, $day);
                                     }
                                    display_notification(_('You are successfully logout'));
                                    unset($_SESSION['login_attendance']);
                                }
				$Mode = 'RESET';
				$update_pager = true;
		} 
                

		$Ajax->activate('details');
	}
}
function can_delete($selected_id) {
    if (key_in_foreign_table($selected_id, 'debtors_master', 'credit_status')) {
        display_error(_("Cannot delete this credit status because customer accounts have been created referring to it."));
        return false;
    }

    return true;
}

//-----------------------------------------------------------------------------------

if ($Mode == 'Delete') {
    delete_guest($selected_id);
    $Mode = 'RESET';
}

if ($Mode == 'RESET') {
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    $_POST['show_inactive'] = $sav;
}
//-----------------------------------------------------------------------------------
//===[Working]=====//

start_form(true);

$attendance_result = get_in_out_time_by_date($_SESSION['wa_current_user']->empl_id, date("Y-m-d"));
$inTimeVal=$attendance_result[0];
$outTimeVal=$attendance_result[1];

$logout = strtotime($outTimeVal);
$login = strtotime($inTimeVal);
$diff=$logout-$login;
if($logout)
$working_hours = decimal_to_time($attendance_result[2]/3600);


$inTimeVal1=explode(' ', $inTimeVal);
$outTimeVal1=explode(' ', $outTimeVal);
//echo '<pre>';
//print_r($attendance_result);
echo '<br>';

//echo '<pre>';
//print_r($_SESSION);
//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);


echo '<div class="att_title"><span> Date: </span>'.date('d-m-Y').'<span> Time </span>'.date('H-i').'</div>';

$in_time = date('Y-m-d H:i:s');
$out_time = date('Y-m-d H:i:s');
table_section_title(_("Mark Your Attendance"));
$stock_img_link = "";
    $check_remove_image = false;
    if ($selected_id != '' && file_exists(company_path() . '/images/empl/' . empl_img_name($_SESSION['wa_current_user']->empl_id) . ".jpg")) {
        $stock_img_link .= "<img id='empl_profile_pic' alt = '[" . $_SESSION['wa_current_user']->empl_id . ".jpg" . "]' src='" . company_path() . '/images/empl/' . empl_img_name($_SESSION['wa_current_user']->empl_id) . ".jpg?nocache=" . rand() . "'" . " height='150' border='1'> "
                . "<span style='float:right'>
                        <ul style='list-style:none;'>
                            <li>In Time: $inTimeVal1[1]<li>
                            <li>Out Time: $outTimeVal1[1]<li>
                            <li>Working Hours: $working_hours<li>
                        </ul>
                    </span>";
        $check_remove_image = true;
    } else {
        $stock_img_link .= "<img id='empl_profile_pic' alt = '[" . $_SESSION['wa_current_user']->empl_id . ".jpg" . "]' src='" . $path_to_root . '/modules/ExtendedHRM/images/no-image.png' . "?nocache=" . rand() . "'" . " height='150' border='1'>"."<span style='float:right'>
                        <ul style='list-style:none;'>
                            <li>In Time: $inTimeVal<li>
                            <li>Out Time: $outTimeVal<li>
                            <li>Working Hours: $working_hours<li>
                        </ul>
                    </span>";
    }
   
    $name=$_SESSION['wa_current_user']->name;
    $login_name=$_SESSION['wa_current_user']->loginname;
    label_row("&nbsp;", $stock_img_link);
    hidden('in_time', $in_time);
    hidden('out_time', $out_time);
    label_row("Welcome:", $name);
    text_row_attendance(_('User ID'), 'username', $login_name,20,50);
   // text_row(_('Password'), 'password', $_POST['edition'], 30);
    password_row(_("Password:"), 'password', $_POST['password']);

end_table(1);
echo '<center>';
$empl_id=$_SESSION['wa_current_user']->empl_id;
$_val=kv_get_attendance_listLogin($empl_id);
$val_attendance12 = db_query($_val);
$attendance_result23 = db_fetch_row($val_attendance12);


if(!empty($attendance_result23)){
    submit_add_or_update_attendance2($selected_id == -1, '', 'both');
}else {
    submit_add_or_update_attendance($selected_id == -1, '', 'both');
}
    
echo '</center>';

end_form();



end_page();
?>


<style>
.att_title {
    font-size: 20px;
}
button#UPDATE_ITEM {
    background: #B22222;
    width: 182px;
    height: 34px;
    font-size: 15px;
    border-radius: 7px;
    color: #fff;
    font-weight: bold;
}
button#ADD_ITEM {
    background: #008000;
    width: 182px;
    height: 34px;
    font-size: 15px;
    border-radius: 7px;
    color: #fff;
    font-weight: bold;
}
</style>
<script>
    /*document.onkeydown = function(e) {
        if(event.keyCode == 123) {
        return false;
        }
        if(event.keyCode == F12) {
        return false;
        }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)){
        return false;
        }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)){
        return false;
        }
        if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)){
        return false;
        }
    }*/
    
    



$(document).keydown(function (event) {
    if (event.keyCode == 123) { // Prevent F12
        return false;
    } else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) { // Prevent Ctrl+Shift+I        
        return false;
    }
});
 $(document).bind("contextmenu",function(e) {
 e.preventDefault();
});
</script>