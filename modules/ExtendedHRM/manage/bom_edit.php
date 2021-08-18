<?php
/* * ********************************************************************

  Released under the terms of the GNU General Public License, GPL,
  as published by the Free Software Foundation, either version 3
  of the License, or (at your option) any later version.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 * ********************************************************************* */
$page_security = 'HR_LEAVEFORM';
$path_to_root = "../../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
add_access_extensions();
//page(_($help_context = "Allocation Request")); 
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/manage/ui/kv_departments.inc" );
//include($path_to_root . "/modules/ExtendedHRM/manage/calendar_master.php");
include_once ($path_to_root);
include($path_to_root . "/modules/ExtendedHRM/includes/ui/leave_acural.inc" );
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
$js = '';
$new_emp = '';
$db_value;
///display_error($version_id['version_id']);
if ($version_id['version_id'] == '2.4.1') {
     
    if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);

   if (user_use_date_picker()) 
      
     $js .= get_js_date_picker();
}else {
     
    if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);

   if (user_use_date_picker()) 
      
     $js .= get_js_date_picker();
}

page(_($help_context = "Leave Request"), @$_REQUEST['popup'], false, "", $js);
?>
<html lang="en">
    <head>
        <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js" ?>"></script>
    </head>
    <?php
    ?>

</html>
<?php
$session_empl_id = $_SESSION['wa_current_user']->empl_id;
$empl_id=$session_empl_id;
 
//getting holiday list from the main website dmi database
$calendar_leave_list = array();
$start_date_end_date = array('start', 'end');
set_global_connection(1);
$result = get_calendar($show_inactive);
$k = 0;
$nos = db_num_rows($result);
$i = 0;
if ($nos != 0) {
    $cd1 = "";
    while ($myrow = db_fetch($result)) {
        $date = $myrow["start"];
        $end_date = $myrow["end"];
        $cd1 = "";
        for ($j = 0; $j < count($start_date_end_date); $j++) {
            $calendar_leave_list_for_diff[$i][$start_date_end_date[$j]] = date('m/d/Y', strtotime($myrow[$start_date_end_date[$j]]));
            $calendar_leave_inc_by_1_to_calculate_week_days[$i]['end'] = date('Y-m-d', strtotime(date('m/d/Y', strtotime($myrow[end]))." +1 days"));
            $calendar_leave_list_for_calculation[$i][$start_date_end_date[$j]] = date('Y-m-d', strtotime($myrow[$start_date_end_date[$j]]));
        }
        $i++;
    }
} else {
    
}//print($calendar_leave_inc_by_1_to_calculate_week_days[0]['end']);
set_global_connection(0);

simple_page_mode(true);
$selected_component = $selected_id;

if (isset($_GET['employees_id'])) {
    $_POST['employees_id'] = $_GET['employees_id'];
    $selected_parent = $_GET['employees_id'];
}

