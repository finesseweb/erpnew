<?php
//////////////////////////////Route Query//////////////////////////////////////
function add_route($route_id, $route_name, $source, $destination, $status){
   
    $sql = "INSERT INTO " . TB_PREF . "route (route_id,route_name,source,destination,status) VALUES (" . db_escape($route_id) . "," . db_escape($route_name) . "," . db_escape($source) . "," . db_escape($destination) . "," . db_escape($status) . ")";
  
    db_query($sql, "The route could not be added");
}


function get_route_list($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "route WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get route");

    return db_fetch($result);
}
function getRouteList($status){
    $status=1;
    $sql = "SELECT * from " . TB_PREF . "route where status=". db_escape($status);
    
    return $sql ;db_query($sql, "could not get designation_master");
}
function update_route($selected_id, $route_id, $route_name, $source, $destination, $status){

    $sql = "UPDATE " . TB_PREF . "route  SET route_id=" . db_escape($route_id) . ",route_name=" . db_escape($route_name). ",source=" . db_escape($source) . ", destination=" . db_escape($destination) . " , status=" . db_escape($status) . " WHERE id = " . db_escape($selected_id);
   
   db_query($sql, "The route could not be updated");
}


//////////////////////////////Stop Query//////////////////////////////////////

function add_stop($stop_id, $stop_name, $status){
   
    $sql = "INSERT INTO " . TB_PREF . "stop (stop_id,stop_name,status) VALUES (" . db_escape($stop_id) . "," . db_escape($stop_name) . "," . db_escape($status) . ")";
  
    db_query($sql, "The route could not be added");
}


function get_stop_list($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "stop WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get route");

    return db_fetch($result);
}
function getStopList($status){
    $status=1;
    $sql = "SELECT * from " . TB_PREF . "stop where status=". db_escape($status);
    
    return $sql ;db_query($sql, "could not get designation_master");
}
function update_stop($selected_id, $stop_id, $stop_name, $status){

    $sql = "UPDATE " . TB_PREF . "stop  SET stop_id=" . db_escape($stop_id) . ",stop_name=" . db_escape($stop_name). ", status=" . db_escape($status) . " WHERE id = " . db_escape($selected_id);
   
   db_query($sql, "The route could not be updated");
}
///////////////////////////////////Route Configuration///////////////////////////////////////////////////////
function route_listsNew($name, $selected_id=null,$spec_opt=false, $submit_on_change=false)
{
	$sql ="SELECT id,route_name FROM ".TB_PREF."route";
	return combo_input($name, $selected_id, $sql, 'id', 'route_name',
 	array('order'=>'id',
		'spec_option' => $spec_opt,
		//'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
 		'async' => false
 	));
}
function route_list_cellNew($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo route_listsNew($name, $selected_id,$spec_opt, $submit_on_change);
	echo "</td>\n";
}
function route_rowNew($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	route_list_cellNew(null, $name, $selected_id,$spec_opt, $submit_on_change);
	echo "</tr>\n";
}

function stop_listsNew($name, $selected_id=null,$spec_opt=false, $submit_on_change=false)
{
	$sql ="SELECT id,stop_name FROM ".TB_PREF."stop";
	return combo_input($name, $selected_id, $sql, 'id', 'route_name',
 	array('order'=>'id',
		'spec_option' => $spec_opt,
		//'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
 		'async' => false
 	));
}
function stop_list_cellNew($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo stop_listsNew($name, $selected_id,$spec_opt, $submit_on_change);
	echo "</td>\n";
}
function stop_rowNew($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	stop_list_cellNew(null, $name, $selected_id,$spec_opt, $submit_on_change);
	echo "</tr>\n";
}
function add_routeConfigval($config_id,$route_name){
   
    $sql = "INSERT INTO " . TB_PREF . "route_config (config_id,route_name) VALUES (" . db_escape($config_id) . "," . db_escape($route_name) . ")";
  
    db_query($sql, "The route could not be added");
}
function add_routeConfigDetail($config_id,$stop_name, $sequence, $exp_time, $drop_time,$cost,$status){
   

    $sql = "INSERT INTO " . TB_PREF . "routeconfig_detail (config_id,s_name,sequence,exp_time,drop_time,cost,status) VALUES (" . db_escape($config_id) . "," . db_escape($stop_name) . "," . db_escape($sequence) . "," . db_escape($exp_time) . "," . db_escape($drop_time) . "," . db_escape($cost) . "," . db_escape($status) . ")";
  
    db_query($sql, "The route could not be added");
    return true;
}

