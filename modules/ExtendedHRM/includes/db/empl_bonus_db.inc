<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

//-----------------------------------------------------------------------------------------------
function add_bonus_type($name, $tax_included, $factor){
	$sql = "INSERT INTO ".TB_PREF."kv_empl_bonus_types (bonus_name, sal_percent, status)  VALUES (".db_escape($name).", ".db_escape($tax_included).", ".db_escape($factor).")";
	return db_query($sql, "could not add bonus type");		
}

function update_bonus_type($id, $name, $tax_included, $factor){

	$sql = "UPDATE ".TB_PREF."kv_empl_bonus_types SET bonus_name = ".db_escape($name).", sal_percent =".db_escape($tax_included).", status=".db_escape($factor)." WHERE id = ".db_escape($id);
	
	db_query($sql, "could not update bonus type");			
}

function get_all_bonus_types(){
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_bonus_types";	
	
	return db_query($sql, "could not get all bonus types");
} 

function get_bonus_type($id){
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_bonus_types WHERE id=".db_escape($id);
	
	$result = db_query($sql, "could not get bonus type");
	
	return db_fetch($result);
}

function get_bonus_name($id){
	$sql = "SELECT bonus_name FROM ".TB_PREF."kv_empl_bonus_types WHERE id=".db_escape($id);
	
	$result = db_query($sql, "could not get bonus type");
	
	$row = db_fetch_row($result);
	return $row[0];
}

function delete_bonus_type($id){
	$sql="DELETE FROM ".TB_PREF."kv_empl_bonus_types WHERE id=".db_escape($id);
	db_query($sql,"The bonus type record could not be deleted");
	
}

//-----------------------------------------------------------------------------------------------
function add_empl_loan($empl_id, $date, $loan_amount, $loan_type_id, $periods, $monthly_pay, $periods_pay,$status){
	$datey = date2sql($date);
	$sql = "INSERT INTO ".TB_PREF."kv_empl_loan (empl_id, date, loan_amount, loan_type_id, periods, monthly_pay, periods_paid, status)  VALUES (".db_escape($empl_id).", ".db_escape($datey).",".db_escape($loan_amount).",  ".db_escape($loan_type_id).",  ".db_escape($periods).",  ".db_escape($monthly_pay).", ".db_escape($periods_pay).", ".db_escape($status).")";
	
	return db_query($sql, "could not add employee loan");		
}

function update_empl_loan($empl_id, $loan_amount, $loan_type_id, $periods, $monthly_pay, $periods_pay){

	$sql = "UPDATE ".TB_PREF."kv_empl_loan SET empl_id = ".db_escape($empl_id).", loan_amount =".db_escape($loan_amount).", loan_type_id=".db_escape($loan_type_id).", periods =".db_escape($periods).", monthly_pay =".db_escape($monthly_pay).", periods_pay =".db_escape($periods_pay)." WHERE id = ".db_escape($id);
	
	db_query($sql, "could not update employee loan");			
}

function get_all_empl_loans(){

	$sql = "SELECT * FROM ".TB_PREF."kv_empl_loan";	
	
	return db_query($sql, "could not get all employee loans");
} 

function get_empl_loan($empl_id){

	$sql = "SELECT * FROM ".TB_PREF."kv_empl_loan WHERE empl_id=".db_escape($empl_id);
	
	$result = db_query($sql, "could not get employee loan");
	
	return db_fetch($result);
}

function get_empl_loan_monthly_payment($id, $date){
	$datey = date2sql($date);
	$sql = "SELECT monthly_pay FROM ".TB_PREF."kv_empl_loan WHERE `periods_paid` < `periods` AND `empl_id`=".db_escape($id)." AND date < ".db_escape($datey)." AND status='Active'";
	
	$result = db_query($sql, "could not get bonus type");
	
	$row = db_fetch_row($result);
	
	//return $date; 
	if(!empty($row[0]))
		return $row[0];
	else
		return 0;
}

function paid_empl_loan_month_payment($id){

	$sql = "SELECT periods_paid, periods FROM ".TB_PREF."kv_empl_loan WHERE empl_id=".db_escape($id);
	
	$result = db_query($sql, "could not get bonus type");
	
	$row = db_fetch_row($result);
	
	$paid_sofar =  $row[0];
	
	$paid_now  =$paid_sofar +1;
	
	if($row[1] == $paid_now)
		$sql = "UPDATE ".TB_PREF."kv_empl_loan SET periods_paid = ".db_escape($paid_now).", status = 'Inactive' WHERE empl_id = ".db_escape($id);
	else
		$sql = "UPDATE ".TB_PREF."kv_empl_loan SET periods_paid = ".db_escape($paid_now)." WHERE empl_id = ".db_escape($id);
	
	db_query($sql, "could not update employee loan");	
}