if (get_post('_request_date_changed')) {
    $req_date = date2sql($_POST['request_date']);
    $reqdt_data = get_empl_req_date($req_date, $_POST['employees_id']);
    if ($reqdt_data >= 2) {
        display_error("You have already applied two leaves from this request date.");
        set_focus('request_date');
        return false;
    }
}
if (get_post('_from_date_changed')) {
    $from_date = date("d-m-Y", strtotime($_POST["from_date"]));
    $todate = date("d-m-Y", strtotime($_POST["to_date"]));

    $f1 = date2sql($_POST["from_date"]);
    $t1 = date2sql($_POST["to_date"]);
    $req_date = date2sql($_POST['request_date']);

    $reqdt_data = get_empl_req_date($req_date, $_POST['employees_id']);

    if ($_POST['selected_id'] != 0) {

        if ($reqdt_data >= 3) {
            display_error("You have already applied two leaves from this request date.");
            set_focus('request_date');
            return false;
        }
    } else {
        if ($reqdt_data >= 2) {
            display_error("You have already applied two leaves from this request date.");
            set_focus('request_date');
            return false;
        }
    }

    if (date2sql($_POST['from_date']) > date2sql($_POST['to_date'])) {
        display_error("Selected To Date or From Date is Invalid!");
        set_focus('no_of_days');
        return false;
    } else {
        $result = get_weekly_off();
        $arr = array();
        $counter = array();
        $last_id_leave=0;
        $weekly_off_days = 0;
        $start_date = date2sql($_POST["from_date"]);
        $end_date = sql2date($_POST["to_date"]);
        $bool = get_leave_wekeend_status($_POST['employees_id'], $_POST["type_leave"], Date("Y"))[0];
        if ($bool == 1) {
            while ($weekly_off = db_fetch($result)) {
                array_push($arr, $arr[$weekly_off['option_name']] = $weekly_off['option_value']);
            }
            //taking first value of week off

            $k = 0;
            //calculation of holiday list form main dmi website 
             for ($i = 0; $i < (int) $nos; $i++) {
              $diff_mon = date_diff2($calendar_leave_list_for_diff[$i]['start'], $calendar_leave_list_for_diff[$i]['end'], 'd');
              $diff_between_start_end_dates = (1 + (-$diff_mon));
              $count_leave=0;
              for ($j = 0; $j <(int)$diff_between_start_end_dates; $j++) {
              $date = $calendar_leave_list_for_diff[$i]['start'];
              if(strtotime($date. ' + '.$j.' days') >= strtotime($_POST['from_date'])
              AND strtotime($date. ' + '.$j.' days') <= strtotime($calendar_leave_list_for_diff[$i]['end'])
              AND strtotime($date. ' + '.$j.' days') <=  strtotime($_POST['to_date'])) {  
                 $check_week_days = date('D',strtotime($date. ' + '.$j.' days'));
                 
                 if(in_array($check_week_days, array('Sun','Sat'))){
              $counter[$i] = ++$count_leave;
              $last_id_leave=$i;
                 }
              }
             }

              }
              $total_count = calculate_holiday_leave($counter,$last_id_leave);
             
           // echo '<p>' . $total_count . '</p>';
//calculation of weekend if there is holiday

             $minu_weekly_off_days_from_dmi_main_site = array();
            $weekly_off_days = calculate_weekly_off($start_date, $end_date, $arr['weekly_off'], $_POST["type_leave"]);
           /*  for ($i = 0; $i < (int) $nos; $i++) {
                 $date = $calendar_leave_list_for_diff[$i]['start'];
                if(strtotime($date. ' + '.$j.' days') >=  strtotime($_POST["from_date"])){
            $minu_weekly_off_days_from_dmi_main_site[$i] = calculate_weekly_off($calendar_leave_list_for_calculation[$i]['start'], $calendar_leave_inc_by_1_to_calculate_week_days[$i]['end'], $arr['weekly_off'], $_POST["type_leave"]);
                }
             }   
             $total_weekly_off_in_calendar = calculate_holiday_leave($minu_weekly_off_days_from_dmi_main_site); */
            $diff_mon = date_diff2($_POST["from_date"], $_POST["to_date"], 'd');
            $_POST['no_of_days'] =((1 + (-$diff_mon))) - (($weekly_off_days + $total_count));
        } else {
            $diff_mon = date_diff2($_POST["from_date"], $_POST["to_date"], 'd');
            $_POST['no_of_days'] = (1 + (-$diff_mon)) - $weekly_off_days;
        }
    }
    $Ajax->activate('no_of_days');

    $company_year = get_company_pref('f_year');
    $fiscal_year = get_fiscalyear($company_year);
    // $holiday_sql = "SELECT * FROM ".TB_PREF."kv_holiday_master WHERE fisc_year = '".$fiscal_year['0']."' AND inactive = 0";
    //	$holiday_res = db_query($holiday_sql);
    //	while($holiday_result = db_fetch($holiday_res)){

    $holiday_date = get_holiday_check(date2sql($_POST['from_date']), date2sql($_POST['to_date']), $fiscal_year['0']);
    if ($holiday_date != 0) {
        display_error('Selected from date and to date range contains holidays  please change your schedule as per your holiday calendar');
        return false;
    } else {
        // return true;
    }
    // }
    $samedts_data = get_empl_same_tdates($f1, $t1, $_POST['employees_id'], $req_date);

    if ($selected_id != -1) {
        $edit_same_dts = get_empl_alloc_single($selected_id);
        if ($edit_same_dts['allocate_id'] != 0) {
            
        } else {
            if ($samedts_data != 0) {
                display_error("Entered invalid dates.");
                set_focus('to_date');
                return false;
            }
        }
    } else {
        if ($samedts_data != 0) {
            display_error("Entered invalid dates.");
            set_focus('to_date');
            return false;
        }
    }

    if (date2sql($_POST['request_date']) > date2sql($_POST['from_date'])) {
        display_error("Selected from date is Invalid!");
        set_focus('from_date');
        return false;
    }
}

