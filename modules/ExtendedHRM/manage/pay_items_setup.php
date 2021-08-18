<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_TYPELEAVE';
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
 
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");

page(_("Payroll Setup "), false, false, "", $js);

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
if(get_post('_description_changed')){
	
		$regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('description')) ==0) {
			
			display_error( _("Accept only Alphabets."));
			
			set_focus('description');
		} 
}
if(get_post('_percentage_changed')){
	    $regex = "/[^ ][ 0-9\.]$/";
		if((preg_match($regex, get_post('percentage')) ==0)) {
			display_error( _("Only Numericals or Non-negative values are allowed."));
			$_POST['percentage'] = 0;
			set_focus('percentage');
		} 
}

if(kv_check_payroll_table_exist()){
	$disabled = true;
}else{
	$disabled = false;
}
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){

	$input_error = 0;

	if (strlen($_POST['description']) == 0) {
		$input_error = 1;
		display_error(_("The Allowance description cannot be empty."));
		set_focus('description');
	}
	if(!empty($_POST['description'])){
	
		$regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('description')) ==0) {
			display_error( _("Accept only Alphabets."));
			set_focus('description');
		} 
	}
	
	if ($_POST['Tax'] == 1 && $_POST['type'] == 'Earnings') {
		$input_error = 1;
		display_error(_("The Tax Can't be an Earnings."));
		set_focus('type');
	}

	/****
         * 
         * changed on [16-11-2018]
         * if ($_POST['basic'] == 1 && $_POST['type'] == 'Deductions') {
		$input_error = 1;
		display_error(_("The Basic Can't be a Deduction."));
		set_focus('type');
	}

	if ($_POST['basic'] == 1 && $_POST['value'] == 'Percentage') {
		$input_error = 1;
		display_error(_("The Basic Can't be a Deduction."));
		set_focus('type');
	}*/

	if($_POST['_percentage']){
	    $regex = "/[^ ][ 0-9\.]$/";
		if((preg_match($regex, $_POST['_percentage']) ==0)) {
			display_error( _("Only Numericals or Non-negative values are allowed."));
			$_POST['percentage'] = 0;
			set_focus('percentage');
		} 
	}

	if ($input_error != 1)	{
    	if ($selected_id != -1)     	{
    		update_allowance($selected_id, $_POST['description'], $_POST['type'], $_POST['value'],$_POST['basic'], $_POST['percentage'], $_POST['Tax'], $_POST['inactive']);
			$note = _('Selected Allowance has been updated');
    	}     	else     	{
    		add_allowance($_POST['description'], $_POST['type'], $_POST['value'],$_POST['basic'], $_POST['percentage'], $_POST['Tax'], $_POST['inactive']);
			$note = _('New Allowance has been added');
    	}
    
		display_notification($note); 
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete'){
	$delete_allow = 0; 
	$result = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_job` LIKE '{$selected_id}'", "Can't Query this Table");
	$exists = (db_num_rows($result))?TRUE:FALSE;
	if($exists) {
	   	$sql = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_job WHERE `{$selected_id}` > 0"; 
	   	$result =  db_query($sql,"could not get kv_departments");
		$row = db_fetch($result);
		if($row[0]> 0){
			$delete_allow = 1; 

		}else{
			$delete_allow = 0; 
		}
	}

	$result = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_salary` LIKE '{$selected_id}'", "Can't Query this Table");
	$exists = (db_num_rows($result))?TRUE:FALSE;
	if($exists) {
	   	$sql = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_salary WHERE `{$selected_id}` > 0"; 
	   	$result =  db_query($sql,"could not get kv_departments");
		$row = db_fetch($result);
		if($row[0]> 0){
			$delete_allow = 1; 

		}else{
			$delete_allow = 0; 
		}
	}

	if($delete_allow== 0){
		$res = delete_allowance($selected_id);
		display_notification(_('Selected  Allowance has been deleted'));
		$Mode = 'RESET';
	}else{
	display_warning(_('Sorry, you cannot Delete this Allowance. Its already used and created with few entires in it.'.$row[0]));
	}
} 

