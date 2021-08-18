<?php
$page_security = 'SA_ADDSELF';
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
	page(_($help_context = "Liabrary Shelf"));
simple_page_mode(true);
?>

<?php

function can_process() {
	
	$regexno = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";
	
	
   /* if (strlen($_POST['self_code']) == 0) {
        display_error(_("Self number can not be empty."));
        set_focus('self_code');
        return false;
    }else if (preg_match($regexno, $_POST['self_code']) != 0) {
		display_error(_("Self number can accept Numericals only."));
		set_focus('self_code');
		return false;
    }
	if ($_POST['self_code'] <= 0) {
        display_error(_("Self number can not be empty."));
        set_focus('self_code');
        return false;
    }*/
	if (strlen($_POST['self_code']) == 0) {
        display_error(_("Self code can not be empty."));
        set_focus('self_code');
        return false;
    }
	
	if (strlen($_POST['self_desc']) == 0) {
        display_error(_("Self Description can not be empty."));
        set_focus('self_desc');
        return false;
    }
   
    return true;
}

//-----------------------------------------------------------------------------------

if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') {
	if(can_process()){
		$update_pager = false;
		if ($Mode == 'ADD_ITEM') {
			$result = '';
                            add_Self($_POST['fl_num'],$_POST['aisle_code'],$_POST['self_desc'],$_POST['self_code']);
                            
                            display_notification(_('New Record  has been added'));
                            $Mode = 'RESET';
                            $update_pager = true;
		} else {
			//if ($_POST['ref_id']) {
				$result = '';
			    update_Self($selected_id, $_POST['fl_num'],$_POST['aisle_code'],$_POST['self_desc'],$_POST['self_code']);

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
if(!isset($_POST['book_location_page_next']))
$_SESSION['fun']['id'] = 0;
start_form(true);
$sql = getSelfInfo($status);

$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("Floor Id.") => array('align' => 'center','fun'=>'floor_num'),
            _("Aisle No.") => array('align' => 'center', 'fun'=>'floor_aisle'),
            _("Description") => array('align' => 'center'),
            _("Shelf No.") => array('align' => 'center'),
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
function floor_num($row) {
	$sql1 = "SELECT * FROM fa_book_location WHERE fl_num=". "'".$row['floor_id']."'";
    $user_data = db_query($sql1);
    $user_result = db_fetch_row($user_data);
	return  $user_result[2];
}
function floor_aisle($row) {
	$sql12 = "SELECT * FROM fa_floor_aisle WHERE floor_aisle=". "'".$row['floor_aisle']."'";
    $user_data1 = db_query($sql12);
    $user_result1 = db_fetch_row($user_data1);
	//echo "bfbfd<pre>";
	//print_r($user_result1);
	return  $user_result1[2];
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

        $myrow = get_self_id($selected_id);
        $_POST['fl_num'] = $myrow["floor_id"];
        $_POST['aisle_code'] = $myrow["floor_aisle"];
        $_POST['self_desc'] = $myrow["self_desc"];
        $_POST['self_code'] = $myrow["self_code"];
    }
    hidden('selected_id', $selected_id);
}

//table_section_title(_("Assign Self"));
table_section_title(_("Shelf Details"));
desiggroup_list_rowNew(_("Floor No.:"), 'fl_num', null, false, true);
$desig_group = $_POST['fl_num'];

if($_POST['fl_num'] !=""){
total_batch_term_list_row2(_("Asile No.:"), 'aisle_code', null, false, true);
}
text_row(_('Shelf No.*'), 'self_code', $_POST['self_code'], 30,50);
text_row(_('Shelf Description*'), 'self_desc', $_POST['self_desc'], 30,50);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();
?>


