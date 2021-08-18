<?php
/**********************************************************************
  
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
$page_security = 'SA_ITEMSTRANSVIEW';
$path_to_root = "../..";

include($path_to_root . "/includes/session.inc");

page(_($help_context = "View Inventory Issue"), true);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

if (isset($_GET["trans_no"]))
{
	$trans_no = $_GET["trans_no"];
}
$sql = "SELECT desig_groups.name as dgs_group,desig_master.name as dsg_name,depart.description,empl_info.empl_firstname,purch_order.reference,purch_order.ord_date FROM ".TB_PREF."purch_orders purch_order LEFT JOIN ".TB_PREF."kv_departments AS depart ON depart.id = purch_order.department_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl_info ON empl_info.id = purch_order.employee_id 
LEFT JOIN ".TB_PREF."kv_desig_group AS desig_groups ON desig_groups.id = purch_order.desig_group LEFT JOIN ".TB_PREF."designation_master AS desig_master ON desig_master.id = purch_order.designation_id WHERE purch_order.order_no = ".$trans_no." "; 
$res = db_query($sql, "could not retreive the department name");
			$row = db_fetch_row($res);
			

display_heading(_("Inventory Issue") . " #" . $_GET['trans_no']);


start_outer_table(TABLESTYLE2, "width='80%'");
table_section(1);
label_row(_("Issue No.:"), $row[4]);
label_row(_("Issue Date"), $row[5]);
table_section(2);
label_row(_("Designation Group:"), $row[0]);
label_row(_("Designation:"),  $row[1]);
label_row(_("Department:"), $row[2]);
label_row(_("Employee:"),$row[3]);
end_outer_table();
start_table(TABLESTYLE, "width='90%'", 6);
display_heading2(_("Item Details"));

start_table(TABLESTYLE, "width='80%'");
$th = array(_("Item Code"), _("Item Description"), _("Quantity"));
table_header($th);
$sql = "SELECT  ord_details.item_code,ord_details.description,ord_details.quantity_ordered FROM ".TB_PREF."purch_order_details ord_details WHERE order_no = ".$trans_no."";
$res1 = db_query($sql, "could not retreive the department name");
while($result = db_fetch($res1)){
	
	label_cell($result['item_code']);
	label_cell($result['description']);
	label_cell($result['quantity_ordered']);
}
end_table();

//end_page(true, false, false, ST_INVENTORYISSUE, $trans_no);
?>