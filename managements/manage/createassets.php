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
$page_security = 'SA_STATUTORY_MASTER';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

//page(_($help_context = "statutory")); 

include($path_to_root . "/managements/includes/db/statutory_db.inc");
include($path_to_root . "/managements/includes/db/nameReturn_db.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");




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

page(_($help_context = "Product Master"), @$_REQUEST['popup'], false, "", $js);
simple_page_mode(true);
//-----------------------------------------------------------------------------------
function can_process() 
{
	$regex_digit = "/^\d{1,3}$/";
        $regex_date = "/\d{2}\/\d{2}\/\d{4}/";
	
        
        
        if(strlen($_POST['name'])=="0")
        {
            display_error(_("The 'Product Name' cannot be empty."));
		set_focus('name');
		return false;
        }
        
        if(strlen($_POST['code'])==0){
             display_error(_("The Product Code cannot be empty."));
		set_focus('code');
		return false;
        }
        
   

        
        return true;
}

//-----------------------------------------------------------------------------------
function isDuplicate(){
   $row_count = hasProductMaster($_POST['statutory'], $_POST['return_name'], $_POST['freq_name']); 
   if($row_count>0)
       return false;
   return true;
       
}

if ($Mode=='ADD_ITEM' && can_process()) 
{if(isDuplicate){
    add_product_master($_POST['name'], $_POST['code']);
	display_notification(_('New asset has been added'));
}
else
      display_error('duplicate value cannot be inserted !');
	$Mode = 'RESET';
} 


//-----------------------------------------------------------------------------------
if ($Mode=='UPDATE_ITEM' && can_process()) 
{
	
	update_product_master($selected_id, $_POST['name'], $_POST['code']);
        display_notification(_('Selected asset has been updated'));
	$Mode = 'RESET';
}

//-----------------------------------------------------------------------------------

function can_delete($selected_id)
{
	if (key_in_foreign_table($selected_id, 'statutory_master', 'freq_id'))
	{
		display_error(_("Cannot delete this statutory."));
		return false;
	}
	
	return true;
}


//-----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{

	if (can_delete($selected_id))
	{
		delete_statutory($selected_id);
		display_notification(_('Selected statutory has been deleted'));
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

$result = get_all_product_master(check_value('show_inactive'));
//display_error('asdf');die;
start_form();
start_table(TABLESTYLE, "width=35%");
$th = array(_("Product Name"),  _("Product Code"),_('Edit'));
inactive_control_column_with_edit_only($th);
table_header($th);

$k = 0; 

 //$result = get_all_return(check_value('show_inactive'));
//echo $result["return_name"][0];
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
	
	label_cell($myrow["name"],'',null,'center');
	label_cell($myrow["code"],'',null,'center');
//	label_cell($disallow_text);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'product_master', 'id');
 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	//delete_button_cell("Delete".$myrow['id'], _("Delete"));
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

		$myrow = get_product_master($selected_id);
		$_POST['name']  = $myrow["name"];
		
		$_POST['code']  = $myrow["code"];
                
	}
	hidden('selected_id', $selected_id);
} 
text_row_ex(_("Product Name:"), 'name', 32);
text_row_ex(_("Product Code :"), 'code', 32);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
