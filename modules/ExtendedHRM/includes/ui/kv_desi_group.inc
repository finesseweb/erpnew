<?php 


 function  db_has_departments_exist(){
	$result = db_query("SELECT COUNT(*) FROM ".TB_PREF."kv_departments", "Can't Select department table");
	if(!$result) {		
		return  false; 
	 } else return true ; 	
} 

function kv_create_desi_group_db() {
	
		$sql= "CREATE TABLE IF NOT EXISTS `".TB_PREF."kv_desi_group` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '','name' varchar(60)NOT NULL DEFAULT '', 
  `description` varchar(60) NOT NULL DEFAULT '',
 
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;" ; 

	return db_query($sql, "Db Table creation failed, Kv Manufacturer table");
}
function add_desi_group($description){
$sql = "INSERT INTO ".TB_PREF."kv_desi_group (description) VALUES (".db_escape($description) .",".db_escape($description) . ")";
   	db_query($sql,"The Designation could not be added");
}

function update_department($selected_id, $description){
	$sql = "UPDATE ".TB_PREF."kv_desi_group SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
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


//------------------------------------------------------------------------------------
function department_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false){
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."kv_departments";

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
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('department');
	return $ret;
}

function designation_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo department_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n";
}

function designation_list_row($label, $name, $selected_id=null, $all_option = false, $submit_on_change=false, $show_inactive=false, $editkey = false){
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo department_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n</tr>\n";
}


?>