<?php
/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */

$page_security = 'SA_OPEN';
$path_to_root = "../../..";
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
//include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");


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


page(_($help_context = "Employees"), @$_REQUEST['popup'], false, "", $js);

if (kv_check_payroll_table_exist()) {
    
} else {
    display_error(_("There are no Allowance defined in this system. Kindly Setup <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/pay_items_setup.php' target='_blank'>Allowances</a> Your Allowances."));
    end_page();
    exit;
}
if (db_has_basic_pay()) {
    
} else {
    display_error(_("Basic Pay is not Setup in the system. Kindly Setup <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/pay_items_setup.php' target='_blank'>Basic Pay</a> here."));
    end_page();
    exit;
}
if (db_has_tax_pay()) {
    
} else {
    display_error(_("Tax is not Setup in the system. Kindly Setup <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/pay_items_setup.php' target='_blank'>Tax </a> here."));
    end_page();
    exit;
}
check_db_has_salary_account(_("There are no Salary Account defined in this system. Kindly Open <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/hrm_settings.php' target='_blank'>Settings</a> to update it."));

check_db_has_Departments(_("There is no Department in the system to add employees. Please Add some <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/department.php' target='_blank'>Department</a> "));
$new_item = get_post('selected_id') == '' || get_post('cancel');

if (isset($_GET['selected_id'])) {
    $_POST['selected_id'] = $_GET['selected_id'];
}
$selected_id = get_post('selected_id');
if (list_updated('selected_id')) {

    $_POST['empl_id'] = $selected_id = get_post('selected_id');
    clear_data();
    $Ajax->activate('details');
    $Ajax->activate('controls');
}
if (list_updated('same_as_correspond_address')) {
    //display_error($_POST['addr_line1']);
    if ($_POST['same_as_correspond_address'] == 1) {
        $_POST['per_addr_line1'] = $_POST['addr_line1'];
        $Ajax->activate('per_addr_line1');
        $_POST['per_addr_line2'] = $_POST['addr_line2'];
        $Ajax->activate('per_addr_line2');
        $_POST['empl_per_city'] = $_POST['empl_city'];
        $Ajax->activate('empl_per_city');
        $_POST['per_country'] = $_POST['country'];
        $Ajax->activate('per_country');
        $_POST['empl_per_state'] = $_POST['empl_state'];
        $Ajax->activate('empl_per_state');
        $_POST['per_pincode'] = $_POST['pincode'];
        $Ajax->activate('per_pincode');
    } else {
        $_POST['per_addr_line1'] = '';
        $Ajax->activate('per_addr_line1');
        $_POST['per_addr_line2'] = '';
        $Ajax->activate('per_addr_line2');
        $_POST['empl_per_city'] = '';
        $Ajax->activate('empl_per_city');
        $_POST['per_country'] = '99';
        $Ajax->activate('per_country');
        $_POST['empl_per_state'] = '1475';
        $Ajax->activate('empl_per_state');
        $_POST['per_pincode'] = '';
        $Ajax->activate('per_pincode');
    }

    $Ajax->activate('details');

    $Ajax->activate('controls');
    //display_error($_POST['same_as_correspond_address']);
}

if (list_updated('empl_type')) {
    $Ajax->activate('contract_end_date');
    $Ajax->activate('contract_duration');
}

$basic_id = kv_get_basic();

$EarAllowance = get_allowances('Earnings');
while ($single = db_fetch($EarAllowance)) {

    if (get_post('_' . $single['id'] . '_changed')) {

        $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{};'?<>]/";

        if (preg_match($regex, get_post($single['id'])) != 0) {
            $input_err = 1;
            display_error(_(" Only numerical values are allowed."));
            set_focus($single['id']);
        } else {
            $input_err = 0;
        }
    }
}

$DedAllowance = get_allowances('Deductions');
while ($single = db_fetch($DedAllowance)) {

    if (get_post('_' . $single['id'] . '_changed')) {

        $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{};'?<>]/";

        if (preg_match($regex, get_post($single['id'])) != 0) {
            $input_err = 1;
            display_error(_(" Only numerical values are allowed."));
            set_focus($single['id']);
        } else {
            $input_err = 0;
        }
    }
}
if (list_updated($basic_id) || get_post('RefreshInquiry') || get_post('_2_changed')) {

    $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{};'?<>]/";

    if (preg_match($regex, get_post($basic_id)) != 0) {
        $input_err = 1;
        display_error(_("Only numerical values are allowed."));
        set_focus($basic_id);
    } else {
        $input_err = 0;
    }

    $month = get_post($basic_id);

    $EarAllowance = get_allowances('Earnings');
    $DedAllowance = get_allowances('Deductions');
    $basic_id = kv_get_basic();



    while ($single = db_fetch($EarAllowance)) {


        if (($single['value'] == 'Percentage') && ($single['percentage'] > 0)) {

            $sql = "SELECT id FROM fa_kv_allowances WHERE type='Earnings' AND value='Amount' ";
            $res = db_query($sql, 'Return Allowances');
            $basic_amount = 0;

            while ($result = db_fetch($res)) {
                if ($result['id'] != 8) {//Excluded Conveyance Allowence from calculation
                    $basic_amount = $basic_amount + get_post($result['id']);

                    $_POST[$result['id']] = get_post($result['id']);

                    $Ajax->activate($result['id']);
                    $default_value = round(($basic_amount) * ($single['percentage'] / 100));
                }
            }
        } else
            $default_value = null;


        if (($single['id'] != $basic_id) && ($single['basic'] != '1') && ($single['id'] != '2')) {

            if (($single['id'] == 4) && ($_POST['eligible_hra'] == 2)) {
                $_POST['4'] = 0;
                $Ajax->activate($_POST['4']);
            } else if ($single['id'] == 8) {
                $_POST[$single['id']] = $_POST['8'];
                $Ajax->activate($_POST[$single['id']]);
            } else {

                $_POST[$single['id']] = $default_value;
                $Ajax->activate($_POST[$single['id']]);
            }
        }
    }
    $prof_tax = kv_get_Tax_allowance();

    while ($single = db_fetch($DedAllowance)) {
        $tot_sal = get_post('1') + get_post('2') + get_post('3');

        if ($single['value'] == 'Percentage' && $single['percentage'] > 0) {
            //display_error($basic_amount);

            $default_value = round(($tot_sal) * ($single['percentage'] / 100));
        } else {

            $default_value = null;
        }

        //if($single['id'] != $prof_tax){//Commented for showing PF value in employee add, Date - 14-Dec-2017
        $_POST[$single['id']] = $default_value;



        //}		
    }

    $Ajax->activate('_page_body');
    $Ajax->activate('payroll_tbl');
}


// DO THE OTHER STUFF AND LOGIC HERE




if (list_updated('country')) {
    $Ajax->activate('empl_state');
}


/* if(get_post('_addr_line1_changed')){
  if(!empty($selected_id)){
  display_error('Recheck same as correspondence address');
  }
  }
  if(get_post('_addr_line2_changed')){

  if(!empty($selected_id)){
  display_error('Recheck same as correspondence address');
  }
  }

  if(get_post('_empl_city_changed')){

  if(!empty($selected_id)){
  display_error('Recheck same as correspondence address');
  }
  }

  if(get_post('_pincode_changed')){

  if(!empty($selected_id)){
  display_error('Recheck same as correspondence address');
  }
  }


  if(!empty($selected_id)){
  if(list_updated('country')){
  display_error('Recheck same as correspondence address');
  $Ajax->activate('country');
  }
  }

  if(!empty($selected_id)){
  if(list_updated('empl_state')){
  display_error('Recheck same as correspondence address');
  $Ajax->activate('empl_state');
  }
  } */

if (!empty($selected_id)) {
    if (list_updated('eligible_hra')) {
        if ($_POST['eligible_hra'] == 1) {
            display_error('Please click on calculate button');
        }
    }
}
if (get_post('gender') == 2 && get_post('marital_status') == 2) {
    //$Ajax->activate('children_data');
    $Ajax->activate('details');
} else {
    //$Ajax->activate('details');
}

if (get_post('cancel')) {
    $_POST['empl_id'] = $selected_id = $_POST['selected_id'] = '';
    clear_data();
    set_focus('selected_id');
    $Ajax->activate('_page_body');
}
if (list_updated('category_id') || list_updated('mb_flag')) {
    $Ajax->activate('details');
}

function clear_data() {
    unset($_POST['empl_id']);
    unset($_POST['empl_salutation']);
    unset($_POST['salutation_text']);
    unset($_POST['empl_firstname']);
    unset($_POST['empl_middlename']);
    unset($_POST['empl_lastname']);
    unset($_POST['addr_line1']);
    unset($_POST['correspondence_address']);
    unset($_POST['same_as_correspond_address']);
    unset($_POST['permanent_address']);
    unset($_POST['addr_line2']);
    unset($_POST['empl_city']);
    unset($_POST['empl_state']);
    unset($_POST['pincode']);
    unset($_POST['per_addr_line1']);
    unset($_POST['per_addr_line2']);
    unset($_POST['empl_per_city']);
    unset($_POST['per_pincode']);
    unset($_POST['gender']);
    unset($_POST['date_of_birth']);
    unset($_POST['age']);
    unset($_POST['pf_number']);
    unset($_POST['pan_no']);
    unset($_POST['aadhaar_no']);
    unset($_POST['esi_no']);
    unset($_POST['pran_no']);
    unset($_POST['marital_status']);
    unset($_POST['no_of_children']);
    unset($_POST['home_phone']);
    unset($_POST['mobile_phone']);
    unset($_POST['email']);
    unset($_POST['status']);
    unset($_POST['del_image']);
    unset($_POST['pic']);
}

