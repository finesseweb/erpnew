<?php
$page_security = 'SA_BOOKMAP';
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

page(_($help_context = "Store Book to Self"), @$_REQUEST['popup'], false, "", $js);

simple_page_mode(true);
?>

<?php

function can_process() {
	
	// $regexno = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";
	
	
 //    if (strlen($_POST['self_code']) == 0) {
 //        display_error(_("Self number can not be empty."));
 //        set_focus('self_code');
 //        return false;
 //    }else if (preg_match($regexno, $_POST['self_code']) != 0) {
	// 	display_error(_("Self number can accept Numericals only."));
	// 	set_focus('self_code');
	// 	return false;
 //    }
	// if ($_POST['self_code'] <= 0) {
 //        display_error(_("Self number can not be empty."));
 //        set_focus('self_code');
 //        return false;
 //    }
	// if (strlen($_POST['self_desc']) == 0) {
 //        display_error(_("Self Description can not be empty."));
 //        set_focus('self_desc');
 //        return false;
 //    }
   
    return true;
}

//-----------------------------------------------------------------------------------

if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') {
	if(can_process()){
		$update_pager = false;
		if ($Mode == 'ADD_ITEM') {
			$result = '';
                            
                 $sql = "INSERT INTO " . TB_PREF . "book_map (cat_id,ISBN, copies_no,title, floor, aisel,self) VALUES (" . db_escape($_POST['category']) . "," . db_escape($_POST['isbn']) . "," . db_escape(implode(",",$_POST['copiesno']))."," . db_escape($_POST['title'])."," . db_escape($_POST['fl_num'])."," . db_escape($_POST['aisle_code'])."," . db_escape($_POST['self_code']).")";
                 
                 db_query($sql, "The sales department could not be added");
             
                
                display_notification(_('New Record  has been added'));
                $Mode = 'RESET';
                $update_pager = true;
		} else {
			
				$result = '';
			    updateSelfmap($selected_id, $_POST['category'], $_POST['isbn'], $_POST['copiesno'], $_POST['title'], $_POST['fl_num'],$_POST['aisle_code'],$_POST['self_code']);

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
if(!isset($_POST['book_location_page_next']))
$_SESSION['fun']['id'] = 0;
start_form(true);
$tablename='book_map group by isbn';
$sql = getTableInfo($tablename);

$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("ISBN.") => array('align' => 'center','fun'=>'isbn'),
            _("Floor Name.") => array('align' => 'center','fun'=>'floor_num'),
            _("Aisle Name.") => array('align' => 'center','fun'=>'floor_aisle'),
            _("Self Name.") => array('align' => 'center','fun'=>'self'),
           // _("Total Number of Copies.") => array('align' => 'center','fun'=>'totalCopies'),
            _("Edit") => array('align' => 'center', 'fun' => 'edit'),
);
$table = &new_db_pager('book_location', $sql, $cols);

$table->width = "80%";
display_db_pager($table);
start_table(TABLESTYLE, "width=40%");
inactive_control_column($th);
table_header($th);

$k = 0;

$nos = db_num_rows($result);

function id($row) {
    
    return ++$_SESSION['fun']['id'] ;
}
function isbn($row) {
    return  $row['ISBN'];
}
function floor_num($row) {
	return  $row['floor'];
}
function floor_aisle($row) {
	
	return $row['aisel'];
}
function self($row) {
    
    return $row['self'];
}
function totalCopies($row) {
    $sql = "SELECT COUNT(*) FROM " . TB_PREF . "book_map WHERE ISBN=" . db_escape($row['ISBN']);
    $result = db_query($sql, "could not get department");
    $val=db_fetch($result);
    return  $val[0];
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

        $myrow = getSelfDetailByid($selected_id);
        $_POST['fl_num'] = $myrow["floor"];
        $_POST['aisle_code'] = $myrow["aisle"];
        $_POST['self_code'] = $myrow["self"];
        $_POST['isbn'] = $myrow["ISBN"];
        $_POST['category'] = $myrow["cat_id"];
        $_POST['copiesno'] = $myrow["copies_no"];

       

        
    }

    hidden('selected_id', $selected_id);

}



//table_section_title(_("Assign Self"));
table_section_title(_("Self Details"));
desiggroup_list_rowNew(_("Floor Name:"), 'fl_num', null, false, true);
$desig_group = $_POST['fl_num'];

if($_POST['fl_num'] !=""){
 total_batch_term_list_row2(_("Asile Name:"), 'aisle_code', null, false, true);
}

if($_POST['aisle_code'] !=""){
 total_self_list_row2(_("Self Name.:"), 'self_code', null, false, true);
}


end_table(1);

//if(isset($_POST['self_code'])){
    start_table(TABLESTYLE_NOBORDER);
    start_row();
    book_category_list_cells12(_("Category:"), 'category', $_POST['category'], false, true);
    $Ajax->activate('isbn');
    book_isbn_list_cells12(_("ISBN:"), 'isbn', $_POST['isbn'], false, true);
    $booval=book_d1($_POST['isbn']);
    $Ajax->activate('title');
    $Ajax->activate('author');
    self_row(_('Book Title'), 'title', $booval[0], 30);
    self_row(_('Author'), 'author', $booval[1], 30);
    $Ajax->activate('copiesno');
    book_copies_list_row('Copies No:', 'copiesno', explode(',',$_POST['copiesno']),null, false,'',$multli_select_height);
    end_row();
    end_table();
//}


submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();


//------------------------------------------------------------------------------------

end_page();
?>


