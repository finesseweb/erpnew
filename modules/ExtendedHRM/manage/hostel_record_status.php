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
$page_security = 'ROOM_STATUS';
$path_to_root = "../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
page(_($help_context = "Room Status"));

include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
?>
<html lang="en">
    <head>
        <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js" ?>"></script>
    </head>


</html>
<?php
$result = get_Room_status(check_value('show_inactive'));

//display_error($result);die;
start_form();
start_table(TABLESTYLE, "width=40%");
$th = array(_("#"), _(" Name ( Id )"), _("Room No. "), _("Bed No."),_('Remarks'), _("Status"), _("Date"));
inactive_control_column($th);

table_header($th);

$k = 0;
$name = '';
$nos = db_num_rows($result);
if ($nos != 0) {
    $i = 1;
    while ($myrow = db_fetch($result)) {
        alt_table_row_color($k);
        label_cell($i, "align='center'");
        if ($myrow["bed_id"] == $myrow["id"]) {
            if (substr($myrow["stu_id"], 0, 3) == 'PDM') {
                $name = student_information($myrow["stu_id"])[1];
                set_global_connection(0);
            } else if (substr($myrow["stu_id"], 0, 3) == 'EMP' || substr($myrow["stu_id"], 0, 2) == 'VF') {
                $name = faculty_staff_information($myrow["stu_id"])[1];
            }else if($myrow["stu_id"]!=''){
                $name = guest_information($myrow["stu_id"])[0];
            }
        } else {
            $name = ' -';
        }
        label_cell($name);
        label_cell($myrow["room_no"], "align='center'");
        label_cell($myrow["bed_no"], "align='center'");
        label_cell($myrow["remark"], "align='center'");
        label_cell($myrow["inactive"] == 1 ? 'Occupide' : 'Vacant', "align='center'");
        //inactive_control_cell($myrow["room_id"], $myrow["inactive"], 'room_master', 'room_id');
        label_cell($myrow['from_date'] != '' ? date('d - m - Y', strtotime($myrow['from_date'])) : '-', "align='center'");
        //edit_button_cell("Edit".$myrow['room_id'], _("Edit"));
        //delete_button_cell("Delete".$myrow['room_id'], _("Delete"));
        submit_js_confirm("Delete" . $myrow["room_id"], sprintf(_("You are about to Inactive Record?"), $myrow['room_id']));
        end_row();
        $i++;
    }
} else {
    label_cell('No Records Found', 'colspan=6 align="center" size="15"');
}

//inactive_control_row($th);
end_table();
//submit_add_or_update_center($selected_id == -1, '', 'both');
end_form();

//------------------------------------------------------------------------------------

end_page();
?>
