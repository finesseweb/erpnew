<?php
// ----------------------------------------------------------------
// Creator: Kvvaradha
// email:   admin@kvcodes.com
// Title:   HRM Extention for 
// website: http://www.kvcodes.com
// ----------------------------------------------------------------
global $kv_empl_gender, $hrm_empl_status, $hrm_empl_salutation, $kv_empl_mop, $kv_empl_type; 

$hrm_empl_status = array(
    1 => _('Active'),  
    2 => _('Suspened'), 
    3 => _('Deceased'), 
    4 => _('Retired'), 
    5 => _('Terminated'),
    6 => _('Resigned') 
);

$hrm_empl_salutation = array(
    1 => _('Mr.'),  
    2 => _('Ms.'), 
    3 => _('Mrs.'), 
    4 => _('Dr.'), 
    5 => _('Prof.'), 
    6 => _('Other.'), 
);
$kv_empl_gender = array(
    1 => _('Male'),  
    2 => _('Female'), 
    3 => _('Transgender'), 

);
$kv_empl_type = array(
	1 => _('Technical'),
        2 => _('Non Technical'),
	//3 => _('Visiting Faculty(Practitioner)'),
//        4 => _('Visiting Faculty(Academic)')
        );
	
$kv_empl_mop = array(
    1 => _('Cash'),  
    2 => _('Bank'), 
    3 => _('Cheque'), 

);




$kv_empl_hra_eligible = array(
	1 => _('Yes'),
	2 => _('No'),
);

$kv_empl_cal_year = array();
for($i=2017; $i<2030; $i++){
$kv_empl_cal_year[$i] = $i;
}
update_leave_encashment_cal_yer();
function kv_empl_gender_list_row($label, $name, $selected_id=null, $submit_on_change=false) {
	global $kv_empl_gender;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $kv_empl_gender, $options);
	echo "</td></tr>\n";
}





function kv_cal_year_list_row($label, $name, $selected_id=null, $submit_on_change=false) {
	global $kv_empl_cal_year;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $kv_empl_cal_year, $options);
	echo "</td></tr>\n";
}

function kv_empl_type_list_row($label, $name, $selected_id=null, $submit_on_change=false) {
	global $kv_empl_type;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $kv_empl_type, $options);
	echo "</td></tr>\n";
}









function kv_empl_hra_eligible_row($label, $name, $selected_id=null, $submit_on_change=false) {
	global $kv_empl_hra_eligible;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $kv_empl_hra_eligible, $options);
	echo "</td></tr>\n";
}

function  kv_empl_salutation_list_row($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_empl_salutation;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $hrm_empl_salutation, $options);
	echo "</td></tr>\n";
}
//--------------------------

function  hrm_empl_status_list_search($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_empl_status;
		
	echo "<td class=''>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $hrm_empl_status, $options);
	echo "</td>";
}


//------------------------
function  hrm_empl_status_list($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_empl_status;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $hrm_empl_status, $options);
	echo "</td></tr>\n";
}

function  hrm_empl_mop_list($label, $name, $selected_id=null, $submit_on_change=false) {
	global $kv_empl_mop;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $kv_empl_mop, $options);
	echo "</td></tr>\n";
}

//-----------------------------------------------------------------------------------------------

function country_list($name, $selected_id=null,$submit_on_change,$disabled=false){

	///$sql = "SELECT COALESCE(id,99) AS id, countries_name FROM ".TB_PREF."kv_countries";
	//display_error($sql);
		$sql = "select id,countries_name FROM ".TB_PREF."kv_countries where id = 99 
		union
		select id,countries_name FROM ".TB_PREF."kv_countries where not id = 99";
		
	return combo_input($name, $selected_id, $sql, 'id', 'countries_name', array('select_submit'=> $submit_on_change,'disabled'=>$disabled,'order'=>''));
}

function country_list_cells($label, $name, $selected_id=null,$submit_on_change,$disabled=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo country_list($name, $selected_id,$submit_on_change,$disabled);
	echo "</td>\n";
}

function country_list_row($label, $name, $selected_id=null,$submit_on_change=false,$disabled=false){
	echo "<tr><td class='label'>$label</td>";
	country_list_cells(null, $name, $selected_id,$submit_on_change,$disabled);
	echo "</tr>\n";
}






