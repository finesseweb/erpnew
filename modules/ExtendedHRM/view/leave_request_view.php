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
$path_to_root = "../../..";

include($path_to_root . "/includes/session.inc");
page(_($help_context = "Leave Request"), true);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include($path_to_root . "/includes/db_pager.inc");

	$all_id = $_GET['id'];

display_heading(_("Leave Request Id") . " #$all_id"); 

$sql = "SELECT req.*,des_group.name as grp_name,des.name as desig_name,dept.description as department,type.leave_type as leaves,empl.empl_firstname as employee FROM ".TB_PREF."kv_allocation_request AS req  LEFT JOIN ".TB_PREF."kv_desig_group AS des_group ON req.desig_group_id=des_group.id LEFT JOIN ".TB_PREF."designation_master AS des ON req.desig_id=des.id LEFT JOIN ".TB_PREF."kv_departments AS dept ON req.dept_id=dept.id LEFT JOIN ".TB_PREF."kv_type_leave_master AS type ON req.type_leave=type.type_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl  ON empl.id=req.employees_id WHERE req.allocate_id='$all_id '" ;
$res=db_query($sql);
$from_trans=db_fetch($res);
//display_error($from_trans['name']);

echo "<br>";
start_table(TABLESTYLE, "width='60%'");

start_row();
label_cells(_("Department"), $from_trans['department'], "class='tableheader'");
label_cells(_("Designation Group"), $from_trans['grp_name'], "class='tableheader'");

end_row();
start_row();
label_cells();
label_cells();
label_cells();
label_cells();
label_cells();
label_cells();
end_row();
start_row();
label_cells(_("Designation"), $from_trans['desig_name'], "class='tableheader'");
label_cells(_("Employee"), $from_trans['employee'], "class='tableheader'");
end_row();
end_table(1);

$sql2 = "SELECT req.*,des_group.name as grp_name,des.name as desig_name,dept.description as department,sec_type.leave_type as leaves,type.leave_type as sec_leaves,empl.empl_firstname as employee FROM ".TB_PREF."kv_allocation_request AS req  LEFT JOIN ".TB_PREF."kv_desig_group AS des_group ON req.desig_group_id=des_group.id LEFT JOIN ".TB_PREF."designation_master AS des ON req.desig_id=des.id LEFT JOIN ".TB_PREF."kv_departments AS dept ON req.dept_id=dept.id LEFT JOIN ".TB_PREF."kv_type_leave_master AS type ON req.type_leave=type.type_id LEFT JOIN ".TB_PREF."kv_type_leave_master AS sec_type ON req.second_leave_type=sec_type.type_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl  ON empl.id=req.employees_id WHERE req.allocate_id='$all_id '" ;
//display_error($sql2); die;
$items=db_query($sql2);
//$items=db_fetch($res2);
//display_error($items['leaves']);

/*if (db_num_rows($items)==0)
{
	display_note(_("There are no items for this Maintenance."));
}
else
{  */

//	display_heading2(_("Items"));
    echo "<br>";
    start_table(TABLESTYLE, "width='60%'");
    $th = array(_("S.No"), _("Leave Type"), _("Reason"), _("From Date"), _("To Date"), _("No.of Leaves"));
	table_header($th);

    $k = 0; //row colour counter
	$i=1;
    while ($item = db_fetch($items))
    {

    		alt_table_row_color($k);
        	label_cell($i);
    		label_cell($item["leaves"],"align=right");
			label_cell($item["reason"],"align=right");
    		label_cell($item["from_date"],"align=right");
    		label_cell($item["to_date"],"align=right");
    		label_cell($item["no_of_days"],"align=right");
    		end_row();
				
			if($item["second_no_of_days"] !=0){
			alt_table_row_color($k);
			label_cell($i+1);
    		label_cell($item["sec_leaves"],"align=right");
			label_cell($item["second_reason"],"align=right");
    		label_cell($item["second_from_date"],"align=right");
    		label_cell($item["second_to_date"],"align=right");
    		label_cell($item["second_no_of_days"],"align=right");
			}else{
			//label_cell("No Data");	
		}
    		end_row();
	$i++;	
	}
end_table(1);

//}

end_page(true, false, false, SA_HELPDESKVIEW, $process_id);
?>