if (get_post('_to_date_changed')) {

    $from_date = date("d-m-Y", strtotime($_POST["from_date"]));
    $todate = date("d-m-Y", strtotime($_POST["to_date"]));

    $f1 = date2sql($_POST["from_date"]);
    $t1 = date2sql($_POST["to_date"]);
    $req_date = date2sql($_POST['request_date']);

    $reqdt_data = get_empl_req_date($req_date, $_POST['employees_id']);

    if ($_POST['selected_id'] != 0) {

        if ($reqdt_data >= 3) {
            display_error("You have already applied two leaves from this request date.");
            set_focus('request_date');
            return false;
        }
    } else {
        if ($reqdt_data >= 2) {
            display_error("You have already applied two leaves from this request date.");
            set_focus('request_date');
            return false;
        }
    }

    $company_year = get_company_pref('f_year');
    $fiscal_year = get_fiscalyear($company_year);
    //$holiday_sql = "SELECT * FROM ".TB_PREF."kv_holiday_master WHERE fisc_year = '".$fiscal_year['0']."' AND inactive = 0";
    //		$holiday_res = db_query($holiday_sql);
    //while($holiday_result = db_fetch($holiday_res)){

    $holiday_date = get_holiday_check(date2sql($_POST['from_date']), date2sql($_POST['to_date']), $fiscal_year['0']);
    if ($holiday_date != 0) {
        display_error('Selected from date and to date range contains holidays  please change your schedule as per your holiday calendar');
        return false;
    } else {
        // return true;
    }
    //}

    if (date2sql($_POST['from_date']) > date2sql($_POST['to_date'])) {
        display_error("Selected to date is Invalid!");
        set_focus('no_of_days');
        return false;
    } else {
        $result = get_weekly_off();
        $arr = array();
        $counter = array();
        $weekly_off_days = 0;
        $start_date_arr = explode("/", $_POST["from_date"]);
        $end_date_arr = explode("/", $_POST["to_date"]);
        $start_date = $start_date_arr[2] . "-" . $start_date_arr[0] . "-" . $start_date_arr[1];
        $end_date = $end_date_arr[2] . "-" . $end_date_arr [0] . "-" . $end_date_arr[1];
        //=====getting leave days weekend status
        $bool = get_leave_wekeend_status($_POST['employees_id'], $_POST["type_leave"], Date("Y"))[0];
        //======if weekend status is 1 then do nothing and exicute else part
        if ($bool == 1) {
            //fettcihing value form kv_empl_option and aligning in array
            while ($weekly_off = db_fetch($result)) {
                array_push($arr, $arr[$weekly_off['option_name']] = $weekly_off['option_value']);
            }
           
            //calculation of holiday list by using main website dmi
             for ($i = 0; $i < (int) $nos; $i++) {
              $diff_mon = date_diff2($calendar_leave_list_for_diff[$i]['start'], $calendar_leave_list_for_diff[$i]['end'], 'd');
              $diff_between_start_end_dates = (1 + (-$diff_mon));
              $count_leave=0;
              for ($j = 0; $j <(int)$diff_between_start_end_dates; $j++) {
              $date = $calendar_leave_list_for_diff[$i]['start'];
              if(strtotime($date. ' + '.$j.' days') >=  strtotime($_POST["from_date"])
              && strtotime($date. ' + '.$j.' days') <= strtotime($calendar_leave_list_for_diff[$i]['end'])
              && strtotime($date. ' + '.$j.' days') <=  strtotime($_POST["to_date"])){
                
                  $check_week_days = date('l',strtotime($date. ' + '.$j.' days'));
                 if($check_week_days != 'Sunday'){
              $counter[$i] = ++$count_leave;
                 }
              }
              }

              }
              $total_count = calculate_holiday_leave($counter); 
            //calculation of weekend 
            //=========calculating weeks days by calling this function  
              $minu_weekly_off_days_from_dmi_main_site = array();
              
                  
            $weekly_off_days = calculate_weekly_off($start_date, $end_date, $arr['weekly_off'], $_POST["type_leave"]);
            
            for ($i = 0; $i < (int) $nos; $i++) {
                 $date = $calendar_leave_list_for_diff[$i]['start'];
               if(strtotime($date. '+'.$j.'days') >=  strtotime($_POST["from_date"]) || strtotime($date. '+'.$j.'days') >=  strtotime($_POST["from_date"]) ){
            $minu_weekly_off_days_from_dmi_main_site[$i] = calculate_weekly_off($calendar_leave_list_for_calculation[$i]['start'], $calendar_leave_inc_by_1_to_calculate_week_days[$i]['end'], $arr['weekly_off'], $_POST["type_leave"]);
                }
             }   
             $total_weekly_off_in_calendar = calculate_holiday_leave($minu_weekly_off_days_from_dmi_main_site); 
            $diff_mon = date_diff2($_POST["from_date"], $_POST["to_date"], 'd');
           
             $_POST['no_of_days'] =((1 + (-$diff_mon)))-(($weekly_off_days+$total_count));
        } else {
            //====simply calculate if weekend status is 1
       //  display_error($diff_mon); 
            $diff_mon = date_diff2($_POST["from_date"], $_POST["to_date"], 'd');
              
            $_POST['no_of_days'] = (1 + (-$diff_mon)) - $weekly_off_days;
        }
    }
 //display_error($_POST['no_of_days']);
    $Ajax->activate('no_of_days');


    if (date2sql($_POST['request_date']) > date2sql($_POST['to_date'])) {
        display_error("Selected to date is Invalid!");
        set_focus('to_date');
        return false;
    }

    $samedts_data = get_empl_same_tdates($f1, $t1, $_POST['employees_id'], $req_date);
    if ($selected_id != -1) {
        $edit_same_dts = get_empl_alloc_single($selected_id);

        if ($edit_same_dts['allocate_id'] != 0) {
            
        } else {
            if ($samedts_data != 0) {
                display_error("Entered invalid dates.");
                set_focus('to_date');
                return false;
            }
        }
    } else {
        if ($samedts_data != 0) {
            display_error("Entered invalid dates.");
            set_focus('to_date');
            return false;
        }
    }
}