//------------------------------------------------------------
$hrm_empl_workings_days = array(
    'Sun' => _('Sunday'),  
    'Mon' => _('Monday'), 
    'Tue' => _('Tuesday'), 
    'Wed' => _('Wednesday'), 
    'Thu' => _('Thursday'), 
    'Fri' => _('Friday'), 
    'Sat' => _('Saturday')
);


$hrm_w_o_no = array(
     1 => _('1'),  
    2 => _('2'), 
    3 => _('3'), 
    4 => _('4'), 
    5 => _('5'), 
    6 => _('6'), 
    7 => _('7') 
);


$half_day_or_full_day = array(
    'half' => _('Half Day'),
    'full' => _('Full Day'),
);

function  hrm_empl_workings_days($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_empl_workings_days;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $hrm_empl_workings_days, $options);
	echo "</td></tr>\n";
}

function  hrm_empl_workings_days_no($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_w_o_no;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
            'async' => false
	);
	echo array_selector($name, $selected_id, $hrm_w_o_no, $options);
	echo "</td></tr>\n";
}



//------------------------------------------------------------
$hrm_empl_ref_type = array(
    0 => _('Manual'),  
    1 => _('Automatic')    
);

function  hrm_empl_ref_type($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_empl_ref_type;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $hrm_empl_ref_type, $options);
	echo "</td></tr>\n";
}

function kv_text_rows($label, $name, $size, $max=null, $submit_on_change=false, $title=null, $init=null, $labparams=null, $post_label=null){
  	global $Ajax;

	default_focus($name);
	if (!isset($_POST[$name]) || $_POST[$name] == "")	{
		if ($init)
			$_POST[$name] = $init;
		else
			$_POST[$name] = "";
	}
	if ($label != null){
		echo '<tr>';
		label_cell($label, $labparams);		
	}

	if (!isset($max))
		$max = $size;

	echo "<td>";
	$class = $submit_on_change ? 'class="searchbox"' : '';
	echo "<input $class type=\"text\" name=\"$name\" size=\"$size\" maxlength=\"$max\" value=\"" . $_POST[$name]. "\""
	 .($title ? " title='$title'": '')." >";

	if ($post_label)
		echo " " . $post_label;

	echo "</td>\n";
	if ($label != null)
	echo '</tr>';
	$Ajax->activate('_page_body');
}

//-----------------------------------------------------------------------------------
function kv_radio($label, $name, $value, $selected=null, $submit_on_change=false, $disabled='',$id='',$change=''){
	
	$change = 'select_leave(this.value,name)';
	if (!isset($selected))
		$selected = get_post($name) == $value;

	if ($submit_on_change === true)
		$submit_on_change = 
			"JsHttpRequest.request(\"_{$name}_update\", this.form);";

	return "<input type='radio' {$disabled} name=$name value='$value' ".($selected ? "checked":'')
	    .($submit_on_change ? " onclick='$submit_on_change'" : '')
		." id=$id onchange='$change'>".($label ? $label : '');
}

function kv_basic_row($label, $name, $size, $max=null, $init=null, $submit_on_change=false,$btn = false){
  	global $Ajax;

  	echo "<tr>";
	default_focus($name);
	if (!isset($_POST[$name]) || $_POST[$name] == ""){
		if ($init)
			$_POST[$name] = $init;
		else
			$_POST[$name] = "";
	}
	if ($label != null)
		label_cell($label, null);

	if (!isset($max))
		$max = $size;

	echo "<td>";
	//$class = $submit_on_change ? 'class="searchbox"' : '';
	
		echo "<input  type=\"text\"  name=\"$name\" size=\"$size\" maxlength=\"$max\" value=\"" . $_POST[$name]. "\""
	 .($label ? " title='$label'": '')."  > &nbsp;&nbsp;";
	 if($btn)
	 submit('RefreshInquiry', _("Calculate"),_('Show Results'), 'default', true);
	echo "</td>\n";
	
	$Ajax->addUpdate($name, $name, $_POST[$name]);
	
	echo "</tr>\n";
}

function kv_basic_rows($label, $name, $size, $max=null, $init=null, $submit_on_change=false,$btn=false){
  	global $Ajax;
	
  	echo "<tr>";
	default_focus($name);
	
		if ($init)
			$_POST[$name] = $init;
		else
			$_POST[$name] = "";
	
	if ($label != null)
		label_cell($label, null);

	if (!isset($max))
		$max = $size;

	echo "<td>";
	$class = $submit_on_change ? 'class="searchbox"' : '';
	
		echo "<input $class type=\"text\"  name=\"$name\" size=\"$size\" maxlength=\"$max\" value=\"" . $_POST[$name]. "\""
	 .($label ? " title='$label'": '')."  > &nbsp;&nbsp;";
                if($btn)
	submit('RefreshInquiry', _("Calculate"),_('Show Results'), 'default', true);
	echo "</td>\n";
	$Ajax->addUpdate($name, $name, $_POST[$name]);
	echo "</tr>\n";
}
//-----------------------------------------------------------------------------------

