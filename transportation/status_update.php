<?php

$page_security = 'SA_ROUTECONFIG';
if (!@$_GET['popup'])
	$path_to_root = "..";
else	
	$path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/transportation/function/function.php");

include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

if(isset($_POST['activate_id'])){
	$route_value = $_POST['route_value'];
	$sql2 = "SELECT * FROM ".TB_PREF."transportation_details JOIN ".TB_PREF."transportation ON ".TB_PREF."transportation_details.trans_id=".TB_PREF."transportation.trans_id where fa_transportation.route_name=".db_escape($route_no)." AND ".TB_PREF."transportation_details.status=1";
    $route_result2=db_query($sql2); 
	echo $routeVal=db_num_rows($route_result2);
}

if(isset($_POST['route_name'])){
	$route_name = $_POST['route_name'];
	$sql2 ="SELECT s_name,stop_name FROM ".TB_PREF."routeconfig_detail JOIN ".TB_PREF."route_config ON ".TB_PREF."route_config.config_id=".TB_PREF."routeconfig_detail.config_id JOIN ".TB_PREF."stop ON ".TB_PREF."stop.id=".TB_PREF."routeconfig_detail.s_name where fa_route_config.route_name=".db_escape($route_name);
	
	$route_result2=db_query($sql2); 
	$stop_arr = array();

	while($routeVal = db_fetch($route_result2)){
	    $id = $routeVal["s_name"];
	    $name = $routeVal["stop_name"];

	    $stop_arr[] = array("id" => $id, "name" => $name);
	}

	echo json_encode($stop_arr);
}


if(isset($_POST['vehicle_no'])){
	$vehicle_no = $_POST['vehicle_no'];
	//$sql2 ="SELECT reg_no,driver_name FROM ".TB_PREF."vehicle Where vehicle_no=".db_escape($vehicle_no);
	$sql2 ="SELECT ModelYear,driver_name FROM ".TB_PREF."vehicle JOIN tblvehicles ON ".TB_PREF."vehicle.reg_no=tblvehicles.id Where ".TB_PREF."vehicle.vehicle_no=".db_escape($vehicle_no);
	$vehicle_no2=db_query($sql2); 
	$vehicle_noVal=db_fetch($vehicle_no2);

	echo json_encode($vehicle_noVal);
}

if(isset($_POST['id'])){
	$stop_no = $_POST['id'];
	$sql2 ="SELECT sequence,exp_time,drop_time FROM ".TB_PREF."routeconfig_detail Where s_name=".db_escape($stop_no);
	$stop_no2=db_query($sql2); 
	$stop_noVal=db_fetch($stop_no2);

	echo json_encode($stop_noVal);
}


if(isset($_POST['student_no'])){
	set_global_connection(0);
	
	$student_no = $_POST['student_no'];

	//$sql21 ="SELECT student_no FROM ".TB_PREF."transportation_details where student_no=".db_escape($student_no)." AND status=1";
	$sql21 ="SELECT student_no,route_name FROM ".TB_PREF."transportation_details JOIN ".TB_PREF."transportation ON ".TB_PREF."transportation_details.trans_id=".TB_PREF."transportation.trans_id where fa_transportation_details.student_no=".db_escape($student_no)." AND ".TB_PREF."transportation_details.status=1";

	$student_no21=db_query($sql21); 
	$student_noVal1=db_fetch($student_no21);
	


	set_global_connection(2);
	$sql2 ="SELECT stu_fname,father_lname FROM erp_student_information Where stu_id=".db_escape($student_no);
	$student_no2=db_query($sql2); 
	$student_noVal=db_fetch($student_no2);
	
	
	if($student_noVal1['student_no']==""){
		echo json_encode($student_noVal);
	}else {
		echo json_encode($student_noVal1);
	}
	
}

if(isset($_POST['batch_no'])){
	set_global_connection(2);
	$batch_no = $_POST['batch_no'];
	$sql2 ="SELECT * FROM participants_login WHERE participant_Alumni=". db_escape(0)."AND participant_academic=". db_escape($batch_no);
	
	$studentlist=db_query($sql2); 
	$users_arr = array();

	while($student_row = db_fetch($studentlist)){
	    $userid = $student_row["roll_no"];
	    $name = $student_row["roll_no"];

	    $users_arr[] = array("id" => $userid, "name" => $name);
	}

	echo json_encode($users_arr);
}

if(isset($_POST['route_no'])){

	$route_no=$_POST['route_no'];

	$sql = "SELECT * FROM ".TB_PREF."transportation_details JOIN ".TB_PREF."transportation ON ".TB_PREF."transportation_details.trans_id=".TB_PREF."transportation.trans_id where fa_transportation.route_name=".db_escape($route_no)." AND ".TB_PREF."transportation_details.status=1";
 	$route_no2=db_query($sql); 
	$routeNoVal=db_num_rows($route_no2);
	echo $routeNoVal;
}


?>