function empl_leave_data($empl_id) {
   
    br();
   // $only = $_SESSION['only'];
   
           // if($only == 1){
    div_start('details');
    $total_days = 31;
    //$_POST['new_empl'] = $empl_id;
   // display_error($empl_id);
    $selected_empl = get_employee_whole_attendance($empl_id);
   
    //print_r($selected_empl);
   

        echo '<!--';
        start_table(TABLESTYLE);
        echo "<tr><td class='tableheader'>" . _("Year") . "</td><td class='tableheader'>" . _("Month") . "</td>";

        for ($kv = 1; $kv <= $total_days; $kv++) {
            echo "<td style='background-color:#e0db98' class='tableheader'>" . $kv . "</td>";
        }
        echo "<td class='tableheader'>" . _("Working Days") . "</td><td class='tableheader'>" . _("Leave Days") . "</td><td class='tableheader'>" . _("LOP Days") . "</td><td class='tableheader'>" . _("Payable Days") . "</td></tr>";
        foreach ($selected_empl as $single_month) {
            $fiscal_yr = get_fiscalyear($single_month['year']);
            echo '<tr style="text-align:center"><td>' . $fiscal_yr['begin'] . '-' . $fiscal_yr['end'] . '</td><td>' . date("F", strtotime("2016-" . $single_month['month'] . "-01")) . '</td>';
            $leave_Day = 0;
            $month_days = date("t", strtotime($single_month['year'] . "-" . $single_month['month'] . "-01"));
            for ($kv = 5; $kv <= $total_days + 4; $kv++) {
                echo '<td>' . ($single_month[$kv] ? $single_month[$kv] : '-') . '</td>';
                if ($single_month[$kv] == 'A')
                    $leave_Day += 1;
                if ($single_month[$kv] == 'HD')
                    $leave_Day += 0.5;
            }
            $Payable_days = $single_month['year'] - $leave_Day;
            echo '<td>' . $single_month['year'] . ' </td>  <td>' . $leave_Day . '</td> <td>' . $leave_Day . ' </td> <td>' . $Payable_days . ' </td><tr>';
        }
        end_table(1);
        echo '-->';
  // } else
        display_notification(_("No data Exist for the selected Employee."));

    start_table(TABLESTYLE);
     display_error($empl_id);
     
      
    $employee_data = get_employee_desig($empl_id);
    $employee_gender = get_employee_gender($empl_id);
 
    $em_lv_count_yrly = get_employee_leave_count_fiscalyear($fiscal_yr['id'], $empl_id);
   // print_r($em_lv_count_yrly);
    //die();
    
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
            if($empl_leave_count_yearly[$k] == 'WO'){
                $empl_Compensatory_leaves ++;
            }
            if($empl_leave_count_yearly[$k] == 'WOP'){
                $empl_Compensatory_leaves_present ++;
            }
        }
    }

    $leaves = get_leaves_data($employee_data['department'], $employee_data['desig_group'], $employee_data['desig'], $fiscal_yr['id'], $empl_id);
    echo "<tr><td class='tableheader'>" . _("Leave Type") . "</td><td class='tableheader'>" . _("No. of Days Used") . "</td><td class='tableheader'>" . ("Available Days") . "</td><td class='tableheader'>" . ("Total No. of Days") . "</td></tr>";
    $tot_utilized_casual_leaves = $leaves['no_of_cls'] - $empl_casual_leaves_count_yearly;
    $tot_utilized_vacation_leaves = $leaves['no_of_pls'] - $empl_vacation_leaves_count_yearly;
    $tot_utilized_medical_leaves = $leaves['no_of_medical_ls'] - $empl_medical_leaves_count_yearly;
    $tot_utilized_Earned_leaves = $leaves['no_of_el'] - $empl_Earned_leaves_count_yearly;
    $tot_utilized_scl_leaves = $leaves['no_of_spl_cls'] - $empl_special_casual_leaves_count_yearly;
    $tot_utilized_mtl_leaves = $leaves['no_of_mat_ls'] - $empl_maternity_leaves_count_yearly;
    $tot_utilized_ptl_leaves = $leaves['no_of_patern_ls'] - $empl_paternity_leaves_count_yearly;
    $tot_utilized_compensatory_leaves = $empl_Compensatory_leaves_present - $empl_Compensatory_leaves;
    echo "<tr><td>" . _("No. of Casual Leaves") . "</td><td>" . $empl_casual_leaves_count_yearly . "</td><td>" . $tot_utilized_casual_leaves . "</td><td>" . (float)$leaves['no_of_cls'] . "</td></tr>";
    echo "<tr><td>" . _("No. of Vacation Leaves") . "</td><td>" . $empl_vacation_leaves_count_yearly . "</td><td>" . $tot_utilized_vacation_leaves . "</td><td>" . (float)$leaves['no_of_pls'] . "</td></tr>";
    echo "<tr><td>" . _("No. of Medical Leaves") . "</td><td>" . $empl_medical_leaves_count_yearly . "</td><td>" . $tot_utilized_medical_leaves . "</td><td>" . (float)$leaves['no_of_medical_ls'] . "</td></tr>";
    echo "<tr><td>" . _("No. of Earned Leaves") . "</td><td>" . $empl_Earned_leaves_count_yearly . "</td><td>" . $tot_utilized_Earned_leaves . "</td><td>" . (float)$leaves['no_of_el'] . "</td></tr>";
    if ($employee_gender['0'] == 1) {
        echo "<tr><td>" . _("No. of Special Casual Leaves") . "</td><td>" . $empl_special_casual_leaves_count_yearly . "</td><td>" . $tot_utilized_scl_leaves . "</td><td>" . (float)$leaves['no_of_spl_cls'] . "</td></tr>";
    } else if ($employee_gender['0'] == 2) {
        echo "<tr><td>" . _("No. of Special Casual Leaves") . "</td><td>" . $empl_special_casual_leaves_count_yearly . "<td>" . $tot_utilized_scl_leaves . "</td><td>" . (float)$leaves['no_of_spl_cls_female'] . "</td></tr>";
    }
    echo "<tr><td>" . _("No. of Maternity Leaves") . "</td><td>" . $empl_paternity_leaves_count_yearly . "</td><td>" . $tot_utilized_mtl_leaves . "</td><td>" . (float)$leaves['no_of_mat_ls'] . "</td></tr>";
    echo "<tr><td>" . _("No. of Paternity Leaves") . "</td><td>" . $empl_paternity_leaves_count_yearly . "</td><td>" . $tot_utilized_ptl_leaves . "</td><td>" . (float)$leaves['no_of_patern_ls'] . "</td></tr>";
    echo "<tr><td>" . _("No. of Compensatory Leaves") . "</td><td>" . $empl_Compensatory_leaves . "</td><td>" . $tot_utilized_compensatory_leaves . "</td><td>" . $empl_Compensatory_leaves_present . "</td></tr>";
    end_table(2);
    div_end();

}

