<?php 


function  db_has_departments_exist(){
	$result = db_query("SELECT COUNT(*) FROM ".TB_PREF."kv_departments", "Can't Select department table");
	if(!$result) {		
		return  false; 
	} else return true ; 	
}


function kv_create_department_db() {
	
	$sql= "CREATE TABLE IF NOT EXISTS `".TB_PREF."kv_departments` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(60) NOT NULL DEFAULT '',
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;" ; 

	return db_query($sql, "Db Table creation failed, Kv Manufacturer table");
}

 
function add_department($description){
	$sql = "INSERT INTO ".TB_PREF."kv_departments (description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The sales department could not be added");
}

function update_department($selected_id, $description){
	$sql = "UPDATE ".TB_PREF."kv_departments SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The sales department could not be updated");
}

function delete_department($selected_id){
	$sql="DELETE FROM ".TB_PREF."kv_departments WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sales department");
}

function get_departments($show_inactive){
	$sql = "SELECT * FROM ".TB_PREF."kv_departments";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get kv_departments");
}

function get_department($selected_id){
	$sql = "SELECT * FROM ".TB_PREF."kv_departments WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}

function get_department_name($group_no){
	$sql = "SELECT description FROM ".TB_PREF."kv_departments WHERE id = ".db_escape($group_no);
	$result = db_query($sql, "could not get department");
	$row = db_fetch($result);
	return $row[0];
}
//-----------------Designation-----------------
function add_desig_group($name, $description){

	$sql = "INSERT INTO ".TB_PREF."kv_desig_group (name,description) VALUES (".db_escape($name).",".db_escape($description).")";
	//display_error($sql);die;
   	db_query($sql,"The sales department could not be added");
}
function get_desig_group($show_inactive){
	$sql = "SELECT * FROM ".TB_PREF."kv_desig_group";
	if (!$show_inactive) $sql .= " WHERE !inactive";
// $sql .= " ORDER BY name";
	return db_query($sql,"could not get Desig Group");
}


function update_desig_group($selected_id, $name, $description){
	$sql = "UPDATE ".TB_PREF."kv_desig_group SET name=".db_escape($name).",description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The sales department could not be updated");
}

function get_desig_group_edit($selected_id){
	$sql = "SELECT * FROM ".TB_PREF."kv_desig_group WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}

function delete_desig_group($selected_id){
	$sql="DELETE FROM ".TB_PREF."kv_desig_group WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sales department");
}


//------------------------------------------------------------------------------------
function department_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false,$designation=null){
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."kv_departments where inactive=0";

	$mode = 0;

	if ($editkey)
		set_editor('department', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
	    'format' => null,
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("All Departments") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new department') :	_('Select department'),
		'show_inactive' => $show_inactive,
                
	) );
	if ($editkey)
		$ret .= add_edit_combo('department');
	return $ret;
}


function getDepartment($empl_id){
    $sql = "SELECT dep.id as department_id , dep.description,desig.id,desig.name FROM ".TB_PREF."kv_departments as dep join ".TB_PREF."kv_empl_job as job on dep.id = job.department join ".TB_PREF."kv_desig_group as desig on desig.id = job.desig_group where job.empl_id ='".$empl_id."'";
    $result = db_query($sql,"could not delete sales department");
    return $result;
    
}




function department_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false,$designation=''){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo department_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey,$designation);
	echo "</td>\n";
}

function department_list_row($label, $name, $selected_id=null, $all_option = false, $submit_on_change=false, $show_inactive=false, $editkey = false,$designation=''){
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo department_list($name, $selected_id, $all_option, $submit_on_change,$show_inactive, $editkey,$designation);
	echo "</td>\n</tr>\n";
}
//----------------Designation Group Master-----------------------------------------n-----

function desig_group_lists($name, $selected_id=null,$spec_opt=false, $submit_on_change=false)
{
	$sql ="SELECT id,name FROM ".TB_PREF."kv_desig_group";
	return combo_input($name, $selected_id, $sql, 'id', 'name',
 	array('order'=>'name',
		'spec_option' => $spec_opt,
		//'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
 		'async' => true
 	));
}
function room_group_lists($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $room_id, $room_ac) {
    $sql = "SELECT room_id,room_no FROM " . TB_PREF . "room_master where deleted=0 AND room_type=" . db_escape($room_id) . "AND ac_avil=" . db_escape($room_ac);
    return combo_input($name, $selected_id, $sql, 'room_id', 'room_no', array('order' => 'room_no',
        'spec_option' => $spec_opt,
        //'spec_id' => -1,
        'select_submit' => $submit_on_change,
        'async' => true
    ));
}

function room_group_list_cell($label, $name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $room_id, $room_ac) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo room_group_lists($name, $selected_id, $spec_opt, $submit_on_change, $room_id, $room_ac);
    echo "</td>\n";
}

function room_list_row($label, $name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $room_id, $room_ac) {
    echo "<tr><td class='label'>$label</td>";
    room_group_list_cell(null, $name, $selected_id, $spec_opt, $submit_on_change, $room_id, $room_ac);
    echo "</tr>\n";
}



function desig_group_list_cell($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo desig_group_lists($name, $selected_id,$spec_opt, $submit_on_change);
	echo "</td>\n";
}

function desiggroup_list_row($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	desig_group_list_cell(null, $name, $selected_id,$spec_opt, $submit_on_change);
	echo "</tr>\n";
}
function room_type($name, $selected_id = null, $name_yes = "", $name_no = "", $submit_on_change = false) {

    $r_type = array(1 => 'Boys', 2 => 'Faculty', 3 => 'Girls', 4 => 'Guest', 5 => 'Staff');

    return array_selector($name, $selected_id, $r_type, array(
        'select_submit' => $submit_on_change,
        'async' => false));
}

function fee_type($name, $selected_id = null, $name_yes = "", $name_no = "", $submit_on_change = false) {

    $r_type = array(1 => 'Daily', 2 => 'Monthly', 3 => 'Yearly');

    return array_selector($name, $selected_id, $r_type, array(
        'select_submit' => $submit_on_change,
        'async' => false));
}

