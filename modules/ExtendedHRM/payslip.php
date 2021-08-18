<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */
$page_security = 'HR_PAYSLIP';
$path_to_root = "../..";
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
include($path_to_root . "/tally/includes/ui/tally.inc" );
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");

page(_("PaySlip"));

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));

check_db_has_salary_account(_("There are no Salary Account defined in this system. Kindly Open <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/hrm_settings.php'>Settings</a> to update it."));

$slip_id = '';
if (isset($_GET['Added'])) {
    display_notification(' The Employee Payslip is added #' . $_GET['Added']);
}
if (isset($_GET['employee_id'])) {
    $_POST['employee_id'] = $_GET['employee_id'];
}
if (isset($_GET['month'])) {
    $_POST['month'] = $_GET['month'];
}
if (isset($_GET['year'])) {
    $_POST['year'] = $_GET['year'];
}
if (isset($_GET['slip_id'])) {
    $slip_id = $_GET['slip_id'];
}

$employee_id = get_post('employee_id', '');
$month = get_post('month', '');
$year = get_post('year', '');
$is_arrear = isset($_GET['is_arrear']) ? $_GET['is_arrear'] : '';

if (list_updated('month') || get_post('RefreshInquiry') || get_post('tds') || get_post('misc') || list_updated('is_arrear')) {

    $month = get_post('month');
    $Ajax->activate('totals_tbl');
}

div_start('totals_tbl');
start_form();
if (db_has_employees()) {
    start_table(TABLESTYLE_NOBORDER);
    start_row();
    kv_fiscalyears_list_cells(_("Fiscal Year:"), 'year', null, true);
    kv_current_fiscal_months_list_cell("Months", "month", null, true);
    employee_list_cells(_("Select an Employee: "), 'employee_id', null, _('New Employee'), true, check_value('show_inactive'));
    //check_cells('Generate Arrear Slip', 'is_arrear', NULL, TRUE);
    end_row();
    end_table();
    br();
    if (get_post('_show_inactive_update')) {
        $Ajax->activate('employee_id');
        $Ajax->activate('month');
        $Ajax->activate('year');
        $Ajax->activate('is_arrear');
        set_focus('employee_id');
    }
} else {
    hidden('employee_id');
    hidden('month');
    hidden('year');
}

///display_error($year.'==='.$month.'====='.$employee_id);


//display_error();

$_POST['EmplName'] = $_POST['desig'] = '';
$_POST['ear_tot'] = $_POST['deduct_tot'] = $_POST['empl_dept'] = $_POST['employee_id'] = $_POST['absent'] = $_POST['lop_amount'] = $_POST['net_pay'] = $_POST['loan'] = 0;

$Allowance = get_allowances();
 
while ($single = db_fetch($Allowance)) {
   
    if($single['id']!=46 && $single['id']!=44)
    $_POST[$single['id']] = 0;
}




/*
 * this code has been written 
 * for to get leave encashment
 * value thank Ashutosh 
 * for more information 
 * please contact http://ashutoshrma.,com
 * 
 * 
 */
$_POST['leave_encashment'] =0;
if(!empty($employee_id)){
$encashmentInfo = getEmplEncashmentRequestInfo($employee_id);
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
        if($month == date('m',strtotime($row['approved_date']))){ //===if approved month >= user selected month then add amount of leave encashment  
            $_POST['leave_encashment'] += $row['approved_amount'];
        }
    }
    
}
}




$dat_of_pay = Today();

$prof_tax = kv_get_Taxable_field();
$_POST[10] = $_POST['proftax'];
if (isset($_POST[$prof_tax]) && $_POST[$prof_tax] > 0) {
    
} else {
    $_POST[$prof_tax] = 0;
}
if (isset($_POST['misc']) && $_POST['misc'] > 0) {
    
} else {
    $_POST['misc'] = 0;
}
if (isset($_POST['ot_other_allowance']) && $_POST['ot_other_allowance'] > 0) {
    
} else {
    $_POST['ot_other_allowance'] = 0;
}
if (isset($_POST['conveyance_allowance']) && $_POST['conveyance_allowance'] > 0) {
    
} else {
    $_POST['conveyance_allowance'] = 0;
}

if (isset($_POST['leave_encashment']) && $_POST['leave_encashment'] > 0) {
    
} else {

    $_POST['leave_encashment'] = 0;
}

if (isset($_POST['adv_sal']) && $_POST['adv_sal'] > 0) {
    
} else {

    $_POST['adv_sal'] = 0;
}
if (isset($_POST['tds']) && ($_POST['tds'] > 0) && !list_updated('employee_id')) {
    
}
else if (isset($_POST['tds']) && ($_POST['tds']==0) && !list_updated('employee_id')) {
    $_POST['tds'] = $tds;  

    
}
else {

    $tds = 0;


    if (!empty($employee_id)) {
        $tds_detail = kv_calculate_tds($employee_id);
        $tds = $tds_detail['payable_tds_per_month'];
         $_POST['tds'] = $tds;
    }
}





if (get_post('tds')) {
    
   
   
    if ($_POST['tds'] != $tds) {
        $tds = $_POST['tds'];
     
    } elseif ($_POST['tds'] == 0) {
        $tds = $_POST['tds'];
        $tds ==0;
        
    } else {
       $tds = $_POST['tds'];
       
    }
}
$_POST['tds'] = $tds;