function display_employee_leave_records($selected_parent, $dept_id, $desig_group_id, $desig_id){
    $result = get_employee_leave_records_By_emp_id($selected_parent);
    div_start('bom');
    start_table(TABLESTYLE, "width='60%'");
    $th = array( _("Request Date"), _("Leave Type"), _("Reason"),
        _("From Date"), _("To Date"), _("No. of Days"), _("Status"), _("Edit"), _("Cancel"));
    table_header($th);

    $k = 0;

    if ((db_num_rows($result)) != 0) {

        while ($myrow = db_fetch($result)) {

            alt_table_row_color($k);
            $leave_name = get_leave_type($myrow["type_leave"]);
            label_cell(date("d-m-Y",strtotime($myrow["request_date"])));
            label_cell($leave_name["leave_type"]);
            label_cell($myrow["reason"]);
            label_cell(date("d-m-Y",strtotime($myrow["from_date"])));
            label_cell(date("d-m-Y",strtotime($myrow["to_date"])));
            label_cell($myrow["no_of_days"]);
            global $leave_approval_status;
            label_cell($leave_approval_status[$myrow["status"]]);
            edit_button_cell("Edit" . $myrow['allocate_id'], _("Edit"));
            delete_button_cell("Delete" . $myrow['allocate_id'], _("Delete"));
            submit_js_confirm("Delete" . $myrow['allocate_id'], sprintf(_("You are about to delete a leave request Do you want to continue?"), $myrow['allocate_id']));

            // label_cell(view_link($myrow["allocate_id"])); 
            end_row();
        } //END WHILE LIST LOOP
    } else {
        label_cell(_("No Records Found"), 'colspan=8 align=center');
    }
    end_table();
    div_end();
}
function display_opening_balance_carry_forward($empl_id) {
    $result = get_employee_leave_records_By_emp_id_carry_for_vl($empl_id);
    div_start('bom');
    start_table(TABLESTYLE, "width='60%'");
    $th = array( _("Leave Type"), _("Carry Forward"), _("Opening Balance"));
    table_header($th);

    $k = 0;
          $myrow_vl = db_fetch($result);
          start_row();
           label_cell('No of Vacational Leaves');
           label_cell((float)$myrow_vl['no_of_pls']);
           label_cell((float)$myrow_vl['no_of_pls']);
           end_row();
             $result = get_employee_leave_records_By_emp_id_carry_for_ml($empl_id);
              $myrow_ml = db_fetch($result);
          start_row();
           label_cell("No. of Medical Leaves");
           label_cell((float)$myrow_ml['no_of_medical_ls']);
           label_cell((float)$myrow_ml['no_of_medical_ls']);
           end_row();
           $result = get_employee_leave_records_By_emp_id_carry_for($empl_id);
              $myrow_el = db_fetch($result);
          start_row();
           label_cell("No. of Earned Leaves");
           label_cell((float)$myrow_el['no_of_el']);
           label_cell((float)$myrow_el['no_of_el']);
           end_row();
             $result = get_employee_leave_records_By_emp_id_carry_for_cl($empl_id);
              $myrow_cl = db_fetch($result);
          start_row();
           label_cell("No. of Casual Leaves");
           label_cell((float)$myrow_cl['no_of_cls']);
           label_cell((float)$myrow_cl['no_of_cls']);
           end_row();
    end_table();
    div_end();
}

