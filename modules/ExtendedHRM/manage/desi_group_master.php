<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'HR_DESIGNATION';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
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
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

page(_("Designation Group"), @$_REQUEST['popup'], false, "", $js);
 
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

if(get_post('_name_changed')){
	    $regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('name')) ==0) {
			display_error( _("Accept Alphabets only."));
			set_focus('name');
			return false;
		}
}
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){
//display_error("gfh");die;
	$input_error = 0;
	if (strlen($_POST['name']) == 0) {
		$input_error = 1;
		display_error(_("The  department name cannot be empty."));
		set_focus('name');
	}
	
	if(!empty($_POST['name']))
	{
		$regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('name')) ==0) {
			display_error( _("Accept alphabets only."));
			set_focus('name');
			return false;
		}		
	}

	if ($input_error != 1)	{
    	if ($selected_id != -1)     	{
    		update_desig_group($selected_id,$_POST['name'], $_POST['description']);
			$note = _('Selected  department has been updated');
    	}     	else     	{
		//	display_error($_POST['name']);die;
    		add_desig_group($_POST['name'],$_POST['description']);
			$note = _('New  department has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete'){

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'kv_empl_job', 'department'))	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this department because Employees have been created using this department."));
	} 
	if ($cancel_delete == 0) {
		delete_desig_group($selected_id);
		display_notification(_('Selected  department has been deleted'));
	} //end if Delete department
	$Mode = 'RESET';
} 

if ($Mode == 'RESET'){
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}
//-------------------------------------------------------------------------------------------------
if(db_has_departments_exist() == false) {
	kv_create_department_db();
}

$result = get_desig_group(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(_("ID"), _("Name"),_("Description"), "", "",);
inactive_control_column($th);

table_header($th);
$k = 0; 

$nos=db_num_rows($result);

if($nos !=0){
	
while ($myrow = db_fetch($result)) {
	
	alt_table_row_color($k);
		
	label_cell($myrow["id"]);
	label_cell($myrow["name"]);
	label_cell($myrow["description"]);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'kv_desig_group', 'id');
 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	submit_js_confirm("Delete".$myrow["id"], sprintf(_("You are about to delete a Designation Group Master Do you want to continue?"), $myrow['id']));
	end_row();
	}
}else{
	label_cell('No Records Found','colspan=5 align="center" size="15"');
}

inactive_control_row($th);
end_table(1);

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) {
 	if ($Mode == 'Edit') {
		//editing an existing department
		$myrow = get_desig_group_edit($selected_id);
		$_POST['name']  = $myrow["name"];
		$_POST['description']  = $myrow["description"];
	}
	hidden("selected_id", $selected_id);
//	label_row(_("ID"), $myrow["id"]);
} 

text_row_ex(_(" Name:*"), 'name', 30,null,null,null,null,null,true); 
text_row_ex(_(" Description:"), 'description', 30); 

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
<!-- <p style='padding: 3px;text-align:center;'> <a href='javascript:goBack();'>Back</a></p> -->

<script>
  
/* $(document).ready(function(){
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