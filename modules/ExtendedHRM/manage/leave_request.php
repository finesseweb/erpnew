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
$page_security = 'HR_LEAVEFORM';
$path_to_root="../../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");

//page(_($help_context = "Allocation Request")); 
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );


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

page(_($help_context = "Leave Request"), @$_REQUEST['popup'], false, "", $js);

?>
<html lang="en">
 <head>
    <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
    <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
   <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js"?>"></script>
</head>
 
 
 </html>
 <?php

simple_page_mode(true);

function can_process() 
{
	if (strlen($_POST['desig_id']) == 0) 
	{
		display_error(_("Designation cannot be empty."));
		set_focus('desig_id');
		return false;
	}
	
	if (strlen($_POST['employees_id']) == 0) 
	{
		display_error(_("Employee Name cannot be empty."));
		set_focus('employees_id');
		return false;
	}	
	
	//return true;	
	
		
	if (strlen($_POST['no_of_days']) == 0) 
	{
		
		display_error(_("No. of Days cannot be empty."));
		set_focus('no_of_days');
		return false;
	}	
	
	return true;
	
	
}

function can_process1() 
{
	
	/* if (strlen($_POST['no_of_pls']) == 0) 
	{
		display_error(_("No. Of Earned Leaves cannot be empty."));
		set_focus('no_of_pls');
		return false;
	}	
	
	return true; */  
}

//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM'  && can_process()) 
{
	$today_date = date('d-m-Y');
	
	$query = "SELECT empl_id FROM ".TB_PREF."kv_empl_info WHERE id=".db_escape($_POST['employees_id']);
	$result1= db_query($query);
	$results = db_fetch_row($result1);
	$emp_code = $results[0];
				
		$tmpname = $_FILES['upload_file']['tmp_name'];
		$dir =  company_path()."/leave_attachments";
		if (!file_exists($dir))
		{
			mkdir ($dir,0777);
			$index_file = "<?php\nheader(\"Location: ../index.php\");\n?>";
			$fp = fopen($dir."/index.php", "w");
			fwrite($fp, $index_file);
			fclose($fp);
		}

		$filename = $_FILES['upload_file']['name'];
		$filesize = $_FILES['upload_file']['size'];
		$filetype = $_FILES['upload_file']['type'];

		// file name compatible with POSIX
		// protect against directory traversal
			if($filename !="")
				$unique_name = uniqid('');

			//display_error($unique_name);
			//display_error($dir."/".$unique_name);die;
		//save the file
		move_uploaded_file($tmpname, $dir."/".$unique_name);
		
		
	//display_error($dir."/".$unique_name); die;	
	   $_POST['upload_file'] = $filename;
	   $_POST['filesize'] = $filesize;
	   $_POST['filetype'] = $filetype;
	   $_POST['unique_name'] = $unique_name;
	add_allocation($_POST['dept_id'],$_POST['desig_group_id'],$_POST['desig_id'],$_POST['employees_id'],$_POST['type_leave'],$_POST['reason'],$today_date,$_POST['from_date'],$_POST['to_date'],$_POST['no_of_days'],$_POST['upload_file'],$_POST['filesize'],$_POST['filetype'],$_POST['unique_name'],$_POST['second_leave_type'],$_POST['second_reason'],$_POST['second_from_date'],$_POST['second_to_date'],$_POST['second_no_of_days'],$_POST['request_date']);
	display_notification(_('Leave Request  has been added'));
	$Mode = 'RESET';
} 

//-----------------------------------------------------------------------------------

