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

$page_security = 'SA_UTILITY';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
page(_($help_context = "Utility")); 

include($path_to_root . "/maintenance/includes/db/utility_db.inc");

include($path_to_root . "/includes/ui.inc");

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
   
	add_utility($_POST['type'],$_POST['category_id'],$_POST['sub_cat_id'],$_POST['items_id'],$_POST['name'], $_POST['maintenance_type_id'],$_POST['description'],$_POST['freq_id']);
	display_notification(_('New utility has been added'));
	$Mode = 'RESET';
} 

//-----------------------------------------------------------------------------------
if ($Mode=='UPDATE_ITEM' && can_process()) 
{
	display_notification(_('Selected utility has been updated'));
	update_utility($selected_id,$_POST['type'],$_POST['category_id'],$_POST['sub_cat_id'],$_POST['items_id'], $_POST['name'],$_POST['maintenance_type_id'], $_POST['description'],$_POST['freq_id']);
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
		delete_utility($selected_id);
		display_notification(_('Selected credit status has been deleted'));
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
$result = get_all_utility(check_value('show_inactive'));
start_form();
start_table(TABLESTYLE, "width=40%");
$th = array(_("Type"),_("Name"),_("Maintenance Type"), _("Description"), _("Frequency"),'','');
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
	if ($myrow["type"] ==1)
		{
			label_cell('Utility');
		}
		else{
			label_cell('Process');
		}
	if(!empty($myrow["name"])){
	 label_cell($myrow["name"]);
	}
	else{
	  $items = get_item_name($myrow["items_id"]);
	  label_cell($items[0]);
	 
	}
	if ($myrow["maintenance_type_id"] ==1)
		{
			label_cell('Preventive');
		}
		else{
			label_cell('N/A');
		}
	label_cell($myrow["description"]);
	label_cell($myrow["frequency"]);
	//label_cell($disallow_text);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'utility', 'id');
 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
	end_row();
}

inactive_control_row($th);
end_table();
echo '<br>';

//-----------------------------------------------------------------------------------
if(list_updated('type')){

	$Ajax->activate('utly');
}

div_start('utly');
start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing status code

		$myrow = get_utility($selected_id);
		$_POST['type']  = $myrow["type"];
		$_POST['items_id']  = $myrow["items_id"];
		
		$_POST['name']  = $myrow["name"];
		
		$_POST['maintenance_type_id']  = $myrow["maintenance_type_id"];
		
		$_POST['description']  = $myrow["description"];
		
		$Pop_frequency = explode(',',$myrow["freq_id"]);
		
		$_POST['freq_id']  = $Pop_frequency;
	}
	hidden('selected_id', $selected_id);
} 
utilitytype_list_row(_("Type:"), 'type', null, false, true);

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
typemaintain_list_row(_("Type of Maintenance:"), 'maintenance_type_id', null, false, false);
textarea_row(_("Description:"), 'description',null, 22,4);
freq_list_row(_("Frequency:"), 'freq_id', null, false, false);

//yesno_list_row(_("Dissallow invoicing ?"), 'DisallowInvoices', null); 

end_table(1);
div_end();
submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
