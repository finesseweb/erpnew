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
$cat  = $_POST['cat'];
//display_error($cat);
?>

 <?php $sql="SELECT sub_cat_id,sub_cat_name FROM ".TB_PREF."stock_sub_category WHERE ".TB_PREF."stock_sub_category.category_id = ".db_escape($cat)."";
  // echo $sql;die;
 $res = db_query($sql);

 while($row = db_fetch($res)){ ?>
 <option value="<?php echo $row['sub_cat_id']; ?>"><?php echo $row['sub_cat_name']; ?></option>
 <?php   //echo $row['sub_cat_name'];
 }
 
 ?>