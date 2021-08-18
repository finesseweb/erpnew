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
$page_security = 'BED_CREATE';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
page(_($help_context = "Create Bed")); 

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
	if (strlen($_POST['bed_no']) == 0) 
	{
		display_error(_("Bed no. should not be empty."));
		set_focus('bed_no');
		return false;
	}
        
        if (strlen($_POST['charge']) == 0) 
	{
		display_error(_("Charge should not be empty."));
		set_focus('charge');
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
/*function clear_data() {
    unset($_POST['empl_id']);
    unset($_POST['empl_salutation']);
    unset($_POST['salutation_text']);
    unset($_POST['empl_firstname']);
    unset($_POST['empl_middlename']);
}*/

//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM'  && can_process()) 
{
	//display_error("afdd");die;
	add_transition_master($_POST['roomId'],$_POST['bed_no'],$_POST['fee_type'],$_POST['charge']);
	display_notification(_('New Record  has been added'));
	$Mode = 'RESET';
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
		//display_notification(_('Designation has been deleted'));
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

$result = get_transition_master(check_value('show_inactive'));
	//display_error($result);die;
start_form();
start_table(TABLESTYLE, "width=40%");
$th = array(_("#"),_("Room No."),_("Bed No."),_(" Fee Type"),_("Charge"),'','');
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
	label_cell($myrow["room_no"]);
	label_cell($myrow["bed_no"],"align='center'");
        if($myrow["fee_type"]== 1 )
            $r='Daily';
        else if($myrow["fee_type"]== 2 )
            $r='Monthly';
        else if($myrow["fee_type"]== 3 )
            $r='Yearly';
	label_cell($r,"align='center'");
	label_cell($myrow["charge"],"align='center'");
	inactive_control_cell2($myrow["id"], $myrow["deleted"], 'room_transition', 'id','bed');
 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
	submit_js_confirm("Delete".$myrow["id"], sprintf(_("You are about to Inactive Record?"), $myrow['id']));
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
            
		$myrow = get_transition_id($selected_id);
		$_POST['roomId']  = $myrow["room_id"];
		$_POST['bed_no']  = $myrow["bed_no"];
                $_POST['fee_type']  = $myrow["fee_type"];
                $_POST['charge']  = $myrow["charge"];
               
                
	}
	hidden('selected_id', $selected_id);
} 
echo "<h1 id='master'></h1>";

room_type_list_row(_("Room Type"), 'room_type',null, "", "",TRUE);

yesno_list_row(_("AC Avilable"), 'ac', null, "", "", TRUE);

if(!empty($_POST['room_type']) || !empty($_POST['ac'])){
     room_list_row(_("Room No."), 'roomId', null, false, TRUE,$_POST['room_type'], $_POST['ac']);
 }

room_no_row_ex(_("Bed No. *"), 'bed_no', 30,null,null,null,null,null,FALSE);

fee_type_list_row(_("Charge Based On"), 'fee_type',null, "", "",FALSE);

room_no_row_ex(_("Charge *"), 'charge', 30,null,null,null,null,null,FALSE);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();
?>
 <script>
     
   /*   var r = $("[name=room_type]").val();
      var a = $("[name=ac]").val();
      $("#master").text(r+' '+a);
    
      
     $("[name=ac]").change(function(){
        a = $(this).val();  
       $("#master").text(r+' '+a);
       //alert(r+' '+a);
     });
     
   $("[name=room_type]").change(function(){
        r = $(this).val();
       $("#master").text(r+' '+a);
       // alert(r+' '+a);
   });
   */
     </script>
