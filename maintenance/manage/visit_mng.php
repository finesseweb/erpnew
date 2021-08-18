<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/visitor/function.php");

include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );


$version_id = get_company_prefs('version_id');

$state_id = "1479";
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
//page(_($help_context = "Visitor Management", @$_REQUEST['popup'], false, "", $js));
page(_($help_context = "Visitor Management"), @$_REQUEST['popup'], false, "", $js);
if (isset($_GET['vw']))
    $view_id = $_GET['vw'];
else
    $view_id = find_submit('view');
if ($view_id != -1) {
    $row = get_employee_cv($view_id);
    if ($row['filename'] != "") {
        if (in_ajax()) {
            $Ajax->popup($_SERVER['PHP_SELF'] . '?vw=' . $view_id);
        } else {
            $type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';
            header("Content-type: " . $type);
            header('Content-Length: ' . $row['filesize']);
            //if ($type == 'application/octet-stream')
            //	header('Content-Disposition: attachment; filename='.$row['filename']);
            //else
            header("Content-Disposition: inline");
            echo file_get_contents(company_path() . "/attachments/emplcv/" . $row['unique_name']);
            exit();
        }
    }
}

if (isset($_GET['dl']))
    $download_id = $_GET['dl'];
else
    $download_id = find_submit('download');

if ($download_id != -1) {

    $row = get_employee_cvdwnld($download_id);

    if ($row['filename'] != "") {
        if (in_ajax()) {
            $Ajax->redirect($_SERVER['PHP_SELF'] . '?dl=' . $download_id);
        } else {

            $type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';
            header("Content-type: " . $type);
            header('Content-Length: ' . $row['filesize']);
            header('Content-Disposition: attachment; filename=' . $row['filename']);
            echo file_get_contents(company_path() . "/attachments/emplcv/" . $row['empl_id'] . "/" . $row['unique_name']);
            exit();
        }
    }
}
//page(_($help_context = "Help Desk"), @$_REQUEST['popup'], false, "", $js);
simple_page_mode(true);
?>

<?php

function can_process() {
	$value = $_POST['contact_number'];
	$mobileregex = "/^[6-9][0-9]{9}$/" ;  
	//display_error($value);
	//display_error(preg_match($mobileregex, $value));
	$regexno = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";
	if (!empty($_POST['contact_number'])) {
		//$regex = "/[0-9]{10}/";
		$regex = "/^[6-9][0-9]{9}$/";

		if(preg_match("/^\d+\.?\d*$/",$_POST['contact_number']) && strlen($_POST['contact_number'])!=10){
			display_error(_("Not allowed more than 10 digit in contact number."));
			set_focus('contact_number');
			return false;
		}
		/*if ((preg_match($regex, $_POST['contact_number'])) == 0) {
		display_error(_("Not allowed more than 10 digit in contact number."));
		set_focus('contact_number');
		return false;
		}*/
	}
	if (strlen($_POST['first_name']) == 0) {
		display_error(_("Visitor first name can not be empty."));
		set_focus('first_name');
		return false;
	}
	if (!empty($_POST['first_name'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['first_name']) != 0) {

			display_error(_("Special characters not allowed in  First Name ."));
			set_focus('first_name');
			return false;
		}
	}
	
	/*if (strlen($_POST['last_name']) == 0) {
		display_error(_("Visitor last name can not be empty."));
		set_focus('last_name');
		return false;
	}
	if (!empty($_POST['last_name'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['last_name']) != 0) {

			display_error(_("Special characters not allowed in  Last Name ."));
			set_focus('last_name');
			return false;
		}
	}
	*/
	if (strlen($_POST['to_meet']) == 0) {
		display_error(_("Visitor to meet can not be empty."));
		set_focus('to_meet');
		return false;
	}
	if (!empty($_POST['to_meet'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['to_meet']) != 0) {

			display_error(_("Special characters not allowed in To Meet Field ."));
			set_focus('to_meet');
			return false;
		}
	}
	
	if (strlen($_POST['company']) == 0) {
		display_error(_("Visitor company can not be empty."));
		set_focus('company');
		return false;
	}
	if (!empty($_POST['company'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['company']) != 0) {

			display_error(_("Special characters not allowed in Company ."));
			set_focus('company');
			return false;
		}
	}
	if (strlen($_POST['coming_from']) == 0) {
		display_error(_("Visitor coming from can not be empty."));
		set_focus('coming_from');
		return false;
	}
	if (!empty($_POST['coming_from'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['coming_from']) != 0) {

			display_error(_("Special characters not allowed in coming from ."));
			set_focus('coming_from');
			return false;
		}
	}
	
	if (strlen($_POST['purpose']) == 0) {
		display_error(_("Visitor purpose can not be empty."));
		set_focus('purpose');
		return false;
	}
	if (!empty($_POST['purpose'])) {
		$regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

		if (preg_match($regex, $_POST['purpose']) != 0) {

			display_error(_("Special characters not allowed in purpose ."));
			set_focus('purpose');
			return false;
		}
	}
	
    if (strlen($_POST['contact_number']) == 0) {
        display_error(_("Contact Number can not be empty."));
        set_focus('c_number');
        return false;
    }else if (preg_match($regexno, $_POST['contact_number']) != 0) {
		display_error(_("Contact number can accept Numericals only."));
		set_focus('c_number');
		return false;
	}
	
	/*$date = $_POST['tr_fromdate'];
    $curdate = date('Y-m-d');
	if (strlen($_POST['tr_fromdate']) == 0) {
        display_error('Recieved date column should not be empty !');
        set_focus('tr_fromdate');
        return false;
    } else if (strtotime($date) <> strtotime($curdate)) {
          display_error('Invalid date !');
        set_focus('tr_fromdate');
        return false;
    }
	
	$date = $_POST['tr_todate'];
    $curdate = date('Y-m-d');
	if (strlen($_POST['tr_todate']) == 0) {
        display_error('Recieved date column should not be empty !');
        set_focus('tr_todate');
        return false;
    } else if (strtotime($date) <> strtotime($curdate)) {
          display_error('Invalid date !');
        set_focus('tr_todate');
        return false;
    }*/
	
	
    if (!empty($_POST['email'])) {

        $regex = "/[^ ][a-zA-Z0-9._{4,}@[a-z]{5,}.[a-z]$/";
        if (preg_match($regex, $_POST['email']) != 0) {
            // $input_err = 0;
        } else {
            // $input_err = 1;
            display_error(_("Entered email is an invalid."));
            set_focus('email');
            return false;
        }
    }
    return true;
}

