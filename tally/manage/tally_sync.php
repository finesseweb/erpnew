<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */


$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
//include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );


$version_id = get_company_prefs('version_id');

$js = '';
if ($version_id['version_id'] == '2.4.1') {
    if (!@$_GET['popup']) {
        if ($SysPrefs->use_popup_windows)
            $js .= get_js_open_window(900, 500);

        if (user_use_date_picker())
            $js .= get_js_date_picker();
    }
}else {
    if (!@$_GET['popup']) {
        if ($use_popup_windows)
            $js .= get_js_open_window(900, 500);
        if ($use_date_picker)
            $js .= get_js_date_picker();
    }
}
include($path_to_root . "/tally/includes/ui/tally.inc" );
include($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");


page(_("Payroll Inquiry"));
simple_page_mode(true);

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));
if (!isTallyOpen()) {
    display_error('warning: strongly recommended to open your tally software before using this module');
    end_page();
    exit;
}
if (isset($_GET['selected_id'])) {
    $_POST['selected_id'] = $_GET['selected_id'];
}
if (isset($_GET['month'])) {
    $_POST['month'] = $_GET['month'];
}
if (isset($_GET['year'])) {
    $_POST['year'] = $_GET['year'];
}

if (isset($_GET['Added'])) {
    display_notification(' The Employees Payroll Processed Successfully');
}


$selected_id = get_post('selected_id', '');
$month = get_post('month', '');
$year = get_post('year', '');
//$company_name = get_post('company_name', '');
//$openingbalance = get_post('openingbalance', '');

if (list_updated('month')) {
    $month = get_post('month');
    $Ajax->activate('totals_tbl');
}
start_form(true);

/*
 * 
 * SYNC LEDGERS 
 * TO TALLY ONLY
 * 
 */


if (isset($_POST['sync_l'])) {
    $requestXML = payHeader();
    $requestXMLL = importHeader();
    $result = getAllAccountListName();
    global $pay_heads;
    while ($myrow = db_fetch($result)) {
        if (getSalaryPayHeads($myrow['account_code'], $pay_heads))
            $requestXML .= payBody($myrow['tally_groups'], $myrow['account_name'], '');
        $requestXMLL .= addLedgerBody($myrow['tally_groups'], $myrow['account_name'], 0);
    }

    $requestXML .= payFooter();
    $requestXMLL .= importFooter();


    $filename = writeFile($requestXMLL, "../../tally/tally_core/createLFile.xml");
    addNew('createLFile.xml');


    //$requestXML = dummy();

    $filename = writeFile($requestXML, "../../tally/tally_core/PayHeads.xml");
    addNew('PayHeads.xml');
}



/*
 * SYNC EMPLOYEES 
 * TO AND FROM TALLY
 * 
 */