function get_routeResultDetails($config){
 	$sql = "SELECT * FROM ".TB_PREF."routeconfig_detail JOIN ".TB_PREF."route_config ON ".TB_PREF."routeconfig_detail.config_id=".TB_PREF."route_config.config_id where fa_route_config.config_id=".db_escape($config);
 	$result = db_query($sql, "could not get route");

 	return $result;
 }

 function get_routeResultDetails2($config){
 	$sql = "SELECT * FROM ".TB_PREF."route_config JOIN ".TB_PREF."routeconfig_detail ON ".TB_PREF."route_config.config_id=".TB_PREF."routeconfig_detail.config_id where fa_route_config.config_id=".db_escape($config);
 	
 	return db_num_rows(db_query($sql, "error"));
 }

function get_routeResult($route_name=null){
   //$sql = "SELECT * from " . TB_PREF . "route_config";
	if($route_name){
		$sql = "SELECT * FROM ".TB_PREF."route_config LEFT JOIN ".TB_PREF."routeconfig_detail ON ".TB_PREF."route_config.config_id=".TB_PREF."routeconfig_detail.config_id where fa_route_config.route_name=".db_escape($route_name);
	
	}else{
		$sql = "SELECT * FROM ".TB_PREF."route_config LEFT JOIN ".TB_PREF."routeconfig_detail ON ".TB_PREF."route_config.config_id=".TB_PREF."routeconfig_detail.config_id  order by fa_routeconfig_detail.config_id DESC";
	}
	
    return $sql ;db_query($sql);
}

function get_routeResult1($route=''){
    //$route=1;
    if($route){
    // $sql = "SELECT * from " . TB_PREF . "route_config where route_name=".db_escape($route);
        $sql = "SELECT * FROM ".TB_PREF."route_config rcong JOIN ".TB_PREF."routeconfig_detail rcond ON rcong.config_id=rcond.config_id where rcong.config_id=".db_escape($route);
    } 
    else {
        $sql = "SELECT * from " . TB_PREF . "route_config where status=1";
    }
    return $sql ;db_query($sql);
}

function update_routeConfigval($config_id,$route_name){
   
    $sql = "UPDATE ".TB_PREF."route_config SET config_id=".db_escape($config_id).
	", route_name=".db_escape($route_name)
		. " WHERE config_id=" . db_escape($config_id);
	return db_query($sql, "could not update");

}
function update_routeConfigDetail($config_id,$stop_name, $sequence, $exp_time, $drop_time,$cost,$status){
   
    $sql = "UPDATE ".TB_PREF."routeconfig_detail SET config_id=".db_escape($config_id).
	", s_name=".db_escape($stop_name).",
		sequence=".db_escape($sequence).",
		exp_time=".db_escape($exp_time).",
		drop_time=".db_escape($drop_time).",
		cost=".db_escape($cost).",
		status=".db_escape($status)
		. " WHERE config_id=" . db_escape($config_id);
	return db_query($sql, "could not update user for $user_id");

}

