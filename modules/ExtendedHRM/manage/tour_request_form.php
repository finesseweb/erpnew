<?php
error_reporting(1);
/* * ********************************************************************

  Released under the terms of the GNU General Public License, GPL,
  as published by the Free Software Foundation, either version 3
  of the License, or (at your option) any later version.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 * ********************************************************************* */
$page_security = 'SA_OPEN';
$path_to_root = "../../..";
$tour_request_document_folder = 'tour_request_attachments';
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
add_access_extensions();
//page(_($help_context = "Allocation Request")); 
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_tour_db.inc");

//echo "<pre>";
//print_r($_SESSION);
$js = '';

if ($version_id['version_id'] == '2.4.1') {
    if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);

    if (user_use_date_picker())
        $js .= get_js_date_picker();
}else {
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
}

page(_($help_context = "Tour Request Form"), @$_REQUEST['popup'], false, "", $js);
simple_page_mode(true);
$selected_component = $selected_id; 
?>
<html lang="en">
    <head>
	
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
		<link href="<?php echo $path_to_root?>/transport_managment/assets/css/bootstrap-datetimepicker.css" rel="stylesheet" media="screen">
        <link rel="stylesheet" href='<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>'>
        <script src='<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>'></script>
        <script src='<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js" ?>'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js'></script>
		<style>
			.select_resource_id {
				text-align: center;
				margin: 18px;
			}
			li {
				list-style: none;
			}
			.select_resource_id select {
				margin: 16px !important;
				font-size: 11px ;
			}
			.get_tour_sub {
				margin: 12px;
				border-radius: 4px;
				background: #007bff;
				color: #fff;
				width: 152px;
				border-color: #007bff;
			}
			.amount {
				text-align: left;
				font-size: 12px;
			}
			input{
			    font-size: 11px !important;
				text-align: left !important;
			}
		</style>
    </head>
</html>
<form action="" method="POST">
	<?php
			$employee_id=$_SESSION['wa_current_user']->empl_id;
			$sql = "SELECT * FROM " . TB_PREF . "kv_tour_request WHERE tr_status = 2 AND tr_employee_id='" . $employee_id . "' AND billed_status=0 ";
			$data=db_query($sql);
	?>
	<div class="select_resource_id">
			<select name="tour_id" id="tour_id" class="combo" onChange="getTourReuestId()" title="" <?php if ($_POST{'direct_TR'}) echo 'disabled' ; ?>>
			<option value="">Tour Request Id</option>
			<?php
			 while ($myrow = db_fetch($data)) { ?>
				<option <?php if ($_POST{'tour_id'} == $myrow["tr_request_id"] ) echo 'selected' ; ?> value="<?php echo $myrow["tr_request_id"];?>"><?php echo $myrow["tr_request_id"];?></option>
				
			<?php } ?>
			</select>
			
		<input type="checkbox" name="direct_TR" id="direct_TR" <?php if ($_POST{'tour_id'}) echo 'disabled' ; ?> value="direct_tr" <?php if ($_POST{'direct_TR'}) echo 'checked' ; ?> > Direct travelling 
		<button type="submit" class="get_tour_sub" name="get_tourid">Tour informations</button>
	</div>
</form>
<?php