$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);


$months_with_years_list[get_post('month')];
//display_error($months_with_years_list[get_post('month')]);
$joining_date = get_employee_join_date($employee_id);

$month_name = kv_month_name_by_id(get_post('month'));

$_POST['today_date'] = date("d-F-Y");
//if(isset($employee_id) && $employee_id != '' && date('m',strtotime($months_with_years_list[get_post('month')])) >= date('m', strtotime($joining_date)) ) {
if (isset($employee_id) && $employee_id != '' && date("Y-m", strtotime($months_with_years_list[get_post('month')])) >= date("Y-m", strtotime(get_employee_join_date($employee_id)))) {

    $sal_row = get_empl_sal_detail2($employee_id, $month, $year, $is_arrear, $slip_id);
    $job_row = get_empl_sal_detail0($employee_id, $month, $year, $is_arrear, $slip_id);
    echo "<pre>";
    ////////////
    //
                     
            while ($single = db_fetch($EarAllowance)) {

        $_POST['ear_tot'] += round($_POST[$single['id']]);
    }

    //////////////

    $name_and_dept = get_empl_name_dept($employee_id);
    $_POST['empl_dept'] = get_department_name($name_and_dept ['deptment']);
    $_POST['absent'] = get_empl_attendance_for_month($employee_id, $month, $year);
    //-------------------------------------Adding manual lop due to short attendance from the attendance pannel -------------------
    $id = getManualSalaryId1($employee_id, date("Y-m", strtotime($months_with_years_list[get_post('month')])));
    $man_sal_result = getValueFromManualSalary1($id);
    $man_sal_row =  db_fetch($man_sal_result);
    $_POST['absent']+= $man_sal_row['days_deducted'];
    //------------------------------------------------------------------------------------------------------------------------------
    $_POST['desig'] = kv_get_empl_desig($employee_id);
    $_POST['EmplName'] = $name_and_dept ['name'];
    $eligible_esi = 1;
    $Allowance = get_allowances();

    while ($single = db_fetch($Allowance)) {
        if ($single['id'] != 10 && $single['id'] != 46 && $single['id'] != 44)
            $_POST[$single['id']] = $job_row[$single['id']];
        if (isset($sal_row['net_pay']) && ($single['id'] == 46 || $single['id'] == 44)) {
            $_POST[$single['id']] = $sal_row[$single['id']];
        }
    } 
    $_POST['employee_id'] = $employee_id;
    if (isset($_POST[$prof_tax]) && $_POST[$prof_tax] == 0 && isset($sal_row[$prof_tax]) && $sal_row[$prof_tax] > 0) {
        $_POST[$prof_tax] = $sal_row[$prof_tax];
    }

    $_POST['loan'] = get_empl_loan_monthly_payment($employee_id, sql2date($months_with_years_list[get_post('month')]));

    $Allowance = get_allowances('Earnings');
    $gross_4_LOP = 0;

    while ($single = db_fetch($Allowance)) {
        if ($single['id'] != 39 && $single['id'] != 46 && $single['id'] != 44)
            $gross_4_LOP += $_POST[$single['id']];
    }
    // $gross_4_LOP = 20000;
    $total_days = date("t", strtotime($months_with_years_list[get_post('month')]));
    $_POST['lop_amount'] = round($_POST['absent'] * $gross_4_LOP / $total_days, 2);
    


    if (isset($sal_row['net_pay'])) {

        $_POST['lop_amount'] = $sal_row['lop_amount'];
        $_POST[$prof_tax] = $sal_row[$prof_tax];
        $_POST['adv_sal'] = $sal_row['adv_sal'];
        $_POST['misc'] = $sal_row['misc'];
        $_POST['net_pay'] = $sal_row['net_pay'];
        $_POST['ot_other_allowance'] = $sal_row['ot_other_allowance'];
        $_POST['conveyance_allowance'] = $sal_row['conveyance_allowance'];
        $_POST['leave_encashment'] = $sal_row['leave_encashment'];
        $_POST['today_date'] = $sal_row['date'];
        $_POST['loan'] = $sal_row['loan'];
        $_POST['tds'] = $sal_row['tds'];
        $_POST['eligible_hra'] = $sal_row['accom_hra_by_org'];
        $_POST['eligible_esi'] = $sal_row['eligible_esi'];
    } else {
       // $_POST['leave_encashment'] = $sal_row['13'];
        $_POST['eligible_hra'] = $sal_row['eligible_hra'];
        $_POST['eligible_esi'] = $sal_row['eligible_esi'];
    }
} else {

    if ($months_with_years_list[get_post('month')] < $joining_date)
        display_warning(_("You can't Pay Employee Salary before his Joining Date!"));
}

