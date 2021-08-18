<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */
$page_security = 'SA_PF_REPORT';
$path_to_root = "../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
$version_id = get_company_prefs('version_id');

$js = '';
if ($version_id['version_id'] == '2.4.1') {
    if (!@$_GET['popup']) {
        if ($SysPrefs->use_popup_windows)
            $js .= get_js_open_window(900, 500);

        if (user_use_date_picker())
            $js .= get_js_date_picker();
    }
}else {
    if (!@$_GET['popup']) {
        if ($use_popup_windows)
            $js .= get_js_open_window(900, 500);
        if ($use_date_picker)
            $js .= get_js_date_picker();
    }
}

include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");
page(_("PF Inquiry"));

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

if (isset($_GET['Added'])) {
    display_notification(' The Employees Payroll Processed Successfully');
}

  // echo "<pre>"; print_r($_POST);

$selected_id = get_post('selected_id', '');
$month = get_post('month', '');
$year = get_post('year', '');

if (list_updated('month')) {
    $month = get_post('month');
    $Ajax->activate('totals_tbl');
}

start_form(true);
// echo "<form method='post' action='/pf_esi_report/modules/ExtendedHRM/reports/rep812.php' $name>\n";
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


$not_print_arr = array(39, 4, 40, 47, 44, 43, 49, 10, 41, 46, 51, 6, 12, 'leave_encashment');
$basic = 0;
div_start('sal_calculation');
start_table(TABLESTYLE_NOBORDER, "width=40%");
label_row(" <center>**Here, you can view the Calculated Employee Provident Fund(EPF).  </center>", '', null);
end_table();
start_form();
start_table(TABLESTYLE, "width=90%");
$th = array(_(''), _("Empl Id "), _("Employee Name"), ("UAN"));

$Allowance = get_allowances('Earnings');
while ($single = db_fetch($Allowance)) {
    if (!in_array($single['id'], $not_print_arr)) {
        if ($single['id'] == 1)
            $th[] = 'Gross Wage';
        elseif ($single['id'] == 52)
            $th[] = 'EPF Wage';
        else
            $th[] = $single['description'];
    }
}
//$th[] = _("Conveyance Allowance");
//$th[] = _("OT & Other Allowance");
$th[] = _("EPS Wages");
$th[] = _("EDLI Wages");
$th[] = _("EPF Contribution Remitted");
$th[] = _("EPS Contribution Remitted");
$th[] = _("EDLI Contribution");
//$th[] = _("SAS (Employer):");
//$th[] = _("Leave Encashment");
//$th[] = _("Gross Pay");
//display_error($Mode2);
$Allowance = get_allowances('Deductions');
while ($single = db_fetch($Allowance)) {
    if (!in_array($single['id'], $not_print_arr))
        $th[] = $single['description'];
}
//$th1 = array(_("Adv Salary"),_("TDS"),_("Loan"),_("LOP Days"),_("LOP Amount"),_("Misc."),_("Total Deduction"),_("Net Salary"), _(""), _(""));
$th1 = array(/* _("TDS"),_("Other Ded."),_("Total Deduction"),_("Net Salary"), _(""), _("") */);
$th_final = array_merge($th, $th1);

table_header($th_final);

$ipt_error = 0;

$prof_tax = kv_get_Taxable_field();

if (empty($selected_id))
    $selected_id = 0;
/* if($hrm_year_list[$year]<= date('Y')){}
  else {
  display_error(_('The Selected Year Yet to Born!'));
  $ipt_error = 1;
  } */

$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);

$months_with_years_list[get_post('month')];

