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

function add_statutory($statutory_name, $statutory_desc) {
    $sql = "INSERT INTO " . TB_PREF . "statutory_body_master (statutory_name, statutory_desc) 
		VALUES (" . db_escape(ucfirst($statutory_name)) . "," . db_escape(ucfirst($statutory_desc)) . ")";

    db_query($sql, "could not add statutory");
}

function hasStatutorybody($statutory_name = '') {

    $sql = "SELECT * FROM " . TB_PREF . "statutory_body_master WHERE statutory_name = " . db_escape(ucfirst($statutory_name)) . " AND inactive = 0";
//display_error($sql);
    $result = db_query($sql, "updated");
    return db_num_rows($result);
}

function add_statutory_master($statutory_name, $return_name, $freq_name, $due_date, $reminder_days, $Remarks, $freq_days) {
    $due_date = date2sql($due_date);
    $sql = "INSERT INTO " . TB_PREF . "statutory_master (statutory_id,return_id,freq_id,due_date,remider_days,statutory_desc,effective_date)"
            . "VALUES (" . db_escape($statutory_name) . "," . db_escape($return_name) . "," . db_escape($freq_name) . "," . db_escape($due_date) . "," . db_escape($reminder_days) . "," . db_escape($Remarks) ."," . db_escape(date('Y-m-d', strtotime($due_date))) .  ")";
    db_query($sql, "could not add statutory");
}

function add_statutory_main($statutory_name, $return_name, $freq_name, $Remarks, $fyear, $empl_id,$freq_days) {
    $due_date = date2sql($due_date);
    $sql = "INSERT INTO " . TB_PREF . "statutory_main (statutory_id,return_id,freq_id,statutory_desc,f_year,updated_date,empl_id)"
            . "VALUES (" . db_escape($statutory_name) . "," . db_escape($return_name) . "," . db_escape($freq_name) . "," . db_escape($Remarks) . "," . db_escape($fyear) . "," . db_escape(date('Y-m-d')) . "," . db_escape($empl_id) . ")";
    if (db_query($sql, "could not add statutory")) {
        $sql = "select max(id) as id from fa_statutory_main where 1 = 1";
        $result = db_query($sql, "could not add statutory");
        $row = db_fetch_row($result);
        //    echo $row[0];
        
        update_status($statutory_name, $return_name, $freq_name, $due_date, $reminder_days, $Remarks,$freq_days);
        return $row[0];
    }
    return false;
}

function hasStatutory($statutory_name = '', $return_name = '', $freq_name = '', $f_year = '') {

    $sql = "SELECT * FROM " . TB_PREF . "statutory_main WHERE statutory_id = $statutory_name AND return_id = $return_name AND freq_id = $freq_name AND f_year = $f_year";

    $result = db_query($sql, "updated");
    return db_num_rows($result);
}

function isAssigned($statutory_name = '', $return_name = '', $freq_name = '') {

    $sql = "SELECT * FROM " . TB_PREF . "statutory_master WHERE statutory_id = $statutory_name AND return_id = $return_name AND freq_id = $freq_name AND status = 0";

    $result = db_query($sql, "updated");
    return db_num_rows($result);
}

function hasStatutoryMaster($statutory_name = '', $return_name = '', $freq_name = '') {

    $sql = "SELECT * FROM " . TB_PREF . "statutory_master WHERE statutory_id = $statutory_name AND return_id = $return_name AND freq_id = $freq_name AND status = 0";
//display_error($sql);
    $result = db_query($sql, "updated");
    return db_num_rows($result);
}

function get_all_matser() {
    $sql = "SELECT * FROM " . TB_PREF . "statutory_master WHERE  !inactive";

    return db_query($sql, "updated");
}

/*function getFrequencyDays($id){
    $sql = "SELECT frequency_days FROM " . TB_PREF . "statutory_frequency_master WHERE freq_id = $id AND !inactive";
    $result = db_query($sql, "couldnot get the frequency days");
    $row = db_fetch_row($result);
    return $row[0];
}*/

