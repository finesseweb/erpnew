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
$page_security = 'HR_LEAVENCASHMENT';
$path_to_root = "../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
page(_($help_context = "Leave Encashment Rules"));

include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/ui/leave_acural.inc" );
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
//include($path_to_root . "/includes/ui.inc");



$version_id = get_company_prefs('version_id');

$js = '';
if($version_id['version_id'] == '2.4.1'){
	if ($SysPrefs->use_popup_windows) 
		$js .= get_js_open_window(900, 500);	

	if (user_use_date_picker()) 
		$js .= get_js_date_picker();
	
}else{
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
}


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
        
    /*if (strlen($_POST['permitted_days']) == 0) {
        display_error(_("Permitted cannot be empty."));
        set_focus('permitted_days');
        return false;
    }
    if (!empty($_POST['permitted_days'])) {

        $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
        if (preg_match($regex, get_post('permitted_days')) != 0) {
            $input_error = 1;
            display_error(_("Only Numericals are allowed in Permitted Days."));

            set_focus('permitted_days');
            return false;
        }
    }*/
    
    
    
    
    if (strlen($_POST['occas_encash']) == 0) {
        display_error(_("Ocassion cannot be empty."));
        set_focus('occas_encash');
        return false;
    }

    if(get_occasion_master_by_id($_POST['occas_encash'])){
    
        
        if (!empty($_POST['freq'])) {
        $regex = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";

        if (preg_match($regex, $_POST['freq']) != 0) {
            display_error(_("Frequency should be always in Numbers."));
            set_focus('freq');
            return false;
        }
    }
    
        
        
    if (strlen($_POST['max_encash']) == 0) {
        display_error(_("Encashment of Leave(Max) cannot be empty."));
        set_focus('max_encash');
        return false;
    }
    if (!empty($_POST['max_encash'])) {

        $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
        if (preg_match($regex, get_post('max_encash')) != 0) {
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
    
    
    if ($_POST['min_encash'] > $_POST['max_encash']) {
        display_error('Encashment of leave max is always greater than Minimum');
        return false;
    }
    }
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

    
   if(!isLeaveEncashAlreadyCreated($_POST['emp_type'], $_POST['leave_type'],'',$_POST['occas_encash'])){
    add_leave_encash($_POST['emp_type'], $_POST['leave_type'], $_POST['occas_encash'], $_POST['freq'],  $_POST['max_encash'], $_POST['min_encash'], $_POST['min_bal'],$_POST['ecash_status'],$_POST['encash_base']);
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
    if(!isLeaveEncashAlreadyCreated($_POST['emp_type'], $_POST['leave_type'],$selected_id,$_POST['occas_encash'])){
    update_leave_encash($_POST['emp_type'], $_POST['leave_type'], $_POST['occas_encash'], $_POST['freq'],  $_POST['max_encash'], $_POST['min_encash'], $_POST['min_bal'], $selected_id,$_POST['ecash_status'],$_POST['encash_base']);
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

$result = get_leave_encash(check_value('show_inactive'));
//display_error($result);die;
start_form();

start_table(TABLESTYLE);
$th = array(_("Employee type"), _("Leave Type"), _("Occasion for encashment"), _("Frequency"), _("Max. Permitted Days"),  _('Min. Days to be Encashed'), 'Min. Remaining Balance','Encashment based upon', 'Edit', 'Delete',);
inactive_control_column($th);
table_header($th);

$k = 0;

$nos = db_num_rows($result);
if ($nos != 0) {

    while ($myrow = db_fetch($result)) {

        alt_table_row_color($k);
        label_cell(leaves_employee_type($myrow["employee_type"]));
        label_cell(leave_type($myrow["leave_type"])[0]);
        label_cell(get_occasion_master_by_id($myrow["occas_encash"],'occ_name'));
        label_cell($myrow["freq"]);
        label_cell($myrow["max_encash"]);
        label_cell($myrow["min_encash"]);
        label_cell($myrow["min_bal"]);
        $encash_based = explode(',',$myrow["encash_based"]);
        $encashment_based = array();
        foreach($encash_based as $key => $value){
           $encashment_based[]= leave_allowance($value)[0];
        }
        label_cell(implode(' + ',$encashment_based));
        inactive_control_cell($myrow["id"], $myrow["inactive"], 'kv_leave_encash', 'id');
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
 start_outer_table(TABLESTYLE2, "width='90%'"); // outer table
$edit_empl_id = Date("Y");
if ($selected_id != -1) {
    if ($Mode == 'Edit') {
        //editing an existing status code
        $myrow = get_leave_encash_edit($selected_id);
        $_POST['emp_type'] = $myrow["employee_type"];
        $_POST['leave_type'] = $myrow["leave_type"];
        $_POST['occas_encash'] = $myrow["occas_encash"];
        $_POST['freq'] = $myrow["freq"];
        $_POST['max_encash'] = $myrow["max_encash"];
        $_POST['min_encash'] = $myrow["min_encash"];
        $_POST['min_bal'] = $myrow["min_bal"];
        $_POST['encash_base'] = $myrow['encash_based'];

    }
    hidden('selected_id', $selected_id);
}
 table_section(1,'50%');
 
 $multli_select_height = 10 ;//on default 
 
 table_section_title('Leave Encashment Rules');

 kv_empl_type_list_row(_("Employee type :"), "emp_type", $_POST['emp_type'], false);

leavetype_list_row(_("Leave Type :"), 'leave_type', null, false, true);

//text_row_ex(_("Occasion for encashment"), 'occas_encash', 30, null, null, $_POST['occas_encash'], null, null, true);

//text_row_ex(_("Occasion for encashment"), 'occas_encash', 30, null, null, $_POST['occas_encash'], null, null, true);

occ_list_row(_("Occasion for encashment"), 'occas_encash', $_POST['occas_encash'], false, true);

if(get_occasion_master_by_id($_POST['occas_encash'])){
//display_error(get_occasion_master_by_name($_POST['occas_encash']));
text_row_ex(_("Max. Permitted Days"), 'max_encash', 30, null, null, $_POST['max_encash'], null, null, true);

text_row_ex(_("Min Days to be Encashed"), 'min_encash', 30, null, null, $_POST['min_encash'], null, null, true);

text_row_ex(_("Min. Remaining Balance"), 'min_bal', 30, null, null, $_POST['min_bal'], null, null, true);

text_row_ex(_("Frequency of Encashment"), 'freq', 30, null, null, autoFiller('freq'), null, null, true);

}
else
    $multli_select_height = false;
$Ajax->activate('_page_body');
table_section(2,'50%');
 
table_section_title('Encashment amount should be based on');
empl_earning_list_row('Earnings', 'encash_base', explode(',',$_POST['encash_base']),null, false,'',$multli_select_height);

end_outer_table();
if (list_updated('leave_type')) {
    $Ajax->activate('leave_type');
}
echo "<br/>";
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