if ($months_with_years_list[get_post('month')] > date('Y-m-d')) {
    display_error(_('The Selected Month Yet to Born!'));
    $ipt_error = 1;
}
if ($ipt_error == 0) {
    $get_employees_list = get_empl_ids_from_dept_id($selected_id);

    $Total_gross = $total_net = 0;

    $k=$j = 0;
    foreach ($get_employees_list as $key => $single_empl) {
        
        //echo $single_empl;
        $data_for_empl_group = GetAll('kv_empl_salary', array('empl_id' => $single_empl, 'month' => $month, 'year' => $year));
//echo '<pre>'; print_r($data_for_empl_group);

        foreach ($data_for_empl_group as $data_for_empl) {
            //print_r($data_for_empl);
            for ($i = 0; $i < 2; $i++) {
                if ($i == 1)
                    $not_print_arr = array(1, 39, 4, 40, 47, 44, 43, 49, 10, 41, 46, 51, 6, 12, 'leave_encashment');
                if ($data_for_empl) {
                    start_row();
                    $employee_leave_record = get_empl_attendance_for_month($data_for_empl['empl_id'], $month, $year);
                    $data_column = array('select_column' => 'pf_number',
                        'column_name' => 'empl_id',
                        'match' => $data_for_empl['empl_id']);

                    $pf_number = get_empl_info($data_column);
                    if ($i != 1) {
                        label_cell('Employer','',null,'center');

                        label_cell($data_for_empl['empl_id'],'',null,'center');

                        label_cell(kv_get_empl_name($data_for_empl['empl_id']),'',null,'center');

                        label_cell($pf_number[0] ? $pf_number[0] : 'N/A','',null,'center');

                    } else {
                        label_cell('Employee','',null,'center');
                        
                        label_cell('--','',null,'center');
                        label_cell('--','',null,'center');
                        label_cell('--','',null,'center');
                    }




                    $EarAllowance = get_allowances('Earnings');
                    $epf_wage = $data_for_empl[1] > 15000 ? 15000 : $data_for_empl[1];
                    while ($single = db_fetch($EarAllowance)) {
                        if ($i != 1) {
                            if (!in_array($single['id'], $not_print_arr)) {
                                if ($single['id'] == 52){
                                    label_cell($data_for_empl[1],'',null,'center');
                                    label_cell($epf_wage,'',null,'center');
                                }
                                elseif ($single['id'] == 1){
                                    label_cell($data_for_empl[1],'',null,'center');
                                }
                            }
                        }
                        elseif ($i == 1 && $single['id'] == 1) {
                            label_cell('--','',null,'center');
                            $j = 1;
                        }
                    }


                    //label_cell($epf_wage);
                    label_cell($epf_wage,'',null,'center');

                    //label_cell($data_for_empl['6']);
                    //label_cell($data_for_empl['leave_encashment']);
                    //label_cell($data_for_empl['gross']);
                    //$total_deduct = $data_for_empl['misc']+$data_for_empl['loan']+$data_for_empl['lop_amount']+$data_for_empl['adv_sal']+$data_for_empl['tds']; 
                    $Allowance = get_allowances('Deductions');
                    while ($single = db_fetch($Allowance)) {
                        if (!in_array($single['id'], $not_print_arr))
                            label_cell($data_for_empl[$single['id']],'',null,'center');
                        //$total_deduct += $data_for_empl[$single['id']];
                    }
                    //label_cell($data_for_empl['adv_sal']);
                    //label_cell($data_for_empl['tds']);
                    //label_cell($data_for_empl['loan']);
                    //label_cell($employee_leave_record);
                    //label_cell($data_for_empl['lop_amount']);
                    //label_cell($data_for_empl['misc']);					
                    //label_cell($data_for_empl['deduct_tot']);
                    //label_cell($data_for_empl['net_pay']);
                    if ($i != 1) {
                        if($j != 0)
                        label_cell($epf_wage,'',null,'center');
                        $epf_contri = round(($epf_wage * 3.67) / 100);
                        label_cell($epf_contri,'',null,'center');
                        $eps_contri = round(($epf_wage * 8.33) / 100);
                        label_cell($eps_contri,'',null,'center');
                        $edli_contri = round(($epf_wage * 0.5) / 100);
                        label_cell($edli_contri,'',null,'center');
                    } elseif ($i == 1) {
                        label_cell('--','',null,'center');
                        label_cell('--','',null,'center');
                        $epf_contri = round(($data_for_empl[52]));
                        label_cell($epf_contri,'',null,'center');
                        label_cell('--','',null,'center');
                        label_cell('--','',null,'center');
                    }
                    $Total_gross += $data_for_empl['gross'];
                    $total_net += $data_for_empl['net_pay'];
                    //label_cell($data_for_empl['other_deduction']);
                    $arrear_q_string = '';
                    if ($data_for_empl['is_arrear'] == 1) {
                        $arrear_q_string = '&is_arrear=1';
                    }
                    //label_cell('<a href="'.$path_to_root.'/modules/ExtendedHRM/payslip.php?employee_id='.$data_for_empl['empl_id'].'&month='.$month.'&                                                      year='.$year.'&slip_id='.$data_for_empl['id'].$arrear_q_string.'" onclick="javascript:openWindow(this.href,this.target); return false;"  target="_blank" > <img src="'.$path_to_root.'/themes/default/images/gl.png" width="12" height="12" border="0" title="GL"></a>');


                    end_row();
                    if($i!=1)
                    $k++;
                }
                
            }
            
        }
        
    }
    start_row();
    $Earnings_colum_count = get_allowances_count('Earnings');
    $Deductions_colum_count = get_allowances_count('Deductions');
    $gross_colm_cnt = $Earnings_colum_count + 4;
    $net_colm_cnt = $Deductions_colum_count + 3;
    //echo " <td colspan='".$gross_colm_cnt."'> </td> <td><strong>Total Gross</strong></td><td><strong>".$Total_gross."</strong></td> ";
    //echo "<td colspan='".$net_colm_cnt."' align='right'></td> <td colspan='2'><strong>Total Net Salary</strong></td> <td><strong>". $total_net."</strong></td><td> </td><td> </td>  ";
    end_row();
}

end_table(1);
  start_table(TABLESTYLE_NOBORDER);
    start_row();
                    label_cell('<a onclick="javascript:openWindow(this.href,this.target); return false;" href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep812.php?PARAM_0='.$year.'&PARAM_1='.$month.'&PARAM_2='.$selected_id.'" target="_blank" class="printlink"> <u>Print</u></a>');
end_row();
end_table();
end_form();

//display_error($Mode);

div_end();
    

if (!@$_GET['popup']) {
    end_form();
    end_page(@$_GET['popup'], false, false);
}
?>