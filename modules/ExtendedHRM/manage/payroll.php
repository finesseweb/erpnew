<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */

$page_security = 'HR_PAYROLL';
$path_to_root = "../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
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
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");

page(_("Payroll Process"));
check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));

if (isset($_GET['selected_id'])) {
    $_POST['selected_id'] = $_GET['selected_id'];
}
if (isset($_GET['month'])) {
    $_POST['month'] = $_GET['month'];
}
if (isset($_GET['year'])) {
    $_POST['year'] = $_GET['year'];
}
$selected_id = get_post('selected_id', '');
$month = get_post('month', '');
$year = get_post('year', '');

if (list_updated('month')) {
    $month = get_post('month');
    $Ajax->activate('totals_tbl');
}
start_form(true);

if (db_has_employees()) {
    start_table(TABLESTYLE_NOBORDER);
    start_row();
    kv_fiscalyears_list_cells(_("Fiscal Year:"), 'year', null, true);
    kv_current_fiscal_months_list_cell("Months", "month", null, true);
    department_list_cells(_("Select a Department: "), 'selected_id', null, _('No Department'), true, check_value('show_inactive'));
    end_row();
    end_table();

    if (get_post('_show_inactive_update') || get_post('month') || get_post('year')) {
        $Ajax->activate('selected_id');
        $Ajax->activate('month');
        $Ajax->activate('year');
        $Ajax->activate('sal_calculation');
        set_focus('selected_id');
    }
} else {
    hidden('selected_id', get_post('selected_id'));
    hidden('month', get_post('month'));
    hidden('year', get_post('year'));
}
$get_employees_list = '';
div_start('sal_calculation');
start_table(TABLESTYLE_NOBORDER, "width=40%");
label_row(" <center>**Here, you can Calculate Salaries.  </center>", '', null);
end_table();
//$prof_tax = kv_get_Taxable_field();
$prof_tax = 10;
$leave_encashment = 13;
$pf = 12;
$sas = 6;
start_table(TABLESTYLE, "width=90%");

$th = array(_(" "), _("Empl Id "), _("Employee Name"));

$Allowance = get_allowances('Earnings');
while ($single = db_fetch($Allowance)) {
    $th[] = $single['description'];
}
/*
  $th[] = _("OT & Other Allowance");
  $th[] = _("Conveyance Allowance");
 * 
 */
$th[] = _("EPF (Employer):");
$th[] = _("SAS (Employer):");
$th[] = _("Leave Encashment");
$th[] = _("Gross Pay");

$Allowance = get_allowances('Deductions');
while ($single = db_fetch($Allowance)) {
    $th[] = $single['description'];
}
$th1 = array(_("TDS"), _("Other Ded."), _("Total Deduction"), _("Net Salary"), _(""));
$th_final = array_merge($th, $th1);

table_header($th_final);

$ipt_error = 0;

if (empty($selected_id))
    $selected_id = -1;
/* if($hrm_year_list[$year]<= date('Y')){}
  else {
  display_error(_('The Selected Year Yet to Born!'));
  $ipt_error = 1;
  } */

$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
$months_with_years_list[get_post('month')];



