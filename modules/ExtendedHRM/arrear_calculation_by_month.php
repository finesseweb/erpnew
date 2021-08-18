<?php
/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */
$page_security = 'HR_ARREAR_CALCULATION_MONTH';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
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

include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/db/empl_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
?>
<style type="text/css">
    .tablestyle input, .tablestyle td{
        width: 75px;
        text-align: center;
    }
</style>
<?php
$allAllowances = get_allowances();
$Allowances = array();
while ($single = db_fetch($allAllowances)) {
    $Allowances[] = $single;
}

function getAllowanceById($id) {
    global $Allowances;
    foreach ($Allowances as $row) {
        if ($row['id'] == $id) {
            return $row;
        }
    }
}

//Getting Current PF
$pf = getAllowanceById(12)['percentage'];
$asa = getAllowanceById(6)['percentage'];
simple_page_mode(true);
page(_("Arrear Calculation By Month"));

$from_month = get_post('from_month', '');
$to_month = get_post('to_month', '');
$f_year = get_post('f_year', '');
$change_in_basic = get_post('change_in_basic', '');
$change_in_da = get_post('change_in_da', '');
$is_da_diff = get_post('is_da_diff', '');
$is_hra_diff = get_post('is_hra_diff', '');


if (list_updated('to_month') || get_post('btn_go') || get_post('Refreshloan')) {
    $month = get_post('to_month');
    $Ajax->activate('totals_tbl');
    //$Ajax->activate('basic_EMP-F-002');
}

$months = kv_get_months_in_fiscal_year();
$hra_var = 20;
if ($_POST['change_in_hra'] != 20 && $_POST['change_in_hra'] != '')
    $hra_var = $_POST['change_in_hra'];

$month_list = array();
if ($from_month != '' && $to_month != '') {
    $f_months1 = array_keys($months);
    $from_month_index = array_search($from_month, $f_months1);
    $to_month_index = array_search($to_month, $f_months1);
    if ($to_month_index < $from_month_index) {
        display_warning(_("Selected months range is incorrect"));
    } else {
        for ($i = $from_month_index; $i <= $to_month_index; $i++) {
            $month_list[] = (int) $f_months1[$i];
        }
    }
}
if ($_GET['id'] == 1) {
    display_notification(_("Arrear Processed"));
}
$num_of_arrear_months = count($month_list);
if (!isset($_POST['change_in_basic'])) {
    $_POST['change_in_basic'] = 0;
}
if (!isset($_POST['change_in_da'])) {
    $da_row = getAllowanceById(3);
    $_POST['change_in_da'] = $da_row['percentage'];
}
$employee_id1 = $_POST['selected_id'];





$empl_det_by_selct_month = getBymonth($f_year, $month_list, $employee_id1);


$employees = get_all_employee_salary1($f_year, $month_list, $employee_id1);
if ($employee_id1)
    $arrear_salary = get_all_employee_arrear_salary1($f_year, $month_list, $employee_id1);