if (isset($_POST['sync_e'])) {

    global $kv_empl_gender, $hrm_empl_marital;


    $results = get_tally_empl_info();
    $desig_groupAll = get_employee_desig_group();


    foreach ($results as $key => $empl_info) {

        $martial_status = 1;
        $gender_status = 1;
        foreach ($hrm_empl_marital as $maritial_key => $marital_code) {
            if (strtolower($marital_code) == strtolower($empl_info['spousename'])) {
                $martial_status = $maritial_key;
                //display_error($marital_code);
            }
        }

        foreach ($kv_empl_gender as $gender_key => $gender_code) {
            if (strtolower($gender_code) == strtolower($empl_info['gender'])) {
                $gender_status = $gender_key;
                //display_error($gender_code);
            }
        }

        if (!has_empl_id($empl_info['empl_id'])) {
            add_employee($empl_info['empl_id'], '', '', $empl_info['empl_name'], '', '', '', '', $empl_info['address'], $empl_info['address'], 1, $empl_info['contactnumbers'], $empl_info['mobilenumber'], $empl_info['emailid'], $gender_status, date('m/d/Y', strtotime($empl_info['dob'])), $_POST['age'], $empl_info['pfaccountnumber'], $empl_info['pannumber'], base64_encode($empl_info['aadharnumber']), $empl_info['esinumber'], $empl_info['praccountnumber'], $martial_status, '', 1, $empl_info['location'], '', '', '', '', '', '', '', '', '');

            $group_id_all = get_employee_desig_all($empl_info['designation'])['desiggr'];
            $desig_id = get_employee_desig_all($empl_info['designation'])['id'];
            $deptNaam = get_employee_desig_departMent($empl_info['category']);

            $desig = 1;
            $desig_gr = 1;
            foreach ($group_id_all as $key => $value) {
                foreach ($desig_groupAll as $gr_key => $value1) {
                    if ($value == $value1) {
                        $desig_gr = $value;
                        $desig = $desig_id[$key];
                        break;
                    }
                }
            }



            $jobs_arr = array('empl_id' => $empl_info['empl_id'],
                'grade' => 0,
                'department' => $deptNaam[0],
                'desig_group' => $desig_gr,
                'employee_type' => strpos($empl_info['empl_id'], 'F') >= 0 ? 1 : 2,
                'desig' => $desig,
                'joining' => $empl_info['doj'],
                'empl_type' => 1,
                'effective_date' => '0000-00-00',
                'contract_end_date' => !empty($empl_info['contractexpirydate']) ? date('Y-m-d', strtotime($empl_info['contractexpirydate'])) : '0000-00-00',
                'eligible_hra' => 1,
                'working_branch' => $empl_info['location'],
                'ifsc_code' => $empl_info['ifscode'],
                'bank_name' => $empl_info['bankdetails'],
                'act_holder_name' => $empl_info['empl_name'],
                'acc_no' => $empl_info['bankaccountnumber']);
            if (count($deptNaam) > 0)
                Insert('kv_empl_job', $jobs_arr);
        }
        /* else
          {
          update_employee($empl_info['empl_id'], '', '', $empl_info['empl_name'], '', '', '', '', $empl_info['address'], $empl_info['address'], 1, $empl_info['contactnumbers'], $empl_info['mobilenumber'], $empl_info['emailid'], $gender_status , date('m/d/Y',strtotime($empl_info['dob'])), $_POST['age'], $empl_info['pfaccountnumber'], $empl_info['pannumber'], base64_encode($empl_info['aadharnumber']), $empl_info['esinumber'], $empl_info['praccountnumber'], $martial_status, '', 1, $empl_info['location'],'', '', '', '', '', '', '', '', '');


          } */
    }
    //===========[PAYROLL ENTRY IN TALLY]============//

    $requestXML = payRollHeader();
    $result = getDeptnameall();
    while ($desig_row = db_fetch($result)) {
        $requestXML .= costCategoryBody($desig_row['description'], $desig_row['description']);
    }
    $requestXML .= payRollFooter();
    $filename = writeFile($requestXML, $path_to_root . "/tally/tally_core/costcenter.xml");
    addNew($filename);
    $result = getAllEmployeeList();
    $requestXML = payRollHeader();
    while ($empl_row = db_fetch($result)) {
        $requestXML .= getEmplInfo($empl_row['empl_id'], 'employeesBody');
    }
    $requestXML .= payRollFooter();
    $filename = writeFile($requestXML, $path_to_root . "/tally/tally_core/allemployeesWithcostcenter.xml");
    addNew($filename);
}
/*
 * 
 * 
 * SYNC PAYROLL VOUCHER TO AND 
 * FROM TALLY
 * 
 * 
 */
