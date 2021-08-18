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
$page_security = 'LEAVE_BED';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
page(_($help_context = "Relinquishment of Occupancy")); 

include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

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
		display_error(_("Please Select Name"));
		set_focus('stu_id');
		return false;
	}
         if (strlen($_POST['remark']) == 0) 
	{
		display_error(_("Remark should not be blank"));
		set_focus('remark');
		return false;
	}
        if (strlen($_POST['dues_clear']) == 0) 
	{
		display_error(_("Please Select Checkbox"));
		set_focus('dues_clear');
		return false;
	}
	/* if(!empty($_POST['room_des']))
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
	leave_hostel($_POST['stu_id'],$_POST['remark']);
	display_notification(_('Record has been updated'));
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

end_table();

start_table(TABLESTYLE2);

select_type_list_row(_("Select Type"), 'select_type',null, "", "",TRUE);

if(!empty($_POST['select_type'])){
    studentType_list_row(_("Select Name "), 'stu_id',null,false,true,$_POST['select_type']);
}
 echo"</br>";
end_table();
echo"<p>Student Information</p>";

if(!empty($_POST['stu_id']) || !empty($_POST['select_type'])){
 $result = get_stu_info(check_value('show_inactive'),$_POST['stu_id'],$_POST['select_type']);
start_table(TABLESTYLE, "width=40%");
$th = array(_(" Name( Id )"),_("Gender"),_("Room No."),_("Bed No."),_("Address"),_("Contact Number"));
//inactive_control_column($th);
table_header($th);

$k = 0;
$nos=db_num_rows($result);
if($nos !=0){
$i=1;	
while ($myrow = db_fetch($result)) 
{
	alt_table_row_color($k);
       $room_no=get_room_no($myrow["bed_id"]);
        if (substr($myrow["stu_id"], 0, 3) == 'PDM') {
        label_cell(student_information($myrow["stu_id"])[1]);
        label_cell(student_information($myrow["stu_id"])[2]==1 ? 'Male':'Female',"align='center'");
	label_cell($room_no,"align='center'");
	label_cell($myrow["bed_id"],"align='center'");
        label_cell(student_information($myrow["stu_id"])[3],"align='center'");
        label_cell(student_information($myrow["stu_id"])[4],"align='center'");
        }else if (substr($myrow["stu_id"], 0, 3) == 'EMP' || substr($myrow["stu_id"], 0, 2) == 'VF') {
        label_cell(faculty_staff_information($myrow["stu_id"])[1]);
        label_cell(faculty_staff_information($myrow["stu_id"])[2]==1 ? 'Male':'Female',"align='center'");
	label_cell($room_no,"align='center'");
	label_cell($myrow["bed_id"],"align='center'");
        label_cell(faculty_staff_information($myrow["stu_id"])[3],"align='center'");
        label_cell(faculty_staff_information($myrow["stu_id"])[4],"align='center'");
        }else if ($myrow["stu_id"]!='') {
        label_cell(guest_information($myrow["stu_id"])[0]);
        label_cell(guest_information($myrow["stu_id"])[1]==1 ? 'Male':'Female',"align='center'");
	label_cell($room_no,"align='center'");
	label_cell($myrow["bed_id"],"align='center'");
        label_cell(guest_information($myrow["stu_id"])[2],"align='center'");
        label_cell(guest_information($myrow["stu_id"])[3],"align='center'");
        }
       /* label_cell(student_information($myrow["stu_id"])[2]==1 ? 'Male':'Female',"align='center'");
	label_cell($myrow["room_no"],"align='center'");
	label_cell($myrow["bed_id"],"align='center'");
        label_cell(student_information($myrow["stu_id"])[3],"align='center'");
        label_cell(student_information($myrow["stu_id"])[4],"align='center'");*/
	//submit_js_confirm("Delete".$myrow["id"], sprintf(_("You are about to Inactive Record?"), $myrow['id']));
	end_row();
        $i++;
 }
 
}else{
	label_cell('No Records Found','colspan=6 align="center" size="15"');
}

//inactive_control_row($th);
end_table();

 }
 
echo"</br>";
echo"<table><tr><td>Remark </td><td><textarea rows='3' cols='30' name='remark'></textarea>"
. "</td></tr><tr><td>Cleared All Dues</td>"
        . "<td> <input type='checkbox' name='dues_clear' >"
        
        . "</td></tr> </table>";
submit_add_or_update_center($selected_id == -1, '', 'both',false,'');
end_form();

//------------------------------------------------------------------------------------

end_page();

?>

 <script type="text/javascript"> 
     
     $('input[type="checkbox"][name="dues_clear"]').change(function() {
     if(this.checked) {
        document.getElementById('ADD_ITEM').disabled=false;
     }
     else
     {
         document.getElementById('ADD_ITEM').disabled=true; 
     }
 });

</script>