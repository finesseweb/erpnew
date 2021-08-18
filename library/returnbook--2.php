<?php
$page_security = 'SA_BOOKRETURN';
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

include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
if (!@$_GET['popup'])
	page(_($help_context = "Return Book Management"));
simple_page_mode(true);
?>

<?php

function can_process() {
	
	$regexno = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";
	
	
    if (strlen($_POST['amount']) == 0) {
        display_error(_("amount can not be empty."));
        set_focus('amount');
        return false;
    }else if (preg_match($regexno, $_POST['no_day']) != 0) {
		display_error(_("amount can accept Numericals only."));
		set_focus('amount');
		return false;
    }
    if (!empty($_POST['amount'])) {
            $regex1 = "^[0-9]{1,2}$";
            //display_error($_POST['no_day']);
            
            if (preg_match('/^[0-9]{3}$/' , $_POST['no_day']) != 0) {
                display_error(_("Not allowed more than 3 digit in no.of days."));
                set_focus('amount');
                return false;
            }
    }
    return true;
}

//-----------------------------------------------------------------------------------

if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') {
		$update_pager = false;
		if ($Mode == 'ADD_ITEM') {
			$result = '';
                            add_FineAmount($_POST['based_on'], $_POST['amount']);
                            
                            display_notification(_('New Record  has been added'));
                            $Mode = 'RESET';
                            $update_pager = true;
		} else {
			//if ($_POST['ref_id']) {
				$result = '';
				if($_POST['status']==1){
					$status=2;
				}else{
					$status=$_POST['status'];
				}
			    return_book($selected_id, $status,$_POST['book_condition']);
			    updatebookstatus($_POST['ISBN'],$_POST['copies_no']);

				$Mode = 'RESET';
				$update_pager = true;
				display_notification(_('Record has been updated'));
			//} else {
			//	display_warning(_("Duplicate isuue no"));
			//}
		}


		$Ajax->activate('details');
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
start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
ref_cells(_("ISBN:"), 'ISBN', '',null, _('Enter ISBN or leave empty'));
ref_cells(_("Book Issue Id :"), 'issueReqId', '',null, _('Enter book issue id or leave empty'));

//datetime_row(_("Check Out (Date &amp; Time)*") . ":", 'tr_todate', 20, null, '', '', '', null, true,false);
//date_row(_("Check In Date") . "", 'tr_fromdate');

submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();
end_form();
if (isset($_GET['ISBN'])) 
	$_POST['ISBN'] = $_GET['ISBN'];
	$isbn = $_POST['ISBN'];

if (isset($_GET['issueReqId'])) 
	$_POST['issueReqId'] = $_GET['issueReqId'];
	$issue_id = $_POST['issueReqId'];

start_form(true);

	set_global_connection(2);
	$sql ="SELECT * FROM user_holdbook WHERE status!=0";
	
	if ($isbn) {
		$sql .= " AND ISBN LIKE ". db_escape("%$isbn%");
	}
	
	if ($issue_id) {
		$sql .= " AND issueReqId LIKE ".db_escape("%$issue_id%");
	}
	
//$sql = retunBookList();

$_SESSION['fun']['id'] = 0;
$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("User Nmae") => array('align' => 'center','fun'=>'username'),
            _("ISBN") => array('align' => 'center'),
            _("Book Title") => array('align' => 'center'),
            _("Copy No") => array('align' => 'center'),
            _("Hold Date") => array('align' => 'center','fun' => 'hold_date'),
            _("Author") => array('align' => 'center','fun' => 'auth'),
            _("Publisher") => array('align' => 'center','fun' => 'pub'),
            _("Edition") => array('align' => 'center','fun' => 'edition'),
            _("Book Issue Id") => array('align' => 'center','fun' => 'issue'),
            _("Book Return Date") => array('align' => 'center','fun' => 'return_date'),
            _("Book Extension Date") => array('align' => 'center','fun' => 'extension_date'),
            _("Book Issue Date") => array('align' => 'center','fun' => 'issue_date'),
            _("Status") => array('align' => 'center','fun' => 'returnbook'),
            _("Edit") => array('align' => 'center', 'fun' => 'edit'),
);
$table = &new_db_pager('book_fine', $sql, $cols);

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
function auth($row) {
	
    return $row['author'];
}
function pub($row) {
	
    return $row['publisher'];
}
function edition($row) {
	
    return $row['edition'];
}
function issue($row) {
	
    return $row['issueReqId'];
}
function returnbook($row) {
	if($row['status']==2){
		$status="Returned";
	}else{
		$status="Pending";
	}
    return $status;
}
function hold_date($row) {
	if($row['hold_date']){
		$date=date('d-m-Y', strtotime($row['hold_date']));
	}else{
		$date='----';
	}
    return $date;
}
function return_date($row) {
	if($row['bookReturndDate']){
		$date=date('d-m-Y', strtotime($row['bookReturndDate']));
	}else{
		$date='----';
	}
    return $date;
}
function extension_date($row) {
	if($row['ext_date'] ){
		$date=date('d-m-Y', strtotime($row['ext_date']));
	}else{
		$date='----';
	}
    return $date;
}
function issue_date($row) {
	if($row['bookIssueDate'] ){
		$date=date('d-m-Y', strtotime($row['bookIssueDate']));
	}else{
		$date='----';
	}
    return $date;
}

function username($row) {
	$sql1 = "SELECT * FROM participants_login WHERE user_id=".$row['user_id'];
    $user_data = db_query($sql1);
    $user_result = db_fetch_row($user_data);
	return  $user_result[1];
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

        $myrow = checkoutholdbook_id($selected_id);
        $_POST['user_id'] = $myrow["user_id"];
        $_POST['ISBN'] = $myrow["ISBN"];
        $_POST['status'] = $myrow["status"];
        $_POST['book_condition'] = $myrow["book_condition"];
        $_POST['copies_no'] = $myrow["copies_no"];
    }
    hidden('selected_id', $selected_id);
    hidden('copies_no', $myrow["copies_no"]);
	//display_error($myrow["copies_no"]);
}


//hidden('ref_id', $_POST['ref_id']);
 if ($Mode == 'Edit') {
table_section_title(_("Issue  Book"));
room_no_row_ex(_("User Name"), 'user_id', 30, null, null, null, null, null, FALSE);
room_no_row_ex(_("ISBN"), 'ISBN', 30, null, null, null, null, null, FALSE);
custom_list_row(_("Book Return"), 'status', null, TRUE, false, 'yes_no_select_box');
custom_list_row(_("Book Condition"), 'book_condition', null, TRUE, false, 'yes_no_select_box');
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');
}
end_form();

//------------------------------------------------------------------------------------

set_global_connection(0);

end_page();
?>
<style>
.tablestyle_noborder tr {
	float: left;
	padding: 12px;
}
.label {
    background-color: #fff;
    color: black;
}
</style>

