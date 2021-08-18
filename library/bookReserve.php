<?php
$page_security = 'SA_BOOKRESERVATION';
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
	page(_($help_context = "Book Reserve"));

?>
<html lang="en">
    <head>
        <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js" ?>"></script>
    </head>
</html>
<?php
//===[Working]=====//
if(!isset($_POST['book_page_next']) && !isset($_POST['book_page_last']))
$_SESSION['fun']['id'] = 0;
simple_page_mode(true);
start_form(true);
$sql = getBookList($status);
$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("ISBN") => array('align' => 'center'),
            _("Book Title") => array('align' => 'center'),
            _("Category") => array('align' => 'center', 'fun'=>'cat_name'),
            _("Author") => array('align' => 'center'),
            _("Publisher") => array('align' => 'center'),
            _("Published Date") => array('align' => 'center'),
            _("Edition") => array('align' => 'center'),
            _("Copies No") => array('align' => 'center'),
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

//$nos = db_num_rows($result);

function id($row) {
    
    return ++$_SESSION['fun']['id'] ;
}
function status($row) {
	if($row['status']==1){
		$status='Active';
	}elseif($row['status']==2){
		$status='Deactive';
	}
    return $status;
}
function cat_name($row) {
	$cat_name=$row['category_name'];
    return $cat_name;
}
function ref($row) {
    return $row['ref_id'];
}
function edit($row) {
   // echo "<pre>";
   // print_r($row);
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
    }
    hidden('selected_id', $selected_id);
}

table_section_title(_("Reserve Books"));

label_row(_("ISBN"), $_POST['ISBN']);

hidden('ISBN', $_POST['ISBN']);
table_section_title(_("Book Details"));
?>
<?php
if ($Mode == 'RESET') {
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    $_POST['show_inactive'] = $sav;
}
//-----------------------------------------------------------------------------------

$result = get_bookCopies(check_value('show_inactive'),$_POST['ISBN']);
//start_form();
//start_table(TABLESTYLE);
$th = array( _("ISBN"),_("Book Title"),/* _("Category"),*/ _("Copies"));
reserve_book_column($th);
table_header($th);

$k = 0;
while ($myrow = db_fetch($result)) {

    alt_table_row_color($k);
    label_cell($myrow["ISBN"]);
    label_cell($myrow["title"]);
    //label_cell($myrow["category"]);
    label_cell($myrow["copies_no"]);
    status_books_copies($myrow["copies_no"], $myrow["status"], 'books_copies', 'copies_no',$myrow["ISBN"]);
    end_row();
}
inactive_control_row_book($th);
end_table();
echo '<br>';
end_form();

//------------------------------------------------------------------------------------

end_page();
?>
<!-- <p style='padding: 3px;text-align:center;'> <a href='javascript:goBack();'>Back</a></p> -->

<style>
.tablestyle2 {
	width: 31%;
}

#Update {
    display:none;
}
</style>