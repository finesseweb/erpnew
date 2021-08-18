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


function get_all_matser1(){
 $sql = "SELECT * FROM ".TB_PREF."statutory_master WHERE !inactive";

  return db_query($sql,"updated");
}

function get_statutory_name1($all=false,$id)
{
	$sql = "SELECT statutory_name FROM ".TB_PREF."statutory_body_master where id = ".db_escape($id);
	if (!$all) $sql .= " AND !inactive";
        $result = db_query($sql, "could not get all Return Name");
        
        $row = db_fetch_row($result);
    //    echo $row[0];
        return $row[0];
}

function get_frequency_name1($all=false,$id)
{
	$sql = "SELECT frequency_name FROM ".TB_PREF."statutory_frequency_master where freq_id = ".db_escape($id);
	if (!$all) $sql .= " AND !inactive";
        $result = db_query($sql, "could not get all Return Name");
        
        $row = db_fetch_row($result);
    //    echo $row[0];
        return $row[0];
}

?>