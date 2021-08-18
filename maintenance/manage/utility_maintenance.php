<?php

$page_security = 'SA_MAINTAINUTILITY';
$path_to_root = "../..";


include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
page(_($help_context = "Utility Maintenance")); 

//include($path_to_root . "/maintenance/includes/db/parameters_db.inc");
include($path_to_root . "/includes/ui.inc");

//include_once($path_to_root . "/manufacturing/includes/manufacturing_db.inc");
//include_once($path_to_root . "/manufacturing/includes/manufacturing_ui.inc");
$_SESSION['page_title'] = _($help_context = "New Process");
 
//page($_SESSION['page_title'], false, false, "", $js);
?>
<html lang="en">
<head>
    <link rel="stylesheet" href="<?php echo $path_to_root . "/js/jquery-ui.css" ?>">
    <script src="<?php echo $path_to_root . "/js/jquery-1.10.2.js" ?>"></script>
   <script src="<?php echo $path_to_root . "/js/jquery-ui.js"?>"></script>
  <script>
  $( function() {
    $( "#datepicker" ).datepicker();
  } );
  </script>
</head>
<body>

<?php
if(isset($_GET['success']))
{
	display_notification("Utility Maintenance has been added!");
}
?>
<form id="basicForm" action="add_proces_maintenance.php" method="POST" >
<table width="30%" align="center" style="padding:10px;">
<tr >

 <td >Utility
  <select type="text" name="utility_id" id="utility_id" >
 <?php  $sql= "SELECT id,name
   FROM ".TB_PREF."utility"; 
   $res=db_query($sql);
     echo "<option>select</option>";
	while($row = db_fetch($res))
	{  
	?>  
	<option value="<?php  echo $row['id']; ?>"><?php echo $row['name']; ?></option>
	<?php  
	 } ?>
  </select></td>  
  
  <td >Frequency
  <select type="text" name="frequency_id" id="frequency_id">
 <?php $sql= "SELECT freq_id,frequency_name
   FROM ".TB_PREF."frequency_master"; 
   $res=db_query($sql);
     echo "<option>select</option>";
	while($row = db_fetch($res))
	{ 
	?>  
	<option value="<?php echo $row['freq_id']; ?>"><?php echo $row['frequency_name']; ?></option>
	<?php  
	} ?>
  </select></td>
  </tr>
 
  <!-- <td>Margin<input type="text" name="margin" id="margin" /></td> -->
  
 </table>
 <div id="records"></div>
<!-- <input type="text" id="qty" class="" value=""/> -->
 <hr>
  <div id="addmore_fields">
<!--  <div style="overflow:auto;">
 <table width="10%" align="center">
 <tr>
 <th class="tableheader">AS Per Drawing</th>
 <th class="tableheader"colspan="16">OBSERVED DIMENSIONS</th>
 </tr>
 <tr>
 <th class="tableheader"></th>
 <th class="tableheader">1</th>
 <th class="tableheader">2</th>
 <th class="tableheader">3</th>
 <th class="tableheader">4</th>
 <th class="tableheader">5</th>
 <th class="tableheader">6</th>
 <th class="tableheader">7</th>
 <th class="tableheader">8</th>
 <th class="tableheader">9</th>
 <th class="tableheader">10</th>
 <th class="tableheader">11</th>
 <th class="tableheader">12</th>
 <th class="tableheader">13</th>
 <th class="tableheader">14</th>
 <th class="tableheader">15</th>
 <th class="tableheader">Action</th>
 </tr>
 <tr >
 
 
 <input type="hidden" name="count_val" id="count_val" value="0" class="count_val">
 <td width="5px"><input type="text" name="test[as_per_drawings][]" label="drawings0" /></td>
 <td width="5px"><input type="text" label="test1_0" name="test[t1][]" /></td>
 <td width="5px"><input type="text" label="test2_0" name="test[t2][]" /></td>
 <td width="5px"><input type="text" label="test3_0" name="test[t3][]" /></td>
 <td width="5px"><input type="text" label="test4_0" name="test[t4][]"/></td>
 <td width="5px"><input type="text" label="test5_0" name="test[t5][]" /></td>
 <td width="5px"><input type="text" label="test6_0" name="test[t6][]" /></td>
