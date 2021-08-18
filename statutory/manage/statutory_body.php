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
$page_security = 'SA_STATUTORY_BODY';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Statutory Body Master")); 

include($path_to_root . "/statutory/includes/db/statutory_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
//-----------------------------------------------------------------------------------

//echo "<pre>";print_r($_SESSION);
function can_process() 
{
    $special_character = '/[!@#$%^&*(),?":{}|<>]/';
	
	if(strlen($_POST['statutory_name']) == "0") 
	{
		display_error(_("The statutory Name cannot be empty."));
		set_focus('statutory_name');
		return false;
	}
         if(preg_match($special_character, get_post('statutory_name'))!=0){
            display_error(_("Special Character are not allowed."));
		set_focus('statutory_name');
		return false;
        }
	return true;
}
function isDuplicate(){
  $row_count = hasStatutorybody(ltrim($_POST['statutory_name']));
   if($row_count>0)
       return false;
   return true;
       
}
//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' && can_process()) 
{
    if(isDuplicate()){
	add_statutory(rtrim(ltrim($_POST['statutory_name'])), $_POST['statutory_desc']);
	display_notification(_('New statutory has been added'));
    }
    else
         display_error('Duplicate value cannot be inserted');
	$Mode = 'RESET';
} 

//-----------------------------------------------------------------------------------
if ($Mode=='UPDATE_ITEM' && can_process()) 
{
	display_notification(_('Selected statutory has been updated'));
	update_statutory($selected_id, rtrim(ltrim($_POST['statutory_name'])), $_POST['statutory_desc']);
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

$result = get_all_statutory(check_value('show_inactive'));
//display_error('asdf');die;
start_form();
start_table(TABLESTYLE, "width=35%");
$th = array(_("Name"), _("Description"),'Edit');
inactive_control_column($th);
table_header($th);

$k = 0;
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);	

	label_cell($myrow["statutory_name"],'',null,'center');
	label_cell($myrow["statutory_desc"],'',null,'center');
//	label_cell($disallow_text);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'statutory_body_master', 'id');
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

		$myrow = get_statutory($selected_id);

		$_POST['statutory_name']  = $myrow["statutory_name"];
		
		$_POST['statutory_desc']  = $myrow["statutory_desc"];
	}
	hidden('selected_id', $selected_id);
} 

text_row_ex(_("Name:"), 'statutory_name', 25);
textarea_row(_("Description:"), 'statutory_desc',null, 22,4);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
