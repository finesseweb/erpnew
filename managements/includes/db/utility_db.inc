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
function add_utility($types,$category_id,$sub_cat_id,$items_id,$name,$maintenance_type_id, $description,$freq_id)
{
    $frequency = implode($freq_id,',');
	$sql = "INSERT INTO ".TB_PREF."utility (type,category_id,sub_cat_id,items_id,name,maintenance_type_id, description,freq_id) 
		VALUES (".db_escape($types).",".db_escape($category_id).",".db_escape($sub_cat_id).",".db_escape($items_id).",".db_escape($name).",".db_escape($maintenance_type_id).",".db_escape($description).",".db_escape($frequency).")";
		
	db_query($sql, "could not add credit status");		
}


function update_utility($status_id,$types,$category_id,$sub_cat_id,$items_id,$name,$maintenance_type_id, $description,$freq_id)
{
   // display_error($maintenance_type_id);
	$frequency = implode($freq_id,',');
	$sql = "UPDATE ".TB_PREF."utility SET type=".db_escape($types).",category_id=".db_escape($category_id).",sub_cat_id=".db_escape($sub_cat_id).",items_id=".db_escape($items_id).",name=".db_escape($name).",maintenance_type_id=".db_escape($maintenance_type_id).",
		description=".db_escape($description).",freq_id=".db_escape($frequency)." WHERE id=".db_escape($status_id);
	
	db_query($sql, "could not update credit status");			
}

function get_all_utility($all=false)
{
	$sql = "SELECT utl.*,GROUP_CONCAT(freq.frequency_name) AS frequency FROM ".TB_PREF."utility AS utl,".TB_PREF."frequency_master AS freq WHERE FIND_IN_SET(freq.freq_id,utl.freq_id)";
  //display_error($sql);
	if (!$all) $sql .= " AND !utl.inactive";
	 $sql .= " GROUP BY utl.id";

	return db_query($sql, "could not get all credit status");
} 

function get_utility($status_id)
{
	$sql = "SELECT * FROM ".TB_PREF."utility WHERE id=".db_escape($status_id);
	//display_error($sql);
	$result = db_query($sql, "could not get Utility");
	
	return db_fetch($result);
}

function delete_utility($status_id)
{
	$sql="DELETE FROM ".TB_PREF."utility WHERE id=".db_escape($status_id);
		
	db_query($sql, "could not delete Utility");	
}

function get_item_name($items_id)
{
	$sql = "SELECT s.description AS item_name FROM ".TB_PREF."utility AS utl,".TB_PREF."stock_master s  WHERE s.stock_id=".db_escape($items_id);
	//display_error($sql);
	$result = db_query($sql, "could not get utility");
	return db_fetch_row($result);
}
?>