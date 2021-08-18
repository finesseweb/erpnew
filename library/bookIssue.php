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

//include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

$js = "";
//if ($use_date_picker)
	//$js .= get_js_date_picker();

page(_($help_context = "Books Issue (Student)"), @$_REQUEST['popup'], false, "", $js);

//page(_($help_context = "Add Books", @$_REQUEST['popup'], false, "", $js));
/*if (!@$_GET['popup'])
	page(_($help_context = "Add Books"));*/

simple_page_mode(true);
?>
<?php
//echo $_POST['stu_id']    ;

///echo "<pre>";print_r($_POST); 
//$Mode = 'Edit';
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
	if ($_POST['copies_no'] == '') {
                 display_error($_POST['copies_no']);
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
                            if($_POST['hold_date']){
			$direct_issue=1;
		         }else{
			$direct_issue=0;  
		             }
		             $max_number = getmaxissue();
                              $last_emp1 = substr($max_number, 9);
	                      $emp_inc_id = $last_emp1 + 1;
	                       if (strlen($emp_inc_id) == 1) {
	                       $bookIssueId = 'IssueId-00' . $emp_inc_id;
	                       } else if (strlen($emp_inc_id) == 2) {
	                      $bookIssueId = 'IssueId-0' . $emp_inc_id;
	                      } else {
	                      $bookIssueId = 'IssueId-' . $emp_inc_id;
	                       }
                               
                               $sid="No-002";
                                 $userissue= getIssueBookList($sid);
                                  $u_Issuebook = getIssueBookList2($_POST['stu_id']);
                                  
                                  if($u_Issuebook > $userissue['no_book']) {
                                      
                                      display_warning(_("You Exceed your limit"));
                                  }

                                  else { 
                               $get_holdDate = "SELECT * from fa_return_policy Where ref_id='$sid'";
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
                               if($_POST['stu_id']){
                                  $user =  Getusername($_POST['stu_id']);  
                                        
                               }
                               else {
                                 $user =  Getusername1($_POST['emp_id']);  
                               }
                             //display_error($max_number);
//                            display_error($_POST);
//                            die();
                               set_global_connection(0);
				$result = '';
			    update_issuebooks ($_POST['stu_id'], $user,$_POST['ISBN'], $_POST['title'], $_POST['copies_no'], $_POST['author'], $_POST['publisher'],$_POST['edition'],$_POST['hold_date'], $bookIssueId, $direct_issue,$issue_date);
                            
                              updateIssueCopyBook($_POST['copies_no'],$_POST['ISBN']);
                              
                              
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
                                  }
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

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
ref_cells(_("ISBN:"), 'ISBN1', '',null, _('Enter ISBN or leave empty'));
ref_cells(_("Book Title :"), 'title1', '',null, _('Enter book issue id or leave empty'));

submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();
end_form();
//if (isset($_GET['ISBN'])) 
//	$_POST['ISBN'] = $_GET['ISBN'];
	$isbn = $_POST['ISBN1'];


//
//if (isset($_GET['title'])) 
//	$_POST['title'] = $_GET['title'];
	$title = $_POST['title1'];

if(!isset($_POST['books_page_next']) && !isset($_POST['books_page_last']))



set_global_connection(0);
	
        if ($isbn =='' || $title =='' ) {
		$sql ="SELECT * FROM fa_books  JOIN ".TB_PREF."book_category ON ". TB_PREF . "books.category = ".TB_PREF."book_category.id WHERE status=1";
        }
	
	elseif ($isbn !='' || $title !='' ) {
		$sql ="SELECT * FROM fa_books JOIN ".TB_PREF."book_category ON ". TB_PREF . "books.category = ".TB_PREF."book_category.id WHERE status=1";
        }
	
	if ($isbn) {
		$sql .= " AND ISBN LIKE ". db_escape("%$isbn%");
	}
	if ($title) {
		$sql .= " AND title LIKE ".db_escape("%$title%");
	}
	
start_form(true);
//$sql = getBookList($status);
  $_SESSION['fun']['id'] = 0;
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
function status($row) {
	if($row['status']==1){
		$status='Active';
	}elseif($row['status']==2){
		$status='Deactive';
	}
    return $status;
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
 div_start('issue_tbl');
//display_error($stu_ids);
start_table(TABLESTYLE2);

if ($selected_id != -1) {
      $Mode = 'Edit';
    if ($Mode == 'Edit') {
      
      
       
	//print_r($cpoies); 
        //die();
        $myrow = get_book_list($selected_id);
        $cat = getcategory($myrow["category"]);
        $auth = getauthor($myrow["author"]);
        $pub = getpublisher($myrow["publisher"]);
        $cpoies=  getcopies($myrow["ISBN"]);
        $stu_id =  $_POST['stu_id'];
        $_POST['ISBN'] = $myrow["ISBN"];
        $_POST['title'] = $myrow["title"];
        $_POST['category'] = $cat;
        $_POST['author'] = $auth;
        $_POST['publisher'] = $pub;
        $_POST['edition'] = $myrow["edition"];
        $_POST['copies_no'] = $cpoies;
        $_POST['status'] = $myrow["status"];

        
    }
    hidden('selected_id', $selected_id);
}
if($Mode =="Edit"){
$u_Issuebook = getIssueBookList2($_POST['stu_id']);
table_section_title(_("Issue Book "));
student_list_cells12(_("Student:"), 'stu_id', $_POST['stu_id'], '--Select--', true);
label_warning(_("No Of Book Issued"), $u_Issuebook);
//hidden('ISBN', $_POST['ISBN']);

table_section_title(_("Book Details"));
room_no_row_ex3(_('ISBN*'), 'ISBN', $_POST['ISBN'], 30);
room_no_row_ex3(_('Book Title*'), 'title', $_POST['title'], 30);
room_no_row_ex3(_('Category*'), 'category', $_POST['category'], 30);
//book_category_list_cells12(_("Category:"), 'category', $_POST['category'], false, true);
room_no_row_ex3(_('Author*'), 'author', $_POST['author'], 30);
room_no_row_ex3(_('Publisher*'), 'publisher', $_POST['publisher'], 30);


//text_row(_('Published Date*'), 'published_date', $_POST['published_date'], 30);
//date_row(_("Published Date") . "", 'published_date');
room_no_row_ex3(_('Edition*'), 'edition', $_POST['edition'], 30);
room_no_row_ex3(_('Copy No*'), 'copies_no', $_POST['copies_no'], 30);
//text_row(_('Cost of @ Book(Rs)'), 'book_cost', $_POST['book_cost'], 30);
//text_row(_('Copyright Year'), 'copyright_year', $_POST['copyright_year'], 30);
record_status_list_row(_("Status"), 'status', null, TRUE, false, 'status');

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');
    
}
end_form();


//------------------------------------------------------------------------------------
div_end();
end_page();
?>


