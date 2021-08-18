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
$page_security = 'HR_TOURFORM';
$path_to_root = "../../..";
$tour_request_document_folder = 'tour_request_attachments';
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
add_access_extensions();
//page(_($help_context = "Allocation Request")); 
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_tour_db.inc");



$js = '';

if ($version_id['version_id'] == '2.4.1') {
   if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);

   if (user_use_date_picker())
        $js .= get_js_date_picker();
}else {
  //  if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    //if ($use_date_picker)
        $js .= get_js_date_picker();
}

page(_($help_context = "Tour Request"), @$_REQUEST['popup'], false, "", $js);
simple_page_mode(true);
$selected_component = $selected_id;
?>
<html lang="en">
    <head>
        <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js" ?>"></script>
    </head>


</html>
<?php

if(get_post('_tr_todate_changed') || get_post('_tr_fromdate_changed')){
    //$from_date=date("d-m-Y", strtotime($_POST["from_date"]));
    //$todate=date("d-m-Y", strtotime($_POST["to_date"]));
    $diff_mon=date_diff2($_POST["tr_fromdate"],$_POST["tr_todate"],'d');
    $num_of_days1 = (1+(-$diff_mon));
    if($num_of_days1 < 0){
        $_POST['tr_no_of_days']=0;
    }
    else{
        $_POST['tr_no_of_days']=(1+(-$diff_mon));
    }
    
    $Ajax->activate('tr_no_of_days');
    //echo $_POST['tr_no_of_days'];
}

function displayEmployeeTourRequests($employee_id) {
    $result = getTourRequestsByEmployeeId($employee_id);
    global $path_to_root, $tour_request_document_folder;
    div_start('bom');
    start_table(TABLESTYLE, "width='60%'");
    $th = array(_("Tour Request Id"),_("Request Date"), _("No. of Paxs."), _("From Date"), _("To Date"), _("No. of Days"), _("Place of Visit"), _("Document"), _("Status"), '', '');
    table_header($th);

    $k = 0;

    if ((db_num_rows($result)) != 0) {

        while ($myrow = db_fetch($result)) {

            alt_table_row_color($k);
            $leave_name = get_leave_type($myrow["type_leave"]);
            
            label_cell($myrow["tr_request_id"]);
            label_cell(sql2date($myrow["tr_request_date"]));
            label_cell($myrow["tr_no_of_paxs"]);
            label_cell(date("M j, Y, g:i a", strtotime($myrow["tr_fromdate"])));
            label_cell(date("M j, Y, g:i a", strtotime($myrow["tr_todate"])));
            label_cell($myrow["tr_no_of_days"]);
            label_cell($myrow["tr_place_of_visit"]);
            $company_path = company_path();
            $file_path = $company_path . '/' . $tour_request_document_folder . '/' . $myrow["tr_attachment_path"];
            if (isset($myrow["tr_attachment_path"]) && !empty($myrow["tr_attachment_path"])) {
                $file_name = pathinfo($file_path, PATHINFO_FILENAME);
            } else {
                $file_name = '';
            }
            label_cell('<a href="' . $file_path . '" target="_blank">' . $file_name . '</a>');
            global $config_master_value;
            $tour_master_status = $config_master_value['tour_status'];
            label_cell($tour_master_status[$myrow["tr_status"]]);
            if($myrow["tr_status"] == 3){//If status is rejected=3, hide edit/delete button
                label_cell('');
                label_cell('');
            }
            else{
                edit_button_cell("Edit" . $myrow['tr_id'], _("Edit"));
                delete_button_cell("Delete" . $myrow['tr_id'], _("Delete"));
                submit_js_confirm("Delete" . $myrow['tr_id'], sprintf(_("You are about to delete a leave request Do you want to continue?"), $myrow['tr_id']));
            }            
            

            // label_cell(view_link($myrow["allocate_id"])); 
            end_row();
        } //END WHILE LIST LOOP
    } else {
        label_cell(_("No Records Found"), 'colspan=8 align=center');
    }
    end_table();
    div_end();
}