function delete_route($config_id)
{
	
	$sql="DELETE FROM ".TB_PREF."routeconfig_detail WHERE config_id=".db_escape($config_id);

	db_query($sql, "could not delete");
}
function route_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false,$designation){
	global $all_items;

	$sql = "SELECT route_id,route_name FROM ".TB_PREF."route";

	$mode = 0;

	if ($editkey)
		set_editor('route', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'route_name', 'route_name',
	array(
	    'format' => null,
	    'order' => array('id'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("All Routes") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		//'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new department') :	_('Select department'),
		'show_inactive' => $show_inactive,
                
	) );
	if ($editkey)
		$ret .= add_edit_combo('department');
	return $ret;
}
function route_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false,$designation=''){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo route_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey,$designation);
	echo "</td>\n";
}
function vehicle_detail($name, $selected_id=null, $spec_option=false, $submit_on_change=false,$show_inactive=false){
	global $all_items;

	$r_name=$_POST['route'];

	$sql = "SELECT vehicle_no,vehicle_no FROM ".TB_PREF."transportation where route_name=".db_escape($r_name)." group by vehicle_no";

	$mode = 0;

	if ($editkey)
		set_editor('stop', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'vehicle_no', 'vehicle_no',
	array(
	    'format' => null,
	    'order' => array('id'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("All Vehicle No.") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		//'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new department') :	_('Select department'),
		'show_inactive' => $show_inactive,
                
	) );
	if ($editkey)
		$ret .= add_edit_combo('department');
	return $ret;
}
function vehicle_list_detail($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false,$designation=''){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo vehicle_detail($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey,$designation);
	echo "</td>\n";
}

function stop_detail($name, $selected_id=null, $spec_option=false, $submit_on_change=false,$show_inactive=false){
	global $all_items;

	$r_name=$_POST['route'];

	$sql = "SELECT s_name,stop_name FROM ".TB_PREF."routeconfig_detail JOIN ".TB_PREF."route_config ON ".TB_PREF."route_config.config_id=".TB_PREF."routeconfig_detail.config_id JOIN ".TB_PREF."stop ON ".TB_PREF."stop.id=".TB_PREF."routeconfig_detail.s_name where fa_route_config.route_name=".db_escape($r_name);
	$mode = 0;

	if ($editkey)
		set_editor('stop', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 's_name', 'stop_name',
	array(
	    'format' => null,
	   // 'order' => array('".TB_PREF."stop.id'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("All Stop No.") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'show_inactive' => $show_inactive,
                
	) );
	if ($editkey)
		$ret .= add_edit_combo('department');
	return $ret;
}
function stop_list_detail($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false,$designation=''){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo stop_detail($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey,$designation);
	echo "</td>\n";
}

//////////////////////////////vehicle  Query//////////////////////////////////////
function add_vehicle($vehicle_id, $vehicle_no, $reg_no, $seating_capacity,$driver_name, $status){
   
    $sql = "INSERT INTO " . TB_PREF . "vehicle (vehicle_id,vehicle_no,reg_no,seating_capacity,driver_name,status) VALUES (" . db_escape($vehicle_id) . "," . db_escape($vehicle_no) . "," . db_escape($reg_no) . "," . db_escape($seating_capacity) . "," . db_escape($driver_name) . "," . db_escape($status) . ")";
  
    db_query($sql, "The route could not be added");
}


function get_vehicle_list($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "vehicle WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get route");

    return db_fetch($result);
}
function getVehicleList($status){
    $status=1;
    $sql = "SELECT * from " . TB_PREF . "vehicle where status=". db_escape($status);
    
    return $sql ;db_query($sql, "could not get designation_master");
}
function update_vehicle($selected_id, $vehicle_id, $vehicle_no, $reg_no, $seating_capacity,$driver_name, $status){

    $sql = "UPDATE " . TB_PREF . "vehicle  SET vehicle_id=" . db_escape($vehicle_id) . ",vehicle_no=" . db_escape($vehicle_no). ",reg_no=" . db_escape($reg_no) . ", seating_capacity=" . db_escape($seating_capacity) . ", driver_name=" . db_escape($driver_name) . " , status=" . db_escape($status) . " WHERE id = " . db_escape($selected_id);
   
   db_query($sql, "The route could not be updated");
}
/////////////////////Transportation///////////////////////////////////

function add_transportation($trans_id, $route_name,$source,$destination,$vehicle_no,$reg_no,$driver_name,$stop,$sequence,$exp_time,$drop_time){
   
    $sql = "INSERT INTO " . TB_PREF . "transportation (trans_id,route_name,source,destination,vehicle_no,reg_no,driver_name,stop,sequence,exp_time,drop_time) VALUES (" . db_escape($trans_id) . "," . db_escape($route_name) . "," . db_escape($source) . "," . db_escape($destination) . "," . db_escape($vehicle_no) . "," . db_escape($reg_no) . "," . db_escape($driver_name) . "," . db_escape($stop) . "," . db_escape($sequence) . "," . db_escape($exp_time) . "," . db_escape($drop_time) . ")";
  	
    db_query($sql, "The route could not be added");
}


