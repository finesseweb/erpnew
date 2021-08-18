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

$page_security = 'HR_DMIHOLIDAY';
$path_to_root = "../../..";

include($path_to_root . "/includes/session.inc");
add_access_extensions();

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


//page(_($help_context = "Calendar Master")); 
// include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");
page(_($help_context = "Calendar Master"), @$_REQUEST['popup'], false, "", $js);
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
//-----------------------------------------------------------------------------------
set_global_connection(1);
$result = get_calendar($show_inactive);

//display_error($result);die;
start_form();
start_table(TABLESTYLE);
$th = array(_("S.No."), _("Particulars"), _("Day"), _("Starting Date"), _("Ending Date"),);
table_header($th);

$k = 0;

$nos = db_num_rows($result);
//print_r(db_fetch($result));
$i = 1;
if ($nos != 0) {
    $cd1 = "";
    while ($myrow = db_fetch($result)) {

        alt_table_row_color($k);
        label_cell($i, 'align="center"');
        label_cell(ucfirst($myrow["title"]));
        $date = $myrow["start"];
        $end_date = $myrow["end"];

        while (strtotime($date) <= strtotime($end_date)) {
            $cd1.=date(" l,", strtotime($date));
            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
        }
        $cd1 = rtrim($cd1, ',');
        label_cell($cd1);
        $cd1 = "";
        for($j=0; $j<count($start_date_end_date); $j++){
        $_SESSION['calendar_leave_list'][$i][$start_date_end_date[$j]] = date('d-m-Y', strtotime($myrow[$start_date_end_date[$j]]));
        }
        label_cell(date('d-m-Y', strtotime($myrow["start"])), 'align="center"');
        label_cell(date('d-m-Y', strtotime($myrow["end"])), 'align="center"');
        end_row();

        $i++;
    }
} else {
    label_cell('No Records Found', 'colspan=7 align="center" size="15"');
} 
end_table();
echo '<br>';

end_form();

end_page();
?>
 

