<?php
/**********************************************************************
  
	Released underhe GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/

$page_security = 'SA_HELPDESK';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/maintenance/includes/db/helpdesk_db.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");

$js = '';
if($version_id['version_id'] == '2.4.1'){
	if ($SysPrefs->use_popup_windows) 
		$js .= get_js_open_window(900, 500);	

	//if (user_use_date_picker()) 
		$js .= get_js_date_picker();
	
}else{
	//if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	//if ($use_date_picker)
		$js .= get_js_date_picker();
}

page(_($help_context = "Help Desk"), @$_REQUEST['popup'], false, "", $js);

simple_page_mode(true);
//-----------------------------------------------------------------------------------
function can_process() 
{
	
/*	if (strlen($_POST['name']) == 0) 
	{
		display_error(_("The credit status description cannot be empty."));
		set_focus('name');
		return false;
	}	
	 */
	return true;
}

//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' && can_process()) 
{
   
	add_helpdesk($_POST['helpdesk_date'],$_POST['category'],$_POST['dept_id'], $_POST['desgroup_id'],$_POST['desig_id'],$_POST['emp_id'],$_POST['stu_name'],$_POST['maintain_dept_id'],$_POST['issues']);
	display_notification(_('New Help Desk Complaint has been added'));
	$Mode = 'RESET';
} 

//-----------------------------------------------------------------------------------
if ($Mode=='UPDATE_ITEM' && can_process()) 
{
	display_notification(_('Selected Help Desk Complaint has been updated'));
	update_helpdesk($selected_id,$_POST['helpdesk_date'],$_POST['category'],$_POST['dept_id'], $_POST['desgroup_id'],$_POST['desig_id'],$_POST['emp_id'],$_POST['stu_name'],$_POST['maintain_dept_id'],$_POST['issues']);
	$Mode = 'RESET';
}

//-----------------------------------------------------------------------------------

/*function can_delete($selected_id)
{
	if (key_in_foreign_table($selected_id, 'debtors_master', 'credit_status'))
	{
		display_error(_("Cannot delete this credit status because customer accounts have been created referring to it."));
		return false;
	}
	
	return true;
}
*/

//-----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{

	//if (can_delete($selected_id))
	//{
		delete_helpdesk($selected_id);
		display_notification(_('Selected Help Desk Complaint has been deleted'));
//	}
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}
//-----------------------------------------------------------------------------------

//$result= get_all_utility(('show_inactive'));
$result = get_all_helpdeskcomplaints(check_value('show_inactive'));
start_form();
start_table(TABLESTYLE, "width=40%");
$th = array(_("Date"),_("Category"),_("Name"), _("Maintenance Department"), _("Issues"),'','');
inactive_control_column($th);
table_header($th);

$k = 0;
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);	
  
    label_cell(dates2sql($myrow["helpdesk_date"]),"nowrap align=right");
	
	if ($myrow["category"] ==1)
	{
		label_cell('Employee');
	}
	else{
		label_cell('Student');
	}
	
	label_cell($myrow["name"]);
	label_cell($myrow["maintain_dept"]);
	label_cell($myrow["issues"]);
	//label_cell($disallow_text);
	inactive_control_cell($myrow["help_id"], $myrow["inactive"], 'maintenance_help_desk', 'help_id');
 	edit_button_cell("Edit".$myrow['help_id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['help_id'], _("Delete"));
	end_row();
}

inactive_control_row($th);
end_table();
echo '<br>';

//-----------------------------------------------------------------------------------
if(list_updated('category')){

	$Ajax->activate('desk');
}

div_start('desk');
start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing status code

		$myrow = get_helpdesk($selected_id);
		
		$_POST['helpdesk_date']  = $myrow["helpdesk_date"];
		
		$_POST['category']  = $myrow["category"];
		
		$_POST['dept_id']  = $myrow["dept_id"];
		
		$_POST['desgroup_id']  = $myrow["desgroup_id"];
		
		$_POST['desig_id']  = $myrow["desig_id"];
		
		$_POST['emp_id']  = $myrow["emp_id"];
		
		$_POST['stu_name']  = $myrow["stu_name"];
		
		$_POST['maintain_dept_id']  = $myrow["maintain_dept_id"];
		
		$_POST['issues']  = $myrow["issues"];
		
		
	}
	    hidden('selected_id', $selected_id);
} 

date_row(_("Date") . ":", 'helpdesk_date',20);
deskcategory_list_row(_("Category:"), 'category', null, false, true);

if($_POST['category'] == 1){

if(list_updated('category')){
	$Ajax->activate('dept_id');
	$Ajax->activate('desgroup_id');
	$Ajax->activate('desig_id');
	$Ajax->activate('emp_id');

}
departments_list_row(_("Department:"), 'dept_id', null, false, true);
desiggroup_list_row(_("Designation Group:"), 'desgroup_id', null, false, true);
$desig_group = $_POST['desgroup_id'];
desig_list_row(_("Designation:"), 'desig_id', null,false,true,$desig_group);
if(list_updated('desgroup_id')){
	$Ajax->activate('desig_id');
}
$designation = $_POST['desig_id'];
$department = $_POST['dept_id'];
//display_error($department);
employeename_list_cells(_("Select an Employee: "), 'emp_id', null,false, true,check_value('show_inactive'),false,$department,$designation,$desig_group);
if(list_updated('desig_id')){
	$Ajax->activate('emp_id');
}

}
else {
if(list_updated('category')){
 
	$Ajax->activate('stu_name');
}
text_row_ex(_("Name:"), 'stu_name', 25);
}

maintaindept_list_row(_("Maintenance Department:"), 'maintain_dept_id', null, false, false);
textarea_row(_("Issues:"), 'issues',null, 22,4);


//yesno_list_row(_("Dissallow invoicing ?"), 'DisallowInvoices', null); 

end_table(1);
div_end();
submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