//------------------------------------------------------------------------------------
$upload_file = "";
if (isset($_POST['addupdate'])) {


    $input_error = 0;
    if ($upload_file == 'No')
        $input_error = 1;

    if (strlen($_POST['empl_id']) == 0) {
        display_error(_("The employee Id Can't be empty."));
        set_focus('empl_id');
        return false;
    }
    if (strlen($_POST['empl_id']) < 3) {
        display_error(_("The employee Id must have minimum three characters."));
        set_focus('empl_id');
        return false;
    }
    if ($new_item && ctype_alnum($_POST['empl_id']) != false) {
        display_error(_("The employee Id must be Combinations of Letters and Numbers, Not symbols."));
        set_focus('empl_id');
        return false;
    }
    if ($new_item && db_has_selected_employee($_POST['empl_id']) != null) {
        display_error(_("The employee Id Already Exist."));
        set_focus('empl_id');
        return false;
    }
    if (strlen($_POST['empl_firstname']) == 0) {
        display_error(_("The employee name cannot be empty."));
        set_focus('empl_firstname');
        return false;
    }


    if (!empty($_POST['empl_firstname'])) {
        $regex = "/[^ ][ 0-9*@$%#^&!~()+-\/><,{};'?<>]$/";
        if (preg_match($regex, $_POST['empl_firstname']) != 0) {
            //$input_err = 1;
            display_error(_("First name can allow only alphabets."));
            set_focus('empl_firstname');
            return false;
        } else {
            //$input_err = 0;
        }
    }

    if (!empty($_POST['addr_line1'])) {
        $regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

        if (preg_match($regex, $_POST['addr_line1']) != 0) {

            display_error(_("Special characters not allowed in correspondence line1 address."));
            set_focus('addr_line1');
            return false;
        }
    }

    if (!empty($_POST['per_addr_line1'])) {
        $regex = "/[^ ][ *@$%#^&!~()+\><{};'?<>]$/";

        if (preg_match($regex, $_POST['per_addr_line1']) != 0) {
            display_error(_("Special characters not allowed in permanent line1 address."));
            set_focus('per_addr_line1');
            return false;
        }
    }

    if (!empty($_POST['mobile_phone'])) {
        $regex = "/[^ ][ a-zA-Z*@$%#^&!~()-\/><{};'?<>]$/";

        if (preg_match($regex, $_POST['mobile_phone']) != 0) {
            display_error(_("Mobile Phone can accept Numericals only."));
            set_focus('mobile_phone');
            return false;
        }
    }

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

    if (!empty($_POST['bank_name'])) {
        $regex = "/[^ ][ A-Za-z\.]$/";

        if (preg_match($regex, $_POST['bank_name']) == 0) {
            display_error(_("Bank name can accept only Alphabets."));
            set_focus('bank_name');
            return false;
        }
    }

    if (!empty($_POST['ifsc_code'])) {
        $regex = "/[^ ][ A-Z0-9]$/";
        $regex1 = "/[^ ][ a-z]$/";
        if (preg_match($regex1, $_POST['ifsc_code']) != 0) {
            display_error(_("IFSC code can allow capital letters onlly."));
            set_focus('ifsc_code');
            return false;
        }
        if (preg_match($regex, $_POST['ifsc_code']) == 0) {
            display_error(_("You have entered an invalid ifsc code."));
            set_focus('ifsc_code');
            return false;
        }
    }

    if (!empty($_POST['acc_no'])) {
        $regex = "/[^ ][ 0-9]$/";

        if (preg_match($regex, $_POST['acc_no']) == 0) {
            display_error(_("You have entered an invalid Account Number."));
            set_focus('acc_no');
            return false;
        }
    }

    $EarAllowance = get_allowances('Earnings');
    while ($single = db_fetch($EarAllowance)) {

        if (!empty($_POST[$single['id']])) {

            $regex = "/[^ ][a-zA-Z*@$%#^&!~()+-\/><.,{};'?<>]/";

            if (preg_match($regex, $_POST[$single['id']]) != 0) {
                $input_err = 1;
                display_error(_(" Only numerical values are allowed."));
                set_focus($single['id']);
                return false;
            }
        }
    }

    $DedAllowance = get_allowances('Deductions');
    while ($single = db_fetch($DedAllowance)) {

        if (!empty($_POST[$single['id']])) {

            $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{};'?<>]/";

            if (preg_match($regex, $_POST[$single['id']]) != 0) {
                $input_err = 1;
                display_error(_(" Only numerical values are allowed."));
                set_focus($single['id']);
                return false;
            }
        }
    }
    if (strlen($_POST['addr_line1']) == 0) {
        display_error(_("Correspondence Line1 cannot be empty."));
        set_focus('addr_line1');
        return false;
    }

    if (strlen($_POST['per_addr_line1']) == 0) {
        display_error(_("Permanent Line1 can't be empty."));
        set_focus('per_addr_line1');
        return false;
    }


    if (strlen($_POST['mobile_phone']) == 0) {
        display_error(_("The employee mobile number can't be empty."));
        set_focus('mobile_phone');
        return false;
    }

    /* if(!preg_match('/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/', $_POST['mobile_phone']))
      {
      display_error(_("The employee mobile number Can't be Invalid."));
      set_focus('mobile_phone');
      return false;
      } */
    if ($new_item && db_has_employee_email($_POST['email'])) {

        display_error(_("The E-mail already in Use."));
        set_focus('email');
        return false;
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
        
    } else {
        display_error(_("The Entered E-Mail is not Valid."));
        set_focus('basic');
        return false;
    }

    if ($new_item && date2sql($_POST['joining']) > date('Y-m-d')) {
        display_error(_("Invalid Joining Date for the Employee."));
        set_focus('joining');
        return false;
    }
    /* if(strlen($_POST['age']) >2 || !check_num('age', 0)){
      display_error(_("The entered age is invalid."));
      set_focus('age');
      return false;
      } */

    /* if ($new_item){
      $joining = new DateTime(date2sql($_POST['joining']));
      $dob = new DateTime(date2sql($_POST['date_of_birth']));

      $diff = $dob->diff($joining);
      display_error($diff);
      if($diff->y < 19){
      display_error(_("The employee Date of Birth is not valid one."));
      set_focus('date_of_birth');
      return false;
      }
      } */

    if (($new_item) || ($_POST['date_of_birth'] != '')) {
        $joining = new DateTime(date2sql($_POST['joining']));
        $dob = new DateTime(date2sql($_POST['date_of_birth']));
        $t_day = new DateTime();
        $diff = $t_day->diff($dob);
        if ($diff->y <= 19) {
            display_error(_("The employee Date of Birth is not valid one."));
            set_focus('date_of_birth');
            return false;
        }
    }


    /* 	if(strlen($_POST['bank_name']) == 0){
      display_error(_("Bank Name cannot be empty."));
      set_focus('bank_name');
      return false;
      }
      if(strlen($_POST['acc_no']) == 0){
      display_error(_("Bank Account Number cannot be empty."));
      set_focus('acc_no');
      return false;
      } */



    if ($new_item && strlen($_POST['bank_name']) == 0 && $_POST['mod_of_pay'] == 2) {
        display_error(_("The employee Bank Name Can't be empty."));
        set_focus('bank_name');
        return false;
    }

    if ($new_item && strlen($_POST['bank_name']) == 0 && $_POST['mod_of_pay'] == 3) {
        display_error(_("The employee Bank Name Can't be empty."));
        set_focus('bank_name');
        return false;
    }

    if ($new_item && strlen($_POST['ifsc_code']) == 0 && $_POST['mod_of_pay'] == 2) {
        display_error(_("The employee IFSC Code Can't be empty."));
        set_focus('ifsc_code');
        return false;
    }

    if ($new_item && strlen($_POST['ifsc_code']) == 0 && $_POST['mod_of_pay'] == 3) {
        display_error(_("The employee IFSC Code Can't be empty."));
        set_focus('ifsc_code');
        return false;
    }

    if ($new_item && strlen($_POST['acc_no']) == 0 && $_POST['mod_of_pay'] == 2) {
        display_error(_("The employee Account Number Can't be empty."));
        set_focus('acc_no');
        return false;
    }

    if ($new_item && strlen($_POST['acc_no']) == 0 && $_POST['mod_of_pay'] == 3) {
        display_error(_("The employee Account Number Can't be empty."));
        set_focus('acc_no');
        return false;
    }

    if ($input_error != 1) {

        if (check_value('del_image')) {
            $filename = company_path() . '/images/empl/' . empl_img_name($_POST['empl_id']) . ".jpg";
            if (file_exists($filename))
                unlink($filename);
        }

        if (!$new_item) { /* so its an existing one */
            //display_error($_POST['same_as_correspond_address']); die;
            if ($_POST['same_as_correspond_address'] == '') {
                $same_checked = 0;
            } else {
                $same_checked = $_POST['same_as_correspond_address'];
            }
            if ($_POST['aadhaar_no'] == '') {
                $_POST['aadhaar_no'] = '';
            }

            update_employee($selected_id, $_POST['empl_salutation'], $_POST['salutation_text'], $_POST['empl_firstname'], $_POST['empl_middlename'], $_POST['empl_lastname'], $_POST['addr_line1'], $_POST['addr_line2'], $_POST['correspondence_address'], $_POST['permanent_address'], $same_checked, $_POST['home_phone'], $_POST['mobile_phone'], $_POST['email'], $_POST['gender'], $_POST['date_of_birth'], $_POST['age'], $_POST['pf_number'], $_POST['pan_no'], base64_encode($_POST['aadhaar_no']), $_POST['esi_no'], $_POST['pran_no'], $_POST['marital_status'], $_POST['no_of_children'], $_POST['status'], $_POST['empl_city'], $_POST['empl_state'], $_POST['pincode'], $_POST['country'], $_POST['per_addr_line1'], $_POST['per_addr_line2'], $_POST['empl_per_city'], $_POST['per_country'], $_POST['empl_per_state'], $_POST['per_pincode']);
            $kv_empl_id = $selected_id;
            set_focus('selected_id');
            $Ajax->activate('selected_id'); // in case of status change
            display_notification(_("Employee Information has been updated."));
        } else { //it is a NEW part
            //display_error($_POST['same_as_correspond_address']);  die;
            /* if($_POST['same_as_correspond_address']=='on'){
              $same_checked = 1;
              }else{
              $same_checked = 0;
              } */

            if ($_POST['aadhaar_no'] == '') {
                $_POST['aadhaar_no'] = '';
            }

            add_employee($_POST['empl_id'], $_POST['empl_salutation'], $_POST['salutation_text'], $_POST['empl_firstname'], $_POST['empl_middlename'], $_POST['empl_lastname'], $_POST['addr_line1'], $_POST['addr_line2'], $_POST['correspondence_address'], $_POST['permanent_address'], $_POST['same_as_correspond_address'], $_POST['home_phone'], $_POST['mobile_phone'], $_POST['email'], $_POST['gender'], $_POST['date_of_birth'], $_POST['age'], $_POST['pf_number'], $_POST['pan_no'], base64_encode($_POST['aadhaar_no']), $_POST['esi_no'], $_POST['pran_no'], $_POST['marital_status'], $_POST['no_of_children'], $_POST['status'], $_POST['empl_city'], $_POST['empl_state'], $_POST['pincode'], $_POST['country'], $_POST['per_addr_line1'], $_POST['per_addr_line2'], $_POST['empl_per_city'], $_POST['per_country'], $_POST['empl_per_state'], $_POST['per_pincode']);
            $kv_empl_id = $_POST['empl_id'];
            //add_empl_department($kv_empl_id, $_POST['department']); 
            $jobs_arr = array('empl_id' => $_POST['empl_id'],
                'grade' => $_POST['grade'],
                'department' => $_POST['department'],
                'desig_group' => $_POST['desig_group'],
                'employee_type' => $_POST['employee_type'],
                'desig' => $_POST['desig'],
                'joining' => array($_POST['joining'], 'date'),
                'empl_type' => $_POST['empl_type'],
                'effective_date' => $_POST['effective_date'],
                'contract_end_date' => $_POST['contract_end_date'],
                'contract_duration' => $_POST['contract_duration'],
                'eligible_hra' => $_POST['eligible_hra'],
                'working_branch' => $_POST['working_place'],
                'mod_of_pay' => $_POST['mod_of_pay'],
                'ifsc_code' => $_POST['ifsc_code'],
                'bank_name' => $_POST['bank_name'],
                'act_holder_name' => $_POST['act_holder_name'],
                'acc_no' => $_POST['acc_no']);
            //display_error($jobs_arr); die;				
            $Allowance = get_allowances();
            $gross_Earnings = 0;
            while ($single = db_fetch($Allowance)) {
                if (isset($_POST[$single['id']]))
                    $jobs_arr[$single['id']] = $_POST[$single['id']];
                if ($single['type'] == 'Earnings')
                    $gross_Earnings += $_POST[$single['id']];
            }
            $jobs_arr['gross'] = $gross_Earnings;
            $jobs_arr['gross_pay_annum'] = $gross_Earnings * 12;
            $jobs_arr['1'] = $_POST['1'];

            Insert('kv_empl_job', $jobs_arr);
            $kv_empl_id = $_POST['empl_id'];
            if (db_has_empl_id()) {
                kv_update_next_empl_id_new($kv_empl_id);
            } else {
                kv_add_next_empl_id_new($kv_empl_id);
            }
            display_notification(_("A new Employee has been added."));

            if (get_post('_tabs_sel')) {
                //display_error($_POST['empl_id']);
                //include_once($path_to_root."/modules/ExtendedHRM/manage/add_empl_info_job.php");
                $Ajax->activate('job');
                if (isset($_POST['empl_id']) || $new_item) {
                    //check_row(_("Create User Account*:"), 'register_user_acc', null);	

                    table_section_title(_("Job Details"));

                    //hrm_empl_grade_list( _("Grade :"), 'grade', null);	
                    hidden('grade', 0);
                    //hrm_empl_desig_group(_("Desgination Group *:"), 'desig_group', null);
                    department_list_row(_("Department :"), 'department', null);
                    desiggroup_list_row(_("Designation Group:"), 'desig_group', null, false, true);
                    $desig_group = $_POST['desig_group'];
                    desig_list_row(_("Designation:"), 'desig', null, false, false, $desig_group);

                    //text_row(_("Desgination *:"), 'desig', null,  35, 100);
                    //text_row(_("Basic Salary *:"), 'basic_salary', null, 30, 30);
                    date_row(_("Date of Join") . ":", 'joining');
                    hrm_empl_type_row(_("Employment Type*:"), 'empl_type', null, true);
                    hidden('effective_date', '');
                    if (($_POST['empl_type'] == 3) || ($_POST['empl_type'] == 4)) {
                        date_row(_("Contract End Date") . ":", 'contract_end_date', null, null, 0, 0, 0, null, true);
                        $d1 = new DateTime($_POST['contract_end_date']);
                        $d2 = new DateTime($_POST['joining']);
                        $dt_diff = $d2->diff($d1);
                        $years = $dt_diff->y;
                        $months = $dt_diff->m;
                        $days = $dt_diff->d;

                        $difference = $years . ' year(s) ' . $months . ' month(s) ' . $days . ' day(s)';
                        text_row(_("Contract Period"), 'contract_duration', $difference, 30, 90);
                        /* if(list_updated('empl_type')){
                          $Ajax->activate('contract_end_date');
                          $Ajax->activate('contract_duration');
                          } */
                    } else {
                        
                    }
                    /* 	if(!empty($_POST['contract_end_date'])){ 
                      echo '<tr id="contract_date">';
                      }
                      else{
                      echo '<tr id="contract_date" style="display:none" >';
                      }
                      echo '<td class="label">Contract End Date</td><td><input type="text" name="contract_end_date" id="contract_end_date" class="date" size="10" maxlength="12" /></td>';
                      echo '</tr>';
                      if(!empty($_POST['contract_duration'])){
                      echo '<tr id="contract_period">';
                      }else{
                      echo '<tr id="contract_period" style="display:none">';
                      }

                      echo '<td class="label">Contract Period</td><td><input type="text" name="contract_duration" id="contract_duration" maxlength="50"/> </td>';
                      echo '</tr>'; */

                    kv_empl_type_list_row(_("Employee Type:"), 'employee_type', null);
                    workcenter_list_row(_("Working Place*:"), 'working_place');
                }
            }

            //clear_data();			
            set_focus('empl_id');
            $Ajax->activate('empl_id');
        }

        if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '') {
            $selected_id = $kv_empl_id;
            $result = $_FILES['pic']['error'];
            $upload_file = 'Yes'; //Assume all is well to start off with
            $filename = company_path() . '/images/empl';

            //if(!isset($max_image_size))
            $max_image_size = 2048;

            if (!file_exists($filename)) {
                mkdir($filename);
            }
            $filename .= "/" . empl_img_name($selected_id) . ".jpg";


            if ((list($width, $height, $type, $attr) = getimagesize($_FILES['pic']['tmp_name'])) !== false) {

                $imagetype = $type;
            } else
                $imagetype = false;

            //display_error($_FILES['pic']['size']);
            //display_error($max_image_size * 1024);die;
            if ($imagetype != IMAGETYPE_GIF && $imagetype != IMAGETYPE_JPEG && $imagetype != IMAGETYPE_PNG && $imagetype == '') {
                display_warning(_('Only graphics files can be uploaded'));
                $upload_file = 'No';
            } elseif (!in_array(strtoupper(substr(trim($_FILES['pic']['name']), strlen($_FILES['pic']['name']) - 3)), array('JPG', 'PNG', 'GIF'))) {
                display_warning(_('Only graphics files are supported - a file extension of .jpg, .png or .gif is expected'));
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
                $result = move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
            }
            $Ajax->activate('details');
        }

        /* if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '') {
          $selected_id = $kv_empl_id;
          $result = $_FILES['pic']['error'];
          $upload_file = 'Yes'; //Assume all is well to start off with
          $filename = company_path().'/images/empl';
          if (!file_exists($filename)){
          mkdir($filename);
          }
          $filename .= "/".empl_img_name($selected_id).".jpg";

          if ((list($width, $height, $type, $attr) = getimagesize($_FILES['pic']['tmp_name'])) !== false){

          $imagetype = $type;
          }
          else
          $imagetype = false;

          if ($imagetype != IMAGETYPE_GIF && $imagetype != IMAGETYPE_JPEG && $imagetype != IMAGETYPE_PNG && $imagetype == ''){
          display_warning( _('Only graphics files can be uploaded'));
          $upload_file ='No';
          }
          elseif (!in_array(strtoupper(substr(trim($_FILES['pic']['name']), strlen($_FILES['pic']['name']) - 3)), array('JPG','PNG','GIF'))){
          display_warning(_('Only graphics files are supported - a file extension of .jpg, .png or .gif is expected'));
          $upload_file ='No';
          }
          elseif ( $_FILES['pic']['size'] > ($max_image_size * 1024)) { //File Size Check
          display_warning(_('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $max_image_size);
          $upload_file ='No';
          }
          elseif (file_exists($filename)){
          $result = unlink($filename);
          if (!$result){
          display_error(_('The existing image could not be removed'));
          $upload_file ='No';
          }
          }

          if ($upload_file == 'Yes'){
          $result  =  move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
          }
          $Ajax->activate('details');

          } */

        $Ajax->activate('_page_body');
    }
}