function fee_list_cells($label, $name, $selected_id = null, $name_yes = "", $name_no = "", $submit_on_change = false) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo fee_type($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function fee_type_list_row($label, $name, $selected_id = null, $name_yes = "", $name_no = "", $submit_on_change = false) {

    echo "<tr><td class='label'>$label</td>";
    fee_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</tr>\n";
}

function select_type($name, $selected_id = null, $name_yes = "", $name_no = "", $submit_on_change = false) {

    $r_type = array(0 => '--Select--',1 => 'Guest', 3 => 'Faculty', 4 => 'Staff', 2 => 'Student');

    return array_selector($name, $selected_id, $r_type, array(
        'select_submit' => $submit_on_change,
        'async' => false));
}

function select_list_cells($label, $name, $selected_id = null, $name_yes = "", $name_no = "", $submit_on_change = false) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo select_type($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function select_type_list_row($label, $name, $selected_id = null, $name_yes = "", $name_no = "", $submit_on_change = false) {

    echo "<tr><td class='label'>$label</td>";
    select_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</tr>\n";
}

function fetch_stu_name($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $stu_id_arr = array()) {
      
    $stu_id = implode(',', $stu_id_arr);
    $sql = "SELECT stu_id, CONCAT(stu_fname, ' ', stu_lname, ' -',stu_id) as firstlast FROM erp_student_information  WHERE stu_status!=2 ";
    $sql_part = " AND stu_id NOT IN ($stu_id)";
    if ($stu_id != '') {
      $sql .= $sql_part;
    }
    $sql.=" order by firstlast";

    return combo_input($name, $selected_id, $sql, 'id',array('order' => 'academic_id',
        'spec_option' => $spec_opt,
        //'spec_id' => -1,
        'select_submit' => $submit_on_change,
        'async' => true
    ));
}

function fetch_guest_name($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $stu_id_arr = array()) {

    $stu_id = implode(',', $stu_id_arr);

    $sql = "SELECT guest_id,guest_name FROM fa_guest_registration WHERE inactive=0 ";
    $sql_part = " AND guest_id NOT IN ($stu_id)";
    if ($stu_id != '') {
       $sql .= $sql_part;
    }

    return combo_input($name, $selected_id, $sql,'id', array('order' => 'guest_name',
        'spec_option' => $spec_opt,
        //'spec_id' => -1,
        'select_submit' => $submit_on_change,
        'async' => true
    ));
}


function fetch_faculty_name($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $stu_id_arr = array()) {

    $stu_id = implode(',', $stu_id_arr);

   // $sql = "SELECT empl_id,CONCAT(empl_firstname, ' ','  ',empl_id) as firstlast FROM fa_kv_empl_info WHERE  ";
     $sql = "SELECT empl_id,CONCAT(empl_firstname, ' ','  ',empl_id) as firstlast FROM fa_kv_empl_info where status = 1";
    $sql_part = " And empl_id LIKE 'EMP-F-%' AND empl_id NOT IN ($stu_id)";
    if ($stu_id != '') {
      $sql .= $sql_part;
    }
     $sql.=" order by firstlast";

    return combo_input($name, $selected_id, $sql,'id' ,array('order' => 'empl_id',
        'spec_option' => $spec_opt,
        //'spec_id' => -1,
        'select_submit' => $submit_on_change,
        'async' => true
    ));
}


function fetch_staff_name($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $stu_id_arr = array()) {

    $stu_id = implode(',', $stu_id_arr);

    //$sql = "SELECT empl_id,CONCAT(empl_firstname, ' ','  ',empl_id) as firstlast FROM fa_kv_empl_info WHERE empl_id LIKE 'EMP-S-%'";
      $sql = "SELECT empl_id,CONCAT(empl_firstname, ' ','  ',empl_id) as firstlast FROM fa_kv_empl_info  ";
    $sql_part = "WHERE empl_id LIKE 'EMP-S-%' AND empl_id NOT IN ($stu_id)";
    if ($stu_id != '') {
       $sql .= $sql_part;
    }

    return combo_input($name, $selected_id, $sql,'id', array('order' => 'empl_id',
        'spec_option' => $spec_opt,
        //'spec_id' => -1,
        'select_submit' => $submit_on_change,
        'async' => true
    ));
}


function select_guest_name_list_cells($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $array) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo fetch_guest_name($name, $selected_id, $spec_opt, $submit_on_change, $array);
    echo "</td>\n";
}


function select_faculty_name_list_cells($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $array) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo fetch_faculty_name($name, $selected_id, $spec_opt, $submit_on_change, $array);
    echo "</td>\n";
}


function select_staff_name_list_cells($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $array) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo fetch_staff_name($name, $selected_id, $spec_opt, $submit_on_change, $array);
    echo "</td>\n";
}


function select_stu_name_list_cells($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $array) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo fetch_stu_name($name, $selected_id, $spec_opt, $submit_on_change, $array);
    echo "</td>\n";
}

function name_list_row($label, $name, $id, $selected_id = null, $spec_opt = false, $submit_on_change = false) {

    echo "<tr><td class='label'>$label</td>";
   
    $sql = "SELECT stu_id FROM fa_alloted_room WHERE inactive=1";
    $result = db_query($sql,"could not delete sales department");
    $array = Array();
    while ($row = db_fetch_assoc($result)) {
        $array[] = "'".$row['stu_id']."'";
    }

    switch ($id) {

        case 1:
            select_guest_name_list_cells($name, $selected_id, $spec_opt, $submit_on_change, $array);
            break;

        case 2:
            set_global_connection(2);
            select_stu_name_list_cells($name, $selected_id, $spec_opt, $submit_on_change, $array);
            set_global_connection(0);
            break;
        case 3:
           select_faculty_name_list_cells($name, $selected_id, $spec_opt, $submit_on_change, $array);
            break;
        case 4:
           select_staff_name_list_cells($name, $selected_id, $spec_opt, $submit_on_change, $array);
            break;
    }
    echo "</tr>\n";
}

function alloted_stu_id($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $stu_id_arr = array()) {

    $stu_id = implode(',', $stu_id_arr);
    $sql = "SELECT student_id, CONCAT(stu_fname, ' ', stu_lname, ' -',stu_id) as firstlast FROM erp_student_information ";
    $sql_part = " WHERE student_id IN ($stu_id)";
    if ($stu_id != '') {
        $sql .= $sql_part;
    }

    // $sql = "SELECT stu_id,stu_id,id FROM fa_alloted_room WHERE inactive=1";

    return combo_input($name, $selected_id, $sql, array('order' => 'stu_id',
        'spec_option' => $spec_opt,
        // 'spec_id' => id,
        'select_submit' => $submit_on_change,
        'async' => true
    ));
}

function id_list_cells($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $stu_id) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo alloted_stu_id($name, $selected_id, $spec_opt, $submit_on_change, $stu_id);
    echo "</td>\n";
}

function student_id_list_row($label, $name, $id, $selected_id = null, $spec_opt = false, $submit_on_change = false) {
    echo "<tr><td class='label'>$label</td>";
    $result = mysql_query("SELECT stu_id FROM fa_alloted_room WHERE inactive=1");
    $stu_id = Array();
    while ($row = mysql_fetch_array($result)) {
        $stu_id[] = $row['stu_id'];
    }

    switch ($id) {
        case 1:
            echo"<td> </td>";
            break;
        case 2:
            set_global_connection(2);
            id_list_cells($name, $selected_id, $spec_opt, $submit_on_change, $stu_id);
            set_global_connection(0);
            break;
        case 3:
            echo"<td> </td>";
            break;
        case 4:
            echo"<td> </td>";
            break;
    }

    echo "</tr>\n";
}

