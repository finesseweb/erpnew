<?php
$page_security = 'SA_ADDBOOK';
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

$js = "";
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Add Authors"), @$_REQUEST['popup'], false, "", $js);

//page(_($help_context = "Add Books", @$_REQUEST['popup'], false, "", $js));
/*if (!@$_GET['popup'])
	page(_($help_context = "Add Books"));*/

simple_page_mode(true);
?>

<?php

function can_process() {
	
	$regexno = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";
	
	if (strlen($_POST['auth_name']) == 0) {
		display_error(_("Author can not be empty."));
		set_focus('author');
		return false;
	}
	if (!empty($_POST['auth_name'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['auth_name']) != 0) {

			display_error(_("Special characters not allowed in author ."));
			set_focus('author');
			return false;
		}
	}
	if (strlen($_POST['auth_code']) == 0) {
		display_error(_("Publisher can not be empty."));
		set_focus('publisher');
		return false;
	}
	if (!empty($_POST['auth_code'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['auth_code']) != 0) {

			display_error(_("Special characters not allowed in publisher ."));
			set_focus('publisher');
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
				add_auth($_POST['auth_name'], $_POST['auth_code']);
				//display_error($_POST['copies_no']);
				
				display_notification(_('New Record  has been added'));
				$Mode = 'RESET';
				$update_pager = true;
		} else {
			if ($_POST['auth_id']) {
				$result = '';
			    update_auth($selected_id, $_POST['auth_name'], $_POST['auth_code'], $_POST['status']);
		
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
if(!isset($_POST['books_page_next']) && !isset($_POST['books_page_last']))
$_SESSION['fun']['id'] = 0;
start_form(true);
$sql = getAuthorList($status);
$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("Author Code") => array('align' => 'left'),
            _("Author Name") => array('align' => 'left'),
            _("Status") => array('align' => 'left','fun' => 'status'),
            _("Edit") => array('fun' => 'edit')
);
$table = &new_db_pager('books', $sql, $cols);

$table->width = "50%";
display_db_pager($table);
start_table(TABLESTYLE);
//inactive_control_column($th);
table_header($th);

$k = 0;

$nos = db_num_rows($result);

function id($row) {
    
    return ++$_SESSION['fun']['id'] ;
}
function status($row) {
	if($row['status']==1){
		$status='Active';
	}elseif($row['status']==0){
		$status='Deactive';
	}
    return $status;
}

function edit($row) {
   
    return edit_button_cell("Edit" . $row['auth_id'], _("Edit"));
   
}

//inactive_control_row($cols);
end_table();
echo '<br>';


//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) {
    if ($Mode == 'Edit') {
         $myrow = get_auth_list($selected_id);
        $_POST['auth_id'] = $myrow["auth_id"];
        $_POST['auth_name'] = $myrow["auth_name"];
        $_POST['auth_code'] = $myrow["auth_code"];
        $_POST['status'] = $myrow["status"];
    }
    hidden('selected_id', $selected_id);
}

//display_error($selected_id);
table_section_title(_("Add Author "));
hidden('auth_id', $_POST['auth_id']);
text_row(_('Author Name *'), 'auth_name', $_POST['auth_name'], 30,50);

text_row(_('Author Code *'), 'auth_code', $_POST['auth_code'], 30,50);
if ($Mode == 'Edit') {
record_status_list_row(_("Status"), 'status','Active');
}
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();
?>