//------------------------------------------------------------------------------------

if (isset($_POST['delete']) && strlen($_POST['delete']) > 1) {
    $selected_id = $_POST['empl_id'];

    if (key_in_foreign_table($selected_id, 'kv_empl_salary', 'empl_id')) {

        display_error(_("Cannot delete this Employee because Payroll Processed to this employee And it will be  added in the financial Transactions."));
    } else {
        delete_employee($selected_id);
        $Ajax->activate('_page_body');
        $filename = company_path() . '/images/empl/' . empl_img_name($selected_id) . ".jpg";
        if (file_exists($filename))
            unlink($filename);
        display_notification(_("Selected Employee has been deleted."));
        $_POST['selected_id'] = '';
        clear_data();
        set_focus('selected_id');
        $new_item = true;
        $Ajax->activate('_page_body');
    }
}

function empl_personal_data(&$selected_id) {
    br();
    global $SysPrefs, $path_to_root, $new_item, $pic_height;

    start_outer_table(TABLESTYLE2, 'style="width:85%;"');

    table_section(1);

    table_section_title(_("Employee Informations"));

//------------------------------------------------------------------------------------


    if ($new_item) {
        //$_POST['empl_id'] = $empl_id = (int)date('Y').(int)date("m").(count_employees()+1);
        if (!get_post('selected_id') || empty($_POST['selected_id'])) {
            //create employee id by according to faculty type;
            if (!empty($_POST['employee_type'])) {
                if ($_POST['employee_type'] == 1) {//If Staff
                    $sql1 = "SELECT MAX(empl_id) FROM fa_kv_empl_info WHERE empl_id LIKE 'EMP-%'";

                    $empl_data = db_query($sql1);
                    $empid_result = db_fetch_row($empl_data);

                    //$last_max_emp_id = $empid_result[0];
                    $last_emp1 = substr($empid_result[0], 6);

                    $emp_inc_id = $last_emp1 + 1;
                    if (strlen($emp_inc_id) == 1) {
                        $_POST['empl_id'] = 'EMP-00' . $emp_inc_id;
                    } else if (strlen($emp_inc_id) == 2) {
                        $_POST['empl_id'] = 'EMP-0' . $emp_inc_id;
                    } else {
                        $_POST['empl_id'] = 'EMP-' . $emp_inc_id;
                    }
                } else if (in_array($_POST['employee_type'], array(3))){//If Visiting Faculty
                    $sql1 = "SELECT MAX(empl_id) FROM fa_kv_empl_info WHERE empl_id LIKE 'INTRN-%'";

                    $empl_data = db_query($sql1);
                    $empid_result = db_fetch_row($empl_data);

                    //$last_max_emp_id = $empid_result[0];
                    $last_emp1 = substr($empid_result[0], 3);

                    $emp_inc_id = $last_emp1 + 1;
                    if (strlen($emp_inc_id) == 1) {
                        $_POST['empl_id'] = 'INTRN-00' . $emp_inc_id;
                    } else if (strlen($emp_inc_id) == 2) {
                        $_POST['empl_id'] = 'INTRN-0' . $emp_inc_id;
                    } else {
                        $_POST['empl_id'] = 'INTRN-' . $emp_inc_id;
                    }
                }
            }
            if (!isset($_POST['employee_type']) || $_POST['employee_type'] == 2){


                $sql1 = "SELECT MAX(empl_id) FROM fa_kv_empl_info WHERE empl_id LIKE 'STF-%'";

                $empl_data = db_query($sql1);
                $empid_result = db_fetch_row($empl_data);

                //$last_max_emp_id = $empid_result[0];
                $last_emp1 = substr($empid_result[0], 6);

                $emp_inc_id = $last_emp1 + 1;
                if (strlen($emp_inc_id) == 1) {
                    $_POST['empl_id'] = 'STF-00' . $emp_inc_id;
                } else if (strlen($emp_inc_id) == 2) {
                    $_POST['empl_id'] = 'STF-0' . $emp_inc_id;
                }
                /**
                  else if(strlen($emp_inc_id) == 3){
                  $_POST['empl_id'] = 'Emp0'.$emp_inc_id;
                  }
                 * 
                 */ else {
                    $_POST['empl_id'] = 'STF-' . $emp_inc_id;
                }
            }
        }

        text_emprow(_("Employee Id:"), 'empl_id', $_POST['empl_id'], 35, 20, null, '', '', true);
        //text_row(_("Employee Id:"), 'empl_id', $_POST['empl_id'], 21, 20);		
        //unset($_POST['empl_id']);
        $_POST['inactive'] = 0;
        if (!isset($_POST['empl_firstname']))
            $_POST['empl_firstname'] = '';
        if (!isset($_POST['empl_middlename']))
            $_POST['empl_middlename'] = '';
        if (!isset($_POST['empl_lastname']))
            $_POST['empl_lastname'] = '';
        if (!isset($_POST['addr_line1']))
            $_POST['addr_line1'] = '';
        if (!isset($_POST['addr_line2']))
            $_POST['addr_line2'] = '';
        if (!isset($_POST['empl_city']))
            $_POST['empl_city'] = '';
        if (!isset($_POST['empl_state']))
            $_POST['empl_state'] = '';
        if (!isset($_POST['date_of_birth']))
            $_POST['date_of_birth'] = add_years(Today(), -20);
        if (!isset($_POST['per_addr_line1']))
            $_POST['per_addr_line1'] = '';
    } else { // Must be modifying an existing item
        if (get_post('empl_id') != get_post('selected_id') || get_post('addupdate')) { // first item display
            $_POST['empl_id'] = $_POST['selected_id'];

            $myrow = get_employee($_POST['empl_id']);

            $_POST['empl_id'] = $myrow["empl_id"];
            $_POST['empl_salutation'] = $myrow["empl_salutation"];
            $_POST['salutation_text'] = $myrow["salutation_text"];
            $_POST['empl_firstname'] = $myrow["empl_firstname"];
            $_POST['empl_middlename'] = $myrow["empl_middlename"];
            $_POST['empl_lastname'] = $myrow["empl_lastname"];
            $_POST['addr_line1'] = $myrow["addr_line1"];
            $_POST['addr_line2'] = $myrow["addr_line2"];
            $_POST['correspondence_address'] = $myrow["correspondence_address"];
            $_POST['permanent_address'] = $myrow["permanent_address"];
            if ($myrow["same_as_correspond_address"] == 0) {
                $_POST['same_as_correspond_address'] = $myrow["same_as_correspond_address"];
            } else if ($myrow["same_as_correspond_address"] == 1) {
                $_POST['same_as_correspond_address'] = $myrow["same_as_correspond_address"];
            } else {

                $_POST['same_as_correspond_address'] = $_POST['same_as_correspond_address'];
            }
            $_POST['empl_city'] = $myrow["empl_city"];
            $_POST['country'] = $myrow["country"];
            $_POST['empl_state'] = $myrow["empl_state"];
            $_POST['pincode'] = $myrow["pincode"];
            $_POST['home_phone'] = $myrow["home_phone"];
            $_POST['mobile_phone'] = $myrow["mobile_phone"];
            $_POST['email'] = $myrow["email"];
            $_POST['gender'] = $myrow["gender"];
            $_POST['date_of_birth'] = sql2date($myrow["date_of_birth"]);
            $_POST['age'] = $myrow["age"];
            $_POST['pf_number'] = $myrow["pf_number"];
            $_POST['pan_no'] = $myrow["pan_no"];
            $_POST['aadhaar_no'] = base64_decode($myrow["aadhaar_no"]);
            $_POST['esi_no'] = $myrow["esi_no"];
            $_POST['pran_no'] = $myrow["pran_no"];
            $_POST['marital_status'] = $myrow["marital_status"];
            $_POST['no_of_children'] = $myrow["no_of_children"];
            $_POST['status'] = $myrow["status"];
            $_POST['del_image'] = 0;
            $_POST['pic'] = '';
            $_POST['per_addr_line1'] = $myrow['per_addr_line1'];
            $_POST['per_addr_line2'] = $myrow['per_addr_line2'];
            $_POST['empl_per_city'] = $myrow['empl_per_city'];
            $_POST['per_country'] = $myrow['per_country'];
            $_POST['empl_per_state'] = $myrow['empl_per_state'];
            $_POST['per_pincode'] = $myrow['per_pincode'];
            //$_POST['eligible_hra'] = $myrow['eligible_hra'];
        }

        label_row(_("Employee Id:*"), $_POST['empl_id']);

        hidden('empl_id', $_POST['empl_id']);

        set_focus('description');
    }
    hidden('ethnic_origin', 0);

    kv_empl_salutation_list_row(_("Salutation:"), 'empl_salutation', null);
    if (!empty($_POST['salutation_text'])) {
        echo '<tr id="salut_text" >';
    } else {
        echo '<tr id="salut_text" style="display:none" >';
    }
    echo '<td class="label">Salutation Text</td><td><input type="text" name="salutation_text" id="salutation_text" size="35" maxlength="100" value="' . $_POST['salutation_text'] . '"/></td>';
    echo '</tr>';
    text_row(_("First Name:*"), 'empl_firstname', $_POST['empl_firstname'], 35, 100, null, '', '', false);
    text_row(_("Middle Name:"), 'empl_middlename', $_POST['empl_middlename'], 35, 100);
    text_row(_("Last Name:"), 'empl_lastname', $_POST['empl_lastname'], 35, 100);
    //text_row(_("Last Name:*"), 'empl_lastname', $_POST['empl_lastname'], 40, 80);	 

    table_section_title(_("Correspondence Address"));
    //textarea_row(_("Present Address:"), 'pre_address', $_POST['pre_address'], 35, 5);
    if ($_POST['same_as_correspond_address'] == 1) {
        text_row(_("Line 1:*"), 'addr_line1', $_POST['addr_line1'], 35, 100, null, '', '', false, true);
    } else {
        text_row(_("Line 1:*"), 'addr_line1', $_POST['addr_line1'], 35, 100, null, '', '', false, false);
    }
    if ($_POST['same_as_correspond_address'] == 1) {
        text_row(_("Line 2:"), 'addr_line2', $_POST['addr_line2'], 35, 100, null, '', '', false, true);
    } else {
        text_row(_("Line 2:"), 'addr_line2', $_POST['addr_line2'], 35, 100, null, '', '', false, false);
    }
    if ($_POST['same_as_correspond_address'] == 1) {
        text_row(_("City:"), 'empl_city', $_POST['empl_city'], 35, 100, null, '', '', false, true);
    } else {
        text_row(_("City:"), 'empl_city', $_POST['empl_city'], 35, 100, null, '', '', false, false);
    }
    //text_row(_("State / Union Territories:"), 'empl_state', $_POST['empl_state'], 35, 100);
    if ($_POST['same_as_correspond_address'] == 1) {
        country_list_row(_("Country:"), 'country', null, true, true);
        hidden('country', $_POST['country']);
    } else {
        country_list_row(_("Country:"), 'country', null, true, false);
    }
    if ($_POST['same_as_correspond_address'] == 1) {
        state_list_row(_("State / Union Territories:"), 'empl_state', null, $_POST['country'], true, true);
        hidden('empl_state', $_POST['empl_state']);
    } else {
        state_list_row(_("State / Union Territories:"), 'empl_state', null, $_POST['country'], true, false);
    }
    if ($_POST['same_as_correspond_address'] == 1) {
        text_row(_("Pincode:"), 'pincode', $_POST['pincode'], 35, 100, null, '', '', false, true);
    } else {
        text_row(_("Pincode:"), 'pincode', $_POST['pincode'], 35, 100, null, '', '', false, false);
    }
    /*  echo '<tr> <td class="label">Same as Correspondence address</td><td>';

      echo '<input type="checkbox" name="same_as_correspond_address" id="same"';
      echo ($_POST['same_as_correspond_address']==1 ? 'checked' : '');
      echo ' />';
      echo ' </td></tr>'; */
    label_row(_("Same as correspondence address"), checkbox(null, 'same_as_correspond_address', null, true, false), "class='tableheader2'", "class='tableheader'");

    table_section_title(_("Permanent Address"));
    if ($_POST['same_as_correspond_address'] == 1) {
        text_row(_("Line 1:*"), 'per_addr_line1', $_POST['per_addr_line1'], 35, 100, null, '', '', false, true);
    } else {
        text_row(_("Line 1:*"), 'per_addr_line1', $_POST['per_addr_line1'], 35, 100, null, '', '', false, false);
    }
    if ($_POST['same_as_correspond_address'] == 1) {
        text_row(_("Line 2:"), 'per_addr_line2', $_POSt['per_addr_line2'], 35, 100, null, '', '', false, true);
    } else {
        text_row(_("Line 2:"), 'per_addr_line2', $_POSt['per_addr_line2'], 35, 100, null, '', '', false, false);
    }
    if ($_POST['same_as_correspond_address'] == 1) {
        text_row(_("City:"), 'empl_per_city', $_POST['empl_per_city'], 35, 100, null, '', '', false, true);
    } else {
        text_row(_("City:"), 'empl_per_city', $_POST['empl_per_city'], 35, 100, null, '', '', false, false);
    }
    if ($_POST['same_as_correspond_address'] == 1) {
        country_list_row(_("Country"), 'per_country', null, true, true);
        hidden('per_country', $_POST['per_country']);
    } else {
        country_list_row(_("Country"), 'per_country', null, true, false);
    }

    if (($_POST['country'] != '99')) {
        if ($_POST['same_as_correspond_address'] == 1) {
            all_state_list_row(_("State / Union Territories:"), 'empl_per_state', null, true);
            hidden('empl_per_state', $_POST['empl_per_state']);
        } else {

            all_state_list_row(_("State / Union Territories:"), 'empl_per_state', null, false);
        }
    } else if (($_POST['country'] == '1') && ($_POST['per_country'] == '1')) {
        if ($_POST['same_as_correspond_address'] == 1) {
            all_state_list_row(_("State / Union Territories:"), 'empl_per_state', null, true);
            hidden('empl_per_state', $_POST['empl_per_state']);
        } else {
            all_state_list_row(_("State / Union Territories:"), 'empl_per_state', null, false);
        }
    } else {
        if ($_POST['same_as_correspond_address'] == 1) {
            state_list_row(_("State / Union Territories:"), 'empl_per_state', null, $_POST['per_country'], true, true);
            hidden('empl_per_state', $_POST['empl_per_state']);
        } else {

            state_list_row(_("State / Union Territories:"), 'empl_per_state', null, $_POST['per_country'], true, false);
        }
    }
    if ($_POST['same_as_correspond_address'] == 1) {
        text_row(_("Pincode:"), 'per_pincode', $_POST['per_pincode'], 35, 100, null, '', '', false, true);
    } else {
        text_row(_("Pincode:"), 'per_pincode', $_POST['per_pincode'], 35, 100, null, '', '', false, false);
    }

    /* textarea_row(_("Correspondence Address"),'correspondence_address',$_POST['correspondence_address'],20,3);
      echo '<tr> <td class="label">Same as Correspondence address</td><td>';

      echo '<input type="checkbox" name="same_as_correspond_address" id="same"';
      echo ($_POST['same_as_correspond_address']==1 ? 'checked' : '');
      echo ' />';
      echo ' </td></tr>';


      textarea_row(_("Permanent Address"),'permanent_address',$_POST['permanent_address'],20,3); */

    table_section_title(_("Contact Details"));
    //text_row(_("Office Phone:"), 'office_phone', null, 40, 40);
    text_row(_("Emergency Contact No:"), 'home_phone', null, 35, 100);
    text_row(_("Mobile Phone:*"), 'mobile_phone', null, 35, 12, null, '', '', false);
    text_row(_("Email:*"), 'email', null, 35, 100, null, '', '', false);
//	$res=get_hphone($_POST['home_phone'],'home_phone');	

    if (!isset($_POST['empl_id']) || $new_item) {
        //check_row(_("Create User Account*:"), 'register_user_acc', null);	

        table_section_title(_("Job Details"));

        //hrm_empl_grade_list( _("Grade :"), 'grade', null);	
        hidden('grade', 0);
        //hrm_empl_desig_group(_("Desgination Group *:"), 'desig_group', null);
        department_list_row(_("Department :"), 'department', null);
        desiggroup_list_row(_("Designation Group:"), 'desig_group', null, false, true);
        $desig_group = $_POST['desig_group'];
        desig_list_row(_("Designation:"), 'desig', null, false, false, $desig_group);

        //text_row(_("Desgination *:"), 'desig', null,  35, 100);
        //text_row(_("Basic Salary *:"), 'basic_salary', null, 30, 30);
        date_row(_("Date of Join") . ":", 'joining');
        hrm_empl_type_row(_("Employment Type*:"), 'empl_type', null, true);

        if (($_POST['empl_type'] == 3) || ($_POST['empl_type'] == 4)) {
            date_row(_("Contract End Date") . ":", 'contract_end_date', null, null, 0, 0, 0, null, true);
            $d1 = new DateTime($_POST['contract_end_date']);
            $d2 = new DateTime($_POST['joining']);
            $dt_diff = $d2->diff($d1);
            $years = $dt_diff->y;
            $months = $dt_diff->m;
            $days = $dt_diff->d;

            $difference = $years . ' year(s) ' . $months . ' month(s) ' . $days . ' day(s)';
            text_row(_("Contract Period"), 'contract_duration', $difference, 30, 90);
        } else {
            
        }
        /* 	if(!empty($_POST['contract_end_date'])){ 
          echo '<tr id="contract_date">';
          }
          else{
          echo '<tr id="contract_date" style="display:none">';
          }
          echo '<td class="label">Contract End Date</td><td><input type="text" name="contract_end_date" id="contract_end_date" class="date" size="10" maxlength="12" /></td>';
          echo '</tr>';
          if(!empty($_POST['contract_duration'])){
          echo '<tr id="contract_period">';
          }else{
          echo '<tr id="contract_period" style="display:none">';
          }

          echo '<td class="label">Contract Period</td><td><input type="text" name="contract_duration" id="contract_duration" maxlength="50"/> </td>';
          echo '</tr>'; */

        kv_empl_type_list_row(_("Employee Type:"), 'employee_type', null, TRUE);
        workcenter_list_row(_("Working Place*:"), 'working_place');
    }

    hrm_empl_status_list(_("Status*:"), 'status', null);

    table_section(2);

    div_start('payroll_tbl');

    // Add image upload for New Item 
    table_section_title(_("Personal Details"));

    $stock_img_link = "";
    $check_remove_image = false;
    if ($selected_id != '' && file_exists(company_path() . '/images/empl/' . empl_img_name($_POST['empl_id']) . ".jpg")) {
        $stock_img_link .= "<img id='empl_profile_pic' alt = '[" . $_POST['empl_id'] . ".jpg" . "]' src='" . company_path() . '/images/empl/' . empl_img_name($_POST['empl_id']) . ".jpg?nocache=" . rand() . "'" . " height='150' border='1'>";
        $check_remove_image = true;
    } else {
        $stock_img_link .= "<img id='empl_profile_pic' alt = '[" . $_POST['empl_id'] . ".jpg" . "]' src='" . $path_to_root . '/modules/ExtendedHRM/images/no-image.png' . "?nocache=" . rand() . "'" . " height='150' border='1'>";
    }
    label_row("&nbsp;", $stock_img_link);

    /* $max=ini_get('upload_max_filesize');
      $max_msg="(max. size :".$max."B)";
      $max_msg1 = "(max. size : 2MB)";
      $disp_max="<div style='color:red;margin-left:65px;margin-top:-16px;'>".$max_msg1."</div>"; */

    $disp_max = "<div style='color:red;margin-left:65px;margin-top:-16px;'>(max. size : 2 MB)</div>";

    kv_image_row(_("Photo (.jpg)") . ": " . $disp_max, 'pic', 'pic');

    if ($check_remove_image)
        check_row(_("Delete Image:"), 'del_image');

    //text_row(_("Skype ID:"), 'skype', null, 40, 40);	
    //text_row(_("LinkedIn:"), 'linkedin', null, 40, 40);
    kv_empl_gender_list_row(_("Gender:"), 'gender', null, true);
    date_row(_("Date of Birth") . ":", 'date_of_birth');
    text_row(_("Age:"), 'age', null, 3, 10);
    text_row(_("PF No :"), 'pf_number', null);
    text_row(_("PAN No :"), 'pan_no', null);
    text_row(_("Aadhaar No :"), 'aadhaar_no', null);
    text_row(_("ESI No :"), 'esi_no', null);
    text_row(_("PRAN No :"), 'pran_no', null);

    hrm_empl_marital_list_row(_("Marital Status:"), 'marital_status', null, true);

    //div_start('children_data');

    if ($_POST['gender'] == 2 && $_POST['marital_status'] == 2) {

        text_row(_("No. of Children :"), 'no_of_children', null, 5, 10);
    }

    //div_end();
    //kv_empl_hra_eligible_row(_("Eligible for HRA:"), 'eligible_hra',null,true);

    hidden('empl_page', 'info');
    if (!isset($_POST['empl_id']) || $new_item) {

        table_section_title(_("Payment Details - Earnings"));
        $EarAllowance = get_allowances('Earnings');
        $DedAllowance = get_allowances('Deductions');
        $basic_id = kv_get_basic();
        // if user has selected "Visiting Faculty(3) as employee type
        if (isset($_POST['employee_type']) && in_array($_POST['employee_type'], array(3,4))) {
            kv_text_row_ex(_("Fixed Consolidated Pay:"), '1', 15, 100, null, $_POST['1'], null, null, true, false);
        } else {

            kv_empl_hra_eligible_row(_("Eligible for HRA:"), 'eligible_hra', null, true);

            kv_basic_row(get_allowance_name($basic_id), $basic_id, 15, 100, null, true);

            while ($single = db_fetch($EarAllowance)) {


                if (($single['value'] == 'Percentage') && ($single['percentage'] > 0)) {

                    $sql = "SELECT id FROM fa_kv_allowances WHERE type='Earnings' AND value='Amount' ";
                    $res = db_query($sql, 'Return Allowances');
                    $basic_amount = 0;

                    while ($result = db_fetch($res)) {

                        $basic_amount = $basic_amount + get_post($result['id']);

                        $default_value = round(($basic_amount) * ($single['percentage'] / 100));
                        //display_error($default_value);
                        //echo($default_value.'|');
                    }
                } else
                    $default_value = null;

                if (($single['id'] != $basic_id) && ($single['basic'] != '1')) {
                    //$da += $basic_amount*$default_value;
                    //display_error($basic_amount);
                    //display_error($default_value);
                    if (($single['id'] == 4) && ($_POST['eligible_hra'] == 2)) {
                        $_POST['4'] = 0;

                        kv_text_row_ex(_($single['description'] . " " . ($single['type'] == 'Deductions' ? '(-)' : '') . " :"), $single['id'], 15, 100, null, $_POST['4'], null, null, true, false);
                    } else if ($single['id'] == 8) {

                        kv_text_row_ex(_($single['description'] . " " . ($single['type'] == 'Deductions' ? '(-)' : '') . " :"), $single['id'], 15, 100, null, 0, null, null, true, false);
                    } else {
                        kv_text_row_ex(_($single['description'] . " " . ($single['type'] == 'Deductions' ? '(-)' : '') . " :"), $single['id'], 15, 100, null, round($default_value), null, null, true, false);
                    }
                }  //$label, $name, $size, $max=null, $title=null, $value=null, $params=null, $post_label=null, $submit_on_change=false,$disabled=false
            }

            table_section_title(_("Deductions"));
            $prof_tax = kv_get_Tax_allowance();

            while ($single = db_fetch($DedAllowance)) {
                //print_r($single);
                $tot_sal = get_post('1') + get_post('2') + get_post('3');

                if ($single['value'] == 'Percentage' && $single['percentage'] > 0) {
                    //display_error($basic_amount);

                    $default_value = ($tot_sal) * ($single['percentage'] / 100);
                } else {

                    $default_value = null;
                }

                //if($single['id'] != $prof_tax){//Commented to show PF, Date - 14-Dec-2017

                kv_text_row_ex(_($single['description'] . " " . ($single['type'] == 'Deductions' ? '(' . $single['percentage'] . '%) (-)' : '') . " :"), $single['id'], 15, 100, null, round($default_value), null, null, true, TRUE);


                //}		
            }
        }


        table_section_title(_("Payment Mode"));

        hrm_empl_mop_list(_("Mode of Pay *:"), 'mod_of_pay', null, true);
        //	display_error($_POST['mod_of_pay']==2); 


        text_row(_("Bank Name *:"), 'bank_name', null, 15, 100);

        if ($_POST['mod_of_pay'] == 2 || $_POST['mod_of_pay'] == 3) {
            text_row(_("Accout Holder Name *:"), 'act_holder_name', null, null, 200);
            text_row(_("IFSC Code *:"), 'ifsc_code', null, 20, 200);
        }
        $ifsc_code = $_POST['ifsc_code'];




        text_row(_("Bank Account No *:"), 'acc_no', null, 15, 100);
    }

    end_outer_table(1);
    div_end();
    div_start('controls');
    if (!isset($_POST['empl_id']) || $new_item) {
        submit_center('addupdate', _("Add New Employee"), true, '', 'default');
        //display_error($_POST['empl_id']);
    } else {
        submit_center_first('addupdate', _("Update Employee Information"), '', @$_REQUEST['popup'] ? true : 'default');
        submit_return('select', get_post('selected_id'), _("Select this items and return to document entry."), 'default');
        submit('delete', _("Delete employee"), true, '', true);
        submit_center_last('cancel', _("Cancel"), _("Cancel Edition"), 'cancel');
    }
    div_end();
}


