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
$frequency_id  = $_POST['frequency_id'];

/* $sql="SELECT prevent.utility_id,prevent.frequency_id FROM ".TB_PREF."prevent_maintain_entry AS prevent where prevent.prevent_id = '$prevent_utility'";
$res = db_query($sql);
$result = db_fetch_row($res);

$utility_id = $result[0];
$frequency_id= $result[1]; */

$sql2="SELECT utly.ut_param_id FROM ".TB_PREF."utility_parameters_master AS utly where utly.utilitys_id = '$utility_id' AND utly.frequency_id='$frequency_id' AND utly.type_maintenance_id=2";

$res2 = db_query($sql2);
$result2 = db_fetch($res2);

$param_id = $result2['ut_param_id'];


$sql3="SELECT utly_items.* FROM ".TB_PREF."utility_parameter_items AS utly_items where utly_items.ut_param_id = '$param_id'";
$res3 = db_query($sql3);
//echo $res3['items_id'];
//echo'<pre>';print_r($res);die;
?>
<center>
<table width="60%">
<tbody>
<tr>
<td>
<table>
 <tr>
 <th class="tableheader">S. No.</th>
 <th class="tableheader">Parameters / Scope of work</th>
 <th class="tableheader">Status</th>
 </tr>
 
 <?php $i=1;


 while($result3 = db_fetch($res3)){
 //display_error($result3['items_id']);
  if(isset($result3)) { 

  ?>
   <input type="hidden" name="ch_id[]" value="<?php  echo $result3['items_id']; ?>" />
  <tr class="evenrow">
  <td align="center"><?php echo $i++; ?></td>
  <td align="center"><?php echo $result3['param_title']; ?></td>
  <td align="center"><input type="checkbox" name="check_<?php echo $result3['items_id']; ?>" id="check_<?php echo $result3['items_id']; ?>" value="<?php echo $result3['items_id']; ?>"/></td>

 </tr>
 <?php 
 } else{ 
?>
  <tr class="evenrow">
  <td align="center" colspan="4"><b>No Records Found</b></td> 
 </tr>
 <?php }
 } ?>
 
</table>
</td>
 <td align="right">
 <table>
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
  <td align="center">Corrective Action Suggested</td>
  <td align="center"><textarea name="ob_2" id="ob_2"></textarea></td>
 </tr>
 <tr class="evenrow">
  <td align="center">Corrective Action Initiated</td>
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
 <table width="40%" id="sub0" align="center">
