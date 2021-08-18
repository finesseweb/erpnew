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
$page_security = 'HR_LEAVEACURAL';
$path_to_root = "../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
//page(_($help_context = "Leave Acural"));

include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/ui/leave_acural.inc" );
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );



$version_id = get_company_prefs('version_id');

$js = '';
if($version_id['version_id'] == '2.4.1'){
	if ($SysPrefs->use_popup_windows) 
		$js .= get_js_open_window(900, 500);	

	
		$js .= get_js_date_picker();
	
}else{
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	
		$js .= get_js_date_picker();
}

page(_($help_context = "Leave Acural"), @$_REQUEST['popup'], false, "", $js);
simple_page_mode(true);
if (get_post('_leave_type_changed')) {
    $regex = "/[^ ][ A-Za-z\.]$/";
    if (preg_match($regex, get_post('leave_type')) == 0) {
        display_error(_("Accept only Alphabets."));
        set_focus('leave_type');
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
    /* if (strlen($_POST['leave_type']) == 0) 
      {

      display_error(_("Name cannot be empty."));
      set_focus('leave_type');
      return false;
      }

      if(!empty($_POST['leave_type'])){
      $regex = "/[^ ][ A-Za-z\.]$/";
      if(preg_match($regex, get_post('leave_type')) ==0) {
      display_error( _("Accept Only Alphabets."));
      set_focus('leave_type');
      return false;
      }
      } */

    if (strlen($_POST['accural_days']) == 0) {
        display_error(_("Accural days  cannot be empty."));
        set_focus('accural_days');
        return false;
    }
    if (!empty($_POST['accural_days'])) {

        $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
        if (preg_match($regex, get_post('accural_days')) != 0) {
            $input_error = 1;
            display_error(_("Only Numericals are allowed in Accural days."));

            set_focus('accural_days');
            return false;
        }
    }
//========max Accumulation

    if ($_POST['yes_no'] == 1) {
        if (strlen($_POST['max_accumulation']) == 0) {
            display_error(_("max accumulation  cannot be empty."));
            set_focus('max_accumulation');
            return false;
        }
        if (!empty($_POST['max_accumulation'])) {

            $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
            if (preg_match($regex, get_post('max_accumulation')) != 0) {
                $input_error = 1;
                display_error(_("Only Numericals are allowed in max accumulation."));

                set_focus('max_accumulation');
                return false;
            }
        }
    }
//============avial leaves 
    if (strlen($_POST['avail_leaves']) == 0) {
        display_error(_("avail leaves cannot be empty."));
        set_focus('avail_leaves');
        return false;
    }
    if (!empty($_POST['avail_leaves'])) {

        $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
        if (preg_match($regex, get_post('avail_leaves')) != 0) {
            $input_error = 1;
            display_error(_("Only Numericals are allowed in avail leaves."));

            set_focus('avail_leaves');
            return false;
        }
    }
//=============No of days at a stretch (Max)
    /*    if($_POST['yes_no'] ==1){
      if (strlen($_POST['max_days']) == 0) {
      display_error(_("No of days at a stretch (Max) cannot be empty."));
      set_focus('max_days');
      return false;
      }
      if (!empty($_POST['max_days'])) {

      $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
      if (preg_match($regex, get_post('max_days')) != 0) {
      $input_error = 1;
      display_error(_("Only Numericals are allowed in No of days at a stretch (Max)."));

      set_focus('max_days');
      return false;
      }
      }
      } */
//===================No of days at a stretch (Min)
    /*    if($_POST['yes_no'] ==1){
      if (strlen($_POST['min_days']) == 0) {
      display_error(_("No of days at a stretch (Min) cannot be empty."));
      set_focus('min_days');
      return false;
      }
      if (!empty($_POST['min_days'])) {

      $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
      if (preg_match($regex, get_post('min_days')) != 0) {
      $input_error = 1;
      display_error(_("Only Numericals are allowed in No of days at a stretch (Min)."));

      set_focus('min_days');
      return false;
      }
      }
      } */
//====  calendar year at a stretch (Min) cannot be empty
    if (strlen($_POST['max_times_in_cal_year']) == 0) {
        display_error(_("Max No. of times to be availed in a calendar year at a stretch (Min) cannot be empty."));
        set_focus('max_times_in_cal_year');
        return false;
    }
    if (!empty($_POST['max_times_in_cal_year'])) {

        $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
        if (preg_match($regex, get_post('max_times_in_cal_year')) != 0) {
            $input_error = 1;
            display_error(_("Only Numericals are allowed in Max No. of times to be availed in a calendar year."));

            set_focus('max_times_in_cal_year');
            return false;
        }
    }

  /*  if (strlen($_POST['max_encash']) == 0) {
        display_error(_("Encashment of Leave(Max) cannot be empty."));
        set_focus('max_encash');
        return false;
    }
    if (!empty($_POST['max_encash'])) {

        $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
        if (preg_match($regex, get_post('max_times_in_cal_year')) != 0) {
            $input_error = 1;
            display_error(_("Only Numericals are allowed in Encashment of Leave(Max)."));

            set_focus('max_encash');
            return false;
        }
    }

    if (strlen($_POST['min_encash']) == 0) {
        display_error(_("Encashment of Leave(Min) cannot be empty."));
        set_focus('min_encash');
        return false;
    }
    if (!empty($_POST['min_encash'])) {

        $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
        if (preg_match($regex, get_post('min_encash')) != 0) {
            $input_error = 1;
            display_error(_("Only Numericals are allowed in Encashment of Leave(Min)."));

            set_focus('min_encash');
            return false;
        }
    }
*/
    return true;
}

function can_process1() {

    /* if (strlen($_POST['no_of_pls']) == 0) 
      {
      display_error(_("cannot be empty."));
      set_focus('no_of_pls');
      return false;
      } */

    return true;
}

//-----------------------------------------------------------------------------------

if ($Mode == 'ADD_ITEM' && can_process() && can_process1()) {
    //display_error("afdd");die;
   if(!isLeaveAlreadyCreated($_POST['emp_type'], $_POST['leave_type'])){
    add_leave_days($_POST['emp_type'], $_POST['leave_type'], $_POST['accural_days'], $_POST['weekend_status'], $_POST['max_accumulation'], $_POST['avail_leaves'], 0, 0, $_POST['max_times_in_cal_year'], 0, 0, /*$_POST['cal_year']*/date('Y'), $_POST['yes_no'],$_POST['merg_status'], $_POST['merg_to'],$_POST['effective_date']);
    display_notification(_('Leave days has been added'));
    $Mode = 'RESET';
   }
   else
   {
       display_error(_('Duplicate value cannot be inserted'));
   }
}

//-----------------------------------------------------------------------------------

if ($Mode == 'UPDATE_ITEM' && can_process() && can_process1()) {
    //display_error("sdff");die;
    if(!isLeaveAlreadyCreated($_POST['emp_type'], $_POST['leave_type'],$selected_id)){
    update_leave_days($_POST['emp_type'], $_POST['leave_type'], $_POST['accural_days'], $_POST['weekend_status'], $_POST['max_accumulation'], $_POST['avail_leaves'], 0, 0,   $_POST['max_times_in_cal_year'], 0, 0, /*$_POST['cal_year']*/date('Y'), $selected_id, $_POST['yes_no'],$_POST['merg_status'], $_POST['merg_to'],$_POST['effective_date']);
    $Mode = 'RESET';
    display_notification(_('Leave type has been updated'));
    }
    else{
        display_error(_('Duplicate value cannot be updated'));
    }
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
    //display_error($selected_id); die;
    delete_leave_days($selected_id);
    display_notification(_('Leave Type has been deleted'));
    $Mode = 'RESET';
}

if ($Mode == 'RESET') {
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    $_POST['show_inactive'] = $sav;
}
//-----------------------------------------------------------------------------------

$result = get_leave_days(check_value('show_inactive'));
//display_error($result);die;
start_form();
start_table(TABLESTYLE);
$th = array(_("Employee type"), _("Leave Type"), _("Rate of Accural Days"), _("status of intervening holidays /weekend"), ("max-Accumulation"), _('Availing of leave'), 'no of days at a stretch(Max)', 'no of days at a stretch (min)', 'Max No. of times to be availed in a calendar year','Carry Forward Leave','Merg Status','Merg Date','Merg From', 'Calendar Year', 'Edit', 'Delete',);
inactive_control_column($th);
table_header($th);

$k = 0;

$nos = db_num_rows($result);
if ($nos != 0) {

    while ($myrow = db_fetch($result)) {

        alt_table_row_color($k);
        label_cell(leaves_employee_type($myrow["employee_type"]));
        label_cell(leave_type($myrow["leave_type"])[0]);
        label_cell($myrow["accural_days"]);
        label_cell(bool($myrow["weekend_status"]));
        label_cell($myrow["max_accumulation"]);
        label_cell($myrow["avail_leaves"]);
        label_cell($myrow["max_days"]);
        label_cell($myrow["min_days"]);
        label_cell($myrow["max_times_in_cal_year"]);
        label_cell($myrow["c_f"]==1?'Yes':'No');
        label_cell($myrow["merg_status"]==1?'Yes':'No');
        label_cell($myrow["merg_date"]!='0000-00-00'?date('d/m/Y',strtotime($myrow["merg_date"])):'00/00/0000');
        label_cell(leave_type($myrow["merg_to"])[0]?leave_type($myrow["merg_to"])[0]:'----');
        label_cell($myrow["cal_year"]);
        inactive_control_cell($myrow["id"], $myrow["inactive"], 'kv_leave_days', 'id');
        edit_button_cell("Edit" . $myrow['id'], _("Edit"));
        delete_button_cell("Delete" . $myrow['id'], _("Delete"));
        submit_js_confirm("Delete" . $myrow["id"], sprintf(_("You are about to delete a Type of Leave Master Do you want to continue?"), $myrow['id']));
        end_row();
    }
} else {
    label_cell('No Records Found', 'colspan=4 align="center" size="15"');
}
inactive_control_row($th);
end_table();
echo '<br>';

//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);
$edit_empl_id = Date("Y");
if ($selected_id != -1) {
    if ($Mode == 'Edit') {
        $myrow = get_leave_days_edit($selected_id);

        $_POST['emp_type'] = $myrow["employee_type"];
        $_POST['leave_type'] = $myrow["leave_type"];
        $_POST['accural_days'] = $myrow["accural_days"];
        $_POST['weekend_status'] = $myrow["weekend_status"];
        $_POST['max_accumulation'] = $myrow["max_accumulation"];
        $_POST['avail_leaves'] = $myrow["avail_leaves"];
        $_POST['max_days'] = $myrow["max_days"];
        $_POST['min_days'] = $myrow["min_days"];
        $_POST['max_times_in_cal_year'] = $myrow["max_times_in_cal_year"];
        $_POST['max_encash'] = $myrow["max_encash"];
        $_POST['min_encash'] = $myrow["min_encash"];
        $_POST['cal_year'] = $myrow["cal_year"];
        $_POST['yes_no'] = $myrow["c_f"];
        $edit_empl_id = $myrow['cal_year'];
        $_POST['merg_status'] = $myrow["merg_status"];
        $_POST['merg_to'] = $myrow["merg_to"];
          $due_date = explode('-',$myrow["merg_date"]);
		$_POST['effective_date']  = $due_date[1].'/'.$due_date[2].'/'.$due_date[0];
       // $_POST['effective_date'] = $myrow["merg_date"];
    }
    hidden('selected_id', $selected_id);
}

kv_empl_type_list_row(_("Employee type :"), "emp_type", $_POST['emp_type'], false);
leavetype_list_row(_("Leave Type :"), 'leave_type', null, false, true);
text_row_ex(_("Rate of accrual days"), 'accural_days', 30, null, null, null, null, null, true);
custom_record_status_list_row(_("Status of intervening holiday/ week end"), 'weekend_status', 'No', 'Yes');
text_row_ex(_("Max-Accumulation"), 'max_accumulation', 30, null, null, $x = 0, null, null, true);
yes_no(_("Carry Forward"), 'yes_no', $_POST['yes_no'], true);
if ($_POST['yes_no'] == 1) {
    
//text_row_ex(_("No of days at a stretch (Max)"), 'max_days', 30, null, null, $x = 0, null, null, true);
//text_row_ex(_("No of days at a stretch (Min)"), 'min_days', 30, null, null, $x = 0, null, null, true);
    yes_no(_("Merg Status"), 'merg_status', $_POST['merg_status'], true);
    if ($_POST['merg_status'] == 1) {  
        leavetype_list_row(_("Merg From :"), 'merg_to', null, false, true);
        date_row(_("Effective Date") . ":", 'effective_date');
    }
}

text_row_ex(_("Availing of Leave"), 'avail_leaves', 30, null, null, $x = 0, null, null, true);

text_row_ex(_("Max No. of times to be availed in a calendar year"), 'max_times_in_cal_year', 30, null, null, $x = 0, null, null, true);

//text_row_ex(_("Encashment of Leave(Max)"), 'max_encash', 30, null, null, $x = 0, null, null, true);
//text_row_ex(_("Encashment of Leave (min.)"), 'min_encash', 30, null, null, $x = 0, null, null, true);

// ======= comented calendar list row  coz om has instructed to increment as per situation ========//
//=====comented on 04-04-2019 calendar_list_row(_("Calendar Year:"), 'cal_year', $edit_empl_id, false, true);
end_table(1);
if (list_updated('leave_type')) {
    $Ajax->activate('leave_type');
}
submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();
?>
<!-- <p style='padding: 3px;text-align:center;'> <a href='javascript:goBack();'>Back</a></p> -->
<script>
    /* $(document).ready(function(){
     $('input[name=leave_type]').keypress(function (e){
     var code =e.keyCode || e.which;
     if((code<65 || code>90)&&(code<97 || code>122)&&code!=32&&code!=46)  
     {
     alert("Only alphabets are allowed");
     return false;
     }
     });
     });
     
     
     
     $(document).ready(function(){
     //alert('aaaaa');
     //var desc=$('input[name=description]').val();
     $('textarea[name=desciption]').keypress(function (e){
     
     var code =e.keyCode || e.which;
     if((code<65 || code>90)&&(code<97 || code>122)&&code!=32&&code!=46)  
     {
     alert("Only alphabets are allowed");
     return false;
     }
     });
     }); */
</script>