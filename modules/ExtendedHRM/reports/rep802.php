<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
  /*  Payslip PDF
 * *************************************** */
$page_security = 'SA_OPEN';

$path_to_root = "../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

global $path_to_root, $systypes_array, $kv_empl_gender;

if (isset($_GET['rep_v'])) {
    include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
}
$year = (isset($_POST['PARAM_0']) ? $_POST['PARAM_0'] : (isset($_GET['PARAM_0']) ? $_GET['PARAM_0'] : 1));
$month = (isset($_POST['PARAM_1']) ? $_POST['PARAM_1'] : (isset($_GET['PARAM_1']) ? $_GET['PARAM_1'] : 01));
$empl_id = (isset($_POST['PARAM_2']) ? $_POST['PARAM_2'] : (isset($_GET['PARAM_2']) ? $_GET['PARAM_2'] : 0));
$comment = (isset($_POST['PARAM_3']) ? $_POST['PARAM_3'] : (isset($_GET['PARAM_3']) ? $_GET['PARAM_3'] : ''));
$is_arrear = isset($_GET['is_arrear']) ? $_GET['is_arrear'] : '';
$slip_id = isset($_GET['slip_id']) ? $_GET['slip_id'] : '';
//$destination = (isset($_POST['PARAM_4']) ? $_POST['PARAM_4'] : (isset($_GET['PARAM_4']) ? $_GET['PARAM_4'] : ''));
$_POST['REP_ID'] = 802;
if ($is_arrear == 1) {
    $rs_status = db_has_employee_payslip1($empl_id, $month, $year);
} else {
    $rs_status = db_has_employee_payslip($empl_id, $month, $year);
}
//==[20-7-2018]if($rs_status){=====[modified for arrea print out on add manage employees]
//	if ($destination)
//	include_once($path_to_root . "/reporting/includes/excel_report.inc");
//else
include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

$orientation = 'P';

$cols = array(10, 215, 300, 490);

$headers = array(_("Earnings"), _("Amount"), _("Deductions"), _("Amount"));

$aligns = array('left', 'left', 'left', 'left');

$rep = new FrontReport(_('Payslip'), "Payslip", user_pagesize(), 9, $orientation);

function get_empl_sal_details_file($empl_id, $month, $year, $slip_id) {

    if (is_numeric($slip_id)) {
        $sql = "SELECT * FROM " . TB_PREF . "kv_empl_salary	WHERE id=" . db_escape($slip_id);
    } else {
        $sql = "SELECT * FROM " . TB_PREF . "kv_empl_salary	WHERE empl_id=" . db_escape($empl_id) . " AND month=" . db_escape($month) . " AND year=" . db_escape($year);
    }

    return db_query($sql, "No transactions were returned");
}

