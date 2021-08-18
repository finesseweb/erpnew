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
	$sql ="SELECT * FROM ".TB_PREF."route";
	$route_result=db_query($sql);
	
	/*Stop Name*/
	$stop_sql ="SELECT * FROM ".TB_PREF."stop";
	$stop_result=db_query($stop_sql);
	
	

?>
<link rel="stylesheet" href="/finesse-erp//transportation/css/bootstrap.min.css">
<div class="panel panel-default">
	<form class="no-margin" id="formValidate1" action="" method="post" data-validate="parsley" >
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Route-Config ID</label>
					<div class="form-group">
						<input type="text" id="batch_name" class="form-control" name="config_id" value="<?=$_POST['config_id']?>" readonly/>
					</div>
				</div>
				<div class="col-sm-3" style="margin-top:25px;">
				    <label class="">Route Name</label>
					<div class="form-group">
						<select id="route_name" class="form-control" name="route_name">
						  <option value="">Select</option>
						  <?php  while ($myrow = db_fetch($route_result)){?>
							<option value="<?=$myrow['id']?>"><?=$myrow['route_name']?></option>
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
							<select id="stop_name" class="form-control" name="grade[stop_name][]">
							  <option value="">Select</option>
							  <?php  while ($stop_row = db_fetch($stop_result)){?>
								<option value="<?=$stop_row['id']?>"><?=$stop_row['stop_name']?></option>
							  <?php } ?>
							</select>
						</div>
					</div>
					</div>
					<div class="col-sm-2"><div class="form-group"><label class="">Sequence</label><input type="text" name="grade[sequence][]" id="sequence" class="form-control" /></div></div>
				
					<div class="col-sm-2"><div class="form-group"><label class="">Expected Pickup Time</label><input type="text" name="grade[exp_time][]" id="exp_time" class="form-control" /></div></div>
					<div class="col-sm-2"><div class="form-group"><label class="">Drop Time</label><input type="text" name="grade[drop_time][]" id="drop_time" class="form-control" /></div></div>
				
					<div class="col-sm-2"><div class="form-group"><label class="">Cost</label><input type="text" name="grade[cost][]" id="cost" class="form-control" /></div></div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="">Status</label>
							<select id="status" class="form-control" name="grade[status][]">
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
if($_POST['submit']){
	echo '<pre>';
	print_r($_POST);
}


/*Stop Name*/
	$stop_sql ="SELECT * FROM ".TB_PREF."stop";
	$stop_result=db_query($stop_sql);
	
	
	
/*function can_process() {
	
	$regexno = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";
	if (strlen($_POST['route_name']) == 0) {
		display_error(_("Route name can not be empty."));
		set_focus('route_name');
		return false;
	}
	if (!empty($_POST['route_name'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match('/\s/', $_POST['route_name']) != 0) {

			display_error(_("Special characters not allowed in route name ."));
			set_focus('route_name');
			return false;
		}
	}
	if (strlen($_POST['source']) == 0) {
		display_error(_("Source can not be empty."));
		set_focus('source');
		return false;
	}
	if (!empty($_POST['source'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match('/\s/', $_POST['source']) != 0) {

			display_error(_("Special characters not allowed in source ."));
			set_focus('source');
			return false;
		}
	}
	if (strlen($_POST['destination']) == 0) {
		display_error(_("Destination can not be empty."));
		set_focus('destination');
		return false;
	}
	if (!empty($_POST['destination'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match('/\s/', $_POST['destination']) != 0) {

			display_error(_("Special characters not allowed in destination ."));
			set_focus('destination');
			return false;
		}
	}
	
    return true;
}

//-----------------------------------------------------------------------------------

if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') {
	if(can_process()){
		$update_pager = false;
		if ($Mode == 'ADD_ITEM') {
			$result = '';
				add_route($_POST['route_id'], $_POST['route_name'], $_POST['source'], $_POST['destination'], $_POST['status']);
				
				display_notification(_('New Record has been added'));
				$Mode = 'RESET';
				$update_pager = true;
		} else {
			if ($_POST['route_id']) {
				$result = '';
			    update_route ($selected_id, $_POST['route_id'], $_POST['route_name'], $_POST['source'], $_POST['destination'], $_POST['status']);
				
				$Mode = 'RESET';
				$update_pager = true;
				display_notification(_('Record has been updated'));
			} else {
				display_warning(_("Duplicate isuue no"));
			}
		}


		$Ajax->activate('details');
	}
}
function can_delete($selected_id) {
    if (key_in_foreign_table($selected_id, 'debtors_master', 'credit_status')) {
        display_error(_("Cannot delete this credit status because customer accounts have been created referring to it."));
        return false;
    }

    return true;
}

//-----------------------------------------------------------------------------------

if ($Mode == 'Delete') {

    //if (can_delete($selected_id))
    //{
    delete_guest($selected_id);
    //display_notification(_(' '));
    //}
    $Mode = 'RESET';
}

if ($Mode == 'RESET') {
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    $_POST['show_inactive'] = $sav;
}
//-----------------------------------------------------------------------------------
//===[Working]=====//
if(!isset($_POST['route_page_next']) && !isset($_POST['route_page_last']))
$_SESSION['fun']['id'] = 0;
start_form(true);
$sql = getRouteList();
$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("Route ID") => array('align' => 'center'),
            _("Route Name") => array('align' => 'center'),
            _("Source") => array('align' => 'center'),
            _("Destination") => array('align' => 'center'),
            _("Status") => array('align' => 'center','fun' => 'status'),
            _("Edit") => array('align' => 'center', 'fun' => 'edit'),
);
$table = &new_db_pager('route', $sql, $cols);

$table->width = "80%";
display_db_pager($table);
start_table(TABLESTYLE, "width=40%");
inactive_control_column($th);
table_header($th);

$k = 0;

$nos = db_num_rows($result);

function id($row) {
    
    return ++$_SESSION['fun']['id'] ;
}

function status($row) {
	if($row['status']==1){
		$status='Active';
	}elseif($row['status']==2){
		$status='Deactive';
	}
    return $status;
}

function edit($row) {
    return edit_button_cell("Edit" . $row['id'], _("Edit"));
}


//inactive_control_row($th);
end_table();
echo '<br>';*/


//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

/*if ($selected_id != -1) {
    if ($Mode == 'Edit') {
        $myrow = get_route_list($selected_id);
        $_POST['route_id'] = $myrow["route_id"];
        $_POST['route_name'] = $myrow["route_name"];
        $_POST['source'] = $myrow["source"];
        $_POST['destination'] = $myrow["destination"];
        $_POST['status'] = $myrow["status"];
        
    }
    hidden('selected_id', $selected_id);
}


table_section_title(_("Route Master"));


if ($Mode != 'Edit') {
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
}
label_row(_("Route Config Code"), $_POST['config_id']);

hidden('config_id', $_POST['config_id']);

table_section_title(_("Configuration Details"));
route_rowNew(_("Route Name:"), 'route_name', null, false, true);
stop_rowNew(_("Stop Name:"), 'stop_name', null, false, true);
text_row(_('Sequence*'), '
', $_POST['sequence'], 30);
text_row(_('Destination*'), 'destination', $_POST['destination'], 30);
custom_list_row(_("Status"), 'status', null, TRUE, false, 'status');

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');*/

end_form();

//------------------------------------------------------------------------------------

end_page();
?>

<script>
var count = $('#count_val').val(); //Addmore script code

		$("body").on("click","#AddButton",function(event)
		{
		var count_value = count++;
		append_html = '<div style="border:2px solid #ccc; padding:24px;margin-top:25px;"><div class="row">';
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Stop Name</label><div class="form-group"><select id="stop_name" class="form-control" name="grade[stop_name][]"><option value="">Select</option><?php  while ($stop_row = db_fetch($stop_result)){?><option value="<?=$stop_row['id']?>"><?=$stop_row['stop_name']?></option><?php } ?></select></div></div></div>';
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Sequence</label><input type="text" name="grade[sequence][]" id="sequence" class="form-control" /></div></div>';
			
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Expected Pickup Time</label><input type="text" name="grade[exp_time][]" id="exp_time" class="form-control" /></div></div>';
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Drop Time</label><input type="text" name="grade[drop_time][]" id="drop_time" class="form-control" /></div></div>';
			
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Cost</label><input type="text" name="grade[cost][]" id="cost" class="form-control" /></div></div>';
		append_html += '<div class="col-sm-2"><div class="form-group"><label class="">Status</label><select id="status" class="form-control" name="grade[status][]"><option value="1">Active</option><option value="2">Deactive</option></select></div></div>';
			
		append_html += '<a href="#" class="removeclass btn btn-primary remove_class pull-right">-</a>';
		append_html += '</div></div>';	
		$('#fields').append(append_html);
		
		});
		$("body").on("click",".removeclass", function(e){
		//alert('sdsd');

                $(this).parent('div').parent('div').remove(); 				
				
        return false;

    }); 
	
</script>