function on_submit($employee_id, $selected_component = -1) {
    $tr_status = 1; //Waiting

    $request_date = date('Y-m-d', strtotime($_POST['tr_request_date']));
    $tr_fromdate = date('Y-m-d', strtotime($_POST['tr_fromdate']));
    $tr_todate = date('Y-m-d', strtotime($_POST['tr_todate']));
    $tr_fromdatetime = $tr_fromdate . ' ' . $_POST['time_tr_fromdate'];
    $tr_todatetime = $tr_todate . ' ' . $_POST['time_tr_todate'];
    global $Mode;
    if ($Mode == 'UPDATE_ITEM') {
        $tr_request_id = updateTourRequest($selected_component, $_POST['dept_id'], $_POST['desig_group_id'], $_POST['desig_id'], $_POST['tr_request_id'], $employee_id, $_POST['tr_single_group'], $_POST['tr_request_for'], $_POST['tr_no_of_paxs'], $_POST['tr_place_of_visit'], $request_date, $tr_fromdatetime, $tr_todatetime, $_POST['tr_no_of_days'], $_POST['tr_purpose_of_visit'], $_POST['tr_transport_by_company'], $_POST['tr_mode_of_transport'], $_POST['tr_accommodation_by'], $_POST['tr_advance_required'], $_POST['tr_advance_in'], $tr_status);
        display_notification(_('Tour Request has been updated'));
    } else {
        $tr_request_id = addTourRequest($employee_id, $_POST['dept_id'], $_POST['desig_group_id'], $_POST['desig_id'], $_POST['tr_single_group'], $_POST['tr_request_for'], $_POST['tr_no_of_paxs'], $_POST['tr_place_of_visit'], $request_date, $tr_fromdatetime, $tr_todatetime, $_POST['tr_no_of_days'], $_POST['tr_purpose_of_visit'], $_POST['tr_transport_by_company'], $_POST['tr_mode_of_transport'], $_POST['tr_accommodation_by'], $_POST['tr_advance_required'], $_POST['tr_advance_in'], $tr_status);
        display_notification(_('Tour Request has been added'));
    }
}

function can_process() {
    global $config_master_value;
    if ($_FILES['tr_attachment_path']['size'] > 0) {
        if (!in_array($_FILES['tr_attachment_path']['type'], $config_master_value['tour_doc_type'])) {
            display_error(_("Only PDF and Image files are allowed as attachments."));
            return false;
        }
    }
    if (date2sql($_POST['tr_fromdate']) > date2sql($_POST['tr_todate'])) {
        
            display_error("From Date should not be greater than To Date");
            set_focus('tr_no_of_days');
            return false;
        
    }
    
    return true;
}

$employee_id = $_SESSION['wa_current_user']->empl_id;
if (empty($employee_id)) {//If the logged doesn't have an user id.
    echo 'You are not allowed to apply for tour request.';
    exit;
}
$empl_job = get_employee_job($employee_id);
if ($empl_job) {
    $selected_parent = $session_empl_id;
    $selected_department = $empl_job['department'];
    $selected_desig_group = $empl_job['desig_group'];
    $selected_desig_id = $empl_job['desig'];
} else {
    echo 'Employee Department & Designation missing';
    exit;
}
//Showing Applied Tour request
start_table(TABLESTYLE_NOBORDER);
start_row();
label_row(_("Employee Id:"), $employee_id);
end_row();
//$Ajax->activate('_page_body');
end_table();
if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') {//If user has clicked on "Add" or "Update" button
    if ($Mode == 'ADD_ITEM' && can_process()) {
        on_submit($employee_id, $selected_id);
        //$selected_parent = $employee_id;
    } elseif ($Mode == 'UPDATE_ITEM' && can_process()) {
        on_submit($employee_id, $selected_id);
    }
} else {//If the page is loading first time
}
if ($Mode == 'Delete')
{
	deleteTourRequest($selected_id);

	display_notification(_("Tour Request has been deleted"));
	$Mode = 'RESET';
}
if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}
// Tour request form
start_form(true);

//display_employee_leave_records($selected_parent,$_POST['dept_id'],$_POST['desig_group_id'],$_POST['desig_id']);
displayEmployeeTourRequests($employee_id);

echo '<br>';

start_table(TABLESTYLE2);