//--------------------------------------------------------------------------
function on_submit($selected_parent, $selected_component = -1) {
    $from_date = date2sql($_POST['from_date']);
    $to_date = date2sql($_POST['to_date']);
    $request_date = date2sql($_POST['request_date']);

    $today_date = date('d-m-Y');

    $query = "SELECT empl_id FROM " . TB_PREF . "kv_empl_info WHERE id=" . db_escape($_POST['employees_id']);
    $result1 = db_query($query);
    $results = db_fetch_row($result1);
    $emp_code = $results[0];

    $tmpname = $_FILES['upload_file']['tmp_name'];
    $dir = company_path() . '/leave_attachments';
    if (!file_exists($dir)) {
        mkdir($dir, 0777);
        $index_file = "<?php\nheader(\"Location: ../index.php\");\n?>";
        $fp = fopen($dir . "/index.php", "w");
        fwrite($fp, $index_file);
        fclose($fp);
    }

    //	$types = array('image/jpeg', 'image/gif', 'image/png');
    //if (in_array($_FILES['uploaded']['type'], $types)) {
    $filename = $_FILES['upload_file']['name'];
    $filesize = $_FILES['upload_file']['size'];
    $filetype = $_FILES['upload_file']['type'];
    //} else {
    //	$ok=0;
    //}
    // file name compatible with POSIX
    // protect against directory traversal
    if ($filename != "")
        $unique_name = uniqid('');
    move_uploaded_file($tmpname, $dir . '/' . $unique_name);


    $_POST['upload_file'] = $filename;
    $_POST['filesize'] = $filesize;
    $_POST['filetype'] = $filetype;
    $_POST['unique_name'] = $unique_name;

    add_allocation($_POST['dept_id'], $_POST['desig_group_id'], $_POST['desig_id'], $selected_parent, $_POST['type_leave'], $_POST['reason'], $today_date, $from_date, $to_date, $_POST['no_of_days'], $_POST['upload_file'], $_POST['filesize'], $_POST['filetype'], $_POST['unique_name'], date('Y-m-d'), $db_value);
    display_notification(_('Leave Request has been added'));

    $Mode = 'RESET';
}

function on_edit($selected_parent, $selected_component = -1) {

    $from_date = date2sql($_POST['from_date']);
    $to_date = date2sql($_POST['to_date']);
    $request_date = date2sql($_POST['request_date']);
    $today_date = date('d-m-Y');
    $query = "SELECT empl_id FROM " . TB_PREF . "kv_empl_info WHERE id=" . db_escape($_POST['employees_id']);
    $result1 = db_query($query);
    $results = db_fetch_row($result1);
    $emp_code = $results[0];

    $tmpname = $_FILES['upload_file']['tmp_name'];
    $dir = company_path() . "/leave_attachments";
    //display_error($dir);
    if (!file_exists($dir)) {
        mkdir($dir, 0777);
        $index_file = "<?php\nheader(\"Location: ../index.php\");\n?>";
        $fp = fopen($dir . "/index.php", "w");
        fwrite($fp, $index_file);
        fclose($fp);
    }

    $filename = $_FILES['upload_file']['name'];
    //display_error($filename);die;
    $filesize = $_FILES['upload_file']['size'];
    $filetype = $_FILES['upload_file']['type'];

    $row = get_empl_alloc_single($selected_component);


    if ($row['upload_file'] == "") {
        $unique_name = $row['unique_name'];
    } else { //exit();
        $unique_name = uniqid('');
    }
    //display_error($unique_name);die;
    if ($filename && file_exists($dir . "/" . $unique_name))
        unlink($dir . "/" . $unique_name);


    move_uploaded_file($tmpname, $dir . "/" . $unique_name);
    $_POST['upload_file'] = $filename;

    $_POST['filesize'] = $filesize;
    $_POST['filetype'] = $filetype;
    $_POST['unique_name'] = $unique_name;

    $today_date = date('d-m-Y');
    update_allocation($selected_component, $_POST['dept_id'], $_POST['desig_group_id'], $_POST['desig_id'], $selected_parent, $_POST['type_leave'], $_POST['reason'], $today_date, $from_date, $to_date, $_POST['no_of_days'], $_POST['upload_file'], $_POST['filesize'], $_POST['filetype'], $_POST['unique_name'], $request_date);
    $Mode = 'RESET';
    display_notification(_('Leave Request has been updated'));

    if (!empty($_POST['type_leave'])) {
       // echo 'hello' . $_POST['type_leave'];
    }
}

