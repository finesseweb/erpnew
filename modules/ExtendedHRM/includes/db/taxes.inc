<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

//--------------------------------------------------------------------------------------
function kv_add_tax($year, $min_sal, $max_sal, $percentage, $offset){
	$sql = "INSERT INTO ".TB_PREF."kv_hrm_tax (year, min_sal, max_sal, percentage, offset)  VALUES (".db_escape($year).", ".db_escape($min_sal).", ".db_escape($max_sal).", ".db_escape($percentage).", ".db_escape($offset)." )";
	return db_query($sql, "could not add bonus type");		
}

function kv_update_tax($id, $year, $min_sal, $max_sal, $percentage, $offset){

	$sql = "UPDATE ".TB_PREF."kv_hrm_tax SET year = ".db_escape($year).", min_sal =".db_escape($min_sal).", max_sal=".db_escape($max_sal).", percentage=".db_escape($percentage).", offset=".db_escape($offset)." WHERE id = ".db_escape($id);
	
	db_query($sql, "could not update bonus type");			
}

function kv_get_taxes($year){
	$sql = "SELECT * FROM ".TB_PREF."kv_hrm_tax WHERE year=".db_escape($year);
	
	$result = db_query($sql, "could not get Tax type");
	
	return $result;
}

function kv_get_tax($id){
	$sql = "SELECT * FROM ".TB_PREF."kv_hrm_tax WHERE id=".db_escape($id);
	
	$result = db_query($sql, "could not get Tax type");
	
	return db_fetch($result);
}

function kv_delete_tax($id){
	$sql="DELETE FROM ".TB_PREF."kv_hrm_tax WHERE id=".db_escape($id);
	db_query($sql,"The Tax type record could not be deleted");	
}

function kv_get_tax_value($gross_pay, $year){
	$sql = "SELECT * FROM ".TB_PREF."kv_hrm_tax WHERE min_sal <= ".db_escape($gross_pay)." AND max_sal >= ".db_escape($gross_pay)." AND year=".db_escape($year)." LIMIT 1" ;

	$result = db_query($sql, "could not get Tax type");
	
	return db_fetch($result);
}

function kv_get_tax_for_an_employee($empl_id, $year){
	$gross_pay_month =  get_empl_gross_pay($empl_id);
	$tax_row = kv_get_tax_value($gross_pay_month, $year);

	$tax_val = ($gross_pay_month - $tax_row['min_sal'])*($tax_row['percentage']/100)+$tax_row['offset'];
	return round($tax_val);
}


//---------------------------------------------------------------------------
function kv_check_payroll_table_exist(){
	$table_exist = db_query("SHOW TABLES LIKE '".TB_PREF."kv_empl_job'");
    $tbl_ext = db_num_rows($table_exist) > 0;
    if($tbl_ext){
    	return true; 
    }else{
    	$table_exist = db_query("SHOW TABLES LIKE '".TB_PREF."kv_empl_salary'");
	    $tbl_ext = db_num_rows($table_exist) > 0;
	    if($tbl_ext){
	    	return true; 
	    }else
	    	return false;
    }
    return false;
}
function get_allowances($value=null, $inactive=0){

	$sql = "SELECT * FROM ".TB_PREF."kv_allowances WHERE inactive={$inactive}";

	if($value != null)
		$sql .= " AND type=".db_escape($value); 

	$sql .= " ORDER BY id";
	return db_query($sql,"could not get kv_departments");
}

function get_empl_info(array $data,$inactive=1){
    
	$sql = "SELECT ".$data['select_column']." FROM ".TB_PREF."kv_empl_info WHERE ". $data['column_name']. "=".db_escape($data['match'])." AND status={$inactive}";

	
	$result =  db_query($sql,"could not get kv_departments");
        return db_fetch_row($result);
  
}

//==============================leave encashment queries

function getEmplEncashmentRequestInfo($empl_id){
    $sql = "Select * from ".TB_PREF."kv_encashment_request where status = 2 AND is_paid = 0 AND empl_id =".db_escape($empl_id);
    return db_query($sql,'cannot abole to get the records');
   
    
}


function update_encashment_request($empl_id){
    $sql = "update ".TB_PREF."kv_encashment_request set is_paid = 1 where status = 2 AND is_paid = 0 AND empl_id =".db_escape($empl_id);
    return db_query($sql, 'cannot be able to update');
}




function get_person_id($empl_id,$month, $year){
    $sql = "SELECT id FROM ".TB_PREF."kv_empl_salary WHERE empl_id='".$empl_id."' AND month = $month AND year = $year";
   // echo $sql;
	$result = db_query($sql,"could not get kv_departments");
        $row = db_fetch($result);
	return $row[0];
}


