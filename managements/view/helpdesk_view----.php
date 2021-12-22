<?php
/**********************************************************************
  
	Released underhe GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
$page_security = 'SA_HELPDESKVIEW';
$path_to_root = "../..";

include($path_to_root . "/includes/session.inc");
page(_($help_context = "View Help Desk"), true);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");

if (isset($_GET['id']))
{
	$help_id = $_GET['id'];
	
}

// get the Process info
$sql = "SELECT desk.*,CASE WHEN(desk.category = '1')  THEN  'Employee'  WHEN (desk.category = '2') THEN 'Student' END AS cat_name,CASE WHEN(desk.emp_id !=0)  THEN  emp.empl_firstname  WHEN (desk.stu_name !='') THEN desk.stu_name END AS name,m_dept.name AS maintain_dept FROM ".TB_PREF."maintenance_help_desk AS desk LEFT JOIN ".TB_PREF."kv_empl_info AS emp ON  desk.emp_id=emp.id LEFT JOIN ".TB_PREF."maintenance_department AS m_dept ON m_dept.id=desk.maintain_dept_id WHERE desk.help_id=$help_id";

$result= db_query($sql,"something went wrong");
 if (db_num_rows($result) != 1)
	display_db_error("duplicate Process Records found", ""); 

$from_trans = db_fetch_row($result);
display_heading(_("Development Management Institute, Patna"));
display_note(_(""),1,1,'<span style="text-align:left;">To</span>');
$voided = is_voided_display(SA_HELPDESKVIEW, $process_id, _("This Help Desk has been voided."));

end_page(true, false, false, SA_HELPDESKVIEW, $process_id);
?>