function update_status($statutory_name, $return_name, $freq_name, $Remarks, $fyear, $empl_id,$freq_days = 0) {
             
             $duedate = getEffectiveDate($statutory_name, $return_name, $freq_name, $Remarks, $fyear);
          $NewDate = date('Y-m-d', strtotime($duedate . " +$freq_days days"));
    $sql = "UPDATE " . TB_PREF . "statutory_master SET effective_date=" . db_escape($NewDate) . " WHERE statutory_id=" . db_escape($statutory_name) . "AND return_id = " . db_escape($return_name) . "AND freq_id =" . db_escape($freq_name);
    //display_error($sql);
    db_query($sql, "The CV could not be updated");
}



function getEffectiveDate($statutory_name, $return_name, $freq_name, $Remarks, $fyear){
    
    $sql = "select effective_date from " . TB_PREF . "statutory_master  WHERE statutory_id=" . db_escape($statutory_name) . "AND return_id = " . db_escape($return_name) . "AND freq_id =" . db_escape($freq_name);
    $result = db_query($sql, "The CV could not be updated");
    $row = db_fetch_row($result);
    return $row[0];
}

/* function update_statutory_main($statutory_name, $return_name, $freq_name,$Remarks,$fyear){
  $due_date = date2sql($due_date);
  $sql = "INSERT INTO ".TB_PREF."statutory_main (statutory_id,return_id,freq_id,statutory_desc,f_year,updated_date)"
  . "VALUES (".db_escape($statutory_name).",".db_escape($return_name).",".db_escape($freq_name).",".db_escape($Remarks).",".db_escape($fyear).",".db_escape(date('Y-m-d')).")";
  if(db_query($sql, "could not add statutory")){
  $sql = "select max(id) as id from fa_statutory_main where 1 = 1";
  $result = db_query($sql, "could not add statutory");
  $row = db_fetch_row($result);
  //    echo $row[0];
  return $row[0];
  }
  return false ;
  } */

function add_upload($statutory_main, $title, $file_name, $unique_name) {
    $date = date('Y-m-d');
    $sql = "INSERT INTO " . TB_PREF . "statutory_uploads (statutory_main_id, title, file_name, unique_name,updated_date) VALUES ("
            . db_escape($statutory_main) . ", "
            . db_escape($title) . ", "
            . db_escape($file_name) . ","
            . db_escape($unique_name) . ","
            . db_escape($date) . ")";
    //display_error($sql);
    db_query($sql, "The CV could not be updated");
}

function update_title($status_id, $title) {
    $sql = "UPDATE " . TB_PREF . "statutory_uploads SET title=" . db_escape($title) . " WHERE id=" . db_escape($status_id);
    db_query($sql, "The CV could not be updated");
}

function update_upload($status_id, $title, $file_name, $unique_name) {
    $date = date('Y-m-d');


    $sql = "UPDATE " . TB_PREF . "statutory_uploads SET title=" . db_escape($title) . ",
		file_name=" . db_escape($file_name) . ",
		unique_name=" . db_escape($unique_name) . " WHERE id=" . db_escape($status_id);


    /* $sql = "INSERT INTO ".TB_PREF."statutory_uploads (statutory_main_id, title, file_name, unique_name,updated_date) VALUES ("
      .db_escape($statutory_main).", "
      .db_escape($title).", "
      .db_escape($file_name).","
      .db_escape($unique_name).","
      .db_escape($date).")"; */

    db_query($sql, "The CV could not be updated");
}

function get_all_statutory_main($all = false, $empl_id) {
    $sql = "SELECT * FROM " . TB_PREF . "statutory_main";
    if (!$all)
        $sql .= " WHERE !inactive";

    return db_query($sql, "could not get all Statutory");
}

function get_all_statutory_main_report($all = false, $empl_id, $f_year,$due_date) {
    $due_date = $due_date = date2sql($due_date);
    $sql = "SELECT * FROM " . TB_PREF . "statutory_main";
    if (!$all)
        $sql .= " WHERE !inactive  AND f_year = " . db_escape($f_year);

    return db_query($sql, "could not get all Statutory");
}

