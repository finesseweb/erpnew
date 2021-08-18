<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
function add_employee($empl_id, $empl_salutation,$salutation_text, $empl_firstname,$empl_middlename,$empl_lastname, $addr_line1, $addr_line2,$correspondence_address,$permanent_address,$same_as_correspond_address,
$home_phone, $mobile_phone, $email, $gender, $date_of_birth, $age, $pf_number,$pan_no,$aadhaar_no,$esi_no,$pran_no,$marital_status,$no_of_children, $status,$empl_city,$empl_state,$pincode,$country,$per_addr_line1,$per_addr_line2,$empl_per_city,$per_country,$empl_per_state,$per_pincode,$eligible_hra=null){
	 	
		$date_of_birt = date2sql($date_of_birth); 
		$sql = "INSERT INTO ".TB_PREF."kv_empl_info (empl_id, empl_salutation,salutation_text,empl_firstname,empl_middlename,empl_lastname, addr_line1,addr_line2,correspondence_address,permanent_address,same_as_correspond_address,home_phone,mobile_phone,email, gender, date_of_birth, age,pf_number,pan_no,aadhaar_no,esi_no,pran_no, marital_status,no_of_children, status,empl_city,empl_state,pincode,country,per_addr_line1,per_addr_line2,empl_per_city,per_country,empl_per_state,per_pincode,eligible_hra) VALUES ("
		.db_escape($empl_id).", "
		.db_escape($empl_salutation).", ".db_escape($salutation_text).", "
		.db_escape($empl_firstname).",".db_escape($empl_middlename).",
		".db_escape($empl_lastname).", "
		.db_escape($addr_line1).", "
		.db_escape($addr_line2).",".db_escape($correspondence_address).",".db_escape($permanent_address).",".db_escape($same_as_correspond_address).","
		.db_escape($home_phone).", " 
		.db_escape($mobile_phone).", " 
		.db_escape($email).", " 
		.db_escape($gender).", "
		.db_escape($date_of_birt).", "
		.db_escape($age).", "
		.db_escape($pf_number).", "
		.db_escape($pan_no).", "
		.db_escape($aadhaar_no).", "
		.db_escape($esi_no).", "
		.db_escape($pran_no).", "
		.db_escape($marital_status).", "
		.db_escape($no_of_children).", "
		.db_escape($status).","
		.db_escape($empl_city).","
		.db_escape($empl_state).",
		".db_escape($pincode).",
		".db_escape($country).",".db_escape($per_addr_line1).",".db_escape($per_addr_line2).",".db_escape($empl_per_city).",".db_escape($per_country).",".db_escape($empl_per_state).",".db_escape($per_pincode).",".db_escape($eligible_hra).")";
                
                
	db_query($sql,"The employee could not be added");
}


//--------------------------------------------------------------------------------------------
function update_employee($selected_id, $empl_salutation,$salutation_text, $empl_firstname,$empl_middlename,$empl_lastname, $addr_line1, $addr_line2, $correspondence_address,$permanent_address,$same_as_correspond_address,$home_phone, $mobile_phone, $email, $gender, $date_of_birth, $age,$pf_number,$pan_no,$aadhaar_no,$esi_no,$pran_no,$marital_status,$no_of_children,$status,$empl_city,$empl_state,$pincode,$country,$per_addr_line1,$per_addr_line2,$empl_per_city,$per_country,$empl_per_state,$per_pincode,$eligible_hra=null){

	$date_of_birt = date2sql($date_of_birth); 
	$sql = "UPDATE ".TB_PREF."kv_empl_info SET
		empl_id=".db_escape($selected_id).",
		empl_salutation=".db_escape($empl_salutation).",
		salutation_text = ".db_escape($salutation_text).",
		empl_firstname=".db_escape($empl_firstname).",
		empl_middlename=".db_escape($empl_middlename).",
		empl_lastname=".db_escape($empl_lastname).",
		addr_line1=".db_escape($addr_line1).",
		addr_line2=".db_escape($addr_line2).",
		correspondence_address = ".db_escape($correspondence_address).",
		permanent_address = ".db_escape($permanent_address).",
		same_as_correspond_address = ".db_escape($same_as_correspond_address).",
		home_phone=".db_escape($home_phone).",
		mobile_phone=".db_escape($mobile_phone).",
		email=".db_escape($email).",
		gender=".db_escape($gender).",
		date_of_birth=".db_escape($date_of_birt).",
		age=".db_escape($age).",
		pf_number=".db_escape($pf_number).",
		pan_no=".db_escape($pan_no).",
		aadhaar_no=".db_escape($aadhaar_no).",
		esi_no=".db_escape($esi_no).",
		pran_no=".db_escape($pran_no).",
		marital_status=".db_escape($marital_status).",
		no_of_children=".db_escape($no_of_children).",
		status=".db_escape($status).",
		empl_city=".db_escape($empl_city).",
		empl_state=".db_escape($empl_state).",
		pincode=".db_escape($pincode).",
		country=".db_escape($country).",
		per_addr_line1 = ".db_escape($per_addr_line1).",
		per_addr_line2 = ".db_escape($per_addr_line2).",
		empl_per_city = ".db_escape($empl_per_city).",
		per_country = ".db_escape($per_country).",
		empl_per_state = ".db_escape($empl_per_state).",
		per_pincode = ".db_escape($per_pincode).",
		eligible_hra = ".db_escape($eligible_hra)."
		
		WHERE empl_id=".db_escape($selected_id);
		
	db_query($sql,"The employee could not be updated");

}