if (!isset($sal_row['net_pay'])) {

    /*     * ******[START] Commented due to incorrect calcuation of PF, Updated Date - 14-Dec-2017 ***** */
    //$_POST[$prof_tax] = kv_get_tax_for_an_employee($_POST['employee_id'], $year);
    /*     * ******[END] Commented due to incorrect calcuation of PF, Updated Date - 14-Dec-2017 ***** */
}
//echo $_POST[$prof_tax].'*';
//display_notification($month_name);
$months_with_years_list[get_post('month')];
$total_days = date("t", strtotime($months_with_years_list[get_post('month')]));
//display_error($_POST['absent']);
$working_days = $total_days - $_POST['absent'];
$deduct_tot = $_POST['adv_sal'] + $_POST['loan'] + $_POST['tds'];
if ($_POST['is_sal'] != 1) {
    if ($_POST[1] < 15000) {
        $epf = $_POST[12];
        $month_epf = $epf / $total_days;
        $absent_amt_epf = $_POST['absent'] * $month_epf;
        $epf_val = $epf - $absent_amt_epf;
        $_POST[12] = $epf_val;
        $sas = $_POST[6];
        $month_sas = $sas / $total_days;
        $absent_amt_sas = $_POST['absent'] * $month_sas;
        $sas_val = $sas - $absent_amt_sas;
        $_POST[6] = $sas_val;
    }
}
$DedAllowance = get_allowances('Deductions');
while ($single = db_fetch($DedAllowance)) {
    if ($single['id'] != $prof_tax)
        $deduct_tot += round($_POST[$single['id']]);
    //$deduct_tot += round($_POST[$single['id']]);
}

$deduct_tot += round($_POST[12] + $_POST[6]);
//Adding Employer's PF & ASA in Total Earning
$_POST['ear_tot'] += $_POST[12] + $_POST[6];
if (isset($_POST[$prof_tax])) {

    $deduct_tot += get_post($prof_tax);
}
if (isset($_POST['misc'])) {
    $deduct_tot += get_post('misc');
}

//If accommodation is provided by Employer, deduct HRA 

if (isset($_POST['eligible_hra']) && $_POST['eligible_hra'] == 2) {
    $deduct_tot += $_POST[4];
    $hra = $_POST[4];
    $month_hra = $hra / $total_days;
    $absent_amt_hra = $_POST['absent'] * $month_hra;
    $hra_val = $hra - $absent_amt_hra;
    $accom_by_org_hra = $hra_val;
} else if ($_POST['eligible_hra'] == 1) {
    $accom_by_org_hra = 0;
} else {

    $accom_by_org_hra = $_POST['eligible_hra'];
}

$EarAllowance = get_allowances('Earnings');
$_POST['ear_tot'] = $_POST['ot_other_allowance'] + $_POST['conveyance_allowance'] + $_POST['leave_encashment'];

$deduct_specialbasic_on_lop = 0;
$deduct_specialHRA_on_lop = 0;
$_POST[47] = round(($_POST[39] - (int) $_POST['lop_amount']));

while ($single = db_fetch($EarAllowance)) {
    //$_POST['ear_tot'] += round($_POST[$single['id']]);
    //$_POST['ear_tot'] += round($_POST[$single['id']]);
    //Working ritika 12-9-18

    if ($_POST['is_sal'] != 1) {
        if ($single['id'] == 1) {
            // display_error($_POST[1]);
            $basic = $_POST[1];
            $month_sal = $basic / $total_days;
            $absent_amt = $_POST['absent'] * $month_sal;
            $basic_lopamt = $basic - $absent_amt;

            $_POST[1] = $basic_lopamt;

            if (isset($_POST[39])) {

                $_POST[47] -= $_POST[1];
            }

            $_POST['ear_tot'] += round($_POST[1]);
        } elseif ($single['id'] == 2) {
            $gp = $_POST[2];
            $month_gp = $gp / $total_days;
            $absent_amtgp = $_POST['absent'] * $month_gp;
            $gp_lopamt = $gp - $absent_amtgp;
            $_POST[2] = $gp_lopamt;
            $_POST['ear_tot'] += round($_POST[2]);
        } elseif ($single['id'] == 3) {
            $da = $_POST[3];
            $month_da = $da / $total_days;
            $absent_amt_da = $_POST['absent'] * $month_da;
            $_POST[3] = $da_val = $da - $absent_amt_da;
            $_POST['ear_tot'] += round($_POST[3]);
        } elseif ($single['id'] == 4) {
            $hra = $_POST[4];
            $month_hra = $hra / $total_days;

            $absent_amt_hra = $_POST['absent'] * round($month_hra);

            $hra_val = $hra - $absent_amt_hra;

            $_POST[4] = $hra_val;
            if (isset($_POST[39])) {
                $_POST[47] -= $_POST[4];
            }

            $_POST['ear_tot'] += round($_POST[4]);
        } elseif ($single['id'] == 40) {
            if (isset($_POST[39])) {
                $_POST[47] -= $_POST[40];
            }

            $_POST['ear_tot'] += round($_POST[40]);
        } elseif ($single['id'] == 41) {
            if (isset($_POST[39])) {
                // display_error($_POST[43]);
                $_POST[47] -= $_POST[41];
            }
            $_POST['ear_tot'] += round($_POST[41]);
        } elseif ($single['id'] == 44) {
            if (isset($_POST[39])) {
             // $_POST[47] -= $_POST[44];
            }
         //   $_POST['ear_tot'] += round($_POST[44]);
        } elseif ($single['id'] == 45) {
            if (isset($_POST[39])) {
                $_POST[47] -= $_POST[45];
            }

            $_POST['ear_tot'] += round($_POST[45]);
        } elseif ($single['id'] == 52) {
            // display_error($_POST[45].'======'.$_POST[52]);
            if (isset($_POST[39])) {
                $_POST[47] -= $_POST[12];
            }

            $_POST['ear_tot'] += round($_POST[45]);
        } elseif ($single['id'] == 46) {
           
            if (isset($_POST[39])) {
        //  $_POST[47] -= $_POST[46];
            }

          //  $_POST['ear_tot'] += round($_POST[46]);
        } else {
            // 
            $_POST['ear_tot'] += round($_POST[$single['id']]);
        }
    } else {
        $_POST['ear_tot'] += round($_POST[$single['id']]);
    }
}
//$_POST[47] -= $_POST[10];
//$_POST['ear_tot'] -=$_POST[10];
//Adding Employer's PF & ASA in Total Earning