function room_list_cells($label, $name, $selected_id = null, $name_yes = "", $name_no = "", $submit_on_change = false) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo room_type($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function room_type_list_row($label, $name, $selected_id = null, $name_yes = "", $name_no = "", $submit_on_change = false) {

    echo "<tr><td class='label'>$label</td>";
    room_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</tr>\n";
}

function add_room_allotment($stu_id, $room_trans_id, $f_date, $t_date) {

    $sql = "SELECT room_id FROM fa_room_transition WHERE id=" . $room_trans_id;
    $result = db_query($sql, "The sales department could not be updated");
    $myrow = db_fetch($result);
    $room_id = $myrow['room_id'];
    
    $sql = "UPDATE " . TB_PREF . "room_master  SET inactive ='1' WHERE room_id = " . db_escape($room_id);
    db_query($sql, "The sales department could not be updated");

    $sql = "INSERT INTO " . TB_PREF . "alloted_room (stu_id ,bed_id,from_date,to_date) VALUES (" . db_escape($stu_id) . "," . db_escape($room_trans_id) . "," . db_escape(date("y-m-d", strtotime($f_date))) . "," . db_escape(date("y-m-d", strtotime($t_date))) . ")";
    db_query($sql, "The sales department could not be added");

    $sql = "UPDATE " . TB_PREF . "room_transition  SET inactive='1' WHERE id = " . db_escape($room_trans_id);
    db_query($sql, "The sales department could not be updated");
  
    $sql = "UPDATE " . TB_PREF . "guest_registration  SET inactive='1' WHERE guest_id = " . db_escape($stu_id);
    db_query($sql, "The sales department could not be updated");
}

function get_room_no($bed_id) {
    $sql = "SELECT room_id FROM fa_room_transition WHERE id=" . "'".$bed_id ."'";
    $result = db_query($sql, "The alloted student could not be updated");
    $myrow = db_fetch($result);
    $room_id = $myrow['room_id'];
    return $room_id;
}

function leave_hostel($stu_id, $remark) {
    $sql = "SELECT bed_id FROM fa_alloted_room WHERE stu_id=" . "'".$stu_id ."'". " AND inactive = 1";
    $result = db_query($sql, "The alloted student could not be updated");
    $myrow = db_fetch($result);
    $bed_id = $myrow['bed_id'];

    $sql = "UPDATE fa_alloted_room  SET inactive=0 WHERE  bed_id= " . db_escape($bed_id);
    db_query($sql, "The sales department could not be updated");

    $sql = "UPDATE fa_room_transition  SET inactive=0 WHERE id= " . db_escape($bed_id);
    db_query($sql, "The sales department could not be updated");

    $sql = "UPDATE " . TB_PREF . "alloted_room  SET remark=" . db_escape($remark) . ",deleted =1,to_date=" . db_escape(date('y-m-d')) . " WHERE bed_id = " . db_escape($bed_id);
    db_query($sql, "The sales department could not be added");
    
    $sql = "UPDATE " . TB_PREF . "guest_registration  SET inactive='0' WHERE guest_id = " . db_escape($stu_id);
    db_query($sql, "The sales department could not be updated");
}

function move_room_allotment($stu_id, $room_trans_id, $remark) {
    $sql = "SELECT bed_id fROM fa_alloted_room WHERE stu_id=" . "'".$stu_id ."'". " AND inactive = 1";
    $result = db_query($sql, "The sales department could not be updated");
    $myrow = db_fetch($result);
    $bed_id = $myrow['bed_id'];
    $sql = "UPDATE fa_alloted_room  SET inactive=0,remark=" . db_escape($remark) . " WHERE  bed_id= " . db_escape($bed_id);
    db_query($sql, "The sales department could not be updated");

    $sql = "UPDATE fa_room_transition  SET inactive=0 WHERE id= " . db_escape($bed_id);
    db_query($sql, "The sales department could not be updated");


    $date = date('y-m-d');
    $sql = "INSERT INTO " . TB_PREF . "alloted_room (stu_id ,bed_id,from_date) VALUES (" . db_escape($stu_id) . "," . db_escape($room_trans_id) . "," . db_escape($date) . ")";
    db_query($sql, "The sales department could not be added");

    $sql = "UPDATE " . TB_PREF . "room_transition  SET inactive='1' WHERE id = " . db_escape($room_trans_id);
    db_query($sql, "The sales department could not be updated");
}

function add_Room_master($room_des, $room_no, $room_type, $ac, $status) {

    $sql = "INSERT INTO " . TB_PREF . "room_master (room_des,room_no,room_type,ac_avil,status) VALUES (" . db_escape($room_des) . "," . db_escape($room_no) . "," . db_escape($room_type) . "," . db_escape($ac) . "," . db_escape($status) . ")";
    //display_error($sql);die;
    db_query($sql, "The sales department could not be added");
}

function add_guest($g_name, $f_name, $gender, $marital_status, $pic, $p_stay, $line1, $line2, $city, $country, $state, $pin_code, $email, $contact_no,$filename,$unique_name) {
    $sql = "INSERT INTO " . TB_PREF . "guest_registration (guest_name,fathers_name,gender,marital_status,id_proof,porpose,line1,line2,city,country,state,pin_code,email_id,contact_number,registraion_date,filename,unique_name) VALUES (" . db_escape($g_name) . "," . db_escape($f_name) . "," . db_escape($gender) . "," . db_escape($marital_status) . "," . db_escape($pic) . "," . db_escape($p_stay) . "," . db_escape($line1) . "," . db_escape($line2) . "," . db_escape($city) . "," . db_escape($country) . "," . db_escape($state) . "," . db_escape($pin_code) . "," . db_escape($email) . "," . db_escape($contact_no) . "," . db_escape(date('y-m-d')) ."," . db_escape($filename) ."," . db_escape($unique_name) . ")";

    //display_error($sql);die;
    db_query($sql, "The sales department could not be added");
}







function add_transition_master($roomId, $bed_no, $fee_type, $charge) {

    $sql = "INSERT INTO " . TB_PREF . "room_transition (room_id,bed_no,fee_type,charge) VALUES (" . db_escape($roomId) . "," . db_escape($bed_no) . "," . db_escape($fee_type) . "," . db_escape($charge) . ")";
    //display_error($sql);die;
    db_query($sql, "The sales department could not be added");
}



function is_issue($issue_no)
{
    $sql = "select issue_no from " . TB_PREF . "dispatch_management where  issue_no ='".$issue_no."'"; 

   $result = db_query($sql, "The alloted student could not be updated");
   // $myrow = db_fetch($result);
    //$issue_no = $myrow['issue_no'];
    return db_num_rows($result);
}

function is_issu3($issue_no)
{
    $sql = "select issue_no from " . TB_PREF . "recieved_management where  issue_no ='".$issue_no."'"; 

   $result = db_query($sql, "The alloted student could not be updated");
   // $myrow = db_fetch($result);
    //$issue_no = $myrow['issue_no'];
    return db_num_rows($result);
}
function is_issue1($issue_no)
{
    
    $sql = "select ref_id from " . TB_PREF . "dispatch_management where  issue_no ='".$issue_no."'"; 
    //display_error($sql);
   $result = db_query($sql, "The alloted student could not be updated");
   $myrow = db_fetch($result);
   // display_error($myrow['ref_id']);
    return $myrow[0];
}


function is_issue2($issue_no)
{
    
    $sql = "select ref_id from " . TB_PREF . "recieved_management where  issue_no ='".$issue_no."'"; 
    //display_error($sql);
   $result = db_query($sql, "The alloted student could not be updated");
   $myrow = db_fetch($result);
    return $myrow[0];
}


function add_guest1($ref_id, $upload_scanned_copy, $issue_no, $line1, $line2, $city, $country, $state, $pin_no, $subject_title, $dispatch_date,$designation,$department,$dispatch_mode,$document_type,$dispatched_reciept_number_if_any,$sender_person,$sender_designation,$sender_department,$email,$c_number,$person_org,$filename,$unique_name,$remarks){
   
    $address = $line1.",". $line2;
    
    $sql = "INSERT INTO " . TB_PREF . "dispatch_management (ref_id,upload_scanned_copy,issue_no,address,city,country,state,pin_no,subject_title,dispatch_date,designation,department,dispatch_mode,document_type,dispatched_reciept_number_if_any,sender_person,sender_designation,sender_department,email_id,contact_no,person_org_name,filename,unique_name,remarks) VALUES (" . db_escape($ref_id) . "," . db_escape($upload_scanned_copy) . "," . db_escape($issue_no) . "," . db_escape($address) . "," . db_escape($city) . "," . db_escape($country) . "," . db_escape($state) . "," . db_escape($pin_no) . "," . db_escape($subject_title) . "," . db_escape(date('Y-m-d', strtotime($dispatch_date))) . "," . db_escape($designation) ."," . db_escape($department) ."," . db_escape($dispatch_mode).",". db_escape($document_type) ."," . db_escape($dispatched_reciept_number_if_any) .",". db_escape($sender_person) ."," . db_escape($sender_designation).",". db_escape($sender_department)  ."," . db_escape($email).",". db_escape($c_number).",". db_escape($person_org).",". db_escape($filename) ."," . db_escape($unique_name) ."," . db_escape($remarks) .")";
    
//display_error($sql); exit;
    db_query($sql, "The sales department could not be added");
}

function recieve_guest1($ref_id,  $issue_no, $subject_title, $recieved_date,$recieved_mode,$document_type,$sender_person,$sender_designation,$sender_department,$remarks){
    $sql = "INSERT INTO " . TB_PREF . "recieved_management(ref_id,issue_no,subject_title,recieved_date,recieve_mode,document_type,sender_person,sender_designation,sender_department,remarks) VALUES (" . db_escape($ref_id) . "," . db_escape($issue_no). "," .  db_escape($subject_title) . "," . db_escape(date('Y-m-d', strtotime($recieved_date))) . "," . db_escape($recieved_mode).",". db_escape($document_type) ."," . db_escape($sender_person) ."," . db_escape($sender_designation).",". db_escape($sender_department) ."," . db_escape($remarks) .")";
    db_query($sql, "The sales department could not be added");
}






function add_designation($desig_group_id, $name, $description){

	$sql = "INSERT INTO ".TB_PREF."designation_master (desig_group_id,name,description) VALUES (".db_escape($desig_group_id).",".db_escape($name).",".db_escape($description).")";
	//display_error($sql);die;
   	db_query($sql,"The sales department could not be added");
}
 function get_designation($show_inactive){
	$sql="SELECT desg.*,des_group.name as grp_name FROM ".TB_PREF."designation_master as desg,".TB_PREF."kv_desig_group as des_group where desg.desig_group_id=des_group.id";
	if (!$show_inactive)
		$sql .= " AND !desg.inactive";
	//$sql .= " ORDER BY name";
	return db_query($sql,"could not get designation_master");
}
function update_designation($selected_id, $desig_group_id, $name, $description){
	$sql = "UPDATE ".TB_PREF."designation_master SET desig_group_id=".db_escape($desig_group_id).",name=".db_escape($name).",description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The sales department could not be updated");
}

