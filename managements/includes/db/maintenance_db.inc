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
function add_maintenance($name, $description)
{
	$sql = "INSERT INTO ".TB_PREF."maintenance (name, description) 
		VALUES (".db_escape($name).",".db_escape($description).")";
		
	db_query($sql, "could not add credit status");		
}


function update_maintenance($status_id,$name, $description)
{
	$sql = "UPDATE ".TB_PREF."maintenance SET name=".db_escape($name).",
		description=".db_escape($description)." WHERE id=".db_escape($status_id);
	
	db_query($sql, "could not update credit status");			
}

function get_all_maintenance($all=false)
{
	$sql = "SELECT * FROM ".TB_PREF."maintenance";
	if (!$all) $sql .= " WHERE !inactive";

	return db_query($sql, "could not get all credit status");
} 

function get_maintenance($status_id)
{
	$sql = "SELECT * FROM ".TB_PREF."maintenance WHERE id=".db_escape($status_id);
	
	$result = db_query($sql, "could not get credit status");
	
	return db_fetch($result);
}

function delete_maintenance($status_id)
{
	$sql="DELETE FROM ".TB_PREF."maintenance WHERE id=".db_escape($status_id);
		
	db_query($sql, "could not delete credit status");	
}
?>