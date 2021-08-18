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
$page_security = 'HR_OCCMASTER';
$path_to_root = "../../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Occasion Master"));

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

    
    if (strlen($_POST['occ_name']) == 0) {
        display_error(_("Encashment Days cannot be empty."));
        set_focus('occ_name');
        return false;
    }
    
    if (!empty($_POST['occ_name'])) {

        $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
        if (preg_match($regex, get_post('occ_name')) == 0) {
            $input_error = 1;
            display_error(_("Only Alphabets are allowed in occasion name"));

            set_focus('occ_name');
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


if(get_post('encashment_days')){
    $leave_field_name = leave_master_table_name($_POST['leave_type']);
    $leave_amount = getLeaveMasterValue($leave_field_name);
    
      $base_field = getEncashmentCalcBasedOn($_POST['leave_type'],$leave_amount);
      $amount = 0;
      if(!empty($base_field[0])){
          $encash_based = explode(',',$base_field[0]);
          foreach($encash_based as $key => $value){
             $amount += getEmployeeJobInfo($value);
          }
      }
 else {
        $amount = 0;
        display_error('You are not allowed for this request !');
      }    
      $total_days = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
      $oneDayAmount = round(($amount*12)/365);
      $_POST['amt'] = !empty($oneDayAmount) ? ($oneDayAmount * $_POST['encashment_days']):0;
}

//-----------------------------------------------------------------------------------

if ($Mode == 'ADD_ITEM' && can_process() && can_process1()) {

   
    
  // if(!isLeaveEncashAlreadyCreated($_POST['emp_type'], $_POST['leave_type'])){
    
    add_occasion_master($_POST['occ_name'], $_POST['yes_no']);
    display_notification(_('Leave days has been added'));
    $Mode = 'RESET';
   /*}
   else
   {
       display_error(_('Duplicate value cannot be inserted'));
   }*/
}

//-----------------------------------------------------------------------------------

if ($Mode == 'UPDATE_ITEM' && can_process() && can_process1()) {
    //display_error("sdff");die;
    //if(!isLeaveEncashAlreadyCreated($_POST['emp_type'], $_POST['leave_type'],$selected_id)){
    
    update_occasion_master($selected_id , $_POST['occ_name'], $_POST['yes_no']);
    $Mode = 'RESET';
    display_notification(_('Leave type has been updated'));
/*}
else{
    display_error(_('Duplicate value cannot be updated'));
}*/
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
 $addId = update_leave_encash_request_status($selected_id, 4);
 if($addId!=5)
    display_notification(_('Leave Encashment cancelation Request has been sent !'));
 else
     display_error('Approved Request cannot be cancled');
    $Mode = 'RESET';
}

if ($Mode == 'RESET') {
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    $_POST['show_inactive'] = $sav;
}
//-----------------------------------------------------------------------------------

$result = get_occasion_master(check_value('show_inactive'));
//display_error($result);die;
start_form();

start_table(TABLESTYLE);
$th = array(_("#"), _("Occasion Name"),"Restrict encash days", 'Edit');
inactive_control_column($th);
table_header($th);

$k = 0;

$nos = db_num_rows($result);
if ($nos != 0) {

    while ($myrow = db_fetch($result)) {

        alt_table_row_color($k);
        label_cell($myrow["id"]);
        label_cell($myrow['occ_name']);
        label_cell($myrow['yes_no']==0?'No':'Yes');
        inactive_control_cell($myrow["id"], $myrow["inactive"], 'kv_occasion_master', 'id');
        edit_button_cell("Edit" . $myrow['id'], _("Edit"));
      //  submit_js_confirm("Delete" . $myrow["id"], sprintf(_("You are about to delete a Type of Leave Master Do you want to continue?"), $myrow['id']));
        end_row();
    }
} else {
    label_cell('No Records Found', 'colspan=4 align="center" size="15"');
}
inactive_control_row($th);
end_table();
echo '<br>';

//-----------------------------------------------------------------------------------
 start_table(TABLESTYLE2); // outer table
$edit_empl_id = Date("Y");
if ($selected_id != -1) {
    if ($Mode == 'Edit') {
        //editing an existing status code
        $myrow = get_occasion_master_edit($selected_id);
        $_POST['occ_name'] = $myrow["occ_name"];
        $_POST['yes_no'] = $myrow["yes_no"];

    }
    hidden('selected_id', $selected_id);
}


text_row_ex(_("occasion Name"), 'occ_name', 30, null, null,$_POST['occ_name'], null, null, true);
yes_no(_("Restriction"), 'yes_no', $_POST['yes_no'], false);
$Ajax->activate('_page_body');
end_table();
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