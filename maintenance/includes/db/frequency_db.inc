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
function add_frequency($frequency_name, $frequency_desc)
{
	$sql = "INSERT INTO ".TB_PREF."frequency_master (frequency_name, frequency_desc) 
		VALUES (".db_escape($frequency_name).",".db_escape($frequency_desc).")";
		
	db_query($sql, "could not add Frequency");		
}


function update_frequency($status_id, $frequency_name, $frequency_desc)
{
	$sql = "UPDATE ".TB_PREF."frequency_master SET frequency_name=".db_escape($frequency_name).",
		frequency_desc=".db_escape($frequency_desc)." WHERE freq_id=".db_escape($status_id);
	
	db_query($sql, "could not update Frequency");			
}

function get_all_frequency($all=false)
{
	$sql = "SELECT * FROM ".TB_PREF."frequency_master";
	if (!$all) $sql .= " WHERE !inactive";

	return db_query($sql, "could not get all Frequencies");
} 

function get_frequency($status_id)
{
	$sql = "SELECT * FROM ".TB_PREF."frequency_master WHERE freq_id=".db_escape($status_id);
	
	$result = db_query($sql, "could not get Frequency");
	
	return db_fetch($result);
}

function delete_frequency($status_id)
{
	$sql="DELETE FROM ".TB_PREF."frequency_master WHERE freq_id=".db_escape($status_id);
		
	db_query($sql, "could not delete Frequency");	
}
?>