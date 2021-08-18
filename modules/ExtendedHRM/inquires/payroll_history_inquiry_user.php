<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_PAYSLIP_USER';
$path_to_root="../../..";
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");

$version_id = get_company_prefs('version_id');

$js = '';
if($version_id['version_id'] == '2.4.1'){
	if (!@$_GET['popup']){
		if ($SysPrefs->use_popup_windows) 
			$js .= get_js_open_window(900, 500);	

		if (user_use_date_picker()) 
			$js .= get_js_date_picker();
	}
}else{
	if (!@$_GET['popup']){
		if ($use_popup_windows)
			$js .= get_js_open_window(900, 500);
		if ($use_date_picker)
			$js .= get_js_date_picker();
	}
}
page(_("Payroll Inquiry"));
 
check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));

$empl_id = $_SESSION['wa_current_user']->empl_id;

if (isset($_GET['selected_id'])){
	$_POST['selected_id'] = $_GET['selected_id'];
}
if (isset($_GET['month'])){
	$_POST['month'] = $_GET['month'];
}
if (isset($_GET['year'])){
	$_POST['year'] = $_GET['year'];
}

if(isset($_GET['Added'])){
	display_notification(' The Employees Payroll Processed Successfully');
}


$selected_id = get_post('selected_id','');
$month = get_post('month','');
$year = get_post('year','');
 
 if(list_updated('month')) {
		$month = get_post('month');   
		$Ajax->activate('totals_tbl');
}
start_form(true);

