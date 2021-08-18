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
$page_security = 'HR_ENCASHMENT_REQUEST';
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
    

    
    if (strlen($_POST['encashment_days']) == 0) {
        display_error(_("Encashment Days cannot be empty."));
        set_focus('max_encash');
        return false;
    }
    if (!empty($_POST['encashment_days'])) {

        $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
        if (preg_match($regex, get_post('encashment_days')) != 0) {
            $input_error = 1;
            display_error(_("Only Numericals are allowed in encashment_days"));

            set_focus('encashment_days');
            return false;
        }
    }

    if (strlen($_POST['amt']) == 0) {
        display_error(_("Amount cannot be empty."));
        set_focus('amt');
        return false;
    }
    if (!empty($_POST['amt'])) {

        $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{}';?<>]/";
        if (preg_match($regex, get_post('amt')) != 0) {
            $input_error = 1;
            display_error(_("Only Numericals are allowed in Amount "));

            set_focus('amt');
            return false;
        }
        

 if($_POST['amt'] == 0){
                display_error('Encashment amount should not be 0');
             return false;
         }
            }
     if($_POST['freq'] == 0 ){
             display_error('No attempt left for this calendar year !');
             return false;
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

$add_update = true ;
if(get_post('encashment_days')){
    $leave_field_name = leave_master_table_name($_POST['leave_type']);
    $leave_amount = getLeaveMasterValue($leave_field_name);
    
        
      $base_field = getEncashmentCalcBasedOn($_POST['leave_type'],$leave_amount,get_occasion_master_by_id($_POST['reason']),$_POST['encashment_days']);
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
        $add_update = false;
      }    
     // $total_days = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
      $oneDayAmount = round(($amount*12)/365);
      $_POST['amt'] = !empty($oneDayAmount) ? ($oneDayAmount * $_POST['encashment_days']):0;
}

//-----------------------------------------------------------------------------------

if ($Mode == 'ADD_ITEM' && can_process() && can_process1() && $add_update) {

   
    
  // if(!isLeaveEncashAlreadyCreated($_POST['emp_type'], $_POST['leave_type'])){
    
    add_leave_encash_request($_POST['request_id'], $_POST['leave_type'], $_POST['reason'], $_POST['encashment_days'],  $_POST['amt'], $_POST['request_date1'], $_POST['remarks']);
    display_notification(_('Leave days has been added'));
    $Mode = 'RESET';
   /*}
   else
   {
       display_error(_('Duplicate value cannot be inserted'));
   }*/
}

//-----------------------------------------------------------------------------------

if ($Mode == 'UPDATE_ITEM' && can_process() && can_process1() && $add_update) {
    //display_error("sdff");die;
    //if(!isLeaveEncashAlreadyCreated($_POST['emp_type'], $_POST['leave_type'],$selected_id)){
    
    update_leave_encash_request($selected_id , $_POST['request_id'], $_POST['leave_type'], $_POST['reason'], $_POST['encashment_days'],  $_POST['amt'], $_POST['request_date1'], $_POST['remarks']);
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
$session_empl_id = $_SESSION['wa_current_user']->empl_id;
if (empty($session_empl_id)) {
    echo 'This panel is only for the employee of the organization.';
    exit;
}
//-----------------------------------------------------------------------------------

$result = get_leave_encash_request(check_value('show_inactive'));
//display_error($result);die;
start_form();

start_table(TABLESTYLE);
$th = array(_("Request ID"), _("Leave Type"), _("Reason for encashment"), _("Encashment Days"),  'Encashment Amt', 'Requested Date','Your Remarks','Approved Days','Approved Amount','Approved Date','Administrator Comments','status', 'Edit','Cancel Request');
inactive_control_column($th);
table_header($th);

$k = 0;

$nos = db_num_rows($result);
if ($nos != 0) {

    while ($myrow = db_fetch($result)) {

        alt_table_row_color($k);
        label_cell($myrow["request_id"]);
        label_cell(leave_type($myrow["leave_type"])[0]);
        label_cell(get_occasion_master_by_id($myrow["reason"],'occ_name'));
        label_cell($myrow["encash_days"]);
        label_cell($myrow["encash_amt"]);
        label_cell(sql2date($myrow["encash_request_date"]));
        label_cell($myrow["remarks"]);
        label_cell($myrow["no_of_days_approved"]);
        label_cell($myrow["approved_amount"]);
        label_cell(sql2date($myrow["approved_date"]));
        label_cell($myrow["comments"]);
        label_cell($leave_approval_status[$myrow["status"]]);
        
        inactive_control_cell($myrow["id"], $myrow["inactive"], 'kv_encashment_request', 'id');
            if($myrow['status']!='2' && $myrow['status']!='5' && $myrow['status']!='3'){
                     edit_button_cell("Edit" . $myrow['id'], _("Edit"));
            }
        else {
                label_cell('unmutable');
            }
            if($myrow['status']!='3'){
      delete_button_cell("Delete" . $myrow['id'], _("Delete"));
            }
 else {
     label_cell('cannot cancled');
 }
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
        $myrow = get_leave_encash_request_edit($selected_id);
        $_POST['request_id'] = $myrow["request_id"];
        $_POST['leave_type'] = $myrow["leave_type"];
        $_POST['reason'] = $myrow["reason"];
        $_POST['encashment_days'] = $myrow["encash_days"];
        $_POST['amt'] = $myrow["encash_amt"];
        $_POST['request_date'] = $myrow["encash_request_date"];
        $_POST['remarks'] = $myrow["remarks"];

    }
    hidden('selected_id', $selected_id);
}

label_row(_("Leave Request ID"),isset($_POST['request_id'])?$_POST['request_id']:nextId('kv_encashment_request','request_id', db_escape('r-%')));
hidden('request_id',isset($_POST['request_id'])?$_POST['request_id']:nextId('kv_encashment_request','request_id', db_escape('r-%')));

$empl_type = getEmployeeJobInfo1('employee_type',$_SESSION['wa_current_user']->empl_id);
$_POST['empl_type'] = $empl_type;
leavetype_list_row_type(_("Leave Type :"), 'leave_type', null, false, true,'',$empl_type);

ocassion_list_row(_('Reason For Encashment'), 'reason', autoFiller('reason'));

text_row_ex(_("Encashment Days"), 'encashment_days', 30, null, null, autoFiller('encashment_days'), null, null, true);

label_row(_("Encashment Amount"), autoFiller('amt'));
hidden('amt',autoFiller('amt'));

 $freq = get_leave_encash_freq($_POST['leave_type']);
 $request_num  =  get_leave_encash_request_freq($_POST['leave_type']);
 $freq = $freq - $request_num; 

 label_row(_("Attempt"),!empty($freq)?$freq:0);
hidden('freq',!empty($freq)?$freq:0);

date_row(_("Request Date") . ":", 'request_date', 30, null, '', '', '', null, true, 'requestDate');
hidden('request_date1',$_POST['request_date'] );
textarea_row(_("Remarks:"), 'remarks', null, 26, 4);
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