//$result = get_tally_employees();
//echo "<pre>";print_r($result);
if (isset($_POST['sync_v_f'])) {
    $selected_id = get_post('selected_id', '');
    $month = get_post('month', '');
    $year = get_post('year', '');
    $result = get_tally_employees();
    $ipt_error = $inc = 0;
    $success_flag = False;

    foreach ($result as $key => $category_arr) {

        foreach ($category_arr as $cateogry => $empl_sal_info) {
            if (key_exists('date', $empl_sal_info)) {
                $f_year_month = getFisicalYearByDate($empl_sal_info['date']);
                $job_arr['month'] = $f_year_month['month'];
                $job_arr['empl_id'] = $empl_sal_info['name'];
                $job_arr['year'] = $f_year_month['f_year'];
                $job_arr['date'] = date('Y-m-d');
                $job_arr['1'] = abs(round($empl_sal_info['Basic Pay']));
                $job_arr['net_pay'] = abs(round($empl_sal_info['Salary Payable']));
                if (key_exists('TDS Payable', $category_arr))
                    $job_arr['tds'] = abs(round($empl_sal_info['TDS Payable']));
                $job_arr['47'] = abs(round($empl_sal_info['Special Allowance']));
                $job_arr['4'] = abs(round($empl_sal_info['House Rent Allowance']));
                if (key_exists('ESIC Payable', $category_arr)) {
                    $job_arr['49'] = abs(round($empl_sal_info["Employer's ESI Cont.@4.75%"]));
                    $job_arr['43'] = abs(round($empl_sal_info["ESIC Payable"]));
                    $job_arr['41'] = abs(round($empl_sal_info["ESIC Payable"]));
                }
                $job_arr['12'] = abs(round($empl_sal_info["EPF  Payable"]));
                $job_arr['52'] = abs(round($empl_sal_info["EPF  Payable"]));
                $deduction = 0;
                foreach ($empl_sal_info as $key => $deduct) {
                    if ($deduct > 0)
                        $deduction += round($deduct);
                }
                $job_arr['deduct_tot'] = $deduction;
                $job_arr['39'] = $job_arr['deduct_tot'] + abs($job_arr['net_pay']);
                $job_arr['gross'] = $job_arr['deduct_tot'] + abs($job_arr['net_pay']);

                /*
                 * is_paid function is is 
                 * if empl is exist in salry table it wont be added again
                 * or is_exist function is used coz employee has to be in job table 
                 * mean added to the company so first go to tally sync 
                 * and sync employees from there
                 */
                      
                if (!is_paid($f_year_month['month'], $f_year_month['f_year'], $empl_sal_info['name']) && is_exist($empl_sal_info['name'])) {
                    $pay_slip_id = Insert('kv_empl_salary', $job_arr);
                    $success_flag = true;



                    $salary_post = array(
                        1 => 1, //basic
                        2 => 4, //HRA
                        3 => 47, //special Allowance
                        4 => 40, //Food coupon
                        5 => 'tds', //TDS
                        6 => 46, //incentive (PLI)
                        7 => 12, //EPF employers contiribution
                        8 => 12, //EPF employee contribution
                        9 => 43, //ESIC employer's contribution
                        10 => 49, //ESIC employee's contribution 
                        11 => 44, //salary Advance
                        12 => 40,
                        13 => 12, //EPF
                        14 => 10, //Professional Tax
                        16 => 41, //ESIC 4.75
                        17 => 'leave_encashment'
                    );

                    $allowance = get_all_hrm_finance_setting();
                    $pending_salary_comp = array();
                    //For Debit
                    $debit_total = 0;
                    $credit_total = 0;
                    $cur_date = Today();
                    while ($single = db_fetch($allowance)) {//Loop over all salary components
                        if ($single['id'] == 15) {
                            $pending_salary_comp = $single;
                        }
                        if (($single['type'] == 'Salary') && $single['inactive'] == 0) {

                            if ($single['allowance_debit_gl_code'] != 0 && isset($job_arr[$salary_post[$single['id']]]) && is_numeric($job_arr[$salary_post[$single['id']]]) && $job_arr[$salary_post[$single['id']]] != 0) {
                                add_gl_trans(99, $pay_slip_id, $cur_date, $single['allowance_debit_gl_code'], 0, 0, 'Employee ' . $single['description'] . ' #' . $empl_sal_info['name'] . '-' . kv_get_empl_name($empl_sal_info['name']), abs($job_arr[$salary_post[$single['id']]]));
                                $debit_total += abs($job_arr[$salary_post[$single['id']]]);
                            }
                            if ($single['allowance_credit_gl_code'] != 0 && isset($job_arr[$salary_post[$single['id']]]) && is_numeric($job_arr[$salary_post[$single['id']]]) && abs($job_arr[$salary_post[$single['id']]]) != 0) {//Skip for 'None'
                                add_gl_trans(99, $pay_slip_id, $cur_date, $single['allowance_credit_gl_code'], 0, 0, 'Employee ' . $single['description'] . ' #' . $empl_sal_info['name'] . '-' . kv_get_empl_name($empl_sal_info['name']), '-' . abs($job_arr[$salary_post[$single['id']]]));
                                $credit_total += abs($job_arr[$salary_post[$single['id']]]);
                            }
                        }
                    }
                    //Processing Pending Salary after deduction PF,ASA,Prof. Tax, etc.
                    $pending_salary = $debit_total - $credit_total;
                    add_gl_trans(99, $pay_slip_id, $cur_date, $pending_salary_comp['allowance_credit_gl_code'], 0, 0, 'Employee ' . $pending_salary_comp['description'] . ' #' . $empl_sal_info['name'] . '-' . kv_get_empl_name($empl_sal_info['name']), -$pending_salary);
                    $inc++;
                }
            }
        }
    }
    if ($success_flag) {
        display_notification("$inc Payroll Voucher has been created from tally");
    } else {
        display_error('0 Vouchers are added to Erp!');
    }
    //unlink($path_to_root."/tally/tally_core/payroll_voucher.xml");
//display_error($selected_id);
    $requestXMLVoucher = tallyHeader(); //=============['Start Tally Header']
    // $res_empl = getSalEmployees($_POST['year'], $_POST['month']);

    $fisical_year1 = get_fiscalyear($year);
    $fis_year = getYearOnfisicalYear($year, $_POST['month'], $fisical_year1);

    $requestXMLVoucher .= addVoucherHeader('Payroll', '', $fis_year, '', 0, 0, '', '', '', strlen($_POST['month']) > 1 ? $_POST['month'] : '0' . $_POST['month']); //========[STARTS VOUCHER HEADER]

    $i = $empl_order = 1;
    if ($ipt_error == 0) {
        $get_employees_list = get_empl_ids_from_dept_id($selected_id);
        //  echo "<pre>"; print_r($get_employees_list);exit;
        $category = getDeptname($selected_id);
        $requestXMLVoucher .= voucherCategory($category);
        if ($selected_id != 0) {

            $Total_gross = $total_net = 0;
            $empl_order = 1;
            foreach ($get_employees_list as $single_empl) {
                //echo $single_empl;

                $data_for_empl_group = GetAll('kv_empl_salary', array('empl_id' => $single_empl, 'month' => $month, 'year' => $year));
//echo '<pre>'; print_r($data_for_empl_group);

                foreach ($data_for_empl_group as $data_for_empl) {

                    if ($data_for_empl) {

                        $sal_month_year_emplId = getMonthAndYear($data_for_empl['id']);

                        $dept_id = getDeptId($sal_month_year_emplId['empl_id']);

                        $result1 = getGlTrans(99, $data_for_empl['id']);
                        $sal_amount = getSalaryPayable(99, $data_for_empl['id'], 1219);


                        //  $category = getDeptname($dept_id);

                        $type_no = pay_slip_id($sal_month_year_emplId['month'], $sal_month_year_emplId['year'], $sal_month_year_emplId['empl_id']);
                        $tally_status = trans_l($type_no);
                        // display_error($type_no);

                        if ($tally_status == 0) {

                            $requestXMLVoucher .= addVoucherTopBody('Payroll', '', '', '', 0, 0, '', $sal_amount, $sal_month_year_emplId['empl_id'], '', $empl_order);
                            $pay_head_order = 1;
                            while ($row1 = db_fetch($result1)) {
                                if ($row1['account'] != 1219) {
                                    $result2 = getAllAccountListName($row1['account']);
                                    $ledger_name = db_fetch($result2);
                                    $ledger_name['account_name'] = $row1['account'] == 0 ? 'Salary Payable' : $ledger_name['account_name'];
                                    //========[DEBIT VOUCHER BODY]
                                    if ($row1['amount'] > 0) {
                                        $requestXMLVoucher .= addVoucherCreditBody('Payroll', '', date('Ymd', strtotime($row1['tran_date'])), $ledger_name['account_name'], 0, 0, '', $row1['amount'], $sal_month_year_emplId['empl_id'], $pay_head_order);
                                        $pay_head_order++;
                                    }//=======[CREDIT VOUCHER BODY]
                                    else if ($row1['amount'] < 0) {
                                        $requestXMLVoucher .= addVoucherDebitBody('Payroll', '', date('Ymd', strtotime($row1['tran_date'])), $ledger_name['account_name'], 0, 0, '', $row1['amount'], $sal_month_year_emplId['empl_id'], $pay_head_order);
                                        $pay_head_order++;
                                    }
                                }


                                //========[ENDS VOUCHER FOOTER]
                            }
                            $requestXMLVoucher .= addVoucherTopBodyEnd();
                            $empl_order++;
                            trans_l_update($type_no);
                        }
                    }
                }
            }
            $requestXMLVoucher .= endCategory();
            $requestXMLVoucher .= addVoucherFooter();
            $requestXMLVoucher .= tallyFooter(); //============[ENDS Tally Footer]
            //  display_error($requestXMLVoucher);
            $filename = writeFile($requestXMLVoucher, $path_to_root . "/tally/tally_core/payroll_voucher.xml");
            addNew('payroll_voucher.xml', 1);
        }
    }
}