function empl_leave_data($empl_id) {
    br();
    //display_error($empl_id);
    div_start('details');
    $total_days = 31;
    $selected_empl = get_employee_whole_attendance($empl_id);
    if (!empty($selected_empl)) {
        start_table(TABLESTYLE);
        echo "<tr><td class='tableheader'>" . _("Year") . "</td><td class='tableheader'>" . _("Month") . "</td>";

        for ($kv = 1; $kv <= $total_days; $kv++) {
            echo "<td style='background-color:#e0db98' class='tableheader'>" . $kv . "</td>";
        }
        echo "<td class='tableheader'>" . _("Working Days") . "</td><td class='tableheader'>" . _("Leave Days") . "</td><td class='tableheader'>" . _("LOP Days") . "</td><td class='tableheader'>" . _("Payable Days") . "</td></tr>";

        $i = 1;
        foreach ($selected_empl as $single_month) {
            $fiscal_yr = get_fiscalyear($single_month['year']);
            echo '<tr style="text-align:center"><td>' . $fiscal_yr['begin'] . '-' . $fiscal_yr['end'] . '</td><td>' . date("F", strtotime("2016-" . $single_month['month'] . "-01")) . '</td>';
            $working_days = 0;
            $leave_Day = 0;
            $lop = 0;
            $total_days = cal_days_in_month(CAL_GREGORIAN, $single_month['month'], $single_month['cal_year']);
            $month_days = date("t", strtotime($single_month['year'] . "-" . $single_month['month'] . "-01"));
            for ($kv = 1; $kv <= 31; $kv++) {
                echo '<td>' . ($single_month[$kv] ? $single_month[$kv] : '-') . '</td>';
                if ($single_month[$kv] == 'A')
                    $lop += 1;
                if ($single_month[$kv] == 'HD')
                    $leave_Day += 0.5;
                if ($single_month[$kv] == 'HCL')
                    $leave_Day += 0.5;
                if ($single_month[$kv] == 'CL' || $single_month[$kv] == 'ML' || $single_month[$kv] == 'EL' || $single_month[$kv] == 'VL')
                    $leave_Day++;
                if ($single_month[$kv] == 'P')
                    $working_days++;
                if ($single_month[$kv] == 'SCL')
                    $leave_Day++;
                if ($single_month[$kv] == 'MTL') 
                    $leave_Day++;
                if ($single_month[$kv] == 'PTL') 
                    $leave_Day++;
                }
                    $Payable_days = $total_days - $lop;
                    echo '<td>' . $total_days . ' </td>  <td>' . $leave_Day . '</td> <td>' . $lop . ' </td> <td>' . $Payable_days . ' </td><tr>';
                }
                end_table(1);
            } else
            display_notification(_("No data Exist for the selected Employee."));

            start_table(TABLESTYLE);
            $employee_data = get_employee_desig($empl_id);
            $employee_gender = get_employee_gender($empl_id);

            $em_lv_count_yrly = get_employee_leave_count_fiscalyear($fiscal_yr['id'], $empl_id);
            $empl_casual_leaves_count_yearly = 0;
            $empl_vacation_leaves_count_yearly = 0;
            $empl_medical_leaves_count_yearly = 0;
            $empl_Earned_leaves_count_yearly = 0;
            $empl_special_casual_leaves_count_yearly = 0;
            $empl_maternity_leaves_count_yearly = 0;
            $empl_paternity_leaves_count_yearly = 0;
            while ($empl_leave_count_yearly = db_fetch($em_lv_count_yrly)) {
                for ($k = 1; $k <= $total_days; $k++) {
                    if ($empl_leave_count_yearly[$k] == 'CL') {
                        $empl_casual_leaves_count_yearly ++;
                    }
                    if ($empl_leave_count_yearly[$k] == 'HCL') {
                        $empl_casual_leaves_count_yearly += 0.5;
                    }
                    if ($empl_leave_count_yearly[$k] == 'VL') {
                        $empl_vacation_leaves_count_yearly ++;
                    }
                    if ($empl_leave_count_yearly[$k] == 'ML') {

                        $empl_medical_leaves_count_yearly ++;
                    }
                    if ($empl_leave_count_yearly[$k] == 'EL') {

                        $empl_Earned_leaves_count_yearly ++;
                    }
                    if ($empl_leave_count_yearly[$k] == 'SCL') {

                        $empl_special_casual_leaves_count_yearly ++;
                    }
                    if ($empl_leave_count_yearly[$k] == 'MTL') {
                        $empl_maternity_leaves_count_yearly ++;
                    }
                    if ($empl_leave_count_yearly[$k] == 'PTL') {

                        $empl_paternity_leaves_count_yearly ++;
                    }
                }
            }

            $leaves = get_leaves_data($employee_data['department'], $employee_data['desig_group'], $employee_data['desig'], $fiscal_yr['id'], $empl_id);

            echo "<tr><td class='tableheader'>" . _("Leave Type") . "</td><td class='tableheader'>" . _("No. of Days Used") . "</td><td class='tableheader'>" . ("Available Days") . "</td><td class='tableheader'>" . ("Total No. of Days") . "</td></tr>";
            $tot_utilized_casual_leaves = $leaves['no_of_cls'] - $empl_casual_leaves_count_yearly;
            $tot_utilized_vacation_leaves = $leaves['no_of_pls'] - $empl_vacation_leaves_count_yearly;
            $tot_utilized_medical_leaves = $leaves['no_of_medical_ls'] - $empl_medical_leaves_count_yearly;
            $tot_utilized_Earned_leaves = $leaves['no_of_el'] - $empl_Earned_leaves_count_yearly;
            $tot_utilized_scl_leaves = $leaves['no_of_spl_cls'] - $empl_special_casual_leaves_count_yearly;
            $tot_utilized_mtl_leaves = $leaves['no_of_mat_ls'] - $empl_maternity_leaves_count_yearly;
            $tot_utilized_ptl_leaves = $leaves['no_of_patern_ls'] - $empl_paternity_leaves_count_yearly;
            echo "<tr><td>" . _("No. of Casual Leaves") . "</td><td>" . $empl_casual_leaves_count_yearly . "</td><td>" . $tot_utilized_casual_leaves . "</td><td>" . $leaves['no_of_cls'] . "</td></tr>";
            echo "<tr><td>" . _("No. of Vacation Leaves") . "</td><td>" . $empl_vacation_leaves_count_yearly . "</td><td>" . $tot_utilized_vacation_leaves . "</td><td>" . $leaves['no_of_pls'] . "</td></tr>";
            echo "<tr><td>" . _("No. of Medical Leaves") . "</td><td>" . $empl_medical_leaves_count_yearly . "</td><td>" . $tot_utilized_medical_leaves . "</td><td>" . $leaves['no_of_medical_ls'] . "</td></tr>";
            echo "<tr><td>" . _("No. of Earned Leaves") . "</td><td>" . $empl_Earned_leaves_count_yearly . "</td><td>" . $tot_utilized_Earned_leaves . "</td><td>" . $leaves['no_of_el'] . "</td></tr>";
            if ($employee_gender['0'] == 1) {
                echo "<tr><td>" . _("No. of Special Casual Leaves") . "</td><td>" . $empl_special_casual_leaves_count_yearly . "</td><td>" . $tot_utilized_scl_leaves . "</td><td>" . $leaves['no_of_spl_cls'] . "</td></tr>";
            } else if ($employee_gender['0'] == 2) {
                echo "<tr><td>" . _("No. of Special Casual Leaves") . "</td><td>" . $empl_special_casual_leaves_count_yearly . "<td>" . $tot_utilized_scl_leaves . "</td><td>" . $leaves['no_of_spl_cls_female'] . "</td></tr>";
            }
            echo "<tr><td>" . _("No. of Maternity Leaves") . "</td><td>" . $empl_paternity_leaves_count_yearly . "</td><td>" . $tot_utilized_mtl_leaves . "</td><td>" . $leaves['no_of_mat_ls'] . "</td></tr>";
            echo "<tr><td>" . _("No. of Paternity Leaves") . "</td><td>" . $empl_paternity_leaves_count_yearly . "</td><td>" . $tot_utilized_ptl_leaves . "</td><td>" . $leaves['no_of_patern_ls'] . "</td></tr>";
            end_table(2);
            div_end();
        }