if($_SESSION['wa_current_user']->role_name=="Faculty"){
	$authorized_by='Director';
}else{
	$authorized_by='Dean';
}
$name = trim($_POST['name']);
$place = trim($_POST['place']);
$tour_purpose = trim($_POST['tour_purpose']);
$auth_by = trim($_POST['auth_by']);
$departure_date = $_POST['departure_date'];
$arrival_date = $_POST['arrival_date'];
$fromArr = $_POST['from'];
$fdate_timeArr = $_POST['fdate_time'];
$toArr = $_POST['to'];
$tdate_timeArr = $_POST['tdate_time'];
$convenceArr = $_POST['purpose'];
$des_modeArr = $_POST['des_mode'];
$classArr = $_POST['class'];
$amountArr = $_POST['amount'];
$remarkArr = $_POST['remark'];
$totalamount = $_POST['total_amount'];
$fileArr = $_FILES["file"]["name"];
$path=$path_to_root . "/modules/ExtendedHRM/upload";
$submit_date= date('Y-m-d');
if(isset($_POST['submit_formrequest'])){
	if(!empty($place) && !empty($tour_purpose) && !empty($auth_by) && !empty($departure_date) && !empty($arrival_date)){
		if($_POST["re_tourid"]){
			$tourId = $_POST["re_tourid"];
			$bill_id='T_Bill_'.$tourId;
			$sqlQuery = "INSERT INTO fa_tour_request_details (place, purpose, auth_by, departure_date, arrival_date, name, emp_id, tour_id, bill_id, total_amount, submit_date)
			VALUES (" . db_escape($place) . ", " . db_escape($tour_purpose) . ", ". db_escape($auth_by) . ", " . db_escape($departure_date) . ", ". db_escape($arrival_date) . ", ". db_escape($name) . ", ". db_escape($employee_id) . ", ". db_escape($tourId) . ", ". db_escape($bill_id) . " , ". db_escape($totalamount) . ", ". db_escape($submit_date) . ")";
			$data = db_query($sqlQuery);
			if(!empty($fromArr)){
				for($i = 0; $i < count($fromArr); $i++){
					if(!empty($fromArr[$i])){
						$from = $fromArr[$i]; 
						$fdate= $fdate_timeArr[$i];
						$to = $toArr[$i];
						$tdate = $tdate_timeArr[$i];
						$convens = $convenceArr[$i];
						$des = $des_modeArr[$i];
						$class = $classArr[$i];
						$amount = $amountArr[$i];
						$remark = $remarkArr[$i];
						$file = $fileArr[$i];
						$file_temp = $_FILES["file"]["tmp_name"][$i];
						move_uploaded_file($file_temp,$path.'/'.$file);
					   $sql = "INSERT INTO fa_kv_tour_requestform (from_place, fdate_time, to_place, tdate_time, purpose, des_mode, class, amount, updated_amount, remark, file, tour_id, bill_id, submit_date)
				VALUES (" . db_escape($from) . ", " . db_escape($fdate) . ", ". db_escape($to) . ", " . db_escape($tdate) . ", ". db_escape($convens) . ", ". db_escape($des) . ", ". db_escape($class) . ", ". db_escape($amount) . ", 0, " . db_escape($remark) . ", " . db_escape($file) . ", " . db_escape($tourId) . ", " . db_escape($bill_id) . ", " . db_escape($submit_date) . " )";
				$ret = db_query($sql);
		   
					}
				}
			}
		}else {
			$sqlQuery = "INSERT INTO fa_tour_request_details (place, purpose, auth_by, departure_date, arrival_date, name, emp_id, tour_id, total_amount, submit_date)
			VALUES (" . db_escape($place) . ", " . db_escape($tour_purpose) . ", ". db_escape($auth_by) . ", " . db_escape($departure_date) . ", ". db_escape($arrival_date) . ", ". db_escape($name) . ", ". db_escape($employee_id) . ", ". db_escape($tourId) . " , ". db_escape($totalamount) . ", ". db_escape($submit_date) . ")";
			$data = db_query($sqlQuery);
			$last_insert_id = db_insert_id();
			$new_tour_request_id = getNewTourRequestIdnewf($last_insert_id);
			$bill_id='T_Bill_'.$new_tour_request_id;
			$rs = updateTourRequestIdDetailTb($new_tour_request_id, $last_insert_id, $bill_id);
			if(!empty($fromArr)){
				for($i = 0; $i < count($fromArr); $i++){
					if(!empty($fromArr[$i])){
						$from = trim($fromArr[$i]); 
						$fdate= $fdate_timeArr[$i];
						$to = trim($toArr[$i]);
						$tdate = $tdate_timeArr[$i];
						$convens = $convenceArr[$i];
						$des = trim($des_modeArr[$i]);
						$class = trim($classArr[$i]);
						$amount = $amountArr[$i];
						$remark = trim($remarkArr[$i]);
						$file = $fileArr[$i];

						move_uploaded_file($_FILES["file"]["tmp_name"],$path.'/'.$file);
						$sql = "INSERT INTO fa_kv_tour_requestform (from_place, fdate_time, to_place, tdate_time, purpose, des_mode, class, amount, updated_amount, remark, file, tour_id, bill_id, submit_date)
				VALUES (" . db_escape($from) . ", " . db_escape($fdate) . ", ". db_escape($to) . ", " . db_escape($tdate) . ", ". db_escape($convens) . ", ". db_escape($des) . ", ". db_escape($class) . ", ". db_escape($amount) . ", 0 , " . db_escape($remark) . ", " . db_escape($file) . ", " . db_escape($new_tour_request_id) . ", " . db_escape($bill_id) . ", ". db_escape($submit_date) . " )";
				$ret = db_query($sql);
		   
					}
				}
			}
		}
		if($data){
			echo "Your Tour Requested Form Submitted Successfully";
		}else{
			echo "Please Fill the form";
		}
	}else{
		echo "Please fill the tour details";
	}
   
}