function get_all_statutory_main_by_id($id) {//display_error($id);
    $sql = "SELECT * FROM " . TB_PREF . "statutory_main where id= " . db_escape($id);
    return db_query($sql, "could not get all Statutory");
}

function get_all_product_master($id) {//display_error($id);
    $sql = "SELECT * FROM " . TB_PREF . "product_master where id = " . db_escape($id);

    return db_query($sql, "could not get all Statutory");
}

function get_all_statutory_upload_by_id2($id) {//display_error($id);
    $sql = "SELECT * FROM " . TB_PREF . "statutory_uploads where id = " . db_escape($id);

    return db_query($sql, "could not get all Statutory");
}

function get_all_statutory_upload_by_id_num($id) {//display_error($id);
    $sql = "SELECT * FROM " . TB_PREF . "statutory_uploads where statutory_main_id = " . db_escape($id);

    $res = db_query($sql, "could not get all Statutory");
    $coun = db_num_rows($res);
    return $coun;
}

function update_statutory_master($status_id, $statutory_name, $return_name, $freq_name, $due_date, $reminder_days, $Remarks) {
    $due_date = date2sql($due_date);
    
    $sql = "UPDATE " . TB_PREF . "statutory_master SET statutory_id =" . db_escape($statutory_name) . ",return_id =" . db_escape($return_name) . ",freq_id =" . db_escape($freq_name) . ",
            due_date =" . db_escape($due_date) . ",remider_days=" . db_escape($reminder_days) . ",statutory_desc=" . db_escape($Remarks). ",effective_date=" . db_escape($due_date)  . " WHERE id=" . db_escape($status_id);

    db_query($sql, "could not update Statutory");
}
function update_statutory_master_date($status_id,$date) {
   // display_error($status_id.'===='.$date);
    $sql = "UPDATE " . TB_PREF . "statutory_master SET effective_date=" . db_escape($date) . " WHERE id=" . db_escape($status_id);

    db_query($sql, "could not update Statutory");
}

function update_statutory_main($status_id, $statutory_name, $return_name, $freq_name, $Remarks, $fyear,$freq_days) {
    $due_date = date2sql($due_date);
    $sql = "UPDATE " . TB_PREF . "statutory_main SET statutory_id =" . db_escape($statutory_name) . ",return_id =" . db_escape($return_name) . ",freq_id =" . db_escape($freq_name) . ",remider_days =" . db_escape('') . ",statutory_desc=" . db_escape($Remarks) . ",f_year=" . db_escape($fyear) . ",updated_date=" . db_escape(date('Y-m-d')) . " WHERE id=" . db_escape($status_id);
    //display_error($sql);
    db_query($sql, "could not update Statutory");
    update_status($statutory_name, $return_name, $freq_name, $due_date, $reminder_days, $Remarks,$freq_days);
}

function update_statutory($status_id, $statutory_name, $statutory_desc) {
    $sql = "UPDATE " . TB_PREF . "statutory_body_master SET statutory_name=" . db_escape($statutory_name) . ",
		statutory_desc=" . db_escape($statutory_desc) . " WHERE id=" . db_escape($status_id);

    db_query($sql, "could not update Statutory");
}

function get_all_statutory_master($all = false) {
    $sql = "SELECT * FROM " . TB_PREF . "statutory_master";
    if (!$all)
        $sql .= " WHERE !inactive";

    return db_query($sql, "could not get all Statutory");
}

function get_statutory_name($all = false, $id) {
    $sql = "SELECT statutory_name FROM " . TB_PREF . "statutory_body_master where id = " . db_escape($id);
    if (!$all)
        $sql .= " AND !inactive";
    $result = db_query($sql, "could not get all Return Name");

    $row = db_fetch_row($result);
    //    echo $row[0];
    return $row[0];
}

function get_f_name($id) {
   
    //    echo $row[0];
    $row = formatF($id);
    return date('m/d/Y',strtotime($row[0])).' - '.date('m/d/Y',strtotime($row[1]));
}


