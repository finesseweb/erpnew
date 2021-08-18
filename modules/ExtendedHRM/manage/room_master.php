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
$page_security = 'ROOM_MASTER';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
page(_($help_context = "Room Master")); 

include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
/*if(get_post('_name_changed'))
{
		$regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('name')) ==0) {
			display_error( _("Accept only Alphabets."));
			set_focus('name');
			
		} else{
			// $input_error = 0;
		}
}*/	
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
	if (strlen($_POST['room_des']) == 0) 
	{
		display_error(_("Room Description can not be empty."));
		set_focus('room_des');
		return false;
	}
        
        if (strlen($_POST['room_no']) == 0) 
	{
		display_error(_("Room No. can not be empty."));
		set_focus('room_no');
		return false;
	}
	/*if(!empty($_POST['room_des']))
	{
		$regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('name')) ==0) {
			display_error( _("Accept only Alphabets."));
			set_focus('name');
			return false;
		} 	
	}
	
	if (strlen($_POST['description']) == 0) 
	{
		display_error(_("The Designation description cannot be empty."));
		set_focus('description');
		return false;
	}	*/
	 
	return true;
}



//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM'  && can_process()) 
{
	//display_error("afdd");die;
	add_Room_master($_POST['room_des'],$_POST['room_no'],$_POST['room_type'],$_POST['ac'],$_POST['status']);
	display_notification(_('New Record  has been added'));
	$Mode = 'RESET';
} 

//-----------------------------------------------------------------------------------

if ($Mode=='UPDATE_ITEM' && can_process()) 
{
	//display_error("sdff");die;
	update_room_Master($selected_id,$_POST['room_des'],$_POST['room_no'],$_POST['room_type'],$_POST['ac'],$_POST['status']);
	$Mode = 'RESET';
	display_notification(_('Record has been updated'));
}


//-----------------------------------------------------------------------------------

 function can_delete($selected_id)
{
	if (key_in_foreign_table($selected_id, 'debtors_master', 'credit_status'))
	{
		display_error(_("Cannot delete this credit status because customer accounts have been created referring to it."));
		return false;
	}
	
	return true;
} 


//-----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{

	//if (can_delete($selected_id))
	//{
		delete_room_Master($selected_id);
		//display_notification(_(' '));
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

$result = get_Room_master(check_value('show_inactive'));
	//display_error($result);die;
start_form();
start_table(TABLESTYLE, "width=40%");
$th = array(_("#"),_("Room Description"),_("Room No."),_("Room Type"),_("Ac Avilable"),'','');
inactive_control_column($th);
table_header($th);

$k = 0;

$nos=db_num_rows($result);
if($nos !=0){
$i=1;	
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
        label_cell($i,"align='center'");
	label_cell($myrow["room_des"]);
	label_cell($myrow["room_no"],"align='center'");
         if($myrow["room_type"]== 1 )
            $r='Boys';
        else if($myrow["room_type"]== 2 )
            $r='Faculty';
        else if($myrow["room_type"]== 3 )
            $r='Girls';
        else if($myrow["room_type"]== 4 )
            $r='Guest';
        else if($myrow["room_type"]== 5 )
            $r='Staff';
	label_cell($r,"align='center'");
	label_cell($myrow["ac_avil"]== 1 ? 'Yes' :  'No',"align='center'");
	//inactive_control_cell($myrow["room_id"], $myrow["inactive"], 'room_master', 'room_id');
        inactive_control_cell2($myrow["room_id"], $myrow["deleted"], 'room_master', 'room_id','bed');
 	edit_button_cell("Edit".$myrow['room_id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['room_id'], _("Delete"));
	submit_js_confirm("Delete".$myrow["room_id"], sprintf(_("You are about to Inactive Record?"), $myrow['room_id']));
	end_row();
        $i++;
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
            
		$myrow = get_masterRoom_id($selected_id);
                
		$_POST['room_des']  = $myrow["room_des"];
		$_POST['room_no']  = $myrow["room_no"];
                $_POST['room_type']  = $myrow["room_type"];
                $_POST['ac']  = $myrow["ac_avil"];
                $_POST['status']  = $myrow["status"];
	}
	hidden('selected_id', $selected_id);
} 
room_des_row_ex(_("Room Description *"), 'room_des', 30,null,null,null,null,null,FALSE);

room_no_row_ex(_("Room No. *"), 'room_no', 30,null,null,null,null,null,FALSE);

room_type_list_row(_("Room Type"), 'room_type',null, "", "",FALSE);

yesno_list_row(_("AC Avilable"), 'ac', null, "", "", FALSE);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