//-------------------------------------------------------------------------------------/

        function empl_payroll_data($empl_id) {
            global $SysPrefs, $path_to_root;
            br();

            $get_employees_list = get_emply_salary($empl_id);

            if (!empty($get_employees_list)) {
                start_table(TABLESTYLE, "width=90%");
                $th = array(_("Fiscal Year"), _("Month"));

                $Allowance = get_allowances('Earnings');
                while ($single = db_fetch($Allowance)) {
                    $th[] = $single['description'];
                }
                /*
                  $Allowance = get_allowances('Tax-E');
                  while ($single = db_fetch($Allowance)) {
                  $th[] = $single['description'];
                  }
                 * 
                 */
                $th[] = _("OT & Other Allowance");
                $th[] = _("Conveyance Allowance");
                $th[] = _("Leave Encashment");
                $th[] = _("Gross Pay");

                $Allowance = get_allowances('Deductions');
                while ($single = db_fetch($Allowance)) {
                    $th[] = $single['description'];
                }
                /*
                  $Allowance = get_allowances('Tax-D');
                  while ($single = db_fetch($Allowance)) {
                  $th[] = $single['description'];
                  }
                 * 
                 */
                $th1 = array(_("Adv Salary"), _("TDS"), _("Loan"), _("LOP Days"), _("Misc."), _("Total Deduction"), _("Net Salary"), _(""), _(""));
                $th_final = array_merge($th, $th1);

                table_header($th_final);


                $get_employees_list = get_emply_salary($empl_id);

                $Total_gross = $total_net = 0;
                foreach ($get_employees_list as $data_for_empl) {

                    if ($data_for_empl) {
                        start_row();
                        $fiscal_yr = get_fiscalyear($data_for_empl['year']);

                        $employee_leave_record = get_empl_attendance_for_month($data_for_empl['empl_id'], $data_for_empl['month'], $data_for_empl['year']);
                        label_cell($fiscal_yr['begin'] . ' ' . $fiscal_yr['end']);
                        label_cell(date("F", strtotime("2016-" . $data_for_empl['month'] . "-01")));
                        $EarAllowance = get_allowances('Earnings');
                        while ($single = db_fetch($EarAllowance)) {
                            label_cell($data_for_empl[$single['id']]);
                        }

                        label_cell($data_for_empl['ot_other_allowance']);
                        label_cell($data_for_empl['conveyance_allowance']);
                        label_cell($data_for_empl['leave_encashment']);
                        label_cell($data_for_empl['gross']);

                        $total_deduct = $data_for_empl['misc'] + $data_for_empl['loan'] + $data_for_empl['lop_amount'];
                        $Allowance = get_allowances('Deductions');
                        while ($single = db_fetch($Allowance)) {
                            label_cell($data_for_empl[$single['id']]);
                            $total_deduct += $data_for_empl[$single['id']];
                        }
                        label_cell($data_for_empl['adv_sal']);
                        label_cell($data_for_empl['tds']);
                        label_cell($data_for_empl['loan']);
                        label_cell($employee_leave_record);
                        //label_cell($data_for_empl['lop_amount']);
                        label_cell($data_for_empl['misc']);
                        label_cell($data_for_empl['deduct_tot']);
                        label_cell($data_for_empl['net_pay']);

                        $Total_gross += $data_for_empl['gross'];
                        $total_net += $data_for_empl['net_pay'];
                        //label_cell($data_for_empl['other_deduction']);
                                                label_cell('<a href="' . $path_to_root . '/modules/ExtendedHRM/payslip.php?employee_id=' . $data_for_empl['empl_id'] . '&month=' . $data_for_empl['month'] . '&year=' . $data_for_empl['year'] . '" onclick="javascript:openWindow(this.href,this.target); return false;"  target="_blank" > <img src="' . $path_to_root . '/themes/default/images/gl.png" width="12" height="12" border="0" title="GL"></a>');
                        if($data_for_empl['is_arrear'] == 1){
						$arrear_q_string = '&is_arrear=1';
                                                                        
                        label_cell('<a onclick="javascript:openWindow(this.href,this.target); return false;" href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep802.php?PARAM_0='.$year.'&PARAM_1='.$month.'&PARAM_2='.$data_for_empl["empl_id"].'&rep_v=yes&slip_id='.$data_for_empl['id'].$arrear_q_string.'" target="_blank" class="printlink"> <img src="'.$path_to_root.'/themes/default/images/print.png" width="12" height="12" border="0" title="Print"> </a>');
					}

                         if($data_for_empl['is_arrear'] != 1){
                        
                        label_cell('<a onclick="javascript:openWindow(this.href,this.target); return false;" href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep802.php?PARAM_0='.$year.'&PARAM_1='.$month.'&PARAM_2='.$data_for_empl["empl_id"].'&rep_v=yes&slip_id='.$data_for_empl['id'].'" target="_blank" class="printlink"> <img src="'.$path_to_root.'/themes/default/images/print.png" width="12" height="12" border="0" title="Print"> </a>');
                         }
    
                        end_row();
                    }
                }
                start_row();
                $Earnings_colum_count = get_allowances_count('Earnings');
                $Deductions_colum_count = get_allowances_count('Deductions');
                $gross_colm_cnt = $Earnings_colum_count + 4;
                $net_colm_cnt = $Deductions_colum_count + 4;
                echo " <td colspan='" . $gross_colm_cnt . "'> </td> <td><strong>Total Gross</strong></td><td><strong>" . $Total_gross . "</strong></td> ";
                echo "<td colspan='" . $net_colm_cnt . "' align='right'></td> <td colspan='2'><strong>Total Net Salary</strong></td> <td><strong>" . $total_net . "</strong></td><td> </td> <td> </td>";

                end_row();
                end_table(1);
            } else {
                display_notification(_("No data Exist for the selected Employee."));
            }
        }

