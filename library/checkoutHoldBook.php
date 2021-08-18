<?php
$page_security = 'SA_BOOKCHECKOUT';
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
	page(_($help_context = "Checkout Book"));
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
set_global_connection(2);
$back_date= date('Y-m-d', strtotime("-1 days"));
	$cronVal=cron($back_date);
	
    $sql = "DELETE FROM user_holdbook WHERE status = 0 AND hold_date='$back_date'";
    db_query($sql, "The sales department could not be updated");
    updatebookstatus($cronVal['ISBN'],$cronVal['copies_no'],0,0);

    set_global_connection(2);
    $cronVal2=cron2($back_date);
    $sql2 = "DELETE FROM user_holdbook WHERE status = 0 AND requested_date LIKE '%$back_date%' AND bookIssueDate=0000-00-00";
    db_query($sql2, "The sales department could not be updated");
    updatebookstatus2($cronVal2['ISBN'],$cronVal2['copies_no'],0,0);



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
			set_global_connection(0);
				$result = '';
				if($_POST['id_stu']=="Student"){
					$id="No-002";
				}else{
					$id="No-001";
				}
				$get_holdDate = "SELECT * from fa_return_policy Where ref_id='$id'";
				//display_error($get_holdDate);
				$aa=db_query($get_holdDate);
				$aa23 = db_fetch_row($aa);
				$re_date= $aa23[3];
				//$i= 20;
				$currentdate=date('Y-m-d');
				$date = new DateTime($_POST['hold_date']);
				$date->modify("+".$re_date." days");
				$date->format('Y-m-d');
			    $issue_date=date('Y-m-d', strtotime("+".$re_date." days"));
			       //$issue_date=$date->format('Y-m-d');
			    if($_POST['status']==1){
				   $copy_no=$_POST['copies_no'];
				   $isbn=$_POST['ISBN'];
				  if($_POST['hold_date']) {
				  if($_POST['hold_date'] == $currentdate){
			         issue_holdbook($selected_id, $_POST['status'],$issue_date);
					 display_notification(_('Record has been updated'));
					}else{
 						display_warning(_('Please issue book on hold date'));
					}
				  }else {
				  	
				  	 issue_holdbook($selected_id, $_POST['status'],$issue_date,$_POST['hold_date']);
					 
				  }
				  updateIssueCopyBook($copy_no,$isbn);
				}
                            
				$Mode = 'RESET';
				$update_pager = true;
				
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
ref_cells(_("ISBN:"), 'ISBN1', '',null, _('Enter ISBN or leave empty'));
ref_cells(_("Book Issue Id :"), 'issueReqId', '',null, _('Enter book issue id or leave empty'));
ref_cells(_("User Name :"), 'username', '',null, _('Enter user name or leave empty'));


//datetime_row(_("Check Out (Date &amp; Time)*") . ":", 'tr_todate', 20, null, '', '', '', null, true,false);
//date_row(_("Check In Date") . "", 'tr_fromdate');

submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();
end_form();

start_form(true);

if (isset($_GET['ISBN1'])) 
	$_POST['ISBN1'] = $_GET['ISBN1'];
	$isbn1 = $_POST['ISBN1'];

if (isset($_GET['username'])) 
	$_POST['username'] = $_GET['username'];
	$username = $_POST['username'];

if (isset($_GET['issueReqId'])) 
	$_POST['issueReqId'] = $_GET['issueReqId'];
	$issue_id = $_POST['issueReqId'];
if(!isset($_POST['book_fine_page_next']) && !isset($_POST['book_fine_page_last']))
$_SESSION['fun']['id'] = 0;
start_form(true);

set_global_connection(0);
	$sql ="SELECT * FROM user_holdbook  WHERE status !=2";

	if ($isbn1) {
		$sql .= " AND ISBN LIKE ". db_escape("%$isbn1%");
	}
	if ($issue_id) {
		$sql .= " AND issueReqId LIKE ".db_escape("%$issue_id%");
	}
	if ($username) {
		$sql .= " AND username LIKE ".db_escape("%$username%");
	}

	
//$sql = getHolBookIssueReq();

