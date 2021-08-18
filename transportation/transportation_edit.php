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

$js='';

if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Route Configuration"), @$_REQUEST['popup'], false, "", $js);


simple_page_mode(true);


// $sql1 = "SELECT MAX(trans_id) FROM fa_transportation WHERE trans_id LIKE 'trans-%'";
//     $empl_data = db_query($sql1);
//     $empid_result = db_fetch_row($empl_data);

//     $last_emp1 = substr($empid_result[0], 7);
//     $emp_inc_id = $last_emp1 + 1;
//     if (strlen($emp_inc_id) == 1) {
//         $_POST['trans_id'] = 'trans-00' . $emp_inc_id;
//     } else if (strlen($emp_inc_id) == 2) {
//         $_POST['trans_id'] = 'trans-0' . $emp_inc_id;
//     } else {
//         $_POST['trans_id'] = 'trans-' . $emp_inc_id;
//     }
	
	
	/*Stop Name*/
	$stop_sql ="SELECT * FROM ".TB_PREF."stop";
	$stop_result=db_query($stop_sql);



	$val=get_transportResultDetails($_GET['config']);
	$count_val=get_transportResultDetails2($_GET['config']);


	$r1_sql ="SELECT * FROM ".TB_PREF."transportation
	 where trans_id=".db_escape($_GET['config']);
	$r1_result=db_query($r1_sql);
	$r1_val=db_fetch($r1_result);

	$student_count=NoOfStudentCount($r1_val['route_name'],$r1_val['stop'],$r1_val['vehicle_no']);
