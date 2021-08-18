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
$utility_id  = $_POST['utility_id'];
//$frequency_id  = $_POST['frequency_id'];

$sql2="SELECT utly.* FROM ".TB_PREF."utility_parameters_master AS utly where utly.utilitys_id = '$utility_id' AND utly.frequency_id='$frequency_id' AND utly.type_maintenance_id=2";

$res2 = db_query($sql2);
$result2 = db_fetch_row($res2);

//echo'<pre>';print_r($result);die;
?>
<center>
<table width="80%">
<tbody>
<tr>
<td>
 <table>
 
 <tr>
 <th class="tableheader">S.No.</th>
 <th class="tableheader">Parameters</th>
 <th class="tableheader">Checked Status</th>
 </tr>
 
 <tr class="evenrow">
  <td align="center"><?php echo 1; ?></td>
  <td align="center"><?php echo $result2[4]; ?></td>
  <td align="center"><input type="checkbox" name="br_check1" id="br_check1"></td>
 </tr>
 
 <tr class="evenrow">
  <td align="center"><?php echo 2; ?></td>
  <td align="center"><?php echo $result2[5]; ?></td>
  <td align="center"><input type="checkbox" name="br_check2" id="br_check2"></td>
 </tr>
 
 <tr class="evenrow">
  <td align="center"><?php echo 3; ?></td>
  <td align="center"><?php echo $result2[6]; ?></td>
  <td align="center"><input type="checkbox" name="br_check3" id="br_check3"></td>
 </tr>
 
 <tr class="evenrow">
  <td align="center"><?php echo 4; ?></td>
  <td align="center"><?php echo $result2[7]; ?></td>
  <td align="center"><input type="checkbox" name="br_check4" id="br_check4"></td>
 </tr>
 
 <tr class="evenrow">
  <td align="center"><?php echo 5; ?></td>
  <td align="center"><?php echo $result2[8]; ?></td>
  <td align="center"><input type="checkbox" name="br_check5" id="br_check5"></td>
 </tr>
 
 <tr class="evenrow">
  <td align="center"><?php echo 6; ?></td>
  <td align="center"><?php echo $result2[9]; ?></td>
  <td align="center"><input type="checkbox" name="br_check6" id="br_check6"></td>
 </tr>
 </table>
 </td>
 <td>
 <table>
 <tr>
 <th class="tableheader" colspan="2">Observations</th>
 </tr>
 <tr class="evenrow">
  <td align="center">Date</td>
  <td align="center"><input type="text" name="break_ob_date" id="break_ob_date"></td>
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
 </td>
 </tr>
 </tbody>
 </table>
 </center>
 <br>
 
 <div style="overflow:auto;">
 <table width="20%" id="sub0" align="center">
<!-- <tr>
  <th class="tableheader">AS Per Drawing</th>
 <th class="tableheader"colspan="16">OBSERVED DIMENSIONS</th> 
 </tr> -->
 <tr>
	<th class="tableheader">Item</th>
	<th class="tableheader">Quantity</th>
	<th class="tableheader">Action</th>
 </tr>
 
 <tr>
   <input type="hidden" name="count_val" id="count_val" value="0" class="count_val">
	<td width="5px">
	<select type="text" name="break[items_id][]" id="frequency_id" label="items0">
	<?php $sql= "SELECT stock_id,description
   FROM ".TB_PREF."stock_master"; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{ 
	?>  
	<option value="<?php echo $row['stock_id']; ?>"><?php echo $row['description']; ?></option>
	<?php  
	} ?>
  </select>
	</td>
	<td width="5px"><input type="text" label="qty0" name="break[qty][]" /></td>
	<td><input type="button" id="AddButton" class="btn btn-primary" value="Add More"></td>

 </tr>
 
 <span class="copy_append0" style="display:none">
 <?php $sql= "SELECT stock_id,description
   FROM ".TB_PREF."stock_master"; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{ 
	?>  
	<option value="<?php echo $row['stock_id']; ?>"><?php echo $row['description']; ?></option>
	<?php  
	} ?>
 </span>
 </table>
 <center>
 <div id="fields"></div> </center>
</div> 
<script type="text/javascript">
$('#break_ob_date').datepicker({
	dateFormat: 'yy-mm-dd',
	prevText: '<i class="fa fa-chevron-left"></i>',
	nextText: '<i class="fa fa-chevron-right"></i>',
	maxDate: 0 
});

$("#AddButton").click(function() {
	 //alert('xxx');
	 var count=$("#count_val").val();
	 var count_val=parseInt(count)+1;
	        append_html = '<table id="sub'+count_val+'" style="margin-top:10px;">';
			append_html += '<tr><td><select type="text" name="break[items_id][]" id="frequency_id" label="items'+count_val+'">'+$('.copy_append0').html()+'</select></td>';
			append_html += '<td><input type="text" label="qty'+count_val+'" name="break[qty][]" /></td>';
			append_html += '<td><input type="button" class="removeclass" style="" value="&nbsp;Remove&nbsp;" onclick="select_remove('+count_val+')"></td></tr>';
			append_html += '</table>';
		$('#fields').append(append_html);
		$("#count_val").val(count_val);
});
function select_remove(num){
	$('#sub'+num+'').remove();
}
</script>