div_start('totals_tbl');
if (get_post('pay_arrear')) {
    $months = implode(',', $month_list);
    $current_f_year = get_company_pref('f_year');
    
    //$sel_empl = get_post('sel_empl','');
    
    while ($row = db_fetch($employees)) {
        $empl_id = get_post('sel_empl_' . $row['empl_id'], '');
        $f_year = get_post('f_year');
        $gross = get_post('tot_employer_cost_' . $row['empl_id'], '');
        $net_payment = get_post('net_payment_' . $row['empl_id'], '');
        $deduct_tot = $gross - $net_payment;
        $tds = get_post('tds_' . $row['empl_id'], '');


        $basic = get_post('diff_basic_' . $row['empl_id'], '');
        $da = get_post('diff_da_' . $row['empl_id'], '');
        $grade_pay = get_post('diff_grade_pay_' . $row['empl_id'], '');
        $hra = get_post('diff_hra_' . $row['empl_id'], '');
        $conveyance = get_post('diff_conveyance_' . $row['empl_id'], '');
        $sas = get_post('diff_sas_' . $row['empl_id'], '');
        $pf = get_post('diff_pf_' . $row['empl_id'], '');





        if (!empty($empl_id)) {
            $jobs_arr = array('empl_id' => $empl_id,
                'month' => date('n'),
                'year' => $current_f_year,
                'gross' => $gross,
                'deduct_tot' => $deduct_tot,
                'loan' => '',
                'date' => array(Today(), 'date'),
                'adv_sal' => '',
                'net_pay' => $net_payment,
                'misc' => '',
                'ot_other_allowance' => '',
                'lop_amount' => '',
                'conveyance_allowance' => '',
                'leave_encashment' => '',
                'tds' => $tds,
                'accom_hra_by_org' => '',
                'is_arrear' => 1,
                'paid_for_months_list' => $months,
                'paid_for_f_year' => $f_year,
                '1' => $basic,
                '3' => $da,
                '4' => $hra,
                '6' => $sas,
                '12' => $pf,
            );

            //display_warning(_($empl_id));
            $pay_slip_id = Insert('kv_empl_salary', $jobs_arr);
            $allowance = get_all_hrm_finance_setting();
            $salary_post = array(
                1 => $basic, //Basic
                2 => $da, //DA
                3 => '', //Grade Pay
                4 => $hra, //HRA
                5 => '', //Conveyance
                6 => $pf, //EPF-Employer Contribution
                7 => $sas, //NPS-SAS- Employer Contribution
                8 => '', //Leave Encashment
                9 => $sas, //NPS-SAS-Employee Contribution
                10 => '', //Professional Tax
                11 => $pf, //EPF-Employee Contribution
                12 => $tds //TDS
            );
            $date = Today();
            $pending_salary_comp = array();
            //For Debit
            $debit_total = 0;
            $credit_total = 0;
            while ($single = db_fetch($allowance)){
                if ($single['id'] == 13) {
                    $pending_salary_comp = $single;
                }
                if (($single['type'] == 'Salary') && $single['inactive'] == 0) { //Only process the active salary component
                    if ($single['allowance_debit_gl_code'] != 0 && isset($salary_post[$single['id']]) && !empty($salary_post[$single['id']]) && $salary_post[$single['id']] != 0) {
                        add_gl_trans(99, $pay_slip_id, $date, $single['allowance_debit_gl_code'], 0, 0, 'Salary Arrear ' . $single['description'] . ' #' . $empl_id . '-' . kv_get_empl_name($empl_id), $salary_post[$single['id']]);
                        $debit_total += $salary_post[$single['id']];
                    }
                    if ($single['allowance_credit_gl_code'] != 0 && isset($salary_post[$single['id']]) && !empty($salary_post[$single['id']]) && $salary_post[$single['id']] != 0) {//Skip for 'None'
                        add_gl_trans(99, $pay_slip_id, $date, $single['allowance_credit_gl_code'], 0, 0, 'Salary Arrear  ' . $single['description'] . ' #' . $empl_id . '-' . kv_get_empl_name($empl_id), -$salary_post[$single['id']]);
                        $credit_total += $salary_post[$single['id']];
                    }
                }
            }
            $pending_salary = $debit_total - $credit_total;
            add_gl_trans(99, $pay_slip_id, $date, $pending_salary_comp['allowance_credit_gl_code'], 0, 0, 'Salary Arrear ' . $pending_salary_comp['description'] . ' #' . $empl_id . '-' . kv_get_empl_name($empl_id), -$pending_salary);
        }
    }


    //Insert('kv_empl_salary', $jobs_arr);
    if (!empty($pay_slip_id)){
        $empl_det_by_selct_month = getBymonth($f_year, $month_list, $employee_id1);


$employees = get_all_employee_salary1($f_year, $month_list, $employee_id1);
if ($employee_id1)
    $arrear_salary = get_all_employee_arrear_salary1($f_year, $month_list, $employee_id1);
        $i = 1;
while ($row = db_fetch($empl_det_by_selct_month)) {
    //echo '<pre>'; print_r($row['old_monthly_hra']);
    $pre_arrear_da = $pre_arrear_pf = $pre_arrear_sas = 0;

  foreach($arrear_salary as $sal_row){  
         $arrear_salary2 = get_all_employee_arrear_salary2($f_year, $month_list, $row['empl_id']);
         $pre_arrear_da = $pre_arrear_pf = $pre_arrear_sas = 0;
            foreach($arrear_salary2 as $val_row){ 
               
                //Count the number of month paid for arrears
                $arrear_da = $val_row['da'];
                $arrear_pf = $val_row['pf'];
                $arrear_sas = $val_row['sas'];
                
                $per_month_da = $arrear_da;
                $per_month_pf = $arrear_pf;
                $per_month_sas = $arrear_sas;
                 //display_error($val_row['paid_for_months_list']);
                 if($row['month']==$val_row['paid_for_months_list']){
                  $pre_arrear_da += $per_month_da ;
                $pre_arrear_pf += $per_month_pf ;
                $pre_arrear_sas += $per_month_sas ;
                 }
           } 
        }
    $row['pre_da'] += $pre_arrear_da;
    $row['pre_pf'] += $pre_arrear_pf;
    $row['pre_sas'] += $pre_arrear_sas;
    /**
     * If "Change_in_basic" has been entered in negative, resultant value will be decreased, otherwise increased
     */
    if ($change_in_basic < 0) {
        $monthly_increased_basic = $row['pre_basic'] - round(($row['pre_basic'] * $change_in_basic / 100));
    } else {
        $monthly_increased_basic = $row['pre_basic'] + round(($row['pre_basic'] * $change_in_basic / 100));
    }


    if ($change_in_basic == 0) {//Only 
        //$default_increased_basic = $monthly_increased_basic * $num_of_arrear_months;
        if (isset($_POST['monthly_basic_']) && ($_POST['monthly_basic_'] != 0) && ($monthly_increased_basic != $_POST['monthly_basic_'])) {
            $monthly_increased_basic = $row['pre_basic'];
        }
    }

    //display_error($monthly_increased_basic);
    $monthly_basic_diff = $monthly_increased_basic - $row['old_monthly_basic'];
    /*
      //If Basic is Same, Calculate "DA" from Basic,
      //If Basic is different AND Grade Pay is ZERO, Calculate "DA" from difference of Basic.
      //If Basic is Different AND Grade Pay is not ZERO, Calculate "DA" from = 1st(Basic + Grade Pay) - New(Basic + Grade Pay)
      if($row['old_monthly_basic'] != $monthly_increased_basic){
      if($row['old_monthly_grade_pay'] == 0){
      $monthly_changed_da = round($monthly_basic_diff * $_POST['change_in_da']/100);
      }
      else{
      $pre_sum1 = $row['old_monthly_basic'] + $row['old_monthly_grade_pay'];
      $new_sum1 = $monthly_increased_basic + $row['old_monthly_grade_pay'];
      $diff_new_sum = $new_sum1 - $pre_sum1;
      $monthly_changed_da = round($diff_new_sum * $_POST['change_in_da']/100);
      }
      }
      else{
      $monthly_changed_da = round(($monthly_increased_basic + $row['old_monthly_grade_pay']) * $_POST['change_in_da']/100);
      }
     * 
     */
    $monthly_changed_da = round(($monthly_increased_basic + $row['old_monthly_grade_pay']) * $_POST['change_in_da'] / 100);

    $total_monthly_changed_da = round($monthly_changed_da);
    //$total_diff_da = ($row['pre_da'] < $total_monthly_changed_da)? $total_monthly_changed_da - $row['pre_da'] : $row['pre_da'] - $total_monthly_changed_da;
    if ($is_da_diff == 1) {
        $total_diff_da = round($total_monthly_changed_da - $row['pre_da']);
    } else {
        $total_diff_da = 0;
    }

    $total_salary = $monthly_increased_basic + $row['old_monthly_grade_pay'] + $monthly_changed_da;
    
    $pf_of_one_month = round($total_salary * $pf / 100);
 
    $asa_of_one_month = round($total_salary * $asa / 100);
    $total_diff_basic = $monthly_basic_diff;
    //$total_diff_da = round($total_monthly_changed_da - $row['pre_da']);
    //  $_POST['monthly_basic_'] = $monthly_increased_basic;
    
    

    $_POST['monthly_basic_'] = $monthly_increased_basic;
    $_POST['basic_'] = $monthly_increased_basic ;
    $_POST['gp_'] = $row['old_monthly_grade_pay'] ; //Calculating Grade Pay        
    $_POST['da_'] = $monthly_changed_da; //Calculating DA
    $_POST['pf_'] = $pf_of_one_month;
    $_POST['sas_'] = $asa_of_one_month;

    $total_diff_pf = $_POST['pf_'] - $row['pre_pf'];
    $total_diff_sas = $_POST['sas_'] - $row['pre_sas'];
    $total_payable = $total_diff_basic + $total_diff_da;

    //display_error($total_payable."==".$total_diff_pf."==".$total_diff_sas.'==='.$_POST['tds_']);


if($is_hra_diff)
    $diff_hra = ($total_diff_basic * $hra_var) / 100;
else
   $diff_hra = 0; 

    $net_payment2 = $total_payable - $total_diff_pf - $total_diff_sas + $diff_hra;
    if (isset($_POST['tds_'])) {
        $net_payment2 = $net_payment2 - $_POST['tds_'];
    }
 
        $total_employer_cost2 = round($total_payable + $total_diff_sas + $total_diff_pf + $diff_hra);
       
    start_row();
    //check_cells('', 'sel_employee[]',$row['empl_id']);
    // echo '<td>' . $i . '</td>';

    $employee_id == $empl_id;
    $accumulated_month .= " " . date("F", strtotime("2016-" . $row['month'] . "-01"));
    // label_cell(date("F", strtotime("2016-" . $row['month'] . "-01")));
    //  label_cell($row['month']);
    //basic
    // label_cell($row['pre_basic']);
    // text_cells('', 'monthly_basic_');
    // label_cell($row['pre_basic']);
    // label_cell($total_diff_basic);
    hidden('diff_basic_', $total_diff_basic);
    //Grade Pay
    // label_cell($row['pre_grade_pay']);
    hidden('gp_', $_POST['gp_']);

    //DA
    // label_cell($row['pre_da']);
    // label_cell($_POST['da_']);
    // label_cell($total_diff_da);
    hidden('diff_da_', $total_diff_da);

    //hra  
    // label_cell($row['old_monthly_hra']);
    // label_cell($row['old_monthly_hra'] + $diff_hra);
    // label_cell($diff_hra);
    hidden('diff_hra_', $diff_hra);
    

    //PF
    // label_cell($row['pre_pf']);
    // label_cell($_POST['pf_']);
    // label_cell($total_diff_pf);
    hidden('pf_', $_POST['pf_']);
    
    
    
    hidden('diff_pf_', $total_diff_pf);
   


    //SAS
   // label_cell($row['pre_sas']);
   // label_cell($_POST['sas_']);
   // label_cell($total_diff_sas);
    hidden('sas_', $_POST['sas_']);
    hidden('diff_sas_', $total_diff_sas);

    //text_cells('', 'sas_'.$row['empl_id']);

    hidden('net_payment_', $net_payment2);
 


    hidden('tot_employer_cost_', $total_employer_cost2);
  
    //label_cell($row['pre_tds']);
    //text_cells('', 'tds_');

    $tds_pay += $_POST['tds_'];

    label_cell($net_payment, 'style="background:#f9f9f9;"');
    label_cell($total_employer_cost);
    end_row();


    $gross2 = $_SESSION['tot_employer_cost_'.$i];
    $deduct_tot2 = $_SESSION['tot_employer_cost_'.$i] - $_SESSION['net_payment_'.$i];
  
    $current_f_year = get_company_pref('f_year');
//[Working]
    $month_arr = array('empl_id' => $row['empl_id'],
        'month' => $row['month'],
        'year' => $current_f_year,
        'gross' => $gross2,
        'deduct_tot' => $deduct_tot2,
        'loan' => '',
        'sal_id'=>$pay_slip_id,
        'date' => array(Today(), 'date'),
        'adv_sal' => '',
        'net_pay' => $_SESSION['net_payment_'.$i],
        'misc' => '',
        'ot_other_allowance' => '',
        'lop_amount' => '',
        'conveyance_allowance' => '',
        'leave_encashment' => '',
        'tds' => $_POST['tds_'],
        'accom_hra_by_org' => '',
        'is_arrear' => 1,
        'paid_for_months_list' => $row['month'],
        'paid_for_f_year' => $f_year,
        '1' => round($_POST['diff_basic_']),
        '3' => round($_POST['diff_da_']),
        '4' => round($_POST['diff_hra_']),
        '6' => round($_POST['diff_sas_']),
        '12' => round($_POST['diff_pf_'])
    );
    
    $arrear_id = Insert('kv_empl_salary_arear_by_month', $month_arr);
    $i++;
}
    
      meta_forward($path_to_root . '/modules/ExtendedHRM/arrear_calculation_by_month.php', "id=1");
    
    }
        else
        display_warning(_("select any employee !"));
}
start_form();
start_table(TABLESTYLE_NOBORDER, '', 2);
start_row();
employee_list_cells(_("Select an Employee: "), 'selected_id', $last_id, _('New Employee'), true, check_value('show_inactive'));
end_row();
end_table();
start_table(TABLESTYLE_NOBORDER);
start_row();


