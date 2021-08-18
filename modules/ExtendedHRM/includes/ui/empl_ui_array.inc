<?php
/**********************************************************************
 AuthoR : Kvvaradha
 
 Module : HRM 
 
 Developed to 1Stop WebSolution.com
 
***********************************************************************/

$hrm_empl_type = array(
    1 => _('Intern'),  
 //   2 => _('Permanant'), 
    2 => _('Confirmed'),
    3=>_('On probation')
	//4 => _('Long Term Contract'),	
      /* 5 => _('Visiting Faculty')*/

);

$hrm_emplcontract_type = array( 
    3 => _('Short Term Contract'),
	4 => _('Long Term Contract'),	

);

$hrm_empl_marital = array(
    1 => _('Single'),  
    2 => _('Married'), 
    3 => _('Widow'), 
    4 => _('UnMarried'), 
    5 => _('Other'),  
);


$hrm_empl_sal_mod = array(
    1 => _('Select'),  
    2 => _('Bank'),  
    3 => _('Cash'), 
    4 => _('Cheque'), 
);

$hrm_empl_grade_list = array(
    1 => _('A - Individual Contributor'),   
    3 => _('B - Professional'), 
	4 => _('C - Manager '), 
    5 => _('D - Director'), 
    6 => _('E - Vice President'),
);


$hrm_empl_desig_group = array(
    1 => _('Labour'),  
    2 => _('Engineer'), 
    3 => _('Planning'), 
    4 => _('Executive'), 
    5 => _('Manager'),  
    6 => _('Other'),  
);


function  hrm_empl_desig_group($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_empl_desig_group;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $hrm_empl_desig_group, $options);
	echo "</td></tr>\n";
}


function  hrm_empl_performance_list($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_empl_performance_list;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $hrm_empl_performance_list, $options);
	echo "</td></tr>\n";
}


function  hrm_empl_grade_list($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false) {
	global $hrm_empl_grade_list;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
		'disabled' => $disabled
	);
	echo array_selector($name, $selected_id, $hrm_empl_grade_list, $options);
	echo "</td></tr>\n";
}

function  hrm_empl_sal_mod_row($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_empl_sal_mod;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $hrm_empl_sal_mod, $options);
	echo "</td></tr>\n";
}


function  hrm_empl_type_row($label,$name,$selected_id=null,$submit_on_change=false) {
	global $hrm_empl_type;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name,$selected_id,$hrm_empl_type,$options);
	echo "</td></tr>\n";
}

function  hrm_empl_contract_row($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_emplcontract_type;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $hrm_emplcontract_type, $options);
	echo "</td>\n";
}


function hrm_empl_marital_list_row($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_empl_marital;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $hrm_empl_marital, $options);
	echo "</td></tr>\n";
}


?>