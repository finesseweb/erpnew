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

include_once($path_to_root . "/includes/date_functions.inc");

page(_($help_context = "Utility Parameters ")); 

include($path_to_root . "/maintenance/includes/db/utilityparameters_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
//-----------------------------------------------------------------------------------

function can_process() 
{
	
	 if(strlen($_POST['param_title']) == "0") 
	{
		display_error(_("The Parameter Title cannot be empty."));
		set_focus('param_title');
		return false;
	}	  
	
	return true;
}


if(list_updated('type_maintenance_id')){
	$Ajax->activate('parameters_table');
	}

//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' && can_process()) 
{
  //  display_error($_POST['utly_type']);
	$result2 = get_values($_POST['utly_type'],$_POST['utilitys_id'],$_POST['type_maintenance_id'],$_POST['frequency_id']);
	$res = db_fetch_row($result2);
  //display_errror($res[0]);
    if(($res[0] != $_POST['utly_type']) && ($res[1] != $_POST['utilitys_id']) && ($res[2] != $_POST['type_maintenance_id']) && ($res[3] != $_POST['frequency_id'])){
	//display_error('abc');
	add_parameters($_POST['utly_type'],$_POST['utilitys_id'],$_POST['type_maintenance_id'], $_POST['frequency_id'],$_POST['param_title'],$_POST['param_desc']);
	}
	else if(($res[0] == $_POST['utly_type']) && ($res[1] == $_POST['utilitys_id']) && ($res[2] == $_POST['type_maintenance_id']) && ($res[3] == $_POST['frequency_id'])){
	//display_error('32434');
	  add_paramitems($_POST['utilitys_id'],$_POST['type_maintenance_id'], $_POST['frequency_id'],$_POST['param_title'],$_POST['param_desc']);
	}

	display_notification(_('Parameters has been added'));
	$Ajax->activate('parameters_table');
	//$Mode = 'RESET';
} 
//-----------------------------------------------------------------------------------

if ($Mode=='UPDATE_ITEM' && can_process()) 
{
	display_notification(_('Selected Parameters has been updated'));
	
	
	update_paramitems($selected_id,$_POST['param_title'],$_POST['param_desc']);
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
	$_POST[' '] = $sav;
}
//-----------------------------------------------------------------------------------



//display_error('asdf');die;
start_form();

$result = get_all_parameters(check_value('show_inactive'));
start_table(TABLESTYLE, "width=35%");
//$th = array(_("Utility"),_("Maintenance Type"), _("Frequency"),'','');
//inactive_control_column($th);
/* table_header($th);

$k = 0;
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);	
	
	
	label_cell($myrow["u_name"]);
	if ($myrow["type_maintenance_id"] ==1)
	{
		//label_cell('Preventive');
	}
	else{
		label_cell('N/A');
	}
	label_cell($myrow["f_name"]);
	//label_cell($myrow["ut_param1"]);
	//label_cell($myrow["ut_param2"]);
	//label_cell($myrow["ut_param3"]);
	//label_cell($myrow["ut_param4"]);
	//label_cell($myrow["ut_param5"]);
	//label_cell($myrow["ut_param6"]);
	inactive_control_cell($myrow["ut_param_id"], $myrow["inactive"], 'parameters_master', 'ut_param_id');
 	edit_button_cell("Edit".$myrow['ut_param_id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['ut_param_id'], _("Delete"));
	end_row();
 }

inactive_control_row($th); */
end_table();
echo '<br>';
//-----------------------------------------------------------------------------------
div_start('parameters_table');
start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing status code

		$myrow = get_parameters($selected_id);
		//display_error($myrow["param_desc"]);
		
		$_POST['param_title']  = $myrow["param_title"];
		$_POST['param_desc']  = $myrow["param_desc"];
		
	}
	hidden('selected_id', $selected_id);
} 
utilitytype_list_row(_("Type:"), 'utly_type', null, false, true);
$utly_type = $_POST['utly_type'];

if($_POST['type'] == 1){
if(list_updated('type')){
	$Ajax->activate('items_id');
	$Ajax->activate('category_id');
	$Ajax->activate('sub_cat_id');
	$Ajax->activate('name');
}
stock_categories_list_row(_("Category:"), 'category_id', null, false, true);
$category_id = $_POST['category_id'];
sub_category_list_row(_("Sub Category:"), 'sub_cat_id', null, false, true, $category_id);
$sub_cat_name = $_POST['sub_cat_id'];
//display_error($sub_cat_name);
if(list_updated('category_id')) {
	$Ajax->activate('sub_cat_id');
	$Ajax->activate('items_id');
}
utly_items_list(_("Name:"), 'items_id', null, false, true,$category_id,$sub_cat_name);

}
else {
if(list_updated('type')){
	$Ajax->activate('name');
}
text_row_ex(_("Name:"), 'name', 25);
}



utility_list_row(_("Name :"), 'utilitys_id', null, false, true,$utly_type);
if(list_updated('utly_type')){
	$Ajax->activate('utilitys_id');
	$Ajax->activate('type_maintenance_id');
	$Ajax->activate('frequency_id');
	$Ajax->activate('parameters_items');
}

$utility_id = $_POST['utilitys_id'];
maintenancetype_list_row(_("Type of Maintenance:"), 'type_maintenance_id', null, false, true,$utility_id);
if(list_updated('utilitys_id')){
	$Ajax->activate('type_maintenance_id');
	$Ajax->activate('parameters_table');
	$Ajax->activate('parameters_items');
}

freq_multilist_row(_("Frequency:"), 'frequency_id', null, false, true,$utility_id);
if(list_updated('utilitys_id')){
	$Ajax->activate('frequency_id');
	$Ajax->activate('parameters_table');
	$Ajax->activate('parameters_items');
	
}
if(list_updated('frequency_id')){
   $Ajax->activate('parameters_table');
	$Ajax->activate('parameters_items');
}

end_table(1);
div_end();

div_start('parameters_items');
start_table(TABLESTYLE3, "width=35%");
//display_error($_POST['frequency_id']);
$result2 = get_parameter_values($_POST['utly_type'],$_POST['utilitys_id'],$_POST['type_maintenance_id'],$_POST['frequency_id']);

$th = array(_("S.No."),_("Parameter Title"),_("Description"),'','');
inactive_control_column($th);
table_header($th);
$k = 0;
$i=1;
while ($myrow = db_fetch($result2)) 
{
	// display_error($myrow["p_desc"]);
	alt_table_row_color($k);	
	label_cell($i);
	label_cell($myrow["p_name"]);
	label_cell($myrow["p_desc"]);
	
	inactive_control_cell($myrow["items_id"], $myrow["inactive"], 'utility_parameter_items,utility_parameters_master', 'items_id');
 	edit_button_cell("Edit".$myrow['items_id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['items_id'], _("Delete"));
	end_row();
	$i++;
}
hidden('selectitem_id', $selectitem_id);
inactive_control_row($th);

end_table(1);
div_end();



div_start('parameters');
start_table(TABLESTYLE2);

textarea_row(_("Parameter Title:"), 'param_title',null, 22,4);
textarea_row(_("Parameter Description:"), 'param_desc',null, 22,4);

end_table(1);
div_end();

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();
//------------------------------------------------------------------------------------

end_page();

?>