function get_person_hra($empl_id,$month, $year){
        $sql = "SELECT accom_hra_by_org FROM ".TB_PREF."kv_empl_salary WHERE accom_hra_by_org !=1 AND empl_id='".$empl_id."' AND month = $month AND year = $year";
        //display_error($sql);
	$result = db_query($sql,"could not get kv_departments");
        $row = db_fetch($result);
	return $row[0];
}


function get_nps($id,$account){

	$sql = "SELECT amount FROM ".TB_PREF."gl_trans WHERE type_no = $id AND account = $account";
        $result = db_query($sql,"could not get kv_departments");
        $row = db_fetch($result);
        
	return $row[0];
}





function get_all_allowances(){
	$sql = "SELECT * FROM ".TB_PREF."kv_allowances ORDER BY description";
	return db_query($sql,"could not get kv_departments");
}
function get_all_tax_setup(){
	$sql = "SELECT * FROM tax_setup ORDER BY description";
	return db_query($sql,"could not get kv_departments");
}

function get_all_hrm_finance_setting(){
	$sql = "SELECT * FROM ".TB_PREF."kv_hrm_finance_setup ORDER BY description";
	return db_query($sql,"could not get kv_departments");
}

function kv_get_sal_details_file($month, $year){
		$Allowance = get_all_allowances();
		$sql0 = "SELECT SUM(`gross`) AS `gross_sum`, SUM(`net_pay`) AS `net_pay_sum`, SUM(`loan`) AS `loan_sum`, SUM(`lop_amount`) AS `lop_amount_sum`, SUM(`ot_other_allowance`) AS `ot_other_allowance_sum`, SUM(`misc`) AS `misc_sum`, ";
		while ($single = db_fetch($Allowance)) {	
			$sql0 .= "SUM(`{$single['id']}`) AS `{$single['id']}_sum`," ;
		}
		$sql = substr($sql0 , 0, -1);
		$sql .=" FROM ".TB_PREF."kv_empl_salary	WHERE month=".db_escape($month)." AND year=".db_escape($year);

		return db_query($sql,"No transactions were returned");
	}

function kv_get_sal_details_Annual($year){
		$Allowance = get_all_allowances();
		$sql0 = "SELECT SUM(`gross`) AS `gross_sum`, SUM(`net_pay`) AS `net_pay_sum`, SUM(`loan`) AS `loan_sum`, SUM(`lop_amount`) AS `lop_amount_sum`, SUM(`ot_other_allowance`) AS `ot_other_allowance_sum`, SUM(`misc`) AS `misc_sum`, ";
		while ($single = db_fetch($Allowance)) {	
			$sql0 .= "SUM(`{$single['id']}`) AS `{$single['id']}_sum`," ;
		}
		$sql = substr($sql0 , 0, -1);
		$sql .=" FROM ".TB_PREF."kv_empl_salary	WHERE year=".db_escape($year);

		return db_query($sql,"No transactions were returned");
	}

function get_all_allowances_count($value=null){
	$sql = "SELECT COUNT(*) FROM ".TB_PREF."kv_allowances ";

	if($value != null)
		$sql .= "WHERE type=".db_escape($value); 

	$sql .= " ORDER BY description";
	$result = db_query($sql,"could not get kv_departments");
	$row = db_fetch($result);
	return $row[0];
}
function get_allowances_count($value=null, $inactive=0){
	$sql = "SELECT COUNT(*) FROM ".TB_PREF."kv_allowances WHERE inactive={$inactive} ";

	if($value != null)
		$sql .= "AND type=".db_escape($value); 

	$sql .= " ORDER BY description";
	$result =  db_query($sql,"could not get kv_departments");
	$row = db_fetch($result);
	return $row[0];
}


function getSalaryPayable($type = 99, $type_no,$account){
    $sql = "SELECT amount  FROM ".TB_PREF."gl_trans where type = $type AND type_no = $type_no AND account = ". db_escape($account);
   // display_error($sql);
    
   $result = db_query($sql,"could not get kv_departments");
   $row = db_fetch($result);
	return $row[0];
}
function getGlTrans($type = 99,$type_no){

    $sql = "SELECT *  FROM ".TB_PREF."gl_trans where type = $type AND type_no = $type_no";
    
    return db_query($sql,"could not get kv_departments");
}


