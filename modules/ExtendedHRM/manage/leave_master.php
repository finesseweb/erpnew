 <?php
/* * ********************************************************************

  Released under the terms of the GNU General Public License, GPL,
  as published by the Free Software Foundation, either version 3
  of the License, or (at your option) any later version.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 * ********************************************************************* */
$page_security = 'HR_LEAVEMASTER';
$path_to_root = "../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include_once($path_to_root . "/includes/db_pager.inc");
//page(_($help_context = "Leave Master"));

include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
//include($path_to_root . "/includes/ui.inc");


$version_id = get_company_prefs('version_id');

$js = '';
if($version_id['version_id'] == '2.4.1'){
	if ($SysPrefs->use_popup_windows) 
		$js .= get_js_open_window(900, 500);	

	//if (user_use_date_picker()) 
		$js .= get_js_date_picker();
	
}else{
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	//if ($use_date_picker)
		$js .= get_js_date_picker();
}
page(_($help_context = "Leave Master"), @$_REQUEST['popup'], false, "", $js);
simple_page_mode(true);


$ismale = 0;
$cl_date = date('m/d/Y');
$ml_date = date('m/d/Y');
$vl_date = date('m/d/Y');
$el_date = date('m/d/Y');
if (!empty($_POST['empl_id'])) {
    $id = $_POST['empl_id'];
    $sql = "select gender from " . TB_PREF . "kv_empl_info where empl_id =" . db_escape($id);
    $query = db_query($sql, "could not get kv_departments");
    $myrow = db_fetch($query);
    $ismale = $myrow['gender']; 
}


if (!empty($_POST['empl_id']) && $_POST['no_of_cls_acess']==0) {
   
  
  
  
    $sql = "select * from " . TB_PREF . "kv_leave_master where empl_id =" . db_escape($_POST['empl_id']);
    $query = db_query($sql, "could not get kv_departments");
    $myrow1 = db_fetch($query);
    if($myrow1['acess_cl'] !=$_POST['no_of_cls_acess']){
        $cl_date = date('d-m-Y');
    }
 else {
        $cl_date = date('d-m-Y', strtotime($myrow1['updated_date']));
    }
    $el_date = date('d-m-Y', strtotime($myrow1['updated_date_el']));
    $ml_date = date('d-m-Y', strtotime($myrow1['updated_date_ml']));
    $vl_date = date('d-m-Y', strtotime($myrow1['updated_date_vl']));
   
}else if(!empty($_POST['empl_id']) && $_POST['no_of_cls_acess']==1){
        $sql = "select * from " . TB_PREF . "kv_leave_master where empl_id =" . db_escape($_POST['empl_id']);
    $query = db_query($sql, "could not get kv_departments");
    $myrow1 = db_fetch($query);
    $cl_date = date('d-m-Y', strtotime($myrow1['updated_date']));
    $el_date = $el_date;
    $ml_date= $ml_date;
    $vl_date = $vl_date;
}



if (!empty($_POST['empl_id'])&& $_POST['no_of_pls_acess']==0) {
   
  
  
  
    $sql = "select * from " . TB_PREF . "kv_leave_master where empl_id =" . db_escape($_POST['empl_id']);
    $query = db_query($sql, "could not get kv_departments");
    $myrow1 = db_fetch($query);
     if($myrow1['acess_vl'] !=$_POST['no_of_pls_acess']){
        $vl_date = date('d-m-Y');
    }
 else {
        $vl_date = date('d-m-Y', strtotime($myrow1['updated_date_vl']));
    }
    $el_date = $el_date;
    $ml_date = $ml_date;
   $cl_date = $cl_date;
}else if(!empty($_POST['empl_id']) && $_POST['no_of_pls_acess']==1){
    
      $sql = "select * from " . TB_PREF . "kv_leave_master where empl_id =" . db_escape($_POST['empl_id']);
    $query = db_query($sql, "could not get kv_departments");
    $myrow1 = db_fetch($query);
    $vl_date = date('d-m-Y', strtotime($myrow1['updated_date_vl']));
    $el_date = $el_date;
    $ml_date= $ml_date;
    $cl_date = $cl_date;
    
}



