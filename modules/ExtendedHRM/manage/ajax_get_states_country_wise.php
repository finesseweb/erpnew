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

$path_to_root = "../../..";


include_once($path_to_root . "/includes/session.inc");

$country = $_POST['country'];
$sql = "SELECT state_id, state_name FROM ".TB_PREF."kv_states WHERE country_id='$country'";
$res = db_query($sql,"Get all states");
$result = db_fetch($res);
echo json_encode($result);
	//echo '<tr> <td class="label">State / Union Territories</td><td>';
	//echo '<select type="text" name="empl_per_state" >';
	//while($result = db_fetch($res)){
	//echo '<optional value='.$result['state_id'].'>'.$result['state_name'].'</option>';
	//}
	//echo '</select></td></tr>';





?>