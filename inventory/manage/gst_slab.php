<?php
$page_security = 'SA_ITEM';
$path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
//include_once($path_to_root . "/library/function.php");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
if (!@$_GET['popup'])
	page(_($help_context = "Store"));
simple_page_mode(true);
?>

<?php

function can_process() {
	
	$regexno = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";
	
	
    if (strlen($_POST['store_desc']) == 0) {
        display_error(_("store Description can not be empty."));
        set_focus('store_desc');
        return false;
    }
   
    return true;
}

//-----------------------------------------------------------------------------------  

if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') {
	//if(can_process()){
		$update_pager = false;
		if ($Mode == 'ADD_ITEM') {
			$result = '';
            add_slab($_POST['gst'], $_POST['gst_desc'],$_POST['status']);
            
            display_notification(_('New Record  has been added'));
            $Mode = 'RESET';
            $update_pager = true;
		} else {
			$result = '';
		    update_slab($selected_id, $_POST['gst'],$_POST['gst_desc'],$_POST['status']);

			$Mode = 'RESET';
			$update_pager = true;
			display_notification(_('Record has been updated'));
		}


		$Ajax->activate('details');
	//}
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
    delete_guest($selected_id);
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

start_form(true);
$sql = getSlabInfo($status);
$_SESSION['fun']['id'] = 0;
$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
          //  _("Location.") => array('align' => 'center','fun'=>'locationName'),
            _("GST Slab") => array('align' => 'center'),
            _("Description") => array('align' => 'center'),
            _("Edit") => array('align' => 'center', 'fun' => 'edit'),
);
$table = &new_db_pager('book_location', $sql, $cols);

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

function edit($row) {
    return edit_button_cell("Edit" . $row['slab_id'], _("Edit"));
}

function reciept($row) {
    return $row['dispatched_reciept_number_if_any'];
}
function locationName($row) {
	$loc=$row['location'];
	$sql1 = "SELECT location_name FROM fa_locations WHERE loc_code='$loc'";
    $user_data = db_query($sql1);
    $user_result = db_fetch_row($user_data);
	
	return  $user_result[0];
}
end_table();
echo '<br>';


//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) {
  // display_error($selected_id);

    if ($Mode == 'Edit') {

        $myrow = get_taxsalb_id($selected_id);
        $_POST['gst'] = $myrow["tax_rate"];
        $_POST['gst_desc'] = $myrow["tax_description"];
        $_POST['status'] = $myrow["status"];
    }
    hidden('selected_id', $selected_id);
}


//locations_list_cells(_("Location:"), 'location', null, true);
room_no_row_ex(_("GST*"), 'gst', 30, null, null, null, null, null, FALSE);
room_no_row_ex(_("GST Description *"), 'gst_desc', 30, null, null, null, null, null, FALSE);
yesno_list_cells(_("Active *"), 'status', 30, null, null, null, null, null, FALSE);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------



end_page();
?>


