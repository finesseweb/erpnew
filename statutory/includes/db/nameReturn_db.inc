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

function add_Return($return_name, $return_desc) {
    $sql = "INSERT INTO " . TB_PREF . "name_return_master (return_name, return_desc) 
		VALUES (" . db_escape(ucfirst($return_name)) . "," . db_escape($return_desc) . ")";

    db_query($sql, "could not add Return Name");
}


     function hasReturnName($return =''){
    
    $sql = "SELECT * FROM ".TB_PREF."name_return_master WHERE return_name = ".db_escape(ucfirst($return))." AND inactive = 0";
//display_error($sql);
$result = db_query($sql,"updated");
return db_num_rows($result);
}

function update_Return($status_id, $return_name, $return_desc) {
    $sql = "UPDATE " . TB_PREF . "name_return_master SET return_name=" . db_escape(ucfirst($return_name)) . ",
		return_desc=" . db_escape($return_desc) . " WHERE id=" . db_escape($status_id);

    db_query($sql, "could not update Return Name");
}

function get_all_Return($all = false) {
    $sql = "SELECT * FROM " . TB_PREF . "name_return_master";
    if (!$all)
        $sql .= " WHERE !inactive";

    return db_query($sql, "could not get all Return Name");
}

function get_all_Return_name($all = false, $id) {
    $sql = "SELECT return_name FROM " . TB_PREF . "name_return_master where id = " . db_escape($id);
    if (!$all)
        $sql .= " AND !inactive";
    $result = db_query($sql, "could not get all Return Name");

    $row = db_fetch_row($result);
    //    echo $row[0];
    return $row[0];
}

function get_Return($status_id) {
    $sql = "SELECT * FROM " . TB_PREF . "name_return_master WHERE id=" . db_escape($status_id);

    $result = db_query($sql, "could not get Return Name");

    return db_fetch($result);
}

function delete_Return($status_id) {
    $sql = "DELETE FROM " . TB_PREF . "name_return_master WHERE id=" . db_escape($status_id);

    db_query($sql, "could not delete Return Name");
}

function return_list_row($label, $name, $selected_id = null, $ret = '', $submit_on_change = false, $disabled = false) {
    echo "<tr><td class='label'>$label</td>";
    return_list_cells(null, $name, $selected_id, $ret, $submit_on_change, $disabled);
    echo "</tr>\n";
}

function return_list_cells($label, $name, $selected_id = null, $ret, $submit_on_change, $disabled = false) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo return_list($name, $selected_id, $ret, $submit_on_change, $disabled);
    echo "</td>\n";
}


function return_list($name, $selected_id = null, $ret , $submit_on_change, $disabled = false) {

    $sql = "select id,return_name FROM " . TB_PREF . "name_return_master where !inactive ";
    	return combo_input($name, $selected_id, $sql, 'id', 'return_name',
 	array('order'=>'return_name',
		'spec_option' => false,
		//'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
 		'async' => false
 	));
}



function filtered_return_list_row($label, $name, $selected_id = null, $ret = '', $submit_on_change = false, $disabled = false) {
    echo "<tr><td class='label'>$label</td>";
    filtered_return_list_cells(null, $name, $selected_id, $ret, $submit_on_change, $disabled);
    echo "</tr>\n";
}

function filtered_return_list_cells($label, $name, $selected_id = null, $ret, $submit_on_change, $disabled = false) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo filtered_return_list($name, $selected_id, $ret, $submit_on_change, $disabled);
    echo "</td>\n";
}



function filtered_return_list($name, $selected_id = null, $ret , $submit_on_change, $disabled = false){
    
      $sql = "select distinct ret.id,ret.return_name FROM " . TB_PREF . "name_return_master as ret join fa_statutory_master as  sat_master on sat_master.return_id = ret.id";
          if($ret)
        $sql .=" where sat_master.statutory_id =".db_escape($ret);

    	return combo_input($name, $selected_id, $sql, 'id', 'return_name',
 	array('order'=>'return_name',
		'spec_option' => false,
 		'select_submit'=> $submit_on_change,
 		'async' => false
 	));
}     
        
        
        
        
function filtered_freq_list_row($label, $name, $selected_id = null, $ret1 = '',$ret2='', $submit_on_change = false, $disabled = false) {
    echo "<tr><td class='label'>$label</td>";
    filtered_freq_list_cells(null, $name, $selected_id, $ret1,$ret2, $submit_on_change, $disabled);
    echo "</tr>\n";
}

function filtered_freq_list_cells($label, $name, $selected_id = null, $ret,$ret2, $submit_on_change, $disabled = false) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo filtered_freq_list($name, $selected_id, $ret, $ret2, $submit_on_change, $disabled);
    echo "</td>\n";
}



function filtered_freq_list($name, $selected_id = null, $ret , $ret2, $submit_on_change, $disabled = false){
    
      $sql = "select distinct freq.freq_id,freq.frequency_name FROM " . TB_PREF . "statutory_frequency_master as freq join fa_statutory_master as  sat_master on sat_master.freq_id = freq.freq_id";
          if($ret)
        $sql .=" where sat_master.statutory_id =".db_escape($ret)." and sat_master.return_id =".db_escape($ret2);
//display_error($sql);
    	return combo_input($name, $selected_id, $sql, 'freq_id', 'frequency_name',
 	array('order'=>'frequency_name',
		'spec_option' => false,
 		'select_submit'=> $submit_on_change,
 		'async' => false
 	));
}
     /*function getDays($date){

      
      $now = time(); // or your date as well
$your_date = strtotime($date);
$datediff =  $your_date - $now ;
return round($datediff / (60 * 60 * 24));
      
      
   /* $d1 = new DateTime($date);
    $d2 = new DateTime(date('Y-m-d'));

    $diff = $d2->diff($d1);
    echo "<pre>";print_r($diff);
    return $diff->d;*/
//}   */


?>