<td width="5px"> <input type="text" label="test7_0" name="test[t7][]" /></td>
 <td width="5px"><input type="text" label="test8_0" name="test[t8][]" /></td>
 <td width="5px"><input type="text" label="test9_0" name="test[t9][]" /></td>
 <td width="5px"><input type="text" label="test10_0" name="test[t10][]" /></td>
 <td width="5px"><input type="text" label="test11_0" name="test[t11][]" /></td>
 <td width="5px"><input type="text" label="test12_0" name="test[t12][]" /></td>
 <td width="5px"><input type="text" label="test13_0" name="test[t13][]" /></td>
 <td width="5px"><input type="text" label="test14_0" name="test[t14][]" /></td>
 <td width="5px"><input type="text" label="test15_0" name="test[t15][]" /></td>
 <td>
 <input type="button" id="AddButton" class="btn btn-primary" value="Add More">
 </td>
 </tr>
 

 
 </table>
 <div id="fields"></div>
 </div>  -->
 <!-- <table border="1" id="Inspectqty" align="center" style="text-align:-moz-center;"> 
 <?php // echo "<br>"; ?>
  <tr>
    <th class="tableheader">Inspected By<input type="text" name="inspected_by" id="inspected_by"></th>
	<th class="tableheader" rowspan="2">Quantity Offered</th> 
    <th class="tableheader" rowspan="2">Quantity Accepted</th>
    <th class="tableheader" rowspan="2">Quantity Rejected</th>
    <th class="tableheader">Checked By<input type="text" name="checked_by" id="checked_by"></th>
  </tr> -->
  
  <!--   <tr>
	 <td class="tableheader">Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="inspect_name" id="inspect_name"  onchange="myFunct(this.value)"></td>
	 <td class="tableheader">Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="check_name" id="check_name" onchange="myFunction(this.value)"></td>
	 
	 </tr>
	
	<?php //$sql= "SELECT *
	//FROM ".TB_PREF."workorders";
	//$res = db_query($sql);
	//print_r($res); die;
	?>
	<tr>
    <td class="tableheader">Sign &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="inspect_sign" id="inspect_sign"> </td>
	 
    <td rowspan="2"><input type="text" style="padding:15px;" name="quan_offered" id="quan_offered" value="<?php //echo $res["units_issued"]; ?>" readonly></td>		
    
  
<td rowspan="2"><input type="text" style="padding:15px;" name="quan_accepted" id="quan_accepted" onchange="myFun(this.value)"></td>
<td rowspan="2"><input type="text" style="padding:15px;" name="quan_rejected" id="quan_rejected" readonly></td>
	<td class="tableheader" >Sign &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="check_sign" id="check_sign" value=""></td>
	</tr>
	<tr>
   <td class="tableheader">Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="inspect_date" id="inspect_date"> </td>
   
	<td class="tableheader">Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="check_date" id="check_date"></td>
	</tr>  -->
  <!-- </table> -->
   </br>
 
 <div align="center">
 <br>
 <br>
 <input type="submit" id="place_quote" name="place_quote" value="Submit Report" align="center"> 
 </div>
 </form>
 </body>
<script type="text/javascript">
function myFunction() {
	var name= document.getElementById("check_name").value;
    document.getElementById("check_sign").value = name;
}

function myFunct() {
	var name= document.getElementById("inspect_name").value;
    document.getElementById("inspect_sign").value = name;
}

function myFun() {
	var quan_offered= document.getElementById("quan_offered").value;
    var quan_accepted= document.getElementById("quan_accepted").value;
    var quan_rejected= quan_offered - quan_accepted;
	document.getElementById("quan_rejected").value = quan_rejected;
  } 

