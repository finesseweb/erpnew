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
$page_security = 'HR_CMPFORM';
$path_to_root = "../../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
add_access_extensions();
//page(_($help_context = "Allocation Request")); 
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/manage/ui/kv_departments.inc" );
//include($path_to_root . "/modules/ExtendedHRM/manage/calendar_master.php");
include_once($path_to_root . "/attendance/function/function.php");
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

page(_($help_context = "Componsentory Request"), @$_REQUEST['popup'], false, "", $js);
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
$start_date_end_date = array('from_date', 'to_date');
//set_global_connection(1);
$fisical = get_current_fiscalyear();
$result =  get_calendar($fisical,$show_inactive);
$k = 0;
$nos = db_num_rows($result);
$i = 0;
if ($nos != 0) {
    $cd1 = "";
    while ($myrow = db_fetch($result)) {
        $date = $myrow["from_date"];
        $end_date = $myrow["to_date"];
        $cd1 = "";
        for ($j = 0; $j < count($start_date_end_date); $j++) {
            $calendar_leave_list_for_diff[$i][$start_date_end_date[$j]] = $myrow[$start_date_end_date[$j]];
            $calendar_leave_inc_by_1_to_calculate_week_days[$i]['to_date'] = date('Y-m-d', strtotime($myrow['to_date'])." +1 days");
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

    if (date2sql($_POST['from_date']) > date2sql($_POST['to_date'])){
        display_error("Selected To Date or From Date is Invalid!");
        set_focus('no_of_days');
        return false;
    } else {
        $result = get_weekly_off();
        $arr = array();
        $counter = array();
        $weekly_off_days = 0;
        $start_date = date2sql($_POST["from_date"]);
        $end_date = date2sql($_POST["to_date"]);
        //=====getting leave days weekend status
        $bool = get_leave_wekeend_status($_POST['employees_id'], $_POST["type_leave"], Date("Y"))[0];
        
             //fettcihing value form kv_empl_option and aligning in array
            while ($weekly_off = db_fetch($result)) {
                 $arr[$weekly_off['option_name']] = $weekly_off['option_value'];
            }
           
            //calculation of holiday list by using main website dmi
             for ($i = 0; $i < (int) $nos; $i++) {
                    $diff_mon = date_diff2($calendar_leave_list_for_diff[$i]['from_date'], $calendar_leave_list_for_diff[$i]['to_date'], 'd');

                    $diff_between_start_end_dates = (1 + ($diff_mon));
                    $count_leave=0;
                    for ($j = 0; $j <(int)$diff_between_start_end_dates; $j++) {
                    $date = $calendar_leave_list_for_diff[$i]['from_date'];
                    if(strtotime($date. ' + '.$j.' days') >=  strtotime($_POST["from_date"])
                    && strtotime($date. ' + '.$j.' days') <= strtotime($calendar_leave_list_for_diff[$i]['to_date'])
                    && strtotime($date. ' + '.$j.' days') <=  strtotime($_POST["to_date"])){
                        $attendance_date = date("Y-m-d",strtotime($date. ' + '.$j.' days'));
                        $logindetails = get_in_out_time_by_date($session_empl_id, $attendance_date);
                       
                        $check_week_days = date('l',strtotime($date. ' + '.$j.' days'));
                       if(!in_array($check_week_days, explode(',',$arr['weekly_off'])) && !empty($logindetails[0])){
                           $totaltime += $logindetails[2]?$logindetails[2]:0;
                    $counter[$i] = ++$count_leave;
                       }
                    }
                    }

              }
              
              $total_count = calculate_holiday_leave($counter); 
             $weekly_off_days = calculate_weekly_off($start_date, $end_date,$arr['weekly_off']);
              $diff_mon = date_diff2($start_date, $end_date, 'd');
         for ($i = 0; $i < (int) $diff_mon+1; $i++) {
             $date = $start_date;
             $attendance_date = date("Y-m-d",strtotime($date. ' + '.$i.' days'));
             $logindetails = get_in_out_time_by_date($session_empl_id, $attendance_date);
             $weekday = date('D',strtotime($date. ' + '.$i.' days'));
              if(in_array($weekday, explode(',',$arr['weekly_off'])) && !empty($logindetails[0])){
                           $totaltime += $logindetails[2]?$logindetails[2]:0;
                       }
         }
         
            $_POST['no_of_days'] =(($weekly_off_days+$total_count))?(($weekly_off_days+$total_count)):0;
               $_POST['total_time'] = decimal_to_time($totaltime/3600);
               
    }
    $Ajax->activate('no_of_days');
 $Ajax->activate('total_time');
    $company_year = get_company_pref('f_year');
    $fiscal_year = get_fiscalyear($company_year);

    $samedts_data = get_empl_same_tdates($f1, $t1, $_POST['employees_id'], $req_date);

    if ($selected_id != -1) {
        $edit_same_dts = get_empl_alloc_single_comp($selected_id);
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
    


    if (date2sql($_POST['from_date']) > date2sql($_POST['to_date'])) {
        display_error("Selected to date is Invalid!");
        set_focus('no_of_days');
        return false;
    } else {
        
        $result = get_weekly_off();
        $arr = array();
        $counter = array();
        $weekly_off_days = 0;
        $start_date = date2sql($_POST["from_date"]);
        $end_date = date2sql($_POST["to_date"]);
        $totaltime = 0;
        //=====getting leave days weekend status
        $bool = get_leave_wekeend_status($_POST['employees_id'], $_POST["type_leave"], Date("Y"))[0];
         
        //======if weekend status is 1 then do nothing and exicute else part
        
            //fettcihing value form kv_empl_option and aligning in array
            while ($weekly_off = db_fetch($result)) {
                 $arr[$weekly_off['option_name']] = $weekly_off['option_value'];
            }
                
            //calculation of holiday list by using main website dmi
             for ($i = 0; $i < (int) $nos; $i++) {
                    $diff_mon = date_diff2($calendar_leave_list_for_diff[$i]['from_date'], $calendar_leave_list_for_diff[$i]['to_date'], 'd');

                    $diff_between_start_end_dates = (1 + ($diff_mon));
                    $count_leave=0;
                    for ($j = 0; $j <(int)$diff_between_start_end_dates; $j++) {
                    $date = $calendar_leave_list_for_diff[$i]['from_date'];
                    if(strtotime($date. ' + '.$j.' days') >=  strtotime($_POST["from_date"])
                    && strtotime($date. ' + '.$j.' days') <= strtotime($calendar_leave_list_for_diff[$i]['to_date'])
                    && strtotime($date. ' + '.$j.' days') <=  strtotime($_POST["to_date"])){
                        $attendance_date = date("Y-m-d",strtotime($date. ' + '.$j.' days'));
                        $logindetails = get_in_out_time_by_date($session_empl_id, $attendance_date);
                       
                        $check_week_days = date('l',strtotime($date. ' + '.$j.' days'));
                       if(!in_array($check_week_days, explode(',',$arr['weekly_off'])) && !empty($logindetails[0])){
                           $totaltime += $logindetails[2]?$logindetails[2]:0;
                    $counter[$i] = ++$count_leave;
                       }
                    }
                    }

              }
              $total_count = calculate_holiday_leave($counter); 
             $weekly_off_days = calculate_weekly_off($start_date, $end_date,$arr['weekly_off']);
              $diff_mon = date_diff2($start_date, $end_date, 'd');
         for ($i = 0; $i < (int) $diff_mon+1; $i++) {
             $date = $start_date;
             $attendance_date = date("Y-m-d",strtotime($date. ' + '.$i.' days'));
             $logindetails = get_in_out_time_by_date($session_empl_id, $attendance_date);
             $weekday = date('D',strtotime($date. ' + '.$i.' days'));
              if(in_array($weekday, explode(',',$arr['weekly_off'])) && !empty($logindetails[0])){
                           $totaltime += $logindetails[2]?$logindetails[2]:0;
                       }
         }
            
           
             $_POST['no_of_days'] =(($weekly_off_days+$total_count))?(($weekly_off_days+$total_count)):0;
               $_POST['total_time'] = decimal_to_time($totaltime/3600);
            
        
    }
 //display_error($_POST['no_of_days']);
    $Ajax->activate('no_of_days');
$Ajax->activate('total_time');

    if (date2sql($_POST['request_date']) > date2sql($_POST['to_date'])) {
        display_error("Selected to date is Invalid!");
        set_focus('to_date');
        return false;
    }

    $samedts_data = get_empl_same_tdates($f1, $t1, $_POST['employees_id'], $req_date);
    if ($selected_id != -1) {
        $edit_same_dts = get_empl_alloc_single_comp($selected_id);

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


function display_employee_leave_records($selected_parent, $dept_id, $desig_group_id, $desig_id){
    $result = get_employee_leave_records_By_emp_id_comp($selected_parent);
    div_start('bom');
    start_table(TABLESTYLE, "width='60%'");
    $th = array( _("Request Date"), _("Request Type"), _("Reason"),
        _("From Date"), _("To Date"), _("No. of Days"), _("Status"),_("Working Hrs."), _("Edit"), _("Cancel"));
    table_header($th);

    $k = 0;

    if ((db_num_rows($result)) != 0) {

        while ($myrow = db_fetch($result)) {

            alt_table_row_color($k);
            label_cell(sql2date($myrow["request_date"]));
            label_cell("Compensatory Request");
            label_cell($myrow["reason"]);
            label_cell(sql2date($myrow["from_date"]));
            label_cell(sql2date($myrow["to_date"]));
            label_cell($myrow["no_of_days"]);
            global $leave_approval_status;
            label_cell($leave_approval_status[$myrow["status"]]);
            label_cell($myrow["working_hours"]);
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
    if($_POST['no_of_days']){
    add_allocation_comp($_POST['dept_id'], $_POST['desig_group_id'], $_POST['desig_id'], $selected_parent, 0, $_POST['reason'], $today_date, $from_date, $to_date, $_POST['no_of_days'], $_POST['upload_file'], $_POST['filesize'], $_POST['filetype'], $_POST['unique_name'], date('Y-m-d'), $db_value,$_POST['total_time']);
    display_notification(_('Leave Request has been added'));
     $Mode = 'RESET';
    
    }
    else{
        display_error("No of Days Should not be less than 1.");
    }
   
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

    $row = get_empl_alloc_single_comp($selected_component);


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
    update_allocation_comp($selected_component, $_POST['dept_id'], $_POST['desig_group_id'], $_POST['desig_id'], $selected_parent, $_POST['type_leave'], $_POST['reason'], $today_date, $from_date, $to_date, $_POST['no_of_days'], $_POST['upload_file'], $_POST['filesize'], $_POST['filetype'], $_POST['unique_name'], $request_date,$_POST['total_time']);
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

    
    //	}
    return true;
}

if ($Mode == 'Delete') {
    $value = get_empl_alloc_single_comp($selected_id);
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
    display_error('Employee Department & Designation missing (only for user purpose)');
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
    $Ajax->activate('componsentory_request');
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
 start_table(TABLESTYLE, "width='20%'");
   

    if ($selected_id != -1) {
        if ($Mode == 'Edit') {

            //editing a selected component from the link to the line item
            $myrow = get_empl_alloc_single_comp($selected_id);
            $_POST['request_date'] = sql2date($myrow["request_date"]);
            $_POST['type_leave'] = 0;
            $_POST['reason'] = $myrow["reason"];
            $_POST['from_date'] = sql2date($myrow["from_date"]);
            $_POST['to_date'] = sql2date($myrow["to_date"]);
            $_POST['no_of_days'] = $myrow["no_of_days"];
             $_POST['total_time'] = $myrow["working_hours"];
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
   
    hidden('employees_id', $selected_parent);
    hidden('dept_id', $selected_department);
    hidden('desig_group_id', $selected_desig_group);
    hidden('desig_id', $selected_desig_id);
   // hidden('total_time', $_POST['total_time']);
    div_start('leave');
    date_row(_("Request Date") . ":", 'request_date', 30, null, '', '', '', null, true, 'requestDate');

    if ($_POST['employees_id']) {
        $empl_gender = get_employee($_POST['employees_id']);
        //display_error($empl_gender['gender']);
        $gender = $empl_gender['gender'];
    } 
    textarea_row(_("Reason:"), 'reason', null, 30, 4);
    date_row(_("From Date") . ":", 'from_date', 20, null, '', '', '', null, true, "from_date");
    date_row(_("To Date") . ":", 'to_date', 20, null, '', '', '', null, true, "to_date");
    text_row_ex(_("No. of Days :"), 'no_of_days', 10, $_POST['no_of_days'], null, 0, null, null, false, true, 'num_day');
    text_row_ex(_("Working  Hours:"), 'total_time', 10, $_POST['total_time'], null, "00:00:00", null, null, false, true, 'time');
    file_row(_("Attached File") . ":", 'upload_file', 'upload_file');
    
  
   div_end();
    
      start_table(TABLESTYLE_NOBORDER);
    start_row();
    br();
    label_cells($label,submit_add_or_update_center($selected_id == -1, '', 'both'));
    end_row();
    end_table();
    end_form();
}
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

