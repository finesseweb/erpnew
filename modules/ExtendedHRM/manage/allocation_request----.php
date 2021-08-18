-<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'SA_OPEN';
$path_to_root="../../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/admin/db/attachments_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
//include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_alloc_db.inc");
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

page(_($help_context = "Allocation Request"), @$_REQUEST['popup'], false, "", $js);

simple_page_mode(true);

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));

//----------------------------------------------------------------------------------------

function can_process() 
{
	if (strlen($_POST['no_of_days']) == 0) 
	{
		
		display_error(_("No. Of Days cannot be empty."));
		set_focus('no_of_days');
		return false;
	}	
	
	return true;
}
function can_process1() 
{
	
	/* if (strlen($_POST['no_of_pls']) == 0) 
	{
		display_error(_("No. Of Earned Leaves cannot be empty."));
		set_focus('no_of_pls');
		return false;
	}	 */
	
	return true;
}


//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM'  && can_process() && can_process1()) 
{
	display_error($_POST['status']);
	if($_POST['status']=='Approved'){
	  $status='Approved';
	}
	else if($_POST['status']=='Rejected'){
	   $status='Rejected';
	}
	else{
	   $status='Waiting';
	}
	add_allocation($_POST['dept_id'],$_POST['desig_group_id'],$_POST['desig_id'],$_POST['employees_id'],$_POST['type_leave'],$_POST['reason'],$_POST['leave_from'],$_POST['no_of_days'],$_POST['comments'],$status);
	display_notification(_('Allocation Request  has been added'));
	$Mode = 'RESET';
} 

//-----------------------------------------------------------------------------------
if ($Mode=='UPDATE_ITEM' && can_process() && can_process1()) 
{

	//display_error("sdff");die;
	if($_POST['status']=='Approved'){
	  $status='Approved';
	}
	else if($_POST['status']=='Rejected'){
	   $status='Rejected';
	}
	else{
	   $status='Waiting';
	}
	update_allocation($selected_id,$_POST['dept_id'],$_POST['desig_group_id'],$_POST['desig_id'],$_POST['employees_id'],$_POST['type_leave'],$_POST['reason'],$_POST['leave_from'],$_POST['no_of_days'],$_POST['comments'],$status);
	$Mode = 'RESET';
	display_notification(_('Allocation Request has been updated'));
}


//-----------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{

	//if (can_delete($selected_id))
	//{
		delete_empl_alloc($selected_id);
		display_notification(_('Allocation Request has been deleted'));
	//}
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}

/* if(list_updated('rejected')){

	$Ajax->activate('_page_body');
} */
//-----------------------------------------------------------------------------------

$result = get_allocation(check_value('show_inactive'));
//display_error($result);die;

start_form();

start_table(TABLESTYLE);
$th = array(_("Designation Group"),_("Designation Name"),_("Department"),_("Employee Name"),_("Type of Leave"),_("Reason"),_("Leave From Date"),_("No. of Days"),_("Comments"),'','',);
inactive_control_column($th);
table_header($th);

$k = 0;
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);	
	label_cell($myrow["department"]);
	label_cell($myrow["grp_name"]);
	label_cell($myrow["desig_name"]);
	label_cell($myrow["employee"]);
	label_cell($myrow["leaves"]);
	label_cell($myrow["reason"]);
	label_cell($myrow["leave_from"]);
	label_cell($myrow["no_of_days"]);
	label_cell($myrow["comments"]);
	//label_cell($status_details);
	//label_cell($disallow_text);
    inactive_control_cell($myrow["allocate_id"],$myrow["inactive"],'kv_allocation_request', 'allocate_id');
 	edit_button_cell("Edit".$myrow['allocate_id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['allocate_id'], _("Delete"));
	end_row();
}

inactive_control_row($th);
end_table();
echo '<br>';

//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);
if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing status code
		//display_error($selected_id);die;
		$myrow = get_empl_alloc_single($selected_id);
		display_error($myrow);
		$_POST['dept_id']  = $myrow["dept_id"];
		$_POST['desig_group_id']  = $myrow["desig_group_id"];
		$_POST['desig_id']  = $myrow["desig_id"];
		$_POST['employees_id']  = $myrow["employees_id"];
		$_POST['type_leave']  = $myrow["type_leave"];
		$_POST['reason']  = $myrow["reason"];
		$_POST['leave_from']  = $myrow["leave_from"];
		$_POST['no_of_days']  = $myrow["no_of_days"];
		$_POST['comments']  = $myrow["comments"];
		$_POST['status']  = $myrow["status"];
	}
	hidden('selected_id', $selected_id);
} 
departments_list_row(_("Department:"), 'dept_id', null, false, true);
desiggroup_list_row(_("Designation Group:"), 'desig_group_id', null, false, true);
$desig_group = $_POST['desig_group_id'];
desig_list_row(_("Designation:"), 'desig_id', null,false,true,$desig_group);
if(list_updated('desig_group_id')){
display_error('list');
	$Ajax->activate('desig_id');
}
$designation = $_POST['desig_id'];
$department = $_POST['dept_id'];
//display_error($department);
employeename_list_cells(_("Select an Employee: "), 'employees_id', null,	false, true,check_value('show_inactive'),false,$department,$designation,$desig_group);
if(list_updated('desig_id')){
display_error('dsfsdf');
	$Ajax->activate('employees_id');
}
leavetype_list_row(_("Leave Type:"), 'type_leave', null, false, false);
textarea_row(_("Reason:"), 'reason',null, 22,4);
date_row(_("Leave From Date") . ":", 'leave_from',20);
text_row_ex(_("No. of Days :"), 'no_of_days', 10);
if($_POST['status']='Rejected'){

	$Ajax->activate('allocation_tbl');
}else{
	// hidden('allocation_tbl');
}

submit_cells('status', _("Approved"),"style='padding-left:20px;'",_('Select Approved'), 'ICON_SUBMIT');
submit_cells('status', _("Rejected"),"style='padding-left:10px;'",_('Select Rejected'), 'ICON_SUBMIT');

submit_cells('status', _("Waiting"),"style='padding-left:10px;'",_('Select Waiting'), 'ICON_SUBMIT');
end_table(1);

div_start('allocation_tbl');
start_table(TABLESTYLE2);
textarea_row(_("Comments:"), 'comments',null, 22,4);
end_table(2);
div_end();  

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>