</script>
 
 <script>
 $('#check_date').datepicker({
	dateFormat: 'yy-mm-dd',
	prevText: '<i class="fa fa-chevron-left"></i>',
	nextText: '<i class="fa fa-chevron-right"></i>',
	maxDate: 0 
});
 </script>
 <script>
 $('#inspect_date').datepicker({
	dateFormat: 'yy-mm-dd',
	prevText: '<i class="fa fa-chevron-left"></i>',
	nextText: '<i class="fa fa-chevron-right"></i>',
	maxDate: 0 
});
 </script>
 
 <script>
 	$(document).ready(function () {
  //called when key is pressed in textbox
  $("#quan_accepted").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57)) {
        //display error message
        $("#errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});
 	
	$(document).ready(function () {
  //called when key is pressed in textbox
  $("#quan_rejected").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57)) {
        //display error message
        $("#errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});
 
/* $("#utility_id").change(function() {
	 var utility_id=$("#utility_id").val();
	  alert(utility_id);
	 $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_maintenance.php";?>',
			data: { utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			$("#maintain_id").html(data);
		});
});  */
  
$("#utility_id").change(function() {
	 var utility_id=$("#utility_id").val();
	 // alert(utility_id);
	 $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_utlyfrequency.php";?>',
			data: { utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			$("#frequency_id").html(data);
		});
});
$("#frequency_id").change(function() {
	var frequency_id=$(this).val();
	 var utility_id=$("#utility_id").val();
	 // alert(utility_id);
	 
	  $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_utlyrecords.php";?>',
			data: { frequency_id : frequency_id,utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			
			$("#records").html(data);
		});
});

 $("#AddButton").click(function() {
	 //alert('xxx');
	 var count=$("#count_val").val();
	 var count_val=parseInt(count)+1;
	        append_html = '<table id="sub'+count_val+'" style="margin-top:10px;">';
			append_html += '<tr><td><input type="text" label="drawings'+count_val+'" name="test[as_per_drawings][]" /></td>';
			append_html += '<td><input type="text" label="test1_'+count_val+'" name="test[t1][]" /></td>';
			append_html += '<td><input type="text" label="test2_'+count_val+'" name="test[t2][]" /></td>';
			append_html += '<td><input type="text" label="test3_'+count_val+'" name="test[t3][]" /></td>';
			append_html += '<td><input type="text" label="test4_'+count_val+'" name="test[t4][]" /></td>';
			append_html += '<td><input type="text" label="test5_'+count_val+'" name="test[t5][]" /></td>';
			append_html += '<td><input type="text" label="test6_'+count_val+'" name="test[t6][]" /></td>';
			append_html += '<td><input type="text" label="test7_'+count_val+'" name="test[t7][]" /></td>';
			append_html += '<td><input type="text" label="test8_'+count_val+'" name="test[t8][]" /></td>';
			append_html += '<td><input type="text" label="test9_'+count_val+'" name="test[t9][]" /></td>';
			append_html += '<td><input type="text" label="test10_'+count_val+'" name="test[t10][]" /></td>';
			append_html += '<td><input type="text" label="test11_'+count_val+'" name="test[t11][]" /></td>';
			append_html += '<td><input type="text" label="test12_'+count_val+'" name="test[t12][]" /></td>';
			append_html += '<td><input type="text" label="test13_'+count_val+'" name="test[t13][]" /></td>';
			append_html += '<td><input type="text" label="test14_'+count_val+'" name="test[t14][]" /></td>';
			append_html += '<td><input type="text" label="test15_'+count_val+'" name="test[t15][]" /></td>';
			append_html += '<td><input type="button" class="removeclass" style="" value="&nbsp;Remove&nbsp;" onclick="select_remove('+count_val+')"></td></tr>';
			append_html += '</table>';
		$('#fields').append(append_html);
		$("#count_val").val(count_val);
});
function select_remove(num){
	$('#sub'+num+'').remove();
}

	$("#w_ref").change(function() {
	 var ref_no=$("#w_ref").val();
	// alert(ref_no);
	  $.ajax({ 
			type: "POST",
		url:'<?php echo $path_to_root . "/manufacturing/ajax_get_records_quantity.php";?>',
			data: { ref_no : ref_no}
		}).done( function( data ) { 
		//alert(data);
		//	$("#qty").append(data);
		//	$("#quan_offered").append(data);
			$("#quan_offered").val(data);
		});
});

 </script>
 
</html>
<?php
end_page();
?>