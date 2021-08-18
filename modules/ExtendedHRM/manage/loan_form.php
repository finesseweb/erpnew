<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_TOURFORM';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
$version_id = get_company_prefs('version_id');

$js = '';
if($version_id['version_id'] == '2.4.1'){
	if ($SysPrefs->use_popup_windows) 
		$js .= get_js_open_window(900, 500);	

	if (user_use_date_picker()) 
		$js .= get_js_date_picker();
	
}else{
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
}
 
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
page(_("Loan Application Form"));

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php' target='_blank'>Add And Manage Employees</a> to update it"));

check_db_has_Loan_types(_("There is no Loan Type defined in the system. Please define some <a href='".$path_to_root."/modules/ExtendedHRM/manage/loan_type.php' target='_blank'>Loan Type</a> "));
if (isset($_GET['selected_id']))
	$_POST['selected_id'] = $_GET['selected_id'];
if(isset($_GET['Added'])){
	display_notification("New Loan Added for the Selected Employee");
}
	
$selected_id = get_post('selected_id');

if (list_updated('selected_id')) {
	$_POST['empl_id'] = $selected_id = get_post('selected_id');
   	$Ajax->activate('details');	
}

function can_process() {
	
	if ($_POST['empl_id'] == ""){
		display_error(_("There is no Employee selected."));
		set_focus('selected_id');
		return false;
	} 
	if ($_POST['loan_amount'] == ""  || !check_num('loan_amount', 0)){
		display_error(_("Loan Amount is Empty or Not number"));
		set_focus('loan_amount');
		return false;
	} 
	if ($_POST['periods'] == ""|| !check_num('periods', 0)){
		display_error(_("Term Period is Empty or not Number."));
		set_focus('periods');
		return false;
	} 
	if ($_POST['loan_type_id'] == ""){
		display_error(_("Select Loan Type."));
		set_focus('loan_id');
		return false;
	} 
	if ($_POST['monthly_pay'] == "" || !check_num('monthly_pay', 0)){
		display_error(_("Calculate the Monthly Pay and Save it."));
		set_focus('loan_id');
		return false;
	} 
	$form_date = date('Y-m-d', strtotime($_POST['date']));
	$today_date = date("Y-m-d");
	if($form_date <= $today_date){
		display_error(_("Enter a Valid Date."));
		set_focus('date');
		return false;
	}
return true;	
}
if (isset($_POST['processloan'])&& can_process()) {
	add_empl_loan($_POST['empl_id'], $_POST['date'], input_num('loan_amount'), $_POST['loan_type_id'], $_POST['periods'],input_num('monthly_pay'),0,'Active');
	meta_forward($_SERVER['PHP_SELF'], "Added=yes&selected_id=".$_POST['empl_id']);
}
start_form(true);

if (db_has_employees()) {
	start_table(TABLESTYLE_NOBORDER);
	start_row();   
	employee_list_cells(_("Select an Employee: "), 'selected_id', null,	_('New Employee'), true, check_value('show_inactive'));
	$new_item = get_post('selected_id')=='';	
	end_row();
	end_table();

	if (get_post('_show_inactive_update')) {
		$Ajax->activate('selected_id');
		set_focus('selected_id');
	}
}
else{
	hidden('selected_id', get_post('selected_id'));
}

function PMT($i, $n, $p) {
	$i = $i/1200; 
	$p = -$p; 
	return $i * $p * pow((1 + $i), $n) / (1 - pow((1 + $i), $n));
}

//echo (number_format(PMT(3.56 , 36, 20000),2)); 