if ($Mode=='UPDATE_ITEM' && can_process() ) 
{
	$today_date = date('d-m-Y');
	$query = "SELECT empl_id FROM ".TB_PREF."kv_empl_info WHERE id=".db_escape($_POST['employees_id']);
	$result1= db_query($query);
	$results = db_fetch_row($result1);
	$emp_code = $results[0];
	
	 $tmpname = $_FILES['upload_file']['tmp_name'];
		$dir =  company_path()."/leave_attachments";
		//display_error($dir);
		if (!file_exists($dir))
		{
			mkdir ($dir,0777);
			$index_file = "<?php\nheader(\"Location: ../index.php\");\n?>";
			$fp = fopen($dir."/index.php", "w");
			fwrite($fp, $index_file);
			fclose($fp);
		}
         
		$filename = $_FILES['upload_file']['name'];
		//display_error($filename);die;
		$filesize = $_FILES['upload_file']['size'];
		$filetype = $_FILES['upload_file']['type'];
		
		$row = get_empl_alloc_single($selected_id);
		
		
		    if ($row['upload_file'] == ""){
				$unique_name = $row['unique_name'];
			}
        	else{	//exit();
			
			$unique_name = uniqid('');
			}
			//display_error($unique_name);die;
			if ($filename && file_exists($dir."/".$unique_name))
				unlink($dir."/".$unique_name);

		
		move_uploaded_file($tmpname, $dir."/".$unique_name);
	    $_POST['upload_file'] = $filename;
		
	   $_POST['filesize'] = $filesize;
	   $_POST['filetype'] = $filetype;
	   $_POST['unique_name'] = $unique_name;

	$today_date = date('d-m-Y');
	update_allocation($selected_id,$_POST['dept_id'],$_POST['desig_group_id'],$_POST['desig_id'],$_POST['employees_id'],$_POST['type_leave'],$_POST['reason'],$today_date,$_POST['from_date'],$_POST['to_date'],$_POST['no_of_days'],$_POST['upload_file'],$_POST['filesize'],$_POST['filetype'],$_POST['unique_name'],$_POST['second_leave_type'],$_POST['second_reason'],$_POST['second_from_date'],$_POST['second_to_date'],$_POST['second_no_of_days'],$_POST['request_date']);
	$Mode = 'RESET';
	display_notification(_('Leave Request has been updated'));
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
		delete_empl_alloc($selected_id);
		display_notification(_('Leave Request has been deleted'));
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

$result = get_allocation(check_value('show_inactive'));
//display_error($result);die;

start_form(true);

start_table(TABLESTYLE);
$th = array(_("#"),("Designation Group"),_("Designation Name"),_("Department"),_("Employee Name"),_("Request Date"),'','',);
inactive_control_column($th);
table_header($th);

function get_trans($prevent_id,$type)
{
	$label = $prevent_id;
	$class ='';
	$id=$prevent_id;
	$icon = '';
	 $viewer = $path_to_root."modules/ExtendedHRM/view/";
	if ($type == 'Prevent')
		$viewer .= "leave_request_view.php?id=".$prevent_id;
	
	return viewer_link($label, $viewer, $class, $id,  $icon);
	
}

$k = 0;

while ($myrow = db_fetch($result)) 
{
	//display_error($myrow['allocate_id']);
	alt_table_row_color($k);	
	label_cell(get_trans($myrow['allocate_id'],'Prevent'));
	label_cell($myrow["grp_name"]);
	label_cell($myrow["desig_name"]);
	label_cell($myrow["department"]);
	label_cell($myrow["employee"]);
	label_cell($myrow["request_date"]);
	//label_cell($myrow["leaves"]);
	//label_cell($myrow["reason"]);
	//label_cell(dates2sql($myrow["from_date"]));
	//label_cell(dates2sql($myrow["to_date"]));
	//label_cell($myrow["no_of_days"]);
	
	//label_cell($disallow_text);
    inactive_control_cell($myrow["allocate_id"],$myrow["inactive"],'kv_allocation_request', 'allocate_id');
 	edit_button_cell("Edit".$myrow['allocate_id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['allocate_id'], _("Delete"));
	end_row();
	
}

inactive_control_row($th);
end_table();
echo '<br>';

//-----------------------------------------------------------------------------------

if(list_updated('type_leave')){
  $Ajax->activate('leave');
}

div_start('leave');

start_table(TABLESTYLE2);
if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing status code
		//display_error($selected_id);die;
		$myrow = get_empl_alloc_single($selected_id);
		$_POST['dept_id']  = $myrow["dept_id"];
		$_POST['desig_group_id']  = $myrow["desig_group_id"];
		$_POST['desig_id']  = $myrow["desig_id"];
		$_POST['employees_id']  = $myrow["employees_id"];
		$_POST['type_leave']  = $myrow["type_leave"];
		$_POST['reason']  = $myrow["reason"];
		$_POST['from_date']  = $myrow["from_date"];
		$_POST['to_date']  = $myrow["to_date"];
		$_POST['no_of_days']  = $myrow["no_of_days"];
		$_POST['upload_file']  = $myrow["upload_file"];
		
	}
	hidden('selected_id', $selected_id);
} 
depts_list_row(_("Department:"), 'dept_id', null, false, true);
$dept_id = $_POST['dept_id'];

designationgroup_list_row(_("Designation Group:"), 'desig_group_id', null, false, true,$dept_id);
$desig_group = $_POST['desig_group_id'];
designt_list_row(_("Designation:"), 'desig_id', null,false,true,$desig_group);
if(list_updated('desig_group_id')){
	$Ajax->activate('desig_id');
	$Ajax->activate('employees_id');
}
if(list_updated('dept_id')){
	$Ajax->activate('desig_group_id');
	$Ajax->activate('desig_id');
	$Ajax->activate('employees_id');
}
$designation = $_POST['desig_id'];
$department = $_POST['dept_id'];
//display_error($department);
employeename_list_cells(_("Select an Employee: "), 'employees_id', null,false, true,check_value('show_inactive'),false,$department,$designation,$desig_group);
if(list_updated('desig_id')){
	$Ajax->activate('employees_id');
}

date_row(_("Request Date") . ":", 'request_date',20);
leavetype_list_row(_("Leave Type:"), 'type_leave', null, false, true);
textarea_row(_("Reason:"), 'reason',null, 22,4);
date_row(_("From Date") . ":", 'from_date',20);
date_row(_("To Date") . ":", 'to_date',20);
text_row_ex(_("No. of Days :"), 'no_of_days', 10);
leavetype_list_row2(_("Leave Type:"),'second_leave_type',null,true,true);
textarea_row(_("Reason:"),'second_reason',null,22,4);
date_row(_("From Date").":",'second_from_date',20);
date_row(_("To Date").":",'second_to_date',20);
text_row_ex(_("No. of Days :"),'second_no_of_days',10);

if(list_updated('to_date'))
{
	display_error($_POST["_last_val"]);
}

/* if($_POST['type_leave'] == 2){

if(list_updated('type_leave')){
  $Ajax->activate('upload_file');
} */
file_row(_("Attached File") . ":", 'upload_file','upload_file');
/* }
else{

} */
 
end_table(1);
div_end();

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------
end_page();

?>			
<script>	

/* $('input[name=to_date]').select(function(){
	
var start_date = Date.parse($('input[name=from_date]').val());

//alert(start_date);
var end_date = Date.parse($('input[name=to_date]').val());

var diff_date =  end_date - start_date;
//alert(diff_date);
var num_years = diff_date/31536000000;
var num_months = (diff_date % 31536000000)/2628000000;
var num_days =  ((diff_date % 31536000000) % 2628000000)/86400000;
//alert(num_days);
var no_days=num_days+1;
//alert(no_days);
//$('#number_of_days').html(no_days);
$('input[name=no_of_days]').val(no_days);
$('input[name=no_of_days]').attr('readonly',true);
}); */



</script>

<script type="text/javascript" >	

$('input[name=second_to_date]').select(function(){
var start_date = Date.parse($('input[name=second_from_date]').val());
var end_date = Date.parse($('input[name=second_to_date]').val());
//alert(end_date);

//var en_date = $('input[name=second_to_date]').val();
//alert(en_date);
//var st_date = $('input[name=from_date]').val();
//alert(st_date);
//var t_date = $('input[name=to_date]').val();
//alert(t_date);


var diff_date =  end_date - start_date;
//alert(diff_date);
var num_years = diff_date/31536000000;
var num_months = (diff_date % 31536000000)/2628000000;
var num_days =  ((diff_date % 31536000000) % 2628000000)/86400000;
//alert(num_days);
var no_days=num_days+1;

//$('#second_no_of_days').html(no_days);
$('input[name=second_no_of_days]').val(no_days);
$('input[name=second_no_of_days]').attr('readonly',true);
});
$(document).ready(function() { 
var start_date = Date.parse($('input[name=second_from_date]').val());
var end_date = Date.parse($('input[name=second_to_date]').val());
var diff_date = end_date - start_date;
var num_years = diff_date/31536000000;
var num_months = (diff_date % 31536000000)/2628000000;
var num_days = ((diff_date % 31536000000) % 2628000000)/864000000;
var no_days = num_days+1;
$('input[name=second_no_of_days]').val(no_days);
$('input[name=second_no_of_days]').attr('readonly',true);

var first_start_date = Date.parse($('input[name=from_date]').val());
var first_end_date = Date.parse($('input[name=to_date]').val());
var first_diff_date = first_end_date - first_start_date;
var first_num_years = first_diff_date/31536000000;
var first_num_months = (diff_date % 31536000000)/2628000000;
var first_num_days = ((diff_date % 31536000000) % 2628000000)/864000000;
var first_no_days = first_num_days+1;
$('input[name=no_of_days]').val(first_no_days);
$('input[name=no_of_days]').attr('readonly',true);
 
});
</script>


<script>
$(document).ready(function(){
	//alert('aaaaaa23');
    $("body").delegate('input[name=from_date]', 'focusout', function(){
        var start_date = Date.parse($('input[name=from_date]').val());
		var end_date = Date.parse($('input[name=to_date]').val());
		if(start_date > end_date){
          alert("Invalid From Date");
          return false;
        }
    });
});
</script>
<!--<script>
	
//$('body').on('click', 'input[name=to_date]', function() {
	$('body').on('click',function(){
	//	  var newDate = $('date("d-m-Y", strtotime(input[name=to_date]))').val();
	 var dateParts = $('input[name=to_date]').val();

	var from_date=$('input[name=from_date]').val();
	var dateParts = from_date.split("/");
	//alert(dateParts);
	var checkindate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
	//alert(checkindate);
	
	var to_date=$('input[name=to_date]').val();
	var datePart = to_date.split("/");
	var check = new Date(datePart[2], datePart[1] - 1, datePart[0]);
	var difference = check - checkindate;
	//alert(difference);
	var days = Math.floor(difference / (1000 * 3600 * 24));
//	var diffWeeks = Math.floor(difference / (1000 * 60 * 60 * 24 * 7));
	//alert(days);
	
$('input[name=no_of_days]').val(days);
//$('#week').html(diffWeeks);
});

	var from_date=$('input[name=from_date]').val();
	var dateParts = from_date.split("/");
	var checkindate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
	
	
	var to_date=$('input[name=to_date]').val();
	var datePart = to_date.split("/");
	var check = new Date(datePart[2], datePart[1] - 1, datePart[0]);
	var difference = check - checkindate;
	var days = Math.floor(difference / (1000 * 3600 * 24));
	//var diffWeeks = Math.floor(difference / (1000 * 60 * 60 * 24 * 7));
	
$('input[name=no_of_days]').val(days);
//$('#week').html(diffWeeks);

</script> -->