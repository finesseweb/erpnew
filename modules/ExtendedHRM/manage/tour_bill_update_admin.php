<?php
$page_security = 'SA_OPEN';
$path_to_root = "../../..";
$tour_request_document_folder = 'tour_request_attachments';
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");

//page(_($help_context = "Allocation Request")); 
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_tour_db.inc");

//echo "<pre>";
//print_r($_SESSION);
$js = '';

if ($version_id['version_id'] == '2.4.1') {
    if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);

    if (user_use_date_picker())
        $js .= get_js_date_picker();
}else {
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
}
?>
<?php 

/*if($_POST['delete_id']){
	$id=$_POST['delete_id'];
	$delete_query="DELETE FROM `fa_kv_tour_requestform` WHERE `id` = $id";
	$delete_data = db_query($delete_query);
}*/

	$logged_in_user_id = $_SESSION["wa_current_user"]->empl_id;//Getting current system user id
    $sql = "UPDATE " . TB_PREF . "kv_tour_requestform SET  updated_amount=".db_escape($_POST['amount_val']) .", admin_remark=".db_escape($_POST['remark']) ." WHERE tour_id = " . db_escape($_POST['tour_id_val'])."AND id = ".db_escape($_POST['id_val']);
    $rs = db_query($sql, "Can't update tour request id");

?>
