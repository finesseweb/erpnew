<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'HR_EMPL_INFO';
if (!@$_GET['popup'])
	$path_to_root = "..";
else	
	$path_to_root = "../../..";

include_once($path_to_root . "/includes/session.inc");
include($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/includes/date_functions.inc");

if (!@$_GET['popup'])
	page(_($help_context = "Employee Job informations"));

//---------------------------------------------------------------------------------------------------

simple_page_mode(true);

if (isset($_GET['selected_id'])){
	$selected_id = $_POST['empl_id'] =$_POST['selected_id'] = $_GET['selected_id'];
}
if($_POST['1'] != ''){
	$new_basic_id = $_POST['1'];
}
if(list_updated($basic_id ) || get_post('RefreshInquiry') || get_post('_2_changed') || list_updated($new_basic_id )) {
	
	$basic_id = kv_get_basic();
	$new_basic_id = $_POST['1'];
	$academic_grade_pay = $_POST['2'];
$EarAllowance = get_allowances('Earnings');
		$DedAllowance = get_allowances('Deductions');
		$basic_id = kv_get_basic();
		  $l  = 121;
		while ($single = db_fetch($EarAllowance)) {	
		$new_single_id = $single['id'];
		 $default_value = round(($new_basic_id+$academic_grade_pay)*($single['percentage'])/100);
		 if( ($single['id'] != $basic_id) && ($single['basic'] != '1') && ($new_single_id != '2')){
				
		
				 if($single['id']== 4){
						//$_POST['4'] = 0;
							$_POST[$l] = $default_value;
							$Ajax->activate($l);
					}
					else if($single['id'] == 8){
								$_POST[$l] = $_POST[$single['id']];
                                 $Ajax->activate($l);								
					}
					else if($single['id'] != 4){ 
								$_POST[$l] = $default_value;
								$Ajax->activate($l);
						
		 			}	
			}		
		
		 $l++; }
		 
		 $prof_tax = kv_get_Tax_allowance();
		$i = 100;
		while ($single = db_fetch($DedAllowance)) {	
		$tot_sal = $new_basic_id+$academic_grade_pay+get_post('3');	
				if($single['value'] == 'Percentage' && $single['percentage']>0){
					$default_value = ($tot_sal)*($single['percentage']/100);
				}else {
					$default_value = null;
				}
		
			//if($single['id'] != $prof_tax){//Commeted to show PF, Date- 14-Dec-2017
			
				$_POST[$i] = round($default_value);
				$Ajax->activate($i);
			      //}		
		$i++; }
	
	$Ajax->activate('price_details');
	
}

if (!@$_GET['popup'])
	start_form();
//------------------------------------------------------------------------------------------------------
if (list_updated('empl_id')) 	$Ajax->activate('price_details');

if (list_updated('empl_id') || isset($_POST['_curr_abrev_update']) || isset($_POST['_sales_type_id_update'])) {
	unset($_POST['price']);
	$Ajax->activate('price_details');
}
if(list_updated('empl_type'))
{
	$Ajax->activate('price_details');
	$Ajax->activate('_page_body');
	//$Ajax->activate('effective_date');

}

function can_process(){ 
	
	
	
	if (strlen($_POST['bank_name']) == 0 && $_POST['mod_of_pay']== 2) {
		
	//	display_error($_POST['bank_name']); 
		
		display_error(_("The employee Bank Name Can't be empty."));
		set_focus('bank_name');
		return false;
	} 
	
	if (strlen($_POST['ifsc_code']) == 0 && $_POST['mod_of_pay']== 2) {
		display_error(_("The employee IFSC Code Can't be empty."));
		set_focus('ifsc_code');
		return false;
	}
	
	
	if (strlen($_POST['bank_name']) == 0 && $_POST['mod_of_pay']== 3) {
		
	//	display_error($_POST['bank_name']); die;
		display_error(_("The employee Bank Name Can't be empty."));
		set_focus('bank_name');
		return false;
	}
	
		if (strlen($_POST['acc_no']) == 0 && $_POST['mod_of_pay']== 2) {
		display_error(_("The employee Account Number Can't be empty."));
		set_focus('acc_no');
		return false;
	} 	
	
	if (strlen($_POST['acc_no']) == 0 && $_POST['mod_of_pay']== 3) {
		display_error(_("The employee Account Number Can't be empty."));
		set_focus('acc_no');
		return false;
	} 
	
	if(!empty($_POST['bank_name'])){
	    $regex = "/[^ ][ A-Za-z\.]$/";
		
		if(preg_match($regex, $_POST['bank_name']) ==0) {
			display_error( _("Accept only Alphabets."));
			set_focus('bank_name');
			return false;
		}	
	}

	if(!empty($_POST['ifsc_code'])){
	    $regex = "/[^ ][ A-Z0-9]$/";
		
		if(preg_match($regex, $_POST['ifsc_code']) ==0) {
			display_error( _("You have entered an invalid ifsc code."));
			set_focus('ifsc_code');
			return false;
		}	
	}

	if(!empty($_POST['acc_no'])){
	    $regex = "/[^ ][ 0-9]$/";
		
		if(preg_match($regex, $_POST['acc_no']) ==0) {
			display_error( _("You have entered an invalid Account Number."));
			set_focus('acc_no');
			return false;
		}	
	}
	
	if(date2sql($_POST['joining']) > date('Y-m-d')){
		display_error(_("Invalid Joining Date for the Employee."));
		set_focus('joining');
		return false;
	}

	return true; 
}
if (($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM' )&& can_process()){	 
		//echo $_POST['empl_id'] ; 

		$jobs_arr =  array(  'eligible_hra' => $_POST['eligible_hra'],
							 'grade' => $_POST['grade'],
							 'department' => $_POST['department'],
							 'desig_group' => $_POST['desig_group'],
							 'desig' => $_POST['desig'] ,
							 'joining' => array($_POST['joining'], 'date'), 
							 'empl_type' =>  $_POST['empl_type'], 
							 'effective_date' => date2sql($_POST['effective_date']),
							 'contract_end_date' => date2sql($_POST['contract_end_date']),
							 'contract_duration' => $_POST['contract_duration'],
							 'working_branch' =>  $_POST['working_place'],
							 'mod_of_pay' => $_POST['mod_of_pay'],
							 'ifsc_code' => $_POST['ifsc_code'],
							 'bank_name' => $_POST['bank_name'],
							 'acc_no' => $_POST['acc_no'],
							 'employee_type' => $_POST['employee_type']);
		$Allowance = get_allowances();
		$gross_Earnings = 0;
		while ($single = db_fetch($Allowance)) {
			if(isset($_POST[$single['id']]))	
				$jobs_arr[$single['id']] = $_POST[$single['id']];
			if($single['type'] == 'Earnings')
				$gross_Earnings += $_POST[$single['id']];
		}

		$jobs_arr['gross'] = $gross_Earnings;
		$jobs_arr['gross_pay_annum'] = $gross_Earnings*12;
                $jobs_arr['1'] = $_POST['1'];
                $jobs_arr['act_holder_name'] = $_POST['act_holder_name'];

	if(!db_employee_has_job($_POST['empl_id'])) { 
		
			$jobs_arr['empl_id'] = $_POST['empl_id'];

			Insert('kv_empl_job', $jobs_arr);
			$kv_empl_id = $_POST['empl_id']; 
			set_focus('selected_id');
			$Ajax->activate('selected_id'); // in case of status change
			display_notification(_("A new Employee Job has been added. "));		
		} 
		else { 
		
			Update('kv_empl_job', array('empl_id', $_POST['empl_id']), $jobs_arr);
			
			$kv_empl_id = $selected_id; 
			set_focus('empl_id'); 
			$Ajax->activate('empl_id'); // in case of status change
			display_notification(_("Employee Job Information has been updated."));
		}
}

if ($Mode == 'RESET'){
	$selected_id = -1;
}

if (@$_GET['popup']){
	hidden('_tabs_sel', get_post('_tabs_sel'));
	hidden('popup', @$_GET['popup']);
}
	
//---------------------------------------------------------------------------------------
//echo $_POST['empl_id'];
$job_details = get_employee_job($_POST['empl_id']);
	
//print_r($job_details."Srserhser");
	$_POST['empl_id'] = $job_details['empl_id'];
	
	if(($_POST['eligible_hra'] == $job_details['eligible_hra']) || ($_POST['eligible_hra'] == '')){ 
	$_POST['eligible_hra'] = $job_details['eligible_hra'];
	}else{
	$_POST['eligible_hra'] = $_POST['eligible_hra'];
	}
	
	//display_error(sql2date($job_details['contract_end_date']));
	if(($_POST['contract_end_date'] == sql2date($job_details['contract_end_date'])) || ($_POST['contract_end_date'] == '')){ 
	$_POST['contract_end_date'] = sql2date($job_details['contract_end_date']);
	}else{
	$_POST['contract_end_date'] = $_POST['contract_end_date'];
	}
	
	if(($_POST['effective_date'] == sql2date($job_details['effective_date'])) || ($_POST['effective_date'] == '')){ 
		$_POST['effective_date'] = sql2date($job_details['effective_date']);
	}else{
		$_POST['effective_date'] = $_POST['effective_date'];
	}
	
	$_POST['grade'] = $job_details['grade'];
	$_POST['department'] = $job_details['department'];
	//$_POST['desig_group'] = $job_details['desig_group'];
	$_POST['desig'] = $job_details['desig'];	
	$_POST['joining'] = sql2date($job_details['joining']);
		
	if(($_POST['desig_group'] == $job_details['desig_group']) || ($_POST['desig_group'] == '')){ 
	$_POST['desig_group'] = $job_details['desig_group'];
	}else{
	$_POST['desig_group'] = $_POST['desig_group'];
	}
	
	if( ($_POST['empl_type'] != '')){
		 
		$Ajax->activate('empl_type');
		$Ajax->activate('price_details');
		
	}else{
		
		$_POST['empl_type'] = $job_details['empl_type'];
		$Ajax->activate('_page_body');
	}
	if(list_updated('selected_id'))
	{
		$_POST['empl_type'] = $job_details['empl_type'];
	}
	if(list_updated('selected_id'))
	{
		$_POST['effective_date'] = $job_details['effective_date'];
	}
	
	//$_POST['shift'] = $job_details['shift'];
	//$_POST['contract_end_date'] = $job_details['contract_end_date'];
	$_POST['contract_duration'] = $job_details['contract_duration'];
	$_POST['working_place'] = $job_details['working_branch']; 
	//$_POST['mod_of_pay'] = $job_details['mod_of_pay'];
	
	if( ($_POST['mod_of_pay'] != '')){
		 
		$Ajax->activate('mod_of_pay');
		$Ajax->activate('price_details');
		
	}else{
		$_POST['mod_of_pay'] = $job_details['mod_of_pay'];
		$Ajax->activate('_page_body');
	}
	if(list_updated('selected_id'))
	{
		$_POST['mod_of_pay'] = $job_details['mod_of_pay'];
	}
	
	$_POST['ifsc_code'] = $job_details['ifsc_code'];
	$_POST['bank_name'] = $job_details['bank_name'];
	$_POST['acc_no'] = $job_details['acc_no'];
	$_POST['employee_type'] = $job_details['employee_type'];
        $_POST['act_holder_name'] = $job_details['act_holder_name'];
        $_POST['1'] = $job_details['1'];
if(!isset($new_basic_id)){
	$Allowance = get_allowances();
	while ($single = db_fetch($Allowance)) {	
	//	 $_POST[$single['id']] = $job_details[$single['id']];
        
      if($single['id'] == 8){
		
	if(($_POST[$single['id']] == $job_details[$single['id']]) || ($_POST[$single['id']] == '')){ 
		$_POST[$single['id']] = $job_details[$single['id']];
	}else{		
		$_POST[$single['id']] = $_POST[$single['id']];
	 }
	}
	 else{  
		 
		$_POST[$single['id']] = $job_details[$single['id']];
	 }  
	
       }	
}

if(list_updated('mode_of_pay')){
//display_error('123');
	$Ajax->activate('price_details');
}

	div_start('price_details');
	br();
	start_outer_table(TABLESTYLE2);
	table_section(1);
	table_section_title(_("Job Details"));
	label_row(_("Employee Id:"),$_POST['empl_id']);	
	//hrm_empl_grade_list( _("Grade :"), 'grade', null);	
	hidden('grade',0);
		$emp_result = get_employee($_POST['empl_id']);
		//$_POST['eligible_hra'] = $emp_result['eligible_hra'];
		
	//hrm_empl_desig_group(_("Desgination Group *:"), 'desig_group', null);
	//text_row(_("Desgination *:"), 'desig', null, 30, 30);
	department_list_row( _("Department :"), 'department', null);
	desiggroup_list_row(_("Designation Group:"), 'desig_group', null, false, true);
	$desig_group = $_POST['desig_group'];
	desig_list_row(_("Designation:"),'desig',null,false,false,$desig_group);
	
	date_row(_("Joining") . ":", 'joining');
	//hrm_empl_status_list(_("Status*:"), 'empl_status', null);
	
	hrm_empl_type_row(_("Employment Type*:"), 'empl_type', null,true);

	if(list_updated('empl_type'))
	{
		//date_row(_("Effective Date ") . ":", 'effective_date');
		$Ajax->activate('effective_date');
	}
	if(($_POST['effective_date'] != '')){ 
		date_row(_("Effective Date ") . ":", 'effective_date');
	}
	
	if(($_POST['empl_type'] ==  '3') || ($_POST['empl_type'] == 4) ){
		
		date_row(_("Contract End Date") . ":", 'contract_end_date',null,null,0,0,0,null,true);
		
		/*	$diff = abs(strtotime($_POST['contract_end_date']) - strtotime($_POST['joining']));

			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			$difference=$years.' year(s) '.$months.' month(s) '.$days.' day(s)'; */
	
			$date11 = date('Y-m-d', strtotime($_POST['contract_end_date']));
                        $date12 = date('Y-m-d', strtotime($_POST['joining']));
			$d1 = new DateTime($date11);
			$d2 = new DateTime($date12);
			$dt_diff = $d2->diff($d1);
			$years = $dt_diff->y;
			$months = $dt_diff->m;
			$days = $dt_diff->d; 
	
			$difference=$years.' year(s) '.$months.' month(s) '.$days.' day(s)';
			text_row(_("Contract Period"), 'contract_duration', $difference,  30, 90);
	}else{
		
	}
	
	kv_empl_type_list_row( _("Employee Type:"), 'employee_type', null);
	//hrm_empl_shift(_("Shift*:"), 'shift', null);
	workcenter_list_row(_("Working Place*:"), 'working_place');
	//check_row(_("PF*:"), 'empl_pf', null);		
	hidden('empl_page', 'job') ; 
	table_section(2);

	table_section_title(_("Pay Details - Earnings"));
        if($_POST['employee_type'] == 3){
	kv_text_row_ex(_("Fixed Consolidated Pay:"), '1', 15, 100, null, $_POST['1'],null,null,FALSE,false);
        }
        else{
	kv_empl_hra_eligible_row(_("Eligible for HRA:"), 'eligible_hra',null, FALSE);

	//text_row(_("Gross Salary *:"), 'gr_salary', null, 30, 30);
	//$Allowance = get_allowances();
	if(!isset($new_basic_id)){
		
	$prof_tax = kv_get_Tax_allowance();
	$EarAllowance = get_allowances('Earnings');
	$DedAllowance = get_allowances('Deductions');
	$basic_id = kv_get_basic();
	//kv_basic_row(get_allowance_name($basic_id), $basic_id, 15, 100, null, true);
	kv_basic_row(_(get_allowance_name($basic_id)), $basic_id, 15, 100,null,true);
	 while ($single = db_fetch($EarAllowance)) {	
		/* if($single['id'] != $basic_id)
			text_row(_($single['description']." ".($single['type'] =='Deductions' ? '(-)': '')." :"), $single['id'], null,  15, 100); */
		//recent 
		
		if($single['id']== 4){
						//$_POST['4'] = 0;
						
				//text_row(_($single['description']." ".($single['type'] =='Deductions' ? '(-)': '')." :"), $single['id'], null,  15, 100);
				kv_text_row_ex(_($single['description']." ".($single['type']=='Deductions' ? '(-)': '')." :"), $single['id'], 15, 100, null, null,null,null,true,false); 
					} else if(($single['id'] == 8) && ($single['id'] != $basic_id)){ 
					kv_text_row_ex(_($single['description']." ".($single['type']=='Deductions' ? '(-)': '')." :"), $single['id'], 15, 100, null, null,null,null,false,false);
					
					}
					else if(($single['id'] != 4) && ($single['id'] != $basic_id)){ 
							//text_row(_($single['description']." ".($single['type'] =='Deductions' ? '(-)': '')." :"), $single['id'], null,  15, 100);
							kv_text_row_ex(_($single['description']." ".($single['type']=='Deductions' ? '(-)': '')." :"), $single['id'], 15, 100, null, null,null,null,true,false); 
							
					}
	}
	table_section_title(_("Deductions"));
	while ($single = db_fetch($DedAllowance)) {	
		//if($single['id'] != $prof_tax)//comented on 14-dec-2017 for showing PF text box in edit mode of employee
			//text_row(_($single['description']." ".($single['type'] =='Deductions' ? '(-)': '')." :"), $single['id'], null,  15, 100);
		kv_text_row_ex(_($single['description']." ".($single['type'] =='Deductions' ? '('.$single['percentage'].'%) (-)': '')." :"), $single['id'],  15, 100, null, null,null,null,FALSE,FALSE);
	}
	}
	
	else if(isset($new_basic_id))
	{
		
		$EarAllowance = get_allowances('Earnings');
		$DedAllowance = get_allowances('Deductions');
		$basic_id = kv_get_basic();
		 
		kv_basic_rows(get_allowance_name($basic_id), $basic_id, 15, 100, $new_basic_id, true);
	
	    $l  = 121;
		while ($single = db_fetch($EarAllowance)) {	
		
		$new_single_id = $single['id'];
		 $default_value = round(($new_basic_id+$academic_grade_pay)*($single['percentage'])/100);
		if($single['id'] == '2'){
		//text_row(_($single['description']." ".($single['type'] =='Deductions' ? '(-)': '')." :"), $single['id'] , $academic_grade_pay,  15, 100);
			
					kv_text_row_ex(_($single['description']." ".($single['type']=='Deductions' ? '(-)': '')." :"), $single['id'] , 15, 100, null, $academic_grade_pay,null,null,true,false); 
		}
			
			if( ($single['id'] != $basic_id) && ($single['basic'] != '1') && ($new_single_id != '2')){
				
		
				 if($single['id']== 4){
						//$_POST['4'] = 0;
						
					kv_text_row_ex(_($single['description']." ".($single['type']=='Deductions' ? '(-)': '')." :"), $l, 15, 100, null, $default_value,null,null,true,false); 
					hidden($single['id'], $default_value);
					}
else if($single['id'] == 8){
						
						kv_text_row_ex(_($single['description']." ".($single['type']=='Deductions' ? '(-)': '')." :"),$l, 15, 100, null, $_POST[$single['id']],null,null,false,false); 
								hidden($single['id'], $_POST[$l]);
						
					} 
					else if($single['id'] != 4){ 
							kv_text_row_ex(_($single['description']." ".($single['type']=='Deductions' ? '(-)': '')." :"),$l, 15, 100, null, $default_value,null,null,true,false); 
							hidden($single['id'], $default_value);
							
					}
								
			}		
		
		 $l++; }
		
		
		table_section_title(_("Deductions"));
		$prof_tax = kv_get_Tax_allowance();
		$i = 100;
		while ($single = db_fetch($DedAllowance)) {	
		$tot_sal = $new_basic_id+$academic_grade_pay+get_post('3');	
				if($single['value'] == 'Percentage' && $single['percentage']>0){
					$default_value = ($tot_sal)*($single['percentage']/100);
				}else {
					$default_value = null;
				}
		
			//if($single['id'] != $prof_tax){//Commented for showing PF box in edit mode, date - 14-dec-2017
			//echo $default_value.'|'.$new_basic_id.'|';
				kv_text_row_ex(_($single['description']." ".($single['type']=='Deductions' ? '(-)': '')." :"), $i, 15, 100, null, round($default_value),null,null,true,TRUE); 
				hidden($single['id'], $default_value);
			     // }		
		$i++; }
		
		
	}
        }
		

	table_section_title(_("Payment Mode"));	
hrm_empl_mop_list(_("Mode of Pay *:"), 'mod_of_pay', null,true);

text_row(_("Bank Name *:"), 'bank_name', null, 30, 30);

if($_POST['mod_of_pay']==2 || $_POST['mod_of_pay'] == 3){
text_row(_("Accout Holder Name *:"), 'act_holder_name', null, null, 200);
text_row(_("IFSC Code *:"), 'ifsc_code', null);
}


	$ifsc_code = $_POST['ifsc_code'];


		text_row(_("Bank Account No *:"), 'acc_no', null, 30, 30);
	
	end_outer_table(1);	
	
	submit_add_or_update_center($selected_id == -1, '', 'both');
	div_end();
if (!@$_GET['popup']){
	end_form();
	end_page(@$_GET['popup']);
}	
?>

<script>

/* $('body').on('change','select[name="empl_type"]',function() {
	$('#contract_end_date').datepicker({
	dateFormat: 'mm/dd/yy',
	prevText: '<i class="fa fa-chevron-left"></i>',
	nextText: '<i class="fa fa-chevron-right"></i>',
	
});
	var emp_type = $('select[name="empl_type"]').val();
	
	
	if(emp_type == 3){
		$('#contract_date').show();
		$('#contract_period').show();
	}
	else{
		$('#contract_date').hide();
		$('#contract_period').hide();
	}
});   */


$('input[name=1]').keypress(function(e){
	var code=e.keyCode || e.which;
	   if(e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57))
	{
		alert("Only Numericals are allowed");
		return false;
	}
});



</script>