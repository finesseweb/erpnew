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
$page_security = 'SA_OPEN';
$path_to_root = "../../..";

include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

if (isset($_GET['dl']))
    $download_id = $_GET['dl'];
else
    $download_id = find_submit('download');


if ($download_id != -1) {

    $row = get_empl_alloc_single($download_id);
    if ($row['upload_file'] != "") {

        if (in_ajax()) {
            $Ajax->redirect($_SERVER['PHP_SELF'] . '?dl=' . $download_id);
        } else {
            //display_error($row['upload_file']);
            $type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';

            header("Content-type: " . $type);
            header('Content-Length: ' . $row['filesize']);
            header('Content-Disposition: attachment; filename=' . $row['upload_file']);
            echo file_get_contents(company_path() . "/leave_attachments/" . $row['unique_name']);
            exit();
        }
    }
}
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

page(_($help_context = "Leave Request"), @$_REQUEST['popup'], false, "", $js);
?>
<html lang="en">
    <head>
        <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js" ?>"></script>
    </head>


</html>  
<?php
simple_page_mode(true);
$fisical = get_current_fiscalyear();

$result = get_weekly_off();
                $woarr = array();
                    while ($weekly_off = db_fetch_assoc($result)) {
                    $woarr[$weekly_off['option_name']] = $weekly_off['option_value'];
                    }
$selected_component = $selected_id;
function download_link($row) {

    return button('download' . $row, _("Download"), _("Download"), ICON_DOWN);
}

if (isset($_GET['leave_status'])) {
    $_POST['leave_status'] = $_GET['leave_status'];
    $selected_parent = $_GET['leave_status'];
       
}

function display_employee_leave_status_records($selected_parent, $dept_id, $employee_id, $access_id, $from_date, $to_date) {
    
    $result = get_employee_leave_status_records($selected_parent, $dept_id, $employee_id, $access_id, $from_date, $to_date);
   
    div_start('bom');
    start_table(TABLESTYLE, "width='60%'");
    $th = array(_("Employee Name"), _("Leave Type"), _("Reason"),
        _("Requested From Date"), _("Requested To Date"), _("No. of Days"), _("Status"), _("No. of Days Approved"), _("Approved From Date"), _("Approved To Date"), _("Comments"), '', '', '');
    table_header($th);

    $k = 0;

    $nos = db_num_rows($result);
    if ($nos != 0) {

        while ($myrow = db_fetch($result)) {

            alt_table_row_color($k);
            $leave_name = get_leave_type($myrow["type_leave"]);
            $empl_name = get_employee_record($myrow["employees_id"]);
            label_cell($empl_name["empl_firstname"]);
            label_cell($leave_name["leave_type"]);
            label_cell($myrow["reason"]);
            label_cell(sql2date($myrow["from_date"]));
            label_cell(sql2date($myrow["to_date"]));

            label_cell($myrow["no_of_days"]);
            if ($myrow["status"] == 1) {
                $lv_sts = "Waiting";
            } else if ($myrow["status"] == 2) {
                $lv_sts = "Approved";
            } else if ($myrow["status"] == 3) {
                $lv_sts = "Rejected";
            }
            else if($myrow["status"] == 4)
            {
                 $lv_sts = "Waiting for Cancelation";
            }
            else if($myrow["status"] == 5)
            {
                $lv_sts = "Cancelation Requested Approved";
            }
            label_cell($lv_sts);
            label_cell($myrow["no_of_days_approved"]);
            label_cell(sql2date($myrow["approved_from_date"]));
            label_cell(sql2date($myrow["approved_to_date"]));
            label_cell($myrow["comments"]);
            edit_button_cell("Edit" . $myrow['allocate_id'], _("Edit"));
            //delete_button_cell("Delete".$myrow['allocate_id'], _("Delete"));
            //display_error($myrow['allocate_id']);
            $path_to_root = "../../..";

            label_cell('<a onclick="javascript:openWindow(this.href,this.target); return false;" href="' . $path_to_root . '/modules/ExtendedHRM/reports/rep810.php?PARAM_0=' . $myrow['allocate_id'] . '&rep_v=yes" target="_blank" class="printlink"> <img src="' . $path_to_root . '/themes/default/images/print.png" width="12" height="12" border="0" title="Print"> </a>');

            label_cell(download_link($myrow["allocate_id"]));
            end_row();
        } //END WHILE LIST LOOP
    } else {
        label_cell('No Records Found', 'colspan=15 align="center" size="15"');
    }

    end_table();
    div_end();
}