/*
 * SYNC PURCHASE VOUCHER
 * 1. FIND DATE AND MEMO
 * 2. GET SUPLIER NAME OF THAT PURCHSE ITEM AND TOTAL COST OF THAT ITEM
 * 3. GET ALL THE VALUE LIKE CST GST,IST
 *  
 */

//$purchase_tally_info = purchaseInfo('Purchase');
//echo "<pre>"; print_r($purchase_tally_info);

if (isset($_POST['sync_p'])) {
        global $purchase_taxes_account;
        
              $purchase_tally_info = purchaseInfo('Purchase');
                
              foreach($purchase_tally_info as $supplier_name => $costkey){
                  display_error($supplier_name);
              }
              
              

    $requestPurchasedVoucherXML = tallyHeader();
    $date_result = getDatesFromGl();
    while ($date_row = db_fetch($date_result)) {
        // display_error($date_row['tran_date']);
        $purchace_gl_result = getPurchaceGl($date_row['tran_date']);
        $inc = 1;
        while ($purchase_row = db_fetch($purchace_gl_result)) {
            if ($purchase_row['tally_status'] == 0) {
                $requestPurchasedVoucherXML .= purchasedJuornalVoucherHeader(date('Ymd', strtotime($purchase_row['tran_date'])), $purchase_row['memo_'], $inc, 'Purchase');

                //=======[Total amount after adding gst CST IST]====//
                $tax_amt = 0;
                foreach ($purchase_taxes_account as $key => $ledger_name) {
                    if ($key != 'price')
                        $tax_amt += $purchase_row[$key] * $purchase_row['recved'];
                }
                $requestPurchasedVoucherXML .= purchasedJuornalPartyLedgerVoucher($purchase_row['supp_name'], -(($purchase_row['price'] * $purchase_row['recved']) + $tax_amt));
                //=======[THIS HAS BEEN DONE TO CREATE SEPARATE LEDGER INTO TALLY OF DIFFENT TAXXES]====//
                foreach ($purchase_taxes_account as $key => $ledger_name) {
                    $requestPurchasedVoucherXML .= purchasedJuornalVoucher($ledger_name, $purchase_row[$key] * $purchase_row['recved']);
                }
                $requestPurchasedVoucherXML .= purchasedJuornalVoucherfooter();
                updatePurchaseGl($purchase_row['tran_date']);
                $inc++;
            }
        }
    }
    $requestPurchasedVoucherXML .= tallyFooter();

    $filename = writeFile($requestPurchasedVoucherXML, $path_to_root . "/tally/tally_core/purchased_voucher.xml");
    addNew($filename);
}