?>

<form name="form1" id="bill-form" class="bill-form" action="" enctype="multipart/form-data" method="post" onSubmit="return form_validate();">
<input type="hidden" name="re_tourid" value="<?=$_POST['tour_id']?>">
<input type="hidden" name="total_amount" class="total_amount" value="">
<?php 

$sql2 = "SELECT * FROM " . TB_PREF . "kv_tour_request WHERE tr_status != 4 AND tr_employee_id='" . $employee_id . "' AND tr_request_id='" . $_POST["tour_id"] . "'";
$data2=db_query($sql2);
while ($myrow2 = db_fetch($data2)) { ?>
	 <div class="col-sm-12">
	 <h2><?php echo $_SESSION["wa_current_user"]->name; ?></h2><br/>
		<div class="row">
			<input type="hidden" name="name" value="<?php echo $_SESSION["wa_current_user"]->name; ?>">
			<div class="col-sm-4">
				<label class="col-md-4 pull-left">Place: </label><input type="text" class="pull-right required" value="<?=$myrow2['tr_place_of_visit'];?>" name="place" id="place"><span id="error"></span><br/>
				<!--<label class="col-md-4 pull-left">Purpose: </label><input type="text" class="pull-right required" value="<?=$myrow2['tr_purpose_of_visit'];?>" name="tour_purpose" id="tour_purpose"><span id="error"></span><br/>-->
				<label class="col-md-4 pull-left">Date and time of departure: </label><input type="text" class="form_datetime departure_date pull-right required" id="departure_date" name="departure_date" value="<?=$myrow2['tr_fromdate'];?>"><span id="error"></span><br/>

			</div>
			<div class="col-sm-4">
				<label class="col-md-4 pull-left">Authorized By: </label><input type="text" class="pull-right required" id="auth_by" name="auth_by" value="<?=$authorized_by?>"><span id="error"></span><br/>
				<label class="col-md-4 pull-left">Date and time of arrival:</label><input type="text" class="form_datetime departure_date pull-right required" id="arrival_date" name="arrival_date" value="<?=$myrow2['tr_todate'];?>"><span id="error"></span><br/>

			</div>
			<div class="col-sm-4">
				<label class="col-md-4 pull-left">Purpose: </label><input type="text" class="pull-right required" value="<?=$myrow2['tr_purpose_of_visit'];?>" name="tour_purpose" id="tour_purpose"><span id="error"></span><br/>
			</div>
			
		</div>
	</div>				
<?php 
	}
	if($_POST['direct_TR']){
?>
	<div class="col-sm-12">
		<div class="">
		<h2><?php echo $_SESSION["wa_current_user"]->name; ?></h2><br/>
			<div class="row">
				<input type="hidden" name="name" value="<?php echo $_SESSION["wa_current_user"]->name; ?>">
				<div class="col-sm-4">
					<label class="col-md-4 pull-left">Place:</label><input type="text" class="pull-right required" value="" name="place" id="place"><span id="error"></span><br/>
					<!--<label class="col-md-4 pull-left">Purpose:</label><input type="text" class="pull-right required" value="" name="tour_purpose" id="tour_purpose"><span id="error"></span><br/>-->
					<label class="col-md-4 pull-left">Date and time of departure: </label><input type="text" class="form_datetime departure_date pull-right required" id="departure_date" name="departure_date" value=""><span id="error"></span><br/>

				</div>
				<div class="col-sm-4">
					<label class="col-md-4 pull-left">Authorized By:</label><input type="text" class="pull-right required" name="auth_by" id="auth_by"><span id="error"></span><br/>
					<!--<label class="col-md-4 pull-left">Date and time of departure: </label><input type="text" class="form_datetime departure_date pull-right required" id="departure_date" name="departure_date" value=""><span id="error"></span><br/>-->
					<label class="col-md-4 pull-left">Date and time of arrival:</label><input type="text" class="form_datetime departure_date pull-right required" id="arrival_date" name="arrival_date" value=""><span id="error"></span><br/>

				</div>
				<div class="col-sm-4">
					<label class="col-md-4 pull-left">Purpose:</label><input type="text" class="pull-right required" value="" name="tour_purpose" id="tour_purpose"><span id="error"></span><br/>
					<!--<label class="col-md-4 pull-left">Date and time of arrival:</label><input type="text" class="form_datetime departure_date pull-right required" id="arrival_date" name="arrival_date" value=""><span id="error"></span><br/>-->
				</div>
			</div>
		</div>
	</div>	
	<?php } ?>
	<div class="input_fields_wrap">
		<div id="newfield1" class="form-group fieldGroup" onclick="updateTbl123(this)">
			<div class="input-group">
				<input type="text" name="from[]" id="from" class="form-control from_a required" placeholder="From" title="From"/>
				<input type="text" name="fdate_time[]" id="fdate_time" class="new_fiel_date form-control required" placeholder="Date and Time" title="Date and Time"/>
				<input type="text" name="to[]" id="to" class="form-control required" placeholder="To" title="To"/>
				<input type="text" name="tdate_time[]" id="tdate_time" class="new_fiel_date form-control required" placeholder="Date and Time" title="Date and Time"/>
				<select name="purpose[]" style="width:80px;height:38px;font-size: 11px;" title="Purpose">
					<option value="food">Food</option>
					<option value="transport">Transport</option>
					<option value="conveyance">Conveyance</option>
					<option value="boarding_loding">Boarding & Lodging</option>
					<option value="incidental">Incidental</option>
					<option value="diem_allowance">Diem allowance</option>
					<option value="other">Other settlements of bills for guests/ delegates</option>
				</select>
				<input type="text" name="des_mode[]" class="form-control" placeholder="Desc./Mode" title="Desc./Mode"/>
				<input type="text" name="class[]" class="form-control" placeholder="Class" title="Class"/>
				<input type="text" name="amount[]" class="form-control amount new_amount qty1" data-validation="number" data-validation-allowing="float" placeholder="Amount" title="Amount"/>
				<input type="text" name="remark[]" class="form-control" placeholder="Remark" title="Remark"/>
				<input type="file" name="file[]" class="" placeholder="Upload" style="height: 37px;margin-top: -2px;"//>
				<!--<a href="javascript:void(0)" class="btn btn-success addMore"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span> Add</a>-->

				<div class="input-group-addon"> 
					<a href="javascript:void(0)" class="btn btn-success addMore neww"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span>+</a>
				</div>
			</div>
		</div>
    </div>
    
    <input type="submit" name="submit_formrequest" class="btn btn-primary" value="Submit"/>
	