div_start('details');

	$_POST['empl_name'] = kv_get_empl_name($selected_id);
	start_outer_table(TABLESTYLE2);
	table_section(1);
	label_row(_("Empl Id").':', $selected_id, 20);	
		
	hidden('empl_id', $selected_id); 
	$get_details = get_empl_loan($selected_id);
	$loan_type= get_single_loan_type($get_details['loan_type_id']);


	$expd_percentage_amt =GetSingleValue('kv_empl_option', 'option_value', array('option_name'=>'expd_percentage_amt'))/100; // Here i have to add the get_option function to reterive the settings details.
	$expected_netpay = get_employee_net_pay($selected_id);
	$get_maximum_monthly_pmt = $expected_netpay * $expd_percentage_amt; 
	label_row(_("Max. Available:").':', $get_maximum_monthly_pmt, 20);
	hidden('get_maximum_monthly_pmt', $get_maximum_monthly_pmt); 


	table_section_title(_(""));		
	if(db_has_empl_loan($selected_id)){
		label_row(_("Loan Amount").':', $get_details['loan_amount']);
		label_row(_("Periods").':', $get_details['periods']);
		label_row(_("Interest Rate:"), $loan_type['interest_rate'].'%');
		label_row(_("Loan Type:"), $loan_type['loan_name']);
		
	} else {
		text_row_ex(_("Loan Amount").':', 'loan_amount', 15);
		text_row_ex(_("Periods").':', 'periods', 8);
		
		kv_loan_list_cells(_("Loan type: "), 'loan_id', null,	_('Select a Loan Type'), true);
		submit_row('Refreshloan', _("Calculate"),'',_('Show Results'), 'default');
	}
	table_section(2);

	$expected_netpay = get_employee_net_pay($selected_id);
	label_row(_("Employee Name").':', $_POST['empl_name'], 20);
	label_row(_("Net Pay").':', $expected_netpay, 20);
	hidden('net_pay', $expected_netpay); 

	table_section_title(_(""));
	if(db_has_empl_loan($selected_id)){
		label_row(_("Months Paid").':', $get_details['periods_paid']);
		label_row(_("Monthly Payment").':', $get_details['monthly_pay']);
		if($get_details['periods'] >0 ){
			$periods_pay = $get_details['periods'];
			$date_started = $get_details['date'];
		} else{
			$periods_pay = get_post('periods');
			$date_started = date('Y-m-d'); 
		}

		$date_of_end = date('Y-m-d', strtotime("+".$periods_pay." months", strtotime($date_started)));
	
		label_row(_("Start Period").':', sql2date($date_started));
		label_row(_("End Period").':', sql2date($date_of_end));
	} else{
		//text_row_ex(_("Months Left").':', 'periods_paid', 8);
		div_start('totals_tbl');
		if (($_POST['loan_id'] != "") && ($_POST['loan_id'] != ALL_TEXT)&& ($_POST['periods'] != "") && ($_POST['periods'] != ALL_TEXT) && ($_POST['loan_amount'] != "") && ($_POST['loan_amount'] != ALL_TEXT)){
			$interest =get_loan_interest_rate($_POST['loan_id']);
			hidden('loan_type_id', $_POST['loan_id']); 
			$_POST['monthly_pay'] = number_format((float)PMT($interest, trim($_POST['periods']), trim(input_num('loan_amount'))), 2);
		}
		text_row_ex(_("Monthly Payment").':', 'monthly_pay', 8);
		date_row(_("Start Period") . ":", 'date');
		div_end();
	}
	end_outer_table(1);	
if(db_has_empl_loan($selected_id)){} else {
		//display_warning( ? 'Right': 'Wrong');
	
	if(isset($_POST['net_pay']) && isset($_POST['get_maximum_monthly_pmt']) && (input_num('get_maximum_monthly_pmt') < input_num('monthly_pay'))){
		display_warning("The Selected Employee's Maximum Monthly Installment(".input_num('get_maximum_monthly_pmt')."), You can't provide loan more than that. You better revise the periods of pay or change the loan amount.");
	}else
		submit_center_first('processloan', 'Place Loan',  _('Check entered data and save document'), 'default');
}

if(get_post('Refreshloan')){
	$Ajax->activate('totals_tbl');
}
 div_end();
end_form();  
br();
if(db_has_empl_inactive_loan($selected_id)){
	$loans = GetDataJoin('kv_empl_loan AS loan', array( 
					0 => array('join' => 'INNER', 'table_name' => 'kv_loan_types AS type', 'conditions' => '`type`.`id` = `loan`.`loan_type_id`'),
					1 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `loan`.`empl_id`') ), 
							array('`info`.`empl_id`, `info`.`empl_firstname`, `type`.`loan_name`, `loan`.`loan_amount`, `loan`.`monthly_pay`, `loan`.`periods`, `loan`. `periods_paid`, `loan`. `date`, `loan`.`status`'),
							array('`loan`.`empl_id`' => $selected_id));
			
			start_table(TABLESTYLE);
				echo  "<tr> <td class='tableheader'>" . _("Loan Type") . "</td>
					<td class='tableheader'>" . _("Loan Amount") . "</td>
					<td class='tableheader'>" . _("Monthly Pay") . "</td>
					<td class='tableheader'>" . _("Periods") . "</td>
					<td class='tableheader'>" . _("Periods Paid") . "</td>
					<td class='tableheader'>" . _("Start Date") . "</td>
					<td class='tableheader'>" . _("End Date") . "</td>
					<td class='tableheader'>" . _("Status") . "</td></tr>";

					foreach($loans as $loan_single) {
						$date_of_end = date('Y-m-d', strtotime("+".$loan_single[5]." months", strtotime($loan_single[7]))); 
						echo '<tr style="text-align:center"><td>'.$loan_single[2].'</td><td>'.$loan_single[3].'</td><td>'.$loan_single[4].'</td><td>'.$loan_single[5].'</td><td>'.$loan_single[6].'</td><td>'.sql2date($loan_single[7]).'</td><td>'.sql2date($date_of_end).'</td><td>'.$loan_single[8].'</td><tr>';
					}
			end_table(1);
}
end_page();
 
?>