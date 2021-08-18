<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'HR_EMPLOYEE_INQ';
$path_to_root="../../..";

include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/admin/db/attachments_db.inc");	
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");
add_access_extensions();
//if (!@$_GET['popup'])
//{
//	$js = "";
//	if ($use_popup_windows)
//		$js .= get_js_open_window(900, 500);
//	if ($use_date_picker)
//		$js .= get_js_date_picker();
//	page(("Contract Inquiry"), @$_GET['popup'], false, "", $js);
//}

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
page(_("Employees Inquiry"), @$_REQUEST['popup'], false, "", $js); 	

/* check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it")); */

simple_page_mode(true);
//----------------------------------------------------------------------------------------
if (isset($_GET['filterType'])) // catch up external links
	$_POST['filterType'] = $_GET['filterType'];
if (isset($_GET['trans_no']))
	$_POST['trans_no'] = $_GET['trans_no'];

if (isset($_GET['delete_id'])){
	$selected_del_id = $_GET['delete_id'];

	if (key_in_foreign_table($selected_del_id, 'kv_empl_salary', 'empl_id')){
		
		display_error(_("Cannot delete this Employee because Payroll Processed to this employee And it will be  added in the financial Transactions."));
	}else {
		delete_employee($selected_del_id);
		$filename = company_path().'/images/empl/'.empl_img_name($selected_del_id).".jpg";
		if (file_exists($filename))
			unlink($filename);
		display_notification(_("Selected Employee has been deleted."));
		$Ajax->activate('_page_body');	
	}
}


function check_contractperiod($row)
{
	
		$dates=today();
		//$dt=date2sql($dates);
		$diff_mon=date_diff2(sql2date($row['contract_end_date']),$dates,'m');
		
	    if(($row['empl_type'] == 3) && ($diff_mon < 1)){
			
			$contract_data = kv_hrm_get_empl_list(get_post('empl_id'),get_post('empl_name'),get_post('empl_email'), get_post('dept_id', -1),get_post('status', -1));
		
			return $contract_data;
	    }
}

/* function edit_link($row){
		$str = "/modules/ExtendedHRM/manage/employees.php?selected_id=".$row['empl_id'];
  	return $str ? pager_link(_('Edit'), $str, ICON_EDIT) : '';
}

function delete_link($row){
  	$str = "/modules/ExtendedHRM/inquires/employees_inquiry.php?delete_id=".$row['empl_id'];
  	return $str ? pager_link(_('Edit'), $str, ICON_DELETE) : '';
}  */

//----------------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('Search'))
{
	$Ajax->activate('kv_empl_info');
}
//--------------------------------------------------------------------------------------
if (!isset($_POST['filterType']))
	$_POST['filterType'] = -1;

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
date_row(_("From Date") . ":", 'from_date',20,null,'','','',null,true);
date_row(_("To Date") .":", 'to_date',20,null,'','','',null,true);
department_list_row(_("Select a Department: "), 'dept_id', null,	true, true, check_value('show_inactive'));
hrm_empl_contract_row(_("Employment Type :"), 'contract_type', null,true);

submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();

function display_rows(){
	$sql = kv_hrm_get_contractempl_list( get_post('from_date'),get_post('to_date'), get_post('dept_id', -1),get_post('contract_type'));

	$cols = array(
		_("Empl Name") => array('name'=>'empl_name'),
		_("Empl Id") => array('name'=>'empl_id'),
		_("Department") => array('name'=>'description'),
		_("Current Position") => array('name'=>'name'),
	   	_("Present Address") => array('name'=>'addr_line1'),  
		//_("Grade") => array('name'=>'grade'),
		_("Mobile No") => array('name'=>'mobile_phone'),
	    _("Email") => array('name'=>'email'),
	    _("Date of Join") => array('name'=>'tran_date', 'type'=>'date'),
		_("Employee Type") => array('name'=>'employees_type'),
		_("Contract End Date") => array('name'=>'contract_end_date', 'type'=>'date', 'ord'=>'desc')
		//array('')
	);	
	$table =& new_db_pager('kv_empl_info', $sql, $cols);
	$table->width = "90%";
	//$table->set_marker('check_contractperiod', _("List of employees whoose contract period is going to be completed before one month."));
	display_db_pager($table);
}

//----------------------------------------------------------------------------------------
	start_form(true);
if (isset($_GET['delete_id'])){} else{
	// display_warning(_(" Once you delete the Employee, The whole informations can be removed from the Database"));
	}
	display_rows();
	end_form();
end_page();
?>