function delete_designation($selected_id){
	$sql="DELETE FROM ".TB_PREF."designation_master WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sales department");
}
//-------------------Leave Master--------------------------------------------------------/
function desig_lists($name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$desig_group)
{

	$sql ="SELECT id,name FROM ".TB_PREF."designation_master WHERE desig_group_id = ".$desig_group."";
	return combo_input($name, $selected_id, $sql, 'id', 'description',
 	array('order'=>'name',
		'spec_option' => $spec_opt,
		'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
 		'async' => true
 	));
	
}

function desig_list_cell($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false,$desig_group)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo desig_lists($name, $selected_id,$spec_opt, $submit_on_change,$desig_group);
	echo "</td>\n";
}


function desig_list_row($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$desig_group)
{
	echo "<tr><td class='label'>$label</td>";
	desig_list_cell(null, $name, $selected_id,$spec_opt, $submit_on_change,$desig_group);
	echo "</tr>\n";
}

function get_weekly_off(){
    $sql ="SELECT * FROM ".TB_PREF."kv_empl_option limit 25";
    return db_query($sql,"unable to get_data!");
}

function student_information($student_id) {

    set_global_connection(2);
    $sql = "SELECT stu_id,CONCAT(stu_fname, ' ', stu_lname, ' ( ' ,stu_id, ' )') as firstlast,gender,premanent_addr,stu_mobileno FROM erp_student_information where stu_id=" . db_escape($student_id);
    $result = db_query($sql, "could not get designation_master");
    return db_fetch($result);
}

function faculty_staff_information($student_id) {
    $sql = "SELECT empl_id,CONCAT(empl_firstname, ' ', ' ( ' ,empl_id, ' )') as firstlast,gender,CONCAT(addr_line1, ', ',addr_line2) as address,mobile_phone FROM fa_kv_empl_info where empl_id=" . db_escape($student_id);
    $result = db_query($sql, "could not get designation_master");
    return db_fetch($result);
}

function guest_information($student_id) {
    $sql = "SELECT guest_name,gender,CONCAT(line1, ', ',line2) as address,contact_number FROM fa_guest_registration where guest_id=" . db_escape($student_id);
    $result = db_query($sql, "could not get designation_master");
    return db_fetch($result);
}
function get_Room_status($show_inactive) {

    //$sql = "SELECT trans.*,master.room_no,allot.bed_id,allot.stu_id FROM " . TB_PREF . "room_transition as trans," . TB_PREF . "room_master as master," . TB_PREF . "alloted_room as allot where trans.room_id = master.room_id AND trans.inactive in (0,1) ";

    $sql = "SELECT trans.*,room_master.room_no,alot.remark, alot.stu_id,alot.bed_id,alot.from_date FROM fa_room_transition as trans LEFT JOIN fa_room_master as room_master ON trans.room_id = room_master.room_id LEFT JOIN fa_alloted_room as alot on trans.id = alot.bed_id AND alot.inactive=1 WHERE trans.deleted=0 AND room_master.deleted=0";

    $sql .= " ORDER BY trans.inactive DESC";
    return db_query($sql, "could not get designation_master");
}

function get_Guest_info($show_inactive) {
    $sql = "SELECT *,CONCAT(line1, ', ', line2, ', ',city) as address from " . TB_PREF . "guest_registration";
    if (!$show_inactive)
        $sql .= " WHERE !deleted";
    // $sql .= " ORDER BY room_no";
    return db_query($sql, "could not get designation_master");
}

function getDispatchInfo($status){
   $status=1;   
    $sql = "SELECT *,CONCAT(address, city,state, country, pin_no) as residence from " . TB_PREF . "dispatch_management where status=1";
    return $sql ;db_query($sql, "could not get designation_master");
}
function getRecieveInfo($status){
    $status=1;
    $sql = "SELECT * from " . TB_PREF . "recieved_management where status=1";
    
    return $sql;
    
    db_query($sql, "could not get designation_master");
}

function getRecieveInfo1(){
    $sql = "SELECT * from " . TB_PREF . "recieved_management";
    
    return db_query($sql, "could not get designation_master");
}


function get_Room_master($show_inactive) {
    $sql = "SELECT * from " . TB_PREF . "room_master";
    if (!$show_inactive)
        $sql .= " WHERE !deleted ";
    $sql .= " ORDER BY room_no";
    return db_query($sql, "could not get designation_master");
}

function get_transition_master($show_inactive) {
    $sql = "SELECT trans.*,master.room_no FROM " . TB_PREF . "room_transition as trans," . TB_PREF . "room_master as master where master.room_id=trans.room_id AND master.deleted=0";

    if (!$show_inactive)
        $sql .= " AND !trans.deleted";
    $sql .= " ORDER BY master.room_no";
    return db_query($sql, "could not get designation_master");
}

function get_record($show_inactive, $room_type, $ac) {
    // $sql = "SELECT room_id,room_no FROM " . TB_PREF . "room_master where room_type=". db_escape($room_type)."AND ac_avil=".db_escape($ac);
    $sql = "SELECT master.*,trans.bed_no,trans.id,trans.room_id FROM " . TB_PREF . "room_master as master," . TB_PREF . "room_transition as trans where master.room_type=" . db_escape($room_type) . "AND master.ac_avil=" . db_escape($ac) . "AND master.room_id=trans.room_id AND master.deleted=0 AND trans.deleted=0";

    if (!$show_inactive)
        $sql .= " AND !trans.inactive";
    $sql .= " ORDER BY room_no";
    return db_query($sql, "could not get designation_master");
}

function get_stu_info($show_inactive, $stu_id,$s_type) {

    // $sql ="SELECT trans.*,room_master.room_no,alot.stu_id,alot.bed_id,alot.from_date FROM fa_room_transition as trans LEFT JOIN fa_room_master as room_master ON trans.room_id = room_master.room_id LEFT JOIN fa_alloted_room as alot on trans.id = alot.bed_id AND alot.inactive=1 AND stu_id=". db_escape($stu_id);
        switch($s_type){
 case 1:
    $sql = "SELECT * FROM fa_alloted_room WHERE inactive=1 AND stu_id=" . db_escape($stu_id);
    return db_query($sql, "could not get designation_master");
    break;  
 case 2:
    $sql = "SELECT * FROM fa_alloted_room WHERE inactive=1 AND stu_id=" . db_escape($stu_id);
    return db_query($sql, " ");
    break;
 case 3:
    $sql = "SELECT * FROM fa_alloted_room WHERE inactive=1 AND stu_id=" . db_escape($stu_id);
    return db_query($sql, " ");
    break;
 case 4:
    $sql = "SELECT * FROM fa_alloted_room WHERE inactive=1 AND stu_id=" . db_escape($stu_id);
    return db_query($sql, "could not get designation_master");
    break;
        }
}

function update_room_Master($selected_id, $room_des, $room_no, $room_type, $ac, $status) {
    $sql = "UPDATE " . TB_PREF . "room_master  SET room_des=" . db_escape($room_des) . ",room_no=" . db_escape($room_no) . ",room_type=" . db_escape($room_type) . ",ac_avil =" . db_escape($ac) . ",          status =" . db_escape($status) . " WHERE room_id = " . db_escape($selected_id);

    db_query($sql, "The sales department could not be updated");
}

function update_guest($selected_id,$g_name, $f_name, $gender, $marital_status, $pic, $p_stay, $line1, $line2, $city, $country, $state, $pin_code, $email, $contact_no,$filename,$unique_name) {
  
    $sql = "UPDATE " . TB_PREF . "guest_registration  SET guest_name=" . db_escape($g_name) . ",fathers_name=" . db_escape($f_name) . ",gender=" . db_escape($gender) . ",marital_status =" . db_escape($marital_status) . ",id_proof =" . db_escape($pic)
            . ",porpose =" . db_escape($p_stay) . ",line1 =" . db_escape($line1) . ",line2 =" . db_escape($line2) . ",city =" . db_escape($city) . ",country =" . db_escape($country) . ",state =" . db_escape($state) . ",pin_code =" . db_escape($pin_code) . ",email_id =" . db_escape($email) . ",contact_number =" . db_escape($contact_no) .",filename=".db_escape($filename) .",unique_name=".db_escape($unique_name) . " WHERE guest_id = " . db_escape($selected_id);

    db_query($sql, "The sales department could not be updated");
}