//--------------------------------------------------------------------------------------
        if (isset($_POST["addupdate"])) {
            $last_id = $_POST["empl_id"];
        }
        start_form(true);

        if (db_has_employees()) {
            start_table(TABLESTYLE_NOBORDER);
            start_row();
            if (!empty($last_id)) {
                $Ajax->activate('selected_id');
                employee_list_cells(_("Select an Employee: "), 'selected_id', $last_id, _('New Employee'), true, check_value('show_inactive'));
                $Ajax->activate('details');
            } else {
                //display_error($last_id);
                employee_list_cells(_("Select an Employee: "), 'selected_id', null, _('New Employee'), true, check_value('show_inactive'));
                $new_item = get_post('selected_id') == '';
            }

            //check_cells(_("Show inactive:"), 'show_inactive', null, true);
            //display_error($new_item); 
            end_row();
            end_table();

            if (get_post('_show_inactive_update')) {
                $Ajax->activate('selected_id');
                set_focus('selected_id');
            }
        } else {

            hidden('selected_id', get_post('selected_id'));
        }
        div_start('details');

        $selected_id = get_post('selected_id');
        if (!$selected_id)
            unset($_POST['_tabs_sel']); // force settings tab for new customer

        tabbed_content_start('tabs', array(
            'personal' => array(_('Personal Info'), $selected_id),
            'job' => array(_('Job'), $selected_id),
            //'contacts' => array(_('Contacts'), $selected_id),		
            'education' => array(_('Education'), $selected_id),
            'previous_emplment' => array(_('Previous Employment'), $selected_id),
            //'professional_certification' => array(_('Professional Certification'), $selected_id),
            //'professional_certification' => array(_('Professional Certification'), $selected_id),
            'training' => array(_('Training'), $selected_id), //,		
            'leave' => array(_('Leave'), $selected_id),
            'payroll' => array(_('Payroll History'), $selected_id), //,
            'tax_detail' => array(_('Tax Detail'), $selected_id)
                //'assets' => array(_('Assets'), $selected_id)		
        ));