<!-- copy of input fields group -->
<div class="sample" style="float: right;font-size: 19px;margin-right: 12px;">
</div>

</form>
<script type="text/javascript" src="<?=$path_to_root?>/transport_managment/assets/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?=$path_to_root?>/transport_managment/assets/js/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
<script>

$(document).ready(function(){
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".addMore"); //Add button ID
   
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div id="newfield'+x+'" class="form-group fieldGroup" onclick="updateTbl123(this)"><div class="input-group"><input type="text" name="from[]" id="from" class="form-control from_a required" placeholder="From" title="From"/><input type="text" name="fdate_time[]" id="fdate_time" class="new_fiel_date form-control required" placeholder="Date and Time" title="Date and Time"/><input type="text" name="to[]" id="to" class="form-control required" placeholder="To" title="To"/><input type="text" name="tdate_time[]" id="tdate_time" class="new_fiel_date form-control required" placeholder="Date and Time" title="Date and Time"/><select name="purpose[]" style="width:80px;height:38px;font-size: 11px;" title="Purpose"><option value="food">Food</option><option value="transport">Transport</option><option value="conveyance">Conveyance</option><option value="boarding_loding">Boarding & Lodging</option><option value="incidental">Incidental</option><option value="diem_allowance">Diem allowance</option><option value="other">Other settlements of bills for guests/ delegates</option></select><input type="text" name="des_mode[]" class="form-control" placeholder="Desc./Mode" title="Desc./Mode"/><input type="text" name="class[]" class="form-control" placeholder="Class" title="Class"/><input type="text" name="amount[]" class="form-control amount new_amount qty1" data-validation="number" data-validation-allowing="float" placeholder="Amount" title="Amount"/><input type="text" name="remark[]" class="form-control" placeholder="Remark" title="Remark"/><input type="file" name="file[]" class="" placeholder="Upload" style="height: 37px;margin-top: -2px;"//><div class="input-group-addon"><a href="javascript:void(0)" class="btn btn-danger remove"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span> -</a></div></div></div>'); //add input box
        }
    });
   
    $(wrapper).on("click",".remove", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').parent('div').parent('div').remove(); x--;
    })
});
</script>
<script type="text/javascript">

