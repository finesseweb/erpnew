<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
//-------------------------------------------------------------------------------------
function add_cv($id, $empl_firstname, $cv_title, $filename,$unique_name,$f_year) {
	
	$date = date('Y-m-d H:i:s');
		$sql = "INSERT INTO ".TB_PREF."kv_empl_cv (empl_id, empl_firstname, cv_title, filename,unique_name, uploaded_date,f_year) VALUES ("
				.db_escape($id).", "
				.db_escape($empl_firstname).", "		
				.db_escape($cv_title).","
				.db_escape($filename).","
				.db_escape($unique_name).","
                                .db_escape($date).","
                                .db_escape($f_year).")";
	
	db_query($sql,"The CV could not be updated");
}

//-----------------------------------------Update CV-------------------------------------
function update_cv($id,$empl_id, $empl_firstname, $cv_title, $filename,$unique_name,$f_year) {
	//display_error($unique_name);
        $date = date('Y-m-d H:i:s');
	$test_sql = db_query("SELECT * FROM ".TB_PREF."kv_empl_cv WHERE id=".db_escape($id));	
	if(db_num_rows($test_sql) > 0){
		$sql = "UPDATE ".TB_PREF."kv_empl_cv SET		
				empl_id=".db_escape($empl_id).",
				empl_firstname=".db_escape($empl_firstname).",		
				cv_title=".db_escape($cv_title).",
				filename=".db_escape($filename).",
				unique_name=".db_escape($unique_name).",
                                last_updated=".db_escape($date).",
                                f_year=".db_escape($f_year).",
				WHERE id=".db_escape($id);
	}
  //  display_error($sql);
	db_query($sql,"The CV could not be updated");
}

//-----------------------------------------------------------------------------------------------
function delete_cv($id) {
	$sql="DELETE FROM ".TB_PREF."kv_empl_cv WHERE id=".db_escape($id);
	db_query($sql, "could not delete the selected Employee");
}

//----------------------------------------------------------------------------------------
function kv_hrm_uploads_list(){
	return "SELECT empl_id, empl_firstname, cv_title FROM ".TB_PREF."kv_empl_cv";
}
 


//---------------------------------------------------------------------------------------
function get_employee_cv($employee_id){
		
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_cv WHERE empl_id=".db_escape($employee_id);
	$result = db_query($sql, "could not get employee");
	return db_fetch($result);
}

function get_emp_cv_documents($employee_id){
		
	$sql = "SELECT id,empl_id,empl_firstname,cv_title FROM ".TB_PREF."kv_empl_cv WHERE empl_id=".db_escape($employee_id);
	
	return $sql;
}

function get_employee_cvdwnld($emplcv_id){
		
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_cv WHERE id=".db_escape($emplcv_id);
	//display_error($sql);
	$result = db_query($sql, "could not get employee");
	return db_fetch($result);
}
?>