?>
<link rel="stylesheet" href="/finesse-erp//transportation/css/bootstrap.min.css">
<div class="panel panel-default">
	<form class="no-margin" id="formValidate1" action="" method="post" onSubmit="return form_validate();">
		<input type="hidden" name="trans_id" value="<?=$r1_val['trans_id']?>">
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Route Name</label>
				    <?php 
					    /*Route Name*/
						$sql ="SELECT * FROM ".TB_PREF."route";
						$route_result=db_query($sql); 
					?>
					<div class="form-group">
				 		<!-- <select id="route_name" class="form-control" name="route_name">
						  <option value="<?=$r1_val['route_name']?>"><?=$r1_val['route_name']?></option>
						  <?php  while ($myrow = db_fetch($route_result)){?>
							<option value="<?=$myrow['route_name']?>"><?=$myrow['route_name']?></option>
						  <?php } ?>
						</select> -->

						<input type="text" name="route_name" id="route_name" class="form-control" value='<?=$r1_val['route_name']?>' readonly="true" />
					</div>
				</div>
				
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Source</label>
					<div class="form-group">
						<input type="text" name="source" id="source" class="form-control" value='<?=$r1_val['source']?>' readonly="true" />
					</div>
				</div>
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Destination</label>
					<div class="form-group">
						<input type="text" name="destination" id="destination" class="form-control" value='<?=$r1_val['destination']?>' readonly="true" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Vehicle No.</label>
				    <?php 
						$sql ="SELECT * FROM ".TB_PREF."vehicle";
						$vehicle_result=db_query($sql); 
					?>
					<div class="form-group">
						<input type="text" name="vehicle_no" id="vehicle_no" class="form-control" value='<?=$r1_val['vehicle_no']?>' readonly="true" />
						<!-- <select id="vehicle_no" class="form-control" name="vehicle_no">
						  <option value="<?=$r1_val['vehicle_no']?>"><?=$r1_val['vehicle_no']?></option>
						  <?php  while ($myrow = db_fetch($vehicle_result)){?>
							<option value="<?=$myrow['vehicle_no']?>"><?=$myrow['vehicle_no']?></option>
						  <?php } ?>
						</select> -->
					</div>
				</div>
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Reg. No.</label>
					<div class="form-group">
						<input type="text" name="reg_no" id="reg_no" class="form-control" value="<?=$r1_val['reg_no']?>" readonly="true" />
					</div>
				</div>
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Driver Name</label>
					<div class="form-group">
						<input type="text" name="driver_name" id="driver_name" class="form-control" value="<?=$r1_val['driver_name']?>" readonly="true" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Stop </label>
				    <?php 

    					$route_name=$r1_val['route_name'];
						$stopNameSql ="SELECT stop_name FROM ".TB_PREF."stop where id=".db_escape($r1_val['stop']);
				        $stopNameResult=db_query($stopNameSql);
				        $stopName=db_fetch($stopNameResult);
									
						$sql ="SELECT s_name,stop_name FROM ".TB_PREF."routeconfig_detail JOIN ".TB_PREF."route_config ON ".TB_PREF."route_config.config_id=".TB_PREF."routeconfig_detail.config_id JOIN ".TB_PREF."stop ON ".TB_PREF."stop.id=".TB_PREF."routeconfig_detail.s_name where fa_route_config.route_name=".db_escape($route_name);
						$stop_result=db_query($sql); 


					?>
					<div class="form-group">
						<input type="text" name="stop1" id="stop1" class="form-control" value='<?=$stopName[0]?>' readonly="true" />
						<input type="hidden" name="stop" id="stop" class="form-control" value='<?=$r1_val['stop']?>' readonly="true" />
						<!-- <select id="stop" class="form-control" name="stop">
						 <option value="<?=$r1_val['stop']?>"><?=$stopName[0]?></option>
						  <?php  while ($myrow = db_fetch($stop_result)){?>
							<option value="<?=$myrow['s_name']?>"><?=$myrow['stop_name']?></option>
						  <?php } ?>
						</select> -->
					</div>
				</div>
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Sequence </label>
					<div class="form-group">
						<input type="text" name="sequence" id="sequencevalue" class="form-control" value="<?=$r1_val['sequence']?>" readonly="true" />
					</div>
				</div>
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Total Students</label>
					<div class="form-group">
						<input type="text" name="no_student" id="no_student" class="form-control" value="<?=$student_count?>" readonly="true" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Expected Pickup Time</label>
					<div class="form-group">
						<input type="time" name="exp_time" id="exp_time" class="form-control" value="<?=$r1_val['exp_time']?>" readonly="true" />
					</div>
				</div>
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Expected Drop Time</label>
					<div class="form-group">
						<input type="time" name="drop_time" id="drop_time" class="form-control" value="<?=$r1_val['drop_time']?>" readonly="true" />
					</div>
				</div>
			</div>
			<?php if($count_val > 0) {  ?>
			<input type="hidden" name="count_val" id="count_val" value="1" class="count_val">
			<div style="border:2px solid #ccc; padding:24px;margin-top:25px;">
				<?php 
				$i = 0;
			while ($myrow = db_fetch($val)) { 
			?>
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group">
							<label class="">Batch Code</label>
							 <?php 
							    set_global_connection(2);
								$batchsql ="SELECT * FROM academic_master";
								$batch_result=db_query($batchsql);


								///Batch Name 
								$batchNameSql ="SELECT batch_code,academic_year_id FROM academic_master where academic_year_id =". db_escape($myrow['batch_no']);
								$batchNameresult=db_query($batchNameSql); 
								$batchCodeVal=db_fetch($batchNameresult);
								

								$mynewId=$myrow['trans_id'];
								$newId=explode('-',$mynewId);
								$newId2=$myrow[0]+55;
								$newId2;
							?>
							<div class="form-group">
								<select id="batch_no<?=$newId2?>" class="form-control batch_no" name="batch_no[]" onchange="batchMultiple(<?=$newId2?>)">
								  <option value="<?=$batchCodeVal[1];?>"><?=$batchCodeVal[0];?></option>
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
								$sql ="SELECT * FROM erp_student_information";
								$student_result=db_query($sql); 
								
							?>
							<div class="form-group">
								<select id="student_no<?=$newId2?>" class="form-control student_no" name="student_no[]" onchange="check(<?=$newId2?>)">
								  <option value="<?=$myrow['student_no']?>"><?=$myrow['student_no']?></option>
								<!--   <?php  while ($student_row = db_fetch($student_result)){?>
									<option value="<?=$student_row['stu_id']?>"><?=$student_row['stu_id']?></option>
								  <?php } ?> -->
								</select>
							</div>
						</div>
					</div>
					<div class="col-sm-2"><div class="form-group"><label class="">Name</label><input type="text" name="name[]" id="s_name<?=$newId2?>" value="<?=$myrow['name']?>" class="form-control" / readonly="true"></div></div>
				
					<div class="col-sm-2"><div class="form-group"><label class="">Father's Name</label><input type="text" name="f_name[]" id="f_name<?=$newId2?>" value="<?=$myrow['f_name']?>" class="form-control" / readonly="true"></div></div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="">Status</label>
							<select id="status<?=$newId2?>" class="form-control" name="status[]">
								<?php
									if($myrow['status']==1){
										$status='Active';
									}else{
										$status='Deactive';
									}
								?>
								<option value="<?=$myrow['status']?>"><?=$status?></option>
								<option value="1">Active</option>
								<option value="2">Deactive</option>
							</select>
						</div>
					</div>
				</div>
				<?php $i++; } 
				?>
			</div>
			<?php } ?>
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
	$nostudent = $_POST['no_student'];
	$exp_time = $_POST['exp_time'];
	$drop_time = $_POST['drop_time'];
	$student_noArr = $_POST['student_no'];
	$batch_noArr = $_POST['batch_no'];
	$nameArr = $_POST['name'];
	$f_nameArr = $_POST['f_name'];
	$statusArr =$_POST['status'];
	if(isset($_POST['submit'])){
		delete_transportantionDetail($trans_id);
		update_transportation($trans_id, $route_name,$source,$destination,$vehicle_no,$reg_no,$driver_name,$stop,$sequence,$nostudent,$exp_time,$drop_time);
		for($i=0; $i<count($student_noArr); $i++ ){
			$student_no=$student_noArr[$i];
			$name=$nameArr[$i];
			$batch_no=$batch_noArr[$i];
			$f_name=$f_nameArr[$i];
			$status=$statusArr[$i];
			//update_transportationDetail($trans_id,$student_no, $name, $f_name,$status);
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
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Name</label><input type="text" name="name[]" id="s_name'+count_value+'" class="form-control" readonly="true" /></div></div>';
			
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Father&#39;s Name</label><input type="text" name="f_name[]" id="f_name'+count_value+'" class="form-control" readonly="true" /></div></div>';
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Status</label><select id="status" class="form-control" name="status[]"><option value="1">Active</option><option value="2">Deactive</option></select></div></div>';
			
		append_html += '<a href="#" class="removeclass btn btn-primary remove_class pull-right">-</a>';
		append_html += '</div></div>';	
		$('#fields').append(append_html);
		
		});
		$("body").on("click",".removeclass", function(e){

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
       // alert($("#stop").val());
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
			//alert(student_no[i].value);
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
		url: "/finesse-erp/transportation/ajax.php", // url to request
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


	$('#vehicle_no').change(function(){
		var vehicle = $(this).val();
		$.ajax({
		type: "POST",
		url: "/finesse-erp/transportation/ajax.php", // url to request
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
		url: "/finesse-erp/transportation/ajax.php", // url to request
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

	// $('.student_no').change(function(){

	// 	var student_id = $(this).val();
	// 	$.ajax({
	// 	type: "POST",
	// 	url: "/finesse-erp/transportation/ajax.php", // url to request
	// 	data: { 
	// 	student_no: student_id 
	// 	},
	// 	success : function(data){

	// 		var obj = JSON.parse(data);
	// 		$('#s_name').val(obj.stu_fname);
	// 		$('#f_name').val(obj.father_lname);

	// 	}
	// 	});
	// });

	// $('.student_no1').change(function(){
	// 	alert('test');
	// 	// var student_id = $(this).val();
	// 	// $.ajax({
	// 	// type: "POST",
	// 	// url: "/finesse-erp/transportation/ajax.php", // url to request
	// 	// data: { 
	// 	// student_no: student_id 
	// 	// },
	// 	// success : function(data){

	// 	// 	var obj = JSON.parse(data);
	// 	// 	$('#s_name').val(obj.stu_fname);
	// 	// 	$('#f_name').val(obj.father_lname);

	// 	// }
	// 	// });
	// });

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
		url: "/finesse-erp/transportation/ajax.php", // url to request
		data: { 
		student_no: student_id 
		},
		success : function(data){

			var obj = JSON.parse(data);
			$('#s_name'+id).val(obj.stu_fname);
			$('#f_name'+id).val(obj.father_lname);

		}
		});

	}


	function batchMultiple(id){

		var batch_id = $('#batch_no'+id).val();
		$('#s_name'+id).val('');
		$('#f_name'+id).val('');
		$.ajax({
		type: "POST",
		url: "/finesse-erp/transportation/ajax.php", // url to request
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

</script>