function form_validate()
{
	if ($.trim($("#place").val()) === "" || $.trim($("#place").val()) === "") {
       // alert('t1!');
		$("#place").css('border','2px solid red');
        return false;
    }else{
		$("#place").css('border','2px solid green');
	}
	if ($.trim($("#auth_by").val()) === "" || $.trim($("#auth_by").val()) === "") {
       // alert('t2');
		$("#auth_by").css('border','2px solid red');
        return false;
    }else{
		$("#auth_by").css('border','2px solid green');
	}
	if ($.trim($("#tour_purpose").val()) === "" || $.trim($("#tour_purpose").val()) === "") {
        //alert('t3');
		$("#tour_purpose").css('border','2px solid red');
        return false;
    }else{
		$("#tour_purpose").css('border','2px solid green');
	}
	if ($.trim($("#departure_date").val()) === "" || $.trim($("#departure_date").val()) === "") {
        //alert('t4');
		$("#departure_date").css('border','2px solid red');
        return false;
    }else{
		$("#departure_date").css('border','2px solid green');
	}
	if ($.trim($("#arrival_date").val()) === "" || $.trim($("#arrival_date").val()) === "") {
       // alert('5');
		$("#arrival_date").css('border','2px solid red');
        return false;
    }else{
		$("#arrival_date").css('border','2px solid green');
	}
	//dynamic field validation
	var dep_date_n=$("#departure_date").val();
	var f_from = document.getElementsByName('from[]');
	var f_date_time = document.getElementsByName('fdate_time[]');
	var f_to = document.getElementsByName('to[]');
	var f_tdate_time = document.getElementsByName('tdate_time[]');
	var f_purpose = document.getElementsByName('purpose[]');
	var f_des_mode = document.getElementsByName('des_mode[]');
	var f_class = document.getElementsByName('class[]');
	var f_amount = document.getElementsByName('amount[]');
	var f_remark = document.getElementsByName('remark[]');
	var f_file = document.getElementsByName('file[]');
	for (var i = 0; i < f_from.length; i++)
    {
		if ($.trim(f_from[i].value) == "")
		{
			f_from[i].style.border = '2px solid red';
			f_from[i].focus();
			return false;            
		}else{
			f_from[i].style.border = '2px solid green';
		}
		if(f_date_time[i].value < dep_date_n){
			//alert('Your date is greater than your bill departure date!!');
			f_date_time[i].style.border = '2px solid red';
			f_date_time[i].focus();
			return false; 
		}
		if (f_date_time[i].value == "")
		{
			f_date_time[i].style.border = '2px solid red';
			f_date_time[i].focus();
			return false;            
		}else{
			f_date_time[i].style.border = '2px solid green';
		}
		if ($.trim(f_to[i].value) == "")
		{
			f_to[i].style.border = '2px solid red';
			f_to[i].focus();
			return false;            
		}else{
			f_to[i].style.border = '2px solid green';
		}
		if(f_tdate_time[i].value < f_date_time[i].value){
			//alert('Your date is greater than your bill departure date!!');
			f_tdate_time[i].style.border = '2px solid red';
			f_tdate_time[i].focus();
			return false; 
		}
		if (f_tdate_time[i].value == "")
		{
			f_tdate_time[i].style.border = '2px solid red';
			f_tdate_time[i].focus();
			return false;            
		}else{
			f_tdate_time[i].style.border = '2px solid green';
		}
		if (f_purpose[i].value == "")
		{
			f_purpose[i].style.border = '2px solid red';
			f_purpose[i].focus();
			return false;            
		}else{
			f_purpose[i].style.border = '2px solid green';
		}
		if ($.trim(f_des_mode[i].value) == "")
		{
			f_des_mode[i].style.border = '2px solid red';
			f_des_mode[i].focus();
			return false;            
		}else{
			f_des_mode[i].style.border = '2px solid green';
		}
		/*if (f_class[i].value == "")
		{
			f_class[i].style.border = '2px solid red';
			f_class[i].focus();
			return false;            
		}else{
			f_class[i].style.border = '2px solid green';
		}*/
		if (f_amount[i].value == "")
		{
			f_amount[i].style.border = '2px solid red';
			f_amount[i].focus();
			return false;            
		}else{
			f_amount[i].style.border = '2px solid green';
		}
		/*if ($.trim(f_remark[i].value) == "")
		{
			f_remark[i].style.border = '2px solid red';
			f_remark[i].focus();
			return false;            
		}else{
			f_remark[i].style.border = '2px solid green';
		}
		if (f_file[i].value == "")
		{
			f_file[i].style.border = '2px solid red';
			f_file[i].focus();
			return false;            
		}else{
			f_file[i].style.border = '2px solid green';
		}*/
	}
	return true;
}

