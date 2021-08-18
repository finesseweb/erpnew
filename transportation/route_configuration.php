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


//if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Route Configuration"), @$_REQUEST['popup'], false, "", $js);


simple_page_mode(true);


$sql1 = "SELECT MAX(config_id) FROM fa_route_config WHERE config_id LIKE 'config-%'";
    $empl_data = db_query($sql1);
    $empid_result = db_fetch_row($empl_data);

    $last_emp1 = substr($empid_result[0], 7);
    $emp_inc_id = $last_emp1 + 1;
    if (strlen($emp_inc_id) == 1) {
        $_POST['config_id'] = 'config-00' . $emp_inc_id;
    } else if (strlen($emp_inc_id) == 2) {
        $_POST['config_id'] = 'config-0' . $emp_inc_id;
    } else {
        $_POST['config_id'] = 'config-' . $emp_inc_id;
    }
	
	
	/*Route Name*/
	$sql ="SELECT * FROM ".TB_PREF."route WHERE status=1";
	$route_result=db_query($sql);
	
	/*Stop Name*/
	$stop_sql ="SELECT * FROM ".TB_PREF."stop WHERE status=1";
	$stop_result=db_query($stop_sql);
	
	

?>
<link rel="stylesheet" href="css/bootstrap.min.css">
<div class="panel panel-default">
	<form class="no-margin" id="formValidate1" action="" method="post" onSubmit="return form_validate();">
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Route-Config ID</label>
					<div class="form-group">
						<input type="text" id="config_id" class="form-control" name="config_id" value="<?=$_POST['config_id']?>" readonly/>
					</div>
				</div>
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Route Name</label>
					<div class="form-group">
						<select id="route_name" class="form-control" name="route_name">
						  <option value="">Select</option>
						  <?php  while ($myrow = db_fetch($route_result)){?>
							<option value="<?=$myrow['route_name']?>"><?=$myrow['route_name']?></option>
						  <?php } ?>
						</select>
					</div>
				</div>
			</div>
			<input type="hidden" name="count_val" id="count_val" value="1" class="count_val">
			<div style="border:2px solid #ccc; padding:24px;margin-top:25px;">
				<div class="row">
					<div class="col-sm-2">
					<div class="form-group">
						<label class="">Stop Name</label>
						<div class="form-group">
							<select id="stop_name" class="form-control" name="stop_name[]">
							  <option value="">Select</option>
							  <?php  while ($stop_row = db_fetch($stop_result)){?>
								<option value="<?=$stop_row['id']?>"><?=$stop_row['stop_name']?></option>
							  <?php } ?>
							</select>
						</div>
					</div>
					</div>
					<div class="col-sm-2"><div class="form-group"><label class="">Sequence</label><input type="text" name="sequence[]" id="sequence" class="form-control" /></div></div>
				
					<div class="col-sm-2"><div class="form-group"><label class="">Expected Pickup Time</label><input type="time" name="exp_time[]" id="exp_time" class="form-control" /></div></div>
					<div class="col-sm-2"><div class="form-group"><label class="">Drop Time</label><input type="time" name="drop_time[]" id="drop_time" class="form-control" /></div></div>
				
					<div class="col-sm-2"><div class="form-group"><label class="">Cost(Rs.)</label><input type="text" name="cost[]" id="cost" class="form-control" /></div></div>
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
	$stop_sql ="SELECT * FROM ".TB_PREF."stop WHERE status=1";
	$stop_result=db_query($stop_sql);
	
	
	$config_id = trim($_POST['config_id']);
	$route_name = trim($_POST['route_name']);
	$stop_nameArr =$_POST['stop_name'];
	$sequenceArr = $_POST['sequence'];
	$exp_timeArr = $_POST['exp_time'];
	$drop_timeArr = $_POST['drop_time'];
	$costArr = $_POST['cost'];
	$statusArr =$_POST['status'];
	if(isset($_POST['submit'])){
		add_routeConfigval($config_id, $route_name);
		for($i=0; $i<count($stop_nameArr); $i++ ){
			$stop_name=$stop_nameArr[$i];
			$sequence=$sequenceArr[$i];
			$exp_time=$exp_timeArr[$i];
			$drop_time=$drop_timeArr[$i];
			$cost=$costArr[$i];
			$status=$statusArr[$i];
			add_routeConfigDetail($config_id,$stop_name, $sequence, $exp_time, $drop_time,$cost,$status);
		}
    }


