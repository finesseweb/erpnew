<?php
$page_security = 'SA_ATTENDANCEHOURS';
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
include_once($path_to_root . "/attendance/function/function.php");

include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );


if (!@$_GET['popup'])
  page(_($help_context = "Set Working Hours"));

simple_page_mode(true);
?>
<?php

//-----------------------------------------------------------------------------------

if ($Mode == 'UPDATE_ITEM') {
			
    $result = '';
    update_workingHours($selected_id, $_POST['full_day'], $_POST['half_day']);
    $Mode = 'RESET';
    $update_pager = true;
    display_notification(_('Record has been updated'));
			
		
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

    delete_guest($selected_id);
   
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
$sql = get_hours_list(1);
$_SESSION['fun']['id'] = 0;
$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("Full day hour") => array('align' => 'center'),
            _("Half day hour") => array('align' => 'center'),
            _("Edit") => array('align' => 'center', 'fun' => 'edit'),
);
$table = &new_db_pager('workinghours', $sql, $cols);

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

function edit($row) {
    return edit_button_cell("Edit" .$row['id'], _("Edit"));
}


//inactive_control_row($th);
end_table();
echo '<br>';


//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) {
    if ($Mode == 'Edit') {
		//display_error($selected_id);
        $myrow = get_hours_list_Id($selected_id);
        $_POST['full_day'] = $myrow["full_day"];
        $_POST['half_day'] = $myrow["half_day"];
        
        table_section_title(_("Add Working Hours "));
        text_row(_('Full Day'), 'full_day', $_POST['full_day'], 30);
        text_row(_('Half'), 'half_day', $_POST['half_day'], 30);
    }
    hidden('selected_id', $selected_id);


end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');
}
end_form();



end_page();
?>


