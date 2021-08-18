<?php
/****************************************
/*  Author  : Kvvaradha
/*  Module  : Extended HRM
/*  E-mail  : admin@kvcodes.com
/*  Version : 1.0
/*  Http    : www.kvcodes.com
 * 
*****************************************/

// Necessary File hook

include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_db.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_bonus_db.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_sal_db.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/employee.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/common.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/empl_degree.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/empl_ui_array.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/empl_experience.inc");
//include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/empl_prof_certification.php");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/empl_training.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/empl_leave.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_sal_structure_db.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_cv_db.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_alloc_db.inc");
//include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/designation.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/taxes.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
//include_once($path_to_root . "/modules/ExtendedHRM/includes/db/work_centers_db.inc");
//---------------------------------------------------------------------------------------

// Add, Edit, Delete, Get And Get Join .
 
$Calender_year = array('2017','2018','2019','2020','2021','2022','2023','2024','2025','2026','2027','2028','2029','2030');
//---------------------------------------------------------------------------------------
function Insert($table_name, $data){    
    $sql0 = "INSERT INTO ".TB_PREF.$table_name."(";
    $sql1 = " VALUES (";
    foreach($data as $key=>$value){
        $sql0 .= "`".$key."`,";
		if(is_array($value)) { 
			if($value[1] == 'date')				
				$sql1 .=  db_escape(date2sql($value[0])).",";
			if($value[1] == 'float')
				$sql1 .= $value.",";
		}else 
			$sql1 .= db_escape($value).",";
    }
    $sql0 = substr($sql0, 0, -1).")";
    $sql1 = substr($sql1, 0, -1).")";
    
//display_notification($sql0.$sql1);exit;
    
db_query($sql0.$sql1, "Could not insert data to table {$table_name}");
	return  db_insert_id();
}





function Update($table_name, $primary_key ,$data ){
    $sql0 = "UPDATE ".TB_PREF.$table_name." SET ";
    foreach($data as $key=>$value){
		if(is_array($value)) { 
			if($value[1] == 'date')				
				$sql0 .= "`".$key."` = ". db_escape(date2sql($value[0])).",";
			if($value[1] == 'float')
				$sql0 .= "`".$key."` = ". $value.",";
		}else 
			$sql0 .= "`".$key."` = ".db_escape($value).",";
    }
    $sql0 = substr($sql0, 0, -1);
    $sql0 .= " where ".$primary_key[0]." = '".$primary_key[1]."'";

	return  db_query($sql0, "Could not update data on table {$table_name}");	 
}
function Delete($table_name, $conditions){
    $sql0 = "DELETE FROM ".TB_PREF.$table_name." WHERE 1=1";
    foreach ($conditions as $key=>$value) {
        $sql0 .= " AND ".$key."=".$value;
    }
    $result = db_query($sql0, "Could not delete data from {$table_name}");
    return $result;
}
function GetDataJoin($main_table, $joins, $columns=array(), $conditions=null){
    $sql0 = "SELECT ";
    if(count($columns)>0){
        foreach ($columns as $value) {
            $sql0 .= $value.",";
        }
        $sql0 = substr($sql0, 0, -1);
    }else{
        $sql0 .= " *";
    }
    $sql0 .= " FROM ".TB_PREF."{$main_table} ";
    foreach ($joins as $value) {
        if(isset($value['join'])){
            $sql0 .= " {$value['join']} JOIN ".TB_PREF.$value['table_name'].' ON '.$value['conditions'];
        }else{
            $sql0 .= " INNER JOIN ".TB_PREF.$value['table_name'].' ON '.$value['conditions'];
        }
    }
    $sql0 .= " WHERE 1=1 ";
    if($conditions != null) {
        foreach($conditions as $key=>$value){
            $sql0 .= " AND {$key} = ${value}";
        }
    }
    //echo $sql0;
    $result = db_query($sql0, "Could not get data!");
    $data = array();
    while($row = db_fetch_row($result)) {
        $data[] = $row;
    }
    return $data;
}
function GetAll($table_name, $conditions = null){
    $sql0 = "SELECT * FROM ".TB_PREF.$table_name." WHERE 1=1";
    if($conditions != null) {
		foreach($conditions as $key=>$value){
			$sql0 .= " AND {$key} = '${value}'";
		}
    }
    
   // display_error($sql0);
    $result = db_query($sql0, "Could not get data from {$table_name}");
    $data = array();
    while($row = db_fetch($result)) {
        $data[] = $row;
    }
    return $data;
}

function GetAllEsi($table_name, $conditions = null){
    $sql0 = "SELECT * FROM ".TB_PREF.$table_name." WHERE eligible_esi = 1";
    if($conditions != null) {
		foreach($conditions as $key=>$value){
			$sql0 .= " AND {$key} = '${value}'";
		}
    }
    
   // display_error($sql0);
    $result = db_query($sql0, "Could not get data from {$table_name}");
    $data = array();
    while($row = db_fetch($result)) {
        $data[] = $row;
    }
    return $data;
}

