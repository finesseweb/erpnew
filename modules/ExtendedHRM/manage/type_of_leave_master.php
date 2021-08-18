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
$page_security = 'HR_TYPELEAVE';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
page(_($help_context = "Type of Leave")); 

include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
if(get_post('_leave_type_changed')){
	    $regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('leave_type')) ==0) {
			display_error( _("Accept only Alphabets."));
			set_focus('leave_type');
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
	if (strlen($_POST['leave_type']) == 0) 
	{
		
		display_error(_("Name cannot be empty."));
		set_focus('leave_type');
		return false;
	}	
	
	if(!empty($_POST['leave_type'])){
	    $regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('leave_type')) ==0) {
			display_error( _("Accept Only Alphabets."));
			set_focus('leave_type');
			return false;
		} 
	}
	
	return true;
}
function can_process1() 
{
	
	/* if (strlen($_POST['no_of_pls']) == 0) 
	{
		display_error(_("cannot be empty."));
		set_focus('no_of_pls');
		return false;
	}	*/
	
	return true;
}


//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM'  && can_process() && can_process1()) 
{
	//display_error("afdd");die;
	add_leaves_type($_POST['leave_type'],$_POST['field_name'],$_POST['desciption']);
	display_notification(_('Leave Type has been added'));
	$Mode = 'RESET';
} 

//-----------------------------------------------------------------------------------

if ($Mode=='UPDATE_ITEM' && can_process() && can_process1()) 
{
	//display_error("sdff");die;
	update_leaves_type($selected_id,$_POST['leave_type'],$_POST['field_name'],$_POST['desciption']);
	$Mode = 'RESET';
	display_notification(_('Leave Type has been updated'));
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
	//display_error($selected_id); die;
		delete_leave_type($selected_id);
		display_notification(_('Leave Type has been deleted'));
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

$result = get_leaves_type(check_value('show_inactive'));
//display_error($result);die;
start_form();
start_table(TABLESTYLE);
$th = array(_("Name"),_("Description"),'','',);
inactive_control_column($th);
table_header($th);

$k = 0;

$nos=db_num_rows($result);
if($nos !=0){
	
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);	
	label_cell($myrow["leave_type"]);
	label_cell($myrow["desciption"]);
	
    inactive_control_cell($myrow["type_id"],$myrow["inactive"],'kv_type_leave_master
    ', 'type_id');
 	edit_button_cell("Edit".$myrow['type_id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['type_id'], _("Delete"));
	submit_js_confirm("Delete".$myrow["type_id"], sprintf(_("You are about to delete a Type of Leave Master Do you want to continue?"), $myrow['type_id']));
	end_row();
 }
}else{
	label_cell('No Records Found','colspan=4 align="center" size="15"');
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
		$myrow = get_leave_type_edit($selected_id);
		$_POST['leave_type']  = $myrow["leave_type"];
                $_POST['field_name'] = $myrow['field_name'];
		$_POST['desciption']  = $myrow["desciption"];
	}
	hidden('selected_id', $selected_id);
} 

text_row_ex(_("Name :*"), 'leave_type', 30,null,null,null,null,null,true);

text_row_ex(_("Field Name :"), 'field_name', null, false, true);

textarea_row(_("Description:"), 'desciption',null, 27,4);


end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
<!-- <p style='padding: 3px;text-align:center;'> <a href='javascript:goBack();'>Back</a></p> -->
<script>
/* $(document).ready(function(){
	$('input[name=leave_type]').keypress(function (e){
		var code =e.keyCode || e.which;
        if((code<65 || code>90)&&(code<97 || code>122)&&code!=32&&code!=46)  
        {
          alert("Only alphabets are allowed");
          return false;
        }
    });
});



$(document).ready(function(){
	//alert('aaaaa');
	//var desc=$('input[name=description]').val();
	$('textarea[name=desciption]').keypress(function (e){
		
        var code =e.keyCode || e.which;
        if((code<65 || code>90)&&(code<97 || code>122)&&code!=32&&code!=46)  
        {
          alert("Only alphabets are allowed");
          return false;
        }
    });
}); */
</script>