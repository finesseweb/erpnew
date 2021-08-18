<?php
/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */

$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/includes/ui.inc");

$version_id = get_company_prefs('version_id');

$js = '';
if ($version_id['version_id'] == '2.4.1') {
    if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);

    if (user_use_date_picker())
        $js .= get_js_date_picker();
}else {
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
}

include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
page(_("Attendance Entry"));
?>
<html lang="en">
    <head>
        <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js" ?>"></script>
        <script>
            $(function () {
                $("#datepicker").datepicker();
            });
        </script>
    </head>
    <body>   
        <?php
        $montharr = array(1,2);
       /* $emplarr = array('EMPL-F-002','EMPL-F-001','EMPL-F-009','EMPL-F-005','EMPL-F-006','EMPL-F-003','EMPL-F-004','EMPL-F-007','EMPL-F-008','EMPL-F-010','EMPL-S-008','EMPL-S-006','EMPL-S-001','EMPL-S-012','EMPL-S-013','EMPL-S-010','EMPL-S-011','EMPL-S-09',);*/
       /*for($i=5; $i<=6; $i++){
            $sql = "update fa_kv_empl_attendancee set `".$i."`= 'CL' where empl_id = 'EMP-S-012' AND month = 1 ANd year = 4  ";
            echo $sql;
           db_query($sql, "could not get sales type");
        }*/
        $compare_date =date('Y-m-d');
        if (isset($_POST['attendance_date'])){
            $compare_date = $_POST['attendance_date'];
        }
        
        $emp_id = '';
        $leave_result_type = '';
        $from_date = '';
        $to_date = '';
        $new_item = get_post('selected_id') == '' || get_post('cancel');
        $leave_sql = "SELECT * FROM " . TB_PREF . "kv_allocation_request WHERE inactive = 0 and status = 2";
        $leave_res = db_query($leave_sql);
        while ($leave_emp_result = db_fetch($leave_res)) {
            $emp_id .= $leave_emp_result['employees_id'] . ",";
            $sql3 = "SELECT empl_firstname,empl_id FROM " . TB_PREF . "kv_empl_info WHERE empl_id=" . db_escape($leave_emp_result['employees_id']) . " ";
            $result3 = db_query($sql3, "could not get sales type");
            $from_date .= $leave_emp_result['from_date'] . ",";
            $to_date .= $leave_emp_result['to_date'] . ",";
            $row3 = db_fetch($result3);

            $dept_sql = "SELECT description FROM " . TB_PREF . "kv_departments WHERE id='" . $leave_emp_result['dept_id'] . "'";
            $dept_res = db_query($dept_sql);
            $dept_result = db_fetch_row($dept_res);

            $type_leave_sql = "SELECT leave_type FROM " . TB_PREF . "kv_type_leave_master WHERE type_id = '" . $leave_emp_result['type_leave'] . "'";
            $type_leave_res = db_query($type_leave_sql);
            $type_leave_result = db_fetch_row($type_leave_res);
            $leave_result_type .= $type_leave_result[0] . ",";
        }
        if (isset($_GET['selected_id'])) {
            $_POST['selected_id'] = $_GET['selected_id'];
        }

        $selected_id = get_post('selected_id');

        if (list_updated('selected_id')) {

            $_POST['empl_id'] = $selected_id = get_post('selected_id');
            $Ajax->activate('details');
        }

        $company_year = get_company_pref('f_year');

        $fiscal_year = get_fiscalyear($company_year);

        $financial_year_start_month = $fiscal_year['1'];
        $financial_year_end_month = $fiscal_year['2'];
//display_error($_POST['attendance_date']); 

        echo '<input type="hidden" name="fiscal_year_start_month" id="fiscal_year_start_month" value="' . $financial_year_start_month . '"/>';
        echo '<input type="hidden" name="fiscal_year_end_month" id="fiscal_year_end_month" value="' . $financial_year_end_month . '" />';
        if (isset($_POST['addupdate'])) {
            $input_error = 0;
            $employees = array();
            foreach ($_POST as $empls => $val) {

                if (substr($empls, 0, 5) == 'Empl_')
                    $employees[] = substr($empls, 5);
            }
            
            //current
           
            

            $empl_count = get_dep_employees_count($_POST['selected_id']);
            //display_notification($empl_count);
            $employees = array_values($employees);

            $attend_count = count($employees);
            //display_notification($attend_count); die;

         /*   if ($empl_count != $attend_count) {
                display_error(_("$empl_count must enter missing field $attend_count"));
                $input_error = 1;
                return false;
            }*/

            if ($input_error == 0) {
                $attendance_date = strtotime($_POST['attendance_date']);
                $month = date("m", $attendance_date);

                $day = date("d", $attendance_date);

                $year = get_fiscal_year_id_from_date($_POST['attendance_date']);
                //print_r($employees);
                foreach ($employees as $empl_id) {
                  
                    if (db_has_day_attendancee($empl_id, $month, $year)) {
                        update_employee_attendance($_POST['Empl_' . $empl_id], $empl_id, $month, $year, $day);
                    } else {
                        add_employee_attendance($_POST['Empl_' . $empl_id], $empl_id, $month, $year, $day, $_POST['selected_id']);
                    }
                }
                display_notification("Attendance Register Saved Successfully");
            }
            $new_role = true;
            //clear_data();
            $Ajax->activate('_page_body');
        }