//-----------------------------------------------------------------------------------

if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') {
if(can_process()){
    if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '') {

        // $selected_id = $kv_empl_id;
        $result = $_FILES['pic']['error'];
        $upload_file = 'Yes'; //Assume all is well to start off with
        $filename = company_path() . '/images/dispatch/' . $_POST['sender_person'] . '/';

        //if(!isset($max_image_size))
        $max_image_size = 2048;

        if (!file_exists($filename)) {
            mkdir($filename);
        }
        $filename .= $_FILES['pic']['name'];
        $filename1 = $_FILES['pic']['name'];

        if ((list($width, $height, $type, $attr) = getimagesize($_FILES['pic']['tmp_name'])) !== false) {

            $imagetype = $type;
        } else
            $imagetype = false;

        //display_error($_FILES['pic']['size']);
        //display_error(strtoupper(substr(trim($_FILES['pic']['name']), strlen($_FILES['pic']['name']) - 3)));die;
        if (!in_array(strtoupper(substr(trim($_FILES['pic']['name']), strlen($_FILES['pic']['name']) - 3)), array('PNG', 'PDF'))) {
            display_warning(_('Only  files  supported  are - a file extension of  .png ,.pdf is expected'));
            $upload_file = 'No';
        } else if ($_FILES['pic']['size'] > ($max_image_size * 1024)) { //File Size Check
            display_warning(_('The file size is over the maximum allowed. The maximum size allowed in MB is') . ' ' . $max_image_size);
            $upload_file = 'No';
        } elseif (file_exists($filename)) {
            $result = unlink($filename);
            if (!$result) {
                display_error(_('The existing image could not be removed'));
                $upload_file = 'No';
            }
        }
        if ($_FILES['pic']['error'] === UPLOAD_ERR_INI_SIZE) {
            // Handle the error
            echo 'Your file is too large.';
            $upload_file = 'No';
            die();
        }

        if ($upload_file == 'Yes') {
            $update_pager = false;
            if ($Mode == 'ADD_ITEM') {
                $state_id = $_POST['state'];
                if (is_issue(trim($_POST['issue_no'])) == 0) {
                    $result = move_uploaded_file($_FILES['pic']['tmp_name'], $filename);

                    add_visitor($_POST['ref_id'], $_POST['first_name'], $_POST['last_name'], $_POST['to_meet'], $_POST['company'], $_POST['coming_from'], $_POST['purpose'], $_POST['contact_number'], $_POST['email'], $_POST['tr_fromdate'], $_POST['time_tr_fromdate'], $_POST['tr_todate'], $_POST['time_tr_todate'],$_POST['remarks']);
                    display_notification(_('New Record  has been added'));
                    $Mode = 'RESET';
                    $update_pager = true;
                } else {
                    display_warning(_("Duplicate isuue no"));
                }
            } else {
                if ($_POST['ref_id']) {
                    update_visitor($_POST['ref_id'], $_POST['first_name'], $_POST['last_name'], $_POST['to_meet'], $_POST['company'], $_POST['coming_from'], $_POST['purpose'], $_POST['contact_number'], $_POST['email'], $_POST['tr_fromdate'], $_POST['time_tr_fromdate'], $_POST['tr_todate'], $_POST['time_tr_todate'],$_POST['remarks']);

                    $Mode = 'RESET';
                    $update_pager = true;
                    display_notification(_('Record has been updated'));
                }else {
                    display_warning(_("Duplicate isuue no"));
                }
            }
        }
        $Ajax->activate('details');
    } else {
        $update_pager = false;
        if ($Mode == 'ADD_ITEM') {
            $result = '';

                add_visitor($_POST['ref_id'], $_POST['first_name'], $_POST['last_name'], $_POST['to_meet'], $_POST['company'], $_POST['coming_from'], $_POST['purpose'], $_POST['contact_number'], $_POST['email'], $_POST['tr_fromdate'], $_POST['time_tr_fromdate'] ,$_POST['tr_todate'], $_POST['time_tr_todate'],$_POST['remarks']);
                display_notification(_('New Record  has been added'));
                $Mode = 'RESET';
                $update_pager = true;
        } else {
            if ($_POST['ref_id']) {
                $result = '';
                update_visitor($selected_id, $_POST['ref_id'], $_POST['first_name'], $_POST['last_name'], $_POST['to_meet'], $_POST['company'], $_POST['coming_from'], $_POST['purpose'], $_POST['contact_number'], $_POST['email'], $_POST['tr_fromdate'] , $_POST['time_tr_fromdate'] ,$_POST['tr_todate'], $_POST['time_tr_todate'],$_POST['remarks']);

                $Mode = 'RESET';
                $update_pager = true;
                display_notification(_('Record has been updated'));
            } else {
                display_warning(_("Duplicate isuue no"));
            }
        }
    }


        $Ajax->activate('details');
    }
}


