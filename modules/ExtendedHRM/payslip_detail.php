<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */
$page_security = 'HR_PAYSLIP_DETAILS';
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

include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");

//display_error(price_format($_POST['accom_hra_by_org']));

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




if (list_updated('month') || get_post('RefreshInquiry')|| get_POST('check') || get_post('misc') || list_updated('is_arrear')) {
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
    //  echo "<td>BASIC: <input type='checkbox' name='checked' /></td>";
    check_cells("Default Values : ", 'check',NULL, true);
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

$_POST['EmplName'] = $_POST['desig'] = '';
$_POST['ear_tot'] = $_POST['deduct_tot'] = $_POST['empl_dept'] = $_POST['employee_id'] = $_POST['absent'] = $_POST['lop_amount'] = $_POST['net_pay'] = $_POST['loan'] = 0;

//$allow = kv_get_sal_details_file(12, 1);
//	while ($single = db_fetch($allow)) {	
//		print_r($single);
//	}

 /*$Allowance = get_allowances();
  while ($single = db_fetch($Allowance)) {
  $_POST[$single['id']]=0;
  }*/





$dat_of_pay = Today();

$prof_tax = kv_get_Taxable_field();


/*if (isset($_POST[$prof_tax]) && $_POST[$prof_tax] > 0) {
    
} else {
    $_POST[$prof_tax] = 0;
}*/
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
    
} else {
    $tds = 0;
    if (!empty($employee_id)) {
        
         
            $tds_detail = kv_calculate_tds($employee_id);
             if(!empty($employee_id)){
            if(get_post('check')==1){
            if ((int) (int)$_POST['tds'] == (int) $tds_detail['payable_tds_per_month'] ) {
        $tds = $tds_detail['payable_tds_per_month'];
        } else {
            $tds = (int)$_POST['tds'];
        }
            }
            else
            {
               $tds = $tds_detail['payable_tds_per_month']; 
            }
    }
    }
 
}
$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);



if(get_post('check')==1){
    $_POST['accom_hra_by_org'] = get_person_hra($employee_id, $month, $year);
}


if(isset($_POST['employee_id']) && (is_numeric(stripos($_POST['employee_id'], 'EMP-F')) || is_numeric(stripos($_POST['employee_id'], 'EMP-S')))){
$person_id = get_person_id($employee_id,$_POST['month'], $_POST['year']);
$result2 = false;
}



$months_with_years_list[get_post('month')];
//display_error($months_with_years_list[get_post('month')]);
$joining_date = get_employee_join_date($employee_id);

$month_name = kv_month_name_by_id(get_post('month'));

    $_POST[12] = $_POST['neepf'];
           