//function clear_data(){	unset($_POST);	}

        start_form(true);

        if (db_has_employees()) {
            if (isset($_POST['selected_id']) && $_POST['selected_id'] > 0) {
                $_POST['selected_id'] = input_num('selected_id');
            }
            start_table(TABLESTYLE2);
            start_row();
            date_cells(_("Date") . ":", 'attendance_date', null, null, 0, 0, 0, null,true);
            department_list_cells(_("Select a Department: "), 'selected_id', null, _('No Department'), true, check_value('show_inactive'));
            echo '<td><input type="button" value="check Leaves" onclick="selectLeavesType()"  id="auto_click" /></td>';

            $new_item = get_post('selected_id') == '';
            end_row();
            end_table();

            if (get_post('_show_inactive_update')) {
                $Ajax->activate('selected_id');
                $attendance_date = get_post('attendance_date');
                set_focus('selected_id');
            }
            if (list_updated('attendance_date')) {
                $attendance_date = get_post('attendance_date');
                $Ajax->activate('totals_tbl');
            }


            div_start('totals_tbl');

            $selected_id = get_post('selected_id');
            $attendance_date = get_post('attendance_date');
            $Ajax->activate('_page_body');
            if (!$selected_id)
                $selected_id = 0;
            $day_absentees = array();

            //echo " <center> Select the Absentees only ...</center>";
            br();
            $disabled = '';
            //display_error($attendance_date."======". Today());
            if (strtotime($attendance_date) > strtotime(Today())) {
                display_warning("You can't Enter Yet to born day Attendance!");
                $disabled = 'disabled';
            }


            $atten_date = date2sql($_POST['attendance_date']);
            if ($_POST['selected_id'] == '') {
                $leave_sql = "SELECT * FROM " . TB_PREF . "kv_allocation_request WHERE inactive = 0  and approved_from_date <= '$atten_date' and approved_to_date >= '$atten_date' and status = 2";
            } else {

                $leave_sql = "SELECT * FROM " . TB_PREF . "kv_allocation_request WHERE inactive = 0  and approved_from_date <= '$atten_date' and approved_to_date >= '$atten_date' and status = 2 and dept_id='" . $_POST['selected_id'] . "'";
            }

            $leave_res = db_query($leave_sql);
            while ($leave_emp_result = db_fetch($leave_res)) {
                $sql3 = "SELECT empl_firstname,empl_id FROM " . TB_PREF . "kv_empl_info WHERE empl_id=" . db_escape($leave_emp_result['employees_id']) . " ";
                $result3 = db_query($sql3, "could not get sales type");

                $row3 = db_fetch($result3);

                $dept_sql = "SELECT description FROM " . TB_PREF . "kv_departments WHERE id='" . $leave_emp_result['dept_id'] . "'";
                $dept_res = db_query($dept_sql);
                $dept_result = db_fetch_row($dept_res);

                $type_leave_sql = "SELECT leave_type FROM " . TB_PREF . "kv_type_leave_master WHERE type_id = '" . $leave_emp_result['type_leave'] . "'";
                $type_leave_res = db_query($type_leave_sql);
                $type_leave_result = db_fetch_row($type_leave_res);
                ?>
                        <!-- 	<marquee style="color:red;font-size:20px;">Mr/Ms.<?php echo '   ' . $row3['empl_firstname'] . ' ( ' . $row3['empl_id'] . ' ) ' . ' of ' . $dept_result[0] . '  department has applied a ' . $type_leave_result[0] . ' for today'; ?></marquee>  -->
                <?php
            }
            /* while($emp_result = db_fetch($leaves_res1)) {

              display_error('dsds');

              //for($i=$emp_result['from_date'];$i<=$_POST['attendance_date'];$i++){
              //display_error($emp_result['from_date']);
              $f_date=$emp_result['approved_from_date'];
              $t_date=$emp_result['approved_to_date'];

              //while($f_date<=$t_date){
              //if($emp_result['to_date'] <= $_POST['attendance_date'] )
              //{
              //display_error($f_date);
              //if($f_date == $_POST['attendance_date']){
              $sql3 = "SELECT empl_firstname,empl_id FROM ".TB_PREF."kv_empl_info WHERE empl_id=".db_escape($emp_result['employees_id'])."LIMIT 1";
              $result3 = db_query($sql3, "could not get sales type");

              $row3 = db_fetch_row($result3);

              $emp_name= $row3[0];
              if($emp_result['type_leave'] == 1){
              $leave = 'Casual Leave';
              }
              else if($emp_result['type_leave'] == 2){
              $leave = 'Medical Leave';
              }
              else if($emp_result['type_leave'] == 3){
              $leave = 'Vacation Leave';
              }
              else {
              $leave = '';
              }
              ?>

              <!--  <marquee style="color:red;font-size:20px;">Mr/Ms.<?php echo '   '.$emp_name.' ( '.$row3[1].' ) '.' of '.$emp_result['department'].'  department has applyed a '.$leave.' for today';?></marquee>  -->
              <?php
              //}
              $f_date = date ("m/d/Y", strtotime ($f_date ."+1 days"));
              //}
              } */
          
            start_table(TABLESTYLE);

            //table_section_title(_("Employees List"));
            //echo '<tr> <th class="tableheader"> Empl ID</th> <th class="tableheader"> Employee Name </th> <th class="tableheader"> Present</th> <th class="tableheader"> Absent </th> <th class="tableheader"> On Duty</th> <th class="tableheader"> Half Day</th> </tr>' ;
            echo '<tr> <th class="tableheader">Empl ID</th><th class="tableheader"> Employee Name </th><th class="tableheader"> Present</th> <th class="tableheader"> Absent </th><th class="tableheader">Half Day</th> <th class="tableheader">CL</th><th class="tableheader">HCL</th><th class="tableheader">VL</th> <th class="tableheader">OD</th> <th class="tableheader">ML</th> <th class="tableheader">EL</th> <th class="tableheader">SCL</th> <th class="tableheader">H</th> <th class="tableheader">HP</th> <th class="tableheader">CO</th> <th class="tableheader">COP</th> <th class="tableheader">MTL</th><th class="tableheader">PTL</th><th class="tableheader">OTP</th></tr>';
            if ($selected_id == 0){
                label_cell('select department to note the attendence');
            }
            else {
        
                $selected_empl = kv_get_employees_based_on_dept($selected_id);
                   
                $day_absentees = get_employees_attendances($attendance_date, $_POST['selected_id']);

                $current_date = date('Y-m-d');
                echo '<input type="hidden" name="current_date" id="current_date" value="' . $current_date . '">';

                $i = 1;
                //display_notification(json_encode($day_absentees));

                while ($row = db_fetch_assoc($selected_empl)) {

                   
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    
                    $empl_data_result = get_employee_data($row['empl_id'], $selected_id);
                    

                    echo '<input type="hidden" name="employee_type" id="employee_type' . $row['empl_id'] . '" value="' . $empl_data_result['employee_type'] . '" />';
                    $employee_gender = get_employee_gender($row['empl_id']);
                    $leaves = get_leaves_data($selected_id, $empl_data_result['desig_group'], $empl_data_result['desig'], $fiscal_year['0']);
                    $attend_date = strtotime($_POST['attendance_date']);

                    $month = date("m", $attend_date);
                    $year = get_fiscal_year_id_from_date($_POST['attendance_date']);

                    $empl_leave_count = get_employee_leave_count($month, $year, $row['empl_id']);
                    $tot_days = 31;
                    $empl_casual_leaves_count = 0;
                    for ($j = 1; $j <= $tot_days; $j++) {
                        if ($empl_leave_count[$j] == 'CL') {
                            $empl_casual_leaves_count ++;
                        }
                    }
                    $empl_lv_ct_yrly = get_employee_leave_count_fiscalyear($year, $row['empl_id']);
                    $empl_overall_medical_leave_count_yearly = get_employee_medical_leave_count($year, $row['empl_id']);


                    $empl_casual_leaves_count_yearly = 0;
                    $empl_vacation_leaves_count_yearly = 0;
                    $empl_special_casual_leaves_count_yearly = 0;
                    $empl_medical_leaves_count_yearly = 0;
                    $empl_earned_leaves_count_yearly =0;
                    $empl_maternity_leaves_count_yearly = 0;
                    $empl_paternity_leaves_count_yearly = 0;
                    //recent
                    $empl_half_casual_leaves_count_yearly = 0;
                    while ($empl_leave_count_yearly = db_fetch($empl_lv_ct_yrly)) {
                        for ($k = 1; $k <= $tot_days; $k++) {
                            if ($empl_leave_count_yearly[$k] == 'CL') {
                                $empl_casual_leaves_count_yearly ++;
                            }
                            //recent
                            if ($empl_leave_count_yearly[$k] == 'HCL') {
                                $empl_half_casual_leaves_count_yearly +=0.5;
                            }
                            if ($empl_leave_count_yearly[$k] == 'VL') {
                                $empl_vacation_leaves_count_yearly ++;
                            }
                            if ($empl_leave_count_yearly[$k] == 'ML') {

                                $empl_medical_leaves_count_yearly ++;
                            }
                            if ($empl_leave_count_yearly[$k] == 'EL') {

                                $empl_earned_leaves_count_yearly ++;
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
                        }
                    }
                    //$tot_utilized_casual_leaves = $leaves['no_of_cls']-$empl_casual_leaves_count_yearly;

                    $tot_utilized_casual_leaves = $leaves['no_of_cls'] - ($empl_casual_leaves_count_yearly + ($empl_half_casual_leaves_count_yearly / 2));
                    $tot_utilized_vacation_leaves = $leaves['no_of_pls'] - $empl_vacation_leaves_count_yearly;
                    $tot_utilized_medical_leaves = $leaves['no_of_medical_ls'] - $empl_medical_leaves_count_yearly;
                    $tot_utilized_earned_leaves = $leaves['no_of_el'] - $empl_earned_leaves_count_yearly;
       
                    if ($employee_gender['0'] == 1) {
                        $tot_utilized_special_casual_leaves = $leaves['no_of_spl_cls'] - $empl_special_casual_leaves_count_yearly;
                    } else if ($employee_gender['0'] == 2) {
                        $tot_utilized_special_casual_leaves = $leaves['no_of_spl_cls_female'] - $empl_special_casual_leaves_count_yearly;
                    }


                    $tot_utilized_maternity_leaves = $leaves['no_of_mat_ls'] - $empl_maternity_leaves_count_yearly;
                    $tot_utilized_paternity_leaves = $leaves['no_of_patern_ls'] - $empl_paternity_leaves_count_yearly;
                    $show_leave .= $row['empl_id'] . ",";
                    //Monthly count of leaves
                    echo '<input type="hidden" name="employee_gender" id="employee_gender' . $row['empl_id'] . '"  value="' . $employee_gender['0'] . '"/>';
                    echo '<input type="hidden" name="empl_tot_cl_count" id="empl_tot_cl_count' . $row['empl_id'] . '" value="' . $tot_utilized_casual_leaves . '" />';
                    echo '<input type="hidden" name="empl_tot_vl_count" id="empl_tot_vl_count' . $row['empl_id'] . '" value="' . $tot_utilized_vacation_leaves . '"/>';
                    echo '<input type="hidden" name="empl_tot_ml_count" id="empl_tot_ml_count' . $row['empl_id'] . '" value="' . $tot_utilized_medical_leaves . '"/>';
                     echo '<input type="hidden" name="empl_tot_el_count" id="empl_tot_ml_count' . $row['empl_id'] . '" value="' . $tot_utilized_earned_leaves . '"/>';
                    echo '<input type="hidden" name="empl_tot_ptl_count" id="empl_tot_ptl_count' . $row['empl_id'] . '" value="' . $tot_utilized_paternity_leaves . '"/>';
                    echo '<input type="hidden" name="empl_tot_scl_count" id="empl_tot_scl_count' . $row['empl_id'] . '" value="' . $tot_utilized_special_casual_leaves . '"/>';
                    echo '<input type="hidden" name="empl_tot_mtl_count" id="empl_tot_mtl_count' . $row['empl_id'] . '" value="' . $tot_utilized_maternity_leaves . '"/>';

                    //Yearly count of leaves
                    echo '<input type="hidden" name="employee_casual_leave_count" id="employee_casual_leave_count' . $row['empl_id'] . '" value="' . $empl_casual_leaves_count . '" />';
                    echo '<input type="hidden" name="employee_casual_leave_count_yearly" id="employee_casual_leave_count_yearly' . $row['empl_id'] . '" value="' . $empl_casual_leaves_count_yearly . '" />';
                    echo '<input type="hidden" name="employee_half_casual_leaves_count_yearly" id="employee_half_casual_leaves_count_yearly' . $row['empl_id'] . '" value="' . $empl_half_casual_leaves_count_yearly . '" />';
                    echo '<input type="hidden" name="employee_special_leave_count_yearly" id="employee_special_casual_leave_count_yearly' . $row['empl_id'] . '" value="' . $empl_special_casual_leaves_count_yearly . '"/>';
                    echo '<input type="hidden" name="employee_vacation_leave_count_yearly" id="employee_vacation_leave_count_yearly' . $row['empl_id'] . '" value="' . $empl_vacation_leaves_count_yearly . '"/>';
                    echo '<input type="hidden" name="employee_maternity_leave_count_yearly" id="employee_maternity_leave_count_yearly' . $row['empl_id'] . '" value="' . $empl_maternity_leaves_count_yearly . '"/>';
                    echo '<input type="hidden" name="employee_paternity_leave_count_yearly" id="employee_paternity_leave_count_yearly' . $row['empl_id'] . '" value="' . $empl_paternity_leaves_count_yearly . '"/>';
                    echo '<input type="hidden" name="employee_medical_leave_count_yearly" id="employee_medical_leave_count_yearly' . $row['empl_id'] . '" value="' . $empl_medical_leaves_count_yearly . '"/>';


                    //Total Leaves based on department,designationgroup,designation
                    echo '<input type="hidden" name="casual_leaves" id="casual_leaves' . $row['empl_id'] . '" value="' . $leaves['no_of_cls'] . '"/>';

                    echo '<input type="hidden" name="vacation_leaves" id="vacation_leaves' . $row['empl_id'] . '" value="' . $leaves['no_of_pls'] . '"/>';


                    echo '<input type="hidden" name="medical_leaves" id="medical_leaves' . $row['empl_id'] . '" value="' . $leaves['no_of_medical_ls'] . '"/>';
                    echo '<input type="hidden" name="earned_leaves" id="earned_leaves' . $row['empl_id'] . '" value="' . $leaves['no_of_el'] . '"/>';
                    if ($employee_gender['0'] == 1) {
                        echo '<input type="hidden" name="special_casual_leaves" id="special_casual_leaves' . $row['empl_id'] . '" value="' . $leaves['no_of_spl_cls'] . '"/>';
                    } else if ($employee_gender['0'] == 2) {
                        echo '<input type="hidden" name="special_casual_leaves" id="special_casual_leaves' . $row['empl_id'] . '" value="' . $leaves['no_of_spl_cls_female'] . '"/>';
                    }
                    echo '<input type="hidden" name="maternity_leaves" id="maternity_leaves' . $row['empl_id'] . '" value="' . $leaves['no_of_mat_ls'] . '"/>';
                    echo '<input type="hidden" name="paternity_leaves" id="paternity_leaves' . $row['empl_id'] . '" value="' . $leaves['no_of_patern_ls'] . '"/>';

                    echo '<input type="hidden" name="employee_joining_date" id="joining_date' . $row['empl_id'] . '" value="' . $row['joining'] . '" />';
                    $joining_date = $row['joining'];
                    $effectiveDate = date('Y-m-d', strtotime("+12 months", strtotime($joining_date)));
                    echo '<input type="hidden" name="employee_year_completion_date" id="employee_year_completion_date' . $row['empl_id'] . '" value="' . $effectiveDate . '"/>';
                    //$end = date('Y-m-d', strtotime('+5 years'));
                    $month_absentees = get_employees_monthly_attendances($_POST['selected_id'], $row['empl_id'], $attendance_date);

                    echo '<input type="hidden" name="employee_type" id="employee_type' . $row['empl_id'] . '" value="' . $empl_data_result['empl_type'] . '" />';
                    //Employee total medical leave terms count
                    echo '<input type="hidden" name="employee_overall_medical_leave_count" id="employee_overall_medical_leave_count' . $row['empl_id'] . '" value="' . $empl_overall_medical_leave_count_yearly['count'] . '"/>';


                    echo '<tr> <td>' . $row['empl_id'] . '</td> <td>' . kv_get_empl_name($row['empl_id']) . '</td><td>';
                        /*
                         * 
                         * 
                         * search query generated by ashutosh
                         * on 04-04-2019 for automatic leave
                         * updation
                         * 
                         * 
                         */
                    
                        $date = date('Y-m-d',strtotime($compare_date));
                        $leave_query = array(
                            'employees_id = ' => db_escape($row['empl_id'])." AND ",
                            'approved_from_date >=' => db_escape($date)." AND ", 
                            'approved_to_date <=' => db_escape($date)." AND ",
                            'cancel_status = ' => '0'.' AND ',
                            'status = ' => '2'.' AND '
                        );
                    
                        if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "P") {

                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "P", "selected", false, $disabled);
                    } else {
                        $flag = 0;
                        $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                        $holiday_res = db_query($holiday_sql);
                        while ($holiday_result = db_fetch($holiday_res)) {

                            if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                                $flag = 1;
                                if ($flag == 1) {
                                    echo kv_radio(" ", 'Empl_' . $row['empl_id'], "P", null, false, "disabled='disabled'");
                                }
                            }
                        }if ($flag == 0) {
                            echo kv_radio(" ", 'Empl_' . $row['empl_id'], "P", "selected", false, $disabled);
                        }
                    }

                    echo '</td><td>';

                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "A") {
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "A", "selected", false, $disabled);
                    } else {
                        $flag = 0;
                        $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                        $holiday_res = db_query($holiday_sql);
                        while ($holiday_result = db_fetch($holiday_res)) {

                            if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                                $flag = 1;
                                if ($flag == 1) {
                                    echo kv_radio(" ", 'Empl_' . $row['empl_id'], "A", null, false, "disabled='disabled'", 'Empl_' . $row['empl_id'], 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                                }
                            }
                        }if ($flag == 0) {

                            echo kv_radio(" ", 'Empl_' . $row['empl_id'], "A", null, false, $disabled, 'Empl_' . $row['empl_id'], 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                        }
                    }
                    echo '</td><td>';
                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "HD")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "HD", "selected", false, $disabled);
                    else
                        $flag = 0;
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    while ($holiday_result = db_fetch($holiday_res)) {

                        if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                            $flag = 1;
                            if ($flag == 1) {
                                echo kv_radio(" ", 'Empl_' . $row['empl_id'], "HD", null, false, "disabled='disabled'", 'Empl_' . $row['empl_id'], 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                            }
                        }
                    }if ($flag == 0) {
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "HD", null, false, $disabled, 'Empl_' . $row['empl_id'], 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                    }
                    echo '</td><td>';

                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "CL")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "CL", "selected", false, $disabled);
                    else
                        $flag = 0;
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    while ($holiday_result = db_fetch($holiday_res)) {

                        if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                            $flag = 1;
                            if ($flag == 1) {
                                echo kv_radio(" ", 'Empl_' . $row['empl_id'], "CL", null, false, "disabled='disabled'", 'Empl_' . $row['empl_id'] . '_CL', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                            }
                        }
                    }if ($flag == 0) {
                        
                        /*
                         * 1. SELECT from kv allocation 
                         * 2. CONDITION ON date ,approved_from, approved_to,empl_id,status = 1, cancel_status = 0,and type_leave
                         * 3. IF ROW found then Radion Should be auto maticaly Selected 
                         * 
                         */
                        unset($leave_query['type_leave = ']);
                        $leave_query['type_leave = '] = 1;
                        $is_approved = get_empl_approved_leaves($leave_query, 'kv_allocation_request');
                        $selected = $is_approved?'selected':null;

                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "CL",$selected, false, $disabled, 'Empl_' . $row['empl_id'] . '_CL', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                    }

                    echo '</td><td>';
                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "HCL")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "HCL", "selected", false, $disabled);
                    else
                        $flag = 0;
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    while ($holiday_result = db_fetch($holiday_res)) {

                        if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                            $flag = 1;
                            if ($flag == 1) {
                                echo kv_radio(" ", 'Empl_' . $row['empl_id'], "HCL", null, false, "disabled='disabled'", 'Empl_' . $row['empl_id'] . '_HCL', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                            }
                        }
                    }if ($flag == 0) {
                        
                        
                           unset($leave_query['type_leave = ']);
                        $leave_query['type_leave = '] = 9;//=====9 is HALF DAY CL IN DATABASE ====//
                        $is_approved = get_empl_approved_leaves($leave_query, 'kv_allocation_request');
                        $selected = $is_approved?'selected':null;
                        
                        
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "HCL", $selected, false, $disabled, 'Empl_' . $row['empl_id'] . '_HCL', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                    }
                    echo '</td><td>';
                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "VL")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "VL", "selected", false, $disabled);
                    else
                        $flag = 0;
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    while ($holiday_result = db_fetch($holiday_res)) {

                        if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                            $flag = 1;
                            if ($flag == 1) {
                                echo kv_radio(" ", 'Empl_' . $row['empl_id'], "VL", null, false, "disabled='disabled'", 'Empl_' . $row['empl_id'] . '_VL', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                            }
                        }
                    }if ($flag == 0) {
                        
                         unset($leave_query['type_leave = ']);
                        $leave_query['type_leave = '] = 3;//=====3 is Vacational leave IN DATABASE ====//
                        $is_approved = get_empl_approved_leaves($leave_query, 'kv_allocation_request');
                        $selected = $is_approved?'selected':null;
                        
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "VL", $selected, false, $disabled, 'Empl_' . $row['empl_id'] . '_VL', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                    }

                    echo '</td><td>';

                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "OD")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "OD", "selected", false, $disabled);
                    else
                        $flag = 0;
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    while ($holiday_result = db_fetch($holiday_res)) {

                        if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                            $flag = 1;
                            if ($flag == 1) {
                                echo kv_radio(" ", 'Empl_' . $row['empl_id'], "OD", null, false, "disabled='disabled'", 'Empl_' . $row['empl_id'], 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                            }
                        }
                    }if ($flag == 0) {
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "OD", null, false, $disabled, 'Empl_' . $row['empl_id'], 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                    }
                    echo '</td><td>';