<!-- <tr>
  <th class="tableheader">AS Per Drawing</th>
 <th class="tableheader"colspan="16">OBSERVED DIMENSIONS</th> 
 </tr> -->
 <tr>
	<th class="tableheader">Category</th>
	<th class="tableheader">Sub Category</th>
	<th class="tableheader">Item</th>
	<th class="tableheader">Quantity</th>
	<th class="tableheader">Total Stock Quantity</th>
	<th class="tableheader">Action</th>
 </tr>
 
 <tr>
   <input type="hidden" name="count_val" id="count_val" value="0" class="count_val">
	<td width="5px">
	<select type="text" name="process[cat_id][]" id="cat_id0" label="cat0" onchange="stockcat(this.value,0);">
	<?php $sql= "SELECT category_id, description, inactive FROM ".TB_PREF."stock_category"; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{ 
	?>  
	<option value="<?php echo $row['category_id']; ?>"><?php echo $row['description']; ?></option>
	<?php  
	} ?>
  </select>
	</td>
	
	<td width="5px">
	<select type="text" name="process[sub_cat_id][]" id="sub_cat_id0" label="sub_cat0" onchange="stock_item(this.value,0);" >
	<?php $sql= "SELECT sub_cat_id,sub_cat_name FROM ".TB_PREF."stock_sub_category"; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{ 
	?>  
	<option value="<?php echo $row['sub_cat_id']; ?>"><?php echo $row['sub_cat_name']; ?></option>
	<?php  
	} ?>
  </select>
	</td>
	
	
	<td width="5px">
	<select type="text" name="process[items_id][]" id="items_id0" label="items0" onchange="stockqty(this.value,0);">
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
	
	
	<td width="5px"><input type="text" label="qty0" name="process[qty][]" id="qty0" onchange="validateqty(this.value,0);" /></td>
	<td width="5px">
	<input type="text" name="process[stock_qty][]" id="stock_qty0" label="stk_qty0" readonly />
	<?php /* $sql= "SELECT SUM(moves.qty) AS qty,stock.stock_id AS stock_id
   FROM ".TB_PREF."stock_master AS stock,".TB_PREF."stock_moves AS moves WHERE stock.stock_id=moves.stock_id GROUP BY stock.stock_id"; 
   $res=db_query($sql);
   //  echo "<option>Select</option>";
	while($row = db_fetch($res))
	{  */
	?>  
	<!-- <option value="<?php // echo $row['stock_id']; ?>"><?php // echo $row['qty']; ?></option>
	<?php  
	// } ?>
  </select> -->
	</td>
	<td><input type="button" id="AddButton" class="btn btn-primary" value="Add More"></td>

 </tr>
 
 <span class="copy_append1" style="display:none">
 <?php $sql= "SELECT category_id, description, inactive FROM ".TB_PREF."stock_category"; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{ 
	?>  
	<option value="<?php echo $row['category_id']; ?>"><?php echo $row['description']; ?></option>
	<?php  
	} ?>
 </span>
 
 <span class="copy_append2" style="display:none">
 <?php $sql= "SELECT sub_cat_id,sub_cat_name FROM ".TB_PREF."stock_sub_category"; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{ 
	?>  
	<option value="<?php echo $row['sub_cat_id']; ?>"><?php echo $row['sub_cat_name']; ?></option>
	<?php  
	} ?>
 </span>
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
 
 <!--  <span class="copy_appends" style="display:none">
 <?php /* $sql= "SELECT SUM(moves.qty) AS qty,stock.stock_id AS stock
   FROM ".TB_PREF."stock_master AS stock,".TB_PREF."stock_moves AS moves WHERE stock.stock_id=moves.stock_id GROUP BY stock.stock_id"; 
   $res=db_query($sql);
    // echo "<option>Select</option>";
	while($row = db_fetch($res))
	{  */
	?>  
	<option value="<?php // echo $row['stock']; ?>"><?php // echo $row['qty']; ?></option>
	<?php  
	// } ?>
 </span> -->
 </table>
 <center>
 <div id="fields"></div> </center>
 
  <center><a href="#" name="new_item[]" id="new_items"  value="" onclick="enterNewItem();">New Item</a></center>
 <input type="hidden" name="count_new" id="count_new" value="0" class="count_new">
 <div id="item_fields" style="display:none">
 
 <table width="50%" id="items_data" align="center">
 <tr>
 <th class="tableheader" colspan="2">New Item Details</th>
 </tr>
 <tr class="evenrow">
  <td align="center">Item Name<input type="text" name="New[n_item][]" id="n_item0"></td>
 </tr>
 <tr class="evenrow">
  <td align="center">Quantity<input type="text" name="New[n_qty][]" id="n_qty0"></td>
 </tr>
 <tr class="evenrow">
  <td align="center">Bill Date<input type="text" name="New[n_bill_date][]" id="n_bill_date0"></td>
 </tr>
 <tr class="evenrow">
  <td align="center">Bill No.<input type="text" name="New[n_billno][]" id="n_billno0"></td>
 </tr>
 <tr class="evenrow">
  <td align="center">Contractor Name<input type="text" name="New[n_contractor][]" id="n_contractor0"></td>
 </tr>
 <tr class="evenrow">
  <td align="center">Comments<textarea name="New[n_comments][]" id="n_comments0"></textarea></td>
 </tr>
   </table>
 </div>
 <center>
 <div style="margin:10px;" id="new_fields"></div>
 </center>
 
</div> 
<script type="text/javascript">
$('#ob_date').datepicker({
	dateFormat: 'dd-mm-yy',
	prevText: '<i class="fa fa-chevron-left"></i>',
	nextText: '<i class="fa fa-chevron-right"></i>',
	maxDate: 0 
});

