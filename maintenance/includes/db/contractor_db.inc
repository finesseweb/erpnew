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
function add_contractor($supp_name, $supp_ref, $address, $supp_address, $gst_no, $website, $bank_account, $curr_code, $payment_terms, $payable_account, $purchase_account, $payment_discount_account,$phone,$phone2, $notes,$contact,$fax,$email, $tax_group_id, $tax_included)
{
	$sql = "INSERT INTO ".TB_PREF."contractor (supp_name, supp_ref, address, supp_address, gst_no, website, bank_account, curr_code, payment_terms, payable_account, purchase_account, payment_discount_account,phone,phone2, notes,contact,fax,email, tax_group_id, tax_included) 
		VALUES (".db_escape($supp_name).",".db_escape($supp_ref).",".db_escape($address).",".db_escape($supp_address).",".db_escape($gst_no).",".db_escape($website).",".db_escape($bank_account).",".db_escape($curr_code).",".db_escape($payment_terms).",".db_escape($payable_account).",".db_escape($purchase_account).",".db_escape($payment_discount_account).",".db_escape($phone).",".db_escape($phone2).",".db_escape($notes).",".db_escape($contact).",".db_escape($fax).",".db_escape($email).",".db_escape($tax_group_id).",".db_escape($tax_included).")";
		
	db_query($sql, "could not add credit status");		
}


function update_contractor($status_id,$supp_name, $supp_ref, $address, $supp_address, $gst_no, $website, $bank_account, $curr_code, $payment_terms, $payable_account, $purchase_account, $payment_discount_account,$phone,$phone2, $notes,$contact,$fax,$email, $tax_group_id, $tax_included)
{

	$sql = "UPDATE ".TB_PREF."contractor SET supp_name=".db_escape($supp_name).",
		supp_ref=".db_escape($supp_ref).",address=".db_escape($address).",supp_address=".db_escape($supp_address).",gst_no=".db_escape($gst_no).",website=".db_escape($website).",bank_account=".db_escape($bank_account).",curr_code=".db_escape($curr_code).",payment_terms=".db_escape($payment_terms).",payable_account=".db_escape($payable_account).",purchase_account=".db_escape($purchase_account).",payment_discount_account=".db_escape($payment_discount_account).",phone=".db_escape($phone).",phone2=".db_escape($phone2).",notes=".db_escape($notes).",contact=".db_escape($contact).",fax=".db_escape($fax).",email=".db_escape($email).",tax_group_id=".db_escape($tax_group_id).",tax_included=".db_escape($tax_included)." WHERE supplier_id=".db_escape($status_id);
//	display_error($sql);
	db_query($sql, "could not update Contractor");			
}

function get_all_contractor($all=false)
{
	$sql = "SELECT * FROM ".TB_PREF."contractor";
	if (!$all) $sql .= " WHERE !inactive";

	return db_query($sql, "could not get all Contractor");
} 

function get_contractor($status_id)
{
	$sql = "SELECT * FROM ".TB_PREF."contractor WHERE supplier_id=".db_escape($status_id);
	
	$result = db_query($sql, "could not get Contractor");
	
	return db_fetch($result);
	
}

function delete_contractor($status_id)
{
	$sql="DELETE FROM ".TB_PREF."contractor WHERE supplier_id=".db_escape($status_id);
		
	db_query($sql, "could not delete credit status");	
}
?>