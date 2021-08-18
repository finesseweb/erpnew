<?php
$page_security = 'SA_BOOKALLOW';
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
	page(_($help_context = " Max. Books Allowed  for Checkout"));
simple_page_mode(true);
?>

<?php

function can_process() {
	
	$regexno = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";
	
	if (strlen($_POST['checkout_policy']) == 0) {
		display_error(_("Extensions Policy can not be empty."));
		set_focus('checkout_policy');
		return false;
	}
	if (!empty($_POST['checkout_policy'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['checkout_policy']) != 0) {

			display_error(_("Special characters not allowed in return policy ."));
			set_focus('checkout_policy');
			return false;
		}
	}
	
    if (strlen($_POST['no_day']) == 0) {
        display_error(_("Number of day can not be empty."));
        set_focus('no_day');
        return false;
    }else if (preg_match($regexno, $_POST['no_day']) != 0) {
		display_error(_("Number of day can accept Numericals only."));
		set_focus('no_day');
		return false;
    }
    if (!empty($_POST['no_day'])) {
            $regex1 = "^[0-9]{1,2}$";
            //display_error($_POST['no_day']);
            
            if (preg_match('/^[0-9]{3}$/' , $_POST['no_day']) != 0) {
                display_error(_("Not allowed more than 2 digit in no.of days."));
                set_focus('no_day');
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
                            add_checkoutPolicy($_POST['ref_id'], $_POST['checkout_policy'], $_POST['no_day']);
                            
                            display_notification(_('New Record  has been added'));
                            $Mode = 'RESET';
                            $update_pager = true;
		} else {
			if ($_POST['ref_id']) {
				$result = '';
			    update_checkoutPolicy ($selected_id, $_POST['ref_id'], $_POST['checkout_policy'], $_POST['no_day']);

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

start_form(true);
$sql = getCheckoutPolicyInfo($status);
$_SESSION['fun']['id'] = 0;
$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
           // _("ID") => array('align' => 'center'),
            _("Checkout Policy") => array('align' => 'center','fun' => 'checkOut'),
            _("No. of Books") => array('align' => 'center','fun' => 'bookNo'),
            _("Edit") => array('align' => 'center', 'fun' => 'edit'),
);
$table = &new_db_pager('allowed_book', $sql, $cols);

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
function ref($row) {
    return $row['ref_id'];
}
function checkOut($row) {
    return $row['checkout_policy'];
}
function bookNo($row) {
    return $row['no_book'];
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
if ($Mode == 'Edit') {
start_table(TABLESTYLE2);

if ($selected_id != -1) {
    // display_error($selected_id);

    if ($Mode == 'Edit') {

        $myrow = get_checkoutPolicy_id($selected_id);
        $_POST['ref_id'] = $myrow["ref_id"];
        $_POST['checkout_policy'] = $myrow["checkout_policy"];
        $_POST['no_day'] = $myrow["no_book"];
    }
    hidden('selected_id', $selected_id);
}


table_section_title(_("Book Allowed "));


if ($Mode != 'Edit') {
    $sql1 = "SELECT MAX(ref_id) FROM fa_allowed_book WHERE ref_id LIKE 'No-%'";

    $empl_data = db_query($sql1);
    $empid_result = db_fetch_row($empl_data);

    //$last_max_emp_id = $empid_result[0];
    $last_emp1 = substr($empid_result[0], 5);

    $emp_inc_id = $last_emp1 + 1;
	
    if (strlen($emp_inc_id) == 1) {
        $_POST['ref_id'] = 'No-00' . $emp_inc_id;
    } else if (strlen($emp_inc_id) == 2) {
        $_POST['ref_id'] = 'No-0' . $emp_inc_id;
    } else {
        $_POST['ref_id'] = 'No-' . $emp_inc_id;
    }
}
label_row(_("No."), $_POST['ref_id']);

hidden('ref_id', $_POST['ref_id']);

table_section_title(_("Assign  No. Books"));
text_row(_('Checkout Policy for*'), 'checkout_policy', $_POST['checkout_policy'], 30,50);
room_no_row_ex(_("No. of Books *"), 'no_day', 30, null, null, null, null, null, FALSE);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');
}
end_form();

//------------------------------------------------------------------------------------



end_page();
?>