function kv_text_row_ex($label, $name, $size, $max=null, $title=null, $value=null, $params=null, $post_label=null, $submit_on_change=false,$disabled=false,$btn = false){
	echo "<tr><td class='label'>$label</td>";
	kv_text_cells_ex(null, $name, $size, $max, $value, $title, $params, $post_label, $submit_on_change, $disabled,$btn);

	echo "</tr>\n";
}


function kv_text_cells_ex($label, $name, $size, $max=null, $init=null, $title=null,
	$labparams=null, $post_label=null, $submit_on_change=false, $disabled= false,$btn = false){
  	global $Ajax;

	default_focus($name);
	if (!isset($_POST[$name]) || $_POST[$name] == "")
	{
		if ($init)
			$_POST[$name] = $init;
		else
			$_POST[$name] = "";
	}
	if ($label != null)
		label_cell($label, $labparams);

	if (!isset($max))
		$max = $size;

	echo "<td>";
	$class = $submit_on_change ? 'class="searchbox"' : '';
	if($disabled){
		echo "<input disabled $class type=\"text\"  name=\"$name\" size=\"$size\" maxlength=\"$max\" value=\"" . $_POST[$name]. "\""
	 .($title ? " title='$title'": '')."  >";
	}else{
		echo "<input $class type=\"text\"  name=\"$name\" size=\"$size\" maxlength=\"$max\" value=\"" . $_POST[$name]. "\""
	 .($title ? " title='$title'": '')."  >";
                 if($btn)
	 submit('RefreshInquiry', _("Calculate"),_('Show Results'), 'default', true);
	}	

	if ($post_label)
		echo " " . $post_label;

	echo "</td>\n";
	$Ajax->addUpdate($name, $name, $_POST[$name]);
}

function kv_checkbox($label, $name, $value=null, $submit_on_change=false, $title=false, $disabled =false)
{
  	global $Ajax;

	$str = '';	

	if ($label)
		$str .= $label . "  ";
	if ($submit_on_change !== false) {
		if ($submit_on_change === true)
			$submit_on_change = 
				"JsHttpRequest.request(\"_{$name}_update\", this.form);";
	}
	if ($value === null)
		$value = get_post($name,0);

	$str .= "<input"
	    .($value == 1 ? ' checked':'')
	    ." type='checkbox' name='$name' value='1'"
	    .($submit_on_change ? " onclick='$submit_on_change'" : '')
	    .($title ? " title='$title'" : '')
	    .($disabled ? "disabled" : '')
	    ." >\n";

	$Ajax->addUpdate($name, $name, $value);
	return $str;
}

function kv_check($label, $name, $value=null, $submit_on_change=false, $title=false, $disabled=false)
{
	echo kv_checkbox($label, $name, $value, $submit_on_change, $title, $disabled);
}

function kv_check_cells($label, $name, $value=null, $submit_on_change=false, $title=false,
	$params='', $disabled =false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td $params>";
	echo kv_check(null, $name, $value, $submit_on_change, $title, $disabled);
	echo "</td>";
}

function kv_check_row($label, $name, $value=null, $submit_on_change=false, $title=false, $disabled=false)
{
	echo "<tr><td class='label'>$label</td>";
	echo kv_check_cells(NULL, $name, $value, $submit_on_change, $title,'', $disabled);
	echo "</tr>\n";
}

function kv_button($name, $value, $title=false, $icon=false,  $aspect='', $disabled=false)
{
	// php silently changes dots,spaces,'[' and characters 128-159
	// to underscore in POST names, to maintain compatibility with register_globals
	$rel = '';
	if ($aspect == 'selector') {
		$rel = " rel='$value'";
		$value = _("Select");
	}
	if($disabled != false)
		$disable = 'disabled';
	else
		$disable = '';
	if (user_graphic_links() && $icon)
	{
		if ($value == _("Delete")) // Helper during implementation
			$icon = ICON_DELETE;
		return "<button type='submit' $disable class='editbutton' name='"
			.htmlentities(strtr($name, array('.'=>'=2E', '='=>'=3D',// ' '=>'=20','['=>'=5B'
			)))
			."' value='1'" . ($title ? " title='$title'":" title='$value'")
			. ($aspect ? " aspect='$aspect'" : '')
			. $rel
			." >".set_icon($icon)."</button>\n";
	}
	else
		return "<input type='submit' $disable class='editbutton' name='"
			.htmlentities(strtr($name, array('.'=>'=2E', '='=>'=3D',// ' '=>'=20','['=>'=5B'
			)))
			."' value='$value'"
			.($title ? " title='$title'":'')
			. ($aspect ? " aspect='$aspect'" : '')
			. $rel
			." >\n";
}

