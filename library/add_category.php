<?php
$page_security = 'SA_BOOKCATEGORY';
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
	page(_($help_context = " Book Category"));

simple_page_mode(true);
?>

<?php

function can_process() {
	
	$regexno = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";
	
	if (strlen($_POST['title']) == 0) {
		display_error(_("Title can not be empty."));
		set_focus('title');
		return false;
	}
	if (!empty($_POST['title'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['title']) != 0) {

			display_error(_("Special characters not allowed in title ."));
			set_focus('title');
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
			    add_bookCategory($_POST['title'], $_POST['status']);
				display_notification(_('New Record  has been added'));
				$Mode = 'RESET';
				$update_pager = true;
		} else {
			
                        $result = '';
                         update_bookCategory($selected_id, $_POST['title'], $_POST['status']);

                             $Mode = 'RESET';
                             $update_pager = true;
                             display_notification(_('Record has been updated'));
			
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
if(!isset($_POST['book_category_page_next']) && !isset($_POST['book_category_page_last']))
$_SESSION['fun']['id'] = 0;
start_form(true);
$sql = getBookCategoryInfo($status);

$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("Title") => array('align' => 'center'),
            _("Status") => array('align' => 'center', 'fun'=>'status'),
            _("Edit") => array('align' => 'center', 'fun' => 'edit'),
);
$table = &new_db_pager('book_category', $sql, $cols);

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
function edit($row) {
    return edit_button_cell("Edit" . $row['id'], _("Edit"));
}
function status($row) {
	if($row['cat_status']==1){
		$status='Active';
	}elseif($row['cat_status']==2){
		$status='Deactive';
	}
    return $status;
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
        $myrow = get_bookCategory_id($selected_id);
        $_POST['title'] = $myrow["category_name"];
        $_POST['status'] = $myrow["cat_status"];
    }
    hidden('selected_id', $selected_id);
}
table_section_title(_("Book Category"));
text_row(_('Titile*'), 'title', $_POST['title'], 30,50);
record_status_list_row(_("Status"), 'status', null, TRUE, false, 'status');
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------



end_page();
?>


