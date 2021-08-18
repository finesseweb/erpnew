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
page(_("Loan Type Setup"));
 
 
simple_page_mode(true);
//----------------------------------------------------------------------------------------------------

function can_process(){
	if (strlen($_POST['loan_name']) == 0){
		display_error(_("The loans type description cannot be empty."));
		set_focus('s_date');
		return false;
	}

	if (strlen($_POST['loan_name']) == 0){
		display_error(_("Calculation factor must be valid positive number."));
		set_focus('s_date');
		return false;
	}
	return true;
}

//------------------------------------------------------------------------------------
if ($Mode=='ADD_ITEM' && can_process()){
	add_loan_types($_POST['loan_name'], $_POST['interest_rate']);
	display_notification(_('New Loan type has been added'));
	$Mode = 'RESET';
}

//----------------------------------------------------------------------------------------
if ($Mode=='UPDATE_ITEM' && can_process()){

	update_loan_types($selected_id, $_POST['loan_name'], $_POST['interest_rate']);
	display_notification(_('Selected Loan type has been updated'));
	$Mode = 'RESET';
}

//----------------------------------------------------------------------------------------------------

if ($Mode == 'Delete'){	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtor_trans'
	if (key_in_foreign_table($selected_id, 'kv_empl_loan', 'loan_type_id'))	{
		display_error(_("Cannot delete this loan type because employees are currently set up to use this loans type."));
	}else	{
		delete_loan_types($selected_id);
		display_notification(_('Selected Loan type has been deleted'));
	}
	$Mode = 'RESET';
}

if ($Mode == 'RESET'){
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$Ajax->activate('_page_body');
	$_POST['show_inactive'] = $sav;
}
//-------------------------------------------------------------------
$result = get_all_loan_types();

start_form();
	start_table(TABLESTYLE, "width=30%");
		$_POST['interest_rate'] = $_POST['loan_name'] = ''; 
		$th = array (_('Loan Name'), _('Interest Rate %'), '','');
		inactive_control_column($th);
		table_header($th);
		$k = 0;
		$base_loans = get_all_loan_types();

		while ($myrow = db_fetch($result)){
			
			label_cell($myrow["loan_name"]);	
			label_cell($myrow["interest_rate"]);	
			
		 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
		 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
			end_row();
		}
		//inactive_control_row($th);
	end_table();
	br(2); 
	//----------------------------------------------------------------
	start_table(TABLESTYLE2);
		table_section_title(_("Loan Types Entry"));
		if ($selected_id != -1){

		 	if ($Mode == 'Edit') {
				$myrow = get_single_loan_type($selected_id);
		//print_r($myrow);
				$_POST['loan_name']  = $myrow[1];
				$_POST['interest_rate']  = $myrow[2];
				
			}
			hidden('selected_id', $selected_id);
		}

		text_row(_("Loan Name:"), 'loan_name', $_POST['loan_name'], 40, 80);	
		//text_row(_("Interest Rate:"), 'interest_rate', $_POST['interest_rate'], 6, 8);	
		text_row_ex(_("Interest Rate:"), 'interest_rate', 10, 10, '', null, null, "%");
		//check_row(_("Tax included").':', 'tax_included', $_POST['tax_included']);

	end_table(1);

	submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();
 
end_page();
 
?>