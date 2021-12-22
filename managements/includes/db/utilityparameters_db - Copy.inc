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
function add_parameters($utilitys_id,$type_maintenance_id,$frequency_name, $param1,$param2,$param3,$param4,$param5,$param6,$br_date,$br_time,$br_observations,$br_comments,$param_title,$param_desc)
{

	$sql = "INSERT INTO ".TB_PREF."utility_parameters_master (utilitys_id,type_maintenance_id,frequency_id, ut_param1,ut_param2,ut_param3,ut_param4,ut_param5,ut_param6,br_date,br_time,br_observations,br_comments) 
		VALUES (".db_escape($utilitys_id).",".db_escape($type_maintenance_id).",".db_escape($frequency_name).",".db_escape($param1).",".db_escape($param2).",".db_escape($param3).",".db_escape($param4).",".db_escape($param5).",".db_escape($param6).",".db_escape($br_date).",".db_escape($br_time).",".db_escape($br_observations).",".db_escape($br_comments).")";
//display_error($sql);
	db_query($sql, "add Utility Parameter");
		
   $last_inserted_id = db_insert_id($sql);
   
 $sql2 = "INSERT INTO ".TB_PREF."utility_parameter_items (ut_param_id,param_title,param_desc) VALUES (".db_escape($last_inserted_id).",".db_escape($param_title).",".db_escape($param_desc).")"; 

 db_query($sql2, "add Utility Parameter Items"); 
 
}

function add_paramitems($utilitys_id,$type_maintenance_id,$frequency_name,$param_title,$param_desc)
{

	$sql = "SELECT param.ut_param_id FROM ".TB_PREF."utility_parameters_master AS param WHERE param.utilitys_id=".db_escape($utilitys_id)." AND param.type_maintenance_id=".db_escape($type_maintenance_id)." AND param.frequency_id=".db_escape($frequency_name)."";

	$result=db_query($sql, "add Utility Parameter");
    $res = db_fetch_row($result);
   $last_inserted_id = $res[0];
   
 $sql2 = "INSERT INTO ".TB_PREF."utility_parameter_items (ut_param_id,param_title,param_desc) VALUES (".db_escape($last_inserted_id).",".db_escape($param_title).",".db_escape($param_desc).")"; 
//display_error($sql2);
 db_query($sql2, "add Utility Parameter Items"); 
 
}

function update_parameters($status_id,$utilitys_id,$type_maintenance_id,$frequency_name, $param1,$param2,$param3,$param4,$param5,$param6,$br_date,$br_time,$br_observations,$br_comments,$param_title,$param_desc)
{
	$sql = "UPDATE ".TB_PREF."utility_parameters_master SET
utilitys_id=".db_escape($utilitys_id).",type_maintenance_id=".db_escape($type_maintenance_id).",frequency_id=".db_escape($frequency_name).",ut_param1=".db_escape($param1).",ut_param2=".db_escape($param2).",ut_param3=".db_escape($param3).",ut_param4=".db_escape($param4).",ut_param5=".db_escape($param5).",ut_param6=".db_escape($param6).",br_date=".db_escape($br_date).",br_time=".db_escape($br_time).",br_observations=".db_escape($br_observations).",br_comments=".db_escape($br_comments)." WHERE ut_param_id=".db_escape($status_id);
	
	db_query($sql, "could not update Parameters");		

	$sql2 = "UPDATE ".TB_PREF."utility_parameter_items SET
param_title=".db_escape($param_title).",param_desc=".db_escape($param_desc)." WHERE ut_param_id=".db_escape($status_id);
    
	db_query($sql2, "could not update Parameters Items");
}

function get_all_parameters($all=false)
{
	$sql = "SELECT param.*,utl.name as u_name,freq.frequency_name as f_name FROM ".TB_PREF."utility_parameters_master AS param LEFT JOIN ".TB_PREF."utility AS utl ON param.utilitys_id=utl.id LEFT JOIN fa_frequency_master AS freq ON param.frequency_id=freq.freq_id ";
	if (!$all) $sql .= " AND !param.inactive";
	$sql .= " GROUP BY param.ut_param_id";

	return db_query($sql, "could not get all Parameters");
} 

function get_parameters($status_id)
{
	$sql = "SELECT parameter.*,Items.items_id,Items.param_title,Items.param_desc FROM ".TB_PREF."utility_parameters_master AS parameter,".TB_PREF."utility_parameter_items AS Items WHERE Items.items_id=".db_escape($status_id);

	$result = db_query($sql, "could not get Parameters");
	return db_fetch($result);
}


 function get_parameter_values($utilitys_id,$type_maintenance_id,$frequency_id)
 {
	//display_error($frequency_id);
	$sql = "SELECT param.ut_param_id,utl_items.items_id,utl_items.param_title as p_name,utl_items.param_desc as p_desc FROM ".TB_PREF."utility_parameters_master AS param,".TB_PREF."utility_parameter_items AS utl_items WHERE param.ut_param_id=utl_items.ut_param_id AND param.utilitys_id=".db_escape($utilitys_id)." AND param.type_maintenance_id=".db_escape($type_maintenance_id)." AND param.frequency_id=".db_escape($frequency_id)."";

	return db_query($sql, "could not get all Parameters");
	
}  

function get_values($utilitys_id,$type_maintenance_id,$frequency_name)
 {
	$sql1 = "SELECT params.utilitys_id,params.type_maintenance_id,params.frequency_id FROM ".TB_PREF."utility_parameters_master AS params WHERE params.utilitys_id=".db_escape($utilitys_id)." AND params.type_maintenance_id=".db_escape($type_maintenance_id)." AND params.frequency_id=".db_escape($frequency_name)."";

	return db_query($sql1, "could not get Parameters");
	
	
}  

function delete_parameters($status_id)
{
	$sql="DELETE FROM ".TB_PREF."utility_parameters_master WHERE ut_param_id=".db_escape($status_id);
		
	db_query($sql, "could not delete Selected Parameters");	
}
?>