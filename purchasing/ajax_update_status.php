<?php
/**********************************************************************
  
	Released under the terms of the GNU General Public License, GPL, 
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

$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");

$ord_no = $_POST['order_no'];
$type = $_POST['type'];

if($type=="approved"){
	$sql = "UPDATE ".TB_PREF."purch_orders SET approved_status='1' WHERE order_no =".$ord_no."";
	db_query($sql, "The purchase order could not be updated");
}
if($type=="cancelled"){
	$sql = "UPDATE ".TB_PREF."purch_orders SET approved_status='2' WHERE order_no =".$ord_no."";
	db_query($sql, "The purchase order could not be updated");
}
?>