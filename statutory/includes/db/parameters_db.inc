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
function add_parameters($maintenance_name,$frequency_name, $param1,$param2,$param3,$param4,$param5,$param6)
{
	$sql = "INSERT INTO ".TB_PREF."parameters_master (maintenance_id,frequency_id, param1,param2,param3,param4,param5,param6) 
		VALUES (".db_escape($maintenance_name).",".db_escape($frequency_name).",".db_escape($param1).",".db_escape($param2).",".db_escape($param3).",".db_escape($param4).",".db_escape($param5).",".db_escape($param6).")";
		
	db_query($sql, "could not add Frequency");		
}


function update_parameters($status_id,$maintenance_name,$frequency_name, $param1,$param2,$param3,$param4,$param5,$param6)
{
	$sql = "UPDATE ".TB_PREF."parameters_master SET
maintenance_id=".db_escape($maintenance_name).",frequency_id=".db_escape($frequency_name).",param1=".db_escape($param1).",param2=".db_escape($param2).",param3=".db_escape($param3).",param4=".db_escape($param4).",param5=".db_escape($param5).",param6=".db_escape($param6)." WHERE param_id=".db_escape($status_id);
	
	db_query($sql, "could not update Parameters");			
}

function get_all_parameters($all=false)
{
	$sql = "SELECT param.*,maintain.name as m_name,freq.frequency_name as f_name FROM ".TB_PREF."parameters_master AS param,".TB_PREF."maintenance AS maintain,".TB_PREF."frequency_master AS freq WHERE param.maintenance_id=maintain.id AND param.frequency_id=freq.freq_id  GROUP BY param.param_id";
	if (!$all) $sql .= " AND !param.inactive";

	return db_query($sql, "could not get all Parameters");
} 

function get_parameters($status_id)
{
	$sql = "SELECT * FROM ".TB_PREF."parameters_master WHERE param_id=".db_escape($status_id);
	
	$result = db_query($sql, "could not get Parameters");
	
	return db_fetch($result);
}

function delete_parameters($status_id)
{
	$sql="DELETE FROM ".TB_PREF."parameters_master WHERE param_id=".db_escape($status_id);
		
	db_query($sql, "could not delete Selected Parameters");	
}
?>