if (!empty($_POST['empl_id'])  && $_POST['no_of_medical_ls_acess']==0) {
   
  $ml_date = date('d-m-Y');
  
  
    $sql = "select * from " . TB_PREF . "kv_leave_master where empl_id =" . db_escape($_POST['empl_id']);
    $query = db_query($sql, "could not get kv_departments");
    $myrow1 = db_fetch($query);
    
      if($myrow1['acess_ml'] !=$_POST['no_of_medical_ls_acess']){
        $ml_date = date('d-m-Y');
    }
 else {
        $ml_date = date('d-m-Y', strtotime($myrow1['updated_date_ml']));
    }
    
    $el_date = $el_date;
    $vl_date = $vl_date;
    $cl_date = $cl_date;
}else if(!empty($_POST['empl_id']) && $_POST['no_of_medical_ls_acess']==1){
      $sql = "select * from " . TB_PREF . "kv_leave_master where empl_id =" . db_escape($_POST['empl_id']);
    $query = db_query($sql, "could not get kv_departments");
    $myrow1 = db_fetch($query);
    $ml_date = date('d-m-Y', strtotime($myrow1['updated_date_ml']));
    $el_date = $el_date;
    $cl_date= $cl_date;
    $vl_date = $vl_date;
}





if (!empty($_POST['empl_id']) && $_POST['no_of_el_acess']==0) {
   
  $el_date = date('d-m-Y');
  
  
    $sql = "select * from " . TB_PREF . "kv_leave_master where empl_id =" . db_escape($_POST['empl_id']);
    $query = db_query($sql, "could not get kv_departments");
    $myrow1 = db_fetch($query);
    $el_date = $el_date;
     $vl_date = $vl_date;
   $ml_date = $ml_date;
}else if(!empty($_POST['empl_id']) &&  $_POST['no_of_el_acess']==1){
      $sql = "select * from " . TB_PREF . "kv_leave_master where empl_id =" . db_escape($_POST['empl_id']);
    $query = db_query($sql, "could not get kv_departments");
    $myrow1 = db_fetch($query);
    
      
      if($myrow1['acess_el'] !=$_POST['no_of_el_acess']){
        $el_date = date('d-m-Y');
    }
 else {
        $el_date = date('d-m-Y', strtotime($myrow1['updated_date_el']));
    }
    
    
    $el_date = date('d-m-Y', strtotime($myrow1['updated_date_el']));
    $cl_date = $cl_date;
    $ml_date= $ml_date;
    $vl_date = $vl_date;
}



simple_page_mode(true);
if (get_post('_no_of_cls_changed')) {

    $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
    if (preg_match($regex, get_post('no_of_cls')) != 0) {
        $input_error = 1;
        display_error(_("Only Numericals are allowed."));

        set_focus('no_of_cls');
    } else {
        $input_error = 0;
    }
}
if (get_post('_no_of_pls_changed')) {

    $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
    if (preg_match($regex, get_post('no_of_pls')) != 0) {
        $input_error = 1;
        display_error(_("Only Numericals are allowed."));

        set_focus('no_of_pls');
    } else {
        $input_error = 0;
    }
}

if (get_post('_no_of_medical_ls_changed')) {

    $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
    if (preg_match($regex, get_post('no_of_medical_ls')) != 0) {
        $input_error = 1;
        display_error(_("Only Numericals are allowed."));

        set_focus('no_of_medical_ls');
    } else {
        $input_error = 0;
    }
}
if (get_post('_no_of_spl_cls_changed')) {
    $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
    if (preg_match($regex, get_post('no_of_spl_cls')) != 0) {
        $input_error = 1;
        display_error(_("Only Numericals are allowed."));

        set_focus('no_of_spl_cls');
    } else {
        $input_error = 0;
    }
}

