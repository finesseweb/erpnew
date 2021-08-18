<?php
$page_security = 'SA_ADDFLOOR';
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
include_once($path_to_root . "/library/function/function.php");

include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
if (!@$_GET['popup'])
	page(_($help_context = "Assign Book Location"));
simple_page_mode(true);
?>

<?php

function can_process() {
	
	$regexno = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";
	
	
    if (strlen($_POST['fl_num']) == 0) {
        display_error(_("floor number/description can not be empty."));
        set_focus('fl_num');
        return false;
    }/*else if (preg_match($regexno, $_POST['fl_num']) != 0) {
		display_error(_("floor number can accept Numericals only."));
		set_focus('fl_num');
		return false;
    }*/
   
    return true;
}

//-----------------------------------------------------------------------------------

if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') {
	if(can_process()){
		$update_pager = false;
		if ($Mode == 'ADD_ITEM') {
			$result = '';
                            add_Floor($_POST['floor_id'], $_POST['fl_num']);
                            
                            display_notification(_('New Record  has been added'));
                            $Mode = 'RESET';
                            $update_pager = true;
		} else {
			//if ($_POST['ref_id']) {
				$result = '';
			    update_FloorNo($selected_id, $_POST['fl_num']);

				$Mode = 'RESET';
				$update_pager = true;
				display_notification(_('Record has been updated'));
			//} else {
			//	display_warning(_("Duplicate isuue no"));
			//}
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
if(!isset($_POST['book_location_page_next']) && !isset($_POST['book_location_page_last']))
$_SESSION['fun']['id'] = 0;
start_form(true);
$sql = getBookFloorInfo($status);

$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
           // _("Floor Id.") => array('align' => 'center'),
            _("Floor No.") => array('align' => 'center','fun' => 'floorNo'),
            _("Edit") => array('align' => 'center', 'fun' => 'edit'),
);
$table = &new_db_pager('book_location', $sql, $cols);

$table->width = "80%";
display_db_pager($table);
start_table(TABLESTYLE, "width=40%");
//$th = array(_("#"),_("Ref No"),_("Issue No."),_("Subject Title"),_("Dispatch Date"),_("Person Org Name"),_("Designation"),_("Department"),_("Address"),_("Dispatch Mode"),_("Document Type"),_("Dispatch Reciept"),_("Download Document"),_("Sender Person"),_("Sender Designation"),_("Sender Department"),'','');
inactive_control_column($th);
table_header($th);

$k = 0;

$nos = db_num_rows($result);

function id($row) {
    
    return ++$_SESSION['fun']['id'] ;
}

function floorNo($row) {
    
    return $row['fl_num'];
}

function edit($row) {
    return edit_button_cell("Edit" . $row['id'], _("Edit"));
}

function reciept($row) {
    return $row['dispatched_reciept_number_if_any'];
}

//inactive_control_row($th);
end_table();
echo '<br>';


//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) {
    // display_error($selected_id);

    if ($Mode == 'Edit') {

        $myrow = get_bookfloor_id($selected_id);
        $_POST['floor_id'] = $myrow["floor_id"];
        $_POST['fl_num'] = $myrow["fl_num"];
    }
    hidden('selected_id', $selected_id);
}


//table_section_title(_("Fine Amount "));


/*if ($Mode != 'Edit') {
    echo $sql1 = "SELECT MAX(floor_id) FROM fa_book_location WHERE floor_id LIKE 'Floor-%'";

    $empl_data = db_query($sql1);
    $empid_result = db_fetch_row($empl_data);

    //$last_max_emp_id = $empid_result[0];
    $last_emp1 = substr($empid_result[0], 5);

   $emp_inc_id = $last_emp1 + 1;
    if (strlen($emp_inc_id) == 1) {
        $_POST['floor_id'] = 'Floor-00' . $emp_inc_id;
    } else if (strlen($emp_inc_id) == 2) {
        $_POST['floor_id'] = 'Floor-0' . $emp_inc_id;
    } else {
        $_POST['floor_id'] = 'Floor-' . $emp_inc_id;
    }
}
label_row(_("Floor Code."), $_POST['floor_id']);

hidden('floor_id', $_POST['floor_id']);*/

if ($Mode != 'Edit') {
    $sql1 = "SELECT MAX(floor_id) FROM fa_book_location WHERE floor_id LIKE 'No-%'";

    $empl_data = db_query($sql1);
    $empid_result = db_fetch_row($empl_data);

    //$last_max_emp_id = $empid_result[0];
    $last_emp1 = substr($empid_result[0], 5);

    $emp_inc_id = $last_emp1 + 1;
    if (strlen($emp_inc_id) == 1) {
        $_POST['floor_id'] = 'No-00' . $emp_inc_id;
    } else if (strlen($emp_inc_id) == 2) {
        $_POST['floor_id'] = 'No-0' . $emp_inc_id;
    } else {
        $_POST['floor_id'] = 'No-' . $emp_inc_id;
    }
}
label_row(_("No."), $_POST['floor_id']);

hidden('floor_id', $_POST['floor_id']);

//table_section_title(_("Assign Amount"));
//text_row(_('Based On*'), 'based_on', $_POST['based_on'], 30);
//custom_list_row(_("Based On"), 'based_on', null, TRUE, false, 'book_fine');
room_no_row_ex(_("Floor Number/Description *"), 'fl_num', 30, null, null, null, null, null, FALSE);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------



end_page();
?>