end_page();
?>

<script>
var count = $('#count_val').val(); //Addmore script code

		$("body").on("click","#AddButton",function(event)
		{
		var count_value = count++;
		append_html = '<div style="border:2px solid #ccc; padding:24px;margin-top:25px;"><div class="row">';
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Stop Name</label><div class="form-group"><select id="stop_name" class="form-control" name="stop_name[]"><option value="">Select</option><?php  while ($stop_row = db_fetch($stop_result)){?><option value="<?=$stop_row['id']?>"><?=$stop_row['stop_name']?></option><?php } ?></select></div></div></div>';
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Sequence</label><input type="text" name="sequence[]" id="sequence" class="form-control" /></div></div>';
			
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Expected Pickup Time</label><input type="time" name="exp_time[]" id="exp_time" class="form-control" /></div></div>';
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Drop Time</label><input type="time" name="drop_time[]" id="drop_time" class="form-control" /></div></div>';
			
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Cost(Rs.)</label><input type="text" name="cost[]" id="cost" class="form-control" /></div></div>';
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
	
	if ($.trim($("#config_id").val()) === "" || $.trim($("#config_id").val()) === "") {
       // alert('t1!');
		$("#config_id").css('border','2px solid red');
        return false;
    }else{
		$("#config_id").css('border','2px solid green');
	}
	if ($.trim($("#route_name").val()) === "" || $.trim($("#route_name").val()) === "") {
       // alert('t2');
		$("#route_name").css('border','2px solid red');
        return false;
    }else{
		$("#route_name").css('border','2px solid green');
	}
	
	//dynamic field validation
	var f_stop_name = document.getElementsByName('stop_name[]');
	var f_sequence = document.getElementsByName('sequence[]');
	var f_exp_time = document.getElementsByName('exp_time[]');
	var f_drop_time = document.getElementsByName('drop_time[]');
	var f_cost = document.getElementsByName('cost[]');
	var f_status = document.getElementsByName('status[]');
	
	for (var i = 0; i < f_stop_name.length; i++)
    {
		if ($.trim(f_stop_name[i].value) == "")
		{
			f_stop_name[i].style.border = '2px solid red';
			f_stop_name[i].focus();
			return false;            
		}else{
			f_stop_name[i].style.border = '2px solid green';
		}
		
		if (f_sequence[i].value == "" || !f_sequence[i].value.match(/^[0-9]+$/))
		{
			f_sequence[i].style.border = '2px solid red';
			f_sequence[i].focus();
			return false;            
		}else{
			f_sequence[i].style.border = '2px solid green';
		}
		
		
		if (f_exp_time[i].value == "")
		{
			f_exp_time[i].style.border = '2px solid red';
			f_exp_time[i].focus();
			return false;            
		}else{
			f_exp_time[i].style.border = '2px solid green';
		}
		if (f_drop_time[i].value == "")
		{
			f_drop_time[i].style.border = '2px solid red';
			f_drop_time[i].focus();
			return false;            
		}else{
			f_drop_time[i].style.border = '2px solid green';
		}
		if(f_drop_time[i].value < f_exp_time[i].value){
			//alert('Your date is greater than your bill departure date!!');
			f_drop_time[i].style.border = '2px solid red';
			f_drop_time[i].focus();
			return false; 
		}

		if (f_cost[i].value == "" || !f_cost[i].value.match(/^[0-9]+$/))
		{
			f_cost[i].style.border = '2px solid red';
			f_cost[i].focus();
			return false;            
		}else{
			f_cost[i].style.border = '2px solid green';
		}
		
	}
	return true;
}

jQuery(function($) {
	$('#route_name').on('change', function() {
		var routeValues = $(this).val();
		$.ajax({
		type: "POST",
		url: "/finesse-erp/transportation/ajaxRoute.php", // url to request
		data: { 
		route: routeValues 
		},
		success : function(data){
			
			if(data!=2){
				
				var url=data;
				var url1 = '<?=$path_to_root?>/transportation/route_configurationdetails.php?config='+url;
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
