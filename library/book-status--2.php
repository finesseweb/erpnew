<?php
$page_security = 'SA_BOOKSTATUS';
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
	page(_($help_context = "Books"));
simple_page_mode(true);
?>

<?php

function can_process() {
	
	/*$regexno = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";
	
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
    }*/
    return true;
}

//-----------------------------------------------------------------------------------

if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') {
	if(can_process()){
		$update_pager = false;
		if ($Mode == 'ADD_ITEM') {
			$result = '';
				add_Boolks($_POST['ISBN'], $_POST['title'], $_POST['category'], $_POST['author'], $_POST['publisher'], $_POST['published_date'], $_POST['edition'], $_POST['copies_no'], $_POST['book_cost'], $_POST['copyright_year'], $_POST['status']);
				//display_error($_POST['copies_no']);
				if($_POST['copies_no'] >0){
					for ($x = 1; $x <= $_POST['copies_no']; $x++) {
					  //echo "The number is: $x <br>";
					  $copy_number='COPY00'.$x;
					  $sql = "INSERT INTO " . TB_PREF . "books_copies (ISBN, title, category, copies_no) VALUES (" . db_escape($_POST['ISBN']) . "," . db_escape($_POST['title'])."," . db_escape($_POST['category'])."," . db_escape($copy_number).")";
  
					// display_error($sql);
					 db_query($sql, "The sales department could not be added");

					}
				}
				display_notification(_('New Record  has been added'));
				$Mode = 'RESET';
				$update_pager = true;
		} else {
			if ($_POST['ISBN']) {
				$result = '';
			    update_books ($selected_id, $_POST['ISBN'], $_POST['title'], $_POST['category'], $_POST['author'], $_POST['publisher'], $_POST['published_date'], $_POST['edition'], $_POST['copies_no'], $_POST['book_cost'], $_POST['copyright_year'], $_POST['status']);
				if($_POST['copies_no'] >0){
					$sql = "DELETE  FROM " . TB_PREF . "books_copies Where ISBN = ".db_escape($_POST['ISBN']); 										 
					$delete_copies=db_query($sql);
					if($delete_copies){
						for ($x = 1; $x <= $_POST['copies_no']; $x++) {
						 // echo "The number is: $x <br>";
						  $copy_number='COPY00'.$x;
						  $sql = "INSERT INTO " . TB_PREF . "books_copies (ISBN, title, category, copies_no) VALUES (" . db_escape($_POST['ISBN']) . "," . db_escape($_POST['title'])."," . db_escape($_POST['category'])."," . db_escape($copy_number).")";
	  
						// display_error($sql);
						 db_query($sql, "The sales department could not be added");

						}
					}
				}
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
$sql = getBookList();
$_SESSION['fun']['id'] = 0;
$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("ISBN") => array('align' => 'center'),
            _("Book Title") => array('align' => 'center'),
           // _("Category") => array('align' => 'center'),
            _("Category") => array('align' => 'center', 'fun'=>'cat_name'),
            _("Author") => array('align' => 'center'),
            _("Publisher") => array('align' => 'center'),
            _("Published Date") => array('align' => 'center'),
            _("Edition") => array('align' => 'center'),
            _("Copies No") => array('align' => 'center'),
            _("Book Cost") => array('align' => 'center'),
            _("Copyright Year") => array('align' => 'center'),
            _("No.Book Damage") => array('align' => 'center','fun' => 'b_cond')
           // _("Edit") => array('align' => 'center', 'fun' => 'edit'),
);
$table = &new_db_pager('books', $sql, $cols);

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
function cat_name($row) {
	$cat_name=$row['category_name'];
    return $cat_name;
}
function b_cond($row) {
	$sql ="SELECT count(*) FROM `fa_books_copies` WHERE `ISBN` = '".$row['ISBN']."' AND `damage` = 1";
	$data12=db_query($sql);
	$dd=db_fetch_row($data12);
    return $dd[0];
}
function ref($row) {
    return $row['ref_id'];
}
function edit($row) {
    return edit_button_cell("Edit" . $row['book_id'], _("Edit"));
}

function reciept($row) {
    return $row['dispatched_reciept_number_if_any'];
}

//inactive_control_row($th);
end_table();
echo '<br>';


//-----------------------------------------------------------------------------------



end_form();

//------------------------------------------------------------------------------------



end_page();
?>


