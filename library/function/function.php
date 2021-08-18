<?php
function floor_list($name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	$sql = "SELECT fl_num, fl_num FROM ".TB_PREF."book_location";
	
	return combo_input($name, $selected_id, $sql, 'fl_num', 'fl_num',
 	array('order'=>'id',
		'spec_option' => $spec_opt,
		'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
 		'async' => true
 	));
}
function floor_list_cells($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo floor_list($name, $selected_id, $spec_opt, $submit_on_change);
	echo "</td>\n";
}
function room_no_row_ex2($label, $name, $size, $max = null, $title = null, $value = null, $params = null, $post_label = null, $submit_on_change = false, $readonly = false, $id = '') {
    echo "<tr><td class='label'>$label</td>";
    text_cells_ex(null, $name, $size, $max, $value, $title, $params, $post_label, $submit_on_change, $readonly, $id);

    echo "</tr>\n";
}
function room_no_row_ex3($label, $name, $size, $max = null, $title = null, $value = null, $params = null, $post_label = null, $submit_on_change = false, $readonly = TRUE, $id = '') {
    echo "<tr><td class='label'>$label</td>";
    text_cells_ex(null, $name, $size, $max, $value, $title, $params, $post_label, $submit_on_change, $readonly, $id);

    echo "</tr>\n";
}