if (get_post('_no_of_spl_cls_female_changed')) {
    $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
    if (preg_match($regex, get_post('no_of_spl_cls_female')) != 0) {
        $input_error = 1;
        display_error(_("Only Numericals are allowed."));

        set_focus('no_of_spl_cls_female');
    } else {
        $input_error = 0;
    }
}

if (get_post('_no_of_mat_ls_changed')) {
    $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
    if (preg_match($regex, get_post('no_of_mat_ls')) != 0) {
        $input_error = 1;
        display_error(_("Only Numericals are allowed."));

        set_focus('no_of_mat_ls');
    } else {
        $input_error = 0;
    }
}

if (get_post('_no_of_patern_ls_changed')) {
    $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
    if (preg_match($regex, get_post('no_of_patern_ls')) != 0) {
        $input_error = 1;
        display_error(_("Only Numericals are allowed."));

        set_focus('no_of_patern_ls');
    } else {
        $input_error = 0;
    }
}
?>
<html lang="en">
    <head>
        <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js" ?>"></script>
        
      
    </head>


</html>
<?php

function can_process() {
    $regex = "/[a-zA-Z*@$%#^&!~()+\/><,{}';?<>]/";
    if (!empty($_POST['no_of_cls'])) {
      
        if (preg_match($regex, get_post('no_of_cls')) != 0 ) {

            display_error(_("Only Numericals are allowed."));

            set_focus('no_of_cls');
            return false;
        }
       
    }
    if (!empty($_POST['no_of_pls_changed'])) {

        
        if (preg_match($regex, get_post('no_of_pls')) != 0) {
            $input_error = 1;
            display_error(_("Only Numericals are allowed."));

            set_focus('no_of_pls');

            return false;
        }
    }

    if (!empty($_POST['no_of_medical_ls'])) {

     
        if (preg_match($regex, get_post('no_of_medical_ls')) != 0) {

            display_error(_("Only Numericals are allowed."));

            set_focus('no_of_medical_ls');

            return false;
        }
    }
    if (!empty($_POST['no_of_spl_cls'])) {
     
        if (preg_match($regex, get_post('no_of_spl_cls')) != 0) {

            display_error(_("Only Numericals are allowed."));

            set_focus('no_of_spl_cls');
            return false;
        }
    }

    if (!empty($_POST['no_of_spl_cls_female'])) {
      
        if (preg_match($regex, get_post('no_of_spl_cls_female')) != 0) {

            display_error(_("Only Numericals are allowed."));

            set_focus('no_of_spl_cls_female');

            return false;
        }
    }

    if (!empty($_POST['no_of_mat_ls_changed'])) {
      
        if (preg_match($regex, get_post('no_of_mat_ls')) != 0) {

            display_error(_("Only Numericals are allowed."));

            set_focus('no_of_mat_ls');

            return false;
        }
    }

    if (!empty($_POST['no_of_patern_ls'])) {
      
        if (preg_match($regex, get_post('no_of_patern_ls')) != 0) {

            display_error(_("Only Numericals are allowed."));

            set_focus('no_of_patern_ls');

            return false;
        }
    }

    return true;
}

/* function can_process1() 
  {

  if (strlen($_POST['no_of_pls']) == 0)
  {
  display_error(_("No. Of Earned Leaves cannot be empty."));
  set_focus('no_of_pls');
  return false;
  }

  return true;
  } */


//-----------------------------------------------------------------------------------

