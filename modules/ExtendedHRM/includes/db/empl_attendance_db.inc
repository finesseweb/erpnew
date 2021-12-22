<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

function db_has_day_attendancee($empl_id, $month, $year){
	$sql = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_attendancee WHERE month=".db_escape($month)." AND empl_id=".db_escape($empl_id)." AND year=".db_escape($year);
	return check_empty_result($sql);
}


function add_employee_attendance($empl_attend, $empl_id, $month, $year, $day,$dept_id){
	$dayy = (int)$day;
	$sql = "INSERT INTO ".TB_PREF."kv_empl_attendancee (empl_id, month, year, `{$dayy}`,dept_id,cal_year) VALUES (".db_escape($empl_id).", ".db_escape($month).", ".db_escape($year).", ".db_escape($empl_attend).",".db_escape($dept_id).",'".date("Y")."')";
        db_query($sql, "The employee could not be added");
}


//--------------------------------------------------------------------------------------------
function update_employee_attendance($empl_attend, $empl_id, $month, $year, $day){
       
	$dayy = (int)$day;
	$sql = "UPDATE ".TB_PREF."kv_empl_attendancee SET `{$dayy}` =".db_escape($empl_attend).",cal_year = '".date("Y")."' WHERE month=".db_escape($month)." AND empl_id=".db_escape($empl_id)." AND year=".db_escape($year);

	db_query($sql,"The employee could not be updated");

}


//--------------------------------------------------------------------------------------------
function get_employees_attendances($attendance_date, $dept_id){
	$month = date("m", strtotime($attendance_date));
	$day = date("d", strtotime($attendance_date));
	$year = get_fiscal_year_id_from_date($attendance_date);

	$degrees = array();
	$dayy = (int)$day;
	$sql = "SELECT empl_id, `{$dayy}` FROM ".TB_PREF."kv_empl_attendancee WHERE year=".(db_escape($year))." AND month=".(db_escape($month))." AND dept_id=".(db_escape($dept_id));
	
	$ret = db_query($sql, "could not retrieve security roles");
	while($get_des=db_fetch($ret)){
		$degrees[$get_des['empl_id']] = $get_des[$dayy];	
	}
	return $degrees;
}
function get_employees_monthly_attendances($dept_id,$empl_id,$attendance_date){

	$month = date("m", strtotime($attendance_date));
	$year = get_fiscal_year_id_from_date($attendance_date);
	$degrees = array();
	
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_attendancee WHERE month=".db_escape($month)." AND dept_id = ".db_escape($dept_id)." AND empl_id=".db_escape($empl_id)." AND year=".db_escape($year)."";
	
	$res = db_query($sql,"could not retrieve attendance data");
	
	$result = db_fetch($res);
	
	return $result;

}
//--------------------------------------------------------------------------------------------
function get_fiscal_year_id_from_date($date){
	$attendance_date = strtotime($date);
	$all_fiscal_years  = get_all_fiscalyears();
		
	while($get_des=db_fetch($all_fiscal_years)){
		$begin = strtotime($get_des['begin']); 
		$end = strtotime($get_des['end']); 
		if( $begin <= $attendance_date && $end >= $attendance_date){
			$year = $get_des['id'];
			break;
		}
	}
	return $year; 
}

function get_dep_employees_count($dep_id) {

	
		$sql= "SELECT COUNT(job.empl_id) FROM ".TB_PREF."kv_empl_job as job LEFT JOIN ".TB_PREF."kv_empl_info as empl_info ON empl_info.empl_id = job.empl_id WHERE job.department=".db_escape($dep_id)." AND (empl_info.status = '1' OR empl_info.status = '2') " ; 
		$result = db_query($sql, "could not get department employee count");

		$get_des=db_fetch($result);
	
	return $get_des[0]; 

} 


//---------------------------------------------------------------------------------
function get_employee_whole_attendance($empl_id,$f_year = ''){
    
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_attendancee WHERE  empl_id=".db_escape($empl_id);
        
        if(!empty($f_year))
            $sql.=" AND year=".db_escape($f_year);

	$attendance = array();
	$ret = db_query($sql, "Can't get empl attendance");
	
	while($cont = db_fetch($ret)) 
		$attendance[] = $cont;
	return $attendance;
}



/*old functions*/

function add_employees_attendance($a_date, $employees){
	 	//display_error(json_encode($employees));
		$a_date = date2sql($a_date); 
		$sql = "INSERT INTO ".TB_PREF."kv_empl_attendance (a_date, employees) VALUES (".db_escape($a_date).", ".db_escape(implode(';', $employees)).")";

	db_query($sql, "The employee could not be added");
}


//--------------------------------------------------------------------------------------------
function update_employees_attendance($a_date, $employees){
	$a_date = date2sql($a_date); 
	$sql = "UPDATE ".TB_PREF."kv_empl_attendance SET employees=".db_escape(implode(';',$employees))." WHERE a_date=".db_escape($a_date);

	db_query($sql,"The employee could not be updated");

}

//--------------------------------------------------------------------------------------------
function db_has_day_attendance($a_date){
	$a_date = date2sql($a_date);
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_attendance WHERE a_date=".db_escape($a_date));
}

function get_empl_attendance_for_month($empl_id,$month, $year) {

 	$leave_Day = 0 ;
	$get_des=GetRow('kv_empl_attendancee', array('empl_id' => $empl_id,
												'month' => $month,
												'year' => $year));
	if($get_des != null || !empty($get_des)){										
		foreach($get_des as $single){
			if($single == 'A')
				$leave_Day += 1;
			if($single == 'HD')
				$leave_Day += 0.5;
		}
	}
	return $leave_Day;
}

function get_employee_attendance_details($empl_id, $start, $end) {

	$start_date = date2sql($start);
	$end_date = date2sql($end);
	$leave_dates=array();
	$sql= "SELECT a_date FROM ".TB_PREF."kv_empl_attendance  WHERE a_date >= ".db_escape($start_date)." AND a_date <= ".db_escape($end_date)." AND employees LIKE '%".$empl_id."%'"; 
	$result = db_query($sql, "could not get sales type");

	while($get_des=db_fetch($result))
		$leave_dates[]=$get_des['a_date'];
		
	return $leave_dates;

} 

function get_employees_absent_count($empl_ids_arr, $start, $end) {

	$start_date = date2sql($start);
	$end_date = date2sql($end);
	$leave_dates=array();
	foreach($empl_ids_arr as $empl_id) {
		$sql= "SELECT COUNT(a_date) FROM ".TB_PREF."kv_empl_attendance  WHERE a_date >= ".db_escape($start_date)." AND a_date <= ".db_escape($end_date)." AND employees LIKE '%".$empl_id."%'"; 
		$result = db_query($sql, "could not get sales type");

		$get_des=db_fetch($result);
		$leave_dates[] = array( 'empl_id' =>$empl_id, 'days' => $get_des[0]); 
	}
	return $leave_dates;

} 
?>