function getMonthAndYear($id){
    
    $sql = "SELECT month,year,empl_id  FROM ".TB_PREF."kv_empl_salary where id = $id" ;
     $result =  db_query($sql,"could not get kv_departments");
     
     $data = array();
     while($row = db_fetch($result)){
     $data['empl_id'] = $row['empl_id'];
     $data['month'] = $row['month'];
     $data['year'] = $row['year'];
     }
     return $data;
    
}


function getDeptId($id){
    
     $sql = "SELECT department  FROM ".TB_PREF."kv_empl_job where empl_id = ". db_escape($id)." LIMIT 1" ;
     $result =  db_query($sql,"could not get kv_departments");
     $row = db_fetch($result);
     return $row[0];
     
}


function getGlTransdistinct($type = 99, $date1){
    
    //$due_date = explode('/',$date1);
	//$date1 = $due_date[2].'-'.$due_date[0].'-'.$due_date[1];
    $sql = "SELECT distinct(`type_no`) FROM ".TB_PREF."gl_trans where type = $type AND tran_date = ". db_escape($date1);
    
    return db_query($sql,"could not get kv_departments");
}
function getSalEmployees($year, $month){

    $sql = "SELECT *  FROM ".TB_PREF."kv_empl_salary where year = $year AND month = $month ";
    
    $result =  db_query($sql,"could not get kv_departments");
    $data = array();
    while($row  = db_fetch($result));
        {
            $data['id'][] = $row['id'];
            $data['empl_id'][] =$row['empl_id'];
    }
    
    display_error(count($data));
}



function add_allowance($description, $type, $value, $basic, $percentage, $Tax, $status=0){
	if($value == 'Amount')
		$percentage = 0;
		
	if($basic == 1){
		$basic_id = kv_get_basic();
		if($basic_id > 0){
			$sql = "UPDATE ".TB_PREF."kv_allowances SET basic=0 WHERE id = ".db_escape($basic_id);
   			db_query($sql,"The sales department could not be updated");
		}			
	}	

	if($Tax == 1){
		$Tax_id = kv_get_Taxable_field();
		if($Tax_id > 0){
			$sql = "UPDATE ".TB_PREF."kv_allowances SET Tax=0 WHERE id = ".db_escape($Tax_id);
   			db_query($sql,"The sales department could not be updated");
		}			
	}	

	$sql = "INSERT INTO ".TB_PREF."kv_allowances (description, type, value, basic, percentage, Tax, inactive) VALUES (".db_escape($description) . ", ".db_escape($type)." , ".db_escape($value).", ".db_escape($basic).", ".db_escape($percentage).", ".db_escape($Tax).", ".db_escape($status).")";
   	db_query($sql,"The sales department could not be added");
}



function add_hrm_finance($description, $type,$allowance_credit_gl_code,$allowance_debit_gl_code,$status=0){
	$sql = "INSERT INTO ".TB_PREF."kv_hrm_finance_setup (description, type,allowance_credit_gl_code,allowance_debit_gl_code,inactive) VALUES (".db_escape($description) . ", ".db_escape($type).", ".db_escape($allowance_credit_gl_code).", ".db_escape($allowance_debit_gl_code).",".db_escape($status).")";
   	db_query($sql,"The sales department could not be added");
}
function add_tax_setup($description, $type,$allowance_credit_gl_code,$allowance_debit_gl_code,$status=0){
	$sql = "INSERT INTO tax_setup (description, type,allowance_credit_gl_code,allowance_debit_gl_code,inactive) VALUES (".db_escape($description) . ", ".db_escape($type).", ".db_escape($allowance_credit_gl_code).", ".db_escape($allowance_debit_gl_code).",".db_escape($status).")";
   	db_query($sql,"The sales department could not be added");
}


function update_allowance($selected_id, $description, $type, $value, $basic, $percentage, $Tax, $status=0){
  // display_error('hello');
	if($value== 'Amount')
		$percentage = 0;

	if($basic == 1){
		$basic_id = kv_get_basic();
		if($basic_id > 0){
			$sql = "UPDATE ".TB_PREF."kv_allowances SET basic=0 WHERE id = ".db_escape($basic_id);
   			db_query($sql,"The sales department could not be updated");
		}			
	}	

	if($Tax == 1){
		$Tax_id = kv_get_Taxable_field();
		if($Tax_id > 0){
			$sql = "UPDATE ".TB_PREF."kv_allowances SET Tax=0 WHERE id = ".db_escape($Tax_id);
   			db_query($sql,"The sales department could not be updated");
		}			
	}	

	$sql = "UPDATE ".TB_PREF."kv_allowances SET description=".db_escape($description)." , type=".db_escape($type)." , value=".db_escape($value).", basic=".db_escape($basic).", percentage=".db_escape($percentage).", Tax=".db_escape($Tax).", inactive=".db_escape($status)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The sales department could not be updated");
        if($selected_id == 1){
        if($value != 'Amount')
           update_ctc_heads(0);
        else
            update_ctc_heads(1);
        }
}