if ($Mode == 'ADD_ITEM' && can_process()) {
    //display_error("afdd");die;
    
    add_leaves($_POST['fisc_year'], $_POST['designation_group_id'], $_POST['desig_id'], $_POST['dept_id'], $_POST['no_of_cls'], $_POST['no_of_pls'], $_POST['no_of_medical_ls'],$_POST['no_of_el'], $_POST['no_of_spl_cls'], $_POST['no_of_spl_cls_female'], $_POST['no_of_mat_ls'], $_POST['no_of_patern_ls'], $_POST['cal_year'], $_POST['empl_id'],$_POST['no_of_cls_acess'],$_POST['no_of_pls_acess'],$_POST['no_of_el_acess'],$_POST['no_of_medical_ls_acess'],$_POST['no_of_spl_cls_acess'],$_POST['no_of_spl_cls_female_acess'], $_POST['no_of_mat_ls_acess'], $_POST['no_of_patern_ls_acess'], date2sql($_POST['no_of_cls_date']), date2sql($_POST['no_of_pls_date']),date2sql($_POST['no_of_el_date']),date2sql($_POST['no_of_medical_ls_date']), $_POST['cal_year']);
    display_notification(_('Leaves  has been added'));
    $Mode = 'RESET';
}

//-----------------------------------------------------------------------------------


if ($Mode == 'UPDATE_ITEM' && can_process()) {
    //display_error("sdff");die;
    update_leaves($selected_id, $_POST['fisc_year'], $_POST['designation_group_id'], $_POST['desig_id'], $_POST['dept_id'], $_POST['no_of_cls'], $_POST['no_of_pls'], $_POST['no_of_medical_ls'],$_POST['no_of_el'], $_POST['no_of_spl_cls'], $_POST['no_of_spl_cls_female'], $_POST['no_of_mat_ls'], $_POST['no_of_patern_ls'],$_POST['no_of_cls_acess'],$_POST['no_of_pls_acess'],$_POST['no_of_el_acess'],$_POST['no_of_medical_ls_acess'],$_POST['no_of_spl_cls_acess'],$_POST['no_of_spl_cls_female_acess'], $_POST['no_of_mat_ls_acess'], $_POST['no_of_patern_ls_acess'], date2sql($_POST['no_of_cls_date']), date2sql($_POST['no_of_pls_date']),date2sql($_POST['no_of_el_date']),date2sql($_POST['no_of_medical_ls_date']), $_POST['cal_year']);
    $Mode = 'RESET';
    display_notification(_('Leaves has been updated'));
}


//-----------------------------------------------------------------------------------

/* function can_delete($selected_id)
  {
  if (key_in_foreign_table($selected_id, 'debtors_master', 'credit_status'))
  {
  display_error(_("Cannot delete this credit status because customer accounts have been created referring to it."));
  return false;
  }

  return true;
  } */


//-----------------------------------------------------------------------------------

if ($Mode == 'Delete') {

    //if (can_delete($selected_id))
    //{
    delete_leave($selected_id);
    display_notification(_('Leave has been deleted'));
    //}
    $Mode = 'RESET';
}

if ($Mode == 'RESET') {
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    $_POST['show_inactive'] = $sav;
}
//-----------------------------------------------------------------------------------

$result = get_leaves(check_value('show_inactive'));
//display_error($result);die;
start_form();
start_table(TABLESTYLE);
$th = array( _("Academic Year"),_("Employee Id"), _("Employee Name")/* _("Designation Group"),_("Designation Name"),_("Department") */, _("No. of CL's"), _("No. of VL's"), _("No. of Medical Leaves"),_("No. of Earned Leaves"), _("No. of S.CL (Male)"), _("No. of S.CL (Female)"), _("No. of Maternity Leaves"), _("No. of Paternity Leaves"),_("Eligible (CL)"), _("Eligible (VL)"), _("Eligible (ML)"),_("Eligible (EL)"), _("Eligible (SPL)"), _("Eligible (SPL Female)"), _("Eligible (MAT)"), _("Eligible (PAT)"), '', '',);
inactive_control_column($th);
table_header($th);

