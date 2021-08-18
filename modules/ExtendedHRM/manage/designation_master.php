<?php
/**********************************************************************
  
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
$page_security = 'HR_DESIGNATIONMASTER';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
page(_($help_context = "Designation Master")); 

include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
if(get_post('_name_changed'))
{
		$regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('name')) ==0) {
			display_error( _("Accept only Alphabets."));
			set_focus('name');
			
		} else{
			// $input_error = 0;
		}
}	
?>
<html lang="en">
 <head>
    <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
    <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
   <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js"?>"></script>
</head>
 
 
 </html>
 <?php
function can_process() 
{
	if (strlen($_POST['name']) == 0) 
	{
		display_error(_("The Designation Name cannot be empty."));
		set_focus('name');
		return false;
	}	
	if(!empty($_POST['name']))
	{
		$regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('name')) ==0) {
			display_error( _("Accept only Alphabets."));
			set_focus('name');
			return false;
		} 	
	}
	
/* 	if (strlen($_POST['description']) == 0) 
	{
		display_error(_("The Designation description cannot be empty."));
		set_focus('description');
		return false;
	}	
	 */
	return true;
}


//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM'  && can_process()) 
{
	//display_error("afdd");die;
	add_designation($_POST['desig_group_id'],$_POST['name'],$_POST['description']);
	display_notification(_('New Designation  has been added'));
	$Mode = 'RESET';
} 

//-----------------------------------------------------------------------------------

if ($Mode=='UPDATE_ITEM' && can_process()) 
{
	//display_error("sdff");die;
	update_designation($selected_id,$_POST['desig_group_id'],$_POST['name'], $_POST['description']);
	$Mode = 'RESET';
	display_notification(_('Designation has been updated'));
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
} */


//-----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{

	//if (can_delete($selected_id))
	//{
		delete_designation($selected_id);
		display_notification(_('Designation has been deleted'));
	//}
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

$result = get_designation(check_value('show_inactive'));
	//display_error($result);die;
start_form();
start_table(TABLESTYLE, "width=40%");
$th = array(_("Designation Group"),_("Designation Name"),_("Description"),'','');
inactive_control_column($th);
table_header($th);

$k = 0;

$nos=db_num_rows($result);
if($nos !=0){
	
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);	

	
	//designation_list_cells(_("Select : "), 'desig_group_id', null, true, true);
	label_cell($myrow["grp_name"]);
	label_cell($myrow["name"]);
	label_cell($myrow["description"]);
	//label_cell($status_details);
	//label_cell($disallow_text);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'designation_master', 'id');
 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
	submit_js_confirm("Delete".$myrow["id"], sprintf(_("You are about to delete a Designation Master Do you want to continue?"), $myrow['id']));
	end_row();
 }
 
}else{
	label_cell('No Records Found','colspan=5 align="center" size="15"');
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

		$myrow = get_designationedit($selected_id);
		$_POST['desig_group_id']  = $myrow["desig_group_id"];
		$_POST['name']  = $myrow["name"];
		$_POST['description']  = $myrow["description"];
		
	
	}
	hidden('selected_id', $selected_id);
} 
desiggroup_list_row(_("Designation Group:"), 'desig_group_id', null, false, false);
//desigroup_list_row( _("Designation :"), 'desigroup_id', null);
//text_row_ex(_("Name:"), 'name', 50);
text_row_ex(_("Designation Name:*"), 'name', 30,null,null,null,null,null,true);
 

text_row_ex(_("Description"), 'description', 30);


end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
<!-- <p style='padding: 3px;text-align:center;'> <a href='javascript:goBack();'>Back</a></p> -->
<script>
/*   
$(document).ready(function(){
	//alert('aaaaa');
	//var desc=$('input[name=description]').val();
	$('input[name=description]').keypress(function (e){
		//alert('dfgdfg');
        var code =e.keyCode || e.which;
        if((code<65 || code>90)&&(code<97 || code>122)&&code!=32&&code!=46)  
        {
          alert("Only alphabets are allowed");
          return false;
        }
    });
});

$(document).ready(function(){
	var desc=$('input[name=name]').val();
	 $('input[name=name]').keypress(function (e){
		var code =e.keyCode || e.which;
        if((code<65 || code>90)&&(code<97 || code>122)&&code!=32&&code!=46)  
        {
          alert("Only alphabets are allowed");
          return false;
        }
    });
}); */
</script>