if ($Mode == 'RESET'){
	$selected_id = -1;
	unset($_POST);	
}

if(isset($_POST['UpdateAllowance'])){

	$result = get_allowances();

	while ($myrow = db_fetch($result)) {	
		$rslt = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_salary` LIKE '{$myrow["id"]}'", "Can't Query this Table");
		$exists = (db_num_rows($rslt))?TRUE:FALSE;
		if(!$exists) {
			$sql ="ALTER TABLE `".TB_PREF."kv_empl_salary` ADD `{$myrow["id"]}` int(15) NOT NULL ";
			db_query($sql, "Db Table creation failed, Kv empl_salary table");
		}

		$reslt = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_job` LIKE '{$myrow["id"]}'", "Can't Query this Table");
		$exists = (db_num_rows($reslt))?TRUE:FALSE;
		if(!$exists) {
			$sql ="ALTER TABLE `".TB_PREF."kv_empl_job` ADD `{$myrow["id"]}` int(15) NOT NULL ";
			db_query($sql, "Db Table creation failed, Kv empl_job table");
		}
	}
	display_notification("Your Allowances are updated.");
}
if(isset($_POST['ProcessAllowance'])){
	$sql0 = "CREATE TABLE IF NOT EXISTS `".TB_PREF."kv_empl_job` (
		  `id` int(10) NOT NULL AUTO_INCREMENT,
		  `empl_id` varchar(10) NOT NULL,
		  `grade` tinyint(2) NOT NULL,
		  `department` tinyint(2) NOT NULL,
		  `desig_group` tinyint(2) NOT NULL,
		  `desig` varchar(40) NOT NULL,
		  `joining` date NOT NULL,
		  `empl_type` tinyint(2) NOT NULL,
		  `working_branch` tinyint(2) NOT NULL,
		  `mod_of_pay` int(2) NOT NULL,
		  `bank_name` varchar(40) NOT NULL,
		  `acc_no` varchar(30) NOT NULL,";

  	$sql1 = "CREATE TABLE IF NOT EXISTS `".TB_PREF."kv_empl_salary` (
	  `id` int(20) NOT NULL AUTO_INCREMENT,
	  `empl_id` varchar(10) NOT NULL,
	  `month` int(2) NOT NULL,
	  `year` int(2) NOT NULL,
	  `date` date NOT NULL,
	  `gross` int(15) NOT NULL,
	  `lop_amount` int(10) NOT NULL,
	  `loan` int(10) NOT NULL,
	  `adv_sal` int(10) NOT NULL,
	  `net_pay` int(10) NOT NULL,
	  `misc` int(10) NOT NULL,
	  `ot_other_allowance` int(10) NOT NULL,";

	$result = get_allowances();

	while ($myrow = db_fetch($result)) {	
		$sql0 .="`{$myrow["id"]}` int(15) NOT NULL, ";
		$sql1 .="`{$myrow["id"]}` int(15) NOT NULL, ";
	}

  $sql0 .=" `gross_pay_annum` int(20) NOT NULL,
	  `gross` int(10) NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=latin1; " ;
	
  $sql1 .= " PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=latin1;" ;

	db_query($sql0, "Db Table creation failed, Kv empl_job table");
	db_query($sql1, "Db Table creation failed, Kv kv_empl_salary table");

	display_notification("Your Allowances are set to work ready.");
}

//----------------------------------------------------------------------------------------
$result = get_all_allowances();

start_form();
start_table(TABLESTYLE, "width=50%");
$th = array( _("Allowance Name"), _("Type"), _("Value Type"), _("Is This Basic"), _("Is This Tax"), _("Status"),"","");

table_header($th);

$nos=db_num_rows($result);

