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

include($path_to_root . "/includes/session.inc");
//include($path_to_root . "/includes/ui.inc");
$item  = $_POST['item'];

?>


 <?php $sql="SELECT SUM(moves.qty) AS qty,moves.stock_id AS stock
   FROM ".TB_PREF."stock_master AS stock,".TB_PREF."stock_moves AS moves WHERE moves.stock_id=".db_escape($item)." GROUP BY stock.stock_id";
  // echo $sql;die;
 $res = db_query($sql);
 $row = db_fetch_row($res);

 echo json_encode($row);die;
 
?>
 