function has_empl_id($empl_id){
     $sql = "select * from ".TB_PREF."kv_empl_info where empl_id = ".db_escape($empl_id);
    $result = db_query($sql, "could not get the selected Employees");
    $rowcount = db_num_rows($result);
    return $rowcount;
}

function kv_get_employees_dept(){
    $sql = "select * from fa_kv_departments";
    return db_query($sql, "could not get the selected Employees");
}


function kv_get_employees_based_on_dept($dept_id) {

	if($dept_id == 0){
		//get_all_employees();
		$sql="SELECT job.empl_id,job.joining FROM ".TB_PREF."kv_empl_job as job LEFT JOIN ".TB_PREF."kv_empl_info as empl_info ON empl_info.empl_id = job.empl_id  WHERE  job.department in (1,2,3,4,6,7,8,9,10,13,14,15) AND (empl_info.status = '1' OR status = '2')";
               //print($sql);
		return db_query($sql, "The employee table is inaccessible");

	} else{
		$sql="SELECT job.empl_id,job.joining FROM ".TB_PREF."kv_empl_job as job LEFT JOIN ".TB_PREF."kv_empl_info as empl_info ON empl_info.empl_id = job.empl_id  WHERE job.department=".db_escape($dept_id)." AND (empl_info.status = '1' OR empl_info.status = '2') AND job.empl_id like 'EMP%'";
		
		return db_query($sql, "could not get the selected Employees");
	}
}

function kv_get_employees_count_based_on_dept($dept_id) {

	if($dept_id == 0){
		//get_all_employees();
		$sql = "SELECT COUNT(empl_id) FROM ".TB_PREF."kv_empl_info";
  
		return check_empty_result($sql);

	} else{
		$sql="SELECT COUNT(empl_id) FROM ".TB_PREF."kv_empl_job WHERE department=".db_escape($dept_id);
		return check_empty_result($sql);
	}
}

function kv_get_empl_name($id) {
	$sql = "SELECT empl_firstname FROM ".TB_PREF."kv_empl_info WHERE empl_id=".db_escape($id)."LIMIT 1";

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	
	return $row[0];
}
 
function is_paid($month,$f_year,$empl_id) {
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($empl_id)." AND month = $month And year = $f_year";
	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	
	return count($row[0])>0?true:false;
}
function pay_slip_id($month,$f_year,$empl_id) {
	$sql = "SELECT id FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($empl_id)." AND month = $month And year = $f_year";
       // display_error($sql);
	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	
	return $row[0];
}
function trans_l($type_no) {
	$sql = "SELECT tally_status FROM ".TB_PREF."gl_trans WHERE tally_status = 1 and type_no = $type_no LIMIT 1";
       // display_error($sql);
	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	
	return strlen($row[0])>0?$row[0]:0;
}

function trans_l_update($type_no) {
	$sql = "update  ".TB_PREF."gl_trans set tally_status = 1 WHERE type_no = $type_no";
       // display_error($sql);
	$result = db_query($sql, "could not get sales type");

	//$row = db_fetch_row($result);
	
	//return $row[0];
}