if($nos !=0){
	
while ($myrow = db_fetch($result)) {
	
	label_cell($myrow["description"]);
	label_cell($myrow["type"]);
	label_cell(($myrow["value"] == 'Percentage' ? $myrow["value"].'('.$myrow["percentage"].'%)': $myrow["value"]));
	label_cell(($myrow["basic"]== 1 ? 'Yes' :  'No'));
	label_cell(($myrow["Tax"]== 1 ? 'Yes' :  'No'));
	label_cell(($myrow["inactive"]== 1 ? 'Inactive' :  'Active'));

 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
	if($myrow["id"] != '1' && $myrow["id"] != '2' && $myrow["id"] != '3' && $myrow["id"] != '8'){
 	kv_delete_button_cell("Delete".$myrow["id"], _("Delete"), false); //, $disabled);
	submit_js_confirm("Delete".$myrow["id"], sprintf(_("You are about to delete a Pay Items Setup Do you want to continue?"), $myrow['id']));
	}else{
		label_cell('');
	}
	end_row();
  }
}else{
	label_cell('No Records Found','colspan=8 align="center" size="15"');
}
end_table(1);

//----------------------------------------------------------------------------------------
start_table(TABLESTYLE2);

if ($selected_id != -1) {
 	if ($Mode == 'Edit') { //editing an existing department
		$myrow = get_allowance($selected_id);

		$_POST['description']  = $myrow["description"];
		$_POST['type']  = $myrow["type"];
		$_POST['value']  = $myrow["value"];
		$_POST['basic']  = $myrow["basic"];
		$_POST['Tax']  = $myrow["Tax"];
		$_POST['inactive']  = $myrow["inactive"];
		$_POST['percentage']  = $myrow["percentage"];
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
}else {
	$_POST['basic'] = $_POST['Tax'] = $_POST['inactive'] = '0'; 
	$_POST['percentage'] = 0;
} 

kv_text_row_ex(_("Allowance Name:*"), 'description', 30, null,null,null,null,null,true,false); 

if(db_has_basic_pay()){ 
	kv_check_row(_("Is This Basic Pay:"), 'basic', $_POST['basic'], false, false,$disabled);
} else{
	kv_check_row(_("Is This Basic Pay:"), 'basic', $_POST['basic'], false, false);
}
if($Mode == 'Edit')
	earning_deductions_list_row(_("Type:"), 'type', null, "", "", false, $disabled);
else
	earning_deductions_list_row(_("Type:"), "type");
percentage_amount_list_row(_("Amount / Percentage:"), 'value', null, "", "", true);
kv_check_row(_("Is This Tax Field:"), 'Tax', $_POST['Tax'], false, false);

if($disabled){
	if(db_has_tax_pay())
		hidden('Tax', $_POST['Tax']);
	if(db_has_basic_pay()){ 
		hidden('basic', $_POST['basic']);
	}
	if($Mode == 'Edit')
		hidden('type', $_POST['type']);
}
//check_row(_("Is This Basic Pay:"), 'basic', $_POST['basic']);
if((list_updated('value') && get_post('value')=='Percentage')|| $_POST['percentage'] >0 ){
	kv_text_row_ex(_("Percentage from Basic:"), 'percentage', 10, null, null, null, null, '%',true,false); 
}else{
	hidden('percentage', null);
}
kv_check_row(_("Is This Inactive:"), 'inactive', $_POST['inactive'], false, false);
end_table(1);
submit_add_or_update_center($selected_id == -1, '', 'both');
if(!$disabled  ){		
	br();	
	submit_center('ProcessAllowance', "Setup My Payroll System", _('Check entered data and save document'), 'default');
}else{
	br();
	submit_center('UpdateAllowance', "Update My Payroll System", _('Check entered data and save document'), 'default');
}

//submit_js_confirm('CancelOrder', _('You are about to void this Document.\nDo you want to continue?'));

end_form();
end_page(); ?> 
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
}); */


/*$('input[name=percentage]').keypress(function(e) {
  if(!((e.keyCode > 95 && e.keyCode < 106)
      || (e.keyCode > 47 && e.keyCode < 58) 
      || e.keyCode == 8)) {
        return false;
   }
}); */

/* $(document).ready(function(){
    $("body").delegate('input[name=percentage]', 'focusout', function(){
        if($(this).val() < 0){
	alert('Negative Values Are Not Allowed');		
            $(this).val('0');
        }
    });
}); */

</script>