$_POST['ear_tot'] += round($_POST[12] + $_POST[6] );
if (isset($_POST[39])) {
    $_POST['ear_tot'] -= round($_POST[39]);
    $_POST['ear_tot'] -= round($_POST[12]);
    $_POST['ear_tot'] -= round($_POST[6]);
}


start_outer_table(TABLESTYLE);
table_section(1, '500px');
label_row(_(" Employee No:"), $_POST['employee_id'], null, 30, 30);
label_row(_(" Employee Name:"), $_POST['EmplName'], null, 30, 30);
label_row(_(" Department:"), $_POST['empl_dept'], null, 30, 30);
label_row(_(" Month of Payment:"), $month_name, null, 30, 30);

table_section_title(_("Earnings"));
$months_with_years_list[get_post('month')];
$total_days = date("t", strtotime($months_with_years_list[get_post('month')]));
//display_error($_POST['absent']);
$working_days = $total_days - $_POST['absent'];
$Allowance = get_allowances('Earnings');
$ctc = 0;
//display_error($_POST['lop_amount']);
while ($single = db_fetch($Allowance)) {
    //label_row(_($single['description']), price_format(round(($_POST[$single['id']]))), null, 30, 30);
    if ($_POST['is_sal'] != 1) {
        if ($ctc <= 0) {
            if (isset($_POST[39])) {
                label_row(_('Actual CTC'), price_format(round(($_POST[39] + $_POST[40]))), null, 30, 30);
                label_row(_('Food Coupon'), price_format(round(($_POST[40]))), null, 30, 30);
                if ($_POST[40] > 0)
                    label_row(_('CTC (After adding food coupon)'), price_format(round(($_POST[39]))), null, 30, 30);
                if ($_POST['lop_amount'] > 0) {
                    label_row(_('CTC (After LOP)'), price_format(round(($_POST[39] - round($_POST['lop_amount'])))), null, 30, 30);
                    $_POST[39] = round(($_POST[39] - round($_POST['lop_amount'])));
                }
                $ctc++;
            }
        }
        if ($single['id'] == 1) {
            /* $basic = $_POST[1];
              $month_sal=$basic/$total_days;
              $absent_amt=$_POST['absent']*$month_sal;
              $basic_lopamt=$basic-$absent_amt;
              $_POST[1]=$basic_lopamt; */
            label_row(_($single['description']), price_format(round(($_POST[1]))), null, 30, 30);
        } elseif ($single['id'] == 2) {
            /* $basic = $_POST[1];
              $month_sal=$basic/$total_days;
              $absent_amt=$_POST['absent']*$month_sal;
              $basic_lopamt=$basic-$absent_amt;
              $_POST[1]=$basic_lopamt; */
            label_row(_($single['description']), price_format(round(($_POST[2]))), null, 30, 30);
        } elseif ($single['id'] == 3) {
            /* $da = $_POST[3];
              $month_da=$da/$total_days;
              $absent_amt_da=$_POST['absent']*$month_da;
              $_POST[3]=$da_val=$da-$absent_amt_da; */
            label_row(_($single['description']), price_format(round(($_POST[3]))), null, 30, 30);
        } elseif ($single['id'] == 4) {
            /* $hra = $_POST[4];
              $month_hra=$hra/$total_days;
              $absent_amt_hra=$_POST['absent']*$month_hra;
              $_POST[4]=$hra_val=$hra-$absent_amt_hra; */
            label_row(_($single['description']), price_format(round(($_POST[4]))), null, 30, 30);
        } elseif ($single['id'] == 8) {
            /* $hra = $_POST[4];
              $month_hra=$hra/$total_days;
              $absent_amt_hra=$_POST['absent']*$month_hra;
              $_POST[4]=$hra_val=$hra-$absent_amt_hra; */
            label_row(_($single['description']), price_format(round(($_POST[8]))), null, 30, 30);
        } elseif ($single['id'] == 39) {
            
        } elseif ($single['id'] == 52) {
            if ($_POST[1] < 15000) {
                $_POST[$single['id']] = round($_POST[12]);
            } else {
                $_POST[$single['id']] = 1800;
                $_POST[12] = 1800;
            }
            label_row(_($single['description']), price_format(round(($_POST[$single['id']]))), null, 30, 30);
        } elseif ($single['id'] == 41) {
            $real_pf = $_POST[41];
            // display_error(round($_POST[39]));
            if ($_POST['eligible_esi'] != 2)
                $_POST[$single['id']] = round((round($_POST[39]) - $_POST[12]) * 4.75 / 100);
            else
                $_POST[$single['id']] = 0;
            $_POST[47] += round($real_pf - $_POST[$single['id']]);

            label_row(_($single['description']), price_format(round(($_POST[$single['id']]))), null, 30, 30);
        }elseif ($single['id'] == 46) {
            if (isset($sal_row['net_pay'])) {
                label_row(_("Incentive:"), $_POST[46]);
            } else {
                text_row(_("Incentive:"), $single['id'], round($_POST[46]), 10, 10, null, '', '', TRUE);
                // $deduct_tot+=$tds;
            }
            $_POST['ear_tot']+=$_POST[46];
        }elseif ($single['id'] == 44) {
            if (isset($sal_row['net_pay'])) {
                label_row(_("Salary Advance:"), $_POST[44]);
            } else {
                text_row(_("Salary Advance:"), $single['id'], round($_POST[44]), 10, 10, null, '', '', TRUE);
                // $deduct_tot+=$tds;
            }
            $_POST['ear_tot']+=$_POST[44];
        }elseif ($single['id'] == 40) {
            
        } else {
            label_row(_($single['description']), price_format(round(($_POST[$single['id']]))), null, 30, 30);
        }
    } else {
        if ($single['id'] != 39)
            label_row(_($single['description']), price_format(round(($_POST[$single['id']]))), null, 30, 30);
    }
}

