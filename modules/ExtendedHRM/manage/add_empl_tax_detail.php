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
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");

if (!@$_GET['popup'])
	page(_($help_context = "Employee Job informations"));

//---------------------------------------------------------------------------------------------------

simple_page_mode(true);

if (isset($_GET['selected_id'])){
	$selected_id = $_POST['empl_id'] =$_POST['selected_id'] = $_GET['selected_id'];
}
if (!@$_GET['popup'])
	start_form();
//------------------------------------------------------------------------------------------------------
if (list_updated('empl_id')) 	$Ajax->activate('tax_detail');

if (list_updated('empl_id') || isset($_POST['_curr_abrev_update']) || isset($_POST['_sales_type_id_update'])) {
	unset($_POST['price']);
	$Ajax->activate('tax_detail');
}
if(list_updated('empl_type'))
{
	$Ajax->activate('tax_detail');
	$Ajax->activate('_page_body');
	//$Ajax->activate('effective_date');

}

function can_process(){ 
	
	
	

	return true; 
}
$current_f_year = get_company_pref('f_year');
if (($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM' )&& can_process()){	 
		//echo $_POST['empl_id'] ; 

                $jobs_arr = array();
		$Allowance = get_allowances();
		$gross_Earnings = 0;
		while ($single = db_fetch($Allowance)) {
			if(isset($_POST[$single['id']])){	
				$jobs_arr[$single['id']] = $_POST[$single['id']];
                        }                        
		}
                
                
                /*
                $jobs_arr['pre_net_salary'] = $_POST['pre_net_salary'];
                $jobs_arr['pre_tds'] = $_POST['pre_tds'];
                $jobs_arr['pre_no_of_month'] = $_POST['pre_no_of_month'];
                 * 
                 */
        
                
                $jobs_arr['pre_basic_pay'] = $_POST['pre_basic_pay'];
                $jobs_arr['pre_grade_pay'] = $_POST['pre_grade_pay'];
                $jobs_arr['pre_da'] = $_POST['pre_da'];
                $jobs_arr['pre_hra'] = $_POST['pre_hra'];
                $jobs_arr['pre_conveyance'] = $_POST['pre_conveyance'];
                $jobs_arr['pre_sas'] = $_POST['pre_sas'];
                $jobs_arr['pre_prof_tax'] = $_POST['pre_prof_tax'];
                $jobs_arr['pre_pf'] = $_POST['pre_pf'];
                $jobs_arr['pre_tds'] = $_POST['pre_tds'];
                
                
                $job_details = get_employee_job($_POST['empl_id']);
                if(in_array($job_details['pre_financial_year'], array(0, NULL))){//only update fiscal year while adding time
                    $jobs_arr['pre_financial_year'] = $current_f_year;  
                }
                      
                
                
                

	 
		
			Update('kv_empl_job', array('empl_id', $_POST['empl_id']), $jobs_arr);
			
			set_focus('empl_id'); 
			$Ajax->activate('tax_detail'); // in case of status change
			display_notification(_("Employee Tax Information has been updated."));
		
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
       
         
        
                
       $_POST['pre_basic_pay'] = $job_details['pre_basic_pay'];
                $_POST['pre_grade_pay'] = $job_details['pre_grade_pay'];
                $_POST['pre_da'] = $job_details['pre_da'];
                $_POST['pre_hra'] = $job_details['pre_hra'];
                $_POST['pre_conveyance'] = $job_details['pre_conveyance'];
                $_POST['pre_sas'] = $job_details['pre_sas'];
                $_POST['pre_prof_tax'] = $job_details['pre_prof_tax'];
                $_POST['pre_pf'] = $job_details['pre_pf'];
                $_POST['pre_tds'] = $job_details['pre_tds'];
                $_POST['working_days_joining_month'] = $job_details['working_days_joining_month'];
                
}


if(list_updated('mode_of_pay')){
//display_error('123');
	$Ajax->activate('tax_detail');
}

	div_start('tax_detail');
	br();
	start_outer_table(TABLESTYLE2);
			
	hidden('empl_page', 'tax_detail') ; 
        
        //Showing Basic Payroll value
        /*
        hidden('tax_basic_pay', $job_details[1]);
        hidden('tax_grade_pay', $job_details[2]);
        hidden('tax_da', $job_details[3]);
        hidden('tax_hra', $job_details[4]);
        hidden('tax_conveyance', $job_details[8]);
        hidden('tax_sas', $job_details[6]);
        hidden('tax_prof_tax', $job_details[10]);
        hidden('tax_pf', $job_details[12]);
         * 
         */
        
	table_section(1);
        
        table_section_title(_("Basic Payroll Info(Monthly)"));
        label_row(_("Basic Pay"), $job_details[1]);
        label_row(_("Grade Pay"), $job_details[2]);
        label_row(_("DA"), $job_details[3]);
        label_row(_("HRA"),  $job_details[4]);
        label_row(_("Conveyance"),  $job_details[8]);
        label_row(_("SAS"),  $job_details[6]);
        label_row(_("Prof. Tax"),  $job_details[10]);
        label_row(_("PF"),  $job_details[12]);
        label_row(_("Special Allowance"),$job_details[47]);
        
	table_section_title(_("Tax Detail(Yearly)"));
        //print_r($_POST);

	//text_row(_("Gross Salary *:"), 'gr_salary', null, 30, 30);
	//$Allowance = get_allowances();
	if(!isset($new_basic_id)){
		
	$prof_tax = kv_get_Tax_allowance();
	$EarAllowance = get_allowances('Tax-E');
	$DedAllowance = get_allowances('Tax-D');
	//$basic_id = kv_get_basic();
	//kv_basic_row(get_allowance_name($basic_id), $basic_id, 15, 100, null, true);
	//kv_basic_row(_(get_allowance_name($basic_id)), $basic_id, 15, 100,null,true);
        
	 while ($single = db_fetch($EarAllowance)) {	
		if($single['id'] != 22){
                    kv_text_row_ex(_($single['description']." ".($single['type']=='Deductions' ? '(-)': '')." :"), $single['id'], 15, 100, null, null,null,null,false,false);
                }		
	}
        
	//table_section_title(_("Deductions"));
	while ($single = db_fetch($DedAllowance)) {	
		//if($single['id'] != $prof_tax)//comented on 14-dec-2017 for showing PF text box in edit mode of employee
			//text_row(_($single['description']." ".($single['type'] =='Deductions' ? '(-)': '')." :"), $single['id'], null,  15, 100);
		kv_text_row_ex(_($single['description']." ".($single['type'] =='Deductions' ? '('.$single['percentage'].'%) (-)': '')." :"), $single['id'],  15, 100, null, null,null,null,FALSE,FALSE);
	}
        table_section_title(_("Previous Employment History"));
        kv_text_row_ex('Basic Salary:','pre_basic_pay', 15, 100, null, null,null,null,FALSE,FALSE);        
        kv_text_row_ex('Grade Pay:','pre_grade_pay',  15, 100, null, null,null,null,FALSE,FALSE);
        kv_text_row_ex('DA:','pre_da',  15, 100, null, null,null,null,FALSE,FALSE);
        kv_text_row_ex('HRA:','pre_hra',  15, 100, null, null,null,null,FALSE,FALSE);
        kv_text_row_ex('Conveyance:','pre_conveyance',  15, 100, null, null,null,null,FALSE,FALSE);
        kv_text_row_ex('SAS:','pre_sas',  15, 100, null, null,null,null,FALSE,FALSE);
        kv_text_row_ex('Prof. Tax:','pre_prof_tax',  15, 100, null, null,null,null,FALSE,FALSE);
        kv_text_row_ex('PF:','pre_pf',  15, 100, null, null,null,null,FALSE,FALSE);
        kv_text_row_ex('Paid TDS:','pre_tds',  15, 100, null, null,null,null,FALSE,FALSE);
        
        
	}

$tds_detail = kv_calculate_tds($_POST['empl_id']);
   
    
//echo $salary_fy_rem_month;
	end_outer_table(1);

	submit_add_or_update_center($selected_id == -1, '', 'both');
        br();
        br();br();
        start_outer_table(TABLESTYLE2);
        table_section(1,500);
        
        table_section_title(_("Tax Summary"));        
        label_row(_("Gross Salary"),$tds_detail['gross_salary']);        
        label_row(_("Total Tax Liability"),$tds_detail['total_tax_liability']);
        label_row(_("Total Paid TDS"),$tds_detail['total_paid_tds']);
        label_row(_("Total Pending TDS"),$tds_detail['total_pending_tds']);
        label_row(_("TDS per Month"),$tds_detail['payable_tds_per_month']);
        
        end_outer_table(2);
        ?>

    
    

<?php
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