<?php
$page_security = 'SA_VEHICLECONFIG';
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

page(_($help_context = "Route Master"), @$_REQUEST['popup'], false, "", $js);


simple_page_mode(true);
?>
<?php

function can_process() {
	
	$regexno = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";
    
    if (strlen($_POST['vehicle_no']) == 0) {
        display_error(_("Vehicle No can not be empty."));
        set_focus('vehicle_no');
        return false;
    }
    if (!empty($_POST['title'])) {
        $regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

        if (preg_match($regex, $_POST['vehicle_no']) != 0) {

            display_error(_("Special characters not allowed in vehicle no ."));
            set_focus('vehicle_no');
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
				add_vehicle($_POST['vehicle_id'], $_POST['vehicle_no'], $_POST['reg_no'], $_POST['seating_capacity'], $_POST['driver_name'], $_POST['status']);
				
				display_notification(_('New Record has been added'));
				$Mode = 'RESET';
				$update_pager = true;
		} else {
			if ($_POST['vehicle_id']) {
				$result = '';

                $vehicle_no=$_POST['vehicle_no'];

                $sql2 = "SELECT * FROM ".TB_PREF."transportation_details JOIN ".TB_PREF."transportation ON ".TB_PREF."transportation_details.trans_id=".TB_PREF."transportation.trans_id where fa_transportation.vehicle_no=".db_escape($vehicle_no)." AND ".TB_PREF."transportation_details.status=1";
                $route_result2=db_query($sql2); 
                $routeVal=db_num_rows($route_result2);

                if($routeVal>0 && $_POST['status']==2){
                    display_warning(_("Please deactivate student first"));
                }else {
                   update_vehicle($selected_id, $_POST['vehicle_id'], $_POST['vehicle_no'], $_POST['reg_no'], $_POST['seating_capacity'], $_POST['driver_name'], $_POST['status']);
                }

			    
				
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
$sql = getVehicleList($status);
$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("Vehicle ID") => array('align' => 'center'),
            _("Vehicle No.") => array('align' => 'center'),
            _("Reg. No.") => array('align' => 'center','fun'=>'reg_no'),
            _("Seating Capacity") => array('align' => 'center'),
            _("Driver Name") => array('align' => 'center'),
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

function reg_no($row) {

$sqlVehicle = "SELECT ModelYear FROM tblvehicles WHERE id =".db_escape($row['reg_no']);
$Vehicledata = db_query($sqlVehicle);
$Vehicleresult = db_fetch_row($Vehicledata);

return $Vehicleresult[0];
   
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
echo '<br>';


//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) {
    if ($Mode == 'Edit') {
        $myrow = get_vehicle_list($selected_id);
        $_POST['vehicle_id'] = $myrow["vehicle_id"];
        $_POST['vehicle_no'] = $myrow["vehicle_no"];
        $_POST['reg_no'] = $myrow["reg_no"];
        $_POST['seating_capacity'] = $myrow["seating_capacity"];
        $_POST['driver_name'] = $myrow["driver_name"];
        $_POST['status'] = $myrow["status"];
        
    }
    hidden('selected_id', $selected_id);
}


table_section_title(_("Vehicle Master"));


if ($Mode != 'Edit') {
    $sql1 = "SELECT MAX(vehicle_id) FROM fa_vehicle WHERE vehicle_id LIKE 'vehicle-%'";
    $empl_data = db_query($sql1);
    $empid_result = db_fetch_row($empl_data);

    $last_emp1 = substr($empid_result[0], 8);
    $emp_inc_id = $last_emp1 + 1;
    if (strlen($emp_inc_id) == 1) {
        $_POST['vehicle_id'] = 'vehicle-00' . $emp_inc_id;
    } else if (strlen($emp_inc_id) == 2) {
        $_POST['vehicle_id'] = 'vehicle-0' . $emp_inc_id;
    } else {
        $_POST['vehicle_id'] = 'vehicle-' . $emp_inc_id;
    }
}
label_row(_("Vehicle Id"), $_POST['vehicle_id']);

hidden('vehicle_id', $_POST['vehicle_id']);

table_section_title(_("Vehicle Details"));
text_row(_('Vehicle No.*'), 'vehicle_no', $_POST['vehicle_no'], 30,50);
vehicle_list_cells(_("Reg. No.:"), 'reg_no', $_POST['reg_no'], false, true);


$sqlVehicle = "SELECT SeatingCapacity,DriverId FROM tblvehicles WHERE id =".db_escape($_POST['reg_no']);
$Vehicledata = db_query($sqlVehicle);
$Vehicleresult = db_fetch_row($Vehicledata);

$Ajax->activate('seating_capacity');
$Ajax->activate('driver_name');
text_vehicle(_('Seating Capacity*'), 'seating_capacity', $Vehicleresult[0], 30,50);

$sqldriver = "SELECT name FROM driver_details WHERE id =".db_escape($_POST['reg_no']);
$driverdata = db_query($sqldriver);
$driverresult = db_fetch_row($driverdata);
text_driver(_('Driver Name*'), 'driver_name', $driverresult[0], 30,50);
custom_list_row(_("Status"), 'status', null, TRUE, false, 'status');

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();
?>


