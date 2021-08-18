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
function add_helpdesk($helpdesk_date,$category,$dept_id,$desgroup_id, $desig_id,$emp_id,$stu_name,$maintain_dept_id,$issues)
{
   //display_error('sdfdfd');   
   if(!empty($stu_name)){
	$dept = 0;
	$dgroup =0;
	$designation =0;
	$empl = 0;
	$sql = "INSERT INTO ".TB_PREF."maintenance_help_desk (helpdesk_date,category,dept_id,desgroup_id, desig_id,emp_id,stu_name,maintain_dept_id,issues) 
		VALUES (".db_escape($helpdesk_date).",".db_escape($category).",".db_escape($dept).",".db_escape($dgroup).",".db_escape($designation).",".db_escape($empl).",".db_escape($stu_name).",".db_escape($maintain_dept_id).",".db_escape($issues).")";
	db_query($sql, "could not add Help Desk Complaint");	
	}
    else{
	//display_error($helpdesk_date);
	$student ='';
     $sql = "INSERT INTO ".TB_PREF."maintenance_help_desk (helpdesk_date,category,dept_id,desgroup_id, desig_id,emp_id,stu_name,maintain_dept_id,issues) 
		VALUES (".db_escape($helpdesk_date).",".db_escape($category).",".db_escape($dept_id).",".db_escape($desgroup_id).",".db_escape($desig_id).",".db_escape($emp_id).",".db_escape($student).",".db_escape($maintain_dept_id).",".db_escape($issues).")";
	//display_error($sql);
	db_query($sql, "could not add Help Desk Complaint");		
      
    }	
	// db_query($sql, "could not add Help Desk Complaint");		
}


function update_helpdesk($selected_id,$helpdesk_date,$category,$dept_id,$desgroup_id, $desig_id,$emp_id,$stu_name,$maintain_dept_id,$issues)
{
  
    if(!empty($stu_name)){
	$dept = 0;
	$dgroup =0;
	$designation =0;
	$empl = 0;
	$sql = "UPDATE ".TB_PREF."maintenance_help_desk SET helpdesk_date=".db_escape($helpdesk_date).",category=".db_escape($category).",dept_id=".db_escape($dept).",desgroup_id=".db_escape($dgroup).",desig_id=".db_escape($designation).",emp_id=".db_escape($empl).",stu_name=".db_escape($stu_name).",maintain_dept_id=".db_escape($maintain_dept_id).",issues=".db_escape($issues)." WHERE help_id=".db_escape($selected_id);
	db_query($sql, "could not update Help Desk Complaint");	
   }
	else{
	$student ='';
	        $sql = "UPDATE ".TB_PREF."maintenance_help_desk SET helpdesk_date=".db_escape($helpdesk_date).",category=".db_escape($category).",dept_id=".db_escape($dept_id).",desgroup_id=".db_escape($desgroup_id).",desig_id=".db_escape($desig_id).",emp_id=".db_escape($emp_id).",stu_name=".db_escape($student).",maintain_dept_id=".db_escape($maintain_dept_id).",issues=".db_escape($issues)." WHERE help_id=".db_escape($selected_id);
	  db_query($sql, "could not update Help Desk Complaint");
	}   
}

function get_all_helpdeskcomplaints($all=false)
{
	$sql = "SELECT desk.*, CASE WHEN(desk.emp_id !=0)  THEN  emp.empl_firstname  WHEN (desk.stu_name !='') THEN desk.stu_name END AS name,m_dept.name AS maintain_dept FROM ".TB_PREF."maintenance_help_desk AS desk LEFT JOIN ".TB_PREF."kv_empl_info AS emp ON  desk.emp_id=emp.id LEFT JOIN ".TB_PREF."maintenance_department AS m_dept ON m_dept.id=desk.maintain_dept_id";
	
  // display_error($sql);
	if (!$all) $sql .= " WHERE !desk.inactive";
	// $sql .= " GROUP BY utl.id";

	return db_query($sql, "could not get all Help Desk Complaints");
} 

function get_helpdesk($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."maintenance_help_desk WHERE help_id=".db_escape($selected_id);
	//display_error($sql);
	$result = db_query($sql, "could not get Help Desk Complaint");
	
	return db_fetch($result);
}

function delete_helpdesk($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."maintenance_help_desk WHERE help_id=".db_escape($selected_id);
		
	db_query($sql, "could not delete Help Desk Complaint");	
}

?>