$("#AddButton").click(function() {
	 //alert('xxx');
	 var count=$("#count_val").val();
	 var count_val=parseInt(count)+1;
	        append_html = '<table id="sub'+count_val+'" width="40%" style="margin-top:10px;" align="center">';
			append_html += '<tr><td><select type="text" name="process[cat_id][]" id="cat_id'+count_val+'" label="cat'+count_val+'" onchange="stockcat(this.value,'+count_val+');">'+$('.copy_append1').html()+'</select></td>';
			append_html += '<td><select type="text" name="process[sub_cat_id][]" id="sub_cat_id'+count_val+'" label="sub_cat'+count_val+'" onchange="stock_item(this.value,'+count_val+');">'+$('.copy_append2').html()+'</select></td>';
			append_html += '<td><select type="text" name="process[items_id][]" id="items_id'+count_val+'" label="items'+count_val+'" onchange="stockqty(this.value,'+count_val+');">'+$('.copy_append0').html()+'</select></td>';
			append_html += '<td><input type="text" label="qty'+count_val+'" name="process[qty][]" id="qty'+count_val+'" onchange="validateqty(this.value,'+count_val+');"/></td>';
			append_html += '<td><input type="text" name="process[stock_qty][]" id="stock_qty'+count_val+'" label="stk_qty'+count_val+'" readonly/></td>';
			append_html += '<td><input type="button" class="removeclass" style="" value="&nbsp;Remove&nbsp;" onclick="select_remove('+count_val+')"></td></tr>';
			append_html += '</table>';
		$('#fields').append(append_html);
		$("#count_val").val(count_val);
});
function select_remove(num){
	$('#sub'+num+'').remove();
}

function stockqty(val,num){
//alert(val);
var item = val;
// var stock = $("#basicForm select[label='stk_qty"+num+"']").val();	
	 $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_process_stock.php";?>',
			data: { item : item}
		}).done( function( data ) { 
	    // 	alert(data);
		 var result = $.parseJSON(data);
			$("#stock_qty"+num+"").val(result[0]);
		});	

}

function validateqty(val,num){
 var qty = val;
 //alert(qty);
 var stock_qty = parseFloat($("#stock_qty"+num+"").val());
 //alert(stock_qty);
 if(stock_qty > qty){
 
 }
 else {
 alert('Insufficient Stock Quantity');
 $("#qty"+num+"").val(0);
 }
}

function stockcat(val,num){
//alert(val);
var cat = val;	
	 $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_sub_cat.php";?>',
			data: { cat : cat}
		}).done( function( data ) { 
	     //	alert(data);
		
			$("#sub_cat_id"+num+"").html(data);
			
			 var sub_cat = $("#sub_cat_id"+num+"").val();
			$.ajax({ 
				type: "POST",
				url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_item.php";?>',
				data: { cat : cat ,sub_cat : sub_cat }
			}).done( function( data ) { 
				//alert(data);
			
				$("#items_id"+num+"").html(data);
				
				var item = $("#items_id"+num+"").val();
				$.ajax({ 
					type: "POST",
					url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_process_stock.php";?>',
					data: { item : item}
				}).done( function( data ) { 
				// 	alert(data);
				 var result = $.parseJSON(data);
					$("#stock_qty"+num+"").val(result[0]);
				});	
				
			
			});
		});	

}

function enterNewItem(){
//$('#item_fields').show();
 
 var count=$("#count_new").val();
	 var count_val=parseInt(count)+1;
	// alert(count_val);
	        append_html = '<table class="" width="30%" id="items_data">';
			append_html += '<tr><th  colspan="6" align="center">New Item Details</th></tr>';
			append_html += '<tr class="evenrow"><td class="tableheader" align="center">Item Name</td><td class="tableheader" align="center">Quantity</td><td class="tableheader" align="center">Bill Date</td><td class="tableheader" align="center">Bill No.</td><td class="tableheader" align="center">Contractor Name</td><td class="tableheader" align="center">Comments</td></tr>';
			append_html += '<tr class="evenrow"><td><input type="text" name="New[n_item][]" id="n_item'+count_val+'"></td><td><input type="text" name="New[n_qty][]" id="n_qty'+count_val+'"></td><td><input type="text" name="New[n_bill_date][]" id="n_bill_date'+count_val+'"></td><td><input type="text" name="New[n_billno][]" id="n_billno'+count_val+'"></td><td><input type="text" name="New[n_contractor][]" id="n_contractor'+count_val+'"></td><td><textarea name="New[n_comments][]" id="n_comments'+count_val+'"></textarea></td></tr>';
			append_html += '</table>';
		$('#new_fields').append(append_html);
		$("#count_new").val(count_val);
		$('#n_bill_date'+count_val+'').datepicker({
				dateFormat: 'dd-mm-yy',
				prevText: '<i class="fa fa-chevron-left"></i>',
				nextText: '<i class="fa fa-chevron-right"></i>',
				maxDate: 0 
	   });
}
</script>
