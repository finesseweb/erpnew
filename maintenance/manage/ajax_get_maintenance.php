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
$utility_id  = $_POST['utility_id'];

//echo json_encode($result[2]);die;
//echo'<pre>';print_r($result);die;
?>

<table width="50%" align="center" colspan="2">
 <tr>
 <td>Maintenance Name
  <select type="text" name="frequency_id" id="frequency_id">
 <?php $sql="SELECT maint.id,maint.name
FROM ".TB_PREF."maintenance AS maint
WHERE maint.id IN
(
   SELECT param.maintenance_id FROM fa_parameters_master AS param where param.utility = '$utility_id' GROUP BY param.param_id 
);";
 $res = db_query($sql);
     echo "<option>select</option>";
	while($row = db_fetch_row($res))
	{ 
	?>  
	<option value="<?php echo $row[0]; ?>"><?php echo $row[1]; ?></option>
	<?php  
	} ?>
  </select></td>

 </tr>
 
 
 
 </table>