if (db_has_employees()) {
	start_table(TABLESTYLE_NOBORDER);
	start_row();	
		kv_fiscalyears_list_cells(_("Fiscal Year:"), 'year', null, true);
		kv_current_fiscal_months_list_cell("Months", "month", null, true);
		department_list_cells(_("Select a Department: "), 'selected_id', null,	_('No Department'), true, check_value('show_inactive'));
	end_row();	
	end_table();

	if (get_post('_show_inactive_update') || get_post('month') || get_post('year')) {
		$Ajax->activate('selected_id');
		$Ajax->activate('month');
		$Ajax->activate('year');
		$Ajax->activate('sal_calculation');
		set_focus('selected_id');
	}
}
else{
	hidden('selected_id', get_post('selected_id'));
	hidden('month', get_post('month'));
	hidden('year', get_post('year'));
}
div_start('sal_calculation');
	start_table(TABLESTYLE_NOBORDER, "width=40%");
	label_row(" <center>**Here, you can view the Calculated Salaries.  </center>", '', null ); 
	end_table();
   
	start_table(TABLESTYLE, "width=90%");
    $th = array(_("Empl Id "),_("Employee Name") );

    $Allowance = get_allowances('Earnings');
	while ($single = db_fetch($Allowance)) {	
		$th[] = $single['description'];
	}
        //$th[] = _("Conveyance Allowance");
	//$th[] = _("OT & Other Allowance");
	
	 $th[] = _("EPF (Employer):");
        $th[] = _("SAS (Employer):");
	$th[] = _("Leave Encashment");
	$th[] = _("Gross Pay");
	
	$Allowance = get_allowances('Deductions');
	while ($single = db_fetch($Allowance)) {	
		$th[] = $single['description'];
	}
   //$th1 = array(_("Adv Salary"),_("TDS"),_("Loan"),_("LOP Days"),_("LOP Amount"),_("Misc."),_("Total Deduction"),_("Net Salary"), _(""), _(""));
   $th1 = array(_("TDS"),_("Other Ded."),_("Total Deduction"),_("Net Salary"), _(""), _(""));
   	$th_final = array_merge($th, $th1);

	table_header($th_final);
	
	$ipt_error = 0;

	$prof_tax = kv_get_Taxable_field();
		
	if(empty($selected_id)) $selected_id = 0;
		/*if($hrm_year_list[$year]<= date('Y')){}
		else {
			display_error(_('The Selected Year Yet to Born!'));
			$ipt_error = 1;
		}*/
		
		$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);

		$months_with_years_list[get_post('month')];
	
		if($months_with_years_list[get_post('month')] > date('Y-m-d')){
			display_error(_('The Selected Month Yet to Born!'));
			$ipt_error = 1;
		}
		if($ipt_error ==0) {
			$get_employees_list = get_empl_ids_from_dept_id_by_empl_id($selected_id,$empl_id);
                       
        //print_r($get_employees_list);
			//kv_get_employees_list_based_on_dept2($selected_id,false,$empl_id);
			$Total_gross = $total_net = 0; 
			foreach($get_employees_list as $single_empl) { 
				//echo $single_empl;
				$data_for_empl_group = GetAll('kv_empl_salary', array('empl_id' => $single_empl, 'month' => $month, 'year' => $year));
                  /// echo '<pre>'; print_r($single_empl.'===='.$month.'======'.$year);
                                foreach($data_for_empl_group as $data_for_empl){
                                    //print_r($data_for_empl);
				if($data_for_empl) {
					start_row();
					$employee_leave_record = get_empl_attendance_for_month($data_for_empl['empl_id'], $month, $year);
					label_cell($data_for_empl['empl_id']);
					label_cell(kv_get_empl_name($data_for_empl['empl_id']));

					$EarAllowance = get_allowances('Earnings');
					while ($single = db_fetch($EarAllowance)) {	
						label_cell($data_for_empl[$single['id']]);
					}
                                        label_cell($data_for_empl['12']);

					label_cell($data_for_empl['6']);
					
					label_cell($data_for_empl['leave_encashment']);
					label_cell($data_for_empl['gross']);

					//$total_deduct = $data_for_empl['misc']+$data_for_empl['loan']+$data_for_empl['lop_amount']+$data_for_empl['adv_sal']+$data_for_empl['tds']; 
					$Allowance = get_allowances('Deductions');
					while ($single = db_fetch($Allowance)) {	
						label_cell($data_for_empl[$single['id']]);
						//$total_deduct += $data_for_empl[$single['id']];
					}
					//label_cell($data_for_empl['adv_sal']);
					label_cell($data_for_empl['tds']);
					//label_cell($data_for_empl['loan']);
					//label_cell($employee_leave_record);
					//label_cell($data_for_empl['lop_amount']);
					label_cell($data_for_empl['misc']);					
					label_cell($data_for_empl['deduct_tot']);
					label_cell($data_for_empl['net_pay']);

					$Total_gross += $data_for_empl['gross'];
					$total_net += $data_for_empl['net_pay'];
					//label_cell($data_for_empl['other_deduction']);
                                        $arrear_q_string = '';
                                        $arrear = '';
					if($data_for_empl['is_arrear'] == 1){
						$arrear_q_string = '&is_arrear=1';
                                                $_SESSION['user']['arrear'] = 1;
					}
					label_cell('<a href="'.$path_to_root.'/modules/ExtendedHRM/payslip_user.php?employee_id='.$data_for_empl['empl_id'].'&month='.$month.'&year='.$year.'&slip_id='.$data_for_empl['id'].$arrear_q_string.'" onclick="javascript:openWindow(this.href,this.target); return false;"  target="_blank" > <img src="'.$path_to_root.'/themes/default/images/gl.png" width="12" height="12" border="0" title="GL"></a>');
                                        //========for security purpose========//
                                        $_SESSION['user']['empl_id'] = $data_for_empl['empl_id'];
                                        $_SESSION['user']['month'] = $month;
                                        $_SESSION['user']['year'] = $year;
                                        $_SESSION['user']['slip_id'] = $data_for_empl['id'];
                                        
                                        
					
					label_cell('<a onclick="javascript:openWindow(this.href,this.target); return false;" href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep802.php?PARAM_0='.$year.'&PARAM_1='.$month.'&PARAM_2='.$data_for_empl["empl_id"].'&rep_v=yes&slip_id='.$data_for_empl['id'].$arrear_q_string.'" target="_blank" class="printlink"> <img src="'.$path_to_root.'/themes/default/images/print.png" width="12" height="12" border="0" title="Print"> </a>');
					
					end_row();
				}
                                }
                                
			}
			start_row();
			$Earnings_colum_count = get_allowances_count('Earnings');
			$Deductions_colum_count = get_allowances_count('Deductions');
			$gross_colm_cnt = $Earnings_colum_count+4; 
			$net_colm_cnt = $Deductions_colum_count+3; 
				echo " <td colspan='".$gross_colm_cnt."'> </td> <td><strong>Total Gross</strong></td><td><strong>".$Total_gross."</strong></td> ";
				echo "<td colspan='".$net_colm_cnt."' align='right'></td> <td colspan='2'><strong>Total Net Salary</strong></td> <td><strong>". $total_net."</strong></td><td> </td><td> </td>  ";
			end_row();
		}
    end_table(1);
	div_end(); 
if (!@$_GET['popup'])
{
	end_form();
	end_page(@$_GET['popup'], false, false);
}

 
?>