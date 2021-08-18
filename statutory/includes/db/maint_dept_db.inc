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
function add_maintenance_dept($name,$depart_id, $desiggroup_id,$designation_id,$empl_id)
{
	$sql = "INSERT INTO ".TB_PREF."maintenance_department (name,depart_id, desiggroup_id,designation_id,empl_id) 
		VALUES (".db_escape($name).",".db_escape($depart_id).",".db_escape($desiggroup_id).",".db_escape($designation_id).",".db_escape($empl_id).")";
	//display_error($sql);	
	db_query($sql, "could not add Maintenance Department");		
}


function update_maintenance_dept($status_id,$name,$depart_id, $desiggroup_id,$designation_id,$empl_id)
{
	$sql = "UPDATE ".TB_PREF."maintenance_department SET name=".db_escape($name).",depart_id=".db_escape($depart_id).",desiggroup_id=".db_escape($desiggroup_id).",designation_id=".db_escape($designation_id).",
		empl_id=".db_escape($empl_id)." WHERE id=".db_escape($status_id);
	
	db_query($sql, "could not update Maintenance Department");			
}

function get_all_maintenance_dept($all=false)
{
	$sql = "SELECT maint_dept.*,des_group.name as grp_name,des.name as desig_name,dept.description as department,empl.empl_firstname as employee FROM ".TB_PREF."maintenance_department AS maint_dept,".TB_PREF."kv_desig_group as des_group,".TB_PREF."designation_master as des,".TB_PREF."kv_departments as dept,".TB_PREF."kv_empl_info AS empl WHERE maint_dept.depart_id=dept.id AND maint_dept.desiggroup_id=des_group.id AND maint_dept.designation_id=des.id AND empl.id=maint_dept.empl_id";
	
	// display_error($sql);
	if (!$all) $sql .= " AND !maint_dept.inactive";

	return db_query($sql, "could not get all Maintenace Department");
} 

function get_maintain_dept($status_id)
{
	$sql = "SELECT * FROM ".TB_PREF."maintenance_department WHERE id=".db_escape($status_id);
	//display_error($sql);
	$result = db_query($sql, "could not get Maintenance Department");
	
	return db_fetch($result);
}

function delete_maint_dept($status_id)
{
	$sql="DELETE FROM ".TB_PREF."maintenance_department WHERE id=".db_escape($status_id);
		
	db_query($sql, "could not delete Maintenace Department");	
}

?>