$result = get_empl_sal_details_file($empl_id, $month, $year, $slip_id);
$newPaysl = '';   
$newPayslY = '';
$newPayslM = '';
$employeeId = '';
$other_earning = 0;
if ($myrow = db_fetch($result)) {
    $hra_deducted_accom_dmi = 0;
    if ($myrow['accom_hra_by_org'] == 2) {
        $hra_deducted_accom_dmi = $myrow['4'];
    } else {
        if ($myrow['accom_hra_by_org'] != 1)
            $hra_deducted_accom_dmi = $myrow['accom_hra_by_org'];
    }
    if ($myrow['ot_other_allowance'] != 0)
        $other_earning = $myrow['ot_other_allowance'];
    $name_and_dept = get_empl_name_dept($myrow['empl_id']);
    if ($is_arrear == 1) {
        $month_list[0] = explode(',', $myrow['paid_for_months_list']);
        $year_list[0] = $myrow['paid_for_f_year'];
        $newPayslY = $myrow['paid_for_f_year'];
        $newPayslM = $myrow['paid_for_months_list'];
    } else {
        $month_list = $myrow['month'];
       // $year_list = ;
        //$newPayslY = $myrow['year'];
        $year_list = $myrow['year'];
        $newPayslY = $myrow['year'];
        $newPayslM = $myrow['month'];
    }


    $lop_days = get_empl_attendance_for_month($empl_id, $month, $year);






    $employee_info = array(
        'id' => $myrow['id'],
        'empl_id' => $myrow['empl_id'],
        'empl_name' => $name_and_dept['name'],
        'department' => get_department_name($name_and_dept ['deptment']),
        'desig' => kv_get_empl_desig($myrow['empl_id']),
        'joining' => sql2date(get_employee_join_date($myrow['empl_id'])),
        'month' => $month_list,
        'year' => $year_list,
        'lop' => $lop_days,
        
    );
    if ($employee_info['id'])
        $result2 = get_nps($employee_info['id'], 3339);
    if ($result2)
        $_POST['nps_er'] = abs($result2);
    $baccount = get_empl_bank_acc_details($myrow['empl_id']);

    $rep->SetHeaderType('Header2');
    $rep->Font();
    $rep->Info(null, $cols, $headers, $aligns);

    //display_error(json_encode($myrow)."ygukgygug".json_encode($employee_info));

    $contacts = array(
        'email' => $name_and_dept['email'],
        'name2' => null,
        'name' => $name_and_dept['name'],
        'lang' => null,
    );




   $rep->SetCommonData($employee_info, $baccount, array($contacts), 'payslip');

    $rep->NewPage();
    


    $i = 1;

    $line = 1;

    $gp1 = $basic1 = $da1 = $hra1 = $conveyance1 = $epf1 = $sas1 = $pre_total = 0;
    $gp2 = $basic2 = $da2 = $hra2 = $conveyance2 = $epf2 = $sas2 = $tds1 = 0;
    $totalNew = $ar_in_epf = $ar_in_sas = $ar_in_tds = $net_payble = $gross_sal = 0;

//==>====[DIFRENCES VALIABLES]=====<==/
    $basic_diffrence = $da_diffrence = $gp_diffrence = $hra_diffrence = $conveyance_diffrence = 0;


    if ($is_arrear == 1) {
        $month_arr = explode(',', $newPayslM);
        $line = 1;
        $empl_det_by_selct_month = getBymonth1($newPayslY, $month_arr, $empl_id);

        if ($empl_id)
            $arrear_salary = get_all_employee_arrear_salary1($newPayslY, $month_arr, $empl_id);

        //  display_error(db_num_rows($empl_det_by_selct_month));
        $k = 0;
        while ($row = db_fetch($empl_det_by_selct_month)) {
            //Calculating/Dividing Arrear Salary for the selected month
            $pre_arrear_hra = $pre_basic = $pre_arrear_basic = $pre_arrear_da = $pre_arrear_pf = $pre_arrear_sas = 0;

            //$month_list1 = array();

            $j = 0;

            foreach ($arrear_salary as $sal_row) {

                $arrear_salary2 = get_all_employee_arrear_salary_for_pay_slip($newPayslY, $month_arr, $row['empl_id']);
                $pre_arrear_da = $pre_arrear_pf = $pre_arrear_sas = 0;
                //   if ($j != (count($arrear_salary))-1) {
                foreach ($arrear_salary2 as $val_row) {
                    //Count the number of month paid for arrears
                    $arrear_basic = $val_row['basic'];
                    $arrear_da = $val_row['da'];
                    $arrear_pf = $val_row['pf'];
                    $arrear_sas = $val_row['sas'];
                    $arrear_hra = $val_row['hra'];

                    if ($row['month'] == $val_row['paid_for_months_list']) {

                        $pre_arrear_basic += $arrear_basic;
                        $pre_arrear_hra += $arrear_hra;
                        $pre_arrear_da += $arrear_da;
                        $pre_arrear_pf += $arrear_pf;
                        $pre_arrear_sas += $arrear_sas;
                        //  display_error($row['month']."==".$arrear_da."==".$pre_arrear_da);
                    }
                }
                //   $j++;
                // }
            }





            // display_error($row['pre_da']);   
            //display_error($pre_arrear_da);
            $row['old_monthly_basic'] += $pre_arrear_basic;
            $row['pre_da'] += $pre_arrear_da;
            $row['pre_pf'] += $pre_arrear_pf;
            $row['pre_sas'] += $pre_arrear_sas;
            $row['pre_hra'] += $pre_arrear_hra;



            $pre_total_sum = $row['pre_basic'] + $row['pre_grade_pay'] + $row['pre_da'] + $row['pre_hra'] + $row['pre_conveyance'] + $row['pre_pf'] + $row['pre_sas'];
            // display_error($pre_total_sum);

            $monthly_basic = $row['basic_monthly'];

            // $monthly_basic = $row['pre_basic'];

            /**
             * Calculating One month Salary's components
             */
            $monthly_da = round($monthly_basic * $change_in_da / 100);

            // $diff_hra = ($total_diff_basic *20)/100; 

            $monthly_hra = $row['pre_hra'];
            

            $monthly_conveyance = $row['old_monthly_conveyance'];
            $monthly_sas = round(($monthly_basic + $monthly_da) * $change_in_sas / 100);
            $monthly_epf = round(($monthly_basic + $monthly_da) * $change_in_pf / 100);

            $total_basic = round($monthly_basic);

            $total_da = round($monthly_da);
            $total_hra = round($monthly_hra);
            $total_conveyance = round($monthly_conveyance);
            $total_sas = round($monthly_sas);
            $total_epf = round($monthly_epf);
            $total_grade_pay = 0;
            $gp2 += $total_grade_pay;
            //Calculating of Difference between Different Salary component
            $diff_basic = $total_basic - $row['pre_basic'];
            $diff_da = $total_da - $row['pre_da'];
            $diff_grade_pay = $total_grade_pay - $row['pre_grade_pay'];
            $diff_hra = $total_hra - $row['pre_hra'];

            $diff_conveyance = $total_conveyance - $row['pre_conveyance'];
            $diff_pf = $total_epf - $row['pre_pf'];
            $diff_sas = $total_sas - $row['pre_sas'];
            $diff_total_pf = ($diff_pf * 2);
            $diff_total_sas = ($diff_sas * 2);

            $diff_gross = $diff_basic + $diff_da + $diff_grade_pay + $diff_hra + $diff_conveyance + $diff_pf + $diff_sas;

            $total_deduction = $diff_total_pf + $diff_total_sas;
            $net_payment = $diff_gross - $total_deduction;
            //Calculating EPF
            //$diff_sum1 = round($diff_basic + $diff_da + $diff_grade_pay);
            if (isset($_POST['tds_' . $i])) {
                $net_payment = $net_payment - $_POST['tds_' . $i];
            }
            $total_gross_sum = round($total_basic + $total_da + $total_hra + $total_conveyance + $total_sas + $total_epf);
            $_POST['monthly_basic_' . $i] = $monthly_basic;


            /*
             * Add round function for round value Ritika 14-08-18
             */
            //check_cells('', 'sel_employee[]',$row['empl_id']);

            $total_hr = $row['pre_hra'];
            $pre_total_sum = $row['pre_basic'] + $row['pre_grade_pay'] + $row['pre_da'] + $total_hr + $row['pre_conveyance'] + $row['pre_pf'] + $row['pre_sas'];
            $gp1 += $row['pre_grade_pay'];

            $basic1 += $row['pre_basic'];


            $da1 += round($row['pre_da']);
            //display_error($row['pre_da']."==".$da1);

            $hra1 += round($total_hr);
            $conveyance1 += $row['pre_conveyance'];
            $epf1 += $row['pre_pf'];
            $sas1 += round($row['pre_sas']);
            $pre_total += $pre_total_sum;
            //Grade Pay
            //hidden('gp_'.$row['empl_id'], $_POST['gp_'.$row['empl_id']]);
            $basic2 += $total_basic;
            //DA
            $da2 += $total_da;
            $hra2 += $total_hra;
            $conveyance2 += $total_conveyance;
            $epf2 += $total_epf;
            $sas2 += $total_sas;
            //hidden('diff_da_'.$row['empl_id'], $total_diff_da);
            //PF
            $basic_diffrence += $diff_basic;
            $da_diffrence += $diff_da;
            $gp_diffrence += $diff_grade_pay;
            $hra_diffrence += $diff_hra;
            $conveyance_diffrence += $diff_conveyance;



            $ar_in_epf += round($diff_total_pf);

            $ar_in_sas += round($diff_total_sas);

            $tds += $_POST['tds_' . $i];
            $tds1 = $tds;

            $net_payble += round($net_payment);
            $gross_sal += round($total_gross_sum - $pre_total_sum);
        }
    }


    $text_value = 40;
    $line_value = 670;
    $earallows = array();
    $EarAllowance = get_allowances('Earnings');
    while ($EarAllow = db_fetch($EarAllowance)) {
        $earallows[] = $EarAllow;
    }

    $DedAllowance = get_allowances('Deductions');
    $dedallows = array();
    while ($DedAllow = db_fetch($DedAllowance)) {
        $dedallows[] = $DedAllow;
    }
    $earnings_count = get_allowances_count('Earnings');
    $deductions_count = get_allowances_count('Deductions');

    //display_error(json_encode($dedallows));
    if ($earnings_count > $deductions_count) {
        $count_final = $earnings_count;
    } else {
        $count_final = $deductions_count;
    }
    //display_error($count_final);
    $prevtotal = 0;
    $Value = -70;
    $total_deduction = 0;
    $count_difference = $count_final - $deductions_count;
    if ($count_difference >= 2)
        $else_deduct = 0;
    else
        $else_deduct = 3;
    if ($is_arrear == 1) {
        $total_de = 0;
        //prev 
        $rep->Text($text_value, 'Prev Basic Pay', 0, 0, $Value);
        $rep->Text(250, price_format($basic1), 0, 0, $Value);

        $rep->Text(330, 'SAS', 0, 0, $Value);
        $rep->Text(530, price_format($sas1), 0, 0, $Value);

        $total_de += $sas1;


        $rep->NewLine($line);

        $rep->Text($text_value, 'Grade Pay', 0, 0, $Value);
        $rep->Text(250, price_format($gp1), 0, 0, $Value);
        $rep->Text(330, $dedallows[1]['description'], 0, 0, $Value);
        $rep->Text(530, price_format($myrow[$dedallows[1]['id']]), 0, 0, $Value);
        $total_de += $myrow[$dedallows[1]['id']];
        $rep->NewLine($line);
//display_error($da1);
        $rep->Text($text_value, 'Prev DA', 0, 0, $Value);
        $rep->Text(250, price_format($da1), 0, 0, $Value);
        $rep->Text(330, 'EPF', 0, 0, $Value);
        $rep->Text(530, price_format($epf1), 0, 0, $Value);
        $total_de += $epf1;
        $rep->NewLine($line);

        $rep->Text($text_value, 'Prev HRA', 0, 0, $Value);
        $rep->Text(250, price_format($hra1), 0, 0, $Value);

        $rep->Text(330, 'HRA deducted (DMI accommodation)', 0, 0, $Value);
        $rep->Text(530, price_format($hra1), 0, 0, $Value);
        $total_de += $hra1;
        $rep->NewLine($line);

        $rep->Text($text_value, 'Prev Conveyance', 0, 0, $Value);
        $rep->Text(250, price_format($conveyance1), 0, 0, $Value);

        $rep->Text(330, 'EPF(Employer\'s Cont. )', 0, 0, $Value);
        $rep->Text(530, price_format($epf1), 0, 0, $Value);
        $total_de += $epf1;
        $rep->NewLine($line);

        $rep->Text($text_value, 'Prev EPF', 0, 0, $Value);
        $rep->Text(250, price_format($epf1), 0, 0, $Value);
        $rep->Text(330, 'NPS-SAS (8.33% - Employer\'s Cont.)', 0, 0, $Value);
        $rep->Text(530, price_format($sas1), 0, 0, $Value);
        $total_de += $sas1;
        $rep->NewLine($line);

        $rep->Text($text_value, 'Prev SAS', 0, 0, $Value);
        $rep->Text(250, price_format($sas1), 0, 0, $Value);
        $rep->Text(330, 'TDS', 0, 0, $Value);
        $rep->Text(530, price_format($tds1), 0, 0, $Value);
        $total_deduction += $tds1;

        $rep->NewLine(2);
        $rep->SetTextColor(255, 152, 0);
        $rep->Text($text_value, 'Prev Total', 0, 0, $Value, 'left', 1);
        $rep->Text(250, price_format($pre_total), 0, 0, $Value);

        $rep->Text(330, 'Prev Deduction', 0, 0, $Value, 'left', 1);
        $rep->Text(530, price_format($total_de), 0, 0, $Value);
        $rep->NewLine(2);
        $prevtotal = $total_de;
        //total 

        $arrear_slip = array($basic1, $gp1, $da1, $hra1, $conveyance1);
        $rep->SetTextColor(0, 0, 0);
        for ($vj = 0; $vj < $count_final; $vj++) {
            if (isset($earallows[$vj])) {

                if ($vj < 5) {
                    $rep->Text($text_value, 'Revised' . " " . $earallows[$vj]['description'], 0, 0, $Value);
                    $rep->Text(250, price_format($arrear_slip[$vj] + $myrow[$earallows[$vj]['id']]), 0, 0, $Value);
                }
            }
            if (isset($dedallows[$vj])) {
                if ($vj == 0) {
                    $rep->Text(330, $dedallows[$vj]['description'], 0, 0, $Value);
                    $rep->Text(530, price_format($sas1 + ($myrow[$dedallows[$vj]['id']] / 2)), 0, 0, $Value);
                    $total_deduction += $sas1 + ($myrow[$dedallows[$vj]['id']] / 2);
                } else if ($vj == 2) {
                    $rep->Text(330, $dedallows[$vj]['description'], 0, 0, $Value);
                    $rep->Text(530, price_format($epf1 + ($myrow[$dedallows[$vj]['id']] / 2)), 0, 0, $Value);
                    $total_deduction += $epf1 + ($myrow[$dedallows[$vj]['id']] / 2);
                } else {
                    $rep->Text(330, $dedallows[$vj]['description'], 0, 0, $Value);
                    $rep->Text(530, price_format($myrow[$dedallows[$vj]['id']]), 0, 0, $Value);
                    $total_deduction += $myrow[$dedallows[$vj]['id']];
                }
            } else if ($else_deduct == 0) {
                $hra_deducted_accom = 0;
                if ($myrow['accom_hra_by_org'] == 2) {
                    $hra_deducted_accom = $myrow['4'];
                } else {
                    $hra_deducted_accom = $hra1;
                }
                $rep->Text(330, 'HRA deducted (DMI accommodation)', 0, 0, $Value);
                $rep->Text(530, price_format($hra_deducted_accom), 0, 0, $Value);
                $total_deduction += $hra_deducted_accom;
                $else_deduct++;
            } elseif ($else_deduct == 1) {
                $rep->Text(330, 'EPF (Employer\'s Cont.)', 0, 0, $Value);
                $rep->Text(530, price_format($epf1 + ($myrow['12'] / 2)), 0, 0, $Value);
                $total_deduction += $epf1 + ($myrow['12'] / 2);
                $else_deduct++;
            }

            $rep->NewLine($line);
        }
        $rep->Text($text_value, 'Revisedl EPF', 0, 0, $Value);
        $rep->Text(250, price_format($epf1 + ($myrow['12'] / 2)), 0, 0, $Value);
        $rep->Text(330, 'NPS-SAS (8.33% - Employer\'s Cont.)', 0, 0, $Value);
        $rep->Text(530, price_format($sas1 + ($myrow['6'] / 2)), 0, 0, $Value);
        $total_deduction += $sas1 + ($myrow['6'] / 2);
        $rep->NewLine(1);
        $rep->Text($text_value, 'revised SAS', 0, 0, $Value);
        $rep->Text(250, price_format($sas1 + ($myrow['6'] / 2)), 0, 0, $Value);
        $rep->NewLine(1);
        $Value++;
    } else {


        $ctc = 0;
        $hj = 2;
        for ($vj = 0; $vj < $count_final; $vj++) {

            if ($ctc == 0) {
                $total_days = date("t", strtotime($months_with_years_list[$month]));
                $lop_amount = round($lop_days * $myrow[39] / $total_days, 2);
                if (array_key_exists(39, $myrow)) {
                    $rep->Text($text_value, 'Actual CTC', 0, 0, $Value);
                    $rep->Text(250, price_format($myrow[39] + $myrow[40]), 0, 0, $Value);

                    

                    $rep->NewLine($line);
                    $rep->Text($text_value, 'Food Coupon', 0, 0, $Value);
                    $rep->Text(250, price_format(round($myrow[40])), 0, 0, $Value);
                    
                  //  $total_deduction += $myrow[$dedallows[1]['id']];
                    $rep->NewLine($line);
                    $rep->Text($text_value, 'CTC (After Lop)', 0, 0, $Value);
                    $rep->Text(250, price_format(round($myrow[39] - $lop_amount)), 0, 0, $Value);
                        $rep->NewLine($line);
                 /*   $rep->Text(330, $dedallows[2]['description'], 0, 0, $Value);
                    $rep->Text(530, price_format($myrow[$dedallows[2]['id']]), 0, 0, $Value);
                    $total_deduction += $myrow[$dedallows[2]['']];*/
                    $rep->NewLine($line);
                    $ctc++;
                }
            } 
            if (isset($earallows[$vj])) {
                if($earallows[$vj]['id'] == 1){
                    
                $rep->Text($text_value, 'Basic Pay', 0, 0, $Value);
                $rep->Text(250, price_format($myrow[1]), 0, 0, $Value);
                $rep->Text(330, 'EPF(Employer\'s contribution)', 0, 0, $Value);
                $rep->Text(530, price_format($myrow[52]), 0, 0, $Value);
                $total_deduction += $myrow[52];
                $rep->newline();
                    
                }
                elseif($earallows[$vj]['id'] == 4){
                      $rep->Text($text_value, 'HRA', 0, 0, $Value);
                $rep->Text(250, price_format($myrow[4]), 0, 0, $Value);
                $rep->Text(330, 'EPF(Employee\'s contribution)', 0, 0, $Value);
                $rep->Text(530, price_format($myrow[52]), 0, 0, $Value);
                $total_deduction += $myrow[52];
                $rep->newline();
                }
                elseif($earallows[$vj]['id'] == 47){
                      
                    
               
                }elseif($earallows[$vj]['id'] == 41){
                $rep->Text($text_value, 'Special Allowance', 0, 0, $Value);
                $rep->Text(250, price_format($myrow[47]), 0, 0, $Value);
                 $rep->Text(330, 'ESI(Employer\'s contribution)', 0, 0, $Value);
                $rep->Text(530, price_format($myrow[49]), 0, 0, $Value);
                $total_deduction += $myrow[49];
                $rep->newline();
                $rep->SetTextColor(16, 123, 15);
                $rep->Font('bold');
                $rep->fontSize+=2;
                 $rep->Text($text_value, 'Salary', 0, 0, $Value);
                $rep->Text(250, price_format($myrow[1]+$myrow[4]+$myrow[47]), 0, 0, $Value);
                $rep->SetTextColor(0, 0, 0);
                $rep->Font();
                $rep->fontSize-=2;
                $rep->Text(330, 'ESI(Employee\'s contribution)', 0, 0, $Value);
                $rep->Text(530, price_format($myrow[43]), 0, 0, $Value);
                $total_deduction += $myrow[43];
                
                $rep->newline();
                $rep->Text(330, 'Food Coupon', 0, 0, $Value);
                $rep->Text(530, price_format($myrow[40]), 0, 0, $Value);
                $total_deduction += $myrow[40];
                $rep->newline();
                 $rep->Text($text_value, 'Employer\'s EPF', 0, 0, $Value);
                    $rep->Text(250,price_format($myrow[52]), 0, 0, $Value);
                $rep->Text(330, 'HRA deducted (ORG accommodation)', 0, 0, $Value);
                $rep->Text(530, price_format($hra_deducted_accom_dmi), 0, 0, $Value);
                $total_deduction += $hra_deducted_accom_dmi;
                $rep->newline();
                $rep->Text($text_value, 'Employer\'s ESI', 0, 0, $Value);
                    $rep->Text(250, price_format($myrow[41]), 0, 0, $Value);
                $rep->Text(330, 'Prof Tax.', 0, 0, $Value);
                    $rep->Text(530, price_format($myrow[10]), 0, 0, $Value);
                    $total_deduction += $myrow[10];
                    $rep->newline();
                    $rep->SetTextColor(16, 123, 15);
                $rep->Font('bold');
                $rep->fontSize+=2;
                      $rep->Text($text_value, '', 0, 0, $Value);
                    $rep->Text(250, price_format($myrow[41]+$myrow[52]), 0, 0, $Value);
                       $rep->SetTextColor(0,0,0);
                $rep->Font();
                $rep->fontSize-=2;
                    $rep->Text(330, 'TDS', 0, 0, $Value);
                $rep->Text(530, price_format($myrow['tds']), 0, 0, $Value);
                $total_deduction += $myrow['tds'];
                $rep->newline();
                $rep->Text($text_value,'Salary Advances', 0, 0, $Value);
                    $rep->Text(250, price_format($myrow[44]), 0, 0, $Value);
                     $rep->Text(330, 'TDS (on arrears, if any)', 0, 0, $Value);
                $rep->Text(530, price_format($tds_on_arrears), 0, 0, $Value);
                $total_deduction += $tds_on_arrears;
                   // $total_deduction += $myrow[$dedallows[$vj]['id']];
                    
               /* $rep->Text(330, 'TDS', 0, 0, $Value);
                $rep->Text(530, price_format($myrow['tds']), 0, 0, $Value);*/
                }elseif($earallows[$vj]['id'] == 44){
                    
                }
                elseif($earallows[$vj]['id'] == 46){
                    $rep->NewLine($line+1);
                     $rep->Text($text_value, 'CTC', 0, 0, $Value);
                    $rep->Text(250, price_format($myrow[39] + $myrow[40]), 0, 0, $Value);
                    $rep->NewLine($line);
                      $rep->Text($text_value, $earallows[$vj]['description'], 0, 0, $Value);
                    $rep->Text(250, price_format($myrow[$earallows[$vj]['id']]), 0, 0, $Value);
                              $rep->newline($line);
                                  $rep->SetTextColor(16, 123, 15);
                $rep->Font('bold');
                $rep->fontSize+=2;
                      $rep->Text($text_value,'', 0, 0, $Value);
                    $rep->Text(250, price_format($myrow[39] + $myrow[40] + $myrow[46]), 0, 0, $Value);
                    $rep->Text(330, 'Deduction', 0, 0, $Value);
                $rep->Text(530, price_format($total_deduction), 0, 0, $Value);
                       $rep->SetTextColor(0,0,0);
                $rep->Font();
                $rep->fontSize-=2;
                    $rep->newline($line);
                }
                elseif($earallows[$vj]['id'] == 52){
                 
                     
                
                }
                elseif ($earallows[$vj]['id'] != 39 && $earallows[$vj]['id'] != 40) {
                    $rep->Text($text_value, $earallows[$vj]['description'], 0, 0, $Value);
                    $rep->Text(250, price_format($myrow[$earallows[$vj]['id']]), 0, 0, $Value);
                }
            }
            if (isset($dedallows[++$hj]) && $dedallows[$hj]['id'] != 43 && $dedallows[$hj]['id'] != 49 && $dedallows[$hj]['id'] != 51 ){
                $rep->Text(330, $dedallows[$hj]['description'], 0, 0, $Value);
                $rep->Text(530, price_format($myrow[$dedallows[$hj]['id']]), 0, 0, $Value);
                $total_deduction += $myrow[$dedallows[$hj]['id']];
            } /*else if ($else_deduct == 0) {
                $rep->Text(330, 'HRA deducted (DMI accommodation)', 0, 0, $Value);
                $rep->Text(530, price_format($hra_deducted_accom_dmi), 0, 0, $Value);
                $total_deduction += $hra_deducted_accom_dmi;
                $else_deduct++;
            }*/
            if ($earallows[$vj]['id'] != 39 && $earallows[$vj]['id'] != 40   && $else_deduct != 0) {
                $rep->NewLine($line);
                $Value++;
            }
        }
    }


    /* $rep->Text(330, 'Prev Basic', 0, 0, $Value);
      $rep->Text(530, $basic1, 0, 0, $Value);
      $rep->NewLine(2); */

    if ($is_arrear != 1) {
        if ($ctc != 1) {
            $rep->Text($text_value, 'EPF (Employer\'s Cont.)', 0, 0, $Value);
            $rep->Text(250, price_format($myrow['12']), 0, 0, $Value);
            //$rep->Text($text_value,'Conveyance Allowance',0,0,$Value);
            //$rep->Text(250,price_format($myrow['conveyance_allowance']),0,0,$Value);
        }
        if ($ctc != 1) {
            $rep->Text(330, 'NPS-SAS (8.33% - Employer\'s Cont.)', 0, 0, $Value);
            $rep->Text(530, price_format($myrow['6']), 0, 0, $Value);
            $total_deduction += $myrow['6'];
            //display_error($text_value);
            //	$rep->Line($line_value-125, 0.00001,0,0);
            $rep->NewLine($line);
        }
    }

    $rep->Text($text_value, 'Leave Encashment', 0, 0, $Value);
    $rep->Text(250, price_format($myrow['leave_encashment']), 0, 0, $Value);

    $rep->NewLine($line);
    $tds_on_arrears = 0;
    $rep->Text($text_value, 'Arrears (If any)', 0, 0, $Value);
    $rep->Text(250, price_format(0), 0, 0, $Value);

    $rep->NewLine($line);
    $tds_on_arrears = 0;
    if ($is_arrear != 1) {
        if ($ctc != 1) {
            $rep->Text($text_value, 'SAS', 0, 0, $Value);
            $rep->Text(250, price_format($_POST['nps_er']), 0, 0, $Value);
            $rep->NewLine(2);
        }
    }
    $rep->Text($text_value, 'Other earnings', 0, 0, $Value);
    $rep->Text(250, price_format($other_earning), 0, 0, $Value);
    //$rep->Text(330, 'TDS (on arrears, if any)',0,0,$Value);
    //$rep->Text(530, price_format($tds_on_arrears),0,0,$Value);
    $total_deduction += $tds_on_arrears;
    if ($is_arrear == 1) {
        $rep->NewLine(2);
        $rep->SetTextColor(255, 152, 0);
        $rep->Text($text_value, 'Revised Total', 0, 0, $Value);
        $rep->Text(250, price_format($myrow['gross'] + $pre_total), 0, 0, $Value);
        $rep->Text(330, 'Revised Deduction', 0, 0, $Value);
        $rep->Text(530, price_format($total_deduction), 0, 0, $Value);
        $rep->NewLine(2);

        $Value++;
    }
    if ($else_deduct == 3) {
        $rep->Text($text_value, ' ', 0, 0, - $Value);
        $rep->Text(250, ' ', 0, 0, $Value);
        //$rep->Text(330, 'Loan Amount ',0,0,$Value);
        //$rep->Text(530, $myrow['loan'],0,0,$Value);
        //$total_deduction += $myrow['loan'];
        //	$rep->Line($line_value-125, 0.00001,0,0);
        $rep->NewLine(2);
        $Value++;

        $rep->Text($text_value, '', 0, 0, $Value);
        $rep->Text(250, '', 0, 0, -65);
        //$rep->Text(330, 'LOP Amount',0,0,$Value);
        //$rep->Text(530, $myrow['lop_amount'],0,0,$Value);
        //$total_deduction += $myrow['lop_amount'];
        //	$rep->Line($line_value-125, 0.00001,0,0);
        $rep->NewLine(2);
        $Value++;
    }

    $rep->row = 120;
    $rep->Line(205, 0.00001, 0, 0);
    /* Gross pay */





    $rep->SetTextColor(255, 152, 0);
    $rep->Text($text_value, 'Gross Pay(Total Earnings)', 0, 0, $Value);
    $rep->Text(250, price_format($myrow['gross']), 0, 0, $Value);
    $rep->SetTextColor(203, 0, 0);
    $rep->Text(330, 'Total Deduction', 0, 0, $Value);
    $rep->Text(530, price_format($total_deduction - $prevtotal), 0, 0, $Value);
    //	$rep->Line($line_value-150, 0.00001,0,0);
    $rep->NewLine($line);
    $rep->SetTextColor(0, 0, 0);

    /* $rep->Text($text_value, 'Advance Salary',0,0,1);
      $rep->Text(400, $myrow['adv_sal'],0,0,1);
      $rep->Line($line_value-225, 0.00001,0,0);
      $rep->NewLine(2);
     */
    $rep->Line(165, 0.00001, 0, 0);
    $rep->SetTextColor(16, 123, 15);
    $rep->Text($text_value, 'Net Amount ( Total Earnings - Total Deduction)', 0, 0, -40);
    $rep->Text(530, price_format($myrow['net_pay']), 0, 0, -40);

    //amount in words
    $words = no_to_words($myrow['net_pay']);

    if ($words != "") {
        $rep->Font('bold');

        $rep->TextCol(0, 7, "Amount In Words :  " . strtoupper($words) . " " . "Only", - 2);
        $rep->Font();
    }

    $rep->NewLine(1);
    $rep->Line(135, 0.00001, 0, 0);
    $rep->Line($line_value - 585, 0.00001, 0, 0);
    $rep->row = 180;
    if ($comment) {
        $rep->SetTextColor(0, 0, 0);
        $rep->Text($text_value, 'Comments', 0, 0, 65);
        $rep->Text(200, $comment, 0, 0, 65);  //$rep->NewLine(2);	
    }
    $rep->Line($line_value - 635, 0.00001, 0, 0);
}