if ($ctc != 1) {
    label_row(_("EPF (Employer's Cont.):"), price_format(round($_POST[12]))); //New
    label_row(_("NPS-SAS (8.33% - Employer's Cont.)"), price_format(round($_POST[6]))); //New
}
//label_row(_("Conveyance Allowance:"), $_POST['conveyance_allowance']);

label_row(_("Leave Encashment:"), price_format($_POST['leave_encashment']));
//hidden('leave_encashment', $_POST['leave_encashment']);
label_row(_("Arrears (If any)"), $_POST['conveyance_allowance']); //New

if (is_numeric(stripos($_POST['employee_id'], 'EMP-F'))) {
    label_row(_("Faculty Others Earnings(If any)"), $_POST['ot_other_allowance']); //New
} else if (is_numeric(stripos($_POST['employee_id'], 'EMP-S'))) {
    label_row(_("Staff Others Earnings(If any)"), $_POST['ot_other_allowance']); //New 
} else {
    label_row(_("Others Earnings(If any)"), $_POST['ot_other_allowance']); //New  
}




table_section_title(_(""));
label_row(_(" Total Earning(Gross Salary):"), price_format(round($_POST['ear_tot'])), 'style="color:#FF9800; background-color:#f9f2bb;"', 'style="color:#FF9800; background-color:#f9f2bb;"');

table_section(2, '500px');

label_row(_(" Date of Payment:"), date("d-F-Y", strtotime($_POST['today_date'])), null, 30, 30);
label_row(_(" LOP Days:"), $_POST['absent'], null, 30, 30);
label_row(_(" Designation:"), $_POST['desig'], null, 30, 30);

table_section_title(_("Deduction"));

$Allowance = get_allowances('Deductions');
while ($single = db_fetch($Allowance)) {
    //label_row(_($single['description']), price_format(round((($_POST[$single['id']])*$working_days)/($total_days))), null, 30, 30);
    if ($_POST['is_sal'] != 1) {
        if ($single['id'] == 12) {
            label_row(_($single['description']), price_format(round($_POST[12])), null, 30, 30);
        } elseif ($single['id'] == 6) {
            label_row(_($single['description']), price_format(round($_POST[6])), null, 30, 30);
        } elseif ($single['id'] == 43) {
            $real_esi = $_POST[43];
            //display_error($_POST[43]);
            if ($_POST['eligible_esi'] != 2)
                $default_value = round((round($_POST[39]) - $_POST[12]) * 4.75 / 100);
            else
                $default_value = 0;
            $deduct_tot -= round($_POST[43] - $default_value);
            $_POST[43] = $default_value;

            label_row(_($single['description']), price_format(round($_POST[$single['id']])), null, 30, 30);
        }
         elseif ($single['id'] == 49) {
            $real_esi = $_POST[49];

            if ($_POST['eligible_esi'] != 2)
                $default_value = round((round($_POST[39]) - $_POST[12]) * 1.75 / 100);
            else
                $default_value = 0;
            $deduct_tot -= round($_POST[49] - $default_value);
            $_POST[49] = $default_value;

            label_row(_($single['description']), price_format(round($_POST[$single['id']])), null, 30, 30);
        }
        elseif ($single['id'] == 10) {
            $proftax = $_POST[10];
            if (isset($sal_row['net_pay'])) {
                label_row(_("Prof Tax:"), round($proftax));
            } else {
                text_row(_("Prof Tax:"), 'proftax', round($proftax), 10, 10, null, '', '', TRUE);
                // $deduct_tot+=round($proftax);
            }
        } else {
            label_row(_($single['description']), price_format(round($_POST[$single['id']])), null, 30, 30);
        }
    } else {
        label_row(_($single['description']), price_format(round($_POST[$single['id']])), null, 30, 30);
    }
}