function formatF($id){
    $sql = "SELECT begin , end FROM " . TB_PREF . "fiscal_year where id = " . db_escape($id);
    //if (!$all) $sql .= " AND !inactive";
    $result = db_query($sql, "could not get all Return Name");

    $row = db_fetch_row($result);
    //    echo $row[0];
    return $row;
}

function get_frequency_name($all = false, $id) {
    $sql = "SELECT frequency_name FROM " . TB_PREF . "statutory_frequency_master where freq_id = " . db_escape($id);
    if (!$all)
        $sql .= " AND !inactive";
    $result = db_query($sql, "could not get all Return Name");

    $row = db_fetch_row($result);
    //    echo $row[0];
    return $row[0];
}

function get_all_statutory($all = false) {
    $sql = "SELECT * FROM " . TB_PREF . "statutory_body_master";
    if (!$all)
        $sql .= " WHERE !inactive";

    return db_query($sql, "could not get all Statutory");
}

function get_statutory_master($status_id) {
    $sql = "SELECT * FROM " . TB_PREF . "statutory_master WHERE id=" . db_escape($status_id);

    $result = db_query($sql, "could not get Statutory");

    return db_fetch($result);
}

function get_statutory($status_id) {
    $sql = "SELECT * FROM " . TB_PREF . "statutory_body_master WHERE id=" . db_escape($status_id);

    $result = db_query($sql, "could not get Statutory");

    return db_fetch($result);
}

function delete_statutory($status_id) {
    $sql = "DELETE FROM " . TB_PREF . "statutory_body_master WHERE id=" . db_escape($status_id);

    db_query($sql, "could not delete Statutory");
}

function statutory_list_row($label, $name, $selected_id = null, $submit_on_change = false, $disabled = false) {
    echo "<tr><td class='label'>$label</td>";
    statutory_list_cells(null, $name, $selected_id, $submit_on_change, $disabled);
    echo "</tr>\n";
}

function statutory_list_cells($label, $name, $selected_id = null, $submit_on_change, $disabled = false) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo statutory_list($name, $selected_id, $submit_on_change, $disabled);
    echo "</td>\n";
}

function statutory_list($name, $selected_id = null, $submit_on_change, $disabled = false) {

    ///$sql = "SELECT COALESCE(id,99) AS id, countries_name FROM ".TB_PREF."kv_countries";
    //display_error($sql);
    $sql = "select id,statutory_name FROM " . TB_PREF . "statutory_body_master where !inactive";

    return combo_input($name, $selected_id, $sql, 'id', 'statutory_name', array(
        'order' => 'statutory_name',
        'spec_option' => false,
        //'spec_id' => -1,
        'select_submit' => $submit_on_change,
        'async' => true
    ));
}

function includeJs() {


    $jsbody .= "<script>";
    $jsbody .= " let inc = 1;";
    $jsbody .= "$('#PLUS').click(function(){";
    $jsbody .= " var tr = '<tr><td class=\"label\">Upload Document:</td><td><input type=\"file\" name=\"kv_attach_name'+inc+'\" accept=\".doc,.docx, .rtf, .txt,.png,.jpg,.pdf\"></td>';";
    $jsbody .= "tr += '<td><button  type=\"button\" name=\"REMOVE\" id=\"REMOVE'+inc+'\" value=\"Remove\" title=\"Remove\"><img src=\"../../themes/default/images/escape.png\" height=\"12\" alt=\"\"></button></td>';";
    $jsbody .= "tr += '<td>Title</td><td><input type=\"text\" name=\"title'+inc+'\" size=\"32\" maxlength=\"\" value=\"\"></td></tr>';";
    $jsbody .= "$('.tablestyle2>tbody>tr:nth-child(4)').after(tr);";
    $jsbody .= "inc += 1;";
    $jsbody .= "});";
    
    $jsbody .="$(\"#REMOVE1\").click(function(){alert('hello')});";



    $jsbody .= "</script>";
    return $jsbody;
}

?>