//--------------------------------------------------------------------------------------------------

function can_process() {

    $company_year = get_company_pref('f_year');

    $fiscal_year = get_fiscalyear($company_year);

    $financial_year_start_month = $fiscal_year['1'];
    $financial_year_end_month = $fiscal_year['2'];

    $from_date = date2sql($_POST['from_date']);
    $to_date = date2sql($_POST['to_date']);
    $request_date = date2sql(date("Y-m-d"));


    /* if (!(($from_date >= $financial_year_start_month) && ($to_date <= $financial_year_end_month))) {

      display_error(_("Please select valid  dates according to fiscal year"));
      return false;
      } */
    /* if(!(($to_date >= $financial_year_start_month) && ($to_date <= $financial_year_end_month)))
      {
      display_error(_("Please select valid to date according to fiscal year"));
      return false;
      } */
    if (($from_date >= $financial_year_start_month) && ($to_date <= $financial_year_end_month)) {
        if ($from_date > $to_date) {

            display_error(_("Please select valid from date"));

            return false;
        }
    }
    
    //=========[COMENTED BY ASHUTOSH PLEASE CONTACT FOR FUTHER QUERIES [HERE FINANCIAL YEAR HAS BEEN REMOVED ACCORDING TO OUR PROJECT MANGER WITS]==============]
  /*  if (!((date('Y-m-d') >= $financial_year_start_month) && (date("Y-m-d") <= $financial_year_end_month))) {
        display_error(_("Please select valid request date"));

        return false;
    }*/

    if ($request_date) {
        //display_error($_POST['UPDATE_ITEM']);die;
        $count_reqdate = get_empl_req_date($request_date, $_POST['employees_id']);
        // display_error($count_reqdate);
        if ($_POST['UPDATE_ITEM'] == 'Update') {

            if ($count_reqdate < 3) {
                //return true;
            } else {
                display_error(_("You should not apply more than two leaves on this Request Date."));
                set_focus('request_date');
                return false;
            }
        } else {
            if ($count_reqdate >= 2) {
                display_error(_("You should not apply more than two leaves on this Request Date."));
                set_focus('request_date');
                return false;
            }
        }
    }

    if ((date2sql($_POST['request_date']) > date2sql($_POST['from_date'])) || (date2sql($_POST['request_date']) > date2sql($_POST['to_date']))) {
        display_error("Selected dates are Invalid!");
        //set_focus('from_date');
        return false;
    }
    //$holiday_sql = "SELECT * FROM ".TB_PREF."kv_holiday_master WHERE fisc_year = '".$fiscal_year['0']."' AND inactive = 0";
    //	   $holiday_res = db_query($holiday_sql);
    //	while($holiday_result = db_fetch($holiday_res)){

    $holiday_date = get_holiday_check(date2sql($_POST['from_date']), date2sql($_POST['to_date']), $fiscal_year['0']);

    if ($holiday_date != 0) {
        display_error('Selected from date and to date range contains holidays  please change your schedule as per your holiday calendar');
        return false;
    } else {
        // return true;
    }
    //	}
    return true;
}

if ($Mode == 'Delete') {
    $value = get_empl_alloc_single($selected_id);
    $emp_alloc = delete_empl_alloc_new($selected_id, $value);
    if ($emp_alloc == 0) {
        display_notification(_('leave request has been deleted'));
        //  display_notification(_("Leave Request has been deleted")); 
    } else {
        display_notification(_("Leave Request has been deleted"));
    }
    $Mode = 'RESET';
}

if ($Mode == 'RESET') {
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    $_POST['show_inactive'] = $sav;
}

//--------------------------------------------------------------------------------------------------

//echo $session_empl_id;
if (empty($session_empl_id)) {
    echo 'This panel is only for the employee of the organization.';
    exit;
}

start_form();

//start_form(false, true);
start_table(TABLESTYLE_NOBORDER);
start_row();
$edit_empl_id = date("Y");
$selected_parent = '';
$selected_department = $empl_job['department'];
$selected_desig_group = $empl_job['desig_group'];
$selected_desig_id = $empl_job['desig'];

$empl_job = get_employee_job($session_empl_id);