kv_current_fiscal_months_list_cell("From:", "from_month", null, TRUE);
kv_current_fiscal_months_list_cell("To:", "to_month", null, TRUE);
text_cells('Change in Basic(%)', 'change_in_basic', NULL, 3);
text_cells('DA(%)', 'change_in_da', NULL, 3);
text_cells('HRA(%)', 'change_in_hra', NULL, 3);
$selected_yes = ($is_da_diff == 1) ? 'selected="selected"' : '';
$selected_no = ($is_da_diff == 0) ? 'selected="selected"' : '';
$selected_yes1 = ($is_hra_diff == 1) ? 'selected="selected"' : '';
$selected_no1 = ($is_hra_diff == 0) ? 'selected="selected"' : '';
echo '<td>DA Diff. Include: <select style="width:100px;" name="is_da_diff"><option value="1" ' . $selected_yes . '>Yes</option><option value="0" ' . $selected_no . '>No</option></select><td>';
echo '<td>HRA Diff. Include: <select style="width:100px;" name="is_hra_diff"><option value="1" ' . $selected_yes1 . '>Yes</option><option value="0" ' . $selected_no1 . '>No</option></select><td>';
//text_cells($label, $name, $value, $size, $max, $title, $labparams, $post_label, $submit_on_change, $readonly)
kv_fiscalyears_list_cells(_("Fiscal Year:"), 'f_year', null, true);
submit_cells('btn_go', _("Go"), '', '', TRUE);
submit_cells('Refreshloan', _("Refresh"), '', _('Show Results'), true);
end_row();
end_table();


