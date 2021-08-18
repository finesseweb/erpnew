<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */

$page_security = 'HR_EMPLOYEE_SETUP';
$path_to_root = "../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();

include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/date_functions.inc");

$version_id = get_company_prefs('version_id');

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

page(_("Settings"));

function can_process() {

    if ($_POST['expd_percentage_amt'] == "") {
        display_error(_("You need to provide the maximum monthly pay limit percentage for employee loan."));
        set_focus('expd_percentage_amt');
        return false;
    }
    if (!check_num('expd_percentage_amt')) {
        display_error(_("Maximum EMI Limit should be a positive number"));
        set_focus('login_tout');
        $input_error = 1;
    }

    return true;
}

if (isset($_POST['addupdate']) && can_process()) {
    //=====modified on 29-01-2019 by Ashutosh
        $_POST['weekly_off'] = implode(',', makeArrayOfWeeklyOff($_POST['no_weekly_off']));
    $options = array('weekly_off', 'half_day_applicable', 'half_day', 'empl_ref_type', 'salary_account', 'paid_from_account', 'expd_percentage_amt');
    foreach ($_POST as $key => $value) {
        if (in_array($key, $options)) {
            Update('kv_empl_option', array('option_name', $key), array('option_value' => $value));
        }
    }
    display_notification("Settings Updated");
}
//=====modified on 29-01-2019 by Ashutosh
function makeArrayOfWeeklyOff($no_weekly_off){
    $off_arr = array();
    for($i=0;$i<(int)$no_weekly_off;$i++)
    $off_arr[] = $_POST["weekly_off$i"];
    return $off_arr;
}



$_POST['weekly_off'] = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'weekly_off'));

if (isset($_POST['weekly_off']) && $_POST['weekly_off'] > 0) {
    
} else {
    $_POST['weekly_off'] = 'Sun';
    Insert('kv_empl_option', array('option_name' => 'weekly_off', 'option_value' => 'Sun'));
}


$_POST['empl_ref_type'] = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'empl_ref_type'));

if (isset($_POST['empl_ref_type']) && $_POST['empl_ref_type'] > 0) {
    
} else {
    $_POST['empl_ref_type'] = 0;
    Insert('kv_empl_option', array('option_name' => 'empl_ref_type', 'option_value' => 0));
}


$_POST['salary_account'] = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'salary_account'));

if (isset($_POST['salary_account']) && $_POST['salary_account'] > 0) {
    
} else {
    $_POST['salary_account'] = 5410;
    Insert('kv_empl_option', array('option_name' => 'salary_account', 'option_value' => 5410));
}

$_POST['paid_from_account'] = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'paid_from_account'));

if (isset($_POST['paid_from_account']) && $_POST['paid_from_account'] > 0) {
    
} else {
    $_POST['paid_from_account'] = 1060;
    Insert('kv_empl_option', array('option_name' => 'paid_from_account', 'option_value' => 1060));
}

$_POST['expd_percentage_amt'] = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'expd_percentage_amt'));

if (isset($_POST['expd_percentage_amt']) && $_POST['expd_percentage_amt'] > 0) {
    
} else {
    $_POST['expd_percentage_amt'] = 30;
    Insert('kv_empl_option', array('option_name' => 'expd_percentage_amt', 'option_value' => 50));
}
start_form();
start_table(TABLESTYLE2);
hrm_empl_workings_days_no('No of Days(Weekly off):', 'no_weekly_off', $_POST['no_weekly_off'], true);
if (get_post('no_weekly_off')) {
  // display_error($_POST['no_weekly_off']);
   $Ajax->activate('body');
    for ($i = 0; (int) $i < $_POST['no_weekly_off']; $i++)
        hrm_empl_workings_days('Weekend/Weekly Off:', 'weekly_off'.$i);
}

hrm_empl_ref_type('Employee ID Method:', 'empl_ref_type');
gl_all_accounts_list_row(_("Salary Account:"), 'salary_account', $_POST['salary_account']);
gl_all_accounts_list_row(_("Bank Account:"), 'paid_from_account', $_POST['paid_from_account']);
text_row_ex(_("Maximum allowed Limit Percentage for Loan Monthly Pay:"), 'expd_percentage_amt', 10, 10, '', null, null, "%");
end_table();
br();
submit_center('addupdate', _("Submit"), true, '', 'default');

end_form();
end_page();
?>