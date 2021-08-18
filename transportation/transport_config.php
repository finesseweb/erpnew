<?php
$page_security = 'SA_ROUTECONFIG';
if (!@$_GET['popup'])
	$path_to_root = "..";
else	
	$path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/transportation/function/function.php");

include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );


if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Transport Configuration"), @$_REQUEST['popup'], false, "", $js);


simple_page_mode(true);


$sql1 = "SELECT MAX(trans_id) FROM fa_transportation WHERE trans_id LIKE 'trans-%'";
    $empl_data = db_query($sql1);
    $empid_result = db_fetch_row($empl_data);

    $last_emp1 = substr($empid_result[0], 7);
    $emp_inc_id = $last_emp1 + 1;
    if (strlen($emp_inc_id) == 1) {
        $_POST['trans_id'] = 'trans-00' . $emp_inc_id;
    } else if (strlen($emp_inc_id) == 2) {
        $_POST['trans_id'] = 'trans-0' . $emp_inc_id;
    } else {
        $_POST['trans_id'] = 'trans-' . $emp_inc_id;
    }
	
	
	/*Stop Name*/
	$stop_sql ="SELECT * FROM ".TB_PREF."stop where status=1";
	$stop_result=db_query($stop_sql);

	//count no of student//////
	$student_sql ="SELECT COUNT(student_no) as student FROM ".TB_PREF."transportation_details
	 where trans_id=".db_escape($_GET['config']);
	$student_result=db_query($student_sql);
	$student_val=db_fetch($student_result);

?>