if ($months_with_years_list[get_post('month')] > date('Y-m-d')) {
    display_warning(_('Salary Cannot be processed for future Month!'));
    $ipt_error = 1;
}
 else {
      $ipt_error = date('Y-m',strtotime($months_with_years_list[get_post('month')])) == date('Y-m')?validateMonth():0;
      $ipt_error==1?display_warning(_(date('F'). ' month has not been Completed yet !')):'';
}
function validateMonth(){
    $total_days=cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
    return date('d')==$total_days?0:1;
}
if ($ipt_error == 0) {
    //display_error($selected_id);
    $get_employees_list = get_empl_ids_from_dept_id($selected_id);

    $Total_gross = $total_net = 0;


    foreach ($get_employees_list as $single_empl) {

        //display_error(get_post('month'));

        $data_for_empl = GetRow('kv_empl_job', array('empl_id' => $single_empl));

        //print_r($data_for_empl);
        //echo $year;
        $empl_id = $data_for_empl['empl_id'];
        $existing_empl_sal = GetRow('kv_empl_salary', array('empl_id' => $empl_id, 'month' => $month, 'year' => $year, 'is_arrear' => NULL));

        //echo $months_with_years_list[get_post('month')].'_______'. get_employee_join_date($empl_id);
        // display_error(date("Y-m",strtotime($months_with_years_list[get_post('month')]))." >= ".date("Y-m",strtotime(get_employee_join_date($empl_id))));


        if (count($data_for_empl) > 0 && empty($existing_empl_sal) && date("Y-m", strtotime($months_with_years_list[get_post('month')])) >= date("Y-m", strtotime(get_employee_join_date($empl_id)))) {
 
            $_POST[$empl_id . 'empl_id'] = $empl_id;
            $_POST[$empl_id . 'lop_days'] = get_empl_attendance_for_month($empl_id, $month, $year);
            $_POST[$empl_id . 'eligible_hra'] = $data_for_empl['eligible_hra'];
            $_POST[$empl_id . 'eligible_esi'] = $data_for_empl['eligible_esi'];
            $total_days = date("t", strtotime($months_with_years_list[get_post('month')]));
            $working_days = $total_days - $_POST[$empl_id . 'lop_days'];
            // $Allowance = get_allowances('Earnings');
            //display_error($working_days);
            /*
              $EarAllowance = get_allowances('Earnings');
              while ($single = db_fetch($EarAllowance)) {
              $_POST[$empl_id.$single['id']]= $data_for_empl[$single['id']];
              }

              $DedAllowance = get_allowances('Deductions');
              while ($single = db_fetch($DedAllowance)) {
              if($single['id'] != $prof_tax)
              $_POST[$empl_id.$single['id']]= $data_for_empl[$single['id']];
              }
             * 
             */


            $Allowance = get_allowances();
            while ($single = db_fetch($Allowance)) {
                // display_error($single['id']);
                if ($single['id'] != $prof_tax && $single['id'] != 46 && $single['id'] != 44) {
                    //  display_error($prof_tax);
                    $_POST[$empl_id . $single['id']] = $data_for_empl[$single['id']];
                } 
                
            }
          

            //$_POST[$empl_id.$prof_tax]=kv_get_tax_for_an_employee($empl_id,$year);
            //$_POST[$empl_id.$prof_tax] = $data_for_empl[$prof_tax];

            $_POST[$empl_id . 'gross_salary'] = 0;
            $_POST[$empl_id . 'leave_encashment'] = $data_for_empl[$leave_encashment];

            //$_POST[$empl_id.'ot_other_allowance'] = 0;

            $tds = 0;
            if (!empty($empl_id) && !isset($_POST[$empl_id . 'tds'])) {
                $tds_detail = kv_calculate_tds($empl_id);
                $tds = $tds_detail['payable_tds_per_month'];
            }
            $tds = isset($_POST[$empl_id . 'tds']) ? $_POST[$empl_id . 'tds'] : $tds;
            $_POST[$empl_id . 'tds'] = $tds;

            $_POST[$empl_id . 'lop_days'] = 0;

            $_POST[$empl_id . 'lop_amount'] = 0;

            $_POST[$empl_id . 'loan_amount'] = 0;

            $_POST[$empl_id . 'net_deductions'] = 0;

            $_POST[$empl_id . 'net_pay'] = 0;

            if (!isset($_POST[$empl_id . 'misc']))
                $_POST[$empl_id . 'misc'] = 0;

            if (!isset($_POST[$empl_id . 'ot_other_allowance']))
                $_POST[$empl_id . 'ot_other_allowance'] = 0;
            if (!isset($_POST[$empl_id . 'conveyance_allowance']))
                $_POST[$empl_id . 'conveyance_allowance'] = 0;
            if (!isset($_POST[$empl_id . 'leave_encashment']))
                $_POST[$empl_id . 'leave_encashment'] = 0;
            if (!isset($_POST[$empl_id . 'tds']))
                $_POST[$empl_id . 'tds'] = 0;
            if (!isset($_POST[$empl_id . 'adv_sal']))
                $_POST[$empl_id . 'adv_sal'] = 0;
        }
    }

    div_start('totals_tbl');
    $dat_of_pay = Today();
    hidden('dat_of_pay', $dat_of_pay);
    //print_r($get_employees_list);
    //display_error($_POST[$empl_id.'prof_tax']);
    $empl_ids = array();

    foreach ($get_employees_list as $empl_id) {
        $existing_empl_sal = GetRow('kv_empl_salary', array('empl_id' => $empl_id, 'month' => $month, 'year' => $year));
        if (count($empl_id) > 0 && empty($existing_empl_sal) && date("Y-m", strtotime($months_with_years_list[get_post('month')])) >= date("Y-m", strtotime(get_employee_join_date($empl_id)))) {
            
            
            
            
                            /*
                 * this code has been written 
                 * for to get leave encashment
                 * value thank Ashutosh 
                 * for more information 
                 * please contact http://ashutoshrma.,com
                 * 
                 * 
                 */
                $_POST[$empl_id.'leave_encashment'] =0;
            
                $encashmentInfo = getEmplEncashmentRequestInfo($empl_id);
                while($row = db_fetch($encashmentInfo)){

                    $encashment_fisical_year = getFisicalYearByDate($row['approved_date'])['f_year']; //===getting fiscal year according to approved date of leave encashment
                   /*
                    * when encashment_fisical_year AND 
                    * fiscal year selected by user are same 
                    * then check month if it is greater or equal than the 
                    * Approved month then only include leave encahsmnet form this code
                    * 
                    */ 
                    if($year == $encashment_fisical_year){
                        if($month <= date('m',strtotime($row['approved_date']))){ //===if approved month >= user selected month then add amount of leave encashment  
                            $_POST[$empl_id.'leave_encashment'] += $row['approved_amount'];
                        }
                    }

                }
                // display_error($empl_id.'==='.$month.'==='.$year.'====='.$encashment_fisical_year.'====='.$_POST[$empl_id.'leave_encashment']);
                
             
            //display_error($empl_id);
            //display_error(get_post($empl_id.'prof_tax'));
            //  print_r($_POST);
            $empl_ids[] = $empl_id;
            $_POST[$empl_id . 'gross_sal'] = $_POST[$empl_id . 'gross_salary'] = 0;

            $total_days = date("t", strtotime($months_with_years_list[get_post('month')]));
            //display_error($total_days);

            $_POST[$empl_id . 'lop_days'] = get_empl_attendance_for_month($empl_id, $month, $year);

            $_POST[$empl_id . 'lop_amount'] = round(input_num($empl_id . 'lop_days') * input_num($empl_id . 'gross_sal') / $total_days, 2);

            $_POST[$empl_id . 'loan_amount'] = get_empl_loan_monthly_payment($empl_id, sql2date($months_with_years_list[get_post('month')]));
            $working_days = $total_days - $_POST[$empl_id . 'lop_days'];

            $EarAllowance = get_allowances('Earnings');
            while ($single = db_fetch($EarAllowance)) {
                //$_POST[$empl_id.'gross_sal'] += round((input_num($empl_id.$single['id'])*($working_days))/($total_days));
                //$_POST[$empl_id.'gross_salary'] += round((input_num($empl_id.$single['id'])*($working_days))/($total_days)); 
                if ($single['id'] != 39 && $single['id'] != 46 && $single['id'] != 44) {
                    $_POST[$empl_id . 'gross_sal'] += round(input_num($empl_id . $single['id']));
                    $_POST[$empl_id . 'gross_salary'] += round(input_num($empl_id . $single['id']));
                }
            }
            //$gross_4_LOP = 0;


            $gross_4_LOP = $_POST[$empl_id . 'gross_salary'];
           // $_POST[$empl_id . 'gross_salary'] -= $_POST[$empl_id . '46'];
            $_POST[$empl_id . 'gross_salary'] -= $_POST[$empl_id . '12'];
            $_POST[$empl_id . 'gross_sal'] -= $_POST[$empl_id . '12'];

            // display_error($empl_id."=====".$_POST[$empl_id . 'gross_salary']);

            $_POST[$empl_id . 'lop_days'] = get_empl_attendance_for_month($empl_id, $month, $year);
            
            //---------------------------------------Addeding manual lop amount due to short attenadance---------------------
                $_POST[$empl_id.'man_id'] = getManualSalaryId1($empl_id, date("Y-m", strtotime($months_with_years_list[get_post('month')])));
                $man_sal_result = getValueFromManualSalary1($_POST[$empl_id.'man_id']);
                $man_sal_row =  db_fetch($man_sal_result);
                $_POST[$empl_id . 'lop_days']+=$man_sal_row['days_deducted'];
           //-----------------------------------------------------------------------------------------------------------------
            
                $_POST[$empl_id . 'lop_amount'] = round(input_num($empl_id . 'lop_days') * $gross_4_LOP / $total_days, 2);

            $_POST[$empl_id . 'loan_amount'] = get_empl_loan_monthly_payment($empl_id, sql2date($months_with_years_list[get_post('month')]));
            $working_days = $total_days - $_POST[$empl_id . 'lop_days'];


            $_POST[$empl_id . 'gross_salary'] -= round($_POST[$empl_id . 'lop_amount']);
            $_POST[$empl_id . 'gross_sal'] -= round($_POST[$empl_id . 'lop_amount']);


            $_POST[$empl_id . 'gross_salary'] += input_num($empl_id . 'ot_other_allowance') + input_num($empl_id . 'conveyance_allowance') + input_num($empl_id . 'leave_encashment') + input_num($empl_id . $pf) + input_num($empl_id . $sas) ;
//$_POST[$empl_id . 'gross_salary']-=round($_POST[$empl_id . '10']);
            //display_error($empl_id."==".$_POST[$empl_id . 'gross_salary']."====".$_POST[$empl_id . '10']);
            //$_POST[$empl_id.'net_deductions'] =input_num($empl_id.'misc')+input_num($empl_id.'loan_amount')+input_num($empl_id.'lop_amount')+input_num($empl_id.'adv_sal')+input_num($empl_id.'tds'); 
            $_POST[$empl_id . 'net_deductions'] = input_num($empl_id . 'loan_amount') + input_num($empl_id . 'adv_sal') + input_num($empl_id . $pf) + input_num($empl_id . $sas);
            $DedAllowance = get_allowances('Deductions');
            while ($single = db_fetch($DedAllowance)) {
                //$_POST[$empl_id.'net_deductions'] += round((input_num($empl_id.$single['id'])*($working_days))/($total_days));
                $_POST[$empl_id . 'net_deductions'] += round(input_num($empl_id . $single['id']));
            }
            $accom_by_org_hra = 0;
            //If accommodation is provided by Employer, deduct HRA                
            if (isset($_POST[$empl_id . 'eligible_hra']) && $_POST[$empl_id . 'eligible_hra'] == 2) {
                $_POST[$empl_id . 'net_deductions'] += $_POST[$empl_id . '4'];
                $accom_by_org_hra = $_POST[$empl_id . '4'];
            }

            $_POST[$empl_id . 'net_pay'] = input_num($empl_id . 'gross_salary') - input_num($empl_id . 'net_deductions');

            start_row();
            echo '<td><input type="checkbox" name="sel_empl_' . $empl_id . '" value="' . $empl_id . '" /></td>';
            label_cell($empl_id);
            label_cell(kv_get_empl_name($empl_id));
            $months_with_years_list[get_post('month')];
            $total_days = date("t", strtotime($months_with_years_list[get_post('month')]));
            $working_days = $total_days - $_POST[$empl_id . 'lop_days'];
            $EarAllowance = get_allowances('Earnings');
            $_POST[$empl_id . '47'] = $_POST[$empl_id . 'gross_salary'] ;
           
            //display_error($_POST[$empl_id . '47']);
            while ($single = db_fetch($EarAllowance)) {
                if ($single['id'] == 1) {
                    //$_POST[$empl_id . 'gross_salary']-=$_POST[$empl_id.'leave_encashment'];//==subtracting leave encashment coz every thing is calulated according to this
                    $_POST[$empl_id . $single['id']] = round(($_POST[$empl_id . '39'] * $single['percentage']) / 100);
                    $_POST[$empl_id . '47'] -= $_POST[$empl_id . $single['id']];
                    label_cell(round($_POST[$empl_id . $single['id']]));
                    hidden($empl_id . $single['id'], round($_POST[$empl_id . $single['id']]));
                } else if ($single['id'] == 52) {

                    label_cell(round($_POST[$empl_id . $single['id']]));
                    hidden($empl_id . $single['id'], round($_POST[$empl_id . $single['id']]));
                } elseif ($single['id'] == 4) {
                    $hra = $_POST[$empl_id . '4'];
                    $month_hra = $hra / $total_days;

                    $absent_amt_hra = $_POST[$empl_id . 'lop_days'] * round($month_hra);

                    $hra_val = $hra - $absent_amt_hra;

                    $_POST[$empl_id . '4'] = $hra_val;
                    $_POST[$empl_id . '47'] -= $_POST[$empl_id . $single['id']];
                    label_cell(round($_POST[$empl_id . $single['id']]));
                    hidden($empl_id . $single['id'], round($_POST[$empl_id . $single['id']]));
                } elseif ($single['id'] == 40) {

                    $_POST[$empl_id . '47'] -= $_POST[$empl_id . '40'];
                    label_cell(round($_POST[$empl_id . $single['id']]));
                    hidden($empl_id . $single['id'], round($_POST[$empl_id . $single['id']]));
                } elseif ($single['id'] == 41) {
                    if ($_POST[$empl_id . '1'] < 15000) {
                        $epf = $_POST[$empl_id . '12'];
                        $month_epf = $epf / $total_days;
                        $absent_amt_epf = $_POST[$empl_id . 'lop_days'] * $month_epf;
                        $epf_val = $epf - $absent_amt_epf;
                        $_POST[$empl_id . '12'] = round($epf_val);
                        $_POST[$empl_id . '52'] = round($epf_val);
                        $_POST[$empl_id . 'net_deductions'] = 0;
                        $_POST[$empl_id . 'net_deductions'] = round($epf_val) * 2;
                    }
                    if ($_POST[$empl_id . 'eligible_esi'] == 2)
                        $_POST[$empl_id . '41'] = 0;
                    else
                        $_POST[$empl_id . '41'] = ((round($_POST[$empl_id . '39']) - round($_POST[$empl_id . '52'])) * 4.75 / 100);
                    $_POST[$empl_id . '47'] -= round($_POST[$empl_id . '41']);
                    label_cell(round($_POST[$empl_id . $single['id']]));
                    hidden($empl_id . $single['id'], round($_POST[$empl_id . $single['id']]));
                } elseif ($single['id'] == 44) {
                  //  $_POST[$empl_id . '47'] -= round($_POST[$empl_id . '44']);
                  // label_cell(round($_POST[$empl_id . $single['id']]));
                   //hidden($empl_id . $single['id'], round($_POST[$empl_id . $single['id']]));
                    text_cells(null, $empl_id .$single['id'], null, 10, 10, null, '', '', TRUE);
                } elseif ($single['id'] == 45) {

                    $_POST[$empl_id . '47'] -= round($_POST[$empl_id . '45']);
                    label_cell(round($_POST[$empl_id . $single['id']]));
                    hidden($empl_id . $single['id'], round($_POST[$empl_id . $single['id']]));
                } elseif ($single['id'] == 46) {
                  //  $_POST[$empl_id . '47'] -= round($_POST[$empl_id . '46']);
                     text_cells(null, $empl_id .$single['id'], null, 10, 10, null, '', '', TRUE);
                     
                    
                  /*  label_cell(round($_POST[$empl_id . $single['id']]));
                    hidden($empl_id . $single['id'], round($_POST[$empl_id . $single['id']]));*///===== [MADE EDITABLE] ==========//
                }  elseif ($single['id'] == 47) {

                    $_POST[$empl_id . '47'] -= round($_POST[$empl_id . '52']);
                    $_POST[$empl_id . '47'] -= round($_POST[$empl_id . 'leave_encashment']);
                   
                    //  $_POST[$empl_id . '47']-=round($_POST[$empl_id . '10']);
                    label_cell(ceil($_POST[$empl_id . $single['id']]));
                    hidden($empl_id . $single['id'], round($_POST[$empl_id . $single['id']]));
                }
                else {
                    label_cell(round($_POST[$empl_id . $single['id']]));
                    hidden($empl_id . $single['id'], round($_POST[$empl_id . $single['id']]));
                }
            }

            //text_cells(null, $empl_id.'ot_other_allowance',null,"5");
            //text_cells(null,$empl_id.'conveyance_allowance',null,"5");
            //text_cells(null,$empl_id.'leave_encashment',null,"5");
            //print_r($_POST);
            label_cell(price_format($_POST[$empl_id . '12']));
            label_cell(price_format($_POST[$empl_id . '6']));
            label_cell(price_format($_POST[$empl_id . 'leave_encashment']));
            $_POST[$empl_id . 'gross_salary1'] = $_POST[$empl_id . 'gross_salary'];
            $_POST[$empl_id . 'gross_salary']+=$_POST[$empl_id . '46'];
            $_POST[$empl_id . 'gross_salary']+=$_POST[$empl_id . '44'];
            hidden($empl_id . 'gross_salary', $_POST[$empl_id . 'gross_salary']);
            hidden($empl_id . 'eligible_hra', $_POST[$empl_id . 'eligible_hra']);

            label_cell($_POST[$empl_id . 'gross_salary']);
            hidden($empl_id . 'gross_salary', $_POST[$empl_id . 'gross_salary']);

            $DedAllowance = get_allowances('Deductions');


            $_POST[$empl_id . 'net_deductions'] += $_POST[$empl_id . 'tds'];
            $_POST[$empl_id . 'net_deductions'] += $_POST[$empl_id . 'misc'];
            $_POST[$empl_id . 'net_deductions'] += $_POST[$empl_id . '10'];
            //   display_error($empl_id."===".$_POST[$empl_id . 'eligible_esi']);
            while ($single = db_fetch($DedAllowance)) {

                if ($single['id'] == 49) {
                    if ($_POST[$empl_id . 'eligible_esi'] != 2)
                        $default_value = ((round($_POST[$empl_id . '39'])  - round($_POST[$empl_id . '52'])) * 4.75 / 100);
                    else
                        $default_value = 0;
                    $_POST[$empl_id . 'net_deductions'] += round($default_value);
                    $_POST[$empl_id . '49'] = round($default_value);
                    label_cell(round($_POST[$empl_id . $single['id']]));
                    hidden($empl_id . $single['id'], round($_POST[$empl_id . $single['id']]));
                } elseif ($single['id'] == 43) {
                    if ($_POST[$empl_id . 'eligible_esi'] != 2)
                        $default_value = round((round($_POST[$empl_id . '39']) - round($_POST[$empl_id . '12'])) * 1.75 / 100);
                    else
                        $default_value = 0;
                    $_POST[$empl_id . 'net_deductions'] += round($default_value);
                    $_POST[$empl_id . '43'] = round($default_value);
                    label_cell(round($_POST[$empl_id . $single['id']]));
                    hidden($empl_id . $single['id'], round($_POST[$empl_id . $single['id']]));
                } else {
                    if ($single['id'] != $prof_tax) {
                        label_cell(round($_POST[$empl_id . $single['id']]));
                        hidden($empl_id . $single['id'], round($_POST[$empl_id . $single['id']]));
                    } else {
                        text_cells(null, $empl_id . $single['id']);
                    }
                }
             
                $_POST[$empl_id . 'net_pay'] = 0;
                $_POST[$empl_id . 'net_pay'] = round(($_POST[$empl_id . 'gross_salary'])) - round($_POST[$empl_id . 'net_deductions']);
            }
            //
            //text_cells(null, $empl_id.'adv_sal',null,"5");
            text_cells(null, $empl_id . 'tds', null, "5");
            //label_cell($_POST[$empl_id.'loan_amount']);
            hidden($empl_id . 'loan_amount', $_POST[$empl_id . 'loan_amount']);

            //label_cell($_POST[$empl_id.'lop_days'] );
            hidden($empl_id . 'lop_days', $_POST[$empl_id . 'lop_days']);
            //label_cell($data_for_empl['lop_amount']);							
            //label_cell($_POST[$empl_id.'lop_amount']);
            hidden($empl_id . 'lop_amount', $_POST[$empl_id . 'lop_amount']);

            text_cells(null, $empl_id . 'misc', null, "5");

            label_cell($_POST[$empl_id . 'net_deductions']);
            hidden($empl_id . 'net_deductions', $_POST[$empl_id . 'net_deductions']);
            label_cell($_POST[$empl_id . 'net_pay']);
            hidden($empl_id . 'basic', $_POST[$empl_id . 'net_pay']);
            
            $Total_gross += $_POST[$empl_id . 'gross_salary'];
            $total_net += $_POST[$empl_id . 'net_pay'];
            label_cell("");

            end_row();
        }
    }
    //foreach ($empl_ids as $value) {
    hidden('empl_ids', implode("-", $empl_ids));
    //}

    div_end();
    start_row();
    $Earnings_colum_count = get_allowances_count('Earnings');
    $Deductions_colum_count = get_allowances_count('Deductions');
    $gross_colm_cnt = $Earnings_colum_count + 4;
    $net_colm_cnt = $Deductions_colum_count + 3;
    echo " <td colspan='" . $gross_colm_cnt . "'> </td> <td><strong>Total Gross</strong></td><td><strong>" . $Total_gross . "</strong></td> ";
    echo "<td colspan='" . $net_colm_cnt . "' align='right'></td> <td colspan='2'><strong>Total Net Salary</strong></td> <td><strong>" . $total_net . "</strong></td>";
    end_row();
}
end_table(1);

