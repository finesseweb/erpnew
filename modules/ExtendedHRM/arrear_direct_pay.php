<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_PAYSLIP';
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
    #totals_tbl input{
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
if($_GET['id']==1){
    display_notification(_("Arrear Processed"));
}
//Getting Current PF
$pf = getAllowanceById(12)['percentage'];
$asa = getAllowanceById(6)['percentage'];
simple_page_mode(true);
page(_("Direct Arrear Pay"));

$from_month = get_post('from_month','');
$to_month = get_post('to_month','');
$f_year = get_post('f_year','');
$change_in_basic = get_post('change_in_basic','');
$change_in_da = get_post('change_in_da','');
$change_in_hra = get_post('change_in_hra','');
$change_in_sas = get_post('change_in_sas','');
$change_in_pf = get_post('change_in_pf','');
$incl_pre_hra = get_post('incl_pre_hra','');

if(list_updated('to_month') || get_post('btn_go') || get_post('Refreshloan')) {	
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
div_start('totals_tbl');
if(get_post('pay_arrear')) {
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
        $misc = get_post('misc_'.$row['empl_id'],'');
        
        
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
							 'misc' =>  $misc, 
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
                    12 => $tds, //TDS
                    14 => $misc //Other Deduction
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
    
   
    if(!empty($pay_slip_id))
    meta_forward($path_to_root.'/modules/ExtendedHRM/arrear_direct_pay.php',"id=1");
        else
        display_warning(_("select any employee !"));  
}
start_form();
start_table(TABLESTYLE_NOBORDER);
start_row();
kv_fiscalyears_list_cells(_("Fiscal Year:"), 'f_year', null, true);

kv_current_fiscal_months_list_cell("From:", "from_month", null, TRUE);
kv_current_fiscal_months_list_cell("To:", "to_month", null, TRUE);
submit_cells('btn_go', _("Go"), '','',TRUE);
end_row();
/*
start_row();
text_cells('Change in Basic(%)', 'change_in_basic',NULL,3);
text_cells('DA(%)', 'change_in_da',NULL,3);
text_cells('HRA(%)', 'change_in_hra',NULL,3);
end_row();
start_row();
text_cells('SAS(%)', 'change_in_sas',NULL,3);
text_cells('EPF(%)', 'change_in_pf',NULL,3);
$selected_yes = ($incl_pre_hra == 1) ? 'selected="selected"' : '';
$selected_no = ($incl_pre_hra == 0) ? 'selected="selected"' : '';
echo '<td>Prev. HRA : </td><td><select style="width:100px;" name="incl_pre_hra"><option value="1" '.$selected_yes.'>Yes</option><option value="0" '.$selected_no.'>No</option></select></td>';
//text_cells($label, $name, $value, $size, $max, $title, $labparams, $post_label, $submit_on_change, $readonly)
submit_cells('btn_go', _("Go"), '','',TRUE);
submit_cells('Refreshloan', _("Refresh"),'',_('Show Results'), true);
end_row();
 * 
 */
end_table();


//Starting table of arrear calculation
start_table(TABLESTYLE, "width=90%");
$th1 = array(_(" "),_("Emp. Id"),_("Name"), _("Basic"), _("Grade Pay"), _("DA"), _("HRA"), _("Conveyance"), _("EPF"), _("SAS"), _("Other Ded."), _("TDS"), _("Employer EPF"), _("Employer SAS"), _("Net Payable"), _("Total cost to Employer"));

table_header($th1, 'style="width:75px;"');
while ($row = db_fetch($employees)) { 
     //print_r($row);
       
        
    
    start_row();
    //check_cells('', 'sel_employee[]',$row['empl_id']);
    echo '<td style="text-align:center;"><input type="checkbox" name="sel_empl_'.$row['empl_id'].'" value="'.$row['empl_id'].'" /></td>';
    label_cell($row['empl_id']);
    label_cell($row['emp_name']);
    //basic
    
    
    //Grade Pay
    
    //hidden('gp_'.$row['empl_id'], $_POST['gp_'.$row['empl_id']]);
    text_cells('', 'diff_basic_'.$row['empl_id'],null,"","","Basic Pay");
    text_cells('','diff_grade_pay_'.$row['empl_id'],null,"","","Grade Pay");
    text_cells('','diff_da_'.$row['empl_id'],null,"","","DA");
    text_cells('','diff_hra_'.$row['empl_id'],null,"","","HRA");
    text_cells('','diff_conveyance_'.$row['empl_id'],null,"","","Coveyance");
    text_cells('','diff_pf_'.$row['empl_id'],null,"","","EPF");
    text_cells('','diff_sas_'.$row['empl_id'],null,"","","SAS"); 
    text_cells('', 'misc_'.$row['empl_id'],null,"","","Other Deduction");    
    text_cells('', 'tds_'.$row['empl_id'],null,"","","TDS");      
    text_cells('','diff_pf1_'.$row['empl_id'],null,"","","Employer EPF");
    text_cells('','diff_sas1_'.$row['empl_id'],null,"","","Employer SAS");  
    text_cells('','net_payment_'.$row['empl_id'],null,"","","Net Payment"); 
    text_cells('','tot_employer_cost_'.$row['empl_id'],null,"","","Total Employer Cost");
    
    
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