<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
//-----------------------------------------------------------------------------------------------
function kv_test_empty_sal($id) {
	
	$test_sql = db_query("SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($id));	
	return db_num_rows($test_sql);
}

function get_empl_bank_acc_details($empl_id){
	$sql="SELECT mod_of_pay, bank_name, acc_no FROM  ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($empl_id)." LIMIT 1";
	$result = db_query($sql,"The employee job details could not be updated"); 
	$empl_id_list=array();
	while($get_des=db_fetch($result)){
		$empl_id_list['mod_of_pay']=$get_des['mod_of_pay'];
		$empl_id_list['bank_name']=$get_des['bank_name'];
		$empl_id_list['acc_no']=$get_des['acc_no'];
	}
		
	return $empl_id_list;
}

function add_empl_department($empl_id, $dept) {
	$sql="INSERT INTO ".TB_PREF."kv_empl_job (`empl_id`, `department`) VALUES (".db_escape($empl_id)." , " .db_escape($dept).")";
	db_query($sql,"The employee job details could not be updated"); 
}

//----------------------------------------------------------------------------------------
function update_employee_job_more($empl_id, $bank_name, $acc_no, $basic, $mod_of_pay){

	$sql = "UPDATE ".TB_PREF."kv_empl_job SET
		mod_of_pay=".db_escape($mod_of_pay).",  
		bank_name=".db_escape($bank_name).",  
		acc_no=".db_escape($acc_no).", 
		basic=".db_escape($basic)."		
		WHERE empl_id=".db_escape($empl_id);

	db_query($sql,"The employee could not be updated");
}

//------------------------------------------------------------------------------
function get_employee_job($employee_id){

	if(is_numeric($employee_id))
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".$employee_id;
	else
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get employee");
	
	return db_fetch($result);	
	
}

//--------------------------------------------------------------------------------------------
function db_employee_has_job($employee_id){
	$sql = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($employee_id);
	
	$result = db_query($sql, "could not get employee");
	
	if (db_fetch_row($result)==0) { 
		return false;	
	} else { 
		return true ;
	}
}

function get_empl_ids_from_dept_id($dept_id) {

	$empl_id_list=array();
	$sql= "SELECT j.empl_id FROM ".TB_PREF."kv_empl_job j INNER JOIN ".TB_PREF."kv_empl_info e ON e.empl_id = j.empl_id ";
	
	if($dept_id != '') {
		 $sql .= "WHERE e.status = 1 AND j.department = ".db_escape($dept_id)." ORDER BY j.empl_id ASC";
	}
        
     
	 
	$result = db_query($sql, "could not get sales type");

	while($get_des=db_fetch($result))
		$empl_id_list[]=$get_des['empl_id'];
		
	return $empl_id_list;
}
function get_empl_ids_from_dept_id_by_empl_id($dept_id,$empl_id) {
//echo $empl_id;
	$empl_id_list=array();
	$sql= "SELECT j.empl_id FROM ".TB_PREF."kv_empl_job j INNER JOIN ".TB_PREF."kv_empl_info e ON e.empl_id = j.empl_id where j.empl_id =".db_escape($empl_id);
	
	if($dept_id != '') {
		 $sql .= " AND e.status = 1 AND j.department = ".db_escape($dept_id)." ORDER BY j.empl_id ASC";
	}
        
     
	 
	$result = db_query($sql, "could not get sales type");
        $get_des=db_fetch($result);
        $empl_id_list[] =$get_des['empl_id'];
return $empl_id_list;
	while($get_des=db_fetch($result))
		$empl_id_list[]=$get_des['empl_id'];
		
        
         
	return $empl_id_list;
}


?>