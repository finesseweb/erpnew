<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
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

page(_("Work Place"), @$_REQUEST['popup'], false, "", $js);

simple_page_mode(true);
//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	//initialise no input errors assumed initially before we test
	$input_error = 0;

	if (strlen($_POST['name']) == 0) 
	{
		$input_error = 1;
		display_error(_("The work place name cannot be empty."));
		set_focus('name');
	}

	if ($input_error != 1) 
	{
		
    	if ($selected_id != -1) 
    	{
    		update_work_centre($selected_id, $_POST['name'], $_POST['description']);
			display_notification(_('Selected work place has been updated'));
    	} 
    	else 
    	{
    		add_work_centre($_POST['name'], $_POST['description']);
			display_notification(_('New work place has been added'));
    	}
		$Mode = 'RESET';
	}
} 

//-----------------------------------------------------------------------------------

function can_delete($selected_id)
{
	if (key_in_foreign_table($selected_id, 'bom', 'workcentre_added'))
	{
		display_error(_("Cannot delete this work place because BOMs have been created referring to it."));
		return false;
	}

	if (key_in_foreign_table($selected_id, 'wo_requirements', 'workcentre'))	
	{
		display_error(_("Cannot delete this work place because work order requirements have been created referring to it."));
		return false;
	}		
	
	return true;
}


//-----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{

	if (can_delete($selected_id))
	{
		delete_work_centre($selected_id);
		display_notification(_('Selected work place has been deleted'));
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

$result = get_all_work_centres(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width='50%'");
$th = array(_("Name"), _("Description"), "", "");
inactive_control_column($th);
table_header($th);

$k = 0;
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);	

	label_cell($myrow["name"]);
	label_cell($myrow["description"]);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'workcentres', 'id');
 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
	end_row();
}

inactive_control_row($th);
end_table(1);
//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing status code
		$myrow = get_work_centre($selected_id);
		
		$_POST['name']  = $myrow["name"];
		$_POST['description']  = $myrow["description"];
	}
	hidden('selected_id', $selected_id);
} 

text_row_ex(_("Name:"), 'name', 30);
textarea_row(_("Description:"), 'description',null, 27,4);
//text_row_ex(_("Description:"), 'description', 30);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
