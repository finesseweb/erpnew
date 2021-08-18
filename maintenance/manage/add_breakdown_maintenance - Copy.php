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
$frequency_id=$_POST["frequency_id"];
$ob_date = $_POST["break_ob_date"];
$ob_1 = $_POST["ob_1"];
$ob_2 = $_POST["ob_2"];
$ob_3 = $_POST["ob_3"];
if(isset($_POST['add_entry'])){
if($_POST["br_check1"] =='on'){
	$check1 =1;
}else{
	$check1=0;
}
if($_POST["br_check2"] =='on'){
	$check2 =1;
}else{
	$check2=0;
}
if($_POST["br_check3"] =='on'){
	$check3 =1;
}else{
	$check3=0;
}
if($_POST["br_check4"] =='on'){
	$check4 =1;
}else{
	$check4=0;
}
if($_POST["br_check5"] =='on'){
	$check5 =1;
}else{
	$check5=0;
}
if($_POST["br_check6"] =='on'){
	$check6 =1;
}else{
	$check6=0;
}

$sql="INSERT INTO ".TB_PREF."breakdown_maintenance(utility_id,maintain_date,frequency_id,br_check1,br_check2, br_check3,br_check4,br_check5,br_check6,break_ob_date,ob_1,ob_2,ob_3) VALUES('$utility_id','$maintain_date','$frequency_id','$check1','$check2','$check3','$check4','$check5','$check6','$ob_date','$ob_1','$ob_2','$ob_3')";

db_query($sql,"something went wrong");

$last_inserted_id = db_insert_id($sql);

$items=$_POST['break'];
 //echo'<pre>';print_r($items);die;

	foreach(array_filter($items['items_id']) as $k => $item_id)
	{
	$sql2="INSERT INTO ".TB_PREF."breakdown_maintain_items(break_id,item_id,quantity) VALUES ('".$last_inserted_id."','".$item_id."','".$items['qty'][$k]."')";
	db_query($sql2,"something went wrong");
	}


header("Location: breakdown_maintenance.php?All=1&success=1");
}
?>