function update_guest1($selected_id,$ref_id, $upload_scanned_copy, $issue_no, $line1, $line2, $city, $country, $state, $pin_no, $subject_title, $dispatch_date,$designation,$department,$dispatch_mode,$document_type,$dispatched_reciept_number_if_any,$sender_person,$sender_designation,$sender_department,$email,$c_number,$person_org,$filename,$unique_name,$remarks){
   
    $address = $line1.",". $line2;
  
    $sql = "UPDATE " . TB_PREF . "dispatch_management  SET upload_scanned_copy=" . db_escape($upload_scanned_copy) . ",designation=" . db_escape($designation). ",issue_no=" . db_escape($issue_no) . ",subject_title=" . db_escape($subject_title) . ",dispatch_date=" . db_escape(date('Y-m-d')) .",department=" . db_escape($department) .",dispatch_mode=" . db_escape($dispatch_mode) .",document_type=" . db_escape($document_type) .",person_org_name=" . db_escape($person_org) .",dispatched_reciept_number_if_any=" . db_escape($dispatched_reciept_number_if_any) .",sender_person=" . db_escape($sender_person)  .",sender_designation=" . db_escape($sender_designation) .",sender_department=" . db_escape($sender_department)  . ",address =" . db_escape($address).  ",city =" . db_escape($city) . ",country =" . db_escape($country) . ",state =" . db_escape($state) . ",pin_no =" . db_escape($pin_no) . ",email_id =" . db_escape($email) . ",contact_no =" . db_escape($c_number) .",filename=".db_escape($filename) .",unique_name=".db_escape($unique_name) .",remarks=" . db_escape($remarks) . " WHERE id = " . db_escape($selected_id);

    db_query($sql, "The sales department could not be updated");
}




function update_recieved($selected_id,$ref_id,  $issue_no, $subject_title,$recieved_date,$recieved_mode,$document_type,$sender_person,$sender_designation,$sender_department,$remarks){
    
    $sql = "UPDATE " . TB_PREF . "recieved_management  SET issue_no=" . db_escape($issue_no) . ",subject_title=" . db_escape($subject_title) . ",recieved_date=" . db_escape(date('Y-m-d',strtotime($recieved_date))) .",recieve_mode=" . db_escape($recieved_mode) .",document_type=" . db_escape($document_type) .",sender_person=" . db_escape($sender_person)  .",sender_designation=" . db_escape($sender_designation) .",sender_department=" . db_escape($sender_department) .",remarks=" . db_escape($remarks) ." WHERE id = " . db_escape($selected_id);
 //  display_error($sql); 
db_query($sql, "The sales department could not be updated");
    
}








function update_transition_master($selected_id, $roomId, $bed_no, $fee_type, $charge) {
    $sql = "UPDATE " . TB_PREF . "room_transition  SET room_id=" . db_escape($roomId) . ",bed_no=" . db_escape($bed_no) . ",fee_type=" . db_escape($fee_type) . ",charge =" . db_escape($charge) . "WHERE id = " . db_escape($selected_id);
    db_query($sql, "The sales department could not be updated");
}


function get_designationedit($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "designation_master WHERE id=" . db_escape($selected_id);

    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}

function get_transition_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "room_transition WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}

function get_masterRoom_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "room_master WHERE room_id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}

function get_guest_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "guest_registration WHERE guest_id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}


function get_dispatch_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "dispatch_management WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}

function get_recieve_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "recieved_management WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}


function depatch_id($id){
     $sql = "SELECT * FROM " . TB_PREF . "dispatch_mode WHERE id=" . db_escape($id);
    $result = db_query($sql, "could not get department");
    //display_error($sql);
    return db_fetch($result);
}

function document_id($id){
  
     $sql = "SELECT * FROM " . TB_PREF . "document_type WHERE id=" . db_escape($id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);

}

function delete_room_Master($selected_id) {
    $sql = "SELECT inactive FROM fa_room_master WHERE room_id=" . $selected_id;
    $result = db_query($sql, "The sales department could not be updated");
    $myrow = db_fetch($result);
    $bed_id = $myrow['inactive'];
    if ($bed_id == '1') {
        display_error(_('Currently Room in Used'));
    } else {
        display_notification(_('Record has been updated'));
        $sql = "UPDATE " . TB_PREF . "room_master  SET deleted ='1' WHERE room_id = " . db_escape($selected_id);
        // $sql = "DELETE FROM " . TB_PREF . "room_transition WHERE id=" . db_escape($selected_id);
        db_query($sql, "could not delete sales department");
    }
}

function delete_guest($selected_id) {
     $sql="SELECT inactive FROM fa_guest_registration WHERE guest_id=".$selected_id;
      $result= db_query($sql, "The Deleted could not be updated");
      $myrow= db_fetch($result);
      $bed_id= $myrow['inactive'];
      if($bed_id=='1'){
      display_error(_('Currently Room in Used'));
      }
      else{ 

    $sql = "UPDATE " . TB_PREF . "guest_registration  SET deleted ='1' WHERE guest_id = " . db_escape($selected_id);
    // $sql = "DELETE FROM " . TB_PREF . "room_transition WHERE guest_id=" . db_escape($selected_id);
    db_query($sql, "could not delete sales department");
    display_notification(_('Record has been updated'));
    }
}

function delete_transition_master($selected_id) {

    $sql = "SELECT inactive FROM fa_room_transition WHERE id=" . $selected_id;
    $result = db_query($sql, "The sales department could not be updated");
    $myrow = db_fetch($result);
    $bed_id = $myrow['inactive'];
    if ($bed_id == '1') {
        display_error(_('Currently Bed in Used'));
    } else {
        display_notification(_('Record has been updated'));
        $sql = "UPDATE " . TB_PREF . "room_transition  SET deleted ='1' WHERE id = " . db_escape($selected_id);
        // $sql = "DELETE FROM " . TB_PREF . "room_transition WHERE id=" . db_escape($selected_id);
        db_query($sql, "could not delete sales department");
    }
}


/*function add_leaves($fisc_year,$desig_group_id, $desig_id,$dept_id,$no_of_cls, $no_of_pls,$no_of_medical_ls,$no_of_spl_cls,$no_of_spl_cls_female,$no_of_mat_ls,$no_of_patern_ls,$cal_year,$empl_id){

	$sql = "INSERT INTO ".TB_PREF."kv_leave_master (fisc_year,designation_group_id,desig_id,dept_id,no_of_cls,no_of_pls,no_of_medical_ls,no_of_spl_cls,no_of_spl_cls_female,no_of_mat_ls,no_of_patern_ls,cal_year,empl_id) VALUES (".db_escape($fisc_year).",".db_escape($desig_group_id).",".db_escape($desig_id).",".db_escape($dept_id).",".db_escape($no_of_cls).",".db_escape($no_of_pls).",".db_escape($no_of_medical_ls).",".db_escape($no_of_spl_cls).",".db_escape($no_of_spl_cls_female).",".db_escape($no_of_mat_ls).",".db_escape($no_of_patern_ls).",".db_escape($cal_year).",".db_escape($empl_id).")";
	//display_error($sql);die;
   	db_query($sql,"The sales department could not be added");
}
 function get_leaves($show_inactive){
	$sql="SELECT leaves.*,cal_year as year,emp.empl_firstname as emp_name, des_group.name as grp_name,des.name as desig_name,dept.description as department,fiscal.begin AS f_styear,fiscal.end AS f_endyear FROM ".TB_PREF."kv_leave_master as leaves,".TB_PREF."kv_desig_group as des_group,".TB_PREF."kv_empl_info as emp,".TB_PREF."designation_master as des,".TB_PREF."kv_departments as dept,".TB_PREF."fiscal_year AS fiscal  where leaves.designation_group_id=des_group.id AND leaves.empl_id = emp.empl_id AND leaves.desig_id=des.id AND leaves.dept_id=dept.id AND leaves.fisc_year=fiscal.id ";
	if (!$show_inactive)
		$sql .= " AND !leaves.inactive";
	// $sql .= " ORDER BY name";
	return db_query($sql,"could not get Leaves master");
}*/
function check_for_duplicate($cal_year,$empl_id){
    $flag = 0;
    $result = get_leaves(check_value('show_inactive'));
    while ($myrow = db_fetch($result)) {
        if($myrow['cal_year']==$cal_year && $myrow['empl_id']==$empl_id){
            $flag = 1;
        }
    }
    return $flag;
}