$k = 0;
while ($myrow = db_fetch($result)) {

    alt_table_row_color($k);
    //designation_list_cells(_("Select : "), 'desig_group_id', null, true, true);
    //label_cell(sql2date($myrow["f_styear"]) . '-' . sql2date($myrow["f_endyear"]));
    label_cell($myrow["year"]);
     label_cell($myrow["emp_id"]);
    label_cell($myrow["emp_name"]);
    //label_cell($myrow["grp_name"]);
    //label_cell($myrow["desig_name"]);
    //label_cell($myrow["department"]);
    label_cell($myrow["no_of_cls"]);
    label_cell($myrow["no_of_pls"]);
    label_cell($myrow["no_of_medical_ls"]);
    label_cell($myrow["no_of_el"]);
    label_cell($myrow["no_of_spl_cls"]);
    label_cell($myrow["no_of_spl_cls_female"]);
    label_cell($myrow["no_of_mat_ls"]);
    label_cell($myrow["no_of_patern_ls"]);
    label_cell(bool1($myrow["acess_cl"]));
    label_cell(bool1($myrow["acess_vl"]));
    label_cell(bool1($myrow["acess_ml"]));
    label_cell(bool1($myrow["acess_el"]));
    label_cell(bool1($myrow["acess_spl_male"]));
    label_cell(bool1($myrow["acess_spl_female"]));
    label_cell(bool1($myrow["acess_mat"]));
    label_cell(bool1($myrow["acess_pat"]));
    //label_cell($status_details);
    //label_cell($disallow_text);
    inactive_control_cell($myrow["leave_id"], $myrow["inactive"], 'kv_leave_master', 'leave_id');
    edit_button_cell("Edit" . $myrow['leave_id'], _("Edit"));
    //delete_button_cell("Delete".$myrow['leave_id'], _("Delete"));
    //submit_js_confirm("Delete".$myrow["leave_id"], sprintf(_("You are about to delete a Leave Master Do you want to continue?"), $myrow['leave_id']));
    end_row();
}
inactive_control_row($th);
end_table();

//later work

/* function display_rows(){
  $sql1 = $sql="SELECT leaves.*,cal_year as year,emp.empl_firstname as emp_name, des_group.name as grp_name,des.name as desig_name,dept.description as department,fiscal.begin AS f_styear,fiscal.end AS f_endyear FROM ".TB_PREF."kv_leave_master as leaves,".TB_PREF."kv_desig_group as des_group,".TB_PREF."kv_empl_info as emp,".TB_PREF."designation_master as des,".TB_PREF."kv_departments as dept,".TB_PREF."fiscal_year AS fiscal  where leaves.designation_group_id=des_group.id AND leaves.empl_id = emp.empl_id AND leaves.desig_id=des.id AND leaves.dept_id=dept.id AND leaves.fisc_year=fiscal.id ";
  $cols = array(
  _("Fiscal Year") => array('name'=>'emp_name'),
  _("Academic Year") => array('name'=>'year'),
  _("Employee Name") => array('name'=>'emp_name'),
  _("No. of CL's") => array('name'=>'no_of_cls'),
  _("No. of VL's") => array('name'=>'no_of_pls'),
  //_("Grade") => array('name'=>'grade'),
  _("No. of Medical Leaves") => array('name'=>'no_of_medical_ls'),
  _("No. of S.CL (Male)") => array('name'=>'no_of_spl_cls'),
  _("No. of S.CL (Female)") => array('name'=>'no_of_spl_cls_female'),
  _("No. of Maternity Leaves") => array('name'=>'no_of_mat_ls'),
  _("No. of Paternity Leaves") => array('name'=>'no_of_patern_ls')
  //array('')
  );
  $table =& new_db_pager('kv_leave_master', $sql1, $cols);
  //print_r($_SESSION);exit;
  $table->width = "90%";
  $table->set_marker('check_contractperiod', _("List of employees whoose contract period is going to be completed before one month."));
  display_db_pager($table);
  }

  display_rows(); */
echo '<br>';

