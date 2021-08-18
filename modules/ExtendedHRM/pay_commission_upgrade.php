<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_UPGRADE';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
$version_id = get_company_prefs('version_id');

$js = '';
if($version_id['version_id'] == '2.4.1'){
	if ($SysPrefs->use_popup_windows) 
		$js .= get_js_open_window(900, 500);	

	if (user_use_date_picker()) 
		$js .= get_js_date_picker();
	
}else{
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
function getAllowanceById($id){
    global $Allowances;
    foreach($Allowances as $row){
        if($row['id'] == $id){
            return $row;
        }
    }
}
//Getting Current PF
$pf = getAllowanceById(12)['percentage'];
$asa = getAllowanceById(6)['percentage'];
simple_page_mode(true);
page(_("Pay Commission Upgrade"));

$from_month = get_post('from_month','');
$to_month = get_post('to_month','');
$f_year = get_post('f_year','');
$change_in_basic = get_post('change_in_basic','');
$change_in_da = get_post('change_in_da','');
$change_in_hra = get_post('change_in_hra','');
$change_in_sas = get_post('change_in_sas','');
$change_in_pf = get_post('change_in_pf','');
$incl_pre_hra = get_post('incl_pre_hra','');

if($_GET['id']==1){
    display_notification(_("Arrear Processed"));
}

if(list_updated('to_month') || get_post('Refreshloan')) {	
        $month = get_post('to_month');
	$Ajax->activate('totals_tbl');
        //$Ajax->activate('basic_EMP-F-002');
}


$months = kv_get_months_in_fiscal_year();



$month_list = array();
if($from_month != '' && $to_month != ''){
    $f_months1 = array_keys($months);
    $from_month_index = array_search($from_month,$f_months1);
    $to_month_index = array_search($to_month,$f_months1);
    if($to_month_index < $from_month_index){
        display_warning(_("Selected months range is incorrect"));
    }
    else{
        for($i = $from_month_index; $i <= $to_month_index; $i++){
            $month_list[] = (int)$f_months1[$i];
        }
    }
}
$num_of_arrear_months = count($month_list);
if(!isset($_POST['change_in_basic'])){//Fetching Basic
    $_POST['change_in_basic'] = 0;
}
if(!isset($_POST['change_in_da'])){//Fetching DA
    $da_row = getAllowanceById(3);
    $_POST['change_in_da'] = $da_row['percentage'];
}
if(!isset($_POST['change_in_hra'])){//Fetching HRA
    $da_row = getAllowanceById(4);
    $_POST['change_in_hra'] = $da_row['percentage'];
}
if(!isset($_POST['change_in_sas'])){//Fetching SAS
    $da_row = getAllowanceById(6);
    $_POST['change_in_sas'] = $da_row['percentage'];
}
if(!isset($_POST['change_in_pf'])){//Fetching EPF
    $da_row = getAllowanceById(12);
    $_POST['change_in_pf'] = $da_row['percentage'];
}
$employees = get_all_employee_salary($f_year, $month_list);
$arrear_salary = get_all_employee_arrear_salary($f_year, $month_list);
div_start('totals_tbl');
if(get_post('pay_arrear')){
    $months = implode(',', $month_list);
    $current_f_year = get_company_pref('f_year');
    //$sel_empl = get_post('sel_empl','');
    while($row = db_fetch($employees)){
        $empl_id = get_post('sel_empl_'.$row['empl_id'],'');
        $f_year = get_post('f_year');
        $gross = get_post('tot_employer_cost_'.$row['empl_id'],'');
        $net_payment = get_post('net_payment_'.$row['empl_id'],'');
        $deduct_tot = $gross - $net_payment;
        $tds = get_post('tds_'.$row['empl_id'],'');
        $basic = get_post('diff_basic_'.$row['empl_id'],'');
        $da = get_post('diff_da_'.$row['empl_id'],'');
        $grade_pay = get_post('diff_grade_pay_'.$row['empl_id'],'');
        $hra = get_post('diff_hra_'.$row['empl_id'],'');
        $conveyance = get_post('diff_conveyance_'.$row['empl_id'],'');
        $sas = get_post('diff_sas_'.$row['empl_id'],'');
        $pf = get_post('diff_pf_'.$row['empl_id'],'');
        
        
        if(!empty($empl_id)){
             $jobs_arr =  array('empl_id' => $empl_id,
							 'month' => date('n'),
							 'year' => $current_f_year,
							 'gross' => $gross,
							 'deduct_tot' =>$deduct_tot,
							 'loan' => '' ,
							 'date' => array(Today(), 'date'), 
							 'adv_sal' => '',
							 'net_pay' =>  $net_payment, 
							 'misc' =>  '', 
							 'ot_other_allowance' => '',
						 	 'lop_amount' => '', 
							 'conveyance_allowance' => $conveyance,
							 'leave_encashment' => '',
							 'tds' => $tds,
                                                         'accom_hra_by_org' => $row['eligible_hra'],
                                                         'is_arrear' => 1,
                                                         'paid_for_months_list' => $months,
                                                         'paid_for_f_year' => $f_year,
                                                         '1' => $basic,
                                                         '3' => $da, // DA
                                                         '2' => $grade_pay,//Grade Pay
                                                         '4' => $hra,//HRA
                                                         '8' => $conveyance, //Conveyance
                                                         '6' => $sas,
                                                         '12' => $pf,
                 );
            //display_warning(_($empl_id));
            $pay_slip_id = Insert('kv_empl_salary', $jobs_arr);
            $allowance = get_all_hrm_finance_setting();
            $salary_post = array(
                    1 => $basic, //Basic
                    2 => $da, //DA
                    3 => $grade_pay, //Grade Pay
                    4 => $hra, //HRA
                    5 => $conveyance, //Conveyance
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
            while ($single = db_fetch($allowance)) {
                if($single['id'] == 13){
                        $pending_salary_comp = $single;
                    }
                if(($single['type'] == 'Salary') && $single['inactive'] == 0 ){ //Only process the active salary component
                     if($single['allowance_debit_gl_code'] != 0 && isset($salary_post[$single['id']]) && !empty($salary_post[$single['id']]) &&  $salary_post[$single['id']] != 0){
                            add_gl_trans(99, $pay_slip_id, $date, $single['allowance_debit_gl_code'], 0,0, 'Salary Arrear '.$single['description'].' #'.$empl_id.'-'. kv_get_empl_name($empl_id), $salary_post[$single['id']]);
                            $debit_total += $salary_post[$single['id']];
                                        
                        }
                        if($single['allowance_credit_gl_code'] != 0 && isset($salary_post[$single['id']]) && !empty($salary_post[$single['id']]) &&  $salary_post[$single['id']] != 0){//Skip for 'None'
                            add_gl_trans(99, $pay_slip_id, $date, $single['allowance_credit_gl_code'], 0,0, 'Salary Arrear  '.$single['description'].' #'.$empl_id.'-'. kv_get_empl_name($empl_id), -$salary_post[$single['id']]);
                            $credit_total += $salary_post[$single['id']];
                            
                       
                        }
                    
                }
            }
            $pending_salary = $debit_total - $credit_total;
                add_gl_trans(99, $pay_slip_id, $date, $pending_salary_comp['allowance_credit_gl_code'], 0,0, 'Salary Arrear '.$pending_salary_comp['description'].' #'.$empl_id.'-'. kv_get_empl_name($empl_id), -$pending_salary);
            
        }
    }
    
   
    if(!empty($pay_slip_id)){
    meta_forward($path_to_root.'/modules/ExtendedHRM/pay_commission_upgrade.php',"id=1");
    }
        else
    display_warning(_("select any employee !"));    
}
start_form();
start_table(TABLESTYLE_NOBORDER);
start_row();
kv_fiscalyears_list_cells(_("Fiscal Year:"), 'f_year', null, true);

kv_current_fiscal_months_list_cell("From:", "from_month", null, true);
kv_current_fiscal_months_list_cell("To:", "to_month", null, true);
text_cells('PEV HRA(%)', 'change_in_prev_hra',NULL,3,3,false,'','',true);
end_row();
start_row();
text_cells('Change in Basic(%)', 'change_in_basic',NULL,3,3,false,'','',true);
text_cells('DA(%)', 'change_in_da',NULL,3,3,false,'','',true);
text_cells('HRA(%)', 'change_in_hra',NULL,3,3,false,'','',true);
end_row();
start_row();
text_cells('SAS(%)', 'change_in_sas',NULL,3,3,false,'','',true);
text_cells('EPF(%)', 'change_in_pf',NULL,3,3,false,'','',true);
$selected_yes = ($incl_pre_hra == 1) ? 'selected="selected"' : '';
$selected_no = ($incl_pre_hra == 0) ? 'selected="selected"' : '';
echo '<td>Prev. HRA : </td><td><select style="width:100px;" name="incl_pre_hra"><option value="1" '.$selected_yes.'>Yes</option><option value="0" '.$selected_no.'>No</option></select></td>';
//text_cells($label, $name, $value, $size, $max, $title, $labparams, $post_label, $submit_on_change, $readonly)
submit_cells('btn_go', _("Go"), '','',TRUE);
submit_cells('Refreshloan', _("Refresh"),'',_('Show Results'), true);
end_row();
end_table();

//Starting table of arrear calculation
start_table(TABLESTYLE, "width=90%");
$th1 = array(_(" "),_("Emp. Id"),_("Name"), _("Basic"), _("Grade Pay"), _("DA"), _("HRA"), _("Conveyance"), _("Employer EPF"), _("Employer SAS"),_("Total"), _("Basic per month"), _("Basic"), _("DA"), _("HRA"), _("Conveyance"), _("Employer EPF"), _("Employer SAS"), _("Total"), _("Arrear in EPF"), _("Arrear in SAS"),_("TDS"), _("Net Payable"), _("Gross Salary"));

table_header($th1);
while ($row = db_fetch($employees)) { 
     //Calculating/Dividing Arrear Salary for the selected month
        $pre_basic = $pre_arrear_da = $pre_arrear_pf = $pre_arrear_sas = 0;
        $month_list1=array();
        
        foreach($arrear_salary as $sal_row){ 
           
            if($sal_row['empl_id'] == $row['empl_id']){
                //Count the number of month paid for arrears
               // echo "<pre>";print_r($sal_row['basic'].'======'.$row['pre_basic']);
                $arrear_da = $sal_row['da'];
                $arrear_pf = $sal_row['pf'];
                $arrear_sas = $sal_row['sas'];
                $month_list1 = explode(',', $sal_row['paid_for_months_list']);
                $num_months1 = count($month_list1);
                $per_month_da = $arrear_da/$num_months1;
                $per_month_pf = $arrear_pf/$num_months1;
                $per_month_sas = $arrear_sas/$num_months1;
                $count = 0;
                
                foreach($month_list1 as $month){            
                    if(in_array($month, $month_list)){
                        $count++;
                         
                    }            
                }
                /*
                 * Comment this line by Ritika 14-08-18
                 */
             //  $pre_basic = $sal_row['basic']/$count;
                $pre_arrear_da += $per_month_da * $count;
                /*
                 * End 14-08-18
                 */
                $pre_arrear_pf += $per_month_pf * $count;
                $pre_arrear_sas += $per_month_sas * $count;
                
            }
 
        }
        
        
        $row['pre_da'] += $pre_arrear_da;
        $row['pre_pf'] += $pre_arrear_pf;
        $row['pre_sas'] += $pre_arrear_sas;
        
        if($change_in_basic < 0){
           
            $monthly_increased_basic = $row['old_monthly_basic'] - round(($row['old_monthly_basic'] * $change_in_basic / 100)); 
              $row['pre_basic'] =$monthly_increased_basic *count($month_list);
        }
        else{
            
            $monthly_increased_basic = $row['old_monthly_basic'] + round(($row['old_monthly_basic'] * $change_in_basic / 100));
              $row['pre_basic'] =$monthly_increased_basic *count($month_list); 
        }
         
        //If user has entered the basic salary manually
        /*
		if($change_in_basic == 0 && get_post('Refreshloan')){//Only 
			$default_increased_basic = $monthly_increased_basic * $num_of_arrear_months;
			if(isset($_POST['basic_'.$row['empl_id']]) && ($_POST['basic_'.$row['empl_id']] != 0) && ($default_increased_basic != $_POST['basic_'.$row['empl_id']]) ){
				$monthly_increased_basic = round($_POST['basic_'.$row['empl_id']]/$num_of_arrear_months);            
			}
		}
         * 
         */
        /*
        $monthly_basic_diff = $monthly_increased_basic - $row['old_monthly_basic'];
        
        $monthly_changed_da = round(($monthly_increased_basic + $row['gp_monthly']) * $_POST['change_in_da']/100);
        $total_monthly_changed_da = round($monthly_changed_da * $num_of_arrear_months);
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
        $total_diff_basic = $monthly_basic_diff * $num_of_arrear_months;        
        //$total_diff_da = round($total_monthly_changed_da - $row['pre_da']);
        
        
        $_POST['basic_'.$row['empl_id']] = $monthly_increased_basic * $num_of_arrear_months;
        $_POST['gp_'.$row['empl_id']] = $row['old_monthly_grade_pay'] * $num_of_arrear_months;//Calculating Grade Pay        
        $_POST['da_'.$row['empl_id']] = $monthly_changed_da * $num_of_arrear_months;//Calculating DA
        $_POST['pf_'.$row['empl_id']] = $pf_of_one_month * $num_of_arrear_months;
        $_POST['sas_'.$row['empl_id']] = $asa_of_one_month * $num_of_arrear_months;
        
        
        
        
        $total_diff_pf = $_POST['pf_'.$row['empl_id']] - $row['pre_pf'];
        $total_diff_sas = $_POST['sas_'.$row['empl_id']] - $row['pre_sas'];
        $total_payable = $total_diff_basic + $total_diff_da;
        $net_payment = $total_payable - $total_diff_pf - $total_diff_sas;
        if(isset($_POST['tds_'.$row['empl_id']])){
            $net_payment = $net_payment - $_POST['tds_'.$row['empl_id']];
            
        }
        
        $total_employer_cost = round($total_payable + $total_diff_sas + $total_diff_pf);
         * 
         */
      
       
        
        $pre_total_sum = $row['pre_basic'] + $row['pre_grade_pay'] + $row['pre_da'] + $row['pre_hra'] + $row['pre_conveyance'] + $row['pre_pf'] + $row['pre_sas'];
       // display_error($pre_total_sum);
        if(isset($_POST['monthly_basic_'.$row['empl_id']]) && get_post('Refreshloan')){
            $monthly_basic = $_POST['monthly_basic_'.$row['empl_id']];
        }
        else{
            $monthly_basic = $row['basic_monthly'];
            
        }
       // $monthly_basic = $row['pre_basic'];
         
        /**
         * Calculating One month Salary's components
         */
        $monthly_da = round($monthly_basic * $change_in_da /100); 
        if($incl_pre_hra == 1){
            if($row['pre_hra'] == 0){
                $monthly_hra = 0;
               
            }
            else{
               // $monthly_hra = $row['old_monthly_hra'];
                $monthly_hra = (($row['pre_basic']+$row['pre_grade_pay'])*20)/100;
              
            }    
            
        }
        else{
            $monthly_hra = ($monthly_basic * $change_in_hra/100); 
        }
         
        $monthly_conveyance = $row['old_monthly_conveyance'];
        $monthly_sas = round(($monthly_basic + $monthly_da) * $change_in_sas/100);
        $monthly_epf = round(($monthly_basic + $monthly_da) * $change_in_pf/100);
        
        $total_basic = round($monthly_basic * $num_of_arrear_months);
         
        $total_da = round($monthly_da * $num_of_arrear_months);
        $total_hra = round($monthly_hra * $num_of_arrear_months);
        $total_conveyance = round($monthly_conveyance * $num_of_arrear_months);
        $total_sas = round($monthly_sas * $num_of_arrear_months);
        $total_epf = round($monthly_epf * $num_of_arrear_months);
        $total_grade_pay = 0;
        
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
        if(isset($_POST['tds_'.$row['empl_id']])){
            $net_payment = $net_payment - $_POST['tds_'.$row['empl_id']];
        }
        
        
        $total_gross_sum = round($total_basic + $total_da + $total_hra + $total_conveyance + $total_sas + $total_epf);
        $_POST['monthly_basic_'.$row['empl_id']] = $monthly_basic;
        /*
        $arrear_gross = $total_gross_sum - $pre_total_sum;
        $arrear_epf = ($total_epf - $row['pre_pf']) * 2;
        $arrear_sas = ($total_sas - $row['pre_sas']) * 2;
        $arrear_net_payable = $arrear_gross - $arrear_epf - $arrear_sas;
         * 
         */
    
    start_row();
    /*
     *Add round function for round value Ritika 14-08-18
     */
    //check_cells('', 'sel_employee[]',$row['empl_id']);
    if($_POST['change_in_prev_hra']){
    $total_hr = (($row['pre_basic']+$row['pre_grade_pay'])*(int)$_POST['change_in_prev_hra'])/100;
      $pre_total_sum = $row['pre_basic'] + $row['pre_grade_pay'] + $row['pre_da'] + $total_hr  + $row['pre_conveyance'] + $row['pre_pf'] + $row['pre_sas'];
    }
 else {
    $total_hr = $row['pre_hra'] ;  
    $pre_total_sum = $row['pre_basic'] + $row['pre_grade_pay'] + $row['pre_da'] + $total_hr  + $row['pre_conveyance'] + $row['pre_pf'] + $row['pre_sas'];
    }
    
    echo '<td><input type="checkbox" name="sel_empl_'.$row['empl_id'].'" value="'.$row['empl_id'].'" /></td>';
    label_cell($row['empl_id']);
    label_cell($row['emp_name']);
    //basic
    
    label_cell($row['pre_basic']); 
    hidden('diff_basic_'.$row['empl_id'], $_POST['gp_'.$row['empl_id']]);
    
    label_cell($row['pre_grade_pay']);
    label_cell(round($row['pre_da']));
    label_cell(round($total_hr));
    label_cell($row['pre_conveyance']);
    label_cell(round($row['pre_pf']));
    label_cell(round($row['pre_sas']));
    label_cell(round($pre_total_sum));
    
    //Grade Pay
    
    //hidden('gp_'.$row['empl_id'], $_POST['gp_'.$row['empl_id']]);
    text_cells('', 'monthly_basic_'.$row['empl_id']);
    label_cell($total_basic);
    //DA
    
    label_cell(round($total_da));
    label_cell($total_hra);
    label_cell($total_conveyance);    
    label_cell(round($total_epf));
    label_cell(round($total_sas));
    label_cell(round($total_gross_sum));
    //hidden('diff_da_'.$row['empl_id'], $total_diff_da);
    
    //PF
 
        
        
    hidden('diff_basic_'.$row['empl_id'], $diff_basic);
    hidden('diff_da_'.$row['empl_id'], $diff_da);
    hidden('diff_grade_pay_'.$row['empl_id'], $diff_grade_pay);
    hidden('diff_hra_'.$row['empl_id'], $diff_hra);
    hidden('diff_conveyance_'.$row['empl_id'], $diff_conveyance);
    
    
    
    hidden('tot_employer_cost_'.$row['empl_id'], $diff_gross);
    
    
    label_cell(round($diff_total_pf));
    hidden('diff_pf_'.$row['empl_id'], $diff_pf);
    
    label_cell(round($diff_total_sas));
    hidden('diff_sas_'.$row['empl_id'], $diff_sas);
    
    text_cells('', 'tds_'.$row['empl_id']);
    
    label_cell(round($net_payment),'style="background:#f9f9f9;"');  
    hidden('net_payment_'.$row['empl_id'], $net_payment);
    label_cell(round($total_gross_sum - $pre_total_sum));
    //label_cell(round($diff_gross));
    /*
     * end 14-08-18
     */
    end_row();   
   
}

end_table();
br();
submit_center('pay_arrear', _("Pay Arrear"),true, _('Payout to Employees'), 'default');

end_form();
div_end();
end_page();
?>
<script>
        $('#pay_arrear').click(function(){
            $('#pay_arrear > span').text('Saving...');
            $(this).attr('disabled','disabled')
        });
    
    </script>