label_row(_("HRA deducted (In case of accommodation provided by ORG):"), price_format(round($accom_by_org_hra)), null, 30, 30);
label_row(_("EPF (Employer's Cont.):"), price_format(round($_POST[12])), null, 30, 30);
//if(isset($_POST[6]))
label_row(_("NPS-SAS (8.33% - Employer's Cont.):"), price_format(round($_POST[6])), null, 30, 30);
label_row(_("Recovery of salary advance/loan:"), 0, null, 30, 30);
label_row(_("Electricity Bill:"), 0, null, 30, 30);
//label_row(_("Canteen/cafeteria charges:"), 0, null, 30, 30);
//TDS ADDED ON 06-12


if (isset($sal_row['net_pay'])) {
    label_row(_("TDS:"), $_POST['tds']);
} else {
    text_row(_("TDS:"), 'tds', round($tds), 10, 10, null, '', '', TRUE);
    // $deduct_tot+=$tds;
}
label_row(_("TDS (on arrears, if any):"), 0, null, 30, 30);

if (isset($sal_row['net_pay'])) {
    label_row(_(" Other deduction. :"), $_POST['misc'], null, 10, 30);
} else {
    text_row(_(" Other deduction. :"), 'misc', null, 10, 30, NULL, '', '', TRUE);
    submit_cells('RefreshInquiry', _("Refresh"), '', _('Show Results'), 'default');
}
     $Ajax->activate('_page_body');

if (!isset($sal_row['net_pay'])) {

    $_POST['net_pay'] = $_POST['ear_tot'] - $deduct_tot;
}

table_section_title(_(""));

label_row(_(" Total Deductions"), price_format(round($deduct_tot)), 'style="color:#f55; background-color:#fed;"', 'style="color:#f55; background-color:#fed;"');
hidden('deduct_tot', $deduct_tot);
//label_row(_(" "), '', null, 30, 30);
label_row(_(" Net Salary Payable:"), price_format(round($_POST['net_pay'])), 'style="color:#107B0F; background-color:#B7DBC1;"', 'style="color:#107B0F; background-color:#B7DBC1;"');

end_outer_table();


if (db_has_employee_payslip($employee_id, $month, $year) == false && $employee_id != null) {
    br();
    br();

    $Allowance = get_allowances('Deductions');
    while ($single = db_fetch($Allowance)) {
        hidden($single['id'], $_POST[$single['id']]);
    }
    hidden('lop_amount', $_POST['lop_amount']);
    hidden('loan', $_POST['loan']);
    hidden('deduct_tot', $deduct_tot);
    hidden('net_pay', $_POST['net_pay']);
    hidden('date_of_pay', Today());
    $end_day_of_this = date("Y-m-t", strtotime($months_with_years_list[get_post('month')]));
    if ($end_day_of_this < date('Y-m-d')) {
        submit_center('pay_salary', _("Process Payout"), true, _('Payout to Employees'), 'default');
    } else {
        if ($is_arrear != 1) {
            display_warning(_("You can't Process Payroll of future!"));
        }
    }
    br();
    end_form();
}
if ($is_arrear == 1) {
    $rs_status = db_has_employee_payslip1($employee_id, $month, $year);
} else {
    $rs_status = db_has_employee_payslip($employee_id, $month, $year);
}
if ($rs_status == true && $employee_id != null) {
    br();
    br();
    //print_document_link($row['trans_no'], _("Print"), true, ST_CUSTDELIVERY, ICON_PRINT);

    if (!isset($sal_row['id'])) {
        $sal_row['id'] = 0;
    }
    $result = get_gl_trans(99, $sal_row['id']);

    if (db_num_rows($result) == 0) {
        echo "<p><center>" . _("No general ledger transactions have been created for") . "</center></p><br><br>";
        end_page(true);
        exit;
    }

    /* show a table of the transactions returned by the sql */
    $dim = get_company_pref('use_dimension');

    if ($dim == 2)
        $th = array(_("Account Code"), _("Account Name"), _("Dimension") . " 1", _("Dimension") . " 2",
            _("Debit"), _("Credit"), _("Memo"));
    else if ($dim == 1)
        $th = array(_("Account Code"), _("Account Name"), _("Dimension"),
            _("Debit"), _("Credit"), _("Memo"));
    else
        $th = array(_("Account Code"), _("Account Name"),
            _("Debit"), _("Credit"), _("Memo"));
    $k = 0; //row colour counter
    $heading_shown = false;

    $credit = $debit = 0;
    while ($myrow = db_fetch($result)) {
        if ($myrow['amount'] == 0)
            continue;
        if (!$heading_shown) {
            start_table(TABLESTYLE, "width='95%'");
            table_header($th);
            $heading_shown = true;
        }

        alt_table_row_color($k);

        label_cell($myrow['account']);
        label_cell($myrow['account_name']);
        if ($dim >= 1)
            label_cell(get_dimension_string($myrow['dimension_id'], true));
        if ($dim > 1)
            label_cell(get_dimension_string($myrow['dimension2_id'], true));

        display_debit_or_credit_cells(round($myrow['amount']));
        label_cell($myrow['memo_']);
        end_row();
        if ($myrow['amount'] > 0)
            $debit += round($myrow['amount']);
        else
            $credit += round($myrow['amount']);
    }
    if ($heading_shown) {
        start_row("class='inquirybg' style='font-weight:bold'");
        label_cell(_("Total"), "colspan=2");
        if ($dim >= 1)
            label_cell('');
        if ($dim > 1)
            label_cell('');
        amount_cell($debit);
        amount_cell(-$credit);
        label_cell('');
        end_row();
        end_table(1);
    }

    echo '<center> <a href="' . $path_to_root . '/modules/ExtendedHRM/reports/rep802.php?PARAM_0=' . $year . '&PARAM_1=' . $month . '&PARAM_2=' . $employee_id . '&rep_v=yes&is_arrear=' . $is_arrear . '" target="_blank" class="printlink"> Print </a> </center>';
    br();
}
div_end();

