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
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

page(_("Taxes"), @$_REQUEST['popup'], false, "", $js);
 
 check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));
 
 check_db_has_salary_account(_("There are no Salary Account defined in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/hrm_settings.php'>Settings</a> to update it."));


simple_page_mode(true);

if (isset($_GET['year'])){
	$_POST['year'] = $_GET['year'];
}

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){

	$input_error = 0;

	if (!is_numeric($_POST['min_sal']) || ($_POST['min_sal']<= 0) ) {
		$input_error = 1;
		display_error(_("The Minimum Salary cannot be empty | Non numeric OR less than zero."));
		set_focus('min_sal');
	}

	if (!is_numeric($_POST['max_sal']) || ($_POST['max_sal']<1 ) || ($_POST['max_sal']< $_POST['min_sal'] ) ) {
		$input_error = 1;
		display_error(_("The Maximum Salary cannot be empty | Non numeric OR less than zero."));
		set_focus('max_sal');
	}

	if ($input_error != 1)	{
    	if ($selected_id != -1)     	{
    		kv_update_tax($selected_id, $_POST['selected_year'], $_POST['min_sal'], $_POST['max_sal'], $_POST['percentage'], $_POST['offset']);
			$note = _('Selected Tax Type has been updated');
    	}     	else     	{
    		kv_add_tax($_POST['selected_year'], $_POST['min_sal'], $_POST['max_sal'], $_POST['percentage'], $_POST['offset']);
			$note = _('New Tax type has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete'){

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	/*if (key_in_foreign_table($selected_id, 'kv_empl_job', 'department'))	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this department because Employees have been created using this department."));
	} */
	if ($cancel_delete == 0) {
		kv_delete_tax($selected_id);
		display_notification(_('Selected  Tax has been deleted'));
	} //end if Delete department
	$Mode = 'RESET';
} 

if ($Mode == 'RESET'){
	$selected_id = -1;
	$sav = get_post('show_inactive');
	$selected_year = $_POST['selected_year'];
	unset($_POST);
	$_POST['year'] = $selected_year;
	if ($sav) $_POST['show_inactive'] = 1;
}


start_form();
	if (db_has_employees()) {
		start_table(TABLESTYLE_NOBORDER);
		start_row();
		kv_fiscalyears_list_cells(_("Fiscal Year:"), 'year', null, true);	
		
		end_row();
		end_table();
		br();
		if (get_post('_show_inactive_update')) {
			$Ajax->activate('year');
			set_focus('year');
		}
	} 
	else {			
		hidden('year');
	}

	$year = get_post('year');

	$result = kv_get_taxes($year);

	start_form();
	start_table(TABLESTYLE, "width=60%");
	$th = array(_("Year"), _("Minimum Wage"), _("Maximum Wage"), _("Percentage(%)"),_("Offset"),"", "");
	//inactive_control_column($th);

	table_header($th);
	$k = 0; 

	while ($myrow = db_fetch($result)) {
		
		alt_table_row_color($k);
			$yearselected = get_fiscalyear($myrow['year']);
		label_cell(sql2date($yearselected['begin']).' - '.sql2date($yearselected['end']));
		label_cell($myrow["min_sal"]);
		label_cell($myrow["max_sal"]);
		label_cell($myrow["percentage"]);
		label_cell($myrow["offset"]);
		//inactive_control_cell($myrow["id"], $myrow["inactive"], 'departments', 'id');
	 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
	 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
		end_row();
	}

	//inactive_control_row($th);
	end_table(1);



	//-------------------------------------------------------------------------------

	start_table(TABLESTYLE2);

	if ($selected_id != -1) {
	 	if ($Mode == 'Edit') {
			//editing an existing department
			$myrow = kv_get_tax($selected_id);

			$_POST['year']  = $myrow["year"];
			$_POST['min_sal']  = $myrow["min_sal"];
			$_POST['max_sal']  = $myrow["max_sal"];
			$_POST['percentage']  = $myrow["percentage"];
			$_POST['offset']  = $myrow["offset"];
		}

		hidden("selected_id", $selected_id);
		
		label_row(_("ID"), $myrow["id"]);
	} 
	hidden("selected_year", $_POST['year']);
	text_row_ex(_("Minimum Salary:"), 'min_sal', 30); 
	text_row_ex(_("Maximum Salary:"), 'max_sal', 30); 
	text_row_ex(_("Percentage:"), 'percentage', 10, 10, '', null, null, "%");
	text_row_ex(_("Offset:"), 'offset', 30); 
	end_table(1);

	submit_add_or_update_center($selected_id == -1, '', 'both');


	//display_notification($selected_id);

	end_form();
end_page();
?>