//Starting table of arrear calculation
start_table(TABLESTYLE, "width=90%");
$th1 = array(_(" "), _("Month"), _("Prev. Basic"), _("Basic/Month"), _("Total Basic"), _("Diff. in Basic"), _("Grade Pay"), _("Prev. DA"), _("DA"), _("Diff. in DA"), _("Prev. HRA"), _("HRA"), _("Diff. in HRA"), _("Prev. PF"), _("PF"), _("Payable PF"), _("Prev. SAS"), _("SAS"), _("Payable SAS"), _("Employer EPF cont."), _("Employer SAS cont."), _("Prev. TDS"), _("TDS"), _("Net Payment"), _("Total Employer Cost"));

table_header($th1);
//print_r($month_list);
//print_r($_POST['f_year']);

$accumulated_month = '';
$basic_diff = $da = $pf_dif = $newpf = $ppf = $prev_hra = $hra = $hra_dif = $da_dif = $prev_sas = $sas = $p_sas = $emp_epf_cont = $emp_sas_cont = $prev_tds = $tds_pay = $net_payment1 = $tot_emp_cost = 0;

//==>======[WORKING]=====<==//



$i = 1;
while($row = db_fetch($empl_det_by_selct_month)){
    //echo '<pre>'; print_r($row['old_monthly_hra']);
       
        
        foreach($arrear_salary as $sal_row){  
         $arrear_salary2 = get_all_employee_arrear_salary2($f_year, $month_list, $row['empl_id']);
         $pre_arrear_da = $pre_arrear_pf = $pre_arrear_sas = 0;
            foreach($arrear_salary2 as $val_row){ 
               
                //Count the number of month paid for arrears
               
                $arrear_da = $val_row['da'];
                $arrear_pf = $val_row['pf'];
                $arrear_sas = $val_row['sas'];
                
                $per_month_da = $arrear_da;
                $per_month_pf = $arrear_pf;
                $per_month_sas = $arrear_sas;
                 //display_error($val_row['paid_for_months_list']);
                 if($row['month']==$val_row['paid_for_months_list']){
                  $pre_arrear_da += $per_month_da ;
                $pre_arrear_pf += $per_month_pf ;
                $pre_arrear_sas += $per_month_sas ;
                 }
           }
              
            
            
        }
        $row['pre_da'] += $pre_arrear_da;
        $row['pre_pf'] += $pre_arrear_pf;
        $row['pre_sas'] += $pre_arrear_sas;
        /**
         * If "Change_in_basic" has been entered in negative, resultant value will be decreased, otherwise increased
         */
        
        
        if($change_in_basic < 0){
  $monthly_increased_basic = $row['pre_basic'] - round(($row['pre_basic'] * $change_in_basic / 100));            
        }
        else{
            $monthly_increased_basic = $row['pre_basic'] + round(($row['pre_basic'] * $change_in_basic / 100));
        }
        
       
		if($change_in_basic == 0){//Only 
			//$default_increased_basic = $monthly_increased_basic * $num_of_arrear_months;
			if(isset($_POST['monthly_basic_']) && ($_POST['monthly_basic_'] != 0) && ($monthly_increased_basic != $_POST['monthly_basic_']) ){
				$monthly_increased_basic = $row['pre_basic'];            
			}
		}
                
       //display_error($monthly_increased_basic);
        $monthly_basic_diff = $monthly_increased_basic - $row['old_monthly_basic'];
        /*
        //If Basic is Same, Calculate "DA" from Basic,
        //If Basic is different AND Grade Pay is ZERO, Calculate "DA" from difference of Basic.
        //If Basic is Different AND Grade Pay is not ZERO, Calculate "DA" from = 1st(Basic + Grade Pay) - New(Basic + Grade Pay)
        if($row['old_monthly_basic'] != $monthly_increased_basic){
            if($row['old_monthly_grade_pay'] == 0){
                $monthly_changed_da = round($monthly_basic_diff * $_POST['change_in_da']/100);
            }
            else{
                $pre_sum1 = $row['old_monthly_basic'] + $row['old_monthly_grade_pay'];
                $new_sum1 = $monthly_increased_basic + $row['old_monthly_grade_pay'];
                $diff_new_sum = $new_sum1 - $pre_sum1;
                $monthly_changed_da = round($diff_new_sum * $_POST['change_in_da']/100);
            }            
        }
        else{
            $monthly_changed_da = round(($monthly_increased_basic + $row['old_monthly_grade_pay']) * $_POST['change_in_da']/100);
        }
         * 
         */       
        $monthly_changed_da = round(($monthly_increased_basic + $row['old_monthly_grade_pay']) * $_POST['change_in_da']/100);
        
        $total_monthly_changed_da = round($monthly_changed_da );
        //$total_diff_da = ($row['pre_da'] < $total_monthly_changed_da)? $total_monthly_changed_da - $row['pre_da'] : $row['pre_da'] - $total_monthly_changed_da;
        if($is_da_diff == 1){
            $total_diff_da = round($total_monthly_changed_da - $row['pre_da']);
        }
        else{
            $total_diff_da = 0;
        }
        
        $total_salary = $monthly_increased_basic + $row['old_monthly_grade_pay'] + $monthly_changed_da;
        $pf_of_one_month = round($total_salary *  $pf / 100);
        $asa_of_one_month = round($total_salary *  $asa / 100);
        $total_diff_basic = $monthly_basic_diff;   
       
        //$total_diff_da = round($total_monthly_changed_da - $row['pre_da']);
        
        
          //  $_POST['monthly_basic_'] = $monthly_increased_basic;
         $_POST['monthly_basic_'] = $monthly_increased_basic;
        $_POST['basic_'] = $monthly_increased_basic ;
        $_POST['gp_'] = $row['old_monthly_grade_pay'];//Calculating Grade Pay        
        $_POST['da_'] = $monthly_changed_da ;//Calculating DA
       $_POST['pf_'] = $pf_of_one_month ;
        $_POST['sas_'] = $asa_of_one_month ;
        
        $total_diff_pf = $_POST['pf_'] - $row['pre_pf'];
        $total_diff_sas = $_POST['sas_'] - $row['pre_sas'];
        $total_payable = $total_diff_basic + $total_diff_da;

        //display_error($total_payable."==".$total_diff_pf."==".$total_diff_sas.'==='.$_POST['tds_']);
            


        if($is_hra_diff){
        $diff_hra = ($total_diff_basic *$hra_var)/100; 
        }
        else
            $diff_hra = 0;
        $net_payment = $total_payable - $total_diff_pf - $total_diff_sas + $diff_hra;
        if(isset($_POST['tds_'])){
            $net_payment = $net_payment - $_POST['tds_'];
        }
        
        $total_employer_cost = round($total_payable + $total_diff_sas + $total_diff_pf+$diff_hra);
  
    
    
    
     start_row();
    //check_cells('', 'sel_employee[]',$row['empl_id']);
    
     echo '<td>'.$i.'</td>';
    
    $employee_id == $row['empl_id']; 
    
    $accumulated_month .= " ".date("F", strtotime("2016-" . $row['month'] . "-01"));
    
    label_cell(date("F", strtotime("2016-" . $row['month'] . "-01"))); 
  //  label_cell($row['month']);
    //basic
    label_cell($row['pre_basic']);
    text_cells('', 'monthly_basic_');
    label_cell($row['pre_basic']);
    label_cell($total_diff_basic);
    hidden('diff_basic_', $total_diff_basic);
    $basic_diff +=$total_diff_basic;
    //Grade Pay
    label_cell($row['pre_grade_pay']);
    hidden('gp_', $_POST['gp_']);
    
    //DA
    label_cell($row['pre_da']);
    label_cell($_POST['da_']);
    label_cell($total_diff_da);
    hidden('diff_da_', $total_diff_da);
    $da+=$_POST['da_'];
    $da_dif+=$total_diff_da;
        
  //hra  
      label_cell(round($row['old_monthly_hra']));
    label_cell(round($row['old_monthly_hra']+$diff_hra));
    label_cell(round($diff_hra));
    hidden('diff_hra_', $diff_hra);
        $hra_dif+=round($diff_hra);
        $prev_hra+=round($row['old_monthly_hra']);
        $hra+=round($row['old_monthly_hra']+$diff_hra);
    
    //PF
    label_cell(round($row['pre_pf']));
    label_cell(round($_POST['pf_']));
    label_cell(round($total_diff_pf));
    hidden('pf_', round($_POST['pf_']));
    hidden('diff_pf_', round($total_diff_pf));
    $newpf+=round($_POST['pf_']);
    $ppf+=round($row['pre_pf']);
     $pf_dif+=round($total_diff_pf);
    
     
     //SAS
    label_cell(round($row['pre_sas']));
    label_cell(round($_POST['sas_']));
    label_cell(round($total_diff_sas));
    hidden('sas_', round($_POST['sas_']));
    hidden('diff_sas_', round($total_diff_sas));
    $prev_sas += round($row['pre_sas']);
    $sas+=round($_POST['sas_']);
    $p_sas+=round($total_diff_sas);
    //text_cells('', 'sas_'.$row['empl_id']);
    
    hidden('net_payment_', round($net_payment));
    $net_payment1+=round($net_payment);
    $_SESSION['net_payment_'.$i] = round($net_payment);
   
    label_cell($total_diff_pf);
    $emp_epf_cont+=$total_diff_pf;
    label_cell($total_diff_sas);
    $emp_sas_cont+=$total_diff_sas;
    
    
    hidden('tot_employer_cost_', round($total_employer_cost));
    $_SESSION['tot_employer_cost_'.$i] = round($total_employer_cost);
    $tot_emp_cost+=$total_employer_cost;
     $_POST['tot_employer_cost_'.$i] = round($total_employer_cost);
    label_cell(round($row['pre_tds']));
    text_cells('', 'tds_');
    
    $tds_pay+=round($_POST['tds_']);
    
    label_cell($net_payment,'style="background:#f9f9f9;"');
    label_cell($total_employer_cost);
    end_row(); 
    $i++;
}