function update_ctc_heads($status){
    	$sql = "UPDATE ".TB_PREF."kv_allowances SET inactive=".db_escape($status)." WHERE id in (39,40,43,44,45,46,47,49,50,51)";
   	db_query($sql,"The sales department could not be updated");
        
        if($status == 0)
            $status = 1;
                else
            $status = 0;
        
        $sql = "UPDATE ".TB_PREF."kv_allowances SET inactive=".db_escape($status)." WHERE id in (3,2)";
   	db_query($sql,"The sales department could not be updated");
        
}





function update_hrm_finance($selected_id, $description, $type,$allowance_credit_gl_code,$allowance_debit_gl_code,$status=0){
	

	$sql = "UPDATE ".TB_PREF."kv_hrm_finance_setup SET description=".db_escape($description)." , type=".db_escape($type)." , allowance_credit_gl_code=".db_escape($allowance_credit_gl_code).", allowance_debit_gl_code=".db_escape($allowance_debit_gl_code).",inactive=".db_escape($status)." WHERE id = ".db_escape($selected_id);
        
   	db_query($sql,"The sales department could not be updated");
}


function update_tax_setup($selected_id, $description, $type,$allowance_credit_gl_code,$allowance_debit_gl_code,$status=0){
	

	$sql = "UPDATE tax_setup SET description=".db_escape($description)." , type=".db_escape($type)." , allowance_credit_gl_code=".db_escape($allowance_credit_gl_code).", allowance_debit_gl_code=".db_escape($allowance_debit_gl_code).",inactive=".db_escape($status)." WHERE id = ".db_escape($selected_id);
        
   	db_query($sql,"The sales department could not be updated");
}
function db_has_basic_pay(){
	$sql = "SELECT id FROM ".TB_PREF."kv_allowances WHERE basic = 1";
	$result = db_query($sql, "could not get department");
	$exists = (db_num_rows($result))?TRUE:FALSE;
	if($exists) 
		return true;
	else
		return false;
}
function kv_get_basic(){
	$sql = "SELECT id FROM ".TB_PREF."kv_allowances WHERE basic = 1";
	$result = db_query($sql, "could not get department");
	$row = db_fetch($result);
       
	return $row[0];
}
function kv_get_sal($empl_id){
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id ='".$empl_id."'";
       
	$result = db_query($sql, "could not get department");
	$row = db_fetch($result);
	return $row;
}

function kv_get_basics(){
	$sql = "SELECT GROUP_CONCAT(id) as id FROM ".TB_PREF."kv_allowances WHERE basic = 1 GROUP BY basic";
	$result = db_query($sql, "could not get department");
	$row = db_fetch($result);
	
	return $row[0];

}
function db_has_tax_pay(){
	$sql = "SELECT id FROM ".TB_PREF."kv_allowances WHERE Tax = 1";
	$result = db_query($sql, "could not get department");
	$exists = (db_num_rows($result))?TRUE:FALSE;
	if($exists) 
		return true;
	else
		return false;
}
function kv_get_Tax_allowance(){
	$sql = "SELECT id FROM ".TB_PREF."kv_allowances WHERE Tax = 1";
	$result = db_query($sql, "could not get department");
	$row = db_fetch($result);
	return $row[0];
}

function kv_get_Taxable_field(){
	$sql = "SELECT id FROM ".TB_PREF."kv_allowances WHERE Tax = 1";
	$result = db_query($sql, "could not get department");
	$row = db_fetch($result);
	return $row[0];
}

function delete_allowance($selected_id){
	$sql="DELETE FROM ".TB_PREF."kv_allowances WHERE id=".db_escape($selected_id);	
	$result = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_salary` LIKE '{$selected_id}'", "Can't Query this Table");
	$exists = (db_num_rows($result))?TRUE:FALSE;
	if($exists) {
		$sql1="ALTER TABLE ".TB_PREF."kv_empl_salary DROP COLUMN `{$selected_id}`";
		db_query($sql1,"could not delete sales department");
	}
	$result = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_job` LIKE '{$selected_id}'", "Can't Query this Table");
	$exists = (db_num_rows($result))?TRUE:FALSE;
	if($exists) {
		$sql1="ALTER TABLE ".TB_PREF."kv_empl_job DROP COLUMN `{$selected_id}`";
		db_query($sql1,"could not delete sales department");
	}
	db_query($sql,"could not delete sales department");
}