//--------------------------------------------------------------------------

function on_edit($selected_parent, $selected_component = -1) {


    $approved_from_date = date2sql($_POST['approved_from_date']);
    $approved_to_date = date2sql($_POST['approved_to_date']);
    $today_date = date('Y-m-d');
  update_leave_request($selected_component, $_POST['leave_status_type'], $_POST['no_of_days_approved'], $approved_from_date, $approved_to_date, $_POST['comments']);
    //update_allocation($selected_component,$_POST['dept_id'],$_POST['desig_group_id'],$_POST['desig_id'],$selected_parent,$_POST['type_leave'],$_POST['reason'],$today_date,$from_date,$to_date,$_POST['no_of_days'],$_POST['upload_file'],$_POST['filesize'],$_POST['filetype'],$_POST['unique_name'],$request_date);
                                          
  if($_POST['leave_status_type'] == 2){ //=======[if Leave is approved]
                            for($i=0;$i<(int)$_POST['no_of_days_approved']; $i++){
                                              $empl_id = $_POST['empl_id'];
                                              $attendance_date = strtotime($approved_from_date."+$i day");
                                              $month = date("m", $attendance_date);
                                               $day = date("d", $attendance_date);
                                               $year = get_fiscal_year_id_from_date(date("Y-m-d",$attendance_date));
                                                $holiday_date =  get_holiday_check(date("Y-m-d",$attendance_date), date("Y-m-d",$attendance_date), $fisical['id']);
                                            if (!db_has_day_attendancee($empl_id, $month, $year)) {
                                                    add_employee_attendance($_POST['leave_code'], $empl_id, $month, $year, $day, $emplDetails['department_id']);
                                                }
                                                else {
                                                        update_employee_attendance($_POST['leave_code'], $empl_id, $month, $year, $day);
                                                    
                                                }
                                            }
  }
                                             
    $Mode = 'RESET';
    display_notification(_('Leave Request has been updated'));
}

//---------------------------------------------------------------------------------------

function can_process() {

    $company_year = get_company_pref('f_year');

    $fiscal_year = get_fiscalyear($company_year);

    $financial_year_start_month = $fiscal_year['1'];
    $financial_year_end_month = $fiscal_year['2'];

    $apprv_from_date = date2sql($_POST['approved_from_date']);
    $apprv_to_date = date2sql($_POST['approved_to_date']);

    if (!empty($apprv_from_date) && !empty($apprv_to_date)) {
        /*if (!(($apprv_from_date >= $financial_year_start_month) && ($apprv_to_date <= $financial_year_end_month))) {

            display_error(_("Please select valid  dates according to fiscal year"));
            return false;
        }*/

        if (($apprv_from_date >= $financial_year_start_month) && ($apprv_to_date <= $financial_year_end_month)) {
            if ($apprv_from_date > $apprv_to_date) {

                display_error(_("Please select valid approved from date or  approved to date"));

                return false;
            }
        }
        $existed_from_date = date2sql($_POST['from_date1']);
        $existed_to_date = date2sql($_POST['to_date1']);
        if (!(($apprv_from_date >= $existed_from_date) && ($apprv_to_date <= $existed_to_date))) {

            display_error(_("Please select date range between requested from date and to date"));

            return false;
        }
    }
    if ($_POST['no_of_days_approved'] > $_POST['no_of_days']) {

        display_error('Entered no of days  are greater than the approved days');

        return false;
    }
    return true;
}