$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("User Name") => array('align' => 'center','fun'=>'username'),
            _("Roll no / Employee Id") => array('align' => 'center','fun'=>'userId'),
            _("ISBN") => array('align' => 'center','fun'=>'isbn'),
            _("Book Title") => array('align' => 'center','fun'=>'book_title'),
            _("Copy No") => array('align' => 'center','fun'=>'copies_no'),
            _("Hold Date") => array('align' => 'center','fun' => 'hold_date'),
            _("Author") => array('align' => 'center','fun' => 'auth'),
            _("Publisher") => array('align' => 'center','fun' => 'pub'),
            _("Edition") => array('align' => 'center','fun' => 'edition'),
            _("Book Issue Id") => array('align' => 'center','fun' => 'issue'),
            _("Book Return Date") => array('align' => 'center','fun' => 'return_date'),
            _("Book Extension Date") => array('align' => 'center','fun' => 'extension_date'),
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
function hold_date($row) {
	if($row['hold_date'] !=NULL){
		$date=date('d-m-Y', strtotime($row['hold_date']));
	}else{
		$date='----';
	}
    return $date;
}
function return_date($row) {
	if($row['bookReturndDate'] !='0000-00-00'){
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

function username($row) {
	if($row['empl_id']!=''){
		set_global_connection(0);
		$sql1 = "SELECT * FROM fa_users WHERE empl_id=". db_escape($row['empl_id']);
		$user_data = db_query($sql1);
		$user_result = db_fetch_row($user_data);
		$val=$user_result[3];
	}else{
		set_global_connection(2);
		$sql1 = "SELECT * FROM participants_login WHERE user_id=".$row['user_id'];
		$user_data = db_query($sql1);
		$user_result = db_fetch_row($user_data);
		$val=$user_result[1];
	}
	//display_error($val);
	return  $val;
}

function userId($row) {
	if($row['empl_id']!=''){
		set_global_connection(0);
		$sql1 = "SELECT * FROM fa_users WHERE empl_id=". db_escape($row['empl_id']);
		$user_data = db_query($sql1);
		$user_result = db_fetch_row($user_data);
		$val=$user_result[30];
	}else{
		set_global_connection(2);
		$sql1 = "SELECT * FROM participants_login WHERE user_id=".$row['user_id'];
		$user_data = db_query($sql1);
		$user_result = db_fetch_row($user_data);
		$val=$user_result[18];
	}
	//display_error($val);
	return  $val;
}
function copies_no($row) {
	$val_copies=$row['copies_no'];
   
	return  $val_copies;
}
function isbn($row) {
	$val_isbn=$row['ISBN'];
   
	return  $val_isbn;
}
function book_title($row) {
	$val_title=$row['book_title'];
   
	return  $val_title;
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
	if($myrow['empl_id']!=''){
		set_global_connection(0);
		$sql1 = "SELECT * FROM fa_users WHERE empl_id=". db_escape($myrow['empl_id']);
		$user_data = db_query($sql1);
		$user_result = db_fetch_row($user_data);
		$username=$user_result[3];
	}else{
		set_global_connection(2);
		$sql1 = "SELECT * FROM participants_login WHERE user_id=".$myrow['user_id'];
		$user_data = db_query($sql1);
		$user_result = db_fetch_row($user_data);
		$username=$user_result[1];
	}

        $_POST['user_id'] = $username;
        $_POST['ISBN'] = $myrow["ISBN"];
        $_POST['status'] = $myrow["status"];
        $_POST['copies_no'] = $myrow["copies_no"];
		$_POST['hold_date'] = $myrow["hold_date"];
		$_POST['bookReturndDate'] = $myrow["bookReturndDate"];
		$_POST['ext_date'] = $myrow["ext_date"];
		$_POST['status1'] = $myrow["status"];
		
		//set_global_connection(0);
		//$sql12 = "SELECT * FROM participants_login WHERE user_id=".$myrow['user_id'];
		//$user_data23 = db_query($sql12);
		//$user_result12 = db_fetch_row($user_data23);
		
		if($myrow["empl_id"]){
			$id="Employee"; 
		}else{
            $id="Student";
			
		}
		$_POST['id_stu'] = $id;
		
    }
    hidden('selected_id', $selected_id);
}

if ($Mode == 'Edit') {
	table_section_title(_("Issue  Book"));
       
	room_no_row_ex3(_("User Name"), 'user_id', 30, null, null, null, null, null, FALSE);
	room_no_row_ex3(_("Member"), 'id_stu', 30, null, null, null, null, null, FALSE);
	room_no_row_ex3(_("ISBN"), 'ISBN', 30, null, null, null, null, null, FALSE);
        room_no_row_ex3(_("Copy No."), 'copies_no', 30, null, null, null, null, null, FALSE);
        if($_POST['status1']==0){
			custom_list_row(_("Book Issue"), 'status', null, TRUE, false, 'yes_no_select_box');
		}
	hidden('hold_date', $_POST['hold_date']);
	end_table(1);

	submit_add_or_update_center($selected_id == -1, '', 'both');
}
end_form();

//------------------------------------------------------------------------------------

set_global_connection(0);

end_page();
?>


