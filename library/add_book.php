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
//if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Add Books"), @$_REQUEST['popup'], false, "", $js);

//page(_($help_context = "Add Books", @$_REQUEST['popup'], false, "", $js));
/*if (!@$_GET['popup'])
	page(_($help_context = "Add Books"));*/

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
	/*if (!empty($_POST['title'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['title']) != 0) {

			display_error(_("Special characters not allowed in title ."));
			set_focus('title');
			return false;
		}
	}*/
	if (strlen($_POST['author']) == 0) {
		display_error(_("Author can not be empty."));
		set_focus('author');
		return false;
	}
	if (!empty($_POST['author'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['author']) != 0) {

			display_error(_("Special characters not allowed in author ."));
			set_focus('author');
			return false;
		}
	}
	if (strlen($_POST['publisher']) == 0) {
		display_error(_("Publisher can not be empty."));
		set_focus('publisher');
		return false;
	}
	if (!empty($_POST['publisher'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['publisher']) != 0) {

			display_error(_("Special characters not allowed in publisher ."));
			set_focus('publisher');
			return false;
		}
	}
	if (strlen($_POST['edition']) == 0) {
		display_error(_("Edition can not be empty."));
		set_focus('edition');
		return false;
	}
	if (!empty($_POST['edition'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['edition']) != 0) {

			display_error(_("Special characters not allowed in edition ."));
			set_focus('edition');
			return false;
		}
	}
	if (strlen($_POST['copies_no']) == 0) {
		display_error(_("Copies no can not be empty."));
		set_focus('copies_no');
		return false;
	}
	if ($_POST['copies_no'] == 0) {
		display_error(_("Copies no can not be empty."));
		set_focus('copies_no');
		return false;
	}
	if ($_POST['copies_no'] < 0) {
		display_error(_("Copies no can not be empty."));
		set_focus('copies_no');
		return false;
	}
	if (!empty($_POST['copies_no'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['copies_no']) != 0) {

			display_error(_("Special characters not allowed in copies no ."));
			set_focus('copies_no');
			return false;
		}
	}
	if (strlen($_POST['book_cost']) == 0) {
		display_error(_("Book cost can not be empty."));
		set_focus('book_cost');
		return false;
	}
	if ($_POST['book_cost'] == 0) {
		display_error(_("Book cost can not be empty."));
		set_focus('book_cost');
		return false;
	}
	if ($_POST['book_cost'] < 0) {
		display_error(_("Book cost can not be empty."));
		set_focus('book_cost');
		return false;
	}
	if (!empty($_POST['book_cost'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['book_cost']) != 0) {

			display_error(_("Special characters not allowed in book cost ."));
			set_focus('book_cost');
			return false;
		}
	}
	if (strlen($_POST['copyright_year']) == 0) {
		display_error(_("Copyright year can not be empty."));
		set_focus('copyright_year');
		return false;
	}
	if (!empty($_POST['copyright_year'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['copyright_year']) != 0) {

			display_error(_("Special characters not allowed in Copyright year ."));
			set_focus('copyright_year');
			return false;
		}
	}
	if (strlen($_POST['book_cost']) == 0) {
        display_error(_("Book cost can not be empty."));
        set_focus('book_cost');
        return false;
    }else if (preg_match($regexno, $_POST['book_cost']) != 0) {
		display_error(_("Book cost can accept Numericals only."));
		set_focus('book_cost');
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
						  $copy_number='COPY00'.$x;
						  $sql = "INSERT INTO " . TB_PREF . "books_copies (ISBN, title, category, copies_no) VALUES (" . db_escape($_POST['ISBN']) . "," . db_escape($_POST['title'])."," . db_escape($_POST['category'])."," . db_escape($copy_number).")";
	  
						 db_query($sql);

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
if(!isset($_POST['books_page_next']) && !isset($_POST['books_page_last']))
$_SESSION['fun']['id'] = 0;
start_form(true);
$sql = getBookList($status);
$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("ISBN") => array('align' => 'center'),
            _("Book Title") => array('align' => 'center'),
           // _("Category") => array('align' => 'center'),
            _("Category") => array('align' => 'center', 'fun'=>'cat_name'),
            _("Author") => array('align' => 'center','fun'=>'auth_name'),
            _("Publisher") => array('align' => 'center','fun'=>'pub_name'),
            _("Published Date") => array('align' => 'center','fun'=>'publishDate'),
            _("Edition") => array('align' => 'center'),
            _("No. of Copies") => array('align' => 'center'),
            _("Book Cost") => array('align' => 'center'),
            _("Copyright Year") => array('align' => 'center'),
            _("Status") => array('align' => 'center','fun' => 'status'),
            _("Edit") => array('align' => 'center', 'fun' => 'edit'),
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

function auth_name($row) {
    $sql = "SELECT * from " . TB_PREF . "author_details where auth_id =" . db_escape($row['author']);
    $result = db_query($sql, "could not get department");
    $res=db_fetch($result);
    $cat_name=$res['1'];
    return $cat_name;
}

function pub_name($row) {
    $sql = "SELECT * from " . TB_PREF . "publisher where pub_id =" . db_escape($row['publisher']);
    $result = db_query($sql, "could not get department");
    $res=db_fetch($result);
    $cat_name=$res['2'];
    return $cat_name;
}
function status($row) {
	if($row['status']==1){
		$status='Active';
	}elseif($row['status']==2){
		$status='Deactive';
	}
    return $status;
}
function ref($row) {
    return $row['ref_id'];
}
function publishDate($row) {
	$date=date('d-m-Y',strtotime($row['published_date']));

    return $date;
}
function edit($row) {
    return edit_button_cell("Edit" . $row['ISBN'], _("Edit"));
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
    if ($Mode == 'Edit') {
		//display_error($selected_id);
        $myrow = get_book_list($selected_id);
        $_POST['ISBN'] = $myrow["ISBN"];
        $_POST['title'] = $myrow["title"];
        $_POST['category'] = $myrow["category"];
        $_POST['author'] = $myrow["author"];
        $_POST['publisher'] = $myrow["publisher"];
        $_POST['published_date'] = $myrow["published_date"];
        $_POST['edition'] = $myrow["edition"];
        $_POST['copies_no'] = $myrow["copies_no"];
        $_POST['book_cost'] = $myrow["book_cost"];
        $_POST['copyright_year'] = $myrow["copyright_year"];
        $_POST['status'] = $myrow["status"];


        $_POST['published_date']=date('m/d/Y', strtotime($_POST['published_date']));
    }
    hidden('selected_id', $selected_id);
}


table_section_title(_("Add Book "));


if ($Mode != 'Edit') {
    $sql1 = "SELECT MAX(ISBN) FROM fa_books WHERE ISBN LIKE 'ISBN-%'";

    $empl_data = db_query($sql1);
    $empid_result = db_fetch_row($empl_data);

    //$last_max_emp_id = $empid_result[0];
    $last_emp1 = substr($empid_result[0], 5);

    $emp_inc_id = $last_emp1 + 1;
    if (strlen($emp_inc_id) == 1) {
        //$_POST['ISBN'] = 'ISBN-00' . $emp_inc_id;
    } else if (strlen($emp_inc_id) == 2) {
        //$_POST['ISBN'] = 'ISBN-0' . $emp_inc_id;
    } else {
        //$_POST['ISBN'] = 'ISBN-' . $emp_inc_id;
    }
}
//label_row(_("ISBN"), $_POST['ISBN']);
text_row(_('ISBN*'), 'ISBN', $_POST['ISBN'], 30,50);
//hidden('ISBN', $_POST['ISBN']);

table_section_title(_("Book Details"));
book_author_list_cells12(_('Author*'), 'author', $_POST['author'], '--Select--',false);
book_publisher_list_cells12(_('Publisher*'), 'publisher', $_POST['publisher'], '--Select--',false);
book_category_list_cells12(_("Category:"), 'category', $_POST['category'], '--Select--', false);
text_row(_('Book Title*'), 'title', $_POST['title'], 30,50);
//text_row(_('Category*'), 'category', $_POST['category'], 30);




//text_row(_('Published Date*'), 'published_date', $_POST['published_date'], 30);
date_row(_("Published Date") . "", 'published_date');
text_row(_('Edition*'), 'edition', $_POST['edition'], 30,50);
text_row(_('No. of Copies*'), 'copies_no', $_POST['copies_no'], 30,50);
text_row(_('Cost of @ Book(Rs)'), 'book_cost', $_POST['book_cost'], 30,50);
text_row(_('Copyright Year'), 'copyright_year', $_POST['copyright_year'], 30,50);
record_status_list_row(_("Status"), 'status','Active');

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();
?>


