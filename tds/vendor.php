<?php
$page_security = 'SA_ADDTDS';
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
include_once($path_to_root . "/tds/function/function.php");

include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
if (!@$_GET['popup'])
	page(_($help_context = "Vendor Categories"));
simple_page_mode(true);
?>

<?php

function can_process() {
	
	$regexno = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";
	if (strlen($_POST['vendor_type']) == 0) {
        display_error(_("Vendor type can not be empty."));
        set_focus('vendor_type');
        return false;
    }
	
	if (strlen($_POST['cumulative_payment']) == 0) {
        display_error(_("Cumulative Payment can not be empty."));
        set_focus('cumulative_payment');
        return false;
    }

    if (strlen($_POST['single_payment']) == 0) {
        display_error(_("Single Payment can not be empty."));
        set_focus('single_payment');
        return false;
    }
    
    if (strlen($_POST['percentage']) == 0) {
        display_error(_("percentage can not be empty."));
        set_focus('percentage');
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
			
                            add_vendor($_POST['cat_id'],$_POST['fascial_year'],$_POST['vendor_type'],$_POST['cumulative_payment'],$_POST['single_payment'],$_POST['percentage'],$_POST['status']);
                            
                            display_notification(_('New Record  has been added'));
                            $Mode = 'RESET';
                            $update_pager = true;
		} else {
			
				$result = '';
			    update_vendor($selected_id, $_POST['cat_id'],$_POST['fascial_year'],$_POST['vendor_type'],$_POST['cumulative_payment'],$_POST['single_payment'],$_POST['percentage'],$_POST['status']);

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
$sql = getVendorInfo($status);

$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("Vendor Categories ID.") => array('align' => 'center'),
            _("Fascial Year") => array('align' => 'center','fun'=>'fascial_year'),
            _("Vendor Type") => array('align' => 'center'),
            _("Cumulative Payment (Max. Amt.)") => array('align' => 'center'),
            _("Single Payment (Max. Amt.)") => array('align' => 'center'),
            _("% Value") => array('align' => 'center'),
            _("Status") => array('align' => 'center', 'fun' => 'status'),
            _("Edit") => array('align' => 'center', 'fun' => 'edit'),
);
$table = &new_db_pager('vendor_details', $sql, $cols);

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
function status($row) {
	if($row['status']==1){
		$status='Active';
	}elseif($row['status']==2){
		$status='Deactive';
	}
    return $status;
}
function fascial_year($row) {
	$sql12 = "SELECT * FROM fa_fiscal_year WHERE id=". "'".$row['fascial_year']."'";
    $user_data1 = db_query($sql12);
    $user_result1 = db_fetch_row($user_data1);
	
	return  sql2date($user_result1[1]).'-'.sql2date($user_result1[2]);
}
function edit($row) {
    return edit_button_cell("Edit" . $row['id'], _("Edit"));
}



//inactive_control_row($th);
end_table();
echo '<br>';


//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) {
    if ($Mode == 'Edit') {

        $myrow = get_vendor_id($selected_id);
        $_POST['cat_id'] = $myrow["cat_id"];
        $_POST['fascial_year'] = $myrow["fascial_year"];
        $_POST['vendor_type'] = $myrow["vendor_type"];
        $_POST['cumulative_payment'] = $myrow["cumulative_payment"];
        $_POST['single_payment'] = $myrow["single_payment"];
        $_POST['percentage'] = $myrow["percentage"];
        $_POST['status'] = $myrow["status"];
    }
    hidden('selected_id', $selected_id);
}

if ($Mode != 'Edit') {
    $sql1 = "SELECT MAX(cat_id) FROM fa_vendor_details WHERE cat_id LIKE 'vendor-%'";

    $empl_data = db_query($sql1);
    $empid_result = db_fetch_row($empl_data);

    //$last_max_emp_id = $empid_result[0];
	
    $last_emp1 = substr($empid_result[0], 7);

    $emp_inc_id = $last_emp1 + 1;
    if (strlen($emp_inc_id) == 1) {
        $_POST['cat_id'] = 'vendor-00' . $emp_inc_id;
    } else if (strlen($emp_inc_id) == 2) {
        $_POST['cat_id'] = 'vendor-0' . $emp_inc_id;
    } else {
        $_POST['cat_id'] = 'vendor-' . $emp_inc_id;
    }
}


hidden('cat_id', $_POST['cat_id']);
	
table_section_title(_("Vendor Details"));
label_row(_("Vendor Categories ID"), $_POST['cat_id']);
kv_fiscalyears_list_cells(_("Fiscal Year:"), 'fascial_year', null, true);
text_row(_('Vendor Type'), 'vendor_type', $_POST['vendor_type'], 30,50);
text_row(_('Cumulative Payment (Amt.)'), 'cumulative_payment', $_POST['cumulative_payment'], 30,50);
text_row(_('Single Payment (Amt.)'), 'single_payment', $_POST['single_payment'], 30,50);
text_row(_('Percentage (%)'), 'percentage', $_POST['percentage'], 30,50);
custom_list_row(_("Status"), 'status', null, TRUE, false, 'status');
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();
?>