//-----------------------------------------------------------------------------------
/*
  if ($Mode=='UPDATE_ITEM' && can_process())
  {
  //display_error("sdff");die;
  update_guest($selected_id,$_POST['g_name'],$_POST['f_name'],$_POST['gender'],$_POST['marital_status'],$_POST['pic'],$_POST['porpose'],$_POST['line1'],$_POST['line2'],$_POST['city'],$_POST['country'],$_POST['state'],$_POST['pin_code'],$_POST['email'],$_POST['c_number']);
  $Mode = 'RESET';
  display_notification(_('Record has been updated'));
  } */


//-----------------------------------------------------------------------------------

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
if(!isset($_POST['visitor_management_page_next']))
$_SESSION['fun']['id'] = 0;
start_form(true);
$sql = getVisitorInfo();

$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("Ref No") => array('align' => 'center','fun' => 'ref'),
            _("First Name") => array('align' => 'center','fun' => 'fname'),
            _("Last Name") => array('align' => 'center','fun' => 'lname'),
            _("To Meet") => array('align' => 'center','fun' => 'to_meet'),
            _("Company") => array('align' => 'center','fun' => 'company'),
            _("Coming From") => array('align' => 'center','fun' => 'coming_from'),
            _("Purpose") => array('align' => 'center','fun' => 'purpose'),
            _("Mobile No") => array('align' => 'center','fun' => 'no'),
            _("Email Id") => array('align' => 'center', 'fun' => 'email'),
            _("Check In (Date &amp; Time)") => array('align' => 'center', 'fun' => 'checking'),
            _("Check Out (Date &amp; Time)") => array('align' => 'center', 'fun' => 'checkout'),
            _("Remarks") => array('align' => 'center', 'fun' => 'remark'),
            _("Edit") => array('align' => 'center', 'fun' => 'edit'),
);
$table = &new_db_pager('visitor_management', $sql, $cols);

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
function ref($row) {
    return $row['ref_id'];
}
function fname($row) {
    return $row['first_name'];
}
function lname($row) {
    return $row['last_name'];
}
function to_meet($row) {
    return $row['to_meet'];
}
function company($row) {
    return $row['company'];
}
function coming_from($row) {
    return $row['coming_from'];
}
function purpose($row) {
    return $row['purpose'];
}
function no($row) {
    return $row['contact_number'];
}
function email($row) {
    return $row['email'];
}
function checking($row) {
	$date = date('Y-m-d h:i A',strtotime($row['tr_fromdate']));
	return $date ;
}
function checkout($row) {
    if($row['tr_todate']){
	$date = date('Y-m-d h:i A',strtotime($row['tr_todate']));
    }else{
       $date = '---'; 
    }
	return $date ;

}
function remark($row) {
    return $row['remarks'];
}
function edit($row) {
	if($row['tr_todate'] ==""){
		$edit_button=edit_button_cell("Edit" . $row['vistitor_id'], _("Edit"));
	}
    return $edit_button;
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
    // display_error($selected_id);

    if ($Mode == 'Edit') {

        $myrow = get_visitor_id($selected_id);
        $_POST['ref_id'] = $myrow["ref_id"];
        $_POST['first_name'] = $myrow["first_name"];
        $_POST['last_name'] = $myrow["last_name"];
       // $_POST['dispatch_date'] = date("m/d/Y", strtotime($myrow["tr_fromdate"]));
        $_POST['to_meet'] = $myrow["to_meet"];
        $_POST['company'] = $myrow["company"];
        $_POST['coming_from'] = $myrow["coming_from"];
        $_POST['purpose'] = $myrow["purpose"];
        $_POST['contact_number'] = $myrow["contact_number"];
        $_POST['email'] = $myrow["email"];
        $_POST['tr_fromdate'] = date("m/d/Y", strtotime($myrow["tr_fromdate"]));
        $_POST['tr_todate'] =$myrow["tr_todate"];
        $_POST['remarks'] = $myrow["remarks"];
       /* $_POST['email'] = $myrow["email"];
        $_POST['c_number'] = $myrow["contact_no"];
        $_POST['person_org_name'] = $myrow["person_org_name"];
        $_POST['line1'] = $line[0];
        $_POST['line2'] = $line[1];
        ;
        $_POST['city'] = $myrow["city"];
        $_POST['country'] = $myrow["country"];
        $_POST['state'] = $myrow["state"];
        $_POST['pin_no'] = $myrow["pin_no"];
        $_POST['email'] = $myrow["email_id"];
        $_POST['c_number'] = $myrow["contact_no"];
        $_POST['remarks'] = $myrow["remarks"];
        $state_id = $_POST['state'];*/
    }
    hidden('selected_id', $selected_id);
}


