<?php

function add_vendor($cat_id, $fascial_year,$vendor_type,$cumulative_payment,$single_payment,$percentage,$status){
    $sql = "INSERT INTO " . TB_PREF . "vendor_details (cat_id,fascial_year,vendor_type,cumulative_payment,single_payment,percentage,status) VALUES (" . db_escape($cat_id) . "," . db_escape($fascial_year)."," . db_escape($vendor_type)."," . db_escape($cumulative_payment)."," . db_escape($single_payment)."," . db_escape($percentage)."," . db_escape($status).")";
    
    db_query($sql, "The sales department could not be added");
}
function update_vendor($selected_id,$cat_id, $fascial_year,$vendor_type,$cumulative_payment,$single_payment,$percentage,$status){
    $sql = "UPDATE " . TB_PREF . "vendor_details  SET cat_id=" . db_escape($cat_id). ", fascial_year=" . db_escape($fascial_year). ", vendor_type=" . db_escape($vendor_type). ", cumulative_payment=" . db_escape($cumulative_payment).", single_payment=" . db_escape($single_payment). ", percentage=" . db_escape($percentage). ", status=" . db_escape($status). " WHERE id = " . db_escape($selected_id);
    db_query($sql, "The sales department could not be updated");
}
function getVendorInfo($status){
    $status=1;
    $sql = "SELECT * from " . TB_PREF . "vendor_details where status=". db_escape($status);
    return $sql ;db_query($sql, "could not get designation_master");
}

function desiggroup_list_rowNew($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	desig_group_list_cellNew(null, $name, $selected_id,$spec_opt, $submit_on_change);
	echo "</tr>\n";
}
function desig_group_list_cellNew($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo desig_group_listsNew($name, $selected_id,$spec_opt, $submit_on_change);
	echo "</td>\n";
}
function desig_group_listsNew($name, $selected_id=null,$spec_opt=false, $submit_on_change=false)
{
	$sql ="SELECT fl_num,fl_num FROM ".TB_PREF."book_location";
	return combo_input($name, $selected_id, $sql, 'fl_num', 'fl_num',
 	array('order'=>'floor_id',
		'spec_option' => $spec_opt,
		//'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
 		'async' => false
 	));
}

function get_vendor_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "vendor_details WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}

?>