function GetRow($table_name, $conditions = null){
   
    $sql0 = "SELECT * FROM ".TB_PREF.$table_name." WHERE 1=1";
    if($conditions != null) {
		foreach($conditions as $key=>$value){
			$sql0 .= " AND {$key} = '${value}'";
		}
    }
	$sql0 .= ' LIMIT 1'; 
    
    $result = db_query($sql0, "Could not get data from {$table_name}");
	$data = db_fetch($result);
    
    return $data;
}

function GetDataFilter($table_name, $columns= array(), $conditions=null){
    $sql0 = "SELECT ";
    if(count($columns)>0){
        foreach ($columns as $value) {
            $sql0 .= $value.",";
        }
    }else{
        $sql0 .= " * ";
    }
    // remove the last character ','
    $sql0 = substr($sql0, 0, -1);
    $sql0 .= " FROM ".TB_PREF.$table_name." WHERE 1=1";
    foreach($conditions as $key=>$value){
        $sql0 .= " AND {$key} = ${value}";
    }
    $result = db_query($sql0, "Could not get data from {$table_name}");
    $data = array();
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function GetSingleValue($tablename, $column_single, $conditions=null){
	$sql0 = "SELECT ".$column_single." FROM ".TB_PREF.$tablename." WHERE 1=1";
    foreach($conditions as $key=>$value){
        $sql0 .= " AND {$key} = '${value}'";
    }
    $result = db_query($sql0, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

//----------------------------------------------------------------------------------------
function kv_hrm_get_empl_list($empl_id,$empl_name,$empl_email,$dept_id,$status){

	//display_error($status); die;
	
    $sql ="SELECT empl_info.empl_firstname,"."job.empl_id,  "."dpt.description,"."desg.name,"."empl_info.addr_line1,"."empl_info.mobile_phone, " ."empl_info.email,". " job.joining,CASE  WHEN(job.empl_type = '1')  THEN  'Temporary' WHEN (job.empl_type = '2') THEN 'Permanant' WHEN (job.empl_type ='3') THEN 'Short Term Contract' WHEN (job.empl_type ='4') THEN 'Long Term Contract' WHEN (job.empl_type ='5') THEN 'Visiting Faculty' END AS employees_type ,". " job.contract_end_date,". " job.empl_type "."FROM ".TB_PREF."kv_empl_info empl_info JOIN ".TB_PREF."kv_empl_job job ON  job.empl_id = empl_info.empl_id JOIN ".TB_PREF."kv_departments dpt ON job.department= dpt.id JOIN ".TB_PREF."designation_master desg ON job.desig= desg.id WHERE 1=1";
	
	if ($empl_id) {
		$sql .= " AND empl_info.empl_id LIKE ". db_escape("%$empl_id%");
	}
	if ($empl_name) {
		$sql .= " AND empl_info.empl_firstname LIKE ". db_escape("%$empl_name%");
	}
	if ($empl_email) {
		$sql .= " AND empl_info.email LIKE ". db_escape("%$empl_email%");
	}
	if ($dept_id != -1) {
		$sql .= " AND dpt.id=".db_escape($dept_id);
	}
	if ($status) {
		$sql .= " AND empl_info.status=".db_escape($status);
	}
	//display_error($sql); die;
	return $sql;
}


//----------------------------------------------------------------------------------------
function kv_get_employees_list_based_on_dept($dept_id, $rep=false) {

    if($dept_id == 0){
        //get_all_employees();
        $sql = "SELECT empl_id, empl_firstname FROM ".TB_PREF."kv_empl_info";
  
        return db_query($sql, "The employee table is inaccessible");

    } else{
	if($rep == true){
		$sql="SELECT info.*, job.* FROM ".TB_PREF."kv_empl_job job JOIN ".TB_PREF."kv_empl_info info ON info.empl_id = job.empl_id WHERE job.department=".db_escape($dept_id) AND info.status !=5;
	}else{	
		$sql="SELECT info.empl_firstname, job.empl_id FROM ".TB_PREF."kv_empl_job job JOIN ".TB_PREF."kv_empl_info info ON info.empl_id = job.empl_id WHERE  job.department=".db_escape($dept_id)  ;
		//	$display_error($sql); 
        }
	
        return db_query($sql, "could not get the selected Employees");
    }
}

//-------------RAJESH-----------------

function kv_get_employees_list_based_on_dept1($dept_id, $rep=false) {

    if($dept_id == 0){
        //get_all_employees();
        $sql = "SELECT empl_id, empl_firstname FROM ".TB_PREF."kv_empl_info";
  
        return db_query($sql, "The employee table is inaccessible");

    } else{
	if($rep == true){
		$sql="SELECT info.*, job.* FROM ".TB_PREF."kv_empl_job job JOIN ".TB_PREF."kv_empl_info info ON info.empl_id = job.empl_id WHERE job.department=".db_escape($dept_id) ;
	}else{	
		$sql="SELECT info.empl_firstname, job.empl_id FROM ".TB_PREF."kv_empl_job job JOIN ".TB_PREF."kv_empl_info info ON info.empl_id = job.empl_id WHERE  job.department=".db_escape($dept_id);
        }
        return db_query($sql, "could not get the selected Employees");
    }
}
function kv_get_employees_list_based_on_dept2($dept_id, $rep=false, $empl_id='') {

    if($dept_id == 0){
        //get_all_employees();
        $sql = "SELECT empl_id, empl_firstname FROM ".TB_PREF."kv_empl_info where empl_id=".db_escape($empl_id);
  
        return db_query($sql, "The employee table is inaccessible");

    } else{
	if($rep == true){
		$sql="SELECT info.*, job.* FROM ".TB_PREF."kv_empl_job job JOIN ".TB_PREF."kv_empl_info info ON info.empl_id = job.empl_id WHERE job.department=".db_escape($dept_id)."job.empl_id=".db_escape($empl_id) ;
	}else{	
		$sql="SELECT info.empl_firstname, job.empl_id FROM ".TB_PREF."kv_empl_job job JOIN ".TB_PREF."kv_empl_info info ON info.empl_id = job.empl_id WHERE  job.department=".db_escape($dept_id)."job.empl_id=".db_escape($empl_id) ;;
        }
        return db_query($sql, "could not get the selected Employees");
    }
}

//----------------------------------------------------------------------------------------
function kv_get_employees_list_based_on_dept_rep($dept_id) {

    if($dept_id == 0){
        //get_all_employees();
        $sql = "SELECT empl_id, empl_firstname FROM ".TB_PREF."kv_empl_info";
  
        return db_query($sql, "The employee table is inaccessible");

    } else{	
	$sql="SELECT info.*, job.* FROM ".TB_PREF."kv_empl_job job JOIN ".TB_PREF."kv_empl_info info ON info.empl_id = job.empl_id WHERE job.department=".db_escape($dept_id);

        return db_query($sql, "could not get the selected Employees");
    }
}


function kv_get_empl_joining_fisical_year($doj){
    $sql = "SELECT begin,end FROM `".TB_PREF."fiscal_year` WHERE  begin <=".db_escape($doj)." and end >=".db_escape($doj)." limit 1";
      $result = db_query($sql, "could not get the selected Employees");
      //display_error($sql);
      $res = db_fetch_row($result);

      return $res;
}



//---------------------------------------------------------------------------------------
function kv_get_months_in_fiscal_year(){   
    $Year_id = get_post('year');
	if($Year_id){
		$fiscal_year_get = get_fiscalyear($Year_id);
	} else {
        $Year_id = get_post('PARAM_0');
        if($Year_id)
            $fiscal_year_get = get_fiscalyear($Year_id);
        else
		    $fiscal_year_get = get_current_fiscalyear();
	}
    $array_months = array();
    $start    = (new DateTime($fiscal_year_get['begin']))->modify('first day of this month');
    $end      = (new DateTime($fiscal_year_get['end']))->modify('first day of next month');
    $interval = DateInterval::createFromDateString('1 month');
    $period   = new DatePeriod($start, $interval, $end);

    foreach ($period as $dt) {
        $array_months[$dt->format("m")] = date("m - F", strtotime($dt->format("Y")."-".$dt->format("m")."-01"));
    }
    return $array_months;
}

//--------------------------------------------------------------------------------------
function getManualSalaryId1($empl_id,$month){
        $sql = "select id from ".TB_PREF."manual_sal_deduction where empl_id  = ".db_escape($empl_id)." AND added_date like '$month%' LIMIT 1";
      //  display_error($sql);
    $result = db_query($sql,'could not get the getEmployeeType');
    $row = db_fetch($result);
    return $row['id'];  
}

function getValueFromManualSalary1($empl_id){
    $sql = "select *  from ".TB_PREF."manual_sal_deduction where id  = ".db_escape($empl_id)." LIMIT 1";
    
    $result = db_query($sql,'could not get the getEmployeeType');
    return $result;
}

//---------------------------------------------------------------------------------------
function kv_get_months_with_years_in_fiscal_year($Year_id){   
   
    $fiscal_year_get = get_fiscalyear($Year_id);
    
 
    $array_months = array();
    $start    = (new DateTime($fiscal_year_get['begin']))->modify('first day of this month');
    $end      = (new DateTime($fiscal_year_get['end']))->modify('first day of next month');
    $interval = DateInterval::createFromDateString('1 month');
    $period   = new DatePeriod($start, $interval, $end);

    foreach ($period as $dt) {
        $array_months[$dt->format("m")] = date("Y-m-d", strtotime($dt->format("Y")."-".$dt->format("m")."-01"));
    }
	
    return $array_months;
}

//---------------------------------------------------------------------------------------
function  kv_current_fiscal_months_list_row($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false) {  
    
    echo "<tr><td class='label'>$label</td>";
    kv_current_fiscal_months_list_cell(null, $name, $selected_id, $submit_on_change, $disabled);
    echo "</tr>\n";
}
//---------------------------------------------------------------------------------------
function  kv_current_fiscal_months_list_cell($label=null, $name, $selected_id=null, $submit_on_change=false, $disabled=false) {  
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>". kv_current_fiscal_months($name, $selected_id, $submit_on_change, $disabled)."</td>\n";
}
function kv_current_fiscal_months($name, $selected_id=null, $submit_on_change=false, $disabled=false) {
   
   $Year_id = get_post('year');
   $month_id = (int)get_post($name); 
  
   if(!empty($month_id) && $month_id>0 ){} else { $selected_id = date("m");}
    $hrm_months_list = kv_get_months_in_fiscal_year();
    $options = array(
        'select_submit'=> $submit_on_change,
        'disabled' => $disabled
    );
    return  array_selector($name, $selected_id, $hrm_months_list, $options);
}

function kv_month_name_by_id($month){
    $months =  array(
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December'
                );
    return $months[$month];
}



function kv_fiscal_year_by_id($year) {
    
	$sql = "SELECT CONCAT(begin,'---',end) AS f_year FROM ".TB_PREF."fiscal_year WHERE id=".db_escape($year)."";
	
	$result=db_query($sql,"Fiscal Year Could not get");
	
	$res = db_fetch_row($result);
	//CONCAT(begin,'---',end) AS f_year
    return $res;
}
//---------------------------------------------------------------------------------------------------

function kv_fiscalyears_list($name, $selected_id=null, $submit_on_change=false,$all_oprtion = false){

	$sql = "SELECT * FROM ".TB_PREF."fiscal_year";

// default to the company current fiscal year

	return combo_input($name, $selected_id, $sql, 'id', '',
	array(
		'order' => 'begin',
                 'spec_option' => $all_oprtion,
		'default' => get_company_pref('f_year'),
		'format' => '_format_fiscalyears',
		'select_submit'=> $submit_on_change,
		'async' => true
	) );
}
     

function kv_fiscalyears_list_cells($label, $name, $selected_id=null, $submit_on_change=false,$all_option = false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td style='text:center;'>";
	echo kv_fiscalyears_list($name, $selected_id, $submit_on_change,$all_option);
	echo "</td>\n";
}

function kv_fiscalyears_list_row($label, $name, $selected_id=null, $submit_on_change=false){
	echo "<tr><td class='label'>$label</td>";
	kv_fiscalyears_list_cells(null, $name, $selected_id, $submit_on_change);
	echo "</tr>\n";
}
      
function calendar_list_row($label, $name, $selected_id=null, $submit_on_change=false){
   
       global $Calender_year;
	echo "<tr><td class='label'>$label</td>";
        echo "<td>";
       
       // display_error($Calender_year);
        
        echo "<select name='$name'>";
        if($Calender_year){
        foreach($Calender_year as $value)    
        {   if($value==$selected_id){
            echo "<option value='$value' selected>".$value. "</option>";
                }
                else{
                echo "<option value='$value'>".$value. "</option>";}
        }
        }
        echo "</select>";
        echo "</td>\n";
	echo "</tr>\n";
}

function kv_truncate_Complete_HRM(){
    display_notification("submitted catch");
    $featured_hrm_tables = array('kv_departments', 'kv_empl_attendancee', 'kv_empl_cv', 'kv_empl_degree', 'kv_empl_experience', 'kv_empl_info', 'kv_empl_job', 'kv_empl_loan', 'kv_empl_salary', 'kv_empl_training', 'kv_loan_types', 'kv_empl_option','kv_empl_certification'); 

    foreach ($featured_hrm_tables as $tbl) {
        $sql_first =" TRUNCATE TABLE ".TB_PREF.$tbl ;
        $result = db_query($sql_first, "could not clean {$tbl} it");
    }

    $sql = "DELETE FROM ".TB_PREF."gl_trans WHERE type=99"; 
    db_query($sql, "Can't delete the GL Transactions");
            
}

function kv_calculate_tds($empl_id){
    $current_f_year = get_company_pref('f_year');
  
    $job_details = get_employee_job($empl_id);
    if(!is_array($job_details) || empty($job_details)){//If result not found, return;
        return false;
    }
    $pre_basic_pay = $job_details['pre_basic_pay'];
    $pre_grade_pay = $job_details['pre_grade_pay'];
    $pre_da = $job_details['pre_da'];
    $pre_hra = $job_details['pre_hra'];
    $pre_conveyance = $job_details['pre_conveyance'];
    $pre_sas = $job_details['pre_sas'];
    $pre_prof_tax = $job_details['pre_prof_tax'];
    $pre_pf = $job_details['pre_pf'];
    $pre_tds = $job_details['pre_tds'];
    $pre_financial_year = $job_details['pre_financial_year'];

    if ($pre_financial_year != $current_f_year) {
        $pre_basic_pay = $pre_grade_pay = $pre_da = $pre_hra = $pre_conveyance = $pre_sas = $pre_prof_tax = $pre_pf = $pre_tds = 0;
    }


  

    $tax_basic_pay = $job_details[1] + $job_details[47];
    $tax_grade_pay = $job_details[2];
    $tax_da = $job_details[3];
    $tax_hra = $job_details[4];
    $tax_conveyance = $job_details[8];
    $tax_sas = $job_details[6];
    $tax_prof_tax = $job_details[10];
    $tax_pf = $job_details[12];


//Investment Plan
    $leave_encashment = $job_details[13];
    $perks_free_house = $job_details[14];
    $other_perks = $job_details[15];
    $tax_on_employment = $job_details[16];
    $interest_on_nsc = $job_details[18];
    $interest_on_housing_loan_negative = $job_details[19];
    $interest_on_deposit_bank = $job_details[20];
    $income_from_house = $job_details[21];
    $public_pf = $job_details[23];
    $lic_direct = $job_details[24];
    $nsc = $job_details[25];
    $int_on_nsc = $job_details[26];
    $infrastructure_bonds = $job_details[27];
    $children_education = $job_details[28];
    $pension_us_80_ccc = $job_details[29];
    $ded_us80_d = $job_details[30];
    $ded_us80_dd = $job_details[31];
    $ded_us80_dd_b = $job_details[32];
    $ded_us80_dd_g = $job_details[33];
    $ded_us80_dd_e = $job_details[34];
    $rent_paid = $job_details[35];
    $udip_lic_dhanraksha = $job_details[36];
    $equity_linked_saving = $job_details[37];
    $repayment_of_housing_loan = $job_details[38];

    $joining_date = explode('-', $job_details['joining']);
    $joining_month = (int) $joining_date[1];
    $joining_year = (int) $joining_date[0];
    $working_days_joining_month = $job_details['working_days_joining_month'];

//Defining static Master value
    
    global $other_perks_percentage, $deductable_hra_percent, $deductable_hra_percent_rent, $qualifying_amount_us80cce, $rebate_us_87a, $rebate_us_87a_exempt,$education_cess_percent, $higher_edu_cess_percent;
            



    $j_month_number_of_days = cal_days_in_month(CAL_GREGORIAN, $joining_month, $joining_year);

    $current_financial_year = get_company_pref('f_year');
   
    $current_month = date('n');
    $current_year = date('Y');

//if joined in current financial year, we need to sum (previous employment salary + salary in joining month + salary to be paid in the remaining months of curent financial year)

    $emp_fiscal_year_history = get_employee_sal_history_year($empl_id, $current_financial_year);
//echo "<pre>";print_r($emp_fiscal_year_history);
    $f_year_months = kv_get_months_in_fiscal_year(); //Fetching Array of months in financial year
############################################## [START]Calculation of TDS ########################################################################
    $joining_month_salary = array();
    $joining_month_basic_salary = 0;
    $prev_employment_basic_salary = 0;
    $total_basic_salary_f_year = 0;
    
####################### [START] Counting number of remaining month in Financial Year #########################

    $start_count = FALSE;
    $rem_months_count = 0;
    foreach ($f_year_months as $key => $value) { //Count number of remaining months till April
        if ((int) $key == $current_month) {//Start counting remaining number of months from current month
            $start_count = TRUE;
        }
        if ($start_count) {
            $rem_months_count++;
        }
    }
    
   /**********[10-12-2018]change to fix on the basis of date of joining ********/ 
    $fisical_date = kv_get_empl_joining_fisical_year($job_details['joining']);
    $effective_date = kv_get_empl_joining_fisical_year($job_details['joining']);
    $runnig_fisical_date = kv_get_empl_joining_fisical_year(date('Y-m-d'));
    
    $running_date = explode('-', $runnig_fisical_date[0]);
    $running_month = (int) $running_date[1];
    $running_year = (int) $running_date[0];
   
    
     $fiscal_begin_date = explode('-', $fisical_date[0]);
    $fiscal_begin_month = (int) $fiscal_begin_date[1];
    $fiscal_begin_year = (int) $fiscal_begin_date[0];
    
     $$effective_begin_date = explode('-', $effective_date[0]);
    $effective_month = (int) $$effective_begin_date[1];
    $effective_year = (int) $$effective_begin_date[0];
 
    
    
   

####################### [END] Counting number of remaining month in Financial Year #########################
####################### [START] Fetching released salary detail from DB for current financial year ########################
//Doing sum of all tds deducted in current organization
    $current_org_month_count = 0;
    $current_org_tds_deducted = 0;
    $current_org_basic_pay = 0;
    $current_org_grade_pay = 0;
    $current_org_da = 0;
    $current_org_hra = 0;
    $current_org_conveyance = 0;
    $current_org_sas = 0;
    $current_org_prof_tax = 0;
    $current_org_pf = 0;



 foreach ($emp_fiscal_year_history as $row) {
        $current_org_tds_deducted += $row['tds'];
       // $current_org_basic_pay += $row[1] + $row[47];
        //$current_org_grade_pay += $row[2];
        //$current_org_da += $row[3];
        //$current_org_hra += $row[4]; 
        //$current_org_prof_tax += $row[10];
        //$current_org_pf += $row[12];
        //$current_org_month_count++;
    }
####################### [END] Fetching released salary detail from DB for current financial year ########################
    //if he is joinned in same fisical year then month would be dynamic calculating if not then default divide by 12
    $pre_no_of_month = ((int)$running_year <= (int)$fiscal_begin_year) ? $rem_months_count : 12 ;

    $pre_no_of_month = ((int)$running_year <= (int)$effective_year) ? $rem_months_count : $pre_no_of_month ;
    
    
   // $pre_no_of_month = ((int)$running_year <= (int)$fiscal_begin_year) ? $pre_no_of_month : 12 ;
   
    //$pre_no_of_month = 12;
    $rem_month_salary = $pre_no_of_month * $tax_basic_pay + $pre_no_of_month * $tax_da + $pre_no_of_month * $tax_grade_pay; //Calculating Salary for the remaining month

    $pre_month_salary = $pre_basic_pay + $pre_grade_pay + $pre_da; //Calculating total salary of previouse organization
   
    $current_org_salary = $current_org_basic_pay + $current_org_grade_pay + $current_org_da;


    $rem_month_salary = $rem_month_salary + $pre_month_salary + $current_org_salary;
    
         
    $rem_month_hra = $tax_hra * $pre_no_of_month; //HRA
    
    $rem_month_hra = $rem_month_hra + $pre_hra + $current_org_hra;
    $rem_month_leave_encashment = round($leave_encashment);
  
    $rem_month_perks_free_house = round($perks_free_house);
    $rem_month_other_perks = round($other_perks * $other_perks_percentage / 100); //other Perks
    $rem_month_conveyance = round($tax_conveyance * $pre_no_of_month) + $pre_conveyance + $current_org_conveyance;
    $rem_month_total_perks = $rem_month_perks_free_house + $rem_month_other_perks + $rem_month_conveyance;
    $rem_month_gross_salary = $rem_month_salary + $rem_month_hra + $rem_month_leave_encashment + $rem_month_total_perks;
      
    $rem_month_rent_paid = round($rent_paid);
    //Calculating Deductable HRA
    $rem_month_salary_40_percent = round($rem_month_salary * $deductable_hra_percent / 100);
    $rem_month_salary_10_percent = round($rem_month_salary * $deductable_hra_percent_rent / 100);
    $rem_month_salary_rent_minus = $rem_month_rent_paid - $rem_month_salary_10_percent;
    //Calculating deductable HRA: Mininum of $rem_month_hra or $rem_month_salary_40_percent or $rem_month_salary_rent_minus
    if ($rem_month_hra < $rem_month_salary_40_percent) {
        if ($rem_month_hra < $rem_month_salary_rent_minus) {
            $rem_month_deductible_hra = $rem_month_hra;
        } else {
            $rem_month_deductible_hra = $rem_month_salary_rent_minus;
        }
    } else {
        if ($rem_month_salary_40_percent < $rem_month_salary_rent_minus) {
            $rem_month_deductible_hra = $rem_month_salary_40_percent;
        } else {
            $rem_month_deductible_hra = $rem_month_salary_rent_minus;
        }
    }
    //Calcuaing Exempted HRA
    $rem_exempted_hra_us10 = ($rem_month_deductible_hra > 0) ? $rem_month_deductible_hra : 0;
    $gratuity_payment_exempt = 0;
    $leave_encashment_resignation = 0;
    $rem_total_deduction_us10 = $rem_exempted_hra_us10 + $gratuity_payment_exempt + $leave_encashment_resignation;
    $rem_month_gross_salary_after_deduct_us10 = $rem_month_gross_salary - $rem_total_deduction_us10;
    $entertainment_allowance = 0;
    $rem_month_total_deduction_us16 = $tax_on_employment + $entertainment_allowance;
    $income_chargeable_salary = $rem_month_gross_salary_after_deduct_us10 - $rem_month_total_deduction_us16;
    $rem_month_other_total_income = round($interest_on_nsc + $interest_on_housing_loan_negative + $interest_on_deposit_bank + $income_from_house);
    $rem_month_gross_total_income = round($rem_month_other_total_income + $income_chargeable_salary);
    $rem_month_statutory_pf = round($tax_pf * $pre_no_of_month) + $pre_pf + $current_org_pf;
    $rem_month_public_pf = round($public_pf);
    $rem_month_lic_direct = round($lic_direct);
    $rem_month_nsc = round($nsc);
    $rem_month_interest_on_nsc = round($int_on_nsc);
    $rem_month_udip_lic_dhanraksha = round($udip_lic_dhanraksha);
    $rem_month_equity_linked_saving = round($equity_linked_saving);
    $rem_month_repayment_of_housing_loan = round($repayment_of_housing_loan);
    $rem_month_sas = round($tax_sas * $pre_no_of_month) + $pre_sas + $current_org_sas;
    $rem_month_infrastructure_bonds = round($infrastructure_bonds);
    $rem_month_children_education = round($children_education);
    $total_of_80c = round($rem_month_statutory_pf + $rem_month_public_pf + $rem_month_lic_direct + $rem_month_nsc + $rem_month_interest_on_nsc + $rem_month_udip_lic_dhanraksha + $rem_month_equity_linked_saving + $rem_month_repayment_of_housing_loan + $rem_month_sas + $rem_month_infrastructure_bonds + $rem_month_children_education);
    $rem_month_pension_us_80_ccc = round($pension_us_80_ccc);
    $rem_month_total_amount_80c_80ccc = round($total_of_80c + $rem_month_pension_us_80_ccc);
    $rem_month_qualifying_amount_80cce_1 = ($rem_month_total_amount_80c_80ccc > $qualifying_amount_us80cce) ? $qualifying_amount_us80cce : $rem_month_total_amount_80c_80ccc;
    $infrastructure_bonds_80ccf_upto_20000 = 0;
    $rem_month_total_amt_80c_80ccc_80ccf = $rem_month_qualifying_amount_80cce_1 + $infrastructure_bonds_80ccf_upto_20000;
    $rem_month_qualifying_amount_80cce_2 = ($rem_month_total_amt_80c_80ccc_80ccf > $qualifying_amount_us80cce) ? $qualifying_amount_us80cce : $rem_month_total_amt_80c_80ccc_80ccf;
    $full_year_total_income = $rem_month_gross_total_income - $rem_month_qualifying_amount_80cce_1 - $ded_us80_d - $ded_us80_dd - $ded_us80_dd_b - $ded_us80_dd_g - $ded_us80_dd_e;
//$full_year_total_income = round($pre_net_salary + $rem_month_net_income);
//=ROUND(IF(BB6<=250000,0,+IF(BB6<=500000,(BB6-250000)*0.05,+IF(BB6<=1000000,12500+(BB6-500000)*0.2,112500+(BB6-1000000)*0.3))),0)
//Calculating Tax on Full Income
    
   global $zero_tax_amount,$first_tax_amount,$second_tax_amount,$first_tax_amount_percent,$second_tax_amount_percent,$after_second_tax_amount_percent;


    
    
  
    
    $tax_on_net_income = 0;
    if ($full_year_total_income <= $zero_tax_amount) {
        $tax_on_net_income = 0;
    } else {
        if ($full_year_total_income <= $first_tax_amount) {
            
            $tax_on_net_income = round(($full_year_total_income - ($first_tax_amount - $zero_tax_amount)) * $first_tax_amount_percent / 100);
           
        } else {
            if ($full_year_total_income <= $second_tax_amount) {
                $first_tax_amount_payable = ($first_tax_amount - $zero_tax_amount) * $first_tax_amount_percent / 100; //Calculating Payable Tax Between 250000 AND 500000
                $second_tax_amount_payable = ($full_year_total_income - $first_tax_amount) * $second_tax_amount_percent / 100; //Calculating Payable Tax Between 500000 AND 1000000
                $tax_on_net_income = round($first_tax_amount_payable + $second_tax_amount_payable); //Sum of both payable amount
            } else {
                $first_tax_amount_payable = ($first_tax_amount - $zero_tax_amount) * $first_tax_amount_percent / 100; //Calculating Payable Tax Between 250000 AND 500000
                $second_tax_amount_payable = ($second_tax_amount - $first_tax_amount) * $second_tax_amount_percent / 100; //Calculating Payable Tax Between 500000 AND 1000000
                $after_second_tax_amount_payable = ($full_year_total_income - $second_tax_amount) * $after_second_tax_amount_percent / 100;
                $tax_on_net_income = round($first_tax_amount_payable + $second_tax_amount_payable + $after_second_tax_amount_payable);
            }
        }
    }

    $net_tax_rebate_us_87a = ($full_year_total_income < $rebate_us_87a) ? $tax_on_net_income - $rebate_us_87a_exempt : $tax_on_net_income;

    if ($net_tax_rebate_us_87a < 0) {
        $net_tax = 0;
    } else {
        $net_tax = $net_tax_rebate_us_87a;
    }
    $surcharge = 0;

    $education_cess = ($net_tax + $surcharge) * $education_cess_percent / 100;
    $higher_edu_cess = ($net_tax + $surcharge) * $higher_edu_cess_percent / 100;
    $total_tax_liability = round($net_tax + $education_cess + $higher_edu_cess);



    $tax_deducted_fy = $pre_tds + $current_org_tds_deducted;
    $balance_tds = round($total_tax_liability - $tax_deducted_fy);
    $tds_per_month = round($balance_tds / ((int)$pre_no_of_month - (int)count($emp_fiscal_year_history)));

        
    $tax_detail = array(
        'payable_tds_per_month' => $tds_per_month,
        'total_paid_tds' => $tax_deducted_fy,
        'gross_salary' => $rem_month_gross_salary,
        'total_tax_liability' => $total_tax_liability,
        'total_pending_tds' => $balance_tds,
    );
    return $tax_detail;
}


function kv_hrm_get_contractempl_list($from_date,$to_date,$dept_id,$contract){
	
	$f_date=date2sql($from_date);
	$t_date=date2sql($to_date);
	
    $sql ="SELECT empl_info.empl_firstname,"."job.empl_id,  "."dpt.description,"."desg.name,"."empl_info.addr_line1,"."empl_info.mobile_phone, " ."empl_info.email,". " job.joining,CASE WHEN (job.empl_type = '3') THEN 'Short Term Contract' WHEN (job.empl_type ='4') THEN 'Long Term Contract' END AS employees_type ,". " job.contract_end_date,". " job.empl_type "."FROM ".TB_PREF."kv_empl_info empl_info JOIN ".TB_PREF."kv_empl_job job ON  job.empl_id = empl_info.empl_id JOIN ".TB_PREF."kv_departments dpt ON job.department= dpt.id JOIN ".TB_PREF."designation_master desg ON job.desig= desg.id WHERE empl_info.status =1 AND job.1=1";
	
	
	if ($from_date) {
		$sql .= " AND job.contract_end_date >= ". db_escape($f_date);
	}
	if ($to_date) {
		$sql .= " AND job.contract_end_date <= ". db_escape($t_date);
	}
	if ($dept_id != -1) {
		$sql .= " AND dpt.id=".db_escape($dept_id);
	}
	if ($contract) {
		$sql .= " AND job.empl_type=".db_escape($contract);
	}
	
	//display_error($sql); die;
	return $sql;
}

/*
 * 
 * created by Ashutosh 
 * for more information 
 * contact @  http://ashutoshsharma.com
 * get value on fisical year
 * 
 */



function getFisicalYearByDate($date){
  
     global $first_year_month, $second_year_month ;
             $month_year = explode('-',$date);
         $monthNum  = $month_year[1];
        $dateObj   = DateTime::createFromFormat('!m', $monthNum);
        $monthName = $dateObj->format('F');
       
         if(in_array($monthName,$first_year_month)){
            $prev_year =  $month_year[0];
            $month = count($second_year_month)+1;
            $month = strlen($month)>1?$month:'0'.$month;
             $begin_date = $prev_year.'-'.$month.'-'.'01';
             
             $month = count($second_year_month);
             $month = strlen($month)>1?$month:'0'.$month;
             
             $end_date =    ($month_year[0] + 1 ).'-'.$month.'-'.'31';//===month[0] is year
         }
                 
         if(in_array($monthName,$second_year_month)){
             $prev_year =  $month_year[0];//====year + 1
           
            $month = count($second_year_month)+1;
             $month = strlen($month)>1?$month:'0'.$month;
             
           
             $begin_date =    ($month_year[0] - 1).'-'.$month.'-'.'01';
             $month = count($second_year_month);
                $month = strlen($month)>1?$month:'0'.$month;
             $end_date = $prev_year.'-'.$month.'-'.'31';
         }
         
        $sql = "SELECT `id` FROM ".TB_PREF."fiscal_year where begin =".db_escape($begin_date)." and end =".db_escape($end_date)." LIMIT 1";
         // display_error($sql);
        $result  =  db_query($sql, "Can't delete the GL Transactions");
        $row = db_fetch_row($result);  
        $data['f_year']  = $row[0];
        $data['month'] = $monthNum;
    return $data;
}



/* function get_expcontractlist($empl_id,$empl_name,$empl_email,$dept_id,$status,$empl_type,$dt){

	//display_error($status); die;
	
    $sql ="SELECT empl_info.empl_firstname,"."job.empl_id,  "."dpt.description,"."desg.name,"."empl_info.addr_line1,CASE  WHEN(job.grade = '1')  THEN  'A - Individual Contributor' WHEN (job.grade = '2') THEN 'B - Professional' WHEN (job.grade ='3') THEN 'C - Manager ' WHEN (job.grade ='4') THEN 'D - Director' WHEN (job.grade ='5') THEN 'E - Vice President' END AS grade,"."empl_info.mobile_phone, " ."empl_info.email,". " job.joining,". " job.empl_type "."FROM ".TB_PREF."kv_empl_info empl_info JOIN ".TB_PREF."kv_empl_job job ON  job.empl_id = empl_info.empl_id JOIN ".TB_PREF."kv_departments dpt ON job.department= dpt.id JOIN ".TB_PREF."designation_master desg ON job.desig= desg.id WHERE 1=1";
	
	if ($empl_id) {
		$sql .= " AND empl_info.empl_id LIKE ". db_escape("%$empl_id%");
	}
	if ($empl_name) {
		$sql .= " AND empl_info.empl_firstname LIKE ". db_escape("%$empl_name%");
	}
	if ($empl_email) {
		$sql .= " AND empl_info.email LIKE ". db_escape("%$empl_email%");
	}
	if ($dept_id != -1) {
		$sql .= " AND dpt.id=".db_escape($dept_id);
	}
	if ($status) {
		$sql .= " AND empl_info.status=".db_escape($status);
	}
	if ($empl_type) {
		$sql .= " AND job.empl_type=".db_escape($empl_type);
	}
	if ($dt) {
		//$diff_mon=date_diff2(job.contract_end_date,$dt,'m');
	//	if($diff_mon < 1){
		$sql .= " AND job.contract_end_date >=".db_escape($dt);
	//	}
	}
	display_error($sql); die;
	$query=db_query($sql);
	return db_fetch($query);
}  */

?>