//-----------------------------------------------------------------------------------
$edit_empl_id=date('Y');
start_table(TABLESTYLE2);
$_POST['no_of_cls'] = 0;
if ($selected_id != -1) {
    if ($Mode == 'Edit') {
        //editing an existing status code
        //display_error($selected_id);die;
        $myrow = get_leaveedit($selected_id);
        //display_error($myrow);
        $_POST['designation_group_id'] = $myrow["designation_group_id"];
        $_POST['desig_id'] = $myrow["desig_id"];
        $_POST['dept_id'] = $myrow["dept_id"];
        $_POST['no_of_cls'] = $myrow["no_of_cls"];
        $_POST['no_of_pls'] = $myrow["no_of_pls"];
        $_POST['no_of_el'] = $myrow["no_of_el"];
        $_POST['no_of_medical_ls'] = $myrow["no_of_medical_ls"];
        $_POST['no_of_spl_cls'] = $myrow["no_of_spl_cls"];
        $_POST['no_of_spl_cls_female'] = $myrow["no_of_spl_cls_female"];
        $_POST['no_of_mat_ls'] = $myrow["no_of_mat_ls"];
        $_POST['no_of_patern_ls'] = $myrow["no_of_patern_ls"];
         $_POST['no_of_cls_acess'] = $myrow["acess_cl"];
        $_POST['no_of_pls_acess'] = $myrow["acess_vl"];
        $_POST['no_of_el_acess'] = $myrow["acess_el"];
        $_POST['no_of_medical_ls_acess'] = $myrow["acess_ml"];
        $_POST['no_of_spl_cls_acess'] = $myrow["acess_spl_male"];
        $_POST['no_of_spl_cls_female_acess'] = $myrow["acess_spl_female"];
        $_POST['no_of_mat_ls_acess'] = $myrow["acess_mat"];
        $_POST['no_of_patern_ls_acess'] = $myrow["acess_pat"];
        $last_id = $myrow["empl_id"];
        $edit_empl_id = $myrow['cal_year'];
        
        
        
    $sql = "select * from " . TB_PREF . "kv_leave_master where empl_id =" . db_escape($last_id);
    $query = db_query($sql, "could not get kv_departments");
    $myrow1 = db_fetch($query);
    
    /*
     * it has been changed 
     * manually cox there is not predefined function 
     * even if it defined i have to check it 
     * thanks 
     */
   
    
    
    $_POST['no_of_cls_date'] = sql2date($myrow1['updated_date']);
    $_POST['no_of_el_date'] = sql2date($myrow1['updated_date_el']);
    $_POST['no_of_medical_ls_date'] = sql2date($myrow1['updated_date_ml']);
    $_POST['no_of_pls_date'] = sql2date($myrow1['updated_date_vl']);
        
        
    }
    hidden('selected_id', $selected_id);
}
kv_fiscalyears_list_cells(_("Fiscal Year:"), 'fisc_year', null, false);
calendar_list_row(_("Calendar Year:"), 'cal_year',$edit_empl_id, false, true);
//departments_list_row(_("Department:"), 'dept_id', null, false, true);
//desiggroup_list_row(_("Designation Group:"), 'designation_group_id', null, false, true);
//echo $_POST['dept_id'];
//$desig_group = $_POST['designation_group_id'];
//desig_list_row(_("Desgination:"), 'desig_id', null,false,true,$desig_group);
employee_list_cells(_("Select an Employee: "), 'empl_id', $last_id, _('Select Employee'), true, check_value('show_inactive'));
if (list_updated('designation_group_id')) {
    $Ajax->activate('desig_id');
}

//desigroup_list_row( _("Designation :"), 'desigroup_id', null);
//text_row_ex(_("Name:"), 'name', 50);
// leavetype_list_row(_("Leave Type:"), 'type_leave', null, false, false);
text_row_ex(_("No. of Casual Leaves :"), 'no_of_cls', 4, null, null, '00', null, " Days", false,false,'',true,$cl_date);

