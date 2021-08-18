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

if(isset($_POST['route'])){
	$route_value = $_POST['route'];
	$sql2 ="SELECT config_id FROM ".TB_PREF."route_config Where route_name=".db_escape($route_value);
	$route_result2=db_query($sql2); 
	$routeVal=db_fetch($route_result2);
	
	if($routeVal[0]){
		echo $routeVal[0];
	}else{
		echo '2';
	}
	
}


if(isset($_POST['stop']) && isset($_POST['routeVal']) && isset($_POST['vehicle'])){
	$route_value = $_POST['routeVal'];
	$stop_value = $_POST['stop'];
	$vehicle_value = $_POST['vehicle'];
	$sql2 ="SELECT trans_id FROM ".TB_PREF."transportation Where route_name=".db_escape($route_value)." AND stop=".db_escape($stop_value)." AND vehicle_no=".db_escape($vehicle_value);
	$route_result2=db_query($sql2); 
	$routeVal=db_fetch($route_result2);
	
	if($routeVal[0]){
		echo $routeVal[0];
	}else{
		echo '2';
	}
	
}



?>