if ($selected_id != -1) {
    if ($Mode == 'Edit') {

        //editing a selected component from the link to the line item
        $myrow = getTourRequestDetailById($selected_id, $employee_id);
        $_POST['tr_request_id'] = $myrow["tr_request_id"];
        $_POST['tr_single_group'] = $myrow["tr_single_group"];
        $_POST['tr_request_for'] = $myrow["tr_request_for"];
        $_POST['tr_no_of_paxs'] = $myrow["tr_no_of_paxs"];
        $_POST['tr_request_date'] = sql2date($myrow["tr_request_date"]);
        $_POST['tr_place_of_visit'] = $myrow["tr_place_of_visit"];
        $from_time = date('H:i:s', strtotime($myrow["tr_fromdate"]));
        $to_time = date('H:i:s', strtotime($myrow["tr_todate"]));

        $_POST['tr_fromdate'] = sql2date($myrow["tr_fromdate"]);
        $_POST['time_tr_fromdate'] = $from_time;
        $_POST['tr_todate'] = sql2date($myrow["tr_todate"]);
        $_POST['time_tr_todate'] = $to_time;
        $_POST['tr_no_of_days'] = $myrow["tr_no_of_days"];
        $_POST['tr_purpose_of_visit'] = $myrow["tr_purpose_of_visit"];
        $_POST['tr_transport_by_company'] = $myrow["tr_transport_by_company"];
        $_POST['tr_mode_of_transport'] = $myrow["tr_mode_of_transport"];
        $_POST['tr_accommodation_by'] = $myrow["tr_accommodation_by"];
        $_POST['tr_advance_required'] = $myrow["tr_advance_required"]; // by Tom Moulton
        $_POST['tr_advance_in'] = $myrow["tr_advance_in"];
        //label_row(_("Component:"), $myrow["component"] . " - " . $myrow["description"]);
    }
    hidden('selected_id', $selected_id);
}

div_start('tour');
hidden('employee_id', $employee_id);
hidden('dept_id', $selected_department);
hidden('desig_group_id', $selected_desig_group);
hidden('desig_id', $selected_desig_id);

hidden('tr_request_id', $tr_request_id);
//text_row_ex(_("Tour Id"), 'tour_id', 10,null,null,null,null,null,false,false);
custom_list_row(_("Single/Group"), 'tr_single_group', null, false, false, 'tour_single_group');
custom_list_row(_("Request For"), 'tr_request_for', null, false, false, 'tour_request_for');
text_row_ex(_("No. of Paxs."), 'tr_no_of_paxs', 10, null, null, 1, null, null, false, false);
date_row(_("Request Date") . ":", 'tr_request_date', 20, null, '', '', '', null, true);
text_row_ex(_("Place of Visit"), 'tr_place_of_visit', 10, null, null, NULL, null, null, false, false);
datetime_row(_("From Date &amp; Time") . ":", 'tr_fromdate', 20, null, '', '', '', null, true);
datetime_row(_("To Date &amp; Time") . ":", 'tr_todate', 20, null, '', '', '', null, true);
text_row_ex(_("No. of Days :"), 'tr_no_of_days', 10, null, null, 1, null, null, true, TRUE);
textarea_row(_("Purpose of Visit:"), 'tr_purpose_of_visit', null, 30, 4);
custom_list_row(_("Transport by ORG:"), 'tr_transport_by_company ', null, false, false);
text_row_ex(_("Mode of Transport"), 'tr_mode_of_transport', null, null, NULL, null, null, null, false, false);
custom_list_row(_("Accommodation by:"), 'tr_accommodation_by', null, false, false, 'tour_accommodation_by');
text_row_ex(_("Advance Required (&#8377;)"), 'tr_advance_required', 10, null, null, 0, null, null, false, false);
custom_list_row(_("Tour Advance in:"), 'tr_advance_in', null, false, false, 'tour_advance_in');
file_row(_("Attached File") . ":", 'tr_attachment_path', 'tr_attachment_path');
div_end();
end_table(1);
submit_add_or_update_center($selected_id == -1, '', 'both');
end_form();

end_page();
?>
<!--
<script type="text/javascript">

    $('input[name=tr_todate],input[name=tr_fromdate]').select(function () {

        var start_date = Date.parse($('input[name=tr_fromdate]').val());
        var end_date = Date.parse($('input[name=tr_todate]').val());
        //alert(end_date);
        var diff_date = end_date - start_date;
//alert(diff_date);
        var num_days = Math.floor(diff_date / 86400000);
//alert(num_days);
        var no_days = num_days + 1;

        $('input[name=tr_no_of_days]').val(no_days);
        $('input[name=tr_no_of_days]').attr('readonly', true);
    });
</script>-->