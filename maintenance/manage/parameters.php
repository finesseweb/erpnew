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

page(_($help_context = "Parameters Master")); 

include($path_to_root . "/maintenance/includes/db/parameters_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
//-----------------------------------------------------------------------------------

function can_process() 
{
	
	if(strlen($_POST['param1']) == "0") 
	{
		display_error(_("The Parameter1 cannot be empty."));
		set_focus('param1');
		return false;
	}	 
	
	return true;
}

//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' && can_process()) 
{
	add_parameters($_POST['maintenance_id'], $_POST['frequency_id'],$_POST['param1'],$_POST['param2'],$_POST['param3'],$_POST['param4'],$_POST['param5'],$_POST['param6']);
	display_notification(_('Parameters has been added'));
	$Mode = 'RESET';
} 
//-----------------------------------------------------------------------------------

if ($Mode=='UPDATE_ITEM' && can_process()) 
{
	display_notification(_('Selected Parameters has been updated'));
	update_parameters($selected_id, $_POST['maintenance_id'], $_POST['frequency_id'],$_POST['param1'],$_POST['param2'],$_POST['param3'],$_POST['param4'],$_POST['param5'],$_POST['param6']);
	$Mode = 'RESET';
}
//-----------------------------------------------------------------------------------

function can_delete($selected_id)
{
	 if (key_in_foreign_table($selected_id, 'parameters_master', 'param_id'))
	{
		display_error(_("Cannot delete this Parameters."));
		return false;
	} 
	
	return true;
}
//-----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{

	if (can_delete($selected_id))
	{
		delete_parameters($selected_id);
		display_notification(_('Selected Parameters has been deleted'));
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

$result = get_all_parameters(check_value('show_inactive'));

//display_error('asdf');die;
start_form();

start_table(TABLESTYLE, "width=35%");
$th = array(_("Maintenance Name"), _("Frequency"),_("Parameter1"),_("Parameter2"),_("Parameter3"),_("Parameter4"),_("Parameter5"),_("Parameter6"),'','');
inactive_control_column($th);
table_header($th);

$k = 0;
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);	
	
	label_cell($myrow["m_name"]);
	label_cell($myrow["f_name"]);
	label_cell($myrow["param1"]);
	label_cell($myrow["param2"]);
	label_cell($myrow["param3"]);
	label_cell($myrow["param4"]);
	label_cell($myrow["param5"]);
	label_cell($myrow["param6"]);
	inactive_control_cell($myrow["param_id"], $myrow["inactive"], 'parameters_master', 'param_id');
 	edit_button_cell("Edit".$myrow['param_id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['param_id'], _("Delete"));
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

		$myrow = get_parameters($selected_id);
        
		$_POST['maintenance_id']  = $myrow["maintenance_id"];
		$_POST['frequency_id']  = $myrow["frequency_id"];
		$_POST['param1']  = $myrow["param1"];
		$_POST['param2']  = $myrow["param2"];
		$_POST['param3']  = $myrow["param3"];
		$_POST['param4']  = $myrow["param4"];
		$_POST['param5']  = $myrow["param5"];
		$_POST['param6']  = $myrow["param6"];
		
	}
	hidden('selected_id', $selected_id);
} 

// utility_list_row(_("Utility:"), 'utility', null, false, false);
maintenance_list_row(_("Maintenance:"), 'maintenance_id', null, false, false);
frequency_list_row(_("Frequency:"), 'frequency_id', null, false, false);
text_row_ex(_("Parameter1:"), 'param1', 50);
text_row_ex(_("Parameter2:"), 'param2', 50);
text_row_ex(_("Parameter3:"), 'param3', 50);
text_row_ex(_("Parameter4:"), 'param4', 50);
text_row_ex(_("Parameter5:"), 'param5', 50);
text_row_ex(_("Parameter6:"), 'param6', 50);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();
//------------------------------------------------------------------------------------

end_page();

?>
