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
$page_security = 'SA_ITEMCATEGORY';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
$js = "";
//if ($use_popup_windows)
	$js .= get_js_open_window(800, 500);
//if ($use_date_picker)
	$js .= get_js_date_picker();
page(_($help_context = "Item Sub Category"), false, false, "", $js);
//page(_($help_context = "Item Sub Category"));

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/inventory/includes/db/items_sub_category_db.inc");

simple_page_mode(false);
//----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	//initialise no input errors assumed initially before we test
	$input_error = 0;

	if (strlen($_POST['sub_cat_name']) == 0)
	{
		$input_error = 1;
		display_error(_("The sub category name cannot be empty."));
		set_focus('sub_item_name');
	}
        if (!intval($_POST['code']))
	{
		$input_error = 1;
		display_error(_("The Code name cannot be String."));
		set_focus('sub_item_name');
	}
	if (strlen(db_escape($_POST['sub_cat_name']))>(20+2))
	{
		$input_error = 1;
		display_error(_("The sub item name is too long."));
		set_focus('sub_item_name');
	}
	if (strlen($_POST['description']) == 0)
	{
		$input_error = 1;
		display_error(_("The sub category description cannot be empty."));
		set_focus('description');
	}

	if ($input_error !=1) {
    	add_sub_category($selected_id, $_POST['sub_cat_name'], $_POST['description'], $_POST['category_id'],$_POST['slab_id'],$_POST['effective_date'],$_POST['code']);
		if($selected_id != '')
			display_notification(_('Selected sub category been updated'));
		else
			display_notification(_('New sub category has been added'));
		$Mode = 'RESET';
	}
}

//----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'stock_master'

	if (sub_cat_used($selected_id))
	{
		display_error(_("Cannot delete this sub category because items have been created using this sub category."));

	}
	else
	{
		delete_sub_category($selected_id);
		display_notification(_('Selected sub category has been deleted'));
	}
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = '';
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}

//----------------------------------------------------------------------------------

$result = get_all_sub_categories(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width='40%'");
$th = array(_('Category'), _('Sub Category Name'),_('GST Slab'),_('Code'),_('Effactive Date'), _('Description'), "", "");
inactive_control_column($th);

table_header($th);
$k = 0; //row colour counter

while ($myrow = db_fetch($result))
{

	alt_table_row_color($k);
	$category_name = get_category_name($myrow["category_id"]);
        $gst_slab = get_gst_slab($myrow["slab_id"]);
	label_cell($category_name);
	label_cell($myrow["sub_cat_name"]);
        label_cell($gst_slab);
        label_cell($myrow['code']);
        label_cell(date('d-m-Y',strtotime($myrow["effective_date"])));
	label_cell($myrow["description"]);
	$id = htmlentities($myrow["sub_cat_name"]);
       
	inactive_control_cell($id, $myrow["inactive"], 'stock_sub_category', 'sub_cat_name', 'category_id');
	if(($myrow["sub_cat_name"] != 'N/A') && ($myrow['description'] != 'N/A')){
 	edit_button_cell("Edit".$id, _("Edit"));
 	delete_button_cell("Delete".$id, _("Delete"));
	}else{
		label_cell();
		label_cell();
		label_cell();
	}
	end_row();
}

inactive_control_row($th);
end_table(1);

//----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != '') 
{
    //display_error($selected_id);
 	if ($Mode == 'Edit') {
		//editing an existing item category

		$myrow = get_sub_category($selected_id);
		//display_error($myrow["category_id"]);die;
		$_POST['category_id'] = $myrow["category_id"];
		$_POST['sub_cat_name'] = $myrow["sub_cat_name"];
		$_POST['description']  = $myrow["description"];
                $_POST['slab_id']  = $myrow["slab_id"];
                $_POST['code']  = $myrow["code"];
		
	}
	hidden('selected_id', $myrow["sub_cat_name"]);
}
 if ($selected_id != '' && sub_cat_used($selected_id)) {
	label_row(_("Category:"), $_POST['category_id']);
	label_row(_("Sub Category Name:"), $_POST['sub_cat_name']);
    hidden('sub_cat_name', $_POST['sub_cat_name']);
} else 
	stock_categories_sub_list_row(_("Category:"), 'category_id', null, false, false);
        
        text_row(_("Sub Category Name:"), 'sub_cat_name', null, 20, 20);
          text_row(_("Code:"), 'code', null, 20, 20);
        tax_slab_list_row(_("GST Slab:"), 'slab_id', null, false, false);
        date_cells1(_("Effactive date:"), 'effective_date', null, false, false);
        textarea_row(_("Description:"), 'description', null, 34, 5);	
end_table(1);

submit_add_or_update_center($selected_id == '', '', 'both');

end_form();

end_page();

?>