/*
 * 
 * SYNC JUORNAL VOUCHERS
 * 
 */


if (isset($_POST['sync_j'])) {
    display_error('SYNC JUORNAL VOUCHERS');
}




if (db_has_employees()) {
    global $Ajax;
    div_start('pmt_header');
    display_note(_("Warning: This is only for testing purpose!"), 0, 1, "class='currentfg'");
    
        start_table(TABLESTYLE2);
            start_row();
            date_cells('From Date', 'from_date');
            date_cells('To Date', 'to_date');
            end_row();
        end_table();
    
    start_outer_table(TABLESTYLE, 'width="50%"');
    table_section(1, "50%");
    table_section_title('Sync All the Ledgers, Employees, and Vouchers');
    start_row();
    label_row('Sync Ledgers', '<button class="ajaxsubmit" type="submit" aspect="sync" name="sync_l" id="sync_l" value="Sync Ledgers"><span>Click here</span></button>');
    end_row();
    start_row();
    label_row('Sync Employee', '<button class="ajaxsubmit" type="submit" aspect="sync" name="sync_e" id="sync_e" value="Sync Employees"><span>Click here</span></button>');
    end_row();
    start_row();
    label_row('Sync Purchase Voucher', '<button class="ajaxsubmit" type="submit" aspect="sync" name="sync_p" id="sync_p" value="Sync Purchase Voucher"><span>Click here</span></button>');
    end_row();
    start_row();
    label_row('Sync Juronal Voucher', '<button class="ajaxsubmit" type="submit" aspect="sync" name="sync_j" id="sync_j" value="Sync Juronal Voucher"><span>Click here</span></button>');
    end_row();
    table_section(2, "50%");
    table_section_title('Sync Payroll Vouchers From here');
    start_row();
    kv_fiscalyears_list_cells(_("Fiscal Year:"), 'year', null, false);
    end_row();

    start_row();
    kv_current_fiscal_months_list_cell("Months", "month", null, false);
    end_row();

    start_row();
    department_list_cells(_("Select a Department: "), 'selected_id', null, _('No Department'), false, check_value('show_inactive'));
    end_row();
    start_row();

    label_cells('', '<button class="ajaxsubmit" type="submit" aspect="sync" name="sync_v_f" id="sync" value="Sync Vouchers"><span>Click here</span></button>');
    end_row();
    end_outer_table();
    $Ajax->activate('pmt_header');
} else {
    hidden('selected_id', get_post('selected_id'));
    hidden('month', get_post('month'));
    hidden('year', get_post('year'));
        //  hidden('company_name',get_post('company_name'));
        // hidden('opening_balance',get_post('opening_balance'));
}
div_start('sal_calculation');
//display_note('Warning: This only for testing purpose ');
start_table(TABLESTYLE_NOBORDER, "width=40%");
//label_row(" <center>**Here, Request and response tally of done.  </center>", '', null);
end_table();
if (!@$_GET['popup']) {
    end_form();
    end_page();
}
?>