function get_allowance($selected_id){
	$sql = "SELECT * FROM ".TB_PREF."kv_allowances WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}
function get_hrm_finance_setup($selected_id){
	$sql = "SELECT * FROM ".TB_PREF."kv_hrm_finance_setup WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}

function get_allowance_name($id){
	$sql = "SELECT description FROM ".TB_PREF."kv_allowances WHERE id = ".db_escape($id);
	$result = db_query($sql, "could not get department");
	$row = db_fetch($result);
	return $row[0];
}

//--------------------------------------------------------------------------------
/*   Earnings Deductions Drop-down   */ 

function earning_deductions_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false, $disabled= false){
	$items = array();
	$items['Earnings'] = strlen($name_no) ? $name_no : _("Earnings");
	$items['Deductions'] = strlen($name_yes) ? $name_yes : _("Deductions");
        $items['Tax-E'] = strlen($name_yes) ? $name_yes : _("Tax-E");
        $items['Tax-D'] = strlen($name_yes) ? $name_yes : _("Tax-D");

	return array_selector($name, $selected_id, $items, 
		array( 
			'select_submit'=> $submit_on_change,
			'async' => false,
			'disabled' => $disabled ) ); // FIX?
}

function earning_deductions_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false, $disabled=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo earning_deductions_list($name, $selected_id, $name_yes, $name_no, $submit_on_change, $disabled);
	echo "</td>\n";
}

function earning_deductions_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false, $disabled=false){
	echo "<tr><td class='label'>$label</td>";
	earning_deductions_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change, $disabled);
	echo "</tr>\n";
}
function earning_deductions_list1($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false, $disabled= false,$tax=false){
	$items = array();
        if($tax=='1'){
        $items['Tax-type'] = strlen($_POST['type']) ? $_POST['type'] : _("Tax-type");   
        } else {
        $items['Salary'] = strlen($name_no) ? $name_no : _("Salary");    
        }
	
	//$items['Course-Fee'] = strlen($name_yes) ? $name_yes : _("Course-Fee");        
	//$items['Purchase'] = strlen($_POST['Purchase']) ? $_POST['Purchase'] : _("Purchase");    
        

	return array_selector($name, $selected_id, $items, 
		array( 
			'select_submit'=> $submit_on_change,
			'async' => false,
			'disabled' => $disabled ) ); // FIX?
}

function earning_deductions_list_cells1($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false, $disabled=false,$tax=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo earning_deductions_list1($name, $selected_id, $name_yes, $name_no, $submit_on_change, $disabled,$tax);
	echo "</td>\n";
}

function earning_deductions_list_row1($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false, $disabled=false,$tax=false){
	echo "<tr><td class='label'>$label</td>";
	earning_deductions_list_cells1(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change, $disabled,$tax);
	echo "</tr>\n";
}

//--------------------------------------------------------------------------------
/*   Amount Percentage Drop-down   */ 

function percentage_amount_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false, $disabled=false){
	$items = array();
	$items['Amount'] = strlen($name_no) ? $name_no : _("Amount");
	$items['Percentage'] = strlen($name_yes) ? $name_yes : _("Percentage");

	return array_selector($name, $selected_id, $items, 
		array( 
			'select_submit'=> $submit_on_change,
			'async' => false,
			'disabled' => $disabled ) ); // FIX?
}

function percentage_amount_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false, $disabled=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo percentage_amount_list($name, $selected_id, $name_yes, $name_no, $submit_on_change, $disabled);
	echo "</td>\n";
}

function percentage_amount_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false, $disabled=false){
	echo "<tr><td class='label'>$label</td>";
	percentage_amount_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change, $disabled);
	echo "</tr>\n";
}







/*
$allowances_count = array(
  1 => _('One'),
  2 => _('Two'),
  3 => _('Three'),
  4 => _('Four'),
  5 => _('Five'),
  6 => _('Six'),
  7 => _('Seven'),
  8 => _('Eight'),
  9 => _('Nine'),
  10 => _('Ten'),
  0 => _('Custom')
);


//--------------------------------------------------------------------------------------
function allowances_count($label, $name, $selected_id=null, $submit_on_change=false) {
	global $allowances_count;
		
	echo "<td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $allowances_count, $options );
	echo "</td></tr>\n";
}*/