<link rel="stylesheet" href="css/bootstrap.min.css">
<div class="panel panel-default">
	<form class="no-margin" id="formValidate1" action="" method="post" onSubmit="return form_validate();">
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Route Name</label>
				    <?php 
					    /*Route Name*/
						$sql ="SELECT * FROM ".TB_PREF."route where status=1";
						$route_result=db_query($sql); 
					?>
					<div class="form-group">
						<select id="route_name" class="form-control" name="route_name">
						  <option value="">Select</option>
						  <?php  while ($myrow = db_fetch($route_result)){?>
							<option value="<?=$myrow['route_name']?>"><?=$myrow['route_name']?></option>
						  <?php } ?>
						</select>
					</div>
				</div>
				
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Source</label>
					<div class="form-group">
						<input type="text" name="source" id="source" class="form-control" value='' readonly="true" />
					</div>
				</div>
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Destination</label>
					<div class="form-group">
						<input type="text" name="destination" id="destination" class="form-control" value='' readonly="true" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Vehicle No.</label>
				    <?php 
						$sql ="SELECT * FROM ".TB_PREF."vehicle Where status=1";
						$vehicle_result=db_query($sql); 
					?>
					<div class="form-group">
						<select id="vehicle_no" class="form-control" name="vehicle_no">
						  <option value="">Select</option>
						  <?php  while ($myrow = db_fetch($vehicle_result)){?>
							<option value="<?=$myrow['vehicle_no']?>"><?=$myrow['vehicle_no']?></option>
						  <?php } ?>
						</select>
					</div>
				</div>
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Reg. No.</label>
					<div class="form-group">
						<input type="text" name="reg_no" id="reg_no" class="form-control" readonly="true" />
					</div>
				</div>
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Driver Name</label>
					<div class="form-group">
						<input type="text" name="driver_name" id="driver_name" class="form-control" readonly="true" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Stop </label>
				    <!-- <?php 
						$sql ="SELECT * FROM ".TB_PREF."stop where status=1";
						$stop_result=db_query($sql); 
					?> -->
					<div class="form-group">
						<select id="stop" class="form-control" name="stop">
						  <option value="">Select</option>
						 <!--  <?php  while ($myrow = db_fetch($stop_result)){?>
							<option value="<?=$myrow['id']?>"><?=$myrow['stop_name']?></option>
						  <?php } ?> -->
						</select>
					</div>
				</div>
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Sequence </label>
					<div class="form-group">
						<input type="text" name="sequence" id="sequencevalue" class="form-control" readonly="true" />
					</div>
				</div>
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Total Students</label>
					<div class="form-group">
						<input type="text" name="no_student" id="no_student" class="form-control" value="<?=$student_val['student']?>" readonly="true" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Expected Pickup Time</label>
					<div class="form-group">
						<input type="time" name="exp_time" id="exp_time" class="form-control" readonly="true" />
					</div>
				</div>
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Expected Drop Time</label>
					<div class="form-group">
						<input type="time" name="drop_time" id="drop_time" class="form-control" readonly="true" />
					</div>
				</div>
			</div>
			<input type="hidden" name="count_val" id="count_val" value="1" class="count_val">
			<div style="border:2px solid #ccc; padding:24px;margin-top:25px;">
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group">
							<label class="">Batch Code</label>
							 <?php 
							    set_global_connection(2);
								$batchsql ="SELECT * FROM academic_master";
								$batch_result=db_query($batchsql); 
							?>
							<div class="form-group">
								<select id="batch_no" class="form-control batch_no" name="batch_no[]">
								  <option value="">Select</option>
								  <?php  while ($batch_val = db_fetch($batch_result)){?>
									<option value="<?=$batch_val['academic_year_id']?>"><?=$batch_val['batch_code']?></option>
								  <?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label class="">Student Registration No.</label>
							 <?php 
							    set_global_connection(2);
								$sql ="SELECT * FROM participants_login WHERE participant_Alumni=". db_escape(0);
								$student_result=db_query($sql); 
							?>
							<div class="form-group">
								<select id="student_no" class="form-control student_no" name="student_no[]">
								  <option value="">Select</option>
								  <!-- <?php  while ($student_row = db_fetch($student_result)){?>
									<option value="<?=$student_row['roll_no']?>"><?=$student_row['roll_no']?></option>
								  <?php } ?> -->
								</select>
							</div>
						</div>
					</div>
					<div class="col-sm-2"><div class="form-group"><label class="">Name</label><input type="text" name="name[]" id="s_name" class="form-control" readonly="true" /></div></div>
				
					<div class="col-sm-2"><div class="form-group"><label class="">Father's Name</label><input type="text" name="f_name[]" id="f_name" class="form-control" readonly="true" /></div></div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="">Status</label>
							<select id="status" class="form-control" name="status[]">
								<option value="1">Active</option>
								<option value="2">Deactive</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div id="fields"></div>
			<div class="col-md-3" style="">	</div><div class="col-md-3"></div><input type="button" value="+" style="margin:24px 15px;padding:7px 15px;" id="AddButton" class="btn btn-primary pull-right" > 	
		</div>
		<div class="panel-footer">									
			<div class="row">
				<div class="col-sm-7 col-sm-offset-5">
					<div style="float:left;margin-right:2px;">
					<input class="btn btn-primary submit" type="submit" name="submit" value="Submit">
					</div>
					<div style="float:left;padding:0px 10px;">
					<button type="reset" class="btn btn-danger btn-default">Reset</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<?php
/*Stop Name*/
set_global_connection(0);	
	
	$trans_id = trim($_POST['trans_id']);
	$route_name = trim($_POST['route_name']);
	$source =$_POST['source'];
	$destination = $_POST['destination'];
	$vehicle_no = $_POST['vehicle_no'];
	$reg_no = $_POST['reg_no'];
	$driver_name = $_POST['driver_name'];
	$stop = $_POST['stop'];
	$sequence = $_POST['sequence'];
	$exp_time = $_POST['exp_time'];
	$drop_time = $_POST['drop_time'];
	$student_noArr = $_POST['student_no'];
	$batch_noArr = $_POST['batch_no'];
	$nameArr = $_POST['name'];
	$f_nameArr = $_POST['f_name'];
	$statusArr =$_POST['status'];
	if(isset($_POST['submit'])){

		$stopCount=checkStop($stop,$route_name);
		$vehicleCount=checkvehicle($stop,$route_name,$vehicle_no);

		if($stopCount > 0 && $vehicleCount > 1){

			$trans_id =$vehicleCount['trans_id'];
			$fetchstoplist=fetchstoplist($stop,$route_name);
			updateTransportationList($trans_id, $route_name,$source,$destination,$vehicle_no,$reg_no,$driver_name,$stop,$sequence,$exp_time,$drop_time);
		}else{

			add_transportation($trans_id, $route_name,$source,$destination,$vehicle_no,$reg_no,$driver_name,$stop,$sequence,$exp_time,$drop_time);
		}
		
		for($i=0; $i<count($student_noArr); $i++ ){
			$student_no=$student_noArr[$i];
			$name=$nameArr[$i];
			$batch_no=$batch_noArr[$i];
			$f_name=$f_nameArr[$i];
			$status=$statusArr[$i];
			add_transportationDetail($trans_id,$batch_no,$student_no, $name, $f_name,$status);
		}
    }

set_global_connection(2);
	$sql ="SELECT * FROM erp_student_information";
	$student_result=db_query($sql); 


end_page();
?>

<script>
var count = $('#count_val').val(); //Addmore script code

		$("body").on("click","#AddButton",function(event)
		{
		var count_value = count++;
		append_html = '<div style="border:2px solid #ccc; padding:24px;margin-top:25px;"><div class="row">';
		append_html += '<div class="col-sm-3"><div class="form-group"><label class="">Batch Code</label><?php set_global_connection(2); $batchsql ="SELECT * FROM academic_master";$batch_result=db_query($batchsql); ?><div class="form-group"><select id="batch_no'+count_value+'" class="form-control batch_no" name="batch_no[]" onchange="batchMultiple('+count_value+')"><option value="">Select</option><?php  while ($batch_val = db_fetch($batch_result)){?><option value="<?=$batch_val['academic_year_id']?>"><?=$batch_val['batch_code']?></option><?php } ?></select></div></div></div>';
		append_html += '<div class="col-sm-3"><div class="form-group"><label class="">Student Registration No.</label><?php set_global_connection(2); $sql ="SELECT * FROM participants_login WHERE participant_Alumni=". db_escape(0);$student_result=db_query($sql); ?><div class="form-group"><select id="student_no'+count_value+'" class="form-control student_no1" name="student_no[]" onchange="check('+count_value+')"><option value="">Select</option></select></div></div></div>';
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Name</label><input type="text" name="name[]" id="s_name'+count_value+'" class="form-control" readonly="true"/></div></div>';
			
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Father&#39;s Name</label><input type="text" name="f_name[]" id="f_name'+count_value+'" class="form-control" readonly="true" /></div></div>';
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Status</label><select id="status" class="form-control" name="status[]"><option value="1">Active</option><option value="2">Deactive</option></select></div></div>';
			
		append_html += '<a href="#" class="removeclass btn btn-primary remove_class pull-right">-</a>';
		append_html += '</div></div>';	
		$('#fields').append(append_html);
		
		});
		$("body").on("click",".removeclass", function(e){
		//alert('sdsd');

                $(this).parent('div').parent('div').remove(); 				
				
        return false;

    }); 




function form_validate() {
	// if ($.trim($("#trans_id").val()) === "" || $.trim($("#trans_id").val()) === "") {
 //       // alert('t1!');
	// 	$("#trans_id").css('border','2px solid red');
 //        return false;
 //    }else{
	// 	$("#trans_id").css('border','2px solid green');
	// }
	if ($.trim($("#route_name").val()) === "" || $.trim($("#route_name").val()) === "") {
       // alert('t2');
		$("#route_name").css('border','2px solid red');
        return false;
    }else{
		$("#route_name").css('border','2px solid green');
	}

	if ($.trim($("#source").val()) === "" || $.trim($("#source").val()) === "") {
       // alert('t2');
		$("#source").css('border','2px solid red');
        return false;
    }else{
		$("#source").css('border','2px solid green');
	}

	if ($.trim($("#destination").val()) === "" || $.trim($("#destination").val()) === "") {
       // alert('t2');
		$("#destination").css('border','2px solid red');
        return false;
    }else{
		$("#destination").css('border','2px solid green');
	}

	if ($.trim($("#vehicle_no").val()) === "" || $.trim($("#vehicle_no").val()) === "") {
       // alert('t2');
		$("#vehicle_no").css('border','2px solid red');
        return false;
    }else{
		$("#vehicle_no").css('border','2px solid green');
	}

	if ($.trim($("#reg_no").val()) === "" || $.trim($("#reg_no").val()) === "") {
       // alert('t2');
		$("#reg_no").css('border','2px solid red');
        return false;
    }else{
		$("#reg_no").css('border','2px solid green');
	}

	if ($.trim($("#driver_name").val()) === "" || $.trim($("#driver_name").val()) === "") {
       // alert('t2');
		$("#driver_name").css('border','2px solid red');
        return false;
    }else{
		$("#driver_name").css('border','2px solid green');
	}
	if ($.trim($("#stop").val()) === "" || $.trim($("#stop").val()) === "") {
       // alert('t2');
		$("#stop").css('border','2px solid red');
        return false;
    }else{
		$("#stop").css('border','2px solid green');
	}

	if ($.trim($("#sequencevalue").val()) === "" || $.trim($("#sequencevalue").val()) === "") {
       // alert('t2');
		$("#sequencevalue").css('border','2px solid red');
        return false;
    }else{
		$("#sequencevalue").css('border','2px solid green');
	}

	if ($.trim($("#no_student").val()) === "" || $.trim($("#no_student").val()) === "") {
       // alert('t2');
		$("#no_student").css('border','2px solid red');
        return false;
    }else{
		$("#no_student").css('border','2px solid green');
	}


	if ($.trim($("#exp_time").val()) === "" || $.trim($("#exp_time").val()) === "") {
       // alert('t2');
		$("#exp_time").css('border','2px solid red');
        return false;
    }else{
		$("#exp_time").css('border','2px solid green');
	}

	if ($.trim($("#drop_time").val()) === "" || $.trim($("#drop_time").val()) === "") {
       // alert('t2');
		$("#drop_time").css('border','2px solid red');
        return false;
    }else{
		$("#drop_time").css('border','2px solid green');
	}
	
	//dynamic field validation
	var student_no = document.getElementsByName('student_no[]');
	var name = document.getElementsByName('name[]');
	var f_name = document.getElementsByName('f_name[]');
	var f_status = document.getElementsByName('status[]');
	
	for (var i = 0; i < student_no.length; i++)
    {
		if ($.trim(student_no[i].value) == "")
		{
			student_no[i].style.border = '2px solid red';
			student_no[i].focus();
			return false;            
		}else{
			student_no[i].style.border = '2px solid green';
		}
		
		if (name[i].value == "")
		{
			name[i].style.border = '2px solid red';
			name[i].focus();
			return false;            
		}else{
			name[i].style.border = '2px solid green';
		}
		
		
		if (f_name[i].value == "")
		{
			f_name[i].style.border = '2px solid red';
			f_name[i].focus();
			return false;            
		}else{
			f_name[i].style.border = '2px solid green';
		}
		
	}
	return true;
}
	
</script>
<script>
	 
	$('#route_name').change(function(){
		var route = $(this).val();
		$.ajax({
		type: "POST",
		url: "transportation/ajax.php", // url to request
		data: { 
		route_value: route  
		},
		success : function(data){
			
			var obj = JSON.parse(data);
			$('#source').val(obj.source);
			$('#destination').val(obj.destination);

		}

		});
	});


	$('#route_name').change(function(){
		var route = $(this).val();
		$.ajax({
		type: "POST",
		url: "transportation/ajax.php", // url to request
		data: { 
		route_name: route  
		},
		success : function(data){
			
			 helpers2.buildDropdown(

                    jQuery.parseJSON(data),

                    $('#stop'),

                    'Select an option'

                );

		}
		
		});
	});


	$('#vehicle_no').change(function(){
		var vehicle = $(this).val();
		$.ajax({
		type: "POST",
		url: "transportation/ajax.php", // url to request
		data: { 
		vehicle_no: vehicle 
		},
		success : function(data){

			var obj = JSON.parse(data);

			$('#reg_no').val(obj.ModelYear);
			$('#driver_name').val(obj.driver_name);

		}
		});
	});
	$('#stop').change(function(){
		var stop_id = $(this).val();
		$.ajax({
		type: "POST",
		url: "transportation/ajax.php", 
		data: { 
		id: stop_id 
		},
		success : function(data){

			var obj = JSON.parse(data);
			$('#sequencevalue').val(obj.sequence);
			$('#exp_time').val(obj.exp_time);
			$('#drop_time').val(obj.drop_time);

		}
		});
	});

	$('.student_no').change(function(){

		var student_id = $(this).val();
		$.ajax({
		type: "POST",
		url: "transportation/ajax.php", // url to request
		data: { 
		student_no: student_id 
		},
		success : function(data){

			
			var obj = JSON.parse(data);
			
			if(obj.student_no){
				alert(obj.student_no+' User already exist. Before adding in this route please deactive from '+obj.route_name);
			}else{
				$('#s_name').val(obj.stu_fname);
				$('#f_name').val(obj.father_lname);
			}
			

		}
		});
	});


	$('.batch_no').change(function(){

		var batch_id = $(this).val();
		$('#s_name').val('');
		$('#f_name').val('');
		$.ajax({
		type: "POST",
		url: "transportation/ajax.php", // url to request
		data: {batch_no:batch_id},
	//	dataType: 'json',
		success:function(data){
                                helpers.buildDropdown(

                    jQuery.parseJSON(data),

                    $('#student_no'),

                    'Select an option'

                );

		}
		});
	});

	$('.student_no1').change(function(){
		alert('test');
		// var student_id = $(this).val();
		// $.ajax({
		// type: "POST",
		// url: "/finesse-erp/transportation/ajax.php", // url to request
		// data: { 
		// student_no: student_id 
		// },
		// success : function(data){

		// 	var obj = JSON.parse(data);
		// 	$('#s_name').val(obj.stu_fname);
		// 	$('#f_name').val(obj.father_lname);

		// }
		// });
	});

	function check(id){

		var student_id = $('#student_no'+id).val();
		var pre_value = $('#student_no').val();
		
		$('#student_no'+id).parent().parent().parent().parent().parent().closest("div").addClass('test');

		$('.test').prev("div").addClass('check');
		var value_student= $(".check").children("div").children("div").children("div").addClass('getChildDivValue');

		var getChildDivValue = $('.getChildDivValue .student_no1').val();
		

		if(student_id == pre_value || student_id == getChildDivValue){
			alert('This User already exits');
			$('#s_name'+id).val('');
			$('#f_name'+id).val('');
			return false;
		}

	

		$.ajax({
		type: "POST",
		url: "transportation/ajax.php", // url to request
		data: { 
		student_no: student_id 
		},
		success : function(data){

			var obj2 = JSON.parse(data);

			$('#s_name'+id).val(obj2.stu_fname);
			$('#f_name'+id).val(obj2.father_lname);

		}
		});

	}



	function batchMultiple(id){

		var batch_id = $('#batch_no'+id).val();
		$('#s_name'+id).val('');
		$('#f_name'+id).val('');
		
		$.ajax({
		type: "POST",
		url: "transportation/ajax.php", // url to request
		data: {batch_no:batch_id},
		success : function(data){

			 helpers.buildDropdown(

                    jQuery.parseJSON(data),

                    $('#student_no'+id),

                    'Select an option'

                );

		}
		});

	}

	var helpers =

{

    buildDropdown: function(result, dropdown, emptyMessage)

    {

        // Remove current options

        dropdown.html('');

        // Add the empty option with the empty message

        dropdown.append('<option value="">' + emptyMessage + '</option>');

        // Check result isnt empty

        if(result != '')

        {

            // Loop through each of the results and append the option to the dropdown

            $.each(result, function(k, v) {

                dropdown.append('<option value="' + v.id + '">' + v.name + '</option>');

            });

        }

    }

}


var helpers2 =

{

    buildDropdown: function(result, dropdown, emptyMessage)

    {

        // Remove current options

        dropdown.html('');

        // Add the empty option with the empty message

        dropdown.append('<option value="">' + emptyMessage + '</option>');

        // Check result isnt empty

        if(result != '')

        {

            // Loop through each of the results and append the option to the dropdown

            $.each(result, function(k, v) {

                dropdown.append('<option value="' + v.id + '">' + v.name + '</option>');

            });

        }

    }

}


$('#stop').change(function(){

	var route_no= $('#route_name').val();
	var stop_no= $('#stop').val();
	var vehicle_no1= $('#vehicle_no').val();
	
	$.ajax({
		type: "POST",
		url: "transportation/ajax.php", // url to request
		data: { 
		route_no: route_no, 
		stop: stop_no,
		vehicleNumber: vehicle_no1 
		},
		success : function(data){
			$('#no_student').val(data)

		}
		});



});

jQuery(function($) {
	$('#stop').on('change', function() {
		var stopVal = $(this).val();
		var routeVal12 = $("#route_name").val();
		var vehicleVal = $("#vehicle_no").val();
		$.ajax({
		type: "POST",
		url: "transportation/ajaxRoute.php", // url to request
		data: { 
		stop: stopVal ,
		routeVal: routeVal12 ,
		vehicle: vehicleVal 
		},
		success : function(data){
			
			if(data!=2){
				
				var url=data;
				var url1 = '<?=$path_to_root?>/transportation/transportation_edit.php?config='+url;
				if (url1) {
					window.location = url1;
				}
				return false;
			}
		   
			
		}

		});

	});
});

</script>