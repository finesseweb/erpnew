<?php
// ----------------------------------------------------------------
// Creator: Kvvaradha
// email:   admin@kvcodes.com
// Title:   HRM Extention for 
// website: http://www.kvcodes.com
// ----------------------------------------------------------------

//---------------------------------------------------------------------------------------------------

function employee_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false,$type = "employees"){
	global $all_items;

	$sql = "SELECT empl.empl_id, empl.empl_firstname, dept.description FROM ".TB_PREF."kv_empl_info empl,".TB_PREF."kv_empl_job job, ".TB_PREF."kv_departments dept WHERE empl.empl_id=job.empl_id AND job.department=dept.id";
//display_error($sql); 
	if ($editkey)
		set_editor('employee', $name, $editkey);
	$mode = 0;
	$ret = combo_input($name, $selected_id, $sql, 'empl.empl_id', 'empl.empl_firstname',
	array_merge(
	  array(
		'format' => '_format_empl',
		'spec_option' => $all_option===true ?  _("All Items") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'type' => 1,
		'search' => array("empl.empl_id", "dept.description","empl.empl_firstname"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>20,
		'async' => false,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('job.department','empl.empl_id'), array('dept.description','empl.empl_firstname'),
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new employee') :	_('Select employee'),
		'show_inactive' => $show_inactive
	  )), $type) ;
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}

function _format_empl($row){
	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1];
}

function employee_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $all=false, $editkey = false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo employee_list($name, $selected_id, $all_option, $submit_on_change,	array('cells'=>true, 'show_inactive'=>$all), $editkey);
	echo "</td>\n"; 
}

function empl_img_name($empl_id){
	$empl_id = strtr($empl_id, "><\\/:|*?", '________');
	return clean_file_name($empl_id);
}

//-----------------------------------------------------------------------------------
function kv_image_cells($label, $name, $id=""){
	if ($id != "")
		$id = "id='$id'";
	label_cells($label, "<input type='file' name='$name' $id  'image/*'/>");
}		
function kv_image_row($label, $name, $id = ""){
	echo "<tr><td class='label'>$label</td>";
	kv_image_cells(null, $name, $id);
	echo "</tr>\n";
}	
//-----------------------------------------------------------------------------------
function kv_doc_cells($label, $name, $id="",$multiple=false){
	if ($id != "")
		$id = "id='$id'";
        if($multiple)
            $multiple = "multiple='multiple'";
      
	label_cells($label, "<input type='file' name='$name' $id  accept='.doc,.docx, .rtf, .txt,.png,.jpg,.pdf' $multiple/>");
}		
function kv_doc_row($label, $name, $id = "",$multiple=false){
	echo "<tr><td class='label'>$label</td>";
	kv_doc_cells(null, $name, $id,$multiple);
	echo "</tr>\n";
}