if ($Mode == 'Delete') {
    delete_empl_alloc($selected_id);
    display_notification(_("Leave Request has been deleted"));
    $Mode = 'RESET';
}

if ($Mode == 'RESET') {
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    $_POST['show_inactive'] = $sav;
}

//---------------------------------------------------------------------------------------

//start_form();

start_form(false, true);

start_table(TABLESTYLE_NOBORDER);
start_row();
date_row(_("From Date") . ":", 'from_date', 20, null, '', '', '', null, true);
date_row(_("To Date") . ":", 'to_date', 20, null, '', '', '', null, true);
department_list_row(_("Select a Department: "), 'department_id', null, true, true, check_value('show_inactive'));
employee_list_cells1(_("Select an Employee: "), 'employee_id', null, _('All Employees'), true, check_value('show_inactive'), false, $_POST["department_id"]);

leave_status_list_row(_("Leave Request Status:"), 'leave_status', null, true);

end_row();

if (list_updated('department_id') || list_updated('employee_id') || list_updated('leave_status') || get_post('_from_date_changed') || get_post('_to_date_changed'))
    $Ajax->activate('_page_body');


end_table();
br();

end_form();
//--------------------------------------------------------------------------------------------------

if (get_post('leave_status') != '') {
//Parent Item selected so display bom or edit component
    $selected_parent = $_POST['leave_status'];
    $selected_department = $_POST['department_id'];
    $selected_employee = $_POST['employee_id'];
    $selected_from_date = $_POST['from_date'];
    $selected_to_date = $_POST['to_date'];

    if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM')
        if ($Mode == 'ADD_ITEM' && can_process()) {
            on_submit($selected_parent, $selected_id);
        }
    if ($Mode == 'UPDATE_ITEM' && can_process()) {
        on_edit($selected_parent, $selected_id);
    }

    start_form(true);
    $session_access_id = $_SESSION['wa_current_user']->role_name;

    display_employee_leave_status_records($selected_parent, $selected_department, $selected_employee, $session_access_id, $selected_from_date, $selected_to_date);

    echo '<br>';
    $_POST['no_of_days_approved'] = $_POST['no_of_days'];
    $_POST['approved_from_date'] = $_POST['from_date1'];
    $_POST['approved_to_date'] = $_POST['to_date1'];
    start_table(TABLESTYLE2);

    if ($selected_id != -1) {

        if ($Mode == 'Edit') {

            //editing a selected component from the link to the line item
            $myrow = get_empl_alloc_single($selected_id);
            $empl_name = get_employee_record($myrow["employees_id"]);
            $_POST['employee_name'] = $empl_name['empl_firstname'];
            $leave_name = get_leave_type($myrow["type_leave"]);
            $_POST['request_date'] = sql2date($myrow["request_date"]);
            $_POST['type_leave'] = $leave_name["leave_type"]; // by Tom Moulton
            $_POST['reason'] = $myrow["reason"];
            $_POST['from_date1'] = sql2date($myrow["from_date"]);
            $_POST['to_date1'] = sql2date($myrow["to_date"]);
            $_POST['no_of_days'] = $myrow["no_of_days"];
            $_POST['leave_status_type'] = $myrow["status"];
            //$_POST['no_of_days_approved'] = $myrow["no_of_days_approved"];
            //$_POST['approved_from_date'] = sql2date($myrow["approved_from_date"]);
            //$_POST['approved_to_date'] = sql2date($myrow["approved_to_date"]);
            if ($_POST['no_of_days'] == $myrow["no_of_days_approved"]) {
                $_POST['no_of_days_approved'] = $_POST['no_of_days'];
            } else {
                $_POST['no_of_days_approved'] = $myrow["no_of_days_approved"];
            }
            if ($_POST['from_date1'] == $myrow["approved_from_date"]) {
                $_POST['approved_from_date'] = sql2date($_POST['from_date1']);
            } else {
                $_POST['approved_from_date'] = sql2date($myrow["approved_from_date"]);
            }
            if ($_POST['to_date1'] == $myrow["approved_to_date"]) {
                $_POST['approved_to_date'] = sql2date($_POST['to_date1']);
            } else {
                $_POST['approved_to_date'] = sql2date($myrow["approved_to_date"]);
            }
            $_POST['comments'] = $myrow["comments"];
            //label_row(_("Component:"), $myrow["component"] . " - " . $myrow["description"]);
        }
        hidden('employee_name', $_POST['employee_name']);
        hidden('type_leave', $_POST['type_leave']);
        hidden('leave_code', $leave_name['code']);
        hidden('empl_id', $myrow["employees_id"]);
        hidden('reason', $_POST['reason']);
        hidden('no_of_days', $_POST['no_of_days']);
        hidden('from_date1', $_POST["from_date1"]);
        hidden('to_date1', $_POST["to_date1"]);
        hidden('selected_id', $selected_id);
    }
    /* else
      {
      start_row();
      label_cell(_("Component:"), "class='label'");

      echo "<td>";
      echo stock_component_items_list('component', $selected_parent, null, false, true);
      if (get_post('_component_update'))
      {
      $Ajax->activate('quantity');
      }
      echo "</td>";
      end_row();
      } */
    hidden('leave_status', $selected_parent);
    hidden('department_id', $selected_department);
    hidden('employee_id', $selected_employee);
    hidden('from_date', $selected_from_date);
    hidden('to_date', $selected_to_date);

    label_row(_("Employee Name:"), $_POST['employee_name']);
    label_row(_("Leave Type:"), $_POST['type_leave']);
    label_row(_("Reason:"), $_POST['reason']);
    label_row(_("Requested From Date:"), ($_POST['from_date1']));
    label_row(_("Requested To Date:"), $_POST['to_date1']);
    label_row(_("No of Days:"), $_POST['no_of_days']);
    leave_status_list_row(_("Leave Request Status:"), 'leave_status_type', null, true);
    if (list_updated('leave_status_type')) {
        $Ajax->activate('_page_body');
    }

    if ($_POST['leave_status_type'] == 2 or $_POST['leave_status_type'] == 5  ) {
        //$_POST['no_of_days_approved'] = $_POST['no_of_days'];
        text_row_ex(_("No. of Days Approved:"), 'no_of_days_approved', 10);
        //$_POST['approved_from_date']=$_POST['from_date1'];
        //$_POST['approved_to_date']=$_POST['to_date1'];
        date_row(_("Approved From Date") . ":", 'approved_from_date', 20);
        date_row(_("Approved To Date") . ":", 'approved_to_date', 20);
    }

    textarea_row(_("Comments:"), 'comments', null, 26, 4);

    end_table(1);

    submit_add_or_update_center($selected_id == -1, '', 'both');

    end_form();

    end_page();
}
?>		
<script type="text/javascript">

    $('input[name=to_date],input[name=from_date]').select(function () {

        var start_date = Date.parse($('input[name=from_date]').val());
        var end_date = Date.parse($('input[name=to_date]').val());
        //alert(end_date);
        var diff_date = end_date - start_date;
//alert(diff_date);
        var num_days = Math.floor(diff_date / 86400000);
//alert(num_days);
        var no_days = num_days + 1;

        $('input[name=no_of_days]').val(no_days);
        $('input[name=no_of_days]').attr('readonly', true);
    });
    
    
 
 

    $(document).ready(function () {
        //alert('aaaaaa23');
        $("body").delegate('input[name=from_date]', 'focusout', function () {
            var start_date = Date.parse($('input[name=from_date]').val());
            var end_date = Date.parse($('input[name=to_date]').val());
            if (start_date > end_date) {
                //alert("Invalid From Date");
                return false;
            }
        });
    });
</script>