submit_center('pay_salary', _("Process Payout"), true, _('Payout to Employees'), 'default',false,'',true);

div_end();
end_form();

if (get_post('Refreshloan')) {
    $Ajax->activate('totals_tbl');
}


if (get_post('pay_salary')) {
    $salary_account = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'salary_account'));
    $paid_from_account = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'paid_from_account'));

    //$get_employees_list = explode("-", $_POST['empl_ids']);			

    $total_days = date("t", strtotime($months_with_years_list[get_post('month')]));
    $working_days = $total_days - $_POST[$empl_id . 'lop_days'];
    foreach ($get_employees_list as $empl_id) {

        $sel_empl_id = get_post('sel_empl_' . $empl_id, '');
        if (!empty($sel_empl_id)) {
            $jobs_arr = array('empl_id' => $empl_id,
                'month' => $_POST['month'],
                'year' => $_POST['year'],
                'gross' => $_POST[$empl_id . 'gross_salary'],
                'deduct_tot' => $_POST[$empl_id . 'net_deductions'],
                'loan' => $_POST[$empl_id . 'loan_amount'],
                'date' => array(Today(), 'date'),
                'adv_sal' => $_POST[$empl_id . 'adv_sal'],
                'net_pay' => $_POST[$empl_id . 'net_pay'],
                'misc' => $_POST[$empl_id . 'misc'],
                'ot_other_allowance' => $_POST[$empl_id . 'ot_other_allowance'],
                'lop_amount' => $_POST[$empl_id . 'lop_amount'],
                'conveyance_allowance' => $_POST[$empl_id . 'conveyance_allowance'],
                'leave_encashment' => $_POST[$empl_id . 'leave_encashment'],
                'tds' => $_POST[$empl_id . 'tds'],
                'accom_hra_by_org' => $_POST[$empl_id . 'eligible_hra']);
            $Allowance = get_allowances();
            while ($single = db_fetch($Allowance)) {
                $jobs_arr[$single['id']] = round(($_POST[$empl_id . $single['id']]));
            }


            $pay_slip_id = Insert('kv_empl_salary', $jobs_arr);

             /*
     * if value has inserted proper 
     * then only update encashment 
     * request table and make is_paid = 1;
     */
    if($pay_slip_id){
            update_encashment_request($empl_id);
            }

            //display_notification(' The Employee Payslip is added #' .$_POST['date_of_pay']);
            if ($_POST[$empl_id . 'loan_amount'] > 0) {
                paid_empl_loan_month_payment($empl_id);
            }
            //Debit/Credit of Ledger account
            //Array Mapping for all the salary componenets form posting & Heads of Finance account
            /*  $salary_post = array(
              1 => 1, //Basic
              2 => 3, //DA
              3 => 2, //Grade Pay
              4 => 4, //HRA
              5 => 8, //Conveyance
              6 => 12, //EPF-Employer Contribution
              7 => 6, //NPS-SAS- Employer Contribution
              8 => 'leave_encashment', //Leave Encashment
              9 => 6, //NPS-SAS-Employee Contribution
              10 => 10, //Professional Tax
              11 => 12, //EPF-Employee Contribution
              12 => 'tds', //TDS
              14 => 'misc' //Other Deduction
              ); */
 $salary_post = array(
        1 => 1, //basic
        2 => 4, //HRA
        3 => 47, //special Allowance
        4 => 40, //Food coupon
        5 => 'tds', //TDS
        6 => 46, //incentive (PLI)
        7 => 12, //EPF employers contiribution
        8 => 12, //EPF employee contribution
        9 => 43, //ESIC employer's contribution
        10 => 49, //ESIC employee's contribution 
        11 => 44, //salary Advance
        12 => 40,
        13 => 12,//EPF
        14=>10,//Professional Tax
        16=>41,//ESIC 4.75
        17=>'leave_encashment'
    );

            $allowance = get_all_hrm_finance_setting();
            $pending_salary_comp = array();
            //For Debit
            $debit_total = 0;
            $credit_total = 0;
            $cur_date = Today();
            while ($single = db_fetch($allowance)) {//Loop over all salary components
                if ($single['id'] == 15) {
                    $pending_salary_comp = $single;
                }
                if (($single['type'] == 'Salary') && $single['inactive'] == 0) {

                    if ($single['allowance_debit_gl_code'] != 0 && isset($_POST[$empl_id . $salary_post[$single['id']]]) && is_numeric($_POST[$empl_id . $salary_post[$single['id']]]) && $_POST[$empl_id . $salary_post[$single['id']]] != 0) {
                        add_gl_trans(99, $pay_slip_id, $cur_date, $single['allowance_debit_gl_code'], 0, 0, 'Employee ' . $single['description'] . ' #' . $empl_id . '-' . kv_get_empl_name($empl_id), $_POST[$empl_id . $salary_post[$single['id']]]);
                        $debit_total += $_POST[$empl_id . $salary_post[$single['id']]];
                    }
                    if ($single['allowance_credit_gl_code'] != 0 && isset($_POST[$empl_id . $salary_post[$single['id']]]) && is_numeric($_POST[$empl_id . $salary_post[$single['id']]]) && $_POST[$empl_id . $salary_post[$single['id']]] != 0) {//Skip for 'None'
                        add_gl_trans(99, $pay_slip_id, $cur_date, $single['allowance_credit_gl_code'], 0, 0, 'Employee ' . $single['description'] . ' #' . $empl_id . '-' . kv_get_empl_name($empl_id), -$_POST[$empl_id . $salary_post[$single['id']]]);
                        $credit_total += $_POST[$empl_id . $salary_post[$single['id']]];
                    }
                }
            }
            //Processing Pending Salary after deduction PF,ASA,Prof. Tax, etc.
            $pending_salary = $debit_total - $credit_total;
            add_gl_trans(99, $pay_slip_id, $cur_date, $pending_salary_comp['allowance_credit_gl_code'], 0, 0, 'Employee ' . $pending_salary_comp['description'] . ' #' . $empl_id . '-' . kv_get_empl_name($empl_id), -$pending_salary);
            //---------------------------------Status update for Manual entry for lop After salary is add into gltable--------------------
                $man_data['status'] = 1;
                Update('manual_sal_deduction', array('id',$_POST[$empl_id.'man_id']), $man_data);
            //----------------------------------------------------------------------------------------------------------------------------
            
        }
    }
    if (!empty($pay_slip_id)) {
        meta_forward($path_to_root . '/modules/ExtendedHRM/inquires/payroll_history_inquiry.php', "selected_id=" . $_POST['selected_id'] . '&month=' . $_POST['month'] . '&year=' . $_POST['year'] . '&Added=yes');
    } else {
        display_warning(_("select any employee !"));
    }
}

end_page();
?>
