<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/


$page_security = 'SA_OPEN';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
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
include_once($path_to_root . "/includes/date_functions.inc");
page(_("Loan Approve Inquiry"));
 
 simple_page_mode(true);

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));
//----------------------------------------------------------------------------------------
	$new_item = get_post('selected_id')=='' || get_post('cancel') ;
	$month = get_post('month','');
	$year = get_post('year','');
	if (isset($_GET['selected_id'])){
		$_POST['selected_id'] = $_GET['selected_id'];
	}
	$selected_id = get_post('selected_id');
	 if (list_updated('selected_id')) {
		$_POST['empl_id'] = $selected_id = get_post('selected_id');
	    $Ajax->activate('details');
	}
	if (isset($_GET['month'])){
		$_POST['month'] = $_GET['month'];
	}
	if (isset($_GET['year'])){
		$_POST['year'] = $_GET['year'];
	}

	
	if (list_updated('month')) {
		$month = get_post('month');   
		$Ajax->activate('details');
	}

//$month = date("m");
//----------------------------------------------------------------------------------------

	start_form(true);
		/*start_table(TABLESTYLE_NOBORDER);
			echo '<tr>';
				fiscalyears_list_cells(_("Fiscal Year:"), 'year');
			 	kv_current_fiscal_months_list_cell("Months", "month", null, true);
			 	department_list_cells(_("Select a Department: "), 'selected_id', null,	_('No Department'), true, check_value('show_inactive'));
				$new_item = get_post('selected_id')=='';
		 	echo '</tr>';
	 	end_table(1);

	 	if (get_post('_show_inactive_update')) {
			$Ajax->activate('month');
			$Ajax->activate('details');
			$Ajax->activate('selected_id');		
			set_focus('month');
		}
		if($month==null){			 
			$month = $_POST['month'];
		}
		if($year==null){			 
			$year = $_POST['year'];
		}
		//echo $month;
		$total_days =  date("t", strtotime($year."-".$month."-01"));
		div_start('details');
		*/
			
			//$selected_empl_attend_details=kv_get_attend_details($selected_id);
			$loans = GetDataJoin('kv_empl_loan AS loan', array( 
					0 => array('join' => 'INNER', 'table_name' => 'kv_loan_types AS type', 'conditions' => '`type`.`id` = `loan`.`loan_type_id`'),
					1 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `loan`.`empl_id`') ), 
							array('`info`.`empl_id`, `info`.`empl_firstname`, `type`.`loan_name`, `loan`.`loan_amount`, `loan`.`monthly_pay`, `loan`.`periods`, `loan`. `periods_paid`, `loan`. `date`, `loan`.`status`'));
			
			start_table(TABLESTYLE);
				echo  "<tr> <td class='tableheader'>" . _("Empl ID") . "</td>
					<td class='tableheader'>" . _("Empl Name") . "</td>					
					<td class='tableheader'>" . _("Loan Type") . "</td>
					<td class='tableheader'>" . _("Loan Amount") . "</td>
					<td class='tableheader'>" . _("Monthly Pay") . "</td>
					<td class='tableheader'>" . _("Periods") . "</td>
					<td class='tableheader'>" . _("Periods Paid") . "</td>
					<td class='tableheader'>" . _("Start Date") . "</td>
					<td class='tableheader'>" . _("End Date") . "</td>
					<td class='tableheader'>" . _("Status") . "</td></tr>";

					foreach($loans as $loan_single) {
						$date_of_end = date('Y-m-d', strtotime("+".$loan_single[5]." months", strtotime($loan_single[7]))); 
						echo '<tr style="text-align:center"><td>'.$loan_single[0].'</td><td>'.$loan_single[1].'</td><td>'.$loan_single[2].'</td><td>'.$loan_single[3].'</td><td>'.$loan_single[4].'</td><td>'.$loan_single[5].'</td><td>'.$loan_single[6].'</td><td>'.sql2date($loan_single[7]).'</td><td>'.sql2date($date_of_end).'</td><td>'.$loan_single[8].'</td><tr>';
					}
			end_table(1);
		
		div_end();
	end_form();
 
 
end_page();
 
?>