if (get_post('pay_salary')) {
    $salary_account = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'salary_account'));
    $paid_from_account = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'paid_from_account'));

    $jobs_arr = array('empl_id' => $_POST['employee_id'],
        'month' => $_POST['month'],
        'year' => $_POST['year'],
        'gross' => $_POST['ear_tot'],
        'deduct_tot' => $deduct_tot,
        'loan' => $_POST['loan'],
        'date' => array(Today(), 'date'),
        'adv_sal' => $_POST['adv_sal'],
        'net_pay' => $_POST['net_pay'],
        'misc' => $_POST['misc'],
        'ot_other_allowance' => $_POST['ot_other_allowance'],
        'lop_amount' => $_POST['lop_amount'],
        'conveyance_allowance' => $_POST['conveyance_allowance'],
        'leave_encashment' => $_POST['leave_encashment'],
        'tds' => $_POST['tds'],
        'accom_hra_by_org' => $_POST['eligible_hra'],
        'eligible_esi' => $_POST['eligible_esi']);
    $months_with_years_list[get_post('month')];
    $total_days = date("t", strtotime($months_with_years_list[get_post('month')]));
    //display_error($_POST['absent']);
    $working_days = $total_days - $_POST['absent'];

    $Allowance = get_allowances();
    while ($single = db_fetch($Allowance)) {
        //$jobs_arr[$single['id']] = round((($_POST[$single['id']])*($working_days))/($total_days));
        //if()
        $jobs_arr[$single['id']] = round($_POST[$single['id']]);
        //}
    }
    $pay_slip_id = Insert('kv_empl_salary', $jobs_arr);
            
    /*
     * if value has inserted proper 
     * then only update encashment 
     * request table and make is_paid = 1;
     */
    if($pay_slip_id){
            update_encashment_request($_POST['employee_id']);
            }

    if ($_POST['loan'] > 0) {
        paid_empl_loan_month_payment($_POST['employee_id']);
    }

    //Array Mapping for all the salary componenets form posting & Heads of Finance account
    /* $salary_post = array(
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
      14 => 'misc', //Other Deduction
      //15 =>   39,//CTC
      16=>   46,//incentive
      17=>    40 ,//food coupon
      //18=>     43 ,//esi
      20=>     44 ,//salary advance
      21=>    47  //specialAllowance
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
        13 => 12, //EPF
        14 => 10, //Professional Tax
        16 => 41, //ESIC 4.75
        17 => 'leave_encashment'
    );



    $allowance = get_all_hrm_finance_setting();
    $pending_salary_comp = array();
    //For Debit
    $debit_total = 0;
    $credit_total = 0;
    //=====[--[TALLY VOUCHER HEARER SATRTS]--]==============//
   //addVoucherHeader(kv_get_empl_name($_POST['employee_id']));
    
   // $requestXML = importHeader();  
    while ($single = db_fetch($allowance)) {//Loop over all salary components
        // display_error($single['allowance_debit_gl_code'].'=='.$_POST[$salary_post[$single['id']]]);
        if ($single['id'] == 15) {
            $pending_salary_comp = $single;
        }
        

        if (($single['type'] == 'Salary') && $single['inactive'] == 0) {
            if ($single['allowance_debit_gl_code'] != 0 && isset($_POST[$salary_post[$single['id']]]) && is_numeric($_POST[$salary_post[$single['id']]]) && $_POST[$salary_post[$single['id']]] != 0) {

                if ($single['allowance_debit_gl_code'] != 8122 && isset($_POST[39])) {
                    add_gl_trans(99, $pay_slip_id, $_POST['date_of_pay'], $single['allowance_debit_gl_code'], 0, 0, 'Employee ' . $single['description'] . ' #' . $_POST['employee_id'] . '-' . kv_get_empl_name($_POST['employee_id']), $_POST[$salary_post[$single['id']]]);
                    $debit_total += $_POST[$salary_post[$single['id']]];  
                    //=====[--[TALLY VOUCHER DEBIT SATRTS]--]==============//
                     /* $result = getAllAccountListName($single['allowance_debit_gl_code']);
                      $ledger_name = db_fetch($result);*/


                    
                } elseif (!isset($_POST[39])) {
                    add_gl_trans(99, $pay_slip_id, $_POST['date_of_pay'], $single['allowance_debit_gl_code'], 0, 0, 'Employee ' . $single['description'] . ' #' . $_POST['employee_id'] . '-' . kv_get_empl_name($_POST['employee_id']), $_POST[$salary_post[$single['id']]]);
                }
            }
            if ($single['allowance_credit_gl_code'] != 0 && isset($_POST[$salary_post[$single['id']]]) && is_numeric($_POST[$salary_post[$single['id']]]) && $_POST[$salary_post[$single['id']]] != 0) {//Skip for 'None'
			//=====[for tally purpose]
              /* $result = getAllAccountListName($single['allowance_credit_gl_code']);
                $ledger_name = db_fetch($result);
               $requestXML.= addLedgerBody($ledger_name['name'], $ledger_name['account_name'], -$_POST[$salary_post[$single['id']]]);*/
                
                add_gl_trans(99, $pay_slip_id, $_POST['date_of_pay'], $single['allowance_credit_gl_code'] , 0, 0, 'Employee ' . $single['description'] . ' #' . $_POST['employee_id'] . '-' . kv_get_empl_name($_POST['employee_id']), -$_POST[$salary_post[$single['id']]]);
                $credit_total += $_POST[$salary_post[$single['id']]];

            }
        }
       
    } 

 
    //=====[--[TALLY VOUCHER FOOTER ENDS]--]==============//
   
    //Processing Pending Salary after deduction PF,ASA,Prof. Tax, etc.
   // $result = getAllAccountListName($pending_salary_comp['allowance_credit_gl_code']);
     //         $ledger_name = db_fetch($result);
                    
    $pending_salary = $debit_total - $credit_total;
    add_gl_trans(99, $pay_slip_id, $_POST['date_of_pay'], $pending_salary_comp['allowance_credit_gl_code'], 0, 0, 'Employee ' . $pending_salary_comp['description'] . ' #' . $_POST['employee_id'] . '-' . kv_get_empl_name($_POST['employee_id']), -$pending_salary);
    
      //---------------------------------Status update for Manual entry for lop After salary is add into gltable--------------------
                $man_data['status'] = 1;
                Update('manual_sal_deduction', array('id',$id), $man_data);
     //----------------------------------------------------------------------------------------------------------------------------
        
    //$requestXMLVoucher .= addVoucherFooter();
    //$filename = writeFile($requestXMLVoucher, "../../tally/tally_core/voucher.xml");
       // addNew($filename);
    //============================[--ADD LEDGER FOR SALARY PAYBLE--]=======================//
    // $requestXML.= addLedgerBody($ledger_name['name'], $ledger_name['account_name'], -$pending_salary);
    

    
    
      // $requestXML.= importFooter();  
    //$filename = writeFile($requestXML, "../../tally/tally_core/createLFile.xml");
    //addNew('createLFile.xml');
    

                    
                    
    
    
    //=======================[--FOR VOUCHER ENTRY 12-02-2019--]===================================//
 /* $requestXML = addVoucherHeader('Salary',$pay_slip_id,$_POST['date_of_pay'],$ledger_name['account_name'],0,0,'Employee ' . $pending_salary_comp['description'] . ' #' . $_POST['employee_id'] . '-' . kv_get_empl_name($_POST['employee_id']),0,kv_get_empl_name($_POST['employee_id']));
    
     $requestXML.=addVoucherDebitBody('Salary',$pay_slip_id,$_POST['date_of_pay'],$ledger_name['account_name'],0,0,'Employee ' . $pending_salary_comp['description'] . ' #' . $_POST['employee_id'] . '-' . kv_get_empl_name($_POST['employee_id']),$debit_total,kv_get_empl_name($_POST['employee_id']));
    
     $requestXML.=addVoucherCreditBody('Salary',$pay_slip_id,$_POST['date_of_pay'],$ledger_name['account_name'],0,0,'Employee ' . $pending_salary_comp['description'] . ' #' . $_POST['employee_id'] . '-' . kv_get_empl_name($_POST['employee_id']),-$debit_total,kv_get_empl_name($_POST['employee_id']));
    
     $requestXML.=addVoucherFooter();
     
      $filename = writeFile($requestXML, "../../tally/tally_core/voucher.xml");
    addNew('voucher.xml');
   */ 
    //add_gl_trans(99, $pay_slip_id, $_POST['date_of_pay'], $salary_account, 0,0, 'Employee Salary #'.$_POST['employee_id'].'-'. kv_get_empl_name($_POST['employee_id']), $_POST['net_pay']);
    //add_gl_trans(99, $pay_slip_id, $_POST['date_of_pay'], $paid_from_account, 0,0, 'Employee Salary #'.$_POST['employee_id'].'-'. kv_get_empl_name($_POST['employee_id']), -$_POST['net_pay']);

meta_forward($_SERVER['PHP_SELF'], "Added=$pay_slip_id&employee_id=" . $_POST['employee_id'] . '&month=' . $_POST['month'] . '&year=' . $_POST['year']);
}
end_page();
?>