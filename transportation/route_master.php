<?php
$page_security = 'SA_ROUTE';
if (!@$_GET['popup'])
	$path_to_root = "..";
else	
	$path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
//add_access_extensions();
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
	if (strlen($_POST['route_name']) == 0) {
		display_error(_("Route name can not be empty."));
		set_focus('route_name');
		return false;
	}
	if (!empty($_POST['route_name'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['route_name']) != 0) {

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

		if (preg_match($regex, $_POST['source']) != 0) {

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

		if (preg_match($regex, $_POST['destination']) != 0) {

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
				$route_no=$_POST['route_name'];
				$sql2 = "SELECT * FROM ".TB_PREF."transportation_details JOIN ".TB_PREF."transportation ON ".TB_PREF."transportation_details.trans_id=".TB_PREF."transportation.trans_id where fa_transportation.route_name=".db_escape($route_no)." AND ".TB_PREF."transportation_details.status=1";
			    $route_result2=db_query($sql2); 
				$routeVal=db_num_rows($route_result2);

				if($routeVal>0 && $_POST['status']==2){
					display_warning(_("Please deactivate stop first"));
				}else {
					update_route ($selected_id, $_POST['route_id'], $_POST['route_name'], $_POST['source'], $_POST['destination'], $_POST['status']);

					$Mode = 'RESET';
					$update_pager = true;
					display_notification(_('Record has been updated'));
				}
			    
				
				
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
$sql = getRouteList($status);
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
echo '<br>';


//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) {
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
    $sql1 = "SELECT MAX(route_id) FROM fa_route WHERE route_id LIKE 'route-%'";
    $empl_data = db_query($sql1);
    $empid_result = db_fetch_row($empl_data);

    $last_emp1 = substr($empid_result[0], 6);
    $emp_inc_id = $last_emp1 + 1;
    if (strlen($emp_inc_id) == 1) {
        $_POST['route_id'] = 'route-00' . $emp_inc_id;
    } else if (strlen($emp_inc_id) == 2) {
        $_POST['route_id'] = 'route-0' . $emp_inc_id;
    } else {
        $_POST['route_id'] = 'route-' . $emp_inc_id;
    }
}
label_row(_("Route Id"), $_POST['route_id']);

hidden('route_id', $_POST['route_id']);

table_section_title(_("Route Details"));
text_row(_('Route name*'), 'route_name', $_POST['route_name'], 30,50);
text_row(_('Source*'), 'source', $_POST['source'], 30,50);
text_row(_('Destination*'), 'destination', $_POST['destination'], 30,50);
custom_list_row(_("Status"), 'status', null, TRUE, false, 'status');

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();
?>


