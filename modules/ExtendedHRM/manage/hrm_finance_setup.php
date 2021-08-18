<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_ALLOWANCE';
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
include_once($path_to_root . "/gl/includes/db/gl_db_accounts.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");

page(_("HRM Finance Setup "), false, false, "", $js);

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
	
	

	if ($input_error != 1)	{
    	if ($selected_id != -1)     	{
    		update_hrm_finance($selected_id, $_POST['description'], $_POST['type'],$_POST['allowance_credit_gl_code'], $_POST['allowance_debit_gl_code'], $_POST['inactive']);
			$note = _('Selected Allowance has been updated');
    	}     	else     	{
    		add_hrm_finance($_POST['description'], $_POST['type'],$_POST['allowance_credit_gl_code'], $_POST['allowance_debit_gl_code'],$_POST['inactive']);
			$note = _('New Allowance has been added');
    	}
    
		display_notification($note); 
		$Mode = 'RESET';
	}
} 

 

if ($Mode == 'RESET'){
	$selected_id = -1;
	unset($_POST);	
}




//----------------------------------------------------------------------------------------
$result = get_all_hrm_finance_setting();

start_form();
start_table(TABLESTYLE, "width=50%");
$th = array( _("Allowance Name"), _("Type"), _("Debit GL"), _("Credit GL"), _("Status"),"");

table_header($th);

$nos=db_num_rows($result);

if($nos !=0){
	
while ($myrow = db_fetch($result)) {
	
    $credit_gl = get_gl_account($myrow["allowance_credit_gl_code"]);
    $debit_gl = get_gl_account($myrow["allowance_debit_gl_code"]);
	label_cell($myrow["description"]);
	label_cell($myrow["type"]);
        $gl_cr_name = ($myrow["allowance_debit_gl_code"] == 0) ? 'None':$myrow["allowance_debit_gl_code"] .'-'. $debit_gl['account_name'];
$gl_dr_name = ($myrow["allowance_credit_gl_code"] == 0) ? 'None':$myrow["allowance_credit_gl_code"] .'-'. $credit_gl['account_name'];           
	label_cell($gl_cr_name);
        label_cell($gl_dr_name);
        
        label_cell(($myrow["inactive"]== 1 ? 'Inactive' :  'Active'));

 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
	
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
		$myrow = get_hrm_finance_setup($selected_id);

		$_POST['description']  = $myrow["description"];
		$_POST['type']  = $myrow["type"];		
		$_POST['inactive']  = $myrow["inactive"];		
                $_POST['allowance_credit_gl_code']  = $myrow["allowance_credit_gl_code"];
		$_POST['allowance_debit_gl_code']  = $myrow["allowance_debit_gl_code"];
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
}else {
	$_POST['basic'] = $_POST['Tax'] = $_POST['inactive'] = 'yes'; 
	$_POST['percentage'] = 0;
} 

kv_text_row_ex(_("Allowance Name:*"), 'description', 30, null,null,null,null,null,true,false); 


if($Mode == 'Edit')
	earning_deductions_list_row1(_("Type:"), 'type', null, "", "", false, $disabled);
else
	earning_deductions_list_row1(_("Type:"), "type");



if($disabled){
	
	if($Mode == 'Edit')
		hidden('type', $_POST['type']);
}
gl_all_accounts_list_row(_("Debit:"), 'allowance_debit_gl_code', $_POST['allowance_debit_gl_code'],'','', _("None"));
gl_all_accounts_list_row(_("Credit:"), 'allowance_credit_gl_code', $_POST['allowance_credit_gl_code'],'','', _("None"));

kv_check_row(_("Is This Inactive:"), 'inactive', $_POST['inactive'], false, false);
end_table(1);
submit_add_or_update_center($selected_id == -1, '', 'both');


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