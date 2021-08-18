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
$page_security = 'ROOM_ALLOTMENT';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
//page(_($help_context = "Room Allotment")); 

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

$js = '';
if ($version_id['version_id'] == '2.4.1') {
    //if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);

   // if (user_use_date_picker())
        $js .= get_js_date_picker();
}else {
    //if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
   // if ($use_date_picker)
        $js .= get_js_date_picker();
}

page(_($help_context = "Room Allotment"), @$_REQUEST['popup'], false, "", $js);
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
  
    
	
         if (strlen($_POST['stu_id']) == 0) 
	{
		display_error(_("Please Select Type And Name"));
		set_focus('select_type');
		return false;
	}
        if (strtotime($_POST['t_date']) < strtotime($_POST['f_date'])) 
	{
		display_error(_("Please Select Correct Date"));
		set_focus('t_date');
		return true;
	}
        if (strlen($_POST['hostel_r']) == 0) 
	{
		display_error(_("Please Select Room and Bed."));
		set_focus('hostel_r');
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
	add_room_allotment($_POST['stu_id'],$_POST['hostel_r'],$_POST['f_date'],$_POST['t_date']);
	display_notification(_('New Record  has been added'));
	//$Mode = 'RESET';
} 

//-----------------------------------------------------------------------------------

if ($Mode=='UPDATE_ITEM' && can_process()) 
{
	//display_error("sdff");die;
	update_transition_master($selected_id,$_POST['roomId'],$_POST['bed_no'],$_POST['fee_type'],$_POST['charge']);
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
		delete_transition_master($selected_id);
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

start_form();

echo '<br>';

//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
    
 	if ($Mode == 'Edit') {
            
		$myrow = get_transition_id($selected_id);
		$_POST['roomId']  = $myrow["room_id"];
		$_POST['bed_no']  = $myrow["bed_no"];
                $_POST['fee_type']  = $myrow["fee_type"];
                $_POST['charge']  = $myrow["charge"];
                
	}
	hidden('selected_id', $selected_id);
} 
//echo "<h1 id='master'></h1>";

room_type_list_row(_("Room Type"), 'room_type',null, "", "",TRUE);

yesno_list_row(_("AC Avilable"), 'ac', null, "", "", TRUE);

//end_table();

//start_table(TABLESTYLE2);

select_type_list_row(_("Select Type :"), 'select_type',null, "", "",TRUE);

if(!empty($_POST['select_type'])){
    name_list_row(_("Select Name"), 'stu_id',$_POST['select_type'],null, "","",FALSE);
}
 date_row(_("From Date"), 'f_date');
 date_row(_("To Date"), 't_date');
 echo"</br>";

end_table();

echo"<p>Vaccant Room</p>";
if(!empty($_POST['room_type']) || !empty($_POST['ac'])){
    
    $result = get_record(check_value('show_inactive'),$_POST['room_type'], $_POST['ac']);
	
start_table(TABLESTYLE, "width=40%");
$th = array(_("#"),_("Room No."),_("Bed No."),'Select Room');
inactive_control_column($th);
table_header($th);

$k = 0;
$nos=db_num_rows($result);
if($nos !=0){
$i=1;	
while ($myrow = db_fetch($result)) 
{
	$_POST['hostel_r'] = $bed_id;
	alt_table_row_color($k);
        label_cell($i,"align='center'");
        label_cell($myrow["room_no"]);
	label_cell($myrow["bed_no"],"align='center'");
	inactive_control_cell($myrow["id"], 'room_transition', 'id','');
        radio_control_cell($myrow["id"], 'room_transition','id','');
 	//edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	//delete_button_cell("Delete".$myrow['id'], _("Delete"));
	submit_js_confirm("Delete".$myrow["id"], sprintf(_("You are about to Inactive Record?"), $myrow['id']));
	end_row();
        $i++;
 }
 
}else{
	label_cell('No Records Found','colspan=5 align="center" size="15"');
}

//inactive_control_row($th);
end_table();

 }
echo"</br>";
submit_add_or_update_center($selected_id == -1, '', 'both');
end_form();

//------------------------------------------------------------------------------------

end_page();
?>
 <script>
     
      
     /*$("[hostel_r]").change(function(){
        a = $(this).val();  
       $("#master").text(r+' '+a);
       //alert(r+' '+a);
     });*/
     
     </script>