//-----------------------------------Employee Name--------------------------------------/
function employeename_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false,$department,$designation,$desig_group){
	global $all_items;
	$sql = "SELECT empl.empl_id, empl.empl_firstname FROM ".TB_PREF."kv_empl_info empl,".TB_PREF."kv_empl_job job, ".TB_PREF."kv_departments dept WHERE job.department=$department AND job.desig=$designation AND job.desig_group=$desig_group AND empl.empl_id=job.empl_id AND (empl.status = '1' OR empl.status = '2') GROUP BY empl.id";

	if ($editkey)
		set_editor('employee', $name, $editkey);
	$mode = 0;
	$ret = combo_input($name, $selected_id, $sql, 'empl.empl_id', 'empl.empl_firstname',
	array_merge(
	  array(
		'format' => '_format_empl',
		'spec_option' => $all_option===true ?  _("All Items") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'type' => 1,
		'search' => array("empl.empl_id", "empl.empl_firstname"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>20,
		'async' => false,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('empl.empl_id','job.department'),
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new employee') :	_('Select employee'),
		'show_inactive' => $show_inactive
	  )) );

//display_error($ret);
	//if ($editkey)
	//	$ret .= add_edit_combo('item');		
	return $ret;
	  
}
function employee_list_active($name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false){
	global $all_items;

	$sql = "SELECT empl.empl_id, empl.empl_firstname, dept.description FROM ".TB_PREF."kv_empl_info empl,".TB_PREF."kv_empl_job job, ".TB_PREF."kv_departments dept WHERE empl.empl_id=job.empl_id AND job.department=dept.id and empl.status=1";
//display_error($sql); 
	if ($editkey)
		set_editor('employee', $name, $editkey);
	$mode = 0;
	$ret = combo_input($name, $selected_id, $sql, 'empl.empl_id', 'empl.empl_firstname',
	array_merge(
	  array(
		'format' => '_format_empl',
		'spec_option' => $all_option===true ?  _("All Items") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'type' => 1,
		'search' => array("empl.empl_id", "dept.description","empl.empl_firstname"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>20,
		'async' => false,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('job.department','empl.empl_id'), array('dept.description','empl.empl_firstname'),
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new employee') :	_('Select employee'),
		'show_inactive' => $show_inactive
	  )) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}
function employee_list_cells_active($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $all=false, $editkey = false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo employee_list_active($name, $selected_id, $all_option, $submit_on_change,	array('cells'=>true, 'show_inactive'=>$all), $editkey);
	echo "</td>\n"; 
}


function dispatch_mode_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false){
	global $all_items;

	$sql = "SELECT id, name FROM ".TB_PREF."dispatch_mode  WHERE status = 0";

	if ($editkey)
		set_editor('employee', $name, $editkey);
	$mode = 0;
	$ret = combo_input($name, $selected_id, $sql, 'id', 'name',
	array_merge(
	  array(
		'format' => '_format_empl',
		'spec_option' => $all_option===true ?  _("All Items") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'type' => 1,
		'search' => array("name"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>20,
		'async' => false,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('name'),
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new employee') :	_('Select employee'),
		'show_inactive' => $show_inactive
	  )) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}




function document_type_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false){
	global $all_items;

	$sql = "SELECT id, name FROM ".TB_PREF."document_type  WHERE status = 0";
//display_error($sql); 
	if ($editkey)
		set_editor('name', $name, $editkey);
	$mode = 0;
	$ret = combo_input($name, $selected_id, $sql, 'id', 'name',
	array_merge(
	  array(
		'format' => '_format_empl',
		'spec_option' => $all_option===true ?  _("All Items") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'type' => 1,
		'search' => array("name"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>20,
		'async' => false,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('name'),
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new employee') :	_('Select employee'),
		'show_inactive' => $show_inactive
	  )) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}


function dispatch_mode_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $all=false, $editkey = false){
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td >";
	echo dispatch_mode_list($name, $selected_id, $all_option, $submit_on_change,	array('cells'=>true, 'show_inactive'=>$all), $editkey);
	echo "</td>\n";
}


function dispatch_mode_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $all=false, $editkey = false){
	
	echo "<tr nowrap>";
	echo dispatch_mode_list_cells($label,$name, $selected_id, $all_option, $submit_on_change,	array('cells'=>true, 'show_inactive'=>$all), $editkey);
	echo "</tr>\n";
}





function document_type_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $all=false, $editkey = false){
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td nowrap>";
	echo document_type_list($name, $selected_id, $all_option, $submit_on_change,	array('cells'=>true, 'show_inactive'=>$all), $editkey);
	echo "</td>\n";
}


function document_type_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $all=false, $editkey = false){
	
	echo "<tr >";
	echo document_type_list_cells($label,$name, $selected_id, $all_option, $submit_on_change,	array('cells'=>true, 'show_inactive'=>$all), $editkey);
	echo "</tr>\n";
}



function employeename_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $all=false, $editkey = false,$department,$designation,$desig_group){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo employeename_list($name, $selected_id, $all_option, $submit_on_change,	array('cells'=>true, 'show_inactive'=>$all),$editkey,$department,$designation,$desig_group);
	echo "</td>\n";
}
//--------------------------------------------------------------------------------------//
//ramesh leave request search 
function employee_list1($name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false,$dept_id){
	global $all_items;
	
	$sql = "SELECT empl.empl_id, empl.empl_firstname, dept.description FROM ".TB_PREF."kv_empl_info empl,".TB_PREF."kv_empl_job job, ".TB_PREF."kv_departments dept WHERE empl.empl_id=job.empl_id AND job.department=dept.id AND (empl.status = '1' OR empl.status = '2') ";
if ($dept_id != null)
		$sql .= " AND job.department = ".db_escape($dept_id);
	//display_error($sql);
	if ($editkey)
		set_editor('employee', $name, $editkey);
	$mode = 0;
	$ret = combo_input($name, $selected_id, $sql, 'empl.empl_id', 'empl.empl_firstname',
	array_merge(
	  array(
		'format' => '_format_empl1',
		'spec_option' => $all_option===true ?  _("All Items") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'type' => 1,
		'search' => array("empl.empl_id", "dept.description","empl.empl_firstname"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>20,
		'async' => false,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('dept.description','empl.empl_firstname'),
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new employee') :	_('Select employee'),
		'show_inactive' => $show_inactive
	  )) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}

function _format_empl1($row){
	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1];
}

function employee_list_cells1($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $all=false, $editkey = false,$dept_id=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo employee_list1($name, $selected_id, $all_option, $submit_on_change,	array('cells'=>true, 'show_inactive'=>$all), $editkey, $dept_id);
	echo "</td>\n";
}

?>