//display_error(get_post('_tabs_sel')); 
        switch (get_post('_tabs_sel')) {
            default:
            case 'personal':
                empl_personal_data($selected_id);
                break;
            case 'job':
                //	empl_job_data($selected_id);	
                $_GET['selected_id'] = $selected_id;
                $_GET['popup'] = 1;
                include_once($path_to_root . "/modules/ExtendedHRM/manage/add_empl_info_job.php");
                break;
            case 'education':
                $degree = new degree('degree', $selected_id, 'employee');
                $degree->show();
                break;
            case 'training':
                $training = new training('training', $selected_id, 'employee');
                $training->show();
                break;
            case 'previous_emplment':
                $exp = new experience('previous_emplment', $selected_id, 'employee');
                $exp->show();
                break;
            /* 	case 'professional_certification':
              $exp = new pfcertification('professional_certification', $selected_id, 'employee');
              $exp->show();
              break; */
            /* case 'professional_certification':
              $cert = new certification('professional_certification', $selected_id, 'employee');
              $cert->show();
              break; */
            case 'leave':
                empl_leave_data($selected_id);
                break;
            case 'payroll':
                empl_payroll_data($selected_id);
                break;
            case 'tax_detail':
                $_GET['selected_id'] = $selected_id;
                $_GET['popup'] = 1;
                include_once($path_to_root . "/modules/ExtendedHRM/manage/add_empl_tax_detail.php");
            case 'assets':
                break;
        }

//tabbed_content_end();

        br();

        div_end();

        hidden('popup', @$_REQUEST['popup']);

        end_form();
        ?>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script>
            /*
             $(document).ready(function(){
             
             //$('input[name="fixed_consolidated_pay"]').closest( "tr" ).hide();
             $('select[name="employee_type"]').change(function(){
             
             var emp_type = $(this).val();
             //If selected "Employee Type" is Visiting Faculty, show Fixed Consolidated Pay
             if(emp_type == 3){
             //$('input[name="fixed_consolidated_pay"]').closest( "tr" ).show();
             //$('input[name="eligible_hra"], input[name="1"], input[name="2"], input[name="3"], input[name="4"], input[name="8"], input[name="6"], input[name="10"], input[name="12"], input[name="eligible_hra"]').closest( "tr" ).show();
             }
             
             $.post("<?php echo $path_to_root; ?>/modules/ExtendedHRM/manage/employees.php", {selected_empId: emp_type}, function(result){
             
             $('input[name="empl_id"]').val(result.trim());
             });
             
             
             });
             
             });
             
             
             */
        </script>
        <?php
        end_page(@$_REQUEST['popup']);
        ?>

        <style>
            #empl_profile_pic { 
                border: 1px solid rgba(128, 128, 128, 0.68);
                border-radius: 2px;
            }
        </style>
        <link rel="stylesheet" href="<?php echo $path_to_root . "/js/jquery-ui.css" ?>">
        <script src="<?php echo $path_to_root . "/js/jquery-1.10.2.js" ?>"></script>
        <script src="<?php echo $path_to_root . "/js/jquery-ui.js" ?>"></script>
        <script src="<?php echo $path_to_root . "/js/readonly.js" ?>"></script>
<script>

    $(document).ready(function () {


        //var email = $('input[name=email]').val();
        // alert(email);
        //$('select[name=country]').val( 99 ).attr('selected', 'selected');
    });
    $('body').on('click', function () {
        var datestring = $('input[name=date_of_birth]').val();

        var date = myDateFormatter(datestring);


        var agestring = getAge(date);

        $('input[name="age"]').val(agestring);
    });

    function myDateFormatter(dateObject) {
        var d = new Date(dateObject);
        var day = d.getDate();
        var month = d.getMonth() + 1;
        var year = d.getFullYear();
        if (day < 10) {
            day = "0" + day;
        }
        if (month < 10) {
            month = "0" + month;
        }
        var date = day + "/" + month + "/" + year;

        return date;
    }
    ;

    function getAge(date) {
        var now = new Date();
        var today = new Date(now.getYear(), now.getMonth(), now.getDate());

        var yearNow = now.getYear();
        var monthNow = now.getMonth();
        var dateNow = now.getDate();

        var date = new Date(date.substring(6, 10),
                date.substring(3, 5),
                date.substring(0, 2) - 30
                );
        //alert(date);		 

        var yearDob = date.getYear();
        var monthDob = date.getMonth();
        //alert(monthDob);
        var dateDob = date.getDate();
        //alert(dateDob);
        var age = {};
        var ageString = "";
        var yearString = "";
        var monthString = "";
        var dayString = "";
        yearAge = yearNow - yearDob;

        if (monthNow >= monthDob) {
            var monthAge = monthNow - monthDob;
            //alert(monthAge);
        } else {
            yearAge--;
            var monthAge = 12 + monthNow - monthDob;
            //alert(monthAge);
        }

        if (dateNow >= dateDob) {
            var dateAge = dateNow - dateDob;

        } else {
            monthAge--;
            var dateAge = 31 + dateNow - dateDob;
            //alert(dateAge);

            if (monthAge < 0) {
                monthAge = 11;
                yearAge--;
            }
        }

        age = {
            years: yearAge,
            months: monthAge,
            days: dateAge
        };


        if (age.years > 1)
            yearString = " years";
        else
            yearString = " year";
        if (age.months > 1)
            monthString = " months";
        else
            monthString = " month";
        if (age.days > 1)
            dayString = " days";
        else
            dayString = " day";
        return age.years;

    }
    ;
    $('body').on('change', 'select[name="empl_salutation"]', function () {
        var salutation = $('select[name="empl_salutation"]').val();
        if (salutation == '6') {
            $('#salut_text').show();
        } else {
            $('#salut_text').hide();
        }
    });

    /* $('body').on('change','select[name="empl_type"]',function() {
     $('#contract_end_date').datepicker({
     dateFormat: 'mm/dd/yy',
     prevText: '<i class="fa fa-chevron-left"></i>',
     nextText: '<i class="fa fa-chevron-right"></i>',
     
     });  
     var emp_type = $('select[name="empl_type"]').val();
     
     if(emp_type == 3){
     $('#contract_date').show();
     $('#contract_period').show();
     }
     else{
     $('#contract_date').hide();
     $('#contract_period').hide();
     }
     }); */