function add_transportationDetail($trans_id,$batch_no,$student_no, $name, $f_name,$status){
   
    $sql = "INSERT INTO " . TB_PREF . "transportation_details (trans_id,batch_no,student_no,name,f_name,status) VALUES (" . db_escape($trans_id) . "," . db_escape($batch_no) . "," . db_escape($student_no) . "," . db_escape($name) . "," . db_escape($f_name) . "," . db_escape($status). ")";
  	
    db_query($sql, "The route could not be added");
}
function get_transportResult($route_name,$vehicle,$stop){
   // $sql = "SELECT * from " . TB_PREF . "route_config";
	
	$sql = "SELECT * FROM ".TB_PREF."transportation";
	if($route_name){
		$sql .= " where route_name=".db_escape($route_name);
	
	}
	if($vehicle){
		$sql .= " AND vehicle_no=".db_escape($vehicle);
	
	}
	if($stop){
		$sql .= " AND stop=".db_escape($stop);
	
	}

    return $sql ;db_query($sql);
}

///////////////////transportation edit/////////////////////////////////////
function get_transportResultDetails($config){
 	$sql = "SELECT * FROM ".TB_PREF."transportation_details JOIN ".TB_PREF."transportation ON ".TB_PREF."transportation_details.trans_id=".TB_PREF."transportation.trans_id where fa_transportation_details.trans_id=".db_escape($config);
 	$result = db_query($sql, "could not get route");

 	return $result;
 }

  function get_transportResultDetails2($config){
 	$sql = "SELECT * FROM ".TB_PREF."transportation_details JOIN ".TB_PREF."transportation ON ".TB_PREF."transportation_details.trans_id=".TB_PREF."transportation.trans_id where fa_transportation_details.trans_id=".db_escape($config);
 	
 	return db_num_rows(db_query($sql, "error"));
 }

 function update_transportation($trans_id, $route_name,$source,$destination,$vehicle_no,$reg_no,$driver_name,$stop,$sequence,$nostudent,$exp_time,$drop_time){

    $sql = "UPDATE " . TB_PREF . "transportation  SET route_name=" . db_escape($route_name). ", source=" . db_escape($source) . ", destination=" . db_escape($destination) . ", vehicle_no=" . db_escape($vehicle_no) . " , reg_no=" . db_escape($reg_no) . ", driver_name=" . db_escape($driver_name) . ", stop=" . db_escape($stop) . ", sequence=" . db_escape($sequence) . ", student=" . db_escape($nostudent) . ", exp_time=" . db_escape($exp_time) . ", drop_time=" . db_escape($drop_time) . " WHERE trans_id = " . db_escape($trans_id);
  
   db_query($sql, "The route could not be updated");
}

 function update_transportationDetail($trans_id,$student_no, $name, $f_name,$status){

    $sql = "UPDATE " . TB_PREF . "transportation_details  SET student_no=" . db_escape($student_no). ",name=" . db_escape($name) . ", f_name=" . db_escape($f_name) . ", status=" . db_escape($status) . "  WHERE trans_id = " . db_escape($trans_id);
   
   db_query($sql, "The route could not be updated");
}
function delete_transportantionDetail($config_id)
{
	
	$sql="DELETE FROM ".TB_PREF."transportation_details WHERE trans_id=".db_escape($config_id);

	db_query($sql, "could not delete");
	
}
//////////////////////// vehicale ///////////////////////////////////////
function vehicle_list_cells($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo vehicle_list($name, $selected_id, $spec_opt, $submit_on_change);
	echo "</td>\n";
}
function vehicle_list($name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	$sql = "SELECT id,ModelYear FROM tblvehicles";
	return combo_input($name, $selected_id, $sql, 'id', 'ModelYear',
 	array('order'=>'id',
		'spec_option' => $spec_opt,
		'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
 		'async' => true
 	));
}