function add_leaves($fisc_year,$desig_group_id, $desig_id,$dept_id,$no_of_cls, $no_of_pls,$no_of_medical_ls,$no_of_el,$no_of_spl_cls,$no_of_spl_cls_female,$no_of_mat_ls,$no_of_patern_ls,$cal_year,$empl_id,$no_of_cls_acess,$no_of_pls_acess,$no_of_el_acess,$no_of_medical_ls_acess,$no_of_spl_cls_acess,$no_of_spl_cls_female_acess,$no_of_mat_ls_acess,$no_of_patern_ls_acess,$cl_date, $vl_date, $el_date, $ml_date){   
        $flag=check_for_duplicate($cal_year,$empl_id);
        if($flag==0){
	$sql = "INSERT INTO ".TB_PREF."kv_leave_master (fisc_year,no_of_cls,no_of_pls,no_of_medical_ls,no_of_el,no_of_spl_cls,no_of_spl_cls_female,no_of_mat_ls,no_of_patern_ls,cal_year, empl_id,acess_cl,acess_vl,acess_el,acess_ml,acess_spl_male,acess_spl_female,acess_mat,acess_pat,updated_date,updated_date_vl,updated_date_el,updated_date_ml) VALUES (".db_escape($fisc_year).",".db_escape($no_of_cls).",".db_escape($no_of_pls).",".db_escape($no_of_medical_ls).",".db_escape($no_of_el).",".db_escape($no_of_spl_cls).",".db_escape($no_of_spl_cls_female).",".db_escape($no_of_mat_ls).",".db_escape($no_of_patern_ls).",".db_escape($cal_year).",".db_escape($empl_id).",".db_escape($no_of_cls_acess).",".db_escape($no_of_pls_acess).",".db_escape($no_of_el_acess).",".db_escape($no_of_medical_ls_acess).",".db_escape($no_of_spl_cls_acess).",".db_escape($no_of_spl_cls_female_acess).",".db_escape($no_of_mat_ls_acess).",".db_escape($no_of_patern_ls_acess).",".db_escape($cl_date).",".db_escape($vl_date).",".db_escape($el_date).",".db_escape($ml_date).")";
        } else {
            display_notification(_('duplicate value cannot be added !'));
        }//display_error($sql);die;
        //print_r($sql);
   	db_query($sql,"The sales department could not be added");
}

 function get_leaves($show_inactive){
	$sql="SELECT leaves.*,cal_year as year,emp.empl_id as emp_id,emp.empl_firstname as emp_name FROM ".TB_PREF."kv_leave_master as leaves,".TB_PREF."kv_empl_info as emp where leaves.empl_id = emp.empl_id";
	if ($show_inactive!=1)
		$sql .= " AND leaves.inactive = 0";
	 $sql .= " order by emp.empl_firstname,leaves.cal_year";
	return db_query($sql,"could not get Leaves master");
}

function update_leaves($selected_id,$fisc_year, $desig_group_id, $desig_id, $dept_id,$no_of_cls,$no_of_pls,$no_of_medical_ls,$no_of_el,$no_of_spl_cls,$no_of_spl_cls_female,$no_of_mat_ls,$no_of_patern_ls,$no_of_cls_acess,$no_of_pls_acess,$no_of_el_acess,$no_of_medical_ls_acess,$no_of_spl_cls_acess,$no_of_spl_cls_female_acess,$no_of_mat_ls_acess,$no_of_patern_ls_acess,$cl_date, $vl_date, $el_date, $ml_date, $cal_year){
    
	$sql = "UPDATE ".TB_PREF."kv_leave_master SET fisc_year =".db_escape($fisc_year).",cal_year =".$cal_year." , designation_group_id=".db_escape($desig_group_id).",desig_id=".db_escape($desig_id).",dept_id=".db_escape($dept_id).",no_of_cls=".db_escape($no_of_cls).",no_of_pls=".db_escape($no_of_pls).",no_of_medical_ls=".db_escape($no_of_medical_ls).",no_of_el=".db_escape($no_of_el).",no_of_spl_cls=".db_escape($no_of_spl_cls).",no_of_spl_cls_female=".db_escape($no_of_spl_cls_female).",no_of_mat_ls=".db_escape($no_of_mat_ls).",no_of_patern_ls=".db_escape($no_of_patern_ls).",acess_cl=".db_escape($no_of_cls_acess).",acess_vl=".db_escape($no_of_pls_acess).",acess_el=".db_escape($no_of_el_acess).",acess_ml=".db_escape($no_of_medical_ls_acess).",acess_spl_male=".db_escape($no_of_spl_cls_acess).",acess_spl_female=".db_escape($no_of_spl_cls_female_acess).",acess_mat=".db_escape($no_of_mat_ls_acess).",acess_pat=".db_escape($no_of_patern_ls_acess).",updated_date=".db_escape($cl_date).",updated_date_vl=".db_escape($vl_date).", updated_date_el=".db_escape($el_date).",updated_date_ml =".db_escape($ml_date)." WHERE leave_id = ".db_escape($selected_id);

   	db_query($sql,"The Leaves could not be updated");
        
}
function get_leaveedit($selected_id){
	$sql = "SELECT * FROM ".TB_PREF."kv_leave_master WHERE leave_id=".db_escape($selected_id);
  //display_error($sql);
	$result = db_query($sql,"could not get Leaves Master");
	return db_fetch($result);
}

function delete_leave($selected_id){
	$sql="DELETE FROM ".TB_PREF."kv_leave_master WHERE leave_id=".db_escape($selected_id);
	db_query($sql,"could not delete Leaves Master");
}
//--------------------------------Type of Leave Master-----------------------------------/

function add_leaves_type($leave_type, $field_name, $desciption){
	$sql = "INSERT INTO ".TB_PREF."kv_type_leave_master(leave_type,field_name,desciption) VALUES (".db_escape($leave_type).",".db_escape($field_name).",".db_escape($desciption).")";
	//display_error($sql);die;
   	db_query($sql,"The sales department could not be added");
}
 function get_leaves_type($show_inactive){
	$sql="SELECT * FROM ".TB_PREF."kv_type_leave_master ";
	if (!$show_inactive)
		$sql .= " WHERE !inactive";
	 $sql .= " ORDER BY type_id";
	return db_query($sql,"could not get Leaves Type master");
}
function update_leaves_type($selected_id, $leave_type,$field_name, $desciption){
	$sql = "UPDATE ".TB_PREF."kv_type_leave_master SET field_name=".db_escape($field_name).",leave_type=".db_escape($leave_type).",desciption=".db_escape($desciption)." WHERE type_id = ".db_escape($selected_id);
	// display_error($sql);
   	db_query($sql,"The Type of Leave could not be updated");
}
function get_leave_type_edit($selected_id){ 
	$sql = "SELECT * FROM ".TB_PREF."kv_type_leave_master WHERE type_id=".db_escape($selected_id);
  //display_error($sql);
	$result = db_query($sql,"could not get Leave Type Master");
	return db_fetch($result);
}

function delete_leave_type($selected_id){
	$sql="DELETE FROM ".TB_PREF."kv_type_leave_master WHERE type_id=".db_escape($selected_id);
	db_query($sql,"could not delete Leaves Type Master");
}
//---------------------------------------Type of Leave List-----------------------------/
function leave_type_lists($name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$gender='')
{
   
    if($gender == 1){
		$sql ="SELECT type_id,leave_type FROM ".TB_PREF."kv_type_leave_master WHERE type_id !=4";
	}
	else{
		$sql ="SELECT type_id,leave_type FROM ".TB_PREF."kv_type_leave_master";
	}
	return combo_input($name, $selected_id, $sql, 'type_id', 'leave_type',
 	array('order'=>'type_id',
		'spec_option' => $spec_opt,
		//'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
                
 		'async' => true
 	));
}

function leave_type_lists_type($name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$gender='',$employee_type = 1)
{

$sql ="SELECT Distinct master.type_id,master.leave_type FROM ".TB_PREF."kv_type_leave_master as master join ".TB_PREF."kv_leave_encash as encash on encash.leave_type = master.type_id where employee_type = $employee_type";
	
	return combo_input($name, $selected_id, $sql, 'type_id', 'leave_type',
 	array('order'=>'type_id',
		'spec_option' => $spec_opt,
		//'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
                
 		'async' => true
 	));
}

