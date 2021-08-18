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
include_once($path_to_root . "/includes/date_functions.inc");
//include($path_to_root . "/includes/ui.inc");
$frequency_id  = $_POST['frequency_id'];
$maintenance_id  = $_POST['maintenance_id'];
$sql="SELECT param.* FROM ".TB_PREF."parameters_master AS param where param.maintenance_id = '$maintenance_id' AND param.frequency_id='$frequency_id'";

$res = db_query($sql);
$result = db_fetch_row($res);

//echo json_encode($result[2]);die;
//echo'<pre>';print_r($result);die;
?>

<table width="40%" align="center" colspan="2">
 <tr>
 <th class="tableheader">S.No.</th>
 <th class="tableheader">Parameters</th>
 <th class="tableheader">Checked Status</th>

 </tr>
 
 <tr class="evenrow">
 <td align="center"><?php echo 1; ?></td>
  <td align="center"><?php echo $result[4]; ?></td>
  <td align="center"><input type="checkbox" name="check1" id="check1"></td>

 </tr>
 <tr class="evenrow">
 <td align="center"><?php echo 2; ?></td>
  <td align="center"><?php echo $result[5]; ?></td>
  <td align="center"><input type="checkbox" name="check2" id="check2"></td>

 </tr>
 <tr class="evenrow">
 <td align="center"><?php echo 3; ?></td>
  <td align="center"><?php echo $result[6]; ?></td>
  <td align="center"><input type="checkbox" name="check3" id="check3"></td>

 </tr>
 <tr class="evenrow">
 <td align="center"><?php echo 4; ?></td>
  <td align="center"><?php echo $result[7]; ?></td>
  <td align="center"><input type="checkbox" name="check4" id="check4"></td>

 </tr>
 <tr class="evenrow">
 <td align="center"><?php echo 5; ?></td>
  <td align="center"><?php echo $result[8]; ?></td>
  <td align="center"><input type="checkbox" name="check5" id="check5"></td>

 </tr>
 <tr class="evenrow">
 <td align="center"><?php echo 6; ?></td>
  <td align="center"><?php echo $result[9]; ?></td>
  <td align="center"><input type="checkbox" name="check6" id="check6"></td>

 </tr>
 </table>
 
 <br>
 <table width="50%" align="center" colspan="2">
 <tr>
 <th class="tableheader" colspan="2">Observations</th>
 </tr>
 <tr class="evenrow">
  <td align="center">Date</td>
  <td align="center"><input type="text" name="ob_date" id="ob_date"></td>
 </tr>
 <tr class="evenrow">
  <td align="center">Observations</td>
  <td align="center"><textarea name="ob_1" id="ob_1"></textarea></td>
 </tr>
 <tr class="evenrow">
  <td align="center">Corrective Action Initiated</td>
  <td align="center"><textarea name="ob_2" id="ob_2"></textarea></td>
 </tr>
 <tr class="evenrow">
  <td align="center">Action Suggested For Improvement</td>
  <td align="center"><textarea name="ob_3" id="ob_3"></textarea></td>
 </tr>
 </table>

<script type="text/javascript">
$('#ob_date').datepicker({
	dateFormat: 'yy-mm-dd',
	prevText: '<i class="fa fa-chevron-left"></i>',
	nextText: '<i class="fa fa-chevron-right"></i>',
	maxDate: 0 
});
</script>