//$('body').on('click','#same',function() { 
    /* $('body').on('click','input[name=same_as_correspond_address]',function() { 
     var checked_value = $('input[name=same_as_correspond_address]').is(':checked') ? 1 : 0;
     
     //var checked_value = $('#same').is(':checked') ? 1 : 0;
     var addr1 = $('input[name="addr_line1"]').val();
     var addr2 = $('input[name="addr_line2"]').val();
     var city1 = $('input[name="empl_city"]').val();
     var country1 = $('select[name="country"]').val();
     var state1 = $('select[name="empl_state"]').val();
     var pincode = $('input[name="pincode"]').val();
     if(checked_value == 1){
     
     $('input[name="per_addr_line1"]').val(addr1);
     $('input[name="per_addr_line2"]').val(addr2);
     $('input[name="empl_per_city"]').val(city1);
     $('select[name="per_country"]').val(country1);
     $('select[name="empl_per_state"]').val(state1);
     $('input[name="per_pincode"]').val(pincode);
     
     //Readonly Enable
     $('input[name="per_addr_line1"]').attr('readonly','readonly');
     $('input[name="per_addr_line2"]').attr('readonly','readonly');
     $('input[name="empl_per_city"]').attr('readonly','readonly');
     $('select[name="per_country"]').readonly(true);
     $('select[name="empl_per_state"]').readonly(true);
     $('input[name="per_pincode"]').attr('readonly','readonly');
     
     $('input[name="addr_line1"]').attr('readonly','readonly');
     $('input[name="addr_line2"]').attr('readonly','readonly');
     $('input[name="empl_city"]').attr('readonly','readonly');
     $('select[name="country"]').readonly(true);
     $('select[name="empl_state"]').readonly(true);
     $('input[name="pincode"]').attr('readonly','readonly');
     }else{
     
     $('input[name="per_addr_line1"]').val('');
     $('input[name="per_addr_line2"]').val('');
     $('input[name="empl_per_city"]').val('');
     $('select[name="per_country"]').val('99');
     $('select[name="empl_per_state"]').val('1475');
     $('input[name="per_pincode"]').val('');
     //Readonly Disable
     $('input[name="per_addr_line1"]').attr('readonly',false);
     $('input[name="per_addr_line2"]').attr('readonly',false);
     $('input[name="empl_per_city"]').attr('readonly',false);
     $('select[name="per_country"]').readonly(false);
     $('select[name="empl_per_state"]').readonly(false);
     $('input[name="per_pincode"]').attr('readonly',false);
     
     $('input[name="addr_line1"]').attr('readonly',false);
     $('input[name="addr_line2"]').attr('readonly',false);
     $('input[name="empl_city"]').attr('readonly',false);
     $('select[name="country"]').readonly(false);
     $('select[name="empl_state"]').readonly(false);
     $('input[name="pincode"]').attr('readonly',false);
     }
     }); */


    /*	$('input[name=1]').keypress(function(e){
     var code=e.keyCode || e.which;
     if(e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57))
     {
     alert("Only Numericals are allowed");
     return false;
     }
     });
     
     $('input[name=2]').keypress(function(e){
     var code=e.keyCode || e.which;
     if(e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57))
     {
     alert("Only Numericals are allowed");
     return false;
     }
     });
     
     $('input[name=3]').keypress(function(e){
     var code=e.keyCode || e.which;
     if(e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57))
     {
     alert("Only Numericals are allowed");
     return false;
     }
     });
     
     $('input[name=4]').keypress(function(e){
     var code=e.keyCode || e.which;
     if(e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57))
     {
     alert("Only Numericals are allowed");
     return false;
     }
     });
     
     $('input[name=8]').keypress(function(e){
     var code=e.keyCode || e.which;
     if(e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57))
     {
     alert("Only Numericals are allowed");
     return false;
     }
     }); */


    /* $('body').on('change','select[name="eligible_hra"]',function() {
     var value=$('select[name=eligible_hra]').val(); 
     //alert(value);
     if(value == 1){
     $('input[name="4"]').readonly(true);
     $('input[name="3"]').readonly(true);
     }
     else{
     $('input[name="4"]').readonly(true);
     $('input[name="3"]').readonly(false);
     }
     
     }); */

    function datedifffuntion() {

        var st_date = $('input[name=start_date]').val();
        var en_date = $('input[name=end_date]').val();

        var d1 = new Date(st_date),
                month1 = '' + (d1.getMonth() + 1),
                day1 = '' + d1.getDate(),
                year1 = d1.getFullYear();
        var start_date = d1;

        var d2 = new Date(en_date),
                month2 = '' + (d2.getMonth() + 1),
                day2 = '' + d2.getDate(),
                year2 = d2.getFullYear();
        var end_date = d2;

        /*	dy = d1.getYear()  - d2.getYear();
         dm = d1.getMonth() - d2.getMonth();
         dd = d1.getDate()  - d2.getDate();
         
         if (dd < 0) { dm -= 1; dd += 30; }
         if (dm < 0) { dy -= 1; dm += 12; }
         alert(dm);
         alert(dd); */
//console.log(dy, "Year(s),", dm, "Month(s), and", dd, "Days.");

        var months = (end_date.getFullYear() - start_date.getFullYear()) * 12;
        months -= start_date.getMonth() + 1;
        months += end_date.getMonth() + 1;

        months <= 0 ? 0 : months;

        /*	var dts = (end_date.getDate() - start_date.getDate());
         
         var years = (end_date.getFullYear() - start_date.getFullYear());
         
         var isLeap = ( (!(years % 4)) && ( (years % 100) || (!(years % 400)) ) );
         
         if (months == 2)
         return (isLeap) ? 29 : 28;
         var mon=30 + (months % 2);   */
        //var years = (end_date.getFullYear() - start_date.getFullYear()) * 12;
        /* if (d1.getMonth() < d2.getMonth())
         {
         months--;
         }  */
//  var diff_date =  end_date - start_date;
//	var num_years = Math.floor(diff_date/31536000000);
        //var num_months = Math.floor((diff_date % 31536000000)/2628000000);
//	var num_days = Math.floor(((diff_date % 31536000000) % 2628000000)/86400000); 

        // function daysInMonth(year, month) {
        /*   var isLeap = ( (!(num_years % 4)) && ( (num_years % 100) || (!(num_years % 400)) ) );
         
         if (num_months == 2)
         return (isLeap) ? 29 : 28;
         var mon=30 + (num_months % 2); */
        // } 

        /*	if (end_date.getMonth() <= start_date.getMonth())
         {
         months--;
         }  */



        $('input[name=experience]').val(months + ' month(s) ');
    }

//End
    $('body').on('change', 'input[name=empl_firstname]', function () {
        var empl_name = $('input[name=empl_firstname]').val();
        //alert(empl_name);
        var letters = /^[^ ][ A-Za-z\.]+$/;
        if (empl_name.match(letters)) {

            return true;
        } else {
            alert("Accept only Alphabets.");
            return false;
        }
    });


    $('body').on('change', 'input[name=addr_line1]', function () {
        var addr1 = $('input[name=addr_line1]').val();
//alert(addr1);
        var letters = /^[^ ][  a-zA-Z0-9\.,:\/-]+$/;
        if (addr1.match(letters)) {
            return true;
        } else {
            alert("Special characters not allowed.");
            return false;
        }
    });

    $('body').on('change', 'input[name=per_addr_line1]', function () {
        var per_addr1 = $('input[name=per_addr_line1]').val();
//alert(addr1);
        var letters = /^[^ ][  a-zA-Z0-9\.,:\/-]+$/;
        if (per_addr1.match(letters)) {
            return true;
        } else {
            alert("Special characters not allowed.");
            return false;
        }
    });


    /*$(document).ready(function(){
     var mobile_phone=$('input[name=mobile_phone]').val();
     $('input[name=mobile_phone]').keypress(function (e){
     
     var code =e.keyCode || e.which;
     if (e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57)) 
     {
     alert("Only Numericals are allowed");
     return false;
     }
     }); 
     
     }); */

    $('body').on('change', 'input[name=mobile_phone]', function () {

        var mob_no = $('input[name=mobile_phone]').val();
        var letters = /^[^ ][ 0-9,-]+$/;
        if (mob_no.match(letters)) {
            return true;
        } else {
            alert("Accept Numericals only.");
            return false;
        }
    });

    $('body').on('change', 'input[name=email]', function () {

        var value = $('input[name=email]').val();

        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value))
        {
            return (true);
        }
        alert("You have entered an invalid email address!")
        return (false);


    });

    $('body').on('change', 'input[name=bank_name]', function () {

        var value = $('input[name=bank_name]').val();
        var letters = /[^ ][ A-Za-z\.]+$/;

        if (value.match(letters))
        {
            return true;
        } else {
            alert("Accept only Alphabets.")
            return false;
        }
    });

    $('body').on('change', 'input[name=ifsc_code]', function () {

        var value = $('input[name=ifsc_code]').val();
        var letters1 = /^[^ ][ a-z]+$/;
        var letters = /^[^ ][ A-Z0-9]+$/;

        if (value.match(letters1)) {
            alert("Allow capital letters only.");
            return false;
        }
        if (value.match(letters))
        {
            return true;
        } else {
            alert("You have entered an invalid ifsc code.")
            return false;
        }
    });

    $('body').on('change', 'input[name=acc_no]', function () {

        var value = $('input[name=acc_no]').val();
        var letters = /^[^ ][0-9]+$/;

        if (value.match(letters))
        {
            return true;
        } else {
            alert("You have entered an invalid Account Number.")
            return false;
        }
    });

    $('body').on('change', 'input[name=pan_no]', function () {

        var value = $('input[name=pan_no]').val();
        var letters = /^[^ ][ a-zA-Z0-9]+$/;

        if (value.match(letters))
        {
            return true;
        } else {
            alert("You have entered an invalid PAN number.");
            $('input[name=pan_no]').focus();
            return false;
        }
    });

    $('body').on('change', 'input[name=aadhaar_no]', function () {

        var value = $('input[name=aadhaar_no]').val();
        // alert(value);
        var letters = /^[^ ][ 0-9]+$/;
        if (value.match(letters))
        {
            var str = value.length;
            // alert(str);
            if (str >= 12) {
                alert("Accept only 12 digits.");
                $('input[name=aadhaar_no]').focus();
                return false;
            } else {
                return true;
            }
        } else {
            alert("Accept Only Numericals.")
            return false;
        }



    });

    $('body').on('change', 'input[name=esi_no]', function () {

        var value = $('input[name=esi_no]').val();
        var letters = /^[^ ][ a-zA-Z0-9]+$/;

        if (value.match(letters))
        {
            return true;
        } else {
            alert("You have entered an invalid ESI number.");
            $('input[name=esi_no]').focus();
            return false;
        }
    });

    $('body').on('change', 'input[name=pran_no]', function () {

        var value = $('input[name=pran_no]').val();
        var letters = /^[^ ][ a-zA-Z0-9]+$/;

        if (value.match(letters))
        {
            return true;
        } else {
            alert("You have entered an invalid PRAN number.");
            $('input[name=pran_no]').focus();
            return false;
        }
        
    });
</script>