function leavetype_list_cell($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$gender=''){
    echo "<td>";
	echo leave_type_lists($name, $selected_id,$spec_opt, $submit_on_change,$gender);
	echo "</td>\n";
}

function leave_type_list_cell($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false,$gender='')
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo leave_type_lists($name, $selected_id,$spec_opt, $submit_on_change,$gender);
	echo "</td>\n";
}

function leave_type_list_cell_type($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false,$gender='',$employee_type =1)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo leave_type_lists_type($name, $selected_id,$spec_opt, $submit_on_change,$gender,$employee_type);
	echo "</td>\n";
}


function leavetype_list_row($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$gender='')
{
	echo "<tr><td class='label'>$label</td>";
	leave_type_list_cell(null, $name, $selected_id,$spec_opt, $submit_on_change,$gender);
	echo "</tr>\n";
}

function leavetype_list_row_type($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$gender='',$employee_type = 1)
{
	echo "<tr><td class='label'>$label</td>";
	leave_type_list_cell_type(null, $name, $selected_id,$spec_opt, $submit_on_change,$gender, $employee_type);
	echo "</tr>\n";
}
//---------------------------------------Ocassion for encashment-----------------------------/
function occas_type_lists($name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$gender='')
{$sql ="SELECT distinct master.id, master.occ_name FROM ".TB_PREF."kv_leave_encash as encash join ".TB_PREF."kv_occasion_master as master on master.id = encash.occas_encash  WHERE encash.employee_type =". $_POST['empl_type']." AND encash.leave_type =".$_POST['leave_type']." AND !encash.inactive AND !master.inactive";	
	return combo_input($name, $selected_id, $sql, 'id', 'occ_name',
 	array('order'=>'occas_encash',
		'spec_option' => $spec_opt,
		//'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
                
 		'async' => true
 	));
}

function occ_type($name, $selected_id=null,$spec_opt=false, $submit_on_change=false){
    
		$sql ="SELECT id, occ_name FROM ".TB_PREF."kv_occasion_master WHERE !inactive";
	
	return combo_input($name, $selected_id, $sql, 'id', 'occ_name',
 	array('order'=>'occ_name',
		'spec_option' => $spec_opt,
		//'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
                
 		'async' => true
 	));
}


function occ_list_cell($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false){
    	
	echo "<td>";
	echo occ_type($name, $selected_id,$spec_opt, $submit_on_change,$gender,$employee_type);
	echo "</td></tr>\n";;
}






function occ_list_row($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false){
    	echo "<tr><td class='label'>$label</td>";
	occ_list_cell(null, $name, $selected_id,$spec_opt, $submit_on_change);
	
}



function occas_type_list_cell($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false,$gender='')
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo occas_type_lists($name, $selected_id,$spec_opt, $submit_on_change,$gender);
	echo "</td>\n";
}


function ocassion_list_row($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$gender='')
{
	echo "<tr><td class='label'>$label</td>";
	occas_type_list_cell(null, $name, $selected_id,$spec_opt, $submit_on_change,$gender);
	echo "</tr>\n";
}

//-----------------------earning types----------------------------------//
function empl_earning_list($name, $selected_id = null,$spec_opt = false, $submit_on_change = false,$height = false ) {
	$sql = 'select id,description from '.TB_PREF.'kv_allowances where type = "Earnings"';
	return combo_input($name, $selected_id, $sql, 'id', 'description',
 	array('order'=>'description',
		'spec_option' => FALSE,
		//'spec_id' => -1,
                'height' => $height,
 		'select_submit'=> $submit_on_change,
                'multi'=> true,
 		'async' => true
 	));
}



function empl_earning_list_cell($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false,$gender='',$height = false)
{ 
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo empl_earning_list($name, $selected_id,$spec_opt, $submit_on_change,$height);
	echo "</td></tr>\n";
}

function empl_earning_list_row($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$gender='',$height = false)
{
   
	echo "<tr><td class='label'>$label</td>";
	empl_earning_list_cell(null, $name, $selected_id,$spec_opt, $submit_on_change,$gender,$height);

}

//---------------------student type list row----------------------------------//

function student_type_lists($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $stu_id_arr = array()) {
       
    $stu_id = implode(',', $stu_id_arr);
    $sql = "SELECT stu_id, CONCAT(stu_fname, ' ', stu_lname, ' -',stu_id) as firstlast FROM erp_student_information WHERE stu_status != 2";
    $sql_part = "AND stu_id IN ($stu_id)";
    if ($stu_id != '') {
      $sql .= $sql_part;
    } else {
        display_error(_("Not any student alloted "));
        $sql = " ";
    }

    return combo_input($name, $selected_id, $sql, 'stu_id', 'firstlast', array('order' => 'stu_id',
        'spec_option' => $spec_opt,
        //'spec_id' => -1,
        'select_submit' => $submit_on_change,
        'async' => false
    ));
}

function guest_type_lists($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $stu_id_arr = array()) {

    $stu_id = implode(',', $stu_id_arr);
    $sql = "SELECT guest_id, CONCAT(guest_name, ' ', guest_id) as firstlast FROM fa_guest_registration ";
    $sql_part = " WHERE guest_id IN ($stu_id)";
    if ($stu_id != '') {
        $sql .= $sql_part;
    } else {
        display_error(_("Not any student alloted "));
        $sql = " ";
    }

    return combo_input($name, $selected_id, $sql, 'guest_id', 'firstlast', array('order' => 'guest_id',
        'spec_option' => $spec_opt,
        //'spec_id' => -1,
        'select_submit' => $submit_on_change,
        'async' => false
    ));
}


function faculty_type_lists($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $stu_id_arr = array()) {

    $stu_id = implode(',', $stu_id_arr);
    $sql = "SELECT empl_id, CONCAT(empl_firstname, ' ', empl_id) as firstlast FROM fa_kv_empl_info WHERE status = 1  ";
    $sql_part = "AND empl_id LIKE 'EMP-F%' AND empl_id IN ($stu_id)";
    if ($stu_id != '') {
        $sql .= $sql_part;
    } else {
        display_error(_("Not any student alloted "));
        $sql = " ";
    }

    return combo_input($name, $selected_id, $sql, 'empl_id', 'firstlast', array('order' => 'empl_id',
        'spec_option' => $spec_opt,
        //'spec_id' => -1,
        'select_submit' => $submit_on_change,
        'async' => false
    ));
}


function staff_type_lists($name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $stu_id_arr = array()) {

    $stu_id = implode(',', $stu_id_arr);
    $sql = "SELECT empl_id, CONCAT(empl_firstname, ' ', empl_id) as firstlast FROM fa_kv_empl_info ";
    $sql_part = " WHERE empl_id LIKE 'EMP-S%' AND empl_id IN ($stu_id)";
    if ($stu_id != '') {
        $sql .= $sql_part;
    } else {
        display_error(_("Not any student alloted "));
        $sql = " ";
    }

    return combo_input($name, $selected_id, $sql, 'empl_id', 'firstlast', array('order' => 'empl_id',
        'spec_option' => $spec_opt,
        //'spec_id' => -1,
        'select_submit' => $submit_on_change,
        'async' => false
    ));
}

function student_type_list_cell($label, $name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $stu_id) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo student_type_lists($name, $selected_id, $spec_opt, $submit_on_change, $stu_id);
    echo "</td>\n";
}


function guest_type_list_cell($label, $name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $stu_id) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo guest_type_lists($name, $selected_id, $spec_opt, $submit_on_change, $stu_id);
    echo "</td>\n";
}


function faculty_type_list_cell($label, $name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $stu_id) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo faculty_type_lists($name, $selected_id, $spec_opt, $submit_on_change, $stu_id);
    echo "</td>\n";
}

function staff_type_list_cell($label, $name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $stu_id) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo staff_type_lists($name, $selected_id, $spec_opt, $submit_on_change, $stu_id);
    echo "</td>\n";
}

