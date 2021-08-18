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
$page_security = 'SA_MAINTENANCE';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Maintenance")); 

include($path_to_root . "/maintenance/includes/db/maintenance_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
//-----------------------------------------------------------------------------------
function can_process() 
{
	
	if (strlen($_POST['name']) == 0) 
	{
		display_error(_("The Maintenance description cannot be empty."));
		set_focus('name');
		return false;
	}	
	
	return true;
}

//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' && can_process()) 
{

	add_maintenance($_POST['name'], $_POST['description']);
	display_notification(_('New Maintenance has been added'));
	$Mode = 'RESET';
} 

//-----------------------------------------------------------------------------------
if ($Mode=='UPDATE_ITEM' && can_process()) 
{
	display_notification(_('Selected Maintenance has been updated'));
	update_maintenance($selected_id, $_POST['name'], $_POST['description']);
	$Mode = 'RESET';
}

//-----------------------------------------------------------------------------------

function can_delete($selected_id)
{
	if (key_in_foreign_table($selected_id, 'debtors_master', 'credit_status'))
	{
		display_error(_("Cannot delete this Maintenance."));
		return false;
	}
	
	return true;
}


//-----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{

	if (can_delete($selected_id))
	{
		delete_maintenance($selected_id);
		display_notification(_('Selected Maintenance has been deleted'));
	}
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

$result = get_all_maintenance(check_value('show_inactive'));
//display_error('asdf');die;
start_form();
start_table(TABLESTYLE, "width=40%");
$th = array(_("Name"), _("Description"),'','');
inactive_control_column($th);
table_header($th);

$k = 0;
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);	

	label_cell($myrow["name"]);
	label_cell($myrow["description"]);
//	label_cell($disallow_text);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'maintenance', 'id');
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

		$myrow = get_maintenance($selected_id);
		
		$_POST['name']  = $myrow["name"];
		
		$_POST['description']  = $myrow["description"];
	}
	hidden('selected_id', $selected_id);
} 
// utility_list_row(_("Utility:"), 'utility_id', null, false, false);
text_row_ex(_("Name:"), 'name', 25);
textarea_row(_("Description:"), 'description',null, 22,4);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
