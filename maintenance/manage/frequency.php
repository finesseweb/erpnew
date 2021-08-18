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

page(_($help_context = "Frequency")); 

include($path_to_root . "/maintenance/includes/db/frequency_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
//-----------------------------------------------------------------------------------
function can_process() 
{
	
	if(strlen($_POST['frequency_name']) == "0") 
	{
		display_error(_("The Frequency Name cannot be empty."));
		set_focus('frequency_name');
		return false;
	}	
	return true;
}

//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' && can_process()) 
{
	add_frequency($_POST['frequency_name'], $_POST['frequency_desc']);
	display_notification(_('New Frequency has been added'));
	$Mode = 'RESET';
} 

//-----------------------------------------------------------------------------------
if ($Mode=='UPDATE_ITEM' && can_process()) 
{
	display_notification(_('Selected Frequency has been updated'));
	update_frequency($selected_id, $_POST['frequency_name'], $_POST['frequency_desc']);
	$Mode = 'RESET';
}

//-----------------------------------------------------------------------------------

function can_delete($selected_id)
{
	if (key_in_foreign_table($selected_id, 'frequency_master', 'freq_id'))
	{
		display_error(_("Cannot delete this Frequency."));
		return false;
	}
	
	return true;
}


//-----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{

	if (can_delete($selected_id))
	{
		delete_frequency($selected_id);
		display_notification(_('Selected Frequency has been deleted'));
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

$result = get_all_frequency(check_value('show_inactive'));
//display_error('asdf');die;
start_form();
start_table(TABLESTYLE, "width=35%");
$th = array(_("Name"), _("Description"),'','');
inactive_control_column($th);
table_header($th);

$k = 0;
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);	

/*	if ($myrow["dissallow_invoices"] == 0) 
	{
		$disallow_text = _("Invoice OK");
	} 
	else 
	{
		$disallow_text = "<b>" . _("NO INVOICING") . "</b>";
	} */
	
	label_cell($myrow["frequency_name"]);
	label_cell($myrow["frequency_desc"]);
//	label_cell($disallow_text);
	inactive_control_cell($myrow["freq_id"], $myrow["inactive"], 'frequency_master', 'freq_id');
 	edit_button_cell("Edit".$myrow['freq_id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['freq_id'], _("Delete"));
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

		$myrow = get_frequency($selected_id);

		$_POST['frequency_name']  = $myrow["frequency_name"];
		
		$_POST['frequency_desc']  = $myrow["frequency_desc"];
	}
	hidden('selected_id', $selected_id);
} 

text_row_ex(_("Name:"), 'frequency_name', 25);
textarea_row(_("Description:"), 'frequency_desc',null, 22,4);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
