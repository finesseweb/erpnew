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
$page_security = 'HR_EMPLOYEE_INQ';
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


$version_id = get_company_prefs('version_id');
$js = '';

if ($version_id['version_id'] == '2.4.1') {
    if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);

    if (user_use_date_picker())
        $js .= get_js_date_picker();
}else {
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
}

page(_($help_context = "Tour Approval"), @$_REQUEST['popup'], false, "", $js);
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

function displayEmployeeTourRequests($filter_tr_status, $selected_employee, $selected_department, $session_access_id,$selected_from_date,$selected_to_date) {
    $result = getTourRequestsByFilter($filter_tr_status, $selected_employee, $selected_department, $session_access_id,$selected_from_date,$selected_to_date);
    global $path_to_root, $tour_request_document_folder;
    div_start('tour_request_filter');
    start_table(TABLESTYLE, "width='60%'");
    $th = array( _("Tour Request Id"), _("Emp. Id"), _("Emp. Name"), _("Department"), _("Place of Visit"), _("Request Date"), _("No. of Paxs."), _("From Date"), _("To Date"), _("No. of Days"), _("Document"), _("Status"), _("Remarks"), '', '');
    table_header($th);

    $k = 0;

    if ((db_num_rows($result)) != 0) {

        while ($myrow = db_fetch($result)) {

            alt_table_row_color($k);
            $leave_name = get_leave_type($myrow["type_leave"]);
            $myrow1 = get_employee_record($myrow['tr_employee_id']); 
            $empl_name = $myrow1['empl_firstname'];
            //Fetching Tour data
            
            
            label_cell($myrow["tr_request_id"]);
            
            label_cell($myrow['tr_employee_id']);
            label_cell($empl_name);
            label_cell($myrow1["dept_name"]);
            label_cell($myrow["tr_place_of_visit"]);
            label_cell(sql2date($myrow["tr_request_date"]));
            label_cell($myrow["tr_no_of_paxs"]);
            label_cell(date("M j, Y, g:i a", strtotime($myrow["tr_fromdate"])));
            label_cell(date("M j, Y, g:i a", strtotime($myrow["tr_todate"])));
            label_cell($myrow["tr_no_of_days"]);
            $company_path = company_path();
            $file_path = $company_path . '/' . $tour_request_document_folder . '/' . $myrow["tr_attachment_path"];
            if(isset($myrow["tr_attachment_path"]) && !empty($myrow["tr_attachment_path"])){
                $file_name = pathinfo($file_path, PATHINFO_FILENAME);
            }
            else{
                $file_name = '';
            }
            label_cell('<a href="' . $file_path . '" target="_blank">' . $file_name . '</a>');
            global $config_master_value;
            $tour_master_status = $config_master_value['tour_status'];
            label_cell($tour_master_status[$myrow["tr_status"]]);
            label_cell($myrow["tr_comment_by_approval"]);
            edit_button_cell("Edit" . $myrow['tr_id'], _("Edit"));
            delete_button_cell("Delete" . $myrow['tr_id'], _("Delete"));
            submit_js_confirm("Delete" . $myrow['tr_id'], sprintf(_("You are about to delete a leave request Do you want to continue?"), $myrow['allocate_id']));

            // label_cell(view_link($myrow["allocate_id"])); 
            end_row();
        } //END WHILE LIST LOOP
    } else {
        label_cell(_("No Records Found"), 'colspan=8 align=center');
    }
    end_table();
    div_end();
}

function on_submit($selected_component = -1) {
    

    //$request_date = date('Y-m-d', strtotime($_POST['tr_request_date']));
    //$tr_fromdate = date('Y-m-d', strtotime($_POST['tr_fromdate']));
    //$tr_todate = date('Y-m-d', strtotime($_POST['tr_todate']));
    //$tr_fromdatetime = $tr_fromdate . ' ' . $_POST['time_tr_fromdate'];
    //$tr_todatetime = $tr_todate . ' ' . $_POST['time_tr_todate'];
    global $Mode;
    if ($Mode == 'UPDATE_ITEM') {        
        $tr_request_id = approveTourRequest($selected_component, $_POST['tr_status'], $_POST['tr_comment_by_approval']);
        display_notification(_('Tour Request has been updated'));
    } 
}

function can_process() {
    return true;
}

$employee_id = $_SESSION['wa_current_user']->empl_id;
/**
if (empty($employee_id)) {//If the logged doesn't have an user id.
    echo 'You are not allowed to apply for tour request.';
    exit;
}
 * 
 */
//Showing Applied Tour request
start_table(TABLESTYLE_NOBORDER);
start_row();
end_row();
//$Ajax->activate('_page_body');
end_table();
if ($Mode == 'UPDATE_ITEM') {//If user has clicked on "Add" or "Update" button
    if (can_process()) {
        on_submit($selected_id);
    }
} else {//If the page is loading first time
}
start_form(false, true);

start_table(TABLESTYLE_NOBORDER);
start_row();
date_row(_("From Date") . ":", 'from_date',20,null,'','','',null,true);
date_row(_("To Date") .":", 'to_date',20,null,'','','',null,true);
department_list_row(_("Select a Department: "), 'department_id', null,	true, true, check_value('show_inactive'));
employee_list_cells1(_("Select an Employee: "), 'employee_id', null,_('All Employees'), true, check_value('show_inactive'),false,$_POST["department_id"]);
	
//custom_list_row(_("Tour Request Status:"),'filter_tr_status',null,true);
custom_list_row(_("Tour Request Status"), 'filter_tr_status', null, TRUE, false, 'tour_status');
end_row();