if ($empl_job) {
    $selected_parent = $session_empl_id;
    $selected_department = $empl_job['department'];
    $selected_desig_group = $empl_job['desig_group'];
    $selected_desig_id = $empl_job['desig'];
} else {
    echo 'Employee Department & Designation missing';
    exit;
}
label_row(_("Employee Id:"), $session_empl_id);
//calendar_list_row(_("Calendar Year:"), 'cal_year', $edit_empl_id, false, true);
//depts_list_row(_("Department:"), 'dept_id', null, false, true);
//designationgroup_list_row(_("Designation Group:"), 'desig_group_id', null, false, true,$_POST['dept_id']);
//designt_list_row(_("Designation:"), 'desig_id', null,false,true,$_POST['desig_group_id']);
//employeename_list_cells(_("Select an Employee: "), 'employees_id', null,false, true,check_value('show_inactive'),false,$_POST['dept_id'],$_POST['desig_id'],$_POST['desig_group_id']);


end_row();

if (list_updated('dept_id') || list_updated('desig_group_id') || list_updated('desig_id'))
    $Ajax->activate('_page_body');

if (list_updated('employees_id')) {
    $Ajax->activate('bom');
    //$Ajax->activate('leave');
}


end_table();
br();

end_form();
//--------------------------------------------------------------------------------------------------

if ($selected_parent != '') {
    if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM')
        if ($Mode == 'ADD_ITEM' && can_process()) {
            on_submit($selected_parent, $selected_id);
        }
    if ($Mode == 'UPDATE_ITEM' && can_process()) {

        on_edit($selected_parent, $selected_id);
    }

    start_form(true);

    display_employee_leave_records($selected_parent, $_POST['dept_id'], $_POST['desig_group_id'], $_POST['desig_id']);


    echo '<br>';
 start_outer_table(TABLESTYLE2, "width='90%'"); // outer table

    table_section(1,'width=33%');
   

    if ($selected_id != -1) {
        if ($Mode == 'Edit') {

            //editing a selected component from the link to the line item
            $myrow = get_empl_alloc_single($selected_id);
            $_POST['request_date'] = sql2date($myrow["request_date"]);
            $_POST['type_leave'] = $myrow["type_leave"]; // by Tom Moulton
            $_POST['reason'] = $myrow["reason"];
            $_POST['from_date'] = sql2date($myrow["from_date"]);
            $_POST['to_date'] = sql2date($myrow["to_date"]);
            $_POST['no_of_days'] = $myrow["no_of_days"];
            //label_row(_("Component:"), $myrow["component"] . " - " . $myrow["description"]);
        }
        hidden('selected_id', $selected_id);
    }
    /* else
      {
      start_row();
      label_cell(_("Component:"), "class='label'");

      echo "<td>";
      echo stock_component_items_list('component', $selected_parent, null, false, true);
      if (get_post('_component_update'))
      {
      $Ajax->activate('quantity');
      }
      echo "</td>";
      end_row();
      } */
    $leaves1 = get_leaves_data($employee_data['department'], $employee_data['desig_group'], $employee_data['desig'], $fiscal_yr['id'], $session_empl_id);
    hidden('employees_id', $selected_parent);
    hidden('dept_id', $selected_department);
    hidden('desig_group_id', $selected_desig_group);
    hidden('desig_id', $selected_desig_id);
    div_start('leave');
    date_row(_("Request Date") . ":", 'request_date', 30, null, '', '', '', null, true, 'requestDate');

    if ($_POST['employees_id']) {
        $empl_gender = get_employee($_POST['employees_id']);
        //display_error($empl_gender['gender']);
        $gender = $empl_gender['gender'];
        leavetype_list_row(_("Leave Type:"), 'type_leave', null, false, false, $gender);
        $Ajax->activate('type_leave');
    } else {
        leavetype_list_row(_("Leave Type:"), 'type_leave', null, false, false);
    }
    textarea_row(_("Reason:"), 'reason', null, 30, 4);
    date_row(_("From Date") . ":", 'from_date', 20, null, '', '', '', null, true, "from_date");
    date_row(_("To Date") . ":", 'to_date', 20, null, '', '', '', null, true, "to_date");
    text_row_ex(_("No. of Days :"), 'no_of_days', 10, null, null, 1, null, null, false, true, 'num_day');
    file_row(_("Attached File") . ":", 'upload_file', 'upload_file');
    
   table_section(2,'width=33%');
   display_opening_balance_carry_forward($selected_parent);
   div_end();
   end_outer_table();
    
      start_table(TABLESTYLE_NOBORDER);
    start_row();
    br();
    label_cells($label,submit_add_or_update_center($selected_id == -1, '', 'both'));
    end_row();
    end_table();
    end_form();
}
empl_leave_data($selected_parent);
end_page();
?>
<script>
    $('#ADD_ITEM').mouseover(function () {
        check = $('[name=type_leave]').val();
        if (check == 9) {
            $("#num_day").val(0.5);
        } else if (check == 10)
        {
            $("#num_day").val(0.5);
        }
    });


    $('#UPDATE_ITEM').mouseover(function () {
        check = $('[name=type_leave]').val();
        if (check == 9) {
            $("#num_day").val(0.5);
        } else if (check == 10)
        {
            $("#num_day").val(0.5);
        }
    });

</script>

