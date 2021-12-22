<?php

/* * ********************************************************************

  Released underhe GNU General Public License, GPL,
  as published by the Free Software Foundation, either version 3
  of the License, or (at your option) any later version.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 * ********************************************************************* */



function get_all_Return_name1($all = false, $id) {
    $sql = "SELECT return_name FROM " . TB_PREF . "name_return_master where id = " . db_escape($id);
    if (!$all)
        $sql .= " AND !inactive";
    $result = db_query($sql, "could not get all Return Name");

    $row = db_fetch_row($result);
    //    echo $row[0];
    return $row[0];
}


  function getDays($date){

      date_default_timezone_set('Asia/Kolkata');
      $now = strtotime(date('Y-m-d')); // or your date as well
      
$your_date = strtotime($date);
$datediff =   $your_date - $now ;
//display_error(round($datediff / (60 * 60 * 24)));
return (round($datediff / (60 * 60 * 24)));

}  
  function getDaysDiff($date1,$date2){

      date_default_timezone_set('Asia/Kolkata');
      $now = strtotime($date1); // or your date as well
      
$your_date = strtotime($date2);
$datediff =  $your_date - $now ;
//display_error(round($datediff / (60 * 60 * 24)));
return abs(round($datediff / (60 * 60 * 24)));

}  
    
    
function getFrequencyDays($id){
    $sql = "SELECT frequency_days FROM " . TB_PREF . "statutory_frequency_master WHERE freq_id = $id AND !inactive";
    $result = db_query($sql, "couldnot get the frequency days");
    $row = db_fetch_row($result);
    return $row[0];
}


function update_statutory_master_date1($status_id,$date) {
   // display_error($status_id.'===='.$date);
    $sql = "UPDATE " . TB_PREF . "statutory_master SET effective_date=" . db_escape($date) . " WHERE id=" . db_escape($status_id);

    db_query($sql, "could not update Statutory");
}


?>