function is_exist($empl_id) {
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($empl_id);
	$result = db_query($sql, "could not get sales type");
	
        $row = db_fetch_row($result);
        
	return count($row[0])>0?true:false;
}
 

function getYearOnfisicalYear($year,$monthNum,$fisical_year){
    

     $first_year = date('Y',strtotime($fisical_year['begin']));
     $second_year = date('Y',strtotime($fisical_year['end']));
     $dateObj   = DateTime::createFromFormat('!m', $monthNum);
     
     $monthName = $dateObj->format('F');
    
     $year = date('Y');
     
     
     $first_year_month = array('April','May','June','July','August','September','October','November','December');
     $second_year_month = array('January','February','March');
     
     if(in_array($monthName,$first_year_month)){
          $year = $first_year;
     }
     else if(in_array($monthName,$second_year_month)){
         $year = $second_year;
     }
     return $year;
}

//--------------------------------------------------------------------------------------------
function get_all_employees($active=true){
  //get only active employees by default
  if ($active == true)
    $act_sql = "WHERE inactive = 0";

  $sql = "SELECT empl_id, empl_firstname FROM ".TB_PREF."kv_empl_info $act_sql";
  
  return db_query($sql, "The employee table is inaccessible");

}
//--------------------------------------------------------------------------------------------
function get_all_employees_list($active=true){
  //get only active employees by default
  if ($active == true)
    $act_sql = "WHERE inactive = 0";

  $sql = "SELECT * FROM ".TB_PREF."kv_empl_info $act_sql";
  
  return db_query($sql, "The employee table is inaccessible");

}
function get_all_employee_salary($f_year, $month_arr){
  //display_error($empl_id);
    if(!empty($month_arr)){
    $months = implode(',', $month_arr);

  //$sql = "SELECT * FROM ".TB_PREF."kv_empl_info $act_sql ORDER BY empl_id";
  
  //$sql = "SELECT emp.empl_id, sal.`old_monthly_basic`, sal.`old_monthly_grade_pay`, sal.`old_monthly_da`,sal.`old_monthly_hra`, sal.`old_monthly_conveyance`, job.`1` as basic_monthly, job.`2` as gp_monthly, job.`3` as da_monthly, job.`eligible_hra`, CONCAT(emp.empl_firstname,' ', emp.empl_middlename,' ', emp.empl_lastname) as emp_name, sal.pre_basic,sal.pre_grade_pay, sal.pre_da, sal.pre_pf, sal.pre_sas, sal.pre_hra, sal.pre_conveyance, sal.pre_tds FROM ".TB_PREF."kv_empl_info emp LEFT JOIN (SELECT empl_id, `1` as old_monthly_basic, `2` as old_monthly_grade_pay, `3` as old_monthly_da, `4` as old_monthly_hra, `8` as old_monthly_conveyance, sum(`1`) as pre_basic, sum(`2`) as pre_grade_pay, sum(`3`) as pre_da, sum(`4`) as pre_hra, sum(`8`) as pre_conveyance, sum(`12`) as pre_pf, sum(`6`) as pre_sas, sum(`tds`) as pre_tds FROM ".TB_PREF."kv_empl_salary WHERE is_arrear IS NULL AND year='$f_year' AND month IN($months) GROUP BY ".TB_PREF."kv_empl_salary.empl_id) sal ON sal.empl_id = emp.empl_id LEFT JOIN ".TB_PREF."kv_empl_job job ON job.empl_id = emp.empl_id WHERE emp.status = 1 ORDER BY emp.empl_id";
  $sql = "SELECT emp.empl_id, sal.`old_monthly_basic`, sal.`old_monthly_grade_pay`, sal.`old_monthly_da`,sal.`old_monthly_hra`, sal.`old_monthly_conveyance`, job.`1` as basic_monthly, job.`2` as gp_monthly, job.`3` as da_monthly, job.`eligible_hra`, CONCAT(emp.empl_firstname,' ', emp.empl_middlename,' ', emp.empl_lastname) as emp_name, sal.pre_basic,sal.pre_grade_pay, sal.pre_da, sal.pre_pf, sal.pre_sas, sal.pre_hra, sal.pre_conveyance, sal.pre_tds FROM ".TB_PREF."kv_empl_info emp LEFT JOIN (SELECT empl_id, `1` as old_monthly_basic, `2` as old_monthly_grade_pay, `3` as old_monthly_da, `4` as old_monthly_hra, `8` as old_monthly_conveyance, sum(`1`) as pre_basic, sum(`2`) as pre_grade_pay, sum(`3`) as pre_da, sum(CASE WHEN accom_hra_by_org = \"1\" THEN `4` ELSE 0 END) as pre_hra, sum(`8`) as pre_conveyance, sum(`12`) as pre_pf, sum(`6`) as pre_sas, sum(`tds`) as pre_tds FROM ".TB_PREF."kv_empl_salary WHERE is_arrear IS NULL AND year='$f_year' AND month IN($months) GROUP BY ".TB_PREF."kv_empl_salary.empl_id) sal ON sal.empl_id = emp.empl_id LEFT JOIN ".TB_PREF."kv_empl_job job ON job.empl_id = emp.empl_id WHERE  emp.status = 1 ORDER BY emp.empl_id";
  //echo $sql;
  
  return db_query($sql, "The employee table is inaccessible");
    }

}