function kv_button_cell($name, $value, $title=false, $icon=false, $aspect='', $disabled=false)
{
	echo "<td align='center'>";
	echo kv_button($name, $value, $title, $icon, $aspect, $disabled);
	echo "</td>";
}

function kv_delete_button_cell($name, $value, $title=false, $disabled=false)
{
	kv_button_cell($name, $value, $title, ICON_DELETE,'', $disabled);
}


// State List
function state_list($name, $selected_id=null,$country_id,$submit_on_change=false,$disabled=false){

	$sql = "SELECT state_id, state_name FROM ".TB_PREF."kv_states WHERE country_id=".db_escape($country_id);
	//display_error($sql);
	return combo_input($name, $selected_id, $sql, 'state_id', 'state_name', array('select_submit'=> $submit_on_change,'disabled'=>$disabled));
}

function state_list_cells($label, $name, $selected_id=null,$country_id,$submit_on_change=false,$disabled=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo state_list($name, $selected_id,$country_id,$submit_on_change,$disabled);
	echo "</td>\n";
}

function state_list_row($label, $name, $selected_id=null,$country_id,$submit_on_change=false,$disabled=false){
	echo "<tr><td class='label'>$label</td>";
	state_list_cells(null, $name, $selected_id,$country_id,$submit_on_change,$disabled);
	echo "</tr>\n";
}
// State List
function all_state_list($name, $selected_id=null,$disabled=false){

	$sql = "SELECT state_id, state_name FROM ".TB_PREF."kv_states";
	//display_error($sql);
	return combo_input($name, $selected_id, $sql, 'state_id', 'state_name', array(disabled=>$disabled));
}

function all_state_list_cells($label, $name, $selected_id=null,$disabled=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo all_state_list($name, $selected_id,$disabled);
	echo "</td>\n";
}

function all_state_list_row($label, $name, $selected_id=null,$disabled=false){
	echo "<tr><td class='label'>$label</td>";
	all_state_list_cells(null, $name, $selected_id,$disabled);
	echo "</tr>\n";
}


/* function leave_status_list($name, $selected_id=null, $submit_on_change=false){


	$sql = "SELECT status_id, status_name FROM ".TB_PREF."kv_leave_request_status";
	
	return combo_input($name, $selected_id, $sql, 'status_id', 'status_id',array('select_submit'=> $submit_on_change));
}

 function leave_status_list_cells($label, $name, $selected_id=null,$submit_on_change=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo leave_status_list($name, $selected_id,$submit_on_change);
	echo "</td>\n";
}

function leave_status_list_row($label, $name, $selected_id=null,$submit_on_change=false){
	echo "<tr><td class='label'>$label</td>";
	leave_status_list_cells(null, $name, $selected_id,$sumbit_on_change);
	echo "</tr>\n";
} */


function leave_status_list($name, $selected_id=null,$submit_on_change=false){

	
	$sql = "SELECT status_id, status_name FROM ".TB_PREF."kv_leave_request_status";
	
	return combo_input($name, $selected_id, $sql, 'status_id', 'status_id',array('select_submit'=> $submit_on_change));
} 

function update_leave_encashment_cal_yer(){
 
    $sql = "UPDATE `".TB_PREF."kv_leave_encash` set cal_year = ". db_escape(date('Y'));
    db_query($sql,'uncable to update the leave encash cal year on commn.inc page');
    
 
}

function leave_status_list_cells($label, $name, $selected_id=null,$submit_on_change=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo leave_status_list($name, $selected_id,$submit_on_change);
	echo "</td>\n";
}

function leave_status_list_row($label, $name, $selected_id=null,$submit_on_change=false){
	echo "<tr><td class='label'>$label</td>";
	leave_status_list_cells(null, $name, $selected_id,$submit_on_change);
	echo "</tr>\n";
}
