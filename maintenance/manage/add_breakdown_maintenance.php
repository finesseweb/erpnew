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
//-----------------------------------------------------------------------------
//
//	Entry/Modify Sales Quotations
//	Entry/Modify Sales Order
//	Entry Direct Delivery
//	Entry Direct Invoice
//

$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");
//echo '<pre>'; print_r($_POST); die;
$utility_id=$_POST["utility_id"];
$maintain_date=$_POST["maintain_date"];
$contractor_id=$_POST["contractor_id"];
$break_st_time = $_POST["break_st_time"];
$break_end_time = $_POST["break_end_time"];
$ob_reason = $_POST["ob_reason"];
$ob_1 = $_POST["ob_1"];
$ob_2 = $_POST["ob_2"];
if(isset($_POST['add_entry'])){

$sql="INSERT INTO ".TB_PREF."breakdown_maintenance(utility_id,maintain_date,contractor_id,break_st_time,break_end_time,ob_reason,ob_1,ob_2) VALUES('$utility_id','$maintain_date','$contractor_id','$break_st_time','$break_end_time','$ob_reason','$ob_1','$ob_2')";

db_query($sql,"something went wrong");

$last_inserted_id = db_insert_id($sql);

$items=$_POST['break'];
 //echo'<pre>';print_r($items);die;

	foreach(array_filter($items['items_id']) as $k => $item_id)
	{
	$sql2="INSERT INTO ".TB_PREF."breakdown_maintain_items(break_id,cat_id,sub_cat_id,item_id,quantity,stock_qty) VALUES ('".$last_inserted_id."','".$items['cat_id'][$k]."','".$items['sub_cat_id'][$k]."','".$item_id."','".$items['qty'][$k]."','".$items['stock_qty'][$k]."')";
	db_query($sql2,"something went wrong");
	
	$query = "INSERT INTO ".TB_PREF."stock_moves(stock_id,qty)VALUES(".db_escape($item_id).",".db_escape(-$items['qty'][$k]).")";
    db_query($query, "The stock not deducted");
	
	}
	
   $new_items=$_POST['New'];
   // echo'<pre>';print_r($new_items);die;

	foreach(array_filter($new_items['n_item']) as $k => $n_item)
	{
	
	$sql1="INSERT INTO ".TB_PREF."breakdown_new_items(break_id,n_item,n_qty,n_bill_date,n_billno,n_contractor,n_comments) VALUES ('".$last_inserted_id."','".$n_item."','".$new_items['n_qty'][$k]."','".$new_items['n_bill_date'][$k]."','".$new_items['n_billno'][$k]."','".$new_items['n_contractor'][$k]."','".$new_items['n_comments'][$k]."')";
    //echo '<pre>'; print_r($sql1); die; 
	db_query($sql1,"something went wrong");
	
	}	


header("Location: breakdown_maintenance.php?All=1&success=1");
}
?>