function getBymonth($f_year, $month_arr,$empl_id){
    
        if(!empty($month_arr)){
    $months = implode(',', $month_arr);
    $sql = "SELECT `empl_id`,month,`1` as old_monthly_basic, `2` as old_monthly_grade_pay, `3` as old_monthly_da, `4` as old_monthly_hra, `8` as old_monthly_conveyance, `1` as pre_basic, `2` as pre_grade_pay, `3` as pre_da, CASE WHEN accom_hra_by_org = \"1\" THEN `4` ELSE 0 END as pre_hra, `8` as pre_conveyance, `12` as pre_pf, `6` as pre_sas, `tds` as pre_tds  FROM fa_kv_empl_salary WHERE empl_id = '$empl_id' AND month in($months) AND year=$f_year";
      return db_query($sql, "The employee table is inaccessible");
        }
    
}



function getBymonth1($f_year, $month_arr,$empl_id){
        if(!empty($month_arr)){
    $months = implode(',', $month_arr);
    $sql = "SELECT `empl_id`,month,`1` as old_monthly_basic, `2` as old_monthly_grade_pay, `3` as old_monthly_da, `4` as old_monthly_hra, `8` as old_monthly_conveyance, `1` as pre_basic, `2` as pre_grade_pay, `3` as pre_da, CASE WHEN accom_hra_by_org = \"1\" THEN `4` ELSE 0 END as pre_hra, `8` as pre_conveyance, `12` as pre_pf, `6` as pre_sas, `tds` as pre_tds  FROM fa_kv_empl_salary WHERE empl_id = '$empl_id' AND month in($months) AND year=$f_year";
    //display_error($sql);
      return db_query($sql, "The employee table is inaccessible");
        }
    
}