table_section_title(_("Visitor Details"));


if ($Mode != 'Edit') {
    $sql1 = "SELECT MAX(ref_id) FROM fa_visitor_management WHERE ref_id LIKE 'ref-%'";

    $empl_data = db_query($sql1);
    $empid_result = db_fetch_row($empl_data);

    //$last_max_emp_id = $empid_result[0];
    $last_emp1 = substr($empid_result[0], 5);

    $emp_inc_id = $last_emp1 + 1;
    if (strlen($emp_inc_id) == 1) {
        $_POST['ref_id'] = 'ref-00' . $emp_inc_id;
    } else if (strlen($emp_inc_id) == 2) {
        $_POST['ref_id'] = 'ref-0' . $emp_inc_id;
    } else {
        $_POST['ref_id'] = 'ref-' . $emp_inc_id;
    }
}
label_row(_("Ref No."), $_POST['ref_id']);

hidden('ref_id', $_POST['ref_id']);

table_section_title(_("To whom Its Dispatched"));
text_row(_('First Name*'), 'first_name', $_POST['first_name'], 30,50);

text_row(_('Last Name'), 'last_name', $_POST['last_name'], 30,50);

text_row(_('To Meet*'), 'to_meet', $_POST['to_meet'], 30,50);

text_row(_('Company*'), 'company', $_POST['company'], 30,50);

text_row(_('Coming From*'), 'coming_from', $_POST['coming_from'], 30,50);

text_row(_('Purpose*'), 'purpose', $_POST['purpose'], 30,50);

room_no_row_ex(_("Mobile No *"), 'contact_number', 30, null, null, null, null, null, FALSE);

room_no_row_ex(_("Email Id "), 'email', 30, null, null, null, null, null, FALSE);
datetime_row_v(_("Check In (Date &amp; Time)*") . ":", 'tr_fromdate', 20, null, '', '', '', null, true,true);
datetime_row_v(_("Check Out (Date &amp; Time)*") . ":", 'tr_todate', 20, null, '', '', '', null, true,true);
table_section_title(_("Remarks"));
textarea_row(_("Remarks"), 'remarks', $_POST['remarks'], 34, 6);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------



end_page();
?>


