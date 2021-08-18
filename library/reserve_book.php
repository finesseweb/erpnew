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
include_once($path_to_root . "/library/function/function.php");

include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
if (!@$_GET['popup'])
	page(_($help_context = "Reserve Book List"));

?>
<html lang="en">
    <head>
        <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js" ?>"></script>
    </head>
</html>
<?php

if ($Mode == 'RESET') {
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    $_POST['show_inactive'] = $sav;
}
//-----------------------------------------------------------------------------------

$result = get_bookCopies(check_value('show_inactive'),$isbn);
start_form();
start_table(TABLESTYLE);
$th = array( _("ISBN"),_("Book Title"), _("Category"), _("Copyies"),/* _("No. of VL's"), _("No. of Medical Leaves"),_("No. of Earned Leaves"), _("No. of S.CL (Male)"), _("No. of S.CL (Female)"), _("No. of Maternity Leaves"), _("No. of Paternity Leaves"),_("Eligible (CL)"), _("Eligible (VL)"), _("Eligible (ML)"),_("Eligible (EL)"), _("Eligible (SPL)"), _("Eligible (SPL Female)"), _("Eligible (MAT)"), _("Eligible (PAT)"),*/ '', '',);
reserve_book_column($th);
table_header($th);

$k = 0;
while ($myrow = db_fetch($result)) {

    alt_table_row_color($k);
    label_cell($myrow["ISBN"]);
    label_cell($myrow["title"]);
    label_cell($myrow["category"]);
    label_cell($myrow["copies_no"]);
    status_books_copies($myrow["copies_no"], $myrow["status"], 'books_copies', 'copies_no',$isbn);
    end_row();
}
inactive_control_row($th);
end_table();
echo '<br>';
end_form();

//------------------------------------------------------------------------------------

end_page();
?>
<!-- <p style='padding: 3px;text-align:center;'> <a href='javascript:goBack();'>Back</a></p> -->

<script>
$(document).ready(function() {		
	$(".no_of_cls_date").datepicker({ dateFormat: 'dd-mm-yy' });     
	$(".no_of_pls_date").datepicker({ dateFormat: 'dd-mm-yy' });  
        $(".no_of_medical_ls_date").datepicker({ dateFormat: 'dd-mm-yy' });  
        $(".no_of_el_date").datepicker({ dateFormat: 'dd-mm-yy' });  
})
</script>