function updateTbl123(elem){
	var id = $(elem).attr("id");
	//alert(id);
	$('#'+id).find('.new_fiel_date').datetimepicker({
		//language:  'fr',
		weekStart: 1,
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		forceParse: 0,
		showMeridian: 1
	});
	var amount_val=$('#'+id).find('.amount').attr('id','amount'+id);
	var parent_id= $('#'+id).prev().attr('id');
	
}

 $('.form_datetime').datetimepicker({
	//language:  'fr',
	weekStart: 1,
	todayBtn:  1,
	autoclose: 1,
	todayHighlight: 1,
	startView: 2,
	forceParse: 0,
	showMeridian: 1
});

var sum = 0;
var value = 0;
function addAmount(elem){
	alert($(elem).val());
	
	if(isNaN($(elem).val())==false && $(elem).val() > 0){		
		var amount_val = $(elem).val();
	}
	if(amount_val2 ==false && $(elem).val() > 0){
		var amount_val2 = isNaN($(elem).val());
	}
	var amount =amount_val;
	var amount2 =amount_val2;
	sum+= parseFloat(amount)|| 0;
	value+= parseFloat(amount2)|| 0;
	$('.sample').text('Total Amount(Rs.): '+sum);
	$('.total_amount').val(value);
} 

document.getElementById("tour_id").addEventListener("change", getTourReuestId);
function getTourReuestId(){
	
   var tr_id= document.getElementById("tour_id").value ;
   document.getElementById("direct_TR").disabled = true;
}
$.validate({
decimalSeparator : ','
});

</script>
<script>
$(document).on("change", ".qty1", function() {
    var sumd = 0;
    $(".qty1").each(function(){
		if(isNaN($(this).val())==false && $(this).val() > 0){		
			var amounrTotal = $(this).val();
		}
		var amount123 =amounrTotal;
		//alert($(this).val());
       // sumd += +amount123;
        sumd += parseFloat(amount123)|| 0;
    });
    $(".sample").text('Total Amount(Rs.): '+sumd);
});
</script>