text_row_ex(_("No. of Vacation Leaves :"), 'no_of_pls', 6, null, null, '00', null, " Days", false,false,'',true,$vl_date);
text_row_ex(_("No. of Medical Leaves :"), 'no_of_medical_ls', 6, null, null, '00', null, " Days", false,false,'',true,$ml_date);
text_row_ex(_("No. of Earned Leaves:"), 'no_of_el', 6, null, null, '00', null, " Days", true,false,'',true,$el_date);
text_row_ex(_("No. of Special Casual Leaves(Male) :"), 'no_of_spl_cls', 6, null, null, '00', null, " Days", false,false,'',false,'');
text_row_ex(_("No. of Special Casual Leaves(Female) :"), 'no_of_spl_cls_female', 6, null, null, '00', null, " Days", false,false,'',false,'');
if ($ismale == 2) {
    text_row_ex(_("No. of Maternity Leaves :"), 'no_of_mat_ls', 6, null, null, '00', null, " Days", false, false,'',false,'');
} else {
    text_row_ex(_("No. of Maternity Leaves :"), 'no_of_mat_ls', 6, null, null, '00', null, " Days", false, true,'',false,'');
}
if ($ismale == 1) {
    text_row_ex(_("No. of Paternity Leaves :"), 'no_of_patern_ls', 6, null, null, '00', null, " Days", false, false,'',false,'');
} else {
    text_row_ex(_("No. of Paternity Leaves :"), 'no_of_patern_ls', 6, null, null, '00', null, " Days", false, true,'',false,'');
}
end_table(1);


//if($_POST['no_of_cls_acess']==1)

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();
?>
<!-- <p style='padding: 3px;text-align:center;'> <a href='javascript:goBack();'>Back</a></p> -->

<script>
    /* $(document).ready(function(){
     
     
     $('input[name=no_of_cls]').keypress(function (e){
     var code =e.keyCode || e.which;
     if (e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57)) 
     {
     alert("Only Numericals are allowed");
     return false;
     }
     });
     });
     
     $(document).ready(function(){
     $('input[name=no_of_pls]').keypress(function (e){
     var code =e.keyCode || e.which;
     if (e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57)) 
     {
     alert("Only Numericals are allowed");
     return false;
     }
     });
     });
     
     $(document).ready(function(){
     $('input[name=no_of_medical_ls]').keypress(function (e){
     var code =e.keyCode || e.which;
     if (e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57)) 
     {
     alert("Only Numericals are allowed");
     return false;
     }
     });
     });
     
     $(document).ready(function(){
     $('input[name=no_of_spl_cls]').keypress(function (e){
     var code =e.keyCode || e.which;
     if (e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57)) 
     {
     alert("Only Numericals are allowed");
     return false;
     }
     });
     });
     
     $(document).ready(function(){
     $('input[name=no_of_spl_cls_female]').keypress(function (e){
     var code =e.keyCode || e.which;
     if (e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57)) 
     {
     alert("Only Numericals are allowed");
     return false;
     }
     });
     });
     
     $(document).ready(function(){
     $('input[name=no_of_mat_ls]').keypress(function (e){
     var code =e.keyCode || e.which;
     if (e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57)) 
     {
     alert("Only Numericals are allowed");
     return false;
     }
     });
     });
     
     $(document).ready(function(){
     $('input[name=no_of_patern_ls]').keypress(function (e){
     var code =e.keyCode || e.which;
     if (e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57)) 
     {
     alert("Only Numericals are allowed");
     return false;
     }
     });
     });
     */

$(document).ready(function() {		
	$(".no_of_cls_date").datepicker({ dateFormat: 'dd-mm-yy' });     
	$(".no_of_pls_date").datepicker({ dateFormat: 'dd-mm-yy' });  
        $(".no_of_medical_ls_date").datepicker({ dateFormat: 'dd-mm-yy' });  
        $(".no_of_el_date").datepicker({ dateFormat: 'dd-mm-yy' });  
})


</script>