function studentType_list_row($label, $name, $selected_id = null, $spec_opt = false, $submit_on_change = false, $id) {
    echo "<tr><td class='label'>$label</td>";
    $result = mysqli_query("SELECT stu_id FROM fa_alloted_room WHERE inactive=1");
    $stu_id = Array();
    while ($row = mysqli_fetch_array($result)) {
        $stu_id[] = "'".$row['stu_id']."'";
    }

    switch ($id) {
        case 1:
             guest_type_list_cell(null, $name, $selected_id, $spec_opt, $submit_on_change, $stu_id);
            break;
        case 2:
            set_global_connection(2);
            student_type_list_cell(null, $name, $selected_id, $spec_opt, $submit_on_change, $stu_id);
            set_global_connection(0);
            break;
        case 3:
            faculty_type_list_cell(null, $name, $selected_id, $spec_opt, $submit_on_change, $stu_id);
            break;
        case 4:
            staff_type_list_cell(null, $name, $selected_id, $spec_opt, $submit_on_change, $stu_id);
            break;
    }
    echo "</tr>\n";
}
//---------------------------------------Yes/No Select box-----------------------------/
function custom_list_cell($label, $name, $selected_id=null, $submit_on_change=false,$disable=FALSE, $dd_type = 'yes_no_select_box')
{
        global $config_master_value;
        $list1 = $config_master_value[$dd_type];
        $options = array(
            'select_submit'=> $submit_on_change,
            'disabled' => $disable
        );
        $select_data = array_selector($name, $selected_id, $list1, $options);
        
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo $select_data;
	echo "</td>\n";
}
function custom_list_row($label, $name, $selected_id=null,$submit_on_change=false,$disable=FALSE,$dd_type = 'yes_no_select_box')
{
	echo "<tr><td class='label'>$label</td>";
	custom_list_cell(null, $name, $selected_id,$submit_on_change,$disable,$dd_type);
	echo "</tr>\n";
}

//--------------------------------Second Type of Leave----------------------------------/
function leave_type_lists2($name, $selected_id=null,$spec_opt=false, $submit_on_change=false)
{
	$sql ="SELECT type_id,leave_type FROM ".TB_PREF."kv_type_leave_master";
	return combo_input($name, $selected_id, $sql, 'type_id', 'leave_type',
 	array('order'=>'type_id',
		'spec_option' => $spec_opt === true ? _("Select Leave") : $spec_opt,
		'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
 		'async' => true
 	));
}

function leave_type_list_cell2($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo leave_type_lists2($name, $selected_id,$spec_opt, $submit_on_change);
	echo "</td>\n";
}

function leavetype_list_row2($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	leave_type_list_cell2(null, $name, $selected_id,$spec_opt, $submit_on_change);
	echo "</tr>\n";
}

//-------------------------------------------------------------------------------------//

//--------------------------------------------------------------------------------------/
//-------------------------------------Holiday Master-----------------------------------/
 function add_holiday($fisc_year, $name,$descpt,$from_date,$to_date){

	$sql = "INSERT INTO ".TB_PREF."kv_holiday_master(fisc_year,name,descpt,from_date,to_date) VALUES (".db_escape($fisc_year).",".db_escape($name).",".db_escape($descpt).",".db_escape($from_date).",".db_escape($to_date).")";
	//display_error($sql);die;
   	db_query($sql,"Holiday could not be added");
}
 function get_holidays($show_inactive){
	$sql="SELECT holiday.*,fiscal.begin AS f_styear,fiscal.end AS f_endyear FROM ".TB_PREF."kv_holiday_master AS holiday,".TB_PREF."fiscal_year AS fiscal WHERE  holiday.fisc_year=fiscal.id";
	if (!$show_inactive)
	 $sql .= " AND !holiday.inactive";
	 $sql .= " ORDER BY holiday.holiday_id";
	return db_query($sql,"could not get Holidays");
}

function get_calendar($fisc,$show_inactive){
    //set_global_connection(1);
	$sql = "SELECT * FROM ".TB_PREF."kv_holiday_master WHERE fisc_year=".db_escape($fisc['id']);
        if($show_inactive)
            $sql.=" inactive = $show_inactive";
	return db_query($sql,"could not get Calendar");
}
function update_holiday($selected_id, $fisc_year, $name,$descpt,$from_date,$to_date){
	$sql = "UPDATE ".TB_PREF."kv_holiday_master SET fisc_year=".db_escape($fisc_year).",name=".db_escape($name).",descpt=".db_escape($descpt).",from_date=".db_escape($from_date).",to_date=".db_escape($to_date)." WHERE holiday_id = ".db_escape($selected_id);
	// display_error($sql);
   	db_query($sql,"Holiday could not be updated");
}
function get_holiday_edit($selected_id){ 
	$sql = "SELECT * FROM ".TB_PREF."kv_holiday_master WHERE holiday_id=".db_escape($selected_id);
  //display_error($sql);
	$result = db_query($sql,"could not get Holiday");
	return db_fetch($result);
}

function delete_holiday($selected_id){
	$sql="DELETE FROM ".TB_PREF."kv_holiday_master WHERE holiday_id=".db_escape($selected_id);
	db_query($sql,"could not delete Holiday ");
} 

function get_holidays_from_fiscalyear($year) {

	$empl_id_list=array();
	$sql= "SELECT * FROM ".TB_PREF."kv_holiday_master WHERE  fisc_year=".db_escape($year)."";
 
	$result = db_query($sql, "could not get Holidays list");
   
	while($get_des=db_fetch($result))
		$holy_id_list[]=$get_des['holiday_id'];
		
	return $holy_id_list;
}
//-------------------------------------------------------------------------------------//
//--------------------Designation Group List from Employee master----------------------

function designation_group_lists($name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$dept_id)
{
	$sql ="SELECT dsg.id AS id,dsg.name AS name FROM ".TB_PREF."kv_desig_group AS dsg,".TB_PREF."kv_empl_job AS job WHERE job.desig_group=dsg.id AND job.department=".db_escape($dept_id)."";
	$sql .= " GROUP BY id";
	return combo_input($name, $selected_id, $sql, 'id', 'name',
 	array('order'=>'name',
		'spec_option' => $spec_opt,
		//'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
 		'async' => true
 	));
}

function designation_group_list_cell($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false,$dept_id)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo designation_group_lists($name, $selected_id,$spec_opt, $submit_on_change,$dept_id);
	echo "</td>\n";
}

function designationgroup_list_row($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$dept_id)
{
	echo "<tr><td class='label'>$label</td>";
	designation_group_list_cell(null, $name, $selected_id,$spec_opt, $submit_on_change,$dept_id);
	echo "</tr>\n";
}
//-------------------------------------------------------------------------------------//
//------------------------------Designation from Employee Master------------------------
function designat_lists($name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$desig_group)
{

	$sql ="SELECT desg.id AS id,desg.name AS name FROM ".TB_PREF."designation_master AS desg,".TB_PREF."kv_empl_job AS job WHERE job.desig = desg.id AND desg.desig_group_id = ".$desig_group."";
	$sql .= " GROUP BY desg.id";
	
	return combo_input($name, $selected_id, $sql, 'id', 'description',
 	array('order'=>'name',
		'spec_option' => $spec_opt,
		'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
 		'async' => true
 	));
	
}

function designat_list_cell($label, $name, $selected_id = null, $spec_opt = false, $submit_on_change=false,$desig_group)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo designat_lists($name, $selected_id,$spec_opt, $submit_on_change,$desig_group);
	echo "</td>\n";
}


function designt_list_row($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$desig_group)
{
	echo "<tr><td class='label'>$label</td>";
	designat_list_cell(null, $name, $selected_id,$spec_opt, $submit_on_change,$desig_group);
	echo "</tr>\n";
}

function get_leave_wekeend_status($empl_id, $leave_type, $cal_year)
{
    $sql = "select leaves.weekend_status from fa_kv_leave_days as leaves, fa_kv_empl_job as job where leaves.employee_type = job.employee_type And job.empl_id=".db_escape($empl_id)." And leaves.leave_type = ".db_escape($leave_type)." And leaves.cal_year = ".db_escape($cal_year);
 // display_error($sql);
    $result = db_query($sql,"unable to get value.");
    return db_fetch($result);           
}
function bool1($id) {
    if ($id == 1) {
        return 'YES';
    } else {
        return 'NO';
    }
}
    


//------------------------------------------------------------------------------------//
