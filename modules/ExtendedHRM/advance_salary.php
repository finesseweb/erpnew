<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'SA_OPEN';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
$js = "";
if ($use_popup_windows)
 $js .= get_js_open_window(900, 500);
if ($use_date_picker)
 $js .= get_js_date_picker();
 
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");

page(_("PaySlip"));

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));
 
 check_db_has_salary_account(_("There are no Salary Account defined in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/hrm_settings.php'>Settings</a> to update it."));


if(isset($_GET['Added'])){
	display_notification(' The Employee Payslip is added #' .$_GET['Added']);
}
if (isset($_GET['employee_id'])){
	$_POST['employee_id'] = $_GET['employee_id'];
}
if (isset($_GET['month'])){
	$_POST['month'] = $_GET['month'];
}
if (isset($_GET['year'])){
	$_POST['year'] = $_GET['year'];
}
$employee_id = get_post('employee_id','');
$month = get_post('month','');
$year = get_post('year','');

if(list_updated('month') || get_post('RefreshInquiry')) {
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
		employee_list_cells(_("Select an Employee: "), 'employee_id', null,	_('New Employee'), true, check_value('show_inactive'));
		
		end_row();
		end_table();
		br();
		if (get_post('_show_inactive_update')) {
			$Ajax->activate('employee_id');
			$Ajax->activate('month');
			$Ajax->activate('year');
			set_focus('employee_id');
		}
	} 
	else {	
		hidden('employee_id');
		hidden('month');
		hidden('year');
	}

	$_POST['EmplName'] =  $_POST['desig']=''; 
	$_POST['ear_tot']= $_POST['deduct_tot']= $_POST['empl_dept']= $_POST['basic']= $_POST['empl_da'] = $_POST['empl_hra'] = $_POST['conveyance'] = $_POST['edu_other'] =  $_POST['employee_id'] = $_POST['absent'] = $_POST['empl_pf']= $_POST['lop_amount'] =  $_POST['adv_sal']= $_POST['net_pay'] = $_POST['loan'] =$_POST['empl_esi']=$_POST['medical_allowance']= 0;
	$dat_of_pay = Today();


 	if(isset($_POST['prof_tax']) && $_POST['prof_tax'] > 0){ }
 	else{
 		$_POST['prof_tax'] = 0 ;
 	}
 	if(isset($_POST['misc']) && $_POST['misc'] > 0){ }
 	else{
 		$_POST['misc'] = 0 ;
 	}
 	
 	$month_name = kv_month_name_by_id(get_post('month'));
	
		$_POST['today_date']=date("d-F-Y");
		if(isset($employee_id) && $employee_id != '') {
			$sal_row = get_empl_sal_details($employee_id, $month, $year); 
			$name_and_dept = get_empl_name_dept($employee_id); 
			$_POST['empl_dept']=get_department_name($name_and_dept ['deptment']);
			$_POST['absent'] =  get_empl_attendance_for_month($employee_id, $month, $year);
			$_POST['desig'] = kv_get_empl_desig($employee_id);
			$_POST['EmplName']=$name_and_dept ['name'];
			$_POST['basic']= $sal_row['basic']; 
			$_POST['empl_da'] = $sal_row['empl_da']; 
			$_POST['empl_hra'] = $sal_row['empl_hra']; 
			$_POST['conveyance'] = $sal_row['conveyance']; 
			$_POST['medical_allowance'] = $sal_row['medical_allowance']; 
			$_POST['edu_other'] =  $sal_row['edu_other']; 
			$_POST['employee_id'] = $employee_id;	
			$_POST['empl_pf']= $sal_row['empl_pf'];
			$_POST['empl_esi']= $sal_row['empl_esi'];
			$_POST['misc'] =  $sal_row['misc']; 
			$_POST['loan'] = get_empl_loan_monthly_payment($employee_id);
			$_POST['ear_tot']	= $_POST['edu_other']+$_POST['conveyance']+$_POST['empl_hra']+$_POST['empl_da']+$_POST['basic']+$_POST['medical_allowance'];
			$total_days =  date("t", strtotime($year."-".$month."-01"));
			$_POST['lop_amount'] =  round($_POST['absent']*$_POST['ear_tot']/$total_days, 2);

			if(isset($sal_row['net_pay'])){
				$_POST['lop_amount'] =  $sal_row['lop_amount'];
				$_POST['prof_tax'] = $sal_row['prof_tax'];
				$_POST['adv_sal']= $sal_row['adv_sal'];
				$_POST['misc'] =  $sal_row['misc']; 
				$_POST['net_pay'] = $sal_row['net_pay'];			 
				$_POST['today_date'] = $sal_row['date'];	
				$_POST['loan'] = $sal_row['loan']		 ;
			} 		
		}

	//display_notification($month_name);
			$deduct_tot = $_POST['adv_sal']+$_POST['lop_amount']+$_POST['empl_pf']+$_POST['empl_esi']+$_POST['loan'];
			if(isset($_POST['prof_tax'])){
				$deduct_tot += get_post('prof_tax');
			}
			if(isset($_POST['misc'])){
				$deduct_tot += get_post('misc');
			}
			if(!isset($sal_row['net_pay'])){
				$_POST['net_pay'] = $_POST['ear_tot']-$deduct_tot;
			}	

		start_outer_table(TABLESTYLE);
		table_section(1);
		label_row(_(" Employee No:"), $_POST['employee_id'], null, 30, 30);
		label_row(_(" Employee Name:"), $_POST['EmplName'], null, 30, 30);
		label_row(_(" Department:"), $_POST['empl_dept'], null, 30, 30);
		label_row(_(" Month of Payment:"), $month_name, null, 30, 30);
		
		table_section_title(_("Earning"));
		label_row(_(" Basic:"), $_POST['basic'], null, 30, 30);
		label_row(_(" DA:"), $_POST['empl_da'], null, 30, 30);
		label_row(_(" HRA:"), $_POST['empl_hra'], null, 30, 30);
		label_row(_(" Conveyance:"), $_POST['conveyance'], null, 30, 30);
		label_row(_(" Education/Other Allowance :"), $_POST['edu_other'], null, 30, 30);
		label_row(_(" Medical Allowance :"), $_POST['medical_allowance'], null, 30, 30);
		//label_row(_(" "), '', null, 30, 30);
		table_section_title(_(""));
		label_row(_(" Total Earning(Gross Salary):"), $_POST['ear_tot'], 'style="color:#FF9800; background-color:#f9f2bb;"', 'style="color:#FF9800; background-color:#f9f2bb;"');

		table_section(2);
		
		label_row(_(" Date of Payment:"), date("d-F-Y", strtotime($_POST['today_date'])), null, 30, 30);
		label_row(_(" LOP Days:"), $_POST['absent'], null, 30, 30);
		label_row(_(" Designation:"), $_POST['desig'], null, 30, 30);
	   
		table_section_title(_("Deduction"));
		label_row(_(" Providant Fund:"), $_POST['empl_pf'], null, 30, 30);
		label_row(_(" ESI :"), $_POST['empl_esi'], null, 30, 30);
		label_row(_(" LOP Amount:"), $_POST['lop_amount'], null, 30, 30);	
		//label_row(_(" Adv Salary:"), $_POST['adv_sal'], null, 30, 30);
		label_row(_(" Loan :"), $_POST['loan'], null, 30, 30);
		if(isset($sal_row['net_pay'])){
			label_row(_(" Profession Tax:"), $_POST['prof_tax'], null, 10, 10);
			label_row(_(" Miscellaneous Expense:"), $_POST['misc'], null, 10, 30);
		}else{
			text_row(_(" Profession Tax:"), 'prof_tax', null, 10, 10);
			text_row(_(" Miscellaneous Expense:"), 'misc', null, 10, 30);
			submit_cells('RefreshInquiry', _("Calculate"),'',_('Show Results'), 'default');
		}
		
		table_section_title(_(""));

		label_row(_(" Total Deductions"), $deduct_tot, 'style="color:#f55; background-color:#fed;"', 'style="color:#f55; background-color:#fed;"');
		label_row(_(" "), '', null, 30, 30);
		label_row(_(" Net Salary Payable:"), $_POST['net_pay'], 'style="color:#107B0F; background-color:#B7DBC1;"', 'style="color:#107B0F; background-color:#B7DBC1;"');

		end_outer_table();

		
		if(db_has_employee_payslip($employee_id, $month, $year) == false && $employee_id != null){
			br();
			br();
			//hidden('year', $year);  hidden('month', $month);hidden('employee_id', $employee_id);
			hidden('basic', $_POST['basic']);
			hidden('empl_da', $_POST['empl_da']);
			hidden('empl_hra', $_POST['empl_hra']);
			hidden('empl_pf', $_POST['empl_pf']);
			hidden('conveyance', $_POST['conveyance']);
			hidden('edu_other', $_POST['edu_other']);
			hidden('medical_allowance', $_POST['medical_allowance']);
			hidden('gross', $_POST['ear_tot']);
			hidden('lop_amount', $_POST['lop_amount']);
			hidden('empl_esi', $_POST['empl_esi']);
			hidden('loan', $_POST['loan']);
			hidden('net_pay', $_POST['net_pay']);
			//echo $date_of_pay; 
			hidden('date_of_pay', Today());

			submit_center('pay_salary', _("Process Payout"), true, _('Payout to Employees'), 'default');
			br();
			end_form();
		}
		if(db_has_employee_payslip($employee_id, $month, $year) == true && $employee_id != null){
			br();
			br(); 
//print_document_link($row['trans_no'], _("Print"), true, ST_CUSTDELIVERY, ICON_PRINT);
			echo '<center> <a href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep802.php?PARAM_0='.$year.'&PARAM_1='.$month.'&PARAM_2='.$employee_id.'&rep_v=yes" target="_blank" > Print </a> </center>'; 
			br();
		}
	div_end();

	if(get_post('pay_salary')) {
		$salary_account= GetSingleValue('kv_empl_option', 'option_value', array('option_name'=>'salary_account'));
		$paid_from_account= GetSingleValue('kv_empl_option', 'option_value', array('option_name'=>'paid_from_account'));
		$pay_slip_id = add_empl_salary($_POST['employee_id'], $_POST['month'], $_POST['year'],$_POST['gross'], $_POST['basic'], $_POST['empl_da'], $_POST['empl_hra'], $_POST['conveyance'], $_POST['medical_allowance'], $_POST['edu_other'],  $_POST['lop_amount'], $_POST['empl_pf'], $_POST['loan'], $_POST['adv_sal'], $_POST['net_pay'], $_POST['prof_tax'], $_POST['empl_esi'], $_POST['misc']); 
		//display_notification(' The Employee Payslip is added #' .$_POST['date_of_pay']);
		if($_POST['loan'] > 0 )
			paid_empl_loan_month_payment($_POST['employee_id']);
		add_gl_trans(99, $pay_slip_id, $_POST['date_of_pay'], $salary_account, 0,0, 'Employee Salary #'.$_POST['employee_id'], $_POST['net_pay']);
		add_gl_trans(99, $pay_slip_id, $_POST['date_of_pay'], $paid_from_account, 0,0, 'Employee Salary #'.$_POST['employee_id'], -$_POST['net_pay']);
		
		meta_forward($_SERVER['PHP_SELF'], "Added=$pay_slip_id&employee_id=".$_POST['employee_id'].'&month='.$_POST['month'].'&year='.$_POST['year']);
	}

end_page(); ?>