function get_all_employee_salary1($f_year, $month_arr,$empl_id){
  //display_error($empl_id);
    if(!empty($month_arr)){
    $months = implode(',', $month_arr);

  //$sql = "SELECT * FROM ".TB_PREF."kv_empl_info $act_sql ORDER BY empl_id";
  
  //$sql = "SELECT emp.empl_id, sal.`old_monthly_basic`, sal.`old_monthly_grade_pay`, sal.`old_monthly_da`,sal.`old_monthly_hra`, sal.`old_monthly_conveyance`, job.`1` as basic_monthly, job.`2` as gp_monthly, job.`3` as da_monthly, job.`eligible_hra`, CONCAT(emp.empl_firstname,' ', emp.empl_middlename,' ', emp.empl_lastname) as emp_name, sal.pre_basic,sal.pre_grade_pay, sal.pre_da, sal.pre_pf, sal.pre_sas, sal.pre_hra, sal.pre_conveyance, sal.pre_tds FROM ".TB_PREF."kv_empl_info emp LEFT JOIN (SELECT empl_id, `1` as old_monthly_basic, `2` as old_monthly_grade_pay, `3` as old_monthly_da, `4` as old_monthly_hra, `8` as old_monthly_conveyance, sum(`1`) as pre_basic, sum(`2`) as pre_grade_pay, sum(`3`) as pre_da, sum(`4`) as pre_hra, sum(`8`) as pre_conveyance, sum(`12`) as pre_pf, sum(`6`) as pre_sas, sum(`tds`) as pre_tds FROM ".TB_PREF."kv_empl_salary WHERE is_arrear IS NULL AND year='$f_year' AND month IN($months) GROUP BY ".TB_PREF."kv_empl_salary.empl_id) sal ON sal.empl_id = emp.empl_id LEFT JOIN ".TB_PREF."kv_empl_job job ON job.empl_id = emp.empl_id WHERE emp.status = 1 ORDER BY emp.empl_id";
  $sql = "SELECT emp.empl_id, sal.`old_monthly_basic`, sal.`old_monthly_grade_pay`, sal.`old_monthly_da`,sal.`old_monthly_hra`, sal.`old_monthly_conveyance`, job.`1` as basic_monthly, job.`2` as gp_monthly, job.`3` as da_monthly, job.`eligible_hra`, CONCAT(emp.empl_firstname,' ', emp.empl_middlename,' ', emp.empl_lastname) as emp_name, sal.pre_basic,sal.pre_grade_pay, sal.pre_da, sal.pre_pf, sal.pre_sas, sal.pre_hra, sal.pre_conveyance, sal.pre_tds FROM ".TB_PREF."kv_empl_info emp LEFT JOIN (SELECT empl_id, `1` as old_monthly_basic, `2` as old_monthly_grade_pay, `3` as old_monthly_da, `4` as old_monthly_hra, `8` as old_monthly_conveyance, sum(`1`) as pre_basic, sum(`2`) as pre_grade_pay, sum(`3`) as pre_da, sum(CASE WHEN accom_hra_by_org = \"1\" THEN `4` ELSE 0 END) as pre_hra, sum(`8`) as pre_conveyance, sum(`12`) as pre_pf, sum(`6`) as pre_sas, sum(`tds`) as pre_tds FROM ".TB_PREF."kv_empl_salary WHERE empl_id = '$empl_id' AND is_arrear IS NULL AND year='$f_year' AND month IN($months) GROUP BY ".TB_PREF."kv_empl_salary.empl_id) sal ON sal.empl_id = emp.empl_id LEFT JOIN ".TB_PREF."kv_empl_job job ON job.empl_id = emp.empl_id WHERE job.empl_id='$empl_id' AND emp.status = 1 ORDER BY emp.empl_id";

  
  return db_query($sql, "The employee table is inaccessible");
    }

}













function get_all_employee_arrear_salary($f_year, $month_arr){
    

  //$sql = "SELECT * FROM ".TB_PREF."kv_empl_info $act_sql ORDER BY empl_id";
  
    $sql = "SELECT empl_id, paid_for_months_list, `1` as basic, `2` as grade_pay, `3` as da, `4` as hra, `8` as conveyance, `12` as pf, `6` as sas, tds FROM ".TB_PREF."kv_empl_salary WHERE is_arrear = 1 AND paid_for_f_year='$f_year'";
    $employees = db_query($sql, "The Salary table is inaccessible");
    $arrear_salary = array();
    while($row = db_fetch($employees)){
        $month_list = explode(',', $row['paid_for_months_list']);
        foreach($month_arr as $month){            
            if(in_array($month, $month_list)){
                $arrear_salary[] = $row;
                break;
            }            
        }
    }
    return $arrear_salary;
}



function get_all_employee_arrear_salary1($f_year, $month_arr,$empl_id){

  //$sql = "SELECT * FROM ".TB_PREF."kv_empl_info $act_sql ORDER BY empl_id";
   
    $sql = "SELECT month, year, empl_id, paid_for_months_list, `1` as basic, `2` as grade_pay, `3` as da, `4` as hra, `8` as conveyance, `12` as pf, `6` as sas, tds FROM ".TB_PREF."kv_empl_salary WHERE is_arrear = 1 AND paid_for_f_year='$f_year' And empl_id = '$empl_id'";
         
    $employees = db_query($sql, "The Salary table is inaccessible");
    $arrear_salary = array();
    while($row = db_fetch($employees)){
        $month_list = explode(',', $row['paid_for_months_list']);
        foreach($month_arr as $month){            
            if(in_array($month, $month_list)){
                $arrear_salary[] = $row;
                break;
            }            
        }
    }
    
 //   print_r($arrear_salary);
    return $arrear_salary;
}



