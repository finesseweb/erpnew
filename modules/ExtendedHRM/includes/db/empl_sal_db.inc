<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

//-----------------------------------------------------------------------------
function get_empl_sal_details($id, $month, $yr,$is_arrear = 0){
    
    if($is_arrear == 1){
        if(db_has_employee_arrear($id, $month, $yr)){
        $sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($id)." AND month=".db_escape($month)." AND year=".db_escape($yr)." AND is_arrear = 1;";
        }
        else{
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($id);
	}
    }
    else{
        if(db_has_employee_payslip($id, $month, $yr)){
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($id)." AND month=".db_escape($month)." AND year=".db_escape($yr)." AND is_arrear IS NULL";
	}else{
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($id);
	}
    }

    /*
	if(db_has_employee_payslip($id, $month, $yr)){
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($id)." AND month=".db_escape($month)." AND year=".db_escape($yr);
	}else{
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($id);
	}
        */
	//display_error($sql);
	$result = db_query($sql, "could not get bonus type");
	
	return db_fetch($result);
}
function get_empl_sal_detail2($id, $month, $yr,$is_arrear, $slip_id){
    
    if($is_arrear == 1){
        if(db_has_employee_arrear($id, $month, $yr)){
        $sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE id=".db_escape($slip_id)." AND is_arrear = 1;";
        }
        else{
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($id);
	}
    }
    else{
        if(db_has_employee_payslip($id, $month, $yr)){
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($id)." AND month=".db_escape($month)." AND year=".db_escape($yr)." AND is_arrear IS NULL";
	}else{
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($id);
	}
    }
    $result = db_query($sql, "could not get bonus type");
	
	return db_fetch($result);
}



function get_empl_sal_detail0($id, $month, $yr,$is_arrear, $slip_id){
    
   
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($id);
	
    $result = db_query($sql, "could not get bonus type");
	
	return db_fetch($result);
}




function get_empl_sal_details_arrear($id, $month, $yr, $is_arrear){    
    
        if($is_arrear == 1){
            if(db_has_employee_arrear($id, $month, $yr)){
                    $sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($id)." AND month=".db_escape($month)." AND year=".db_escape($yr)." AND is_arrear = 1";
            }
            else{
                    $sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($id);
            }
        }
        else{
            if(db_has_employee_payslip($id, $month, $yr)){
                    $sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($id)." AND month=".db_escape($month)." AND year=".db_escape($yr)." AND is_arrear != 1";
            }else{
                    $sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($id);
            }
        }
        echo $sql;
	//display_error($sql);
	$result = db_query($sql, "could not get bonus type");
	
	return db_fetch($result);
}

function kv_get_allowance_transactions($rep, $month, $year){

	$sql = "SELECT id, date, empl_id, `{$rep}` FROM ".TB_PREF."kv_empl_salary WHERE month=".db_escape($month)." AND year=".db_escape($year);
	
	return $result = db_query($sql, "could not get bonus type");
	
	//return db_fetch($result);
}

function db_has_employee_payslip($empl_id, $month, $year){

	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($empl_id)." AND month=".db_escape($month)." AND year=".db_escape($year)." AND is_arrear IS NULL");
}
function db_has_employee_payslip1($empl_id, $month, $year){

	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($empl_id)." AND month=".db_escape($month)." AND year=".db_escape($year)." AND is_arrear = 1");
}

function db_has_employee_arrear($empl_id, $month, $year){

	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($empl_id)." AND month=".db_escape($month)." AND year=".db_escape($year)." AND is_arrear = 1");
}
function db_has_some_entry_to_this_allowance($rep, $month, $year){

	return check_empty_result("SELECT IF(`{$rep}` >0, 1, 0) FROM ".TB_PREF."kv_empl_salary WHERE month=".db_escape($month)." AND year=".db_escape($year));
}

function db_has_sal_for_selected_dept($dept_id, $month, $year){

	$sql = "SELECT empl_id FROM ".TB_PREF."kv_empl_salary WHERE month=".db_escape($month)." AND year=".db_escape($year);

	$salaried_employes = $selected_empl_list= array();
	
	$ret = db_query($sql, "Can't get empl attendance");
	
	while($cont = db_fetch($ret)) 
		$salaried_employes[] = $cont;
	$salaried_employees = array_values($salaried_employes);

	$get_employees_list = get_empl_ids_from_dept_id($dept_id);

	display_notification(json_encode($salaried_employees).'---'. json_encode($get_employees_list));
	
	if(empty($salaried_employees) || empty($get_employees_list)){
		return 0;
	}else{
		$selected_empl_list = array_intersect($salaried_employees, $get_employees_list);

		if(empty($selected_empl_list)){
			return 0;
		}else {
			return $selected_empl_list;
		}
	}
	
}

