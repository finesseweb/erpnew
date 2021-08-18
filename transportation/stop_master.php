<?php
$page_security = 'SA_STOP';
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
//if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Stop Master"), @$_REQUEST['popup'], false, "", $js);


simple_page_mode(true);
?>
<?php

function can_process() {
	
	$regexno = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";
	if (strlen($_POST['stop_name']) == 0) {
		display_error(_("Stop name can not be empty."));
		set_focus('stop_name');
		return false;
	}
	if (!empty($_POST['stop_name'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match('/\s/', $_POST['stop_name']) != 0) {

			display_error(_("Stop name can not be empty ."));
			set_focus('stop_name');
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
				add_stop($_POST['stop_id'], $_POST['stop_name'], $_POST['status']);
				
				display_notification(_('New Record has been added'));
				$Mode = 'RESET';
				$update_pager = true;
		} else {
			if ($_POST['stop_id']) {
				$result = '';
				$stop=$_POST['hidden_id'];
				$sql2 = "SELECT * FROM ".TB_PREF."transportation_details JOIN ".TB_PREF."transportation ON ".TB_PREF."transportation_details.trans_id=".TB_PREF."transportation.trans_id where fa_transportation.stop=".db_escape($stop)." AND ".TB_PREF."transportation_details.status=1";
			    $route_result2=db_query($sql2); 
				$routeVal=db_num_rows($route_result2);

				if($routeVal>0 && $_POST['status']==2){
					display_warning(_("Please deactivate student first"));
				}else {
					update_stop($selected_id, $_POST['stop_id'], $_POST['stop_name'], $_POST['status']);
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
if(!isset($_POST['stop_page_next']) && !isset($_POST['stop_page_last']))
$_SESSION['fun']['id'] = 0;
start_form(true);
$sql = getStopList($status);
$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("Stop ID") => array('align' => 'center'),
            _("Stop Name") => array('align' => 'center'),
            _("Status") => array('align' => 'center','fun' => 'status'),
            _("Edit") => array('align' => 'center', 'fun' => 'edit'),
);
$table = &new_db_pager('stop', $sql, $cols);

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
        $myrow = get_stop_list($selected_id);
        $_POST['stop_id'] = $myrow["stop_id"];
        $_POST['stop_name'] = $myrow["stop_name"];
        $_POST['status'] = $myrow["status"];
        $_POST['id'] = $myrow["id"];
        
    }
    hidden('selected_id', $selected_id);
    hidden('hidden_id', $_POST['id']);
}


table_section_title(_("Stop Master"));


if ($Mode != 'Edit') {
    $sql1 = "SELECT MAX(stop_id) FROM fa_stop WHERE stop_id LIKE 'stop-%'";
    $empl_data = db_query($sql1);
    $empid_result = db_fetch_row($empl_data);

    $last_emp1 = substr($empid_result[0], 6);
    $emp_inc_id = $last_emp1 + 1;
    if (strlen($emp_inc_id) == 1) {
        $_POST['stop_id'] = 'stop-00' . $emp_inc_id;
    } else if (strlen($emp_inc_id) == 2) {
        $_POST['stop_id'] = 'stop-0' . $emp_inc_id;
    } else {
        $_POST['stop_id'] = 'stop-' . $emp_inc_id;
    }
}
label_row(_("Stop Id"), $_POST['stop_id']);

hidden('stop_id', $_POST['stop_id']);

table_section_title(_("Stop Details"));
text_row(_('Stop name*'), 'stop_name', $_POST['stop_name'], 30,50);
custom_list_row(_("Status"), 'status', null, TRUE, false, 'status');

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------



end_page();
?>


<script type="text/javascript">
	$( "#_status_sel .combo" ).change(function() {
	  alert( "Handler for .change() called." );
	});
</script>