function text_vehicle($label, $name, $value, $size, $max, $title = null, $params = "", $post_label = "", $submit_on_change = true, $readonly = true) {
    echo "<tr><td class='label'>$label</td>";
    text_cells(null, $name, $value, $size, $max, $title, $params, $post_label, $submit_on_change, $readonly);

    echo "</tr>\n";
}

function text_driver($label, $name, $value, $size, $max, $title = null, $params = "", $post_label = "", $submit_on_change = true, $readonly = true) {
    echo "<tr><td class='label'>$label</td>";
    text_cells(null, $name, $value, $size, $max, $title, $params, $post_label, $submit_on_change, $readonly);

    echo "</tr>\n";
}

//count no of student//////
function NoOfStudentCount($route_no,$stop,$vehicle_no){
	$student_sql ="SELECT * FROM ".TB_PREF."transportation_details JOIN ".TB_PREF."transportation ON ".TB_PREF."transportation_details.trans_id=".TB_PREF."transportation.trans_id where fa_transportation.route_name=".db_escape($route_no)." AND ".TB_PREF."transportation_details.status=1 AND ".TB_PREF."transportation.stop=".db_escape($stop)." AND ".TB_PREF."transportation.vehicle_no=".db_escape($vehicle_no);
	
	$student_result=db_query($student_sql);
	$studentNoVal=db_num_rows($student_result);
	return $studentNoVal;

}

function stopList()
{
	echo $val ='<td><label class="">Stop </label>
				<select id="stop" class="form-control" name="stop_no">
				  <option value="">Select</option>
				</select></td>';

	return $val;
}

function totalstudent($route_no,$stop,$vehicle){

	$student_sql ="SELECT * FROM ".TB_PREF."transportation_details JOIN ".TB_PREF."transportation ON ".TB_PREF."transportation_details.trans_id=".TB_PREF."transportation.trans_id where fa_transportation.route_name=".db_escape($route_no);

	if($stop){
		$student_sql .= "AND ".TB_PREF."transportation.stop=".db_escape($stop);
	}

	if($vehicle){
		$student_sql .= "AND ".TB_PREF."transportation.vehicle_no=".db_escape($vehicle);
	}

	$student_sql .= "AND ".TB_PREF."transportation_details.status=1";
	$student_result=db_query($student_sql);
	$studentNoVal=db_num_rows($student_result);
	echo $val = '<div class="total_student">Total Number of Student : '.$studentNoVal.'</div>';

	return $val;
}

function checkStop($stop,$route_no){
	$sql="SELECT * FROM ".TB_PREF."transportation where route_name=".db_escape($route_no)." AND stop=".db_escape($stop);

	$result=db_query($sql);
    $Val=db_num_rows($result);
    return $Val;

}

function fetchstoplist($stop,$route_no){
	$sql="SELECT * FROM ".TB_PREF."transportation where route_name=".db_escape($route_no)." AND stop=".db_escape($stop);

	$result=db_query($sql);
    $Val=db_fetch($result);
    return $Val;

}

function checkVehicle($stop,$route_no,$vehicle){
	$sql="SELECT * FROM ".TB_PREF."transportation where route_name=".db_escape($route_no)." AND stop=".db_escape($stop)." AND vehicle_no=".db_escape($vehicle);

	$result=db_query($sql);
    $Val=db_fetch($result);
    return $Val;

}


function updateTransportationList($trans_id, $route_name,$source,$destination,$vehicle_no,$reg_no,$driver_name,$stop,$sequence,$exp_time,$drop_time){

    $sql = "UPDATE " . TB_PREF . "transportation  SET route_name=" . db_escape($route_name) . ",source=" . db_escape($source). ", destination=" . db_escape($destination) . ", vehicle_no=" . db_escape($vehicle_no) . ", reg_no=" . db_escape($reg_no) . ", driver_name=" . db_escape($driver_name) . " , stop=" . db_escape($stop) . ", sequence=" . db_escape($sequence) . ", exp_time=" . db_escape($exp_time) . ", drop_time=" . db_escape($drop_time) . " WHERE trans_id = " . db_escape($trans_id);
   
   db_query($sql, "The route could not be updated");
}

?>