//===========option button for medical leaves 
                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "ML")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "ML", "selected", false, $disabled);
                    else
                        $flag = 0;
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    while ($holiday_result = db_fetch($holiday_res)) {

                        if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                            $flag = 1;
                            if ($flag == 1) {
                                echo kv_radio(" ", 'Empl_' . $row['empl_id'], "ML", null, false, "disabled='disabled'", 'Empl_' . $row['empl_id'] . '_ML', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                            }
                        }
                    }if ($flag == 0) {
                         
                        unset($leave_query['type_leave = ']);
                        $leave_query['type_leave = '] = 2;//=====2 is Medical leave IN DATABASE ====//
                        $is_approved = get_empl_approved_leaves($leave_query, 'kv_allocation_request');
                        $selected = $is_approved?'selected':null;
                        
                        
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "ML", $selected, false, $disabled, 'Empl_' . $row['empl_id'] . '_ML', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                    }
                    echo '</td><td>';
//=======option button of newly added earned leaves                    
                       if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "EL")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "EL", "selected", false, $disabled);
                    else
                        $flag = 0;
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    while ($holiday_result = db_fetch($holiday_res)) {

                        if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                            $flag = 1;
                            if ($flag == 1) {
                                echo kv_radio(" ", 'Empl_' . $row['empl_id'], "EL", null, false, "disabled='disabled'", 'Empl_' . $row['empl_id'] . '_EL', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                            }
                        }
                    }if ($flag == 0) {
                        
                         unset($leave_query['type_leave = ']);
                        $leave_query['type_leave = '] = 11;//=====11 is Earned leave IN DATABASE ====//
                        $is_approved = get_empl_approved_leaves($leave_query, 'kv_allocation_request');
                        $selected = $is_approved?'selected':null;
                        
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "EL", $selected, false, $disabled, 'Empl_' . $row['empl_id'] . '_EL', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                    }
                    echo '</td><td>';
                    