function stu_no_row_ex3($label, $name, $size, $max = null, $title = null, $value = null, $params = null, $post_label = null, $submit_on_change = false, $readonly = TRUE, $id = '') {
    echo "<tr><td class='label'>$label</td>";
    text_cells_ex(null, $name, $size, $max, $value, $title, $params, $post_label, $submit_on_change, $readonly, $id);

    echo "\n";
}
function toatl_batch_term__list2($name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{

	$sql = "SELECT floor_aisle,floor_aisle FROM fa_floor_aisle WHERE floor_id =". db_escape($_POST['fl_num']);
	
	return combo_input($name, $selected_id, $sql, 'floor_aisle', 'floor_aisle',
 	array('order'=>'floor_id',
		'spec_option' => $spec_opt,
		'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
 		'async' => false
 	)); 
	
       
}


function total_batch_term_list_cells2($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo toatl_batch_term__list2($name, $selected_id, $spec_opt, $submit_on_change);
	echo "</td>\n";
}

function total_batch_term_list_row2($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	total_batch_term_list_cells2(null, $name, $selected_id, $spec_opt, $submit_on_change);
	echo "</tr>\n";
}

//////////////////////////////////2-07-19///////////////////////////////
function total_self_list2($name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{

    $sql = "SELECT self_code,self_code FROM fa_self WHERE floor_aisle =". db_escape($_POST['aisle_code']);

    return combo_input($name, $selected_id, $sql, 'self_code', 'self_code',
    array('order'=>'id',
        'spec_option' => $spec_opt,
        'spec_id' => -1,
        'select_submit'=> $submit_on_change,
        'async' => false
    )); 
    
       
}


function total_self_list_cells2($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo total_self_list2($name, $selected_id, $spec_opt, $submit_on_change);
    echo "</td>\n";
}

function total_self_list_row2($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    echo "<tr><td class='label'>$label</td>";
    total_self_list_cells2(null, $name, $selected_id, $spec_opt, $submit_on_change);
    echo "</tr>\n";
}
//8-05-19//////////////////////////////////////////////////////////////



function add_auth($author, $author_code){
    
   // $date1 = str_replace('/', '-', $published_date);
    $date1 = explode(' ', $published_date);
    $date=date('Y-m-d', strtotime($date1[0]));
    $IP_address=$_SESSION['IPaddress'];
    $entered_by=$_SESSION['wa_current_user']->name;
    
    $sql = "INSERT INTO " . TB_PREF . "author_details (auth_name,auth_code) VALUES (" . db_escape($author) . "," . db_escape($author_code).")";

    
    db_query($sql, "The sales department could not be added");
}

function add_pub($publisher, $publisher_code){
    
   // $date1 = str_replace('/', '-', $published_date);
    $date1 = explode(' ', $published_date);
    $date=date('Y-m-d', strtotime($date1[0]));
    $IP_address=$_SESSION['IPaddress'];
    $entered_by=$_SESSION['wa_current_user']->name;
    
    $sql = "INSERT INTO " . TB_PREF . "publisher (pub_name,pub_code) VALUES (" . db_escape($publisher) . "," . db_escape($publisher_code).")";

    
    db_query($sql, "The sales department could not be added");
}
function add_Boolks($ISBN, $title, $category, $author, $publisher, $published_date, $edition, $copies_no, $book_cost, $copyright_year, $status){
    
   // $date1 = str_replace('/', '-', $published_date);
    $date1 = explode(' ', $published_date);
    $date=date('Y-m-d', strtotime($date1[0]));
    $IP_address=$_SESSION['IPaddress'];
    $entered_by=$_SESSION['wa_current_user']->name;
    
    $sql = "INSERT INTO " . TB_PREF . "books (ISBN, title, category, author, publisher, published_date, edition, copies_no, book_cost, copyright_year, status, entered_by, IP_address) VALUES (" . db_escape($ISBN) . "," . db_escape($title)."," . db_escape($category)."," . db_escape($author)."," . db_escape($publisher)."," . db_escape($date)."," . db_escape($edition)."," . db_escape($copies_no)."," . db_escape($book_cost)."," . db_escape($copyright_year)."," . db_escape($status)."," . db_escape($entered_by)."," . db_escape($IP_address).")";

    
    db_query($sql, "The sales department could not be added");
}
function update_books($selected_id, $ISBN, $title, $category, $author, $publisher, $published_date, $edition, $copies_no, $book_cost, $copyright_year, $status){
    
    //$IP_address=$_SESSION['IPaddress'];
    $entered_by=$_SESSION['wa_current_user']->name;
    $date=date('Y-m-d H:i:s');
	//$date1 = str_replace('/', '-', $published_date);
    $date1 = explode(' ', $published_date);
    $published_date1=date('Y-m-d', strtotime($date1[0]));
    $sql = "UPDATE " . TB_PREF . "books  SET ISBN=" . db_escape($ISBN) . ",title=" . db_escape($title). ",category=" . db_escape($category) . ", author=" . db_escape($author) . " , publisher=" . db_escape($publisher) . ", published_date=" . db_escape($published_date1) . " , edition=" . db_escape($edition) . ", copies_no=" . db_escape($copies_no) . ", book_cost=" . db_escape($book_cost) . ", copyright_year=" . db_escape($copyright_year) . ", status=" . db_escape($status) . ", modified_by=" . db_escape($entered_by) . ", modified_date=" . db_escape($date) . " WHERE ISBN = " . db_escape($selected_id);
   
    db_query($sql, "The sales department could not be updated");
}


function update_pub($selected_id, $publisher,$pub_code,$status){
    
    //$IP_address=$_SESSION['IPaddress'];
    $entered_by=$_SESSION['wa_current_user']->name;
    $date=date('Y-m-d H:i:s');
	//$date1 = str_replace('/', '-', $published_date);
    $date1 = explode(' ', $published_date);
    $published_date1=date('Y-m-d', strtotime($date1[0]));
    $sql = "UPDATE " . TB_PREF . "publisher  SET pub_name=" . db_escape($publisher) . ",pub_code=" . db_escape($pub_code). ",status=" . db_escape($status) . " WHERE pub_id = " . db_escape($selected_id);
   
    db_query($sql, "The sales department could not be updated");
}
function update_auth($selected_id, $author,$auth_code,$status){
    
    //$IP_address=$_SESSION['IPaddress'];
    $entered_by=$_SESSION['wa_current_user']->name;
    $date=date('Y-m-d H:i:s');
	//$date1 = str_replace('/', '-', $published_date);
    $date1 = explode(' ', $published_date);
    $published_date1=date('Y-m-d', strtotime($date1[0]));
    $sql = "UPDATE " . TB_PREF . "author_details  SET auth_name=" . db_escape($author) . ",auth_code=" . db_escape($auth_code). ",status=" . db_escape($status) . " WHERE auth_id = " . db_escape($selected_id);
   
    db_query($sql, "The sales department could not be updated");
}


function update_issuebooks($user_id,$username,$ISBN, $title, $copies_no, $author, $publisher, $edition, $hold_date=NULL, $issueReqId, $direct_issue,$issue_date){
    
     //display_error($direct_issue);
                            //die();
     $dt_issue=date('Y-m-d');
     $status = '1';
    //$IP_address=$_SESSION['IPaddress'];
    $entered_by=$_SESSION['wa_current_user']->name;
    $date=date('Y-m-d H:i:s');
	//$date1 = str_replace('/', '-', $published_date);
    $date1 = explode(' ', $published_date);
    $published_date1=date('Y-m-d', strtotime($date1[0]));
   $sql = "INSERT INTO user_holdbook (user_id,username, ISBN, book_title, copies_no, author, publisher, edition, issueReqId, direct_issue,bookIssueDate,bookReturndDate,status) VALUES (" . db_escape($user_id) . " ," . db_escape($username) . "," . db_escape($ISBN) . "," . db_escape($title)."," . db_escape($copies_no)."," . db_escape($author)."," . db_escape($publisher).",". db_escape($edition)."," . db_escape($issueReqId)."," . db_escape($direct_issue)."," . db_escape($dt_issue)."," . db_escape($issue_date)."," . db_escape($status).")";

   
    db_query($sql, "The sales department could not be updated");
}


function update_issuebooks1($user_id,$emp,$username,$ISBN, $title, $copies_no, $author, $publisher, $edition, $hold_date=NULL, $issueReqId, $direct_issue,$issue_date){
    
     //display_error($direct_issue);
                            //die();
     $dt_issue=date('Y-m-d');
     $status = '1';
    //$IP_address=$_SESSION['IPaddress'];
    $entered_by=$_SESSION['wa_current_user']->name;
    $date=date('Y-m-d H:i:s');
	//$date1 = str_replace('/', '-', $published_date);
    $date1 = explode(' ', $published_date);
    $published_date1=date('Y-m-d', strtotime($date1[0]));
   $sql = "INSERT INTO user_holdbook (user_id,empl_id,username,ISBN,book_title,copies_no,author,publisher,edition,issueReqId, direct_issue,bookIssueDate,bookReturndDate,status) VALUES (" . db_escape($user_id) . " ," . db_escape($emp) . "," . db_escape($username) . "," . db_escape($ISBN) . "," . db_escape($title)."," . db_escape($copies_no)."," . db_escape($author)."," . db_escape($publisher).",". db_escape($edition)."," . db_escape($issueReqId)."," . db_escape($direct_issue)."," . db_escape($dt_issue)."," . db_escape($issue_date)."," . db_escape($status).")";

   
    db_query($sql, "The sales department could not be updated");
}
function getBookList($status){
   $status=1;
    $sql = "SELECT * from " . TB_PREF . "books JOIN ".TB_PREF."book_category ON ". TB_PREF . "books.category = ".TB_PREF."book_category.id where status =" . db_escape($status)." order by ". TB_PREF . "books.book_id desc";
   
    return $sql ;db_query($sql, "could not get designation_master");
}

function getAuthorList($status){
   $status=1;
    $sql = "SELECT * from " . TB_PREF . "author_details where status =" . db_escape($status);
   
    return $sql ;db_query($sql, "could not get designation_master");
}
function get_auth_list($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "author_details WHERE auth_id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}

function getPublisherList($status){
   $status=1;
    $sql = "SELECT * from " . TB_PREF . "publisher where status =" . db_escape($status);
   
    return $sql ;db_query($sql, "could not get designation_master");
}
function get_pub_list($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "publisher WHERE pub_id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}
function get_book_list($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "books WHERE ISBN=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}
function add_bookCategory($title, $status){
    $sql = "INSERT INTO " . TB_PREF . "book_category (category_name,cat_status) VALUES (" . db_escape($title) . "," . db_escape($status) . ")";
    
    db_query($sql, "The sales department could not be added");
}
function update_bookCategory($selected_id,$title, $status){
    $sql = "UPDATE " . TB_PREF . "book_category  SET category_name=" . db_escape($title) . ",cat_status=" . db_escape($status). " WHERE id = " . db_escape($selected_id);
   
    db_query($sql, "The sales department could not be updated");
}
function getBookCategoryInfo($status){
    $status=1;
    $sql = "SELECT * from " . TB_PREF . "book_category where cat_status = ". db_escape($status);
    return $sql ;db_query($sql, "could not get designation_master");
}
function get_bookCategory_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "book_category WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    
    return db_fetch($result);
}
function add_checkoutPolicy($ref_id, $checkout_policy, $no_day){
    $sql = "INSERT INTO " . TB_PREF . "allowed_book (ref_id,checkout_policy,no_book) VALUES (" . db_escape($ref_id) . "," . db_escape($checkout_policy) . "," . db_escape($no_day).")";
    
    db_query($sql, "The sales department could not be added");
}
function update_checkoutPolicy($selected_id, $ref_id, $checkout_policy, $no_day){
    $sql = "UPDATE " . TB_PREF . "allowed_book  SET ref_id=" . db_escape($ref_id) . ",checkout_policy=" . db_escape($checkout_policy). ",no_book=" . db_escape($no_day) . " WHERE id = " . db_escape($selected_id);
    
    db_query($sql, "The sales department could not be updated");
}
function getCheckoutPolicyInfo($status){
    $status=0;
    $sql = "SELECT * from " . TB_PREF . "allowed_book where status =" . db_escape($status);
    return $sql ;db_query($sql, "could not get designation_master");
}


function getIssueBookList($sid){
    $sql = "SELECT * from fa_allowed_book where ref_id='$sid'";
    $user_data =  db_query($sql);
    $user_result = db_fetch($user_data);
    //display_error($user_result['no_book']);
    return  $user_result;
   
}

function getIssueBookList2($sid){
    $sql = "SELECT * from user_holdbook where user_id='$sid' and direct_issue ='0' and status!='2'";
     //display_error($sql);
    $user_data =  db_query($sql);
    $user_result = db_num_rows($user_data);
    //display_error($user_result);
    return  $user_result;
   
}

function getIssueBookList3($sid){
    $sql = "SELECT * from user_holdbook where user_id='$sid' and direct_issue ='0' and status!='2' and empl_id!=''";
     //display_error($sql);
    $user_data =  db_query($sql);
    $user_result = db_num_rows($user_data);
    //display_error($user_result);
    return  $user_result;
   
}


function get_checkoutPolicy_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "allowed_book WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    
    return db_fetch($result);
}
function add_Floor($floor_id, $fl_num){
    $sql = "INSERT INTO " . TB_PREF . "book_location (floor_id,fl_num) VALUES (" . db_escape($floor_id) . "," . db_escape($fl_num).")";
    
    db_query($sql, "The sales department could not be added");
} 
function update_FloorNo($selected_id, $fl_num){
    $sql = "UPDATE " . TB_PREF . "book_location  SET fl_num=" . db_escape($fl_num). " WHERE id = " . db_escape($selected_id);
    
    db_query($sql, "The sales department could not be updated");
}
function getBookFloorInfo($status){
    $status=0;
    $sql = "SELECT * from " . TB_PREF . "book_location where status =" . db_escape($status);
    return $sql ;db_query($sql, "could not get designation_master");
}
function get_bookfloor_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "book_location WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    
    return db_fetch($result);
}

 function get_bookCopies($show_inactive,$isbn){
	$sql="SELECT * FROM ".TB_PREF."books_copies Where ISBN LIKE ".db_escape($isbn);
	if ($show_inactive!=1)
		$sql .= " AND status = 0 ";
	 //$sql .= " order by emp.empl_firstname,leaves.cal_year";
	return db_query($sql,"could not get Leaves master");
}

function issue_holdbook($selected_id, $status, $issue_date){
    set_global_connection(0);
    $dt_issue=date('Y-m-d');
    $sql = "UPDATE user_holdbook  SET status=" . db_escape($status). ", bookIssueDate=" . db_escape($dt_issue). ", bookReturndDate=" . db_escape($issue_date). " WHERE id = " . db_escape($selected_id);
    db_query($sql, "The sales department could not be updated");
}



function ext_holdbook($selected_id,$issue_date){
    set_global_connection(0);
    $dt_issue=date('Y-m-d');
    $sql = "UPDATE user_holdbook  SET ext_date=" . db_escape($issue_date). " WHERE id = " . db_escape($selected_id);
    db_query($sql, "The sales department could not be updated");
}
function updateIssueCopyBook($copy_no,$isbn){
    set_global_connection(0);
    $status=1;
    $dt_issue=date('Y-m-d');
    $sql = "UPDATE fa_books_copies  SET issue=" . db_escape($status). " WHERE copies_no = " . db_escape($copy_no)."AND ISBN=". db_escape($isbn);
    
    db_query($sql, "The sales department could not be updated");
}
function checkoutholdbook_id($selected_id) {
    set_global_connection(0);
    $sql = "SELECT * FROM user_holdbook WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
   
    return db_fetch($result);
}


function getEXTBookList($selected_id) {
    set_global_connection(0);
    $sql = "SELECT * FROM user_holdbook WHERE user_id=" . db_escape($selected_id)." and ext_date != 'NULL'";
    $result = db_query($sql, "could not get department");
    return db_num_rows($result);
}

function getext($selected_id) {
    set_global_connection(0);
    $sql = "SELECT * FROM fa_ext_policy WHERE ref_id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}

function update_FineAmount($selected_id, $based_on, $amount){
    $sql = "UPDATE " . TB_PREF . "book_fine  SET based_on=" . db_escape($based_on). ",amount=" . db_escape($amount) . " WHERE id = " . db_escape($selected_id);
   
    db_query($sql, "The sales department could not be updated");
}
function getBookFineInfo($status){
    $status=0;
    $sql = "SELECT * from " . TB_PREF . "book_fine WHERE status=". db_escape($status);
    return $sql ;db_query($sql, "could not get designation_master");
}
function get_bookfine_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "book_fine WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    
    return db_fetch($result);
}
function add_FloorAsile($floor_id, $aisle_code,$desc){
    $sql = "INSERT INTO " . TB_PREF . "floor_aisle (floor_id,floor_aisle,aisle_desc) VALUES (" . db_escape($floor_id) . "," . db_escape($aisle_code)."," . db_escape($desc).")";
    
    db_query($sql, "The sales department could not be added");
}
function update_FloorAsile($selected_id, $fl_num,$aisle,$desc){
    $sql = "UPDATE " . TB_PREF . "floor_aisle  SET floor_id=" . db_escape($fl_num). ", floor_aisle=" . db_escape($aisle). " , aisle_desc=" . db_escape($desc). " WHERE id = " . db_escape($selected_id);
   
    db_query($sql, "The sales department could not be updated");
}
function getFloorAisleInfo($status){
    $status=0;
    $sql = "SELECT * from " . TB_PREF . "floor_aisle WHERE status=". db_escape($status);
    return $sql ;db_query($sql, "could not get designation_master");
}
function get_flooraisle_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "floor_aisle WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
     
    return db_fetch($result);
}
function add_holdBook($ref_id, $hold_book, $no_day){
    $sql = "INSERT INTO " . TB_PREF . "hold_book (ref_id,hold_book,no_book,no_day) VALUES (" . db_escape($ref_id) . "," . db_escape($hold_book) . "," . db_escape($no_book)."," . db_escape($no_day).")";
    
    db_query($sql, "The sales department could not be added");
}
function update_holdBook($selected_id, $ref_id, $hold_book, $no_book, $no_day){
    $sql = "UPDATE " . TB_PREF . "hold_book  SET ref_id=" . db_escape($ref_id) . ",hold_book=" . db_escape($hold_book). ",no_book=" . db_escape($no_book) . ", no_day=" . db_escape($no_day) . " WHERE id = " . db_escape($selected_id);
   
    db_query($sql, "The sales department could not be updated");
}
function getHoldBookInfo($status){
    $status=0;
    $sql = "SELECT * from " . TB_PREF . "hold_book where status =". db_escape($status);
    return $sql ;db_query($sql, "could not get designation_master");
}
function get_holdBook_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "hold_book WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
     
    return db_fetch($result);
}
function add_returnPolicy($ref_id, $return_policy, $no_day){
    $sql = "INSERT INTO " . TB_PREF . "return_policy (ref_id,return_policy,no_day) VALUES (" . db_escape($ref_id) . "," . db_escape($return_policy) . "," . db_escape($no_day).")";
    
    db_query($sql, "The sales department could not be added");
}
function update_returnPolicy($selected_id,$ref_id, $return_policy, $no_day){
    $sql = "UPDATE " . TB_PREF . "return_policy  SET ref_id=" . db_escape($ref_id) . ",return_policy=" . db_escape($return_policy). ",no_day=" . db_escape($no_day) . " WHERE id = " . db_escape($selected_id);
    db_query($sql, "The sales department could not be updated");
}
function getReturnPolicyInfo($status){
    $status ='0';
    $sql = "SELECT * from " . TB_PREF . "return_policy WHERE status =". db_escape($status);
    return $sql ;db_query($sql, "could not get designation_master");
}
function get_returnPolicy_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "return_policy WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}
function add_FineAmount($based_on, $amount){
    $sql = "INSERT INTO " . TB_PREF . "book_fine (based_on,amount) VALUES (" . db_escape($based_on) . "," . db_escape($amount).")";
    
    db_query($sql, "The sales department could not be added");
}
function return_book($selected_id, $status, $book_condition,$copy_no){
    set_global_connection(0);
	$date=date('Y-m-d');
    $sql = "UPDATE user_holdbook  SET status=" . db_escape($status). ", returnon=" . db_escape($date). ", book_condition=" . db_escape($book_condition). " WHERE id = " . db_escape($selected_id)." AND copies_no=" . db_escape($copy_no);
    db_query($sql, "The sales department could not be updated");
}
function add_Self($floor_id, $aisle_code,$self_desc,$self_code){
    $sql = "INSERT INTO " . TB_PREF . "self (floor_id,floor_aisle,self_desc,self_code) VALUES (" . db_escape($floor_id) . "," . db_escape($aisle_code)."," . db_escape($self_desc)."," . db_escape($self_code).")";
    
    db_query($sql, "The sales department could not be added");
}
function update_Self($selected_id, $fl_num,$aisle,$self_desc,$self_code){
    $sql = "UPDATE " . TB_PREF . "self  SET floor_id=" . db_escape($fl_num). ", floor_aisle=" . db_escape($aisle). ", self_desc=" . db_escape($self_desc). ", self_code=" . db_escape($self_code). " WHERE id = " . db_escape($selected_id);
    db_query($sql, "The sales department could not be updated");
}
function getSelfInfo($status){
    $status=0;
    $sql = "SELECT * from " . TB_PREF . "self where status =".db_escape($status);
    return $sql ;db_query($sql, "could not get designation_master");
}
function getTableInfo($tablename){
    $sql = "SELECT * from " . TB_PREF .$tablename;
    return $sql ;db_query($sql, "could not get designation_master");
}
function get_self_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "self WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}
function getCopyesNumber($isbn) {
    $sql = "SELECT copies_no FROM " . TB_PREF . "book_map WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return $sql ;db_query($sql, "could not get designation_master");
}
function getSelfDetailByid($id) {
    $sql = "SELECT * FROM " . TB_PREF . "book_map WHERE id=" . db_escape($id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}
function add_extPolicy($ref_id, $ext_policy, $no_day){
    $sql = "INSERT INTO " . TB_PREF . "ext_policy (ref_id,ext_policy,no_day) VALUES (" . db_escape($ref_id) . "," . db_escape($ext_policy) . "," . db_escape($no_day).")";
    db_query($sql, "The sales department could not be added");
}
function update_extPolicy($selected_id, $ref_id, $ext_policy, $no_day, $no_time){
    $sql = "UPDATE " . TB_PREF . "ext_policy  SET ref_id=" . db_escape($ref_id) . ",ext_policy=" . db_escape($ext_policy). ",no_day=" . db_escape($no_day) . ",no_time=" . db_escape($no_time) . " WHERE id = " . db_escape($selected_id);
    db_query($sql, "The sales department could not be updated");
}
function getExtensionsPolicyInfo($status=NULL){
    $status=0;
    $sql = "SELECT * from " . TB_PREF . "ext_policy WHERE status=". db_escape($selected_id);
    return $sql ;db_query($sql, "could not get designation_master");
}
function get_extPolicy_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "ext_policy WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}
function desig_group_listsNew($name, $selected_id=null,$spec_opt=false, $submit_on_change=false)
{
	$sql ="SELECT fl_num,fl_num FROM ".TB_PREF."book_location";
	return combo_input($name, $selected_id, $sql, 'fl_num', 'fl_num',
 	array('order'=>'floor_id',
		'spec_option' => $spec_opt,
		//'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
 		'async' => false
 	));
}
function desig_group_list_cellNew($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo desig_group_listsNew($name, $selected_id,$spec_opt, $submit_on_change);
	echo "</td>\n";
}
function desiggroup_list_rowNew($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	desig_group_list_cellNew(null, $name, $selected_id,$spec_opt, $submit_on_change);
	echo "</tr>\n";
}

 
function getcopies($isbn) {
$sql= "select * from fa_books_copies where ISBN =".db_escape($isbn)." AND status != 1 AND  hold != 1 AND  issue != 1 AND  req_copy != 1" ;
         $user_data = db_query($sql);
	 $user_result = db_fetch_row($user_data);
         return  $user_result[4];
}

function getcategory($isbn) {
$sql= "select * from fa_book_category Where cat_status=1 and id =".db_escape($isbn) ;
         $user_data = db_query($sql);
	 $user_result = db_fetch($user_data);
         //display_error($sql);
         return  $user_result[1];
}

function getauthor($isbn) {
$sql= "select * from fa_author_details Where status=1 and auth_id =".db_escape($isbn) ;
         $user_data = db_query($sql);
	 $user_result = db_fetch($user_data);
         //display_error($sql);
         return  $user_result[1];
}

function getpublisher($isbn) {
$sql= "select * from fa_publisher Where status=1 and pub_id =".db_escape($isbn) ;
         $user_data = db_query($sql);
	 $user_result = db_fetch($user_data);
         //display_error($sql);
         return  $user_result[2];
}


function getmaxissue() {
$sql= "select max(issueReqId) as maxId from user_holdbook where issueReqId Like 'IssueId-%'";
         $user_data = db_query($sql);
	 $user_result = db_fetch($user_data);
         //display_error($user_result);
         return  $user_result['maxId'];
}
function updatebookstatus($isbn,$copies_no,$status,$damage){
    set_global_connection(0);
   
    $sql = "UPDATE  fa_books_copies  SET damage=" . db_escape($damage). ", req_copy=" . db_escape(0). ", status=" . db_escape($status). ", issue=" . db_escape(0). " WHERE ISBN = " . db_escape($isbn)." AND copies_no = " . db_escape($copies_no);
    db_query($sql, "The sales department could not be updated");
}
function updatebookstatus2($isbn,$copies_no,$status,$damage){
    set_global_connection(0);
   
    $sql = "UPDATE  fa_books_copies  SET damage=" . db_escape($damage). ", req_copy=" . db_escape(0). ", status=" . db_escape($status). ", issue=" . db_escape(0). " WHERE ISBN = " . db_escape($isbn)." AND copies_no = " . db_escape($copies_no);
    db_query($sql, "The sales department could not be updated");
}
function reserve_book_column(&$th) {
    global $Ajax;

    if (check_value('show_inactive'))
        array_insert($th, count($th) - 0, _("Reserve"));
    if (get_post('_show_inactive_update')) {
        $Ajax->activate('_page_body');
    }
}
function select_book_copies(&$th) {
    global $Ajax;

    if (check_value('show_inactive'))
        array_insert($th, count($th) - 0, _("Select"));
    if (get_post('_show_inactive_update')) {
        $Ajax->activate('_page_body');
    }
}
function inactive_control_row_book($th) {
    echo "<tr><td colspan=" . (count($th)) . ">"
    . "<div style='float:left;'>"
    . checkbox(null, 'show_inactive', null, true) . _("Show Reserve Copies")
    . "</div><div style='float:right;'>"
    . submit('Update', _('Update'), false, '', null)
    . "</div></td></tr>";
}
function status_books_copies($id, $value, $table, $key,$isbn) {
    global $Ajax;

    $name = "Inactive" . $id;
    $value = $value ? 1 : 0;

    if (check_value('show_inactive')) {
        if (isset($_POST['LInact'][$id]) && (get_post('_Inactive' . $id . '_update') ||
                get_post('Update')) && (check_value('Inactive' . $id) != $value)) {
            update_copies_status($id, !$value, $table, $key,$isbn);
        }
        echo '<td align="center">' . checkbox(null, $name, $value, true, '')
        . hidden("LInact[$id]", $value, false) . '</td>';
    }
}
function update_copies_status($id, $status, $table, $key, $isbn) {
    $sql = "UPDATE ".TB_PREF.$table." SET status = "
        . ((int)$status)." WHERE $key=".db_escape($id)." AND ISBN=".db_escape($isbn);
    db_query($sql, "Can't update record status");
}
/////////////////////////////////Map Sell/////////////////////////////////////

function select_book_copies2(&$th) {
    global $Ajax;

    if (check_value('show_inactive'))
        array_insert($th, count($th) - 0, _("Select"));
    if (get_post('_show_inactive_update')) {
        $Ajax->activate('_page_body');
    }
}

function get_ALLbookCopiesNumber($show_inactive,$isbn){
    $sql="SELECT * FROM ".TB_PREF."books_copies Where ISBN LIKE ".db_escape($isbn)." AND damage != 2 AND  map = 0";
    return db_query($sql,"could not get Leaves master");
}
function get_ALLbookCopiesNumber23($show_inactive,$isbn,$floor,$aisel,$self){
    $sql="SELECT * FROM ".TB_PREF."book_map Where ISBN LIKE ".db_escape($isbn)." AND floor=". db_escape($floor)." AND aisel=". db_escape($aisel)." AND self=". db_escape($self);
    return db_query($sql,"could not get Leaves master");
}
function inactive_control_row_copies($th) {
    echo "<tr><td colspan=" . (count($th)) . ">"
    . "<div style='float:left;'>"
    . checkbox(null, 'show_inactive', null, true) . _("Show Book Copies")
    . "</div><div style='float:right;'>"
    . submit('Update', _('Update'), false, '', null)
    . "</div></td></tr>";
}

function inactive_control_row_copies2($th) {
    echo "<tr><td colspan=" . (count($th)) . ">"
    . "<div style='float:left;'>"
    . checkbox(null, 'show_inactive', null, true) . _("Show Book Copies")
    . "</div><div style='float:right;'>"
    . submit('Update', _('Update'), false, '', null)
    . "</div></td></tr>";
}
function insert_books_copies($id, $value, $table, $key,$isbn) {
    global $Ajax;

    $name = "Inactive" . $id;
    $value = $value ? 1 : 0;
    if (check_value('show_inactive')) {
       
        if (isset($_POST['LInact'][$id]) && (get_post('_Inactive' . $id . '_update') )) {
            insert_copies($id, !$value, $table, $key,$isbn);
        }
        echo '<td align="center">' . checkbox(null, $name, $value, true, '')
        . hidden("LInact[$id]", $value, false) . '</td>';
    }
}

function update_books_copies_Number33($id, $value, $table, $key,$isbn) {
    global $Ajax;

    $name = "Inactive" . $id;
    $value = $value ? 1 : 0;
    if (check_value('show_inactive')) {
       
        if (isset($_POST['LInact'][$id]) && (get_post('_Inactive' . $id . '_update') )) {
            update_books_copies_number($id, !$value, $table, $key,$isbn);
        }
        echo '<td align="center">' . checkbox(null, $name, $value, true, '')
        . hidden("LInact[$id]", $value, false) . '</td>';
    }
}
//function insert_copies() {
function insert_copies($id, $status, $table, $key, $isbn) {
        $sql1 = "UPDATE ".TB_PREF. "books_copies SET map = 1 WHERE copies_no=".db_escape($id)." AND ISBN=".db_escape($isbn);

        db_query($sql1, "Can't update record status");

        $category=$_POST['category'];
        $isbn=$_POST['isbn'];
        $copiesno=$id;
        $title=$_POST['title'];
        $fl_num=$_POST['fl_num'];
        $aisle_code=$_POST['aisle_code'];
        $self_code=$_POST['self_code'];
        $author=$_POST['author'];
        $sql = "INSERT INTO " . TB_PREF . "book_map (cat_id,ISBN, copies_no,title,author, floor, aisel,self) VALUES (" . db_escape($category) . "," . db_escape($isbn) . "," . db_escape($copiesno)."," . db_escape($title)."," . db_escape($author)."," . db_escape($fl_num)."," . db_escape($aisle_code)."," . db_escape($self_code).")";
        db_query($sql, "Can't update record status");

        $Ajax->activate('_page_body');
  
}

 function update_books_copies_number($id, $status, $table, $key, $isbn) {

        $sql = "DELETE FROM " . TB_PREF . "book_map WHERE copies_no=" . db_escape($id)." AND ISBN=".db_escape($isbn);
        db_query($sql, "check failed");

        $sql1 = "UPDATE ".TB_PREF. "books_copies SET map = 0 WHERE copies_no=".db_escape($id)." AND ISBN=".db_escape($isbn);
        db_query($sql1, "check failed");

        $Ajax->activate('_page_body');
  
 }


function cron($back_date){
    set_global_connection(2);
     $sql = "SELECT * FROM user_holdbook WHERE status = 0 AND hold_date='$back_date'";
   
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}

function cron2($back_date){
    set_global_connection(2);
    $sql = "SELECT * FROM user_holdbook WHERE status = 0 AND bookIssueDate='$back_date'";
    
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}

function book_author_list_cells12($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo book_author_list12($name, $selected_id, $spec_opt, $submit_on_change);
    echo "</td></tr>\n";
}
function book_author_list12($name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    $sql = "SELECT auth_id,auth_name FROM ".TB_PREF."author_details Where status=1";
    return combo_input($name, $selected_id, $sql, 'id', 'auth_name',
    array('order'=>'auth_name',
        'spec_option' => $spec_opt,
        'spec_id' => -1,
        'select_submit'=> $submit_on_change,
        'async' => true
    ));
}
function book_publisher_list_cells12($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo book_publisher_list12($name, $selected_id, $spec_opt, $submit_on_change);
    echo "</td></tr>\n";
}


function book_publisher_list12($name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    $sql = "SELECT pub_id,pub_name FROM ".TB_PREF."publisher Where status=1";
    return combo_input($name, $selected_id, $sql, 'id', 'pub_name',
    array('order'=>'pub_name',
        'spec_option' => $spec_opt,
        'spec_id' => -1,
        'select_submit'=> $submit_on_change,
        'async' => true
    ));
}

function book_category_list_cells12($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo book_categoty_list12($name, $selected_id, $spec_opt, $submit_on_change);
    echo "</td></tr>\n";
}


function book_categoty_list12($name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    $sql = "SELECT * FROM ".TB_PREF."book_category Where cat_status=1";
    return combo_input($name, $selected_id, $sql, 'id', 'description',
    array('order'=>'category_name',
        'spec_option' => $spec_opt,
        'spec_id' => -1,
        'select_submit'=> $submit_on_change,
        'async' => true
    ));
}
function student_list_cells12($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo student_list($name, $selected_id, $spec_opt, $submit_on_change);
    echo "</td>\n";
}


function student_list($name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    
      set_global_connection(2);
//		$sql1 = "SELECT * FROM participants_login WHERE user_id=".$myrow['user_id'];
//		$user_data = db_query($sql1);
//		$user_result = db_fetch_row($user_data);
//		$username=$user_result[1];
    $sql = "SELECT user_id,roll_no FROM participants_login";
    return combo_input($name, $selected_id, $sql, 'id', 'description',
    array('order'=>'user_id',
        'spec_option' => $spec_opt,
        'spec_id' => -1,
        'select_submit'=> $submit_on_change,
        'async' => false
    ));
      set_global_connection(0);
}


function staff_list_cells($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo staff_list($name, $selected_id, $spec_opt, $submit_on_change);
    echo "</td>\n";
}


function staff_list($name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    
      set_global_connection(0);
//		$sql1 = "SELECT * FROM participants_login WHERE user_id=".$myrow['user_id'];
//		$user_data = db_query($sql1);
//		$user_result = db_fetch_row($user_data);
//		$username=$user_result[1];
    $sql = "SELECT * FROM fa_users";
    return combo_input($name, $selected_id, $sql, 'id', 'description',
    array('order'=>'user_id',
        'spec_option' => $spec_opt,
        'spec_id' => -1,
        'select_submit'=> $submit_on_change,
        'async' => true
    ));
    
}

function user_list($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<tr><td>$label</td>\n";
    echo "<td>";
    echo "<select name='user_type'><option value='1'> Student</option> <option value='2'> Staff</option></select>";
    echo "</td></tr>\n";
}
function book_isbn_list_cells12($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo book_isbn_list12($name, $selected_id, $spec_opt, $submit_on_change);
    echo "</td>\n";
}
function book_isbn_list12($name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    $sql = "SELECT ISBN,ISBN FROM ".TB_PREF."books Where category=".db_escape($_POST['category'])." AND status=1";
    return combo_input($name, $selected_id, $sql, 'ISBN', 'ISBN',
    array('order'=>'book_id',
        'spec_option' => $spec_opt,
        'spec_id' => -1,
        'select_submit'=> $submit_on_change,
        'async' => true
    ));
}
function book_d1($isbn) {
    $sql = "SELECT title,author FROM " . TB_PREF . "books WHERE ISBN=" . db_escape($isbn);
   
    $result = db_query($sql, "could not get department");
    return db_fetch($result);
}
function book_copies_list($name, $selected_id = null,$spec_opt = false, $submit_on_change = false,$height = false ) {
   $sql = "SELECT copies_no,copies_no FROM ".TB_PREF."books_copies where ISBN=". db_escape($_POST['isbn'])." AND damage!=2";
    return combo_input($name, $selected_id, $sql, 'copies_no', 'copies_no',
    array('order'=>'id',
        'spec_option' => FALSE,
                'height' => $height,
        'select_submit'=> $submit_on_change,
                'multi'=> true,
        'async' => true
    ));
}



function book_copies_list_cell($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false,$gender='',$height = false)
{ 
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo book_copies_list($name, $selected_id,$spec_opt, $submit_on_change,$height);
    echo "</td></tr>\n";
}

function book_copies_list_row($label, $name, $selected_id=null,$spec_opt=false, $submit_on_change=false,$gender='',$height = false)
{
   
    echo "<td class='label'>$label</td>";
    book_copies_list_cell(null, $name, $selected_id,$spec_opt, $submit_on_change,$gender,$height);

}
function self_row($label, $name, $value, $size, $max, $title = null, $params = "", $post_label = "", $submit_on_change = false, $readonly = true) {
    echo "<td class=''>$label</td>";
    text_cells(null, $name, $value, $size, $max, $title, $params, $post_label, $submit_on_change, $readonly);

}

function updateSelfmap($selected_id, $category, $isbn, $copies_no, $title, $fl_num,$aisle_code,$self_code){
     $sql = "UPDATE " . TB_PREF . "book_map  SET cat_id=" . db_escape($category) . ",ISBN=" . db_escape($isbn). ",copies_no=" . db_escape(implode(",",$copies_no)) . ", title=" . db_escape($title) . " , floor=" . db_escape($fl_num) . ", aisel=" . db_escape($aisle_code) . " , self=" . db_escape($self_code) . "  WHERE ISBN = " . db_escape($selected_id);
   
    db_query($sql, "The sales department could not be updated");
}


function Getusername($uid){
      set_global_connection(2);	
                  $sql = "SELECT participant_fname,participant_lname FROM participants_login where user_id=$uid";
                           $result = db_query($sql);
                           $username = db_fetch($result);
                           return $user =  $username['participant_fname'].''.$username['participant_lname'];
}

function Getusername1($uid){
      set_global_connection(0);	
                  $sql = "SELECT * FROM fa_users where id=$uid";
           
                           $result = db_query($sql);
                           $username = db_fetch($result);
                           $user =  $username;
                           return $user;
                        
}
function getstudents($rollid) {
    set_global_connection(2);
$sql= "select * from participants_login where roll_no=".db_escape($rollid) ;
         $user_data = db_query($sql);
        
	 $user_result = db_fetch($user_data);
         // display_error($user_result['user_id']);
         return  $user_result;
}

?>