function get_all_employee_arrear_salary2($f_year, $month_arr,$empl_id){
    
    $sql = "SELECT month, year, empl_id, paid_for_months_list, `1` as basic, `2` as grade_pay, `3` as da, `4` as hra, `8` as conveyance, `12` as pf, `6` as sas, tds FROM ".TB_PREF."kv_empl_salary_arear_by_month WHERE is_arrear = 1 AND paid_for_f_year='$f_year' And empl_id = '$empl_id'";
//display_error($sql);
    $employees = db_query($sql, "The Salary table is inaccessible");
   
    $arrear_salary = array();
    $j = 0;
    while($row = db_fetch($employees)){
        $month_list = explode(',', $row['paid_for_months_list']);
       
        foreach($month_arr as $month){ 
            
            if(in_array($month, $month_list)){
                $arrear_salary[] = $row;
                break;
            }            
        }
    }
    return $arrear_salary;
}



function get_all_employee_arrear_salary_for_pay_slip($f_year, $month_arr,$empl_id){
    
    $str_month = implode(',',$month_arr);
    
    $sql = "SELECT month, year, empl_id, paid_for_months_list, `1` as basic, `2` as grade_pay, `3` as da, `4` as hra, `8` as conveyance, `12` as pf, `6` as sas, tds FROM ".TB_PREF."kv_empl_salary_arear_by_month WHERE is_arrear = 1 AND paid_for_f_year='$f_year' And empl_id = '$empl_id' AND paid_for_months_list in ($str_month)";
//display_error($sql);
    $employees = db_query($sql, "The Salary table is inaccessible");
   
    $arrear_salary = array();
    $j = 0;
    while($row = db_fetch($employees)){
        if($j < db_num_rows($employees)-count($month_arr)){
         
        $month_list = explode(',', $row['paid_for_months_list']);
       
        foreach($month_arr as $month){ 
            
            if(in_array($month, $month_list)){
                $arrear_salary[] = $row;
                break;
            }            
        }
        
       
      }
       $j++;
    }
    return $arrear_salary;
}


function getEmployerPaidArrear($arrear_salary, $empl_id){
    $emp_sal = array();
    foreach($arrear_salary as $row){
        if($row['empl_id'] == $empl_id){
            $emp_sal = $row;
            break;
        }
    }
    return $emp_sal;
}


//--------------------------------------------------------------------------------------------
function get_employee($employee_id){
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_info  WHERE empl_id=".db_escape($employee_id);
//display_error($sql); die;
	$result = db_query($sql, "could not get employee");

	return db_fetch($result);
}
function get_employee_desig_group(){
	$sql = "SELECT * FROM ".TB_PREF."kv_desig_group ";

	$result = db_query($sql, "could not get employee");
        $data = array();
        while($row =  db_fetch($result)){
            $data[] = $row['id'];
        }
        return $data;
}
function get_employee_desig_all($name){
	$sql = "SELECT * FROM ".TB_PREF."designation_master where name like '".$name."%'";

	$result = db_query($sql, "could not get employee");
        $data = array();
        while($row =  db_fetch($result)){
            $data['desiggr'][] = $row['desig_group_id'];
            $data['id'][] = $row['id'];
        }
        return $data;
}
function get_employee_desig_departMent($name){
	$sql = "SELECT * FROM ".TB_PREF."kv_departments where description like '".$name."%' limit 1";

	$result = db_query($sql, "could not get employee");
        $data = array();
        while($row =  db_fetch($result)){
            $data[] = $row['id'];
        }
        return $data;
}
function get_employee_desig($employee_id){
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get employee");

	return db_fetch($result);
}
function get_employee_data($employee_id,$department_id){

	$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($employee_id)." AND department = ".db_escape($department_id)."";
	
	$result = db_query($sql,"could not get employee");
	
	return db_fetch($result);
	

}

function get_employee_gender($employee_id){
	$sql = "SELECT gender FROM ".TB_PREF."kv_empl_info WHERE empl_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get employee");

	return db_fetch_row($result);
}

function get_employee_sal_year($employee_id,$year_id){

	$sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($employee_id)." AND year = ".db_escape($year_id)."";
	
	$result = db_query($sql,"could not get employee");
	
	return db_fetch($result);
	

}

function get_leaves_data($department_id,$desig_group,$desig,$fisc_year,$empl_id=null){
	$sql ="SELECT * FROM ".TB_PREF."kv_leave_master WHERE empl_id = '".$empl_id."'";//AND cal_year=".date("Y");//AND fisc_year = '".$fisc_year."'";
	$result = db_query($sql, "could not get employee");
	return db_fetch($result);
}
//--------------------------------------------------------------------------------------------
function db_has_employees(){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_info");
}

