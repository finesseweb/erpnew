<?php

$page_security = 'SA_MAINTAINPREVENTIVE';
$path_to_root = "../..";


include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
page(_($help_context = "Process Maintenance ")); 

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
	display_notification("Process Maintenance has been added!");
}
?>
<form id="basicForm" action="add_proces_maintenance.php" method="POST" >
<table width="30%" align="center" style="padding:10px;">
<tr >
 <td>Date<input type="text"  class="sal" name="maintain_date" id="maintain_date"/></td>
 <td >Process
  <select type="text" name="utility_id" id="utility_id" >
 <?php  $sql= "SELECT id,name
   FROM ".TB_PREF."utility WHERE maintenance_type_id=2 AND type=2"; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{  
	?>  
	<option value="<?php  echo $row['id']; ?>"><?php echo $row['name']; ?></option>
	<?php  
	 } ?>
  </select></td>  
  
  <td>Frequency
  <select type="text" name="frequency_id" id="frequency_id">
 <?php $sql= "SELECT freq_id,frequency_name
   FROM ".TB_PREF."frequency_master"; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{ 
	?>  
	<option value="<?php echo $row['freq_id']; ?>"><?php echo $row['frequency_name']; ?></option>
	<?php  
	} ?>
  </select></td>
  <td>Contractor
  <select type="text" name="contractor_id" id="contractor_id">
 <?php $sql= "SELECT supplier_id,supp_name FROM ".TB_PREF."contractor ORDER BY supplier_id"; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{ 
	?>  
	<option value="<?php echo $row['supplier_id']; ?>"><?php echo $row['supp_name']; ?></option>
	<?php  
	} ?>
  </select></td>
  </tr>
 </table>
 <div id="records"></div>
 <hr>
  <div id="addmore_fields">

   </br>
 
 <div align="center">
 <br>
 <br>
 <input type="submit" id="add_entry" name="add_entry" value="Submit" align="center"> 
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
 $('#maintain_date').datepicker({
	dateFormat: 'dd-mm-yy',
	prevText: '<i class="fa fa-chevron-left"></i>',
	nextText: '<i class="fa fa-chevron-right"></i>',
	maxDate: 0 
});
 </script>
 <script>
 $('#inspect_date').datepicker({
	dateFormat: 'dd-mm-yy',
	prevText: '<i class="fa fa-chevron-left"></i>',
	nextText: '<i class="fa fa-chevron-right"></i>',
	maxDate: 0 
});
 </script>
 
 <script>
  
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
	//  alert(utility_id);
	 
	  $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_processrecords.php";?>',
			data: { frequency_id : frequency_id,utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			
			$("#records").html(data);
		});
});  

 
 </script>
 
</html>
<?php
end_page();
?>
<style>
input.sal{
width:70px;
}
</style>