while ($row = db_fetch($employees)) {
    //Calculating/Dividing Arrear Salary for the selected month

    $pre_arrear_da = $pre_arrear_pf = $pre_arrear_sas = 0;

    foreach ($arrear_salary as $sal_row) {
        //  display_error($row['empl_id']);
        //Count the number of month paid for arrears
        $arrear_da = $sal_row['da'];
        $arrear_pf = $sal_row['pf'];
        $arrear_sas = $sal_row['sas'];
        $month_list1 = explode(',', $sal_row['paid_for_months_list']);
        $num_months1 = count($month_list1);
        $per_month_da = $arrear_da / $num_months1;
        $per_month_pf = $arrear_pf / $num_months1;
        $per_month_sas = $arrear_sas / $num_months1;
        $count = 0;
        foreach ($month_list1 as $month) {
            if (in_array($month, $month_list)) {
                $count++;
            }
        }
        $pre_arrear_da += $per_month_da * $count;
        $pre_arrear_pf += $per_month_pf * $count;
        $pre_arrear_sas += $per_month_sas * $count;
    }
    $row['pre_da'] += $pre_arrear_da;
    $row['pre_pf'] += $pre_arrear_pf;
    $row['pre_sas'] += $pre_arrear_sas;
    /**
     * If "Change_in_basic" has been entered in negative, resultant value will be decreased, otherwise increased
     */
    if ($change_in_basic < 0) {
        $monthly_increased_basic = $row['old_monthly_basic'] - round(($row['old_monthly_basic'] * $change_in_basic / 100));
    } else {
        $monthly_increased_basic = $row['old_monthly_basic'] + round(($row['old_monthly_basic'] * $change_in_basic / 100));
    }
    //If user has entered the basic salary manually
    if ($change_in_basic == 0) {//Only 
        //$default_increased_basic = $monthly_increased_basic * $num_of_arrear_months;
        if (isset($_POST['monthly_basic_' . $row['empl_id']]) && ($_POST['monthly_basic_' . $row['empl_id']] != 0) && ($monthly_increased_basic != $_POST['monthly_basic_' . $row['empl_id']])) {
            $monthly_increased_basic = $_POST['monthly_basic_' . $row['empl_id']];
        }
    }
    $monthly_basic_diff = $monthly_increased_basic - $row['old_monthly_basic'];
    /*
      //If Basic is Same, Calculate "DA" from Basic,
      //If Basic is different AND Grade Pay is ZERO, Calculate "DA" from difference of Basic.
      //If Basic is Different AND Grade Pay is not ZERO, Calculate "DA" from = 1st(Basic + Grade Pay) - New(Basic + Grade Pay)
      if($row['old_monthly_basic'] != $monthly_increased_basic){
      if($row['old_monthly_grade_pay'] == 0){
      $monthly_changed_da = round($monthly_basic_diff * $_POST['change_in_da']/100);
      }
      else{
      $pre_sum1 = $row['old_monthly_basic'] + $row['old_monthly_grade_pay'];
      $new_sum1 = $monthly_increased_basic + $row['old_monthly_grade_pay'];
      $diff_new_sum = $new_sum1 - $pre_sum1;
      $monthly_changed_da = round($diff_new_sum * $_POST['change_in_da']/100);
      }
      }
      else{
      $monthly_changed_da = round(($monthly_increased_basic + $row['old_monthly_grade_pay']) * $_POST['change_in_da']/100);
      }
     * 
     */
    $monthly_changed_da = round(($monthly_increased_basic + $row['old_monthly_grade_pay']) * $_POST['change_in_da'] / 100);

    $total_monthly_changed_da = round($monthly_changed_da * $num_of_arrear_months);
    //$total_diff_da = ($row['pre_da'] < $total_monthly_changed_da)? $total_monthly_changed_da - $row['pre_da'] : $row['pre_da'] - $total_monthly_changed_da;
    if ($is_da_diff == 1) {
        $total_diff_da = round($total_monthly_changed_da - $row['pre_da']);
    } else {
        $total_diff_da = 0;
    }

    $total_salary = $monthly_increased_basic + $row['old_monthly_grade_pay'] + $monthly_changed_da;
    $pf_of_one_month = round($total_salary * $pf / 100);
    $asa_of_one_month = round($total_salary * $asa / 100);
    $total_diff_basic = $monthly_basic_diff * $num_of_arrear_months;

    //$total_diff_da = round($total_monthly_changed_da - $row['pre_da']);


    $_POST['monthly_basic_' . $row['empl_id']] = $row['pre_basic'];

    $_POST['basic_' . $row['empl_id']] = $row['pre_basic'];
    $_POST['gp_' . $row['empl_id']] = $row['old_monthly_grade_pay'] * $num_of_arrear_months; //Calculating Grade Pay        
    $_POST['da_' . $row['empl_id']] = $monthly_changed_da * $num_of_arrear_months; //Calculating DA
    $_POST['pf_' . $row['empl_id']] = $pf_of_one_month * $num_of_arrear_months;
    $_POST['sas_' . $row['empl_id']] = $asa_of_one_month * $num_of_arrear_months;

    $total_diff_pf = $_POST['pf_' . $row['empl_id']] - $row['pre_pf'];
    $total_diff_sas = $_POST['sas_' . $row['empl_id']] - $row['pre_sas'];
    $total_payable = $total_diff_basic + $total_diff_da;
    $net_payment = $total_payable - $total_diff_pf - $total_diff_sas;

    $_POST['tds_' . $row['empl_id']] = $tds_pay;
    $diff_hra = ($total_diff_basic * 20) / 100;
    if (isset($_POST['tds_' . $row['empl_id']])) {
        $net_payment = $net_payment - $_POST['tds_' . $row['empl_id']] + $diff_hra;
    }

    $total_employer_cost = round($total_payable + $total_diff_sas + $total_diff_pf + $diff_hra);

    // display_error($employee_id1);

    start_row();
    //check_cells('', 'sel_employee[]',$row['empl_id']);
    echo '<td style="color:#FF9800; background-color:#f9f2bb;"><input  type="checkbox" name="sel_empl_' . $row['empl_id'] . '" value="' . $row['empl_id'] . '" /></td>';

    $employee_id == $row['empl_id'];
    label_cell("Total", "style='letter-spacing:.4em; color:#FF9800; background-color:#f9f2bb;'");
    //basic
    label_cell($row['pre_basic'], "style=' color:#FF9800; background-color:#f9f2bb;'");
    // text_cells('', 'monthly_basic_'.$row['empl_id']);
    label_cell($_POST['monthly_basic_' . $row['empl_id']], "style=' color:#FF9800; background-color:#f9f2bb;'");
    hidden('monthly_basic_' . $row['empl_id'], $_POST['monthly_basic_' . $row['empl_id']]);
    label_cell($_POST['basic_' . $row['empl_id']], "style='color:#FF9800; background-color:#f9f2bb;'");
    label_cell($basic_diff, "style=' color:#FF9800; background-color:#f9f2bb;'");
    hidden('diff_basic_' . $row['empl_id'], $basic_diff);

    //Grade Pay
    label_cell($row['pre_grade_pay'], "style=' color:#FF9800; background-color:#f9f2bb;'");
    hidden('gp_' . $row['empl_id'], $_POST['gp_' . $row['empl_id']]);

    //DA
    label_cell($row['pre_da'], "style='color:#FF9800; background-color:#f9f2bb;'");
    label_cell($da, "style=' color:#FF9800; background-color:#f9f2bb;'");
    hidden('da_' . $row['empl_id'], $da);
    label_cell($da_dif, "style=' color:#FF9800; background-color:#f9f2bb;'");
    hidden('diff_da_' . $row['empl_id'], $da_dif);


    //Hra
    label_cell(round($prev_hra), "style='color:#FF9800; background-color:#f9f2bb;'");
    label_cell(round($hra), "style='color:#FF9800; background-color:#f9f2bb;'");
    label_cell(round($hra_dif), "style='color:#FF9800; background-color:#f9f2bb;'");
    hidden('diff_hra_' . $row['empl_id'], round($hra_dif));




    //PF
    label_cell($ppf, "style='color:#FF9800; background-color:#f9f2bb;'");
    label_cell($newpf, "style=' color:#FF9800; background-color:#f9f2bb;'");
    label_cell($pf_dif, "style=' color:#FF9800; background-color:#f9f2bb;'");
    hidden('pf_' . $row['empl_id'], $newpf);
    hidden('diff_pf_' . $row['empl_id'], $pf_dif);


    label_cell($prev_sas, "style=' color:#FF9800; background-color:#f9f2bb;'");
    label_cell($sas, "style=' color:#FF9800; background-color:#f9f2bb;'");
    label_cell($p_sas, "style=' color:#FF9800; background-color:#f9f2bb;'");
    hidden('sas_' . $row['empl_id'], $sas);
    hidden('diff_sas_' . $row['empl_id'], $p_sas);
    //text_cells('', 'sas_'.$row['empl_id']);

    hidden('net_payment_' . $row['empl_id'], $net_payment1);
    label_cell($emp_epf_cont, "style=' color:#FF9800; background-color:#f9f2bb;'");
    label_cell($emp_sas_cont, "style=' color:#FF9800; background-color:#f9f2bb;'");


    hidden('tot_employer_cost_' . $row['empl_id'], $tot_emp_cost);

    label_cell($row['pre_tds'], "style='color:#FF9800; background-color:#f9f2bb;'");
    text_cells('', 'tds_' . $row['empl_id']);
    label_cell($net_payment1, "style='color:#FF9800; background-color:#f9f2bb;'");
    label_cell($tot_emp_cost, "style='color:#FF9800; background-color:#f9f2bb;'");
    end_row();
}

end_table();

br();
//Ending table of arrear calculation
submit_center('pay_arrear', _("Pay Arrear"), TRUE, _('Payout to Employees'), 'default');
end_form();
div_end();
end_page();
?>

<script>
    $('#pay_arrear').click(function () {
        $('#pay_arrear > span').text('Saving...');
        $(this).attr('disabled', 'disabled')
    });
</script>