function get_empl_name_dept($id) {

	$sql = "SELECT CONCAT_WS(' ', empl.empl_firstname, empl.empl_lastname) as name, dept.department as deptment, empl.email FROM ".TB_PREF."kv_empl_info as empl, ".TB_PREF."kv_empl_job as dept WHERE empl.empl_id=".db_escape($id)." AND dept.empl_id=".db_escape($id);
	
	$result = db_query($sql, "could not get bonus type");
	
	return db_fetch($result);

}

function get_empl_gross_pay($id) {
	$sql = "SELECT	gross FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($id)."LIMIT 1";

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);

	return $row[0];
}

function add_empl_salary($empl_id, $month, $year, $gross, $basic, $da, $hra, $convey_allow, $medical, $edu_other_allow, $lop_amount, $pf, $loan, $adv_Sal, $net_pay, $prof_tax, $empl_esi, $misc, $ot_other_allowance){
	$dat_of_pay = date2sql(Today());

	$sql = "INSERT INTO ".TB_PREF."kv_empl_salary (empl_id, month, year, gross, basic, empl_da, empl_hra,conveyance, medical_allowance, edu_other, lop_amount, empl_pf, loan, adv_sal, net_pay, prof_tax, empl_esi, misc, date, ot_other_allowance) VALUES ("
		.db_escape($empl_id).", "
		.db_escape($month).", "
		.db_escape($year).", "
		.db_escape($gross).", "
		.db_escape($basic).", "
		.db_escape($da).", "
		.db_escape($hra).", "
		.db_escape($convey_allow).", "
		.db_escape($medical).", "
		.db_escape($edu_other_allow).", "
		.db_escape($lop_amount).", "		
		.db_escape($pf).", " 
		.db_escape($loan).", " 
		.db_escape($adv_Sal).", "  
		.db_escape($net_pay).", "  
		.db_escape($prof_tax).", "  
		.db_escape($empl_esi).", "  
		.db_escape($misc).", "  
		.db_escape($dat_of_pay).", "
		.db_escape($ot_other_allowance).")";

	db_query($sql,"The employee could not be added");
	return  db_insert_id(); 
}


function update_empl_salary($empl_id, $month, $year, $gross, $basic, $da, $hra, $convey_allow, $edu_other_allow, $lop_amount, $pf, $loan, $adv_Sal, $net_pay){
	
		$sql = "UPDATE ".TB_PREF."kv_empl_salary SET
		empl_id=".db_escape($empl_id).",
		month=".db_escape($month).",
		year=".db_escape($year).",
		gross=".db_escape($gross).",
		basic=".db_escape($basic).",
		da=".db_escape($da).",
		hra=".db_escape($hra).",
		convey_allow=".db_escape($convey_allow).",
		edu_other_allow=".db_escape($edu_other_allow).",
		lop_amount=".db_escape($lop_amount).",
		pf=".db_escape($pf).",
		loan=".db_escape($loan).",
		adv_sal=".db_escape($adv_Sal).",
		net_pay=".db_escape($net_pay)."
		WHERE empl_id=".db_escape($empl_id)." AND month=".db_escape($month)." AND year =".db_escape($year);

	db_query($sql,"The employee could not be added");
}


//--------------------------------------------------------------------------------------------
function get_emply_salary($id) {

	$sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE  empl_id=".db_escape($id);
      
	$salaries = array();
	$ret = db_query($sql, "Can't get empl attendance");
	
	while($cont = db_fetch($ret)) 
		$salaries[] = $cont;
	return $salaries;
}

function db_has_sal_for_selected_Date($empl_id, $month, $year){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($empl_id)." AND month=".db_escape($month)." AND year=".db_escape($year));
}

function get_employee_sal_history($empl_id , $year) { 
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($empl_id)." AND year=".db_escape($year);
	
	$result = db_query($sql, "could not get bonus type");
        
        
	
	return db_fetch($result);

} 
function get_employee_sal_history_year($empl_id , $year) { 
   
    $salaries = array();
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($empl_id)." AND year=".db_escape($year);
	
	$result = db_query($sql, "could not get bonus type");
	while($cont = db_fetch($result)) 
		$salaries[] = $cont;
	return $salaries;

}

function get_gross_pay_of_an_employee($empl_id){

	
	$sql = "SELECT 	gross FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($empl_id)."LIMIT 1";

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);

	if(!empty($row[0]))
		return $row[0];
	else
		return 0;
}

function get_employee_net_pay($empl_id){
	
	$Allowance = get_allowances('Deductions');
	$sum = '';
	while ($single = db_fetch($Allowance)) {	
		$sum .=$single['id']."+";
	}
	$summ = substr($sum, 0, -1);

	$sql = "SELECT SUM(".$summ.") FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($empl_id)."LIMIT 1";

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);

	if(!empty($row[0]))
		$othr_ded =  $row[0];
	else
		$othr_ded = 0;

	return get_gross_pay_of_an_employee($empl_id) - (get_empl_loan_monthly_payment($empl_id, Today()) + $othr_ded);
}
?>