if ($rep->row < $rep->bottomMargin)
    $rep->NewPage();
$rep->End(); //1, 'Payslip ');
/* }else{20-07-2018=====[modified for arrea print out on add manage employees]
  display_warning("No Payroll Entry Found For Selected Period.");
  } */

//===end

function no_to_words($no) {
    $words = array('0' => '', '1' => 'one', '2' => 'two', '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six', '7' => 'seven', '8' => 'eight', '9' => 'nine', '10' => 'ten', '11' => 'eleven', '12' => 'twelve', '13' => 'thirteen', '14' => 'fouteen', '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen', '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty', '30' => 'thirty', '40' => 'fourty', '50' => 'fifty', '60' => 'sixty', '70' => 'seventy', '80' => 'eighty', '90' => 'ninty', '100' => 'hundred', '1000' => 'thousand', '100000' => 'lakh', '10000000' => 'crore');
    if ($no == 0)
        return ' ';
    else {
        $novalue = '';
        $highno = $no;
        $remainno = 0;
        $value = 100;
        $value1 = 1000;
        while ($no >= 100) {
            if (($value <= $no) && ($no < $value1)) {
                $novalue = $words["$value"];
                $highno = (int) ($no / $value);
                $remainno = $no % $value;
                break;
            }
            $value = $value1;
            $value1 = $value * 100;
        }
        if (array_key_exists("$highno", $words))
            return $words["$highno"] . " " . $novalue . " " . no_to_words($remainno);
        else {
            $unit = $highno % 10;
            $ten = (int) ($highno / 10) * 10;
            return $words["$ten"] . " " . $words["$unit"] . " " . $novalue . " " . no_to_words($remainno);
        }
    }
}

?>