$_POST['today_date'] = date("d-F-Y");
   if(isset($employee_id) && $employee_id != '' && date("Y-m",strtotime($months_with_years_list[get_post('month')])) >= date("Y-m",strtotime(get_employee_join_date($employee_id))))
                {
    $sal_row = get_empl_sal_detail2($employee_id, $month, $year, $is_arrear, $slip_id);
    $name_and_dept = get_empl_name_dept($employee_id);
    $_POST['empl_dept'] = get_department_name($name_and_dept ['deptment']);
    $_POST['absent'] = get_empl_attendance_for_month($employee_id, $month, $year);

    $_POST['desig'] = kv_get_empl_desig($employee_id);
    $_POST['EmplName'] = $name_and_dept ['name'];

    $Allowance = get_allowances();
    while ($single = db_fetch($Allowance)) {
     
        if(get_post('check') == 1){
            
             $_POST[$single['id']] = $sal_row[$single['id']];
        }
        else if ((int) str_replace(",", "", $_POST[$single['id']]) == (int) $sal_row[$single['id']] ) {
            $_POST[$single['id']] = $sal_row[$single['id']];
        }
         else if((int) str_replace(",", "", $_POST[$single['id']]) != (int) $sal_row[$single['id']])  {
            $_POST[$single['id']] = (int)str_replace(",", "", $_POST[$single['id']]);
        }
        else
        {
           $_POST[$single['id']]=00; 
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
        $gross_4_LOP += $_POST[$single['id']];
    }


  
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

        if ((int) str_replace(",", "", $_POST['leave_encashment']) == (int) $sal_row['leave_encashment'] || (int) str_replace(",", "", $_POST['leave_encashment']) == 0) {
            $_POST['leave_encashment'] = $sal_row['leave_encashment'];
        } else {
            $_POST['leave_encashment'] = (int) str_replace(",", "", $_POST['leave_encashment']);
        }
        $_POST['today_date'] = $sal_row['date'];
        $_POST['loan'] = $sal_row['loan'];
        $_POST['tds'] = $sal_row['tds'];
       // display_error($_POST['eligible_hra']);
         if ((int) str_replace(",", "", $_POST['accom_hra_by_org']) == (int) $sal_row['accom_hra_by_org'] ) {
            $_POST['accom_hra_by_org'] = $sal_row['accom_hra_by_org'];
        } else {
            $_POST['accom_hra_by_org'] = (int) str_replace(",", "", $_POST['accom_hra_by_org']);
        }
        
        
        //$_POST['eligible_hra'] = $sal_row['accom_hra_by_org'];
    } else {
        if ((int) str_replace(",", "", $_POST['leave_encashment']) == (int) $sal_row['13'] ){
            $_POST['leave_encashment'] = $sal_row['13'];
        } else {
            $_POST['leave_encashment'] = (int) str_replace(",", "", $_POST['leave_encashment']);
        }
      
            $_POST['eligible_hra'] = $sal_row['eligible_hra'];
              
        if($_POST['eligible_hra']==2){
            $_POST['accom_hra_by_org']  = $_POST['4'];
        }
        else if($_POST['eligible_hra']==1){
            $_POST['accom_hra_by_org']  = 0;
        }else{
            
        }          
 //display_error($_POST['eligible_hra'].'=='.$_POST['accom_hra_by_org']);
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
$DedAllowance = get_allowances('Deductions');






while ($single = db_fetch($DedAllowance)){
  
     if(get_post('check') == 1){
             $_POST[$single['id']] = $sal_row[$single['id']];
        }
    else if ((int) str_replace(",", "", $_POST[$single['id']]) == (int) $sal_row[$single['id']]) {
      //  display_error($single['id']);
        $_POST[$single['id']] = $sal_row[$single['id']];
    }  else if((int) str_replace(",", "", $_POST[$single['id']]) != (int) $sal_row[$single['id']])  {
        $_POST[$single['id']] = (int) str_replace(",", "", $_POST[$single['id']]);
    }
    else
    {
        $_POST[$single['id']]=00;
        $_POST[12] = 00;
    }
    if ($single['id'] != $prof_tax)
        $deduct_tot += round($_POST[$single['id']]);
    //$deduct_tot += round($_POST[$single['id']]);
}



if($person_id)
$result2 = get_nps($person_id, 3339);

if($result2)
    $_POST['nps_er'] = abs($result2);





if($person_id)
$result2 = get_nps($person_id, 3338);
if($result2)
    $_POST['nps'] = abs($result2);


$deduct_tot += round($_POST[12] + $_POST['nps_er']);

$deduct_tot += $_POST['nps'];
$deduct_tot-=$_POST['nps_er'];
//Adding Employer's PF & ASA in Total Earning
$_POST['ear_tot'] += $_POST[12] + $_POST['nps_er'];


if (isset($_POST[$prof_tax])) {
    $deduct_tot += get_post($prof_tax);
}
if (isset($_POST['misc'])) {
    $deduct_tot += get_post('misc');
}

      $deduct_tot += $_POST['accom_hra_by_org'];
  //  $accom_by_org_hra = $_POST['accom_hra_by_org'];



$EarAllowance = get_allowances('Earnings');
$_POST['ear_tot'] = $_POST['ot_other_allowance'] + $_POST['conveyance_allowance'] + $_POST['leave_encashment'];

while ($single = db_fetch($EarAllowance)) {

    $_POST['ear_tot'] += round($_POST[$single['id']]);
}
//Adding Employer's PF & ASA in Total Earning
$_POST['ear_tot'] += round($_POST[12] + $_POST['nps_er']);

if (!isset($sal_row['net_pay'])) {
    $_POST['net_pay'] = $_POST['ear_tot'] - $deduct_tot;
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
//display_error($working_days);
//display_error($_POST['nps_er']);
$Allowance = get_allowances('Earnings');
while ($single = db_fetch($Allowance)) {
    text_row(_($single['description']), $single['id'], price_format(round(($_POST[$single['id']]))), 10, 10, NULL, '', '', TRUE);
    //label_row(_($single['description']), price_format(round(($_POST[$single['id']]))), null, 30, 30);
}

/*if(get_post("check")!=1){
    $_POST['ear_tot'] -=$_POST[12];
     $deduct_tot-=$_POST[12]*2;
    $_POST[12]=00;
}*/

text_row(_("EPF (Employer's Cont.):"), "neepf", price_format($_POST[12]), 10, 10, NULL, '', '', true);
text_row(_("NPS-SAS"), 'nps_er', round($_POST['nps_er']), 10, 10, NULL, '', '', true);
text_row(_("Leave Encashment:"), 'leave_encashment', price_format($_POST['leave_encashment']), 10, 10, NULL, '', '', true);
text_row(_("Arrears (If any)"), 'conveyance_allowance', $_POST['conveyance_allowance'], 10, 10,NULL,'','',TRUE);
//display_error(stripos($_POST['employee_id'], 'EMP-F'));
if(is_numeric(stripos($_POST['employee_id'], 'EMP-F'))){
text_row(_("Faculty Others Earnings(If any)"), 'ot_other_allowance', $_POST['ot_other_allowance'], 10, 10, NULL, '', '', true);
}
else if(is_numeric(stripos($_POST['employee_id'], 'EMP-S'))){
  text_row(_("Staff Others Earnings(If any)"), 'ot_other_allowance', $_POST['ot_other_allowance'], 10, 10, NULL, '', '', true);  
}
else
{
    text_row(_("Others Earnings(If any)"), 'ot_other_allowance', $_POST['ot_other_allowance'], 10, 10, NULL, '', '', true);  
}
//label_row(_("Conveyance Allowance:"), $_POST['conveyance_allowance']);
// label_row(_("Leave Encashment:"), price_format($_POST['leave_encashment']));
//hidden('leave_encashment', $_POST['leave_encashment']);
//label_row(_("Arrears (If any)"), $_POST['conveyance_allowance']);//New
//text_row(_("Other earnings"), 'conveyance_allowance', $_POST['conveyance_allowance'], 10, 10, NULL, '', '', false);
//label_row(_("Other earnings"), $_POST['conveyance_allowance']); //New
/*
  if(isset($sal_row['net_pay'])){
  label_row(_("Conveyance Allowance:"), $_POST['conveyance_allowance']);
  }else{
  text_row(_("Conveyance Allowance:"), 'conveyance_allowance', null, 10, 10);
  }

  if(isset($sal_row['net_pay'])){
  label_row(_("Other Allowance:"), $_POST['ot_other_allowance']);
  }else{
  text_row(_("Other Allowance:"), 'ot_other_allowance', null, 10, 10);
  }


  if(isset($sal_row['net_pay'])){
  label_row(_("Leave Encashment:"), $_POST['leave_encashment']);
  }else{
  text_row(_("Leave Encashment:"), 'leave_encashment', null, 10, 10);
  }
 * 
 */

//label_row(_(" "), '', null, 30, 30);

table_section_title(_(""));
label_row(_(" Total Earning(Gross Salary):"), price_format($_POST['ear_tot']), 'style="color:#FF9800; background-color:#f9f2bb;"', 'style="color:#FF9800; background-color:#f9f2bb;"');

table_section(2, '500px');

label_row(_(" Date of Payment:"), date("d-F-Y", strtotime($_POST['today_date'])), null, 30, 30);
label_row(_(" LOP Days:"), $_POST['absent'], null, 30, 30);
label_row(_(" Designation:"), $_POST['desig'], null, 30, 30);

table_section_title(_("Deduction"));

$Allowance = get_allowances('Deductions');





while ($single = db_fetch($Allowance)) {
    //label_row(_($single['description']), price_format(round((($_POST[$single['id']])*$working_days)/($total_days))), null, 30, 30);
    
       text_row(_($single['description']),$single['id'] , price_format(round($_POST[$single['id']])), 10, 10, NULL, '', '', true);
  
}
//display_error($_POST['accom_hra_by_org']);
//label_row(_("HRA deducted (In case of accommodation provided by DMI):"), price_format($accom_by_org_hra), null, 30, 30);
text_row(_("HRA deducted (In case of accommodation provided by DMI):"), 'accom_hra_by_org', $_POST['accom_hra_by_org'], 10, 10, NULL, '', '', true);
text_row(_("EPF (Employer's Cont.):"), 'epf', $_POST[12], 10, 10, NULL, '', '', true);

text_row(_("NPS-SAS :"), 'nps', $_POST['nps'], 10, 10, NULL, '', '', true);

text_row(_("Recovery of salary advance/loan:"), 'loan', price_format($_POST['loan']), 10, 10, NULL, '', '', true);
text_row(_("Electricity Bill:"), 'electricity', 0, 10, 10, NULL, '', '', true);
//label_row(_("Recovery of salary advance/loan:"), 0, null, 30, 30);
//label_row(_("Electricity Bill:"), 0, null, 30, 30);
//label_row(_("Canteen/cafeteria charges:"), 0, null, 30, 30);


if (isset($sal_row['net_pay'])) {
    label_row(_("TDS:"), $_POST['tds']);
} else {
    text_row(_("TDS:"), 'tds', null, 10, 10, NULL, '', '', true);
}
//text_row(_("TDS (on arrears, if any):"), 'tds', null, 10, 10, NULL, '', '', TRUE);
label_row(_("TDS (on arrears, if any):"), 0, null, 30, 30);
//label_row(_("Any other deduction:"), 0, null, 30, 30);
/*
  if(isset($sal_row['net_pay'])){
  label_row(_("Recovery of Adv:"), $_POST['adv_sal']);
  }else{
  text_row(_("Recovery of Adv:"), 'adv_sal', null, 10, 10);
  }
 * 
 */

//label_row(_(" LOP Amount:"), $_POST['lop_amount'], null, 30, 30);
//label_row(_(" Loan :"), $_POST['loan'], null, 30, 30);
if (isset($sal_row['net_pay'])) {
    label_row(_(" Other deduction. :"), $_POST['misc'], null, 10, 30);
} else {
    text_row(_(" Other deduction. :"), 'misc', null, 10, 30, NULL, '', '', TRUE);
    submit_cells('RefreshInquiry', _("Refresh"), '', _('Show Results'), 'default');
}



table_section_title(_(""));

label_row(_("Total Deductions"), price_format($deduct_tot), 'style="color:#f55; background-color:#fed;"', 'style="color:#f55; background-color:#fed;"');
hidden('deduct_tot', $deduct_tot);
label_row(_(" "), '', null, 30, 30);
label_row(_(" Net Salary Payable:"), price_format($_POST['net_pay']), 'style="color:#107B0F; background-color:#B7DBC1;"', 'style="color:#107B0F; background-color:#B7DBC1;"');

end_outer_table();


if (db_has_employee_payslip($employee_id, $month, $year) == false && $employee_id != null) {
    br();
    br();

    $Allowance = get_allowances('Deductions');
    while ($single = db_fetch($Allowance)) {
        //  hidden($single['id'], $_POST[$single['id']]);
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
            //display_gl_heading($myrow);
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

        display_debit_or_credit_cells($myrow['amount']);
        label_cell($myrow['memo_']);
        end_row();
        if ($myrow['amount'] > 0)
            $debit += $myrow['amount'];
        else
            $credit += $myrow['amount'];
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
        'accom_hra_by_org' => $_POST['accom_hra_by_org']);
    $months_with_years_list[get_post('month')];
    $total_days = date("t", strtotime($months_with_years_list[get_post('month')]));
    //display_error($_POST['absent']);
    $working_days = $total_days - $_POST['absent'];

    $Allowance = get_allowances();
    while ($single = db_fetch($Allowance)) {
        //$jobs_arr[$single['id']] = round((($_POST[$single['id']])*($working_days))/($total_days));	
        if($single['id']==6){
           
        }
        else{
        $jobs_arr[$single['id']] = round($_POST[$single['id']]);
            }
    }
    $pay_slip_id = Insert('kv_empl_salary', $jobs_arr);

    if ($_POST['loan'] > 0) {
        paid_empl_loan_month_payment($_POST['employee_id']);
    }

 
    //Array Mapping for all the salary componenets form posting & Heads of Finance account
    $salary_post = array(
        1 => 1, //Basic
        2 => 3, //DA
        3 => 2, //Grade Pay
        4 => 4, //HRA
        5 => 8, //Conveyance
        6 => 12, //EPF-Employer Contribution
        7 => 'nps_er', //NPS-SAS- Employer Contribution
        8 => 'leave_encashment', //Leave Encashment
        9 => 'nps', //NPS-SAS-Employee Contribution
        10 => 10, //Professional Tax
        11 => 12, //EPF-Employee Contribution
        12 => 'tds', //TDS
        14 => 'misc', //Other Deduction
    );
    
    
    if(is_numeric(stripos($_POST['employee_id'], 'EMP-F'))){
      $salary_post[16] = 'ot_other_allowance';
}
else if(is_numeric(stripos($_POST['employee_id'], 'EMP-S'))){ 
     $salary_post[17] = 'ot_other_allowance';
}
else{
}

    $allowance = get_all_hrm_finance_setting();
    $pending_salary_comp = array();
    //For Debit
    $debit_total = 0;
    $credit_total = 0;
    while ($single = db_fetch($allowance)) {


        if ($single['id'] == 13) {
            $pending_salary_comp = $single;
        }
       
        
        if (($single['type'] == 'Salary') && $single['inactive'] == 0) {
            if ($single['allowance_debit_gl_code'] != 0 && isset($_POST[$salary_post[$single['id']]]) && is_numeric($_POST[$salary_post[$single['id']]]) && $_POST[$salary_post[$single['id']]] != 0) {
                add_gl_trans(99, $pay_slip_id, $_POST['date_of_pay'], $single['allowance_debit_gl_code'], 0, 0, 'Employee ' . $single['description'] . ' #' . $_POST['employee_id'] . '-' . kv_get_empl_name($_POST['employee_id']), $_POST[$salary_post[$single['id']]]);
                $debit_total += $_POST[$salary_post[$single['id']]];
            }
            
            
            if ($single['allowance_credit_gl_code'] != 0 && isset($_POST[$salary_post[$single['id']]]) && is_numeric($_POST[$salary_post[$single['id']]]) && $_POST[$salary_post[$single['id']]] != 0) {//Skip for 'None'
                add_gl_trans(99, $pay_slip_id, $_POST['date_of_pay'], $single['allowance_credit_gl_code'], 0, 0, 'Employee ' . $single['description'] . ' #' . $_POST['employee_id'] . '-' . kv_get_empl_name($_POST['employee_id']), -$_POST[$salary_post[$single['id']]]);
                $credit_total += $_POST[$salary_post[$single['id']]];
            }
            
        }
    }
    //Processing Pending Salary after deduction PF,ASA,Prof. Tax, etc.
    $pending_salary = $debit_total - $credit_total;
    add_gl_trans(99, $pay_slip_id, $_POST['date_of_pay'], $pending_salary_comp['allowance_credit_gl_code'], 0, 0, 'Employee ' . $pending_salary_comp['description'] . ' #' . $_POST['employee_id'] . '-' . kv_get_empl_name($_POST['employee_id']), -$pending_salary);
    //add_gl_trans(99, $pay_slip_id, $_POST['date_of_pay'], $salary_account, 0,0, 'Employee Salary #'.$_POST['employee_id'].'-'. kv_get_empl_name($_POST['employee_id']), $_POST['net_pay']);
    //add_gl_trans(99, $pay_slip_id, $_POST['date_of_pay'], $paid_from_account, 0,0, 'Employee Salary #'.$_POST['employee_id'].'-'. kv_get_empl_name($_POST['employee_id']), -$_POST['net_pay']);

    meta_forward($_SERVER['PHP_SELF'], "Added=$pay_slip_id&employee_id=" . $_POST['employee_id'] . '&month=' . $_POST['month'] . '&year=' . $_POST['year']);
}

end_page();
?>