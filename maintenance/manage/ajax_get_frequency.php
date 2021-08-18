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
$maintenance_id  = $_POST['maintenance_id'];

//echo json_encode($result[2]);die;
//echo'<pre>';print_r($result);die;
?>

<table width="50%" align="center" colspan="2">
 <tr>
 <td>Frequency Name
  <select type="text" name="frequency_id" id="frequency_id">
 <?php $sql="SELECT freq.frequency_name,freq.freq_id
FROM ".TB_PREF."frequency_master AS freq
WHERE freq.freq_id IN
(
   SELECT param.frequency_id FROM ".TB_PREF."parameters_master AS param,".TB_PREF."frequency_master AS freq where param.maintenance_id = '$maintenance_id' GROUP BY param.param_id 
);";
 $res = db_query($sql);
     echo "<option>select</option>";
	while($row = db_fetch_row($res))
	{ 
	?>  
	<option value="<?php echo $row[1]; ?>"><?php echo $row[0]; ?></option>
	<?php  
	} ?>
  </select></td>

 </tr>
 
 
 
 </table>
