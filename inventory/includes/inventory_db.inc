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
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/inventory.inc");

include_once($path_to_root . "/inventory/includes/db/items_category_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_trans_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_prices_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_purchases_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_codes_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_locations_db.inc");
include_once($path_to_root . "/inventory/includes/db/movement_types_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_adjust_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_transfer_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_units_db.inc");

function item_img_name($stock_id)
{
	$stock_id = strtr($stock_id, "><\\/:|*?", '________');
	return clean_file_name($stock_id);
}

function calculate_reorder_level($location, $line, &$st_ids, &$st_names, &$st_num, &$st_reorder)
{
	$sql = "SELECT ".TB_PREF."loc_stock.*, ".TB_PREF."locations.location_name, ".TB_PREF."locations.email
		FROM ".TB_PREF."loc_stock, ".TB_PREF."locations
		WHERE ".TB_PREF."loc_stock.loc_code=".TB_PREF."locations.loc_code
		AND ".TB_PREF."loc_stock.stock_id = '" . $line->stock_id . "'
		AND ".TB_PREF."loc_stock.loc_code = '" . $location . "'";
	$res = db_query($sql,"a location could not be retreived");
	$loc = db_fetch($res);
	if ($loc['email'] != "")
	{
		$qoh = get_qoh_on_date($line->stock_id, $location);
		$qoh -= get_demand_qty($line->stock_id, $location);
		$qoh -= get_demand_asm_qty($line->stock_id, $location);
		$qoh -= $line->quantity;
		if ($qoh < $loc['reorder_level'])
		{
			$st_ids[] = $line->stock_id;
			$st_names[] = $line->item_description;
			$st_num[] = $qoh - $loc['reorder_level'];
			$st_reorder[] = $loc['reorder_level'];
		}
	}
	return $loc;
}

function send_reorder_email($loc, $st_ids, $st_names, $st_num, $st_reorder)
{
	global $path_to_root;
	
	require_once($path_to_root . "/reporting/includes/class.mail.inc");
	$company = get_company_prefs();
	$mail = new email($company['coy_name'], $company['email']);
	$from = $company['coy_name'] . " <" . $company['email'] . ">";
	$to = $loc['location_name'] . " <" . $loc['email'] . ">";
	$subject = _("Stocks below Re-Order Level at " . $loc['location_name']);
	$msg = "\n";
	for ($i = 0; $i < count($st_ids); $i++)
		$msg .= $st_ids[$i] . " " . $st_names[$i] . ", " . _("Re-Order Level") . ": " . $st_reorder[$i] . ", " . _("Below") . ": " . $st_num[$i] . "\n";
	$msg .= "\n" . _("Please reorder") . "\n\n";
	$msg .= $company['coy_name'];
	$mail->to($to);
	$mail->subject($subject);
	$mail->text($msg);
	return $mail->send();
}
function add_store($location,$store, $store_desc){
    $sql = "INSERT INTO " . TB_PREF . "store_location (location,store,store_desc) VALUES (" . db_escape($location) . "," . db_escape($store)."," . db_escape($store_desc).")";
   
    db_query($sql, "The sales department could not be added");
} 
function add_slab($gst,$gst_desc,$status){
    $sql = "INSERT INTO " . TB_PREF . "tax_slab (tax_rate,tax_description,status) VALUES (" . db_escape($gst) . "," . db_escape($gst_desc).",". db_escape($status).")";
   
    db_query($sql, "The sales department could not be added");
} 
function update_store($selected_id, $location ,$store ,$store_desc){
    $sql = "UPDATE " . TB_PREF . "store_location  SET location=" . db_escape($location). ", store=" . db_escape($store). ", store_desc=" . db_escape($store_desc). " WHERE id = " . db_escape($selected_id);
    db_query($sql, "The sales department could not be updated");
}
function update_slab($selected_id ,$gst ,$gst_desc,$status){
    $sql = "UPDATE " . TB_PREF . "tax_slab  SET tax_rate=" . db_escape($gst). ", tax_description=" . db_escape($gst_desc). ",status=" . db_escape($status). "  WHERE slab_id = " . db_escape($selected_id);
    db_query($sql, "The sales department could not be updated");
}
function get_storeLocation_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "store_location WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}
function get_taxsalb_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "tax_slab WHERE slab_id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}
function getSlabInfo($status){
    $status=1;
    $sql = "SELECT * from " . TB_PREF . "tax_slab where status=". db_escape($status);
    return $sql ;db_query($sql, "could not get designation_master");
}
?>