//========option button for special cause leaves                     
                    
                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "SCL")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "SCL", "selected", false, $disabled);
                    else
                        $flag = 0;
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    while ($holiday_result = db_fetch($holiday_res)) {

                        if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                            $flag = 1;
                            if ($flag == 1) {
                                echo kv_radio(" ", 'Empl_' . $row['empl_id'], "SCL", null, false, "disabled='disabled'", 'Empl_' . $row['empl_id'] . '_SCL', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                            }
                        }
                    }if ($flag == 0) {
                        
                        
                           unset($leave_query['type_leave = ']);
                        $leave_query['type_leave = '] = 4;//=====4 is Special Cause Leave IN DATABASE ====//
                        $is_approved = get_empl_approved_leaves($leave_query, 'kv_allocation_request');
                        $selected = $is_approved?'selected':null;

                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "SCL", $selected, false, $disabled, 'Empl_' . $row['empl_id'] . '_SCL', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                    
                        
                        
                    }

                    echo '</td><td>';

                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "H")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "H", "selected", false, $disabled);
                    else
                        $flag = 0;
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    while ($holiday_result = db_fetch($holiday_res)) {

                        if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                            $flag = 1;
                            if ($flag == 1) {

                                echo kv_radio(" ", 'Empl_' . $row['empl_id'], "H", "selected", false, $disabled, 'Empl_' . $row['empl_id'], 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                            }
                        }
                    }if ($flag == 0) {

                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "H", null, false, $disabled, 'Empl_' . $row['empl_id'], 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                    }

                    echo '</td><td>';

                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "HP")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "HP", "selected", false, $disabled);
                    else
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "HP", null, false, $disabled);
                    echo '</td><td>';

                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "WO")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "WO", "selected", false, $disabled, 'Empl_' . $row['empl_id'], 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                    else
                        $flag = 0;
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    while ($holiday_result = db_fetch($holiday_res)) {

                        if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                            $flag = 1;
                            if ($flag == 1) {

                                echo kv_radio(" ", 'Empl_' . $row['empl_id']."WO", "WO", null, false, "disabled='disabled'",'Empl_' . $row['empl_id'] . '_WO');
                            }
                        }
                    } if ($flag == 0) {

                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "WO", null, false, $disabled,'Empl_' . $row['empl_id'] . '_WO');
                    }
                    echo '</td><td>';

                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "WOP")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "WOP", "selected", false, $disabled);
                    else
                        $flag = 0;
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    while ($holiday_result = db_fetch($holiday_res)) {

                        if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                            $flag = 1;
                            if ($flag == 1) {
                                echo kv_radio(" ", 'Empl_' . $row['empl_id'], "WOP", null, false, "disabled='disabled'", 'Empl_' . $row['empl_id'].'_WOP', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                            }
                        }
                    }if ($flag == 0) {
                        
                         unset($leave_query['type_leave = ']);
                        $leave_query['type_leave = '] = 7;//=====7 is Weekly of Leave IN DATABASE ====//
                        $is_approved = get_empl_approved_leaves($leave_query, 'kv_allocation_request');
                        $selected = $is_approved?'selected':null;
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "WOP", $selected, false, $disabled, 'Empl_' . $row['empl_id'].'_WOP', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                    }
                    echo '</td><td>';

                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "MTL")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "MTL", "selected", false, $disabled);
                    else
                        $flag = 0;
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    while ($holiday_result = db_fetch($holiday_res)) {

                        if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                            $flag = 1;
                            if ($flag == 1) {
                                echo kv_radio(" ", 'Empl_' . $row['empl_id'], "MTL", null, false, "disabled='disabled'", 'Empl_' . $row['empl_id'] . '_MTL', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                            }
                        }
                    }if ($flag == 0) {
                        
                        unset($leave_query['type_leave = ']);
                        $leave_query['type_leave = '] = 5;//=====5 is Maternity Leave IN DATABASE ====//
                        $is_approved = get_empl_approved_leaves($leave_query, 'kv_allocation_request');
                        $selected = $is_approved?'selected':null;
                        
                        
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "MTL", $selected, false, $disabled, 'Empl_' . $row['empl_id'] . '_MTL', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                    }
                    echo '</td><td>';

                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "PTL")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "PTL", "selected", false, $disabled);
                    else
                        $flag = 0;
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    while ($holiday_result = db_fetch($holiday_res)) {

                        if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                            $flag = 1;
                            if ($flag == 1) {
                                echo kv_radio(" ", 'Empl_' . $row['empl_id'], "PTL", null, false, "disabled='disabled'", 'Empl_' . $row['empl_id'] . '_PTL', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                            }
                        }
                    }if ($flag == 0) {
                        
                        unset($leave_query['type_leave = ']);
                        $leave_query['type_leave = '] = 6;//=====6 is Paternity Leave IN DATABASE ====//
                        $is_approved = get_empl_approved_leaves($leave_query, 'kv_allocation_request');
                        $selected = $is_approved?'selected':null;
                        
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "PTL", $selected, false, $disabled, 'Empl_' . $row['empl_id'] . '_PTL', 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                    
                        
                        
                    }
                    echo '</td><td>';

                    if (array_key_exists($row['empl_id'], $day_absentees) && $day_absentees[$row['empl_id']] == "OTP")
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "OTP", "selected", false, $disabled);
                    else
                        $flag = 0;
                    $holiday_sql = "SELECT * FROM " . TB_PREF . "kv_holiday_master WHERE fisc_year = '" . $fiscal_year['0'] . "' AND inactive = 0";
                    $holiday_res = db_query($holiday_sql);
                    while ($holiday_result = db_fetch($holiday_res)) {

                        if ((date2sql($_POST['attendance_date']) >= $holiday_result['from_date']) && (date2sql($_POST['attendance_date']) <= $holiday_result['to_date'])) {
                            $flag = 1;
                            if ($flag == 1) {
                                echo kv_radio(" ", 'Empl_' . $row['empl_id'], "OTP", null, false, "disabled='disabled'", 'Empl_' . $row['empl_id'], 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                            }
                        }
                    }if ($flag == 0) {
                        echo kv_radio(" ", 'Empl_' . $row['empl_id'], "OTP", null, false, $disabled, 'Empl_' . $row['empl_id'], 'onchange="select_value(this.value,' . $row['empl_id'] . ')"');
                    }
                    echo '</td></tr>';
                }
            }
            end_table();
            ?>
            <script type='text/javascript'>
                //alert('hello');
                //$('#auto_click').trigger('click');
            </script>
            <?php
            br();
            if (strtotime($attendance_date) > strtotime(Today())) {
                
            } else {
                submit_center('addupdate', _("Submit Attendance"), true, '', 'default', false);
            }
            div_end();
        } else {
            check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));
        }

        end_form();
        end_page();
        ?>
    </body>
    <script>
        
        
        

        function selectLeavesType() {
            data = $('[name=attendance_date]').val();
            var emp_id = '<?php echo $emp_id ?>'.split(",");
            
            var leave_type = '<?php echo $leave_result_type ?>'.split(",");
            
            var from_date = '<?php echo $from_date; ?>'.split(",");
            var to_date = '<?php echo $to_date; ?>'.split(",");
            var data1 = data.split('/');
            var today = data1[2]+"-"+data1[0]+"-"+data1[1];
            //var today = currentDate();
            for (i = 0; i < emp_id.length - 1; i++)
            {
                var start = new Date(from_date[i]);
                var end = new Date(to_date[i]);
                var now = new Date(today);
                shortForm = leaveTypeShortForm(leave_type[i]);
                /*  if (isNaN(starttimestamp) == false || isNaN(Endtimestamp) == false || isNaN(today) == false )
                 {*/

                /*}*/
                //alert(from_date[i]+"===="+today+"==="+to_date[i]);
                
               if (start <= now && end >= now)
                   if(shortForm!='WO' && shortForm!='WOP')
                    $('#Empl_' + emp_id[i] + '_' + shortForm).attr('checked', 'true');
                    else if(shortForm=='WO')
                       $('#Empl_' + emp_id[i] + '_' + shortForm).attr('checked', 'true');


            }
        }

        function leaveTypeShortForm(leave_type)
        {
            if (leave_type == 'Casual Leave')
                return 'CL';
            else if (leave_type == 'Medical Leave')
                return 'ML';
            else if (leave_type == 'Earned Leave')
                return 'EL';
            else if (leave_type == 'Vacation Leave')
                return 'VL';
            else if (leave_type == 'Special Casual Leave')
                return 'SCL';
            else if (leave_type == 'Maternity Leave')
                return 'MTL';
            else if (leave_type == 'Paternity leave')
                return 'PTL';
            else if (leave_type == 'Half Day CL')
                return 'HCL'
            else if(leave_type=='Compensatory Leave')
                return 'WO'

        }

        function currentDate()
        {

            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth() + 1;
            var yyyy = today.getFullYear();

            if (dd < 10) {
                dd = '0' + dd
            }

            if (mm < 10) {
                mm = '0' + mm
            }

            today = yyyy + "-" + mm + "-" + dd;
            return today;
        }



        function select_leave(val, name) {
            var value = val;
            var current_date = $('#current_date').val();
            var employee_name = name;
            var str = employee_name;
            var res = str.substring(5);
            var joining_date = $('#joining_date' + res + '').val();
            var end_month = $('#fiscal_year_end_month').val();
            var start_month = $('#fiscal_year_start_month').val();
            var employee_type = $('#employee_type' + res + '').val();
            var year_completion_date = $('#employee_year_completion_date' + res + '').val();
            var employee_type = $('#employee_type' + res + '').val();
            var employee_gender = $('#employee_gender' + res + '').val();



            var cls = $('#casual_leaves' + res + '').val();
            var scl = $('#special_casual_leaves' + res + '').val();
            var vcl = $('#vacation_leaves' + res + '').val();
            var mcl = $('#medical_leaves' + res + '').val();
            var ml = $('#maternity_leaves' + res + '').val();
            var pl = $('#paternity_leaves' + res + '').val();
            //available leaves
            var tot_available_cl_leaves = $('#empl_tot_cl_count' + res + '').val();
            var tot_available_vl_leaves = $('#empl_tot_vl_count' + res + '').val();
            var tot_available_ml_leaves = $('#empl_tot_ml_count' + res + '').val();
            var tot_available_scl_leaves = $('#empl_tot_scl_count' + res + '').val();
            var tot_avaialble_mtl_leaves = $('#empl_tot_mtl_count' + res + '').val();
            var tot_available_ptl_leaves = $('#empl_tot_ptl_count' + res + '').val();

            //Casual leaves count
            var empl_cl_count = $('#employee_casual_leave_count' + res + '').val();
            var empl_cl_count_yearly = $('#employee_casual_leave_count_yearly' + res + '').val();
            //Special casual leaves count
            var empl_scl_count_yearly = $('#employee_special_casual_leave_count_yearly' + res + '').val();
            //Vacation leaves count
            var empl_vl_count_yearly = $('#employee_vacation_leave_count_yearly' + res + '').val();
            //Maternity leaves count
            var empl_mtl_count_yearly = $('#employee_maternity_leave_count_yearly' + res + '').val();
            //Paternity leaves count
            var empl_ptl_count_yearly = $('#employee_paternity_leave_count_yearly' + res + '').val();
            //Medical leaves count
            var empl_ml_count_yearly = $('#employee_medical_leave_count_yearly' + res + '').val();
            //Overall Medical leaves count
            var empl_overall_medical_leave_count = $('#employee_overall_medical_leave_count' + res + '').val();

            if (value == 'CL') {

                if (joining_date >= start_month && joining_date <= end_month) {
                    if ((empl_cl_count == '0') && (tot_available_cl_leaves != '0')) {
                        //alert('True');
                    } else {
                        alert('The employee is not eligible to avail casual leaves for this month');
                    }

                } else if ((joining_date <= start_month))
                {
                    if ((empl_cl_count >= 6) && (tot_available_cl_leaves == '0')) {
                        alert('The employee is  not eligible to avail casual leave more than 6 days');

                    } else
                    {
                        //alert('True');
                    }
                }
            } 
        else if (value == 'EL') {

                if (joining_date >= start_month && joining_date <= end_month) {
                    if ((empl_cl_count == '0') && (tot_available_cl_leaves != '0')) {
                        //alert('True');
                    } else {
                        alert('The employee is not eligible to avail casual leaves for this month');
                    }

                } else if ((joining_date <= start_month))
                {
                    if ((empl_cl_count >= 6) && (tot_available_cl_leaves == '0')) {
                        alert('The employee is  not eligible to avail casual leave more than 6 days');

                    } else
                    {
                        //alert('True');
                    }
                }
            }    
        else if (value == 'HCL') {
                if (joining_date >= start_month && joining_date <= end_month) {

                    if ((empl_cl_count == '0') && (tot_available_cl_leaves != '0')) {
                    } else {

                        alert('The employee is not eligible to avail half casual leaves for this month');
                    }
                } else if ((joining_date <= start_month))
                {

                    if ((empl_cl_count >= 6) && (tot_available_cl_leaves == '0')) {

                        alert('The employee is not eligible to avail casual leave more than 6 days');
                    } else
                    {

                    }
                }
            } else if (value == 'SCL') {
                if ((empl_scl_count_yearly >= scl) && (tot_available_scl_leaves == '0')) {

                    alert('The employee  is not eligible to avail special casual leave');

                } else {

                    //alert('True');
                }

            } else if (value == 'VL') {
                if (employee_type == 2) {

                    alert('This leave is applicable only for faculty');

                } else if ((empl_vl_count_yearly >= vcl) && (tot_available_vl_leaves == '0')) {

                    alert('The employee  is not eligible to avail vacation leave');
                } else {

                    //alert('True');
                }


            } else if (value == 'MTL')
            {
                if (employee_gender == 2) {
                    if (current_date < year_completion_date) {

                        alert('The employee not  yet completed one year of service');

                    } else if ((empl_mtl_count_yearly >= ml) && (tot_avaialble_mtl_leaves == '0'))
                    {
                        alert('The employee is not eligible to avail maternity leave');
                    } else {

                        //alert('True');
                    }
                } else
                {
                    alert('Maternity leave is not applicable');

                }
            } else if (value == 'PTL') {
                if (employee_gender == 1) {
                    if ((empl_ptl_count_yearly >= pl) && (tot_available_ptl_leaves == '0'))
                    {

                        alert('The employee  is not eligible to avail paternity leave');
                    } else {
                        //alert('True');
                    }
                } else {

                    alert('Paternity leave is not applicable');
                }
            } else if (value == 'ML') {
                if ((tot_available_ml_leaves == '0') && (empl_overall_medical_leave_count >= 3)) {
                    alert('The employee is not eligible to avail medical leave');

                } else {
                    //alert('True');
                }
            }
            else if(value == 'WOP'){
               $('[value=P]').removeAttr('checked');
                }


        }
    </script>
</html>