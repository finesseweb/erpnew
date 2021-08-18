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

$page_security = 'SA_MAINTAIN_DEPT';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
page(_($help_context = "Maintenance Departments")); 

include($path_to_root . "/maintenance/includes/db/maint_dept_db.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
//-----------------------------------------------------------------------------------
function can_process() 
{
	
	if (strlen($_POST['name']) == 0) 
	{
		display_error(_("The Name cannot be empty."));
		set_focus('name');
		return false;
	}	
	 
	return true;
}

//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' && can_process()) 
{
   
	add_maintenance_dept($_POST['name'],$_POST['depart_id'], $_POST['desiggroup_id'],$_POST['designation_id'],$_POST['empl_id']);
	display_notification(_('New Maintenance Department has been added'));
	$Mode = 'RESET';
} 

//-----------------------------------------------------------------------------------
if ($Mode=='UPDATE_ITEM' && can_process()) 
{
	display_notification(_('Selected Maintenance Department has been updated'));
	update_maintenance_dept($selected_id,$_POST['name'],$_POST['depart_id'], $_POST['desiggroup_id'],$_POST['designation_id'],$_POST['empl_id']);
	$Mode = 'RESET';
}

//-----------------------------------------------------------------------------------

/*function can_delete($selected_id)
{
	if (key_in_foreign_table($selected_id, 'maintenance_department', 'credit_status'))
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
		delete_maint_dept($selected_id);
		display_notification(_('Selected Maintenance Department has been deleted'));
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
$result = get_all_maintenance_dept(check_value('show_inactive'));
start_form();
start_table(TABLESTYLE, "width=40%");
$th = array(_("Name"),_("Department"),_("Designation Group"), _("Designation"), _("Alloted to"),'','');
inactive_control_column($th);
table_header($th);

$k = 0;
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);	

	if ($myrow["dissallow_invoices"] == 0) 
	{
		$disallow_text = _("Invoice OK");
	} 
	else 
	{
		$disallow_text = "<b>" . _("NO INVOICING") . "</b>";
	}
	
	label_cell($myrow["name"]);
    label_cell($myrow["department"]);
	label_cell($myrow["grp_name"]);
	label_cell($myrow["desig_name"]);
	label_cell($myrow["employee"]);
	//label_cell($disallow_text);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'maintenance_department', 'id');
 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
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

		$myrow = get_maintain_dept($selected_id);
		
		$_POST['name']  = $myrow["name"];
		
		$_POST['depart_id']  = $myrow["depart_id"];
		
		$_POST['desiggroup_id']  = $myrow["desiggroup_id"];
		
		$_POST['designation_id']  = $myrow["designation_id"];
		
		$_POST['empl_id']  = $myrow["empl_id"];
		
	 }
	    hidden('selected_id', $selected_id);
} 

text_row_ex(_("Name:"), 'name', 25);
departments_list_row(_("Department:"), 'depart_id', null, false, true);
$department=$_POST['depart_id'];
desiggroup_list_row(_("Designation Group:"), 'desiggroup_id', null, false, true);
$desig_group = $_POST['desiggroup_id'];
if(list_updated('desiggroup_id')){
	$Ajax->activate('designation_id');
}
desig_list_row(_("Designation:"), 'designation_id', null,false,true,$desig_group);
if(list_updated('designation_id')){
	$Ajax->activate('empl_id');
}
$designation=$_POST['designation_id'];
employeename_list_cells(_("Alloted to: "), 'empl_id', null,false, true,check_value('show_inactive'),false,$department,$designation,$desig_group);

end_table(1);
submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