//--------------------------------------------------------------------------------------------
function db_has_employee_email($email){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_info WHERE email=".db_escape($email));
}

function db_has_selected_employee($empl_id){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_info WHERE empl_id=".db_escape($empl_id));
}
//--------------------------------------------------------------------------------------------
function count_employees(){
	//return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_info");
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_info";

	$result = db_query($sql, "could not get employee");

	return db_num_rows($result);
}


function delete_employee($empl_id){
	$tables_array = array('kv_empl_info', 'kv_empl_job', 'kv_empl_attendancee', 'kv_empl_cv', 'kv_empl_degree', 'kv_empl_experience', 'kv_empl_loan', 'kv_empl_salary', 'kv_empl_training','kv_empl_certification');
	foreach($tables_array as $tabl){
		$sql="DELETE FROM ".TB_PREF."{$tabl} WHERE `empl_id`=".db_escape($empl_id);
		
		db_query($sql, "could not delete the selected Employee");
	}
}
function db_has_empl_id(){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_option WHERE option_name = 'next_empl_id'");
}

function kv_get_next_empl_id() {
	 $sql = "SELECT option_value FROM ".TB_PREF."kv_empl_option WHERE option_name='next_empl_id' LIMIT 1";
	 $result = db_query($sql, "could not get sales type");
	 $row = db_fetch_row($result);
	 return $row[0];
}

function delete_empl_id(){
	$sql="DELETE FROM ".TB_PREF."kv_empl_option WHERE option_name= ='next_empl_id'" ;
	db_query($sql, "could not delete the selected Employee"); 
}

function kv_add_next_empl_id_new($empl_id){ 
	//$pos1 = strpos($empl_id, '-');
	$option_value = (int)substr($empl_id, 1); 
	$option_value = $option_value+1 ; 
	$option_2 = substr($empl_id, 0,1); 
	$empl_next_id = $option_2.(string)$option_value; 
		//display_notification($empl_next_id);

	//$empl_id= $option_value; 
	$sql_insert = "INSERT INTO ".TB_PREF."kv_empl_option (option_name,option_value) VALUES ('next_empl_id',$empl_next_id)";				
	db_query($sql_insert,"The employee could not be added");
	//display_notification();
	
}
function kv_update_next_empl_id_new($empl_id){ 
	//$pos1 = strpos($empl_id, '-');
	$option_value = (int)substr($empl_id, 1); 
	$option_value = 'E'.$option_value +1;  	
	$sql_update = "UPDATE ".TB_PREF."kv_empl_option SET option_value=" . db_escape($option_value) . " 
	 WHERE option_name ='next_empl_id' ";
	db_query($sql_update ,"The employee could not be updated");

}



function get_country_code($name)
{
	$sql = "SELECT id from ".TB_PREF."kv_countries WHERE countries_name=".db_escape(ucfirst($name));

	$result = db_query($sql,"could not retreive the account name for $code");

	if (db_num_rows($result) == 1)
	{
		$row = db_fetch_row($result);
		return $row[0];
	}

	//display_db_error("could not retreive the account name for $name", $sql, true);
}
function get_department_code($name)
{
	$sql = "SELECT id from ".TB_PREF."kv_departments WHERE description=".db_escape(ucfirst($name));

	$result = db_query($sql,"could not retreive the account name for $code");

	if (db_num_rows($result) == 1)
	{
		$row = db_fetch_row($result);
		return $row[0];
	}

	//display_db_error("could not retreive the account name for $name", $sql, true);
}
function get_desiggr_code($name)
{
	$sql = "SELECT id from ".TB_PREF."kv_desig_group WHERE name like '".$name."%'";

	$result = db_query($sql,"could not retreive the account name for $code");

	if (db_num_rows($result) == 1)
	{
		$row = db_fetch_row($result);
		return $row[0];
	}

	//display_db_error("could not retreive the account name for $name", $sql, true);
}
function get_desig_code($name,$id)
{
	$sql = "SELECT id from ".TB_PREF."designation_master WHERE desig_group_id = ".db_escape(ucfirst($id))." and name like '".$name."%'";

	$result = db_query($sql,"could not retreive the account name for $code");

	if (db_num_rows($result) == 1)
	{
		$row = db_fetch_row($result);
		return $row[0];
	}

	//display_db_error("could not retreive the account name for $name", $sql, true);
}