function get_empl_loan_details_Complete($empl_id){ 
	$loans = GetDataJoin('kv_empl_loan AS loan', array( 
					0 => array('join' => 'INNER', 'table_name' => 'kv_loan_types AS type', 'conditions' => '`type`.`id` = `loan`.`loan_type_id`'),
					1 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `loan`.`empl_id`') ), 
							array('`info`.`empl_id`, `info`.`empl_firstname`, `type`.`loan_name`, `loan`.`loan_amount`, `loan`.`monthly_pay`, `loan`.`periods`, `loan`. `periods_paid`, `loan`. `date`, `loan`.`status`'),
							array('`loan`.`empl_id`' => $empl_id));
	return $loans;
}

function delete_empl_loan($id){

	$sql="DELETE FROM ".TB_PREF."kv_empl_loan WHERE id=".db_escape($id);
	
	db_query($sql,"The bonus type record could not be deleted");
	
}

function db_has_empl_loan($empl_id){

	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_loan WHERE status='Active' AND empl_id=".db_escape($empl_id));
	
}

function db_has_empl_inactive_loan($empl_id){

	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_loan WHERE status='Inactive' AND empl_id=".db_escape($empl_id));
	
}

function db_has_empl_loan_all_status($empl_id){

	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_loan WHERE empl_id=".db_escape($empl_id));
	
}

// Loan types and its supported functions.....


function add_loan_types($loan_name,  $interest_rate) {
		
	$sql = "INSERT INTO ".TB_PREF."kv_loan_types (loan_name,  interest_rate)
		VALUES ("
		  .db_escape($loan_name) . ", "		 
		  .db_escape($interest_rate) 
		.")";

	begin_transaction();
	
	$ret = db_query($sql, "Can't insert employee experience");
	
	commit_transaction();
	return $ret;
}

function update_loan_types($id, $loan_name, $interest_rate) {
	
	$sql = "UPDATE ".TB_PREF."kv_loan_types SET "
		  ."loan_name=".db_escape($loan_name) . ", "		  
		  ."interest_rate=".db_escape($interest_rate) 
		  ."WHERE id = ".db_escape($id);

	begin_transaction();

	$ret = db_query($sql, "Can't update employee experience");
	
	commit_transaction();
	return $ret;
}

function delete_loan_types($person){
	begin_transaction();
	
	$sql = "DELETE FROM ".TB_PREF."kv_loan_types WHERE id=".db_escape($person);
	$ret = db_query($sql, "Can't delete employee experience");

	commit_transaction();
	return $ret;
}

/*
	Retrieve full experience data from database for selected type/action/entity or experience
*/
function get_single_loan_type($id)
{
	$kv_id = (int)$id ;
	$sql = "SELECT * FROM  ".TB_PREF."kv_loan_types WHERE id=".db_escape($kv_id);

	$result = db_query($sql, "Can't get employee  experiences");
	
	$row = db_fetch($result);
	return $row;
}

function get_loan_interest_rate($id){
	$sql = "SELECT interest_rate FROM ".TB_PREF."kv_loan_types WHERE id=".db_escape($id);
	
	$result = db_query($sql, "could not get interest rate");
	
	$row = db_fetch_row($result);
	return $row[0];
}


function get_all_loan_types(){
	
	$sql = "SELECT * FROM "	.TB_PREF."kv_loan_types ";
	
	$ret = db_query($sql, "Can't get employee  experiences");	
	
	return $ret;
}

function kv_loan_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false){
	global $all_items;

	$sql = "SELECT id, loan_name, interest_rate FROM ".TB_PREF."kv_loan_types";

	$mode = 0;

	if ($editkey)
		set_editor('employee', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'id', 'loan_name',
	array(
	   'format' => '_format_empl_loan',
	    'order' => array('loan_name'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("All Employee") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new employee') :	_('Select employee'),
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('employee');
	return $ret;
}


function _format_empl_loan($row){
	return (user_show_codes() ?  ($row[2] . "% &nbsp;-&nbsp;") : "") . $row[1];
}


function kv_loan_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo kv_loan_list($name, $selected_id, $all_option, $submit_on_change,	$show_inactive, $editkey);
	echo "</td>\n";
}

function kv_loan_list_row($label, $name, $selected_id=null, $all_option = false, $submit_on_change=false, $show_inactive=false, $editkey = false){
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo kv_loan_list_cells($name, $selected_id, $all_option, $submit_on_change,	$show_inactive, $editkey);
	echo "</td>\n</tr>\n";
}

?>