if(list_updated('department_id') || list_updated('employee_id') || list_updated('filter_tr_status') || get_post('_from_date_changed') || get_post('_to_date_changed')){
    $Ajax->activate('_page_body');
}
end_table();
br();

end_form();

$filter_tr_status = $_POST['filter_tr_status'];
$selected_department = $_POST['department_id'];
$selected_employee = $_POST['employee_id'];
$selected_from_date = $_POST['from_date'];
$selected_to_date = $_POST['to_date'];
// Tour request form
start_form(true);
$session_access_id = $_SESSION['wa_current_user']->role_name;
//display_employee_leave_records($selected_parent,$_POST['dept_id'],$_POST['desig_group_id'],$_POST['desig_id']);
if(empty($filter_tr_status)){
    $filter_tr_status = 1;//1 = Waiting
}
displayEmployeeTourRequests($filter_tr_status, $selected_employee, $selected_department, $session_access_id,$selected_from_date,$selected_to_date);
if ($selected_id != -1) {
    if ($Mode == 'Edit') {

        
        //editing a selected component from the link to the line item
        $myrow = getTourRequestDetailByIdForApproval($selected_id);
        
        //Fetch employee
        $myrow1 = get_employee_record($myrow['tr_employee_id']);
        $empl_name = $myrow1['empl_firstname'];

        $_POST['tr_request_id'] = $myrow["tr_request_id"];
        $_POST['tr_single_group'] = $myrow["tr_single_group"];
        $_POST['tr_request_for'] = $myrow["tr_request_for"];
        $_POST['tr_no_of_paxs'] = $myrow["tr_no_of_paxs"];
        $_POST['tr_request_date'] = sql2date($myrow["tr_request_date"]);
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
        $_POST['tr_status'] = $myrow["tr_status"];
        $_POST['tr_comment_by_approval'] = $myrow["tr_comment_by_approval"];
        //label_row(_("Component:"), $myrow["component"] . " - " . $myrow["description"]);
    }
    hidden('selected_id', $selected_id);
}

echo '<br>';

if ($selected_id != -1) {
    if ($Mode == 'Edit') {
start_table(TABLESTYLE2);


global $config_master_value;

div_start('tour');

        hidden('employee_id', $employee_id);
        hidden('tr_request_id', $tr_request_id);
//text_row_ex(_("Tour Id"), 'tour_id', 10,null,null,null,null,null,false,false);
        label_row(_("Employee Name"), $empl_name);
        label_row(_("Single/Group"), $config_master_value['tour_single_group'][$_POST['tr_single_group']]);
        label_row(_("Request For"), $config_master_value['tour_request_for'][$_POST['tr_request_for']]);
        label_row(_("No. of Paxs."), $_POST['tr_no_of_paxs']);
        label_row(_("Request Date"), $_POST['tr_request_date']);
        label_row(_("From Date &amp; Time"), date("M j, Y, g:i a",strtotime($myrow["tr_fromdate"])));
        label_row(_("T0 Date &amp; Time"), date("M j, Y, g:i a",strtotime($myrow["tr_todate"])));
        label_row(_("No. of Days"), $_POST['tr_no_of_days']);
        label_row(_("Purpose of Visit"), $_POST['tr_purpose_of_visit']);
        label_row(_("Transport by DMI"), $config_master_value['yes_no_select_box'][$_POST['tr_transport_by_company']]);
        label_row(_("Mode of Transport"), $_POST['tr_mode_of_transport']);
        label_row(_("Accommodation by"), $config_master_value['tour_accommodation_by'][$_POST['tr_accommodation_by']]);
        label_row(_("Advance Required"), $_POST['tr_advance_required']);
        label_row(_("Tour Advance in:"), $config_master_value['tour_advance_in'][$_POST['tr_advance_in']]);
        label_row(_("Document"), $_POST['tr_request_for']);
        custom_list_row(_("Status"), 'tr_status', null, false, false, 'tour_status');
        textarea_row(_("Comments:"), 'tr_comment_by_approval', null, 26, 4);



/*

  custom_list_row(_("Single/Group"), 'tr_single_group', null, false, false, 'tour_single_group');
  custom_list_row(_("Request For"), 'tr_request_for', null, false, false, 'tour_request_for');
  text_row_ex(_("No. of Paxs."), 'tr_no_of_paxs', 10, null, null, null, null, null, false, false);
  date_row(_("Request Date") . ":", 'tr_request_date', 20, null, '', '', '', null, true);
  datetime_row(_("From Date &amp; Time") . ":", 'tr_fromdate', 20, null, '', '', '', null, true);
  datetime_row(_("T0 Date &amp; Time") . ":", 'tr_todate', 20, null, '', '', '', null, true);
  text_row_ex(_("No. of Days :"), 'tr_no_of_days', 10, null, null, 1, null, null, false, false);
  textarea_row(_("Purpose of Visit:"), 'tr_purpose_of_visit', null, 30, 4);
  custom_list_row(_("Transport by DMI:"), 'tr_transport_by_company ', null, false, false);
  text_row_ex(_("Mode of Transport"), 'tr_mode_of_transport', 10, null, 'Flight/Train/Bus/Car', null, null, null, false, false);
  custom_list_row(_("Accommodation by:"), 'tr_accommodation_by', null, false, false, 'tour_accommodation_by');
  text_row_ex(_("Advance Required"), 'tr_advance_required', 10, null, null, 0, null, null, false, false);
  custom_list_row(_("Tour Advance in:"), 'tr_advance_in', null, false, false, 'tour_advance_in');
  file_row(_("Attached File") . ":", 'tr_attachment_path', 'tr_attachment_path');
 * 
 */
div_end();
end_table(1);
submit_add_or_update_center($selected_id == -1, '', 'both');
    }
}
end_form();

end_page();