function get_state_code($name, $country_id)
{
	$sql = "SELECT state_id from ".TB_PREF."kv_states WHERE country_id=".db_escape($country_id)." and state_name=". db_escape(ucfirst($name)) ;

	$result = db_query($sql,"could not retreive the account name for $code");

	if (db_num_rows($result) == 1)
	{
		$row = db_fetch_row($result);
		return $row[0];
	}

	//display_db_error("could not retreive the account name for $name", $sql, true);
}




function kv_get_empl_desig($id) {
	$sql = "SELECT desig FROM ".TB_PREF."kv_empl_job
 WHERE empl_id=".db_escape($id)."LIMIT 1";

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function check_db_has_salary_account($msg){
	global $path_to_root;
    if (!check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_option WHERE option_name='salary_account'"))  {
    	display_error($msg, true);
    	end_page();
    	exit;	
    }	
}

function check_db_has_employees($msg){
	global $path_to_root;
    if (!check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_info"))  {
    	display_error($msg, true);
    	end_page();
    	exit;	
    }	
}

function get_employee_join_date($empl_id){
    
	$sql = "SELECT joining FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($empl_id)."LIMIT 1";

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function check_db_has_Departments($msg){
	global $path_to_root;
    if (!check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_departments"))  {
    	display_error($msg, true);
    	end_page();
    	exit;	
    }	
}

function check_db_has_Loan_types($msg){
	global $path_to_root;
    if (!check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_loan_types"))  {
    	display_error($msg, true);
    	end_page();
    	exit;	
    }	
	
}
function get_employee_leave_count($month,$year,$empl_id){

	$sql = "SELECT * FROM ".TB_PREF."kv_empl_attendancee WHERE month=".db_escape($month)." AND year=".db_escape($year)." AND empl_id = ".db_escape($empl_id)."";
	
	$result = db_query($sql,"could not get leave count");
	
	$row = db_fetch($result);
	
	return $row;

}


function get_empl_approved_leaves(array $leave_query,$table_name){
   
    $sql = "SELECT * FROM ".TB_PREF."$table_name ";
        if(is_array($leave_query)){
            $sql.='Where ';
                foreach($leave_query as $key => $value){
                    $sql .= "$key  $value";
                }
            }
            //display_error($sql);
            $result = db_query($sql,"could not get result");
           return  db_num_rows($result);
}


function get_employee_leave_count_fiscalyear($year,$empl_id){
      //display_error($year);
     // die();
	//$sql = "SELECT * FROM ".TB_PREF."kv_empl_attendancee WHERE year=".db_escape($year)." AND empl_id=".db_escape($empl_id)." AND cal_year = ". date("Y");
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_attendancee WHERE  empl_id=".db_escape($empl_id);
        if(!empty($year))
            $sql.=" AND year = ". db_escape($year);
	$result  = db_query($sql,"could not get leave count yearly");
	
	
	return $result;
	
	

}
function get_employee_medical_leave_count($year,$empl_id)
{
	$sql .= "SELECT count(".TB_PREF."kv_empl_attendancee.empl_id) as count FROM ".TB_PREF."kv_empl_attendancee WHERE year=".db_escape($year)." AND empl_id=".db_escape($empl_id)."  AND ";
	$sql .= "(";
	$i = 31;
	for($i=1;$i<=31;$i++){
	if($i ==1){
	$sql .= "   ".TB_PREF."kv_empl_attendancee.$i = 'ML' ";
	}else{
	
	$sql .= " OR  ".TB_PREF."kv_empl_attendancee.$i = 'ML' ";
	
	}
	
	}
	$sql .= ")";
	
	$result = db_query($sql,"could not get medical leave count yearly");
	
	$row = db_fetch($result);
	
	return $row;

}

function updateEmployeeLeave($empl_id,$vl,$el,$cl,$ml){
    
    $sql = 'update fa_kv_leave_master set no_of_cls = '.$cl.' , no_of_pls ='.$vl.' , no_of_el ='.$el.' ,no_of_medical_ls ='.$ml.' where empl_id ='. db_escape($empl_id);
    db_query($sql,"get all states");
    
}


function get_all_states(){
$sql = "SELECT state_id, state_name FROM ".TB_PREF."kv_states";
$res = db_query($sql,"get all states");
return $res;

}
?>