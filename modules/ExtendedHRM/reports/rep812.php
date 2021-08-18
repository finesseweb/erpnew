<?php 
$page_security = 'SA_OPEN';

$path_to_root="../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");

global $path_to_root, $systypes_array, $kv_empl_gender;

    	$year = $_GET['PARAM_0'];
    	$month = $_GET['PARAM_1'];
    	$selected_id = $_GET['PARAM_2'];
    	$destination = 1;
        $orientation ='';

   
  //  if(kv_get_employees_count_based_on_dept($dept_id)){
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');

	$cols = array(0,50,150, 300, 450, 530, 610, 710, 880, 940, 1020 ,1150 ,1285,1300);
        
	$headers = array(('#'),_(''),_('Employee Id'), _('Member Name'),_('UAN'), _('Gross Wage'), _('EPF Wage'), _('EPS Wage'), 
		_('EDLI Wage'), _('EPF Contribution Remitted'),_('EPS Contribution Remitted'),_('EDLI Contribution'),_('NCP Days'),_("Refund of Advance"));

	$aligns = array('center','center','center','center','center','center','center','center','center','center','center','center','center');
       $dept =  get_department_name($selected_id);
       $dept = !empty($dept)?$dept:'All Department';
       $months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
        $rep = new FrontReport(_('EPF - REPORT: '.date("F", strtotime($months_with_years_list[$month]))), "EPF-".$dept.date('d-m-Y'), user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);
		
    $rep->Font();
    $rep->Info(null, $cols, $headers, $aligns);
    $rep->NewPage();
   
    
    if (empty($selected_id))
    $selected_id = 0;
/* if($hrm_year_list[$year]<= date('Y')){}
  else {
  display_error(_('The Selected Year Yet to Born!'));
  $ipt_error = 1;
  } */

$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);

$months_with_years_list[get_post('month')];

if ($months_with_years_list[get_post('month')] > date('Y-m-d')) {
    display_error(_('The Selected Month Yet to Born!'));
    $ipt_error = 1;
}
$not_print_arr = array(39, 4, 40, 47, 44, 43, 49, 10, 41, 46, 51, 6, 12, 'leave_encashment');
if ($ipt_error == 0) {
    $get_employees_list = get_empl_ids_from_dept_id($selected_id);
    
        $Total_gross = $total_net = 0;

    $k=$j = 0;
    foreach ($get_employees_list as $key => $single_empl) {
        
        //echo $single_empl;
        $data_for_empl_group = GetAll('kv_empl_salary', array('empl_id' => $single_empl, 'month' => $month, 'year' => $year));
//echo '<pre>'; print_r($data_for_empl_group);

        foreach ($data_for_empl_group as $data_for_empl) {
            //print_r($data_for_empl);
            
            for ($i = 0; $i < 2; $i++) {
                $row = $col = 0;
                $rep->NewLine(2, 3);
                
                if ($i == 1)
                    $not_print_arr = array(1, 39, 4, 40, 47, 44, 43, 49, 10, 41, 46, 51, 6, 12, 'leave_encashment');
                if ($data_for_empl) {
                    start_row();
                    $employee_leave_record = get_empl_attendance_for_month($data_for_empl['empl_id'], $month, $year);
                    $data_column = array('select_column' => 'pf_number',
                        'column_name' => 'empl_id',
                        'match' => $data_for_empl['empl_id']);
		
                    $pf_number = get_empl_info($data_column);
                    if ($i != 1) {
                        $rep->TextCol($row++, ++$col, $k+1);
                        $rep->TextCol($row++, ++$col, 'Employer');
                        $rep->TextCol($row++,++$col,	$data_for_empl['empl_id']);
                         $rep->TextCol($row++,++$col,	kv_get_empl_name($data_for_empl['empl_id']));
                         $rep->TextCol($row++, ++$col,	$pf_number[0] ? $pf_number[0] : 'N/A');
                       
                    } else {
                        $rep->TextCol($row++, ++$col, '');
                        $rep->TextCol($row++, ++$col, 'Employee');
                        $rep->TextCol($row++, ++$col, '--');
                        $rep->TextCol($row++, ++$col, '--');
                        $rep->TextCol($row++, ++$col, '--');
                    }




                    $EarAllowance = get_allowances('Earnings');
                    $epf_wage = $data_for_empl[1] > 15000 ? 15000 : $data_for_empl[1];
                    while ($single = db_fetch($EarAllowance)) {
                        if ($i != 1) {
                            if (!in_array($single['id'], $not_print_arr)) {
                                if ($single['id'] == 52){
                                    $rep->TextCol($row++, ++$col,$data_for_empl[1]);
                                    $rep->TextCol($row++, ++$col, $epf_wage);
                                }
                                elseif ($single['id'] == 1){
                                    $rep->TextCol($row++, ++$col,$data_for_empl[1]);
                                }
                            }
                        }
                        elseif ($i == 1 && $single['id'] == 1) {
                             $rep->TextCol($row++, ++$col, '--');
                            $j = 1;
                        }
                    }


                    //label_cell($epf_wage);
                    $rep->TextCol($row++, ++$col, $epf_wage);

                    //label_cell($data_for_empl['6']);
                    //label_cell($data_for_empl['leave_encashment']);
                    //label_cell($data_for_empl['gross']);
                    //$total_deduct = $data_for_empl['misc']+$data_for_empl['loan']+$data_for_empl['lop_amount']+$data_for_empl['adv_sal']+$data_for_empl['tds']; 
                    $Allowance = get_allowances('Deductions');
                    while ($single = db_fetch($Allowance)) {
                        if (!in_array($single['id'], $not_print_arr))
                            $rep->TextCol($row++, ++$col,$data_for_empl[1]);
                        //$total_deduct += $data_for_empl[$single['id']];
                    }
                    //label_cell($data_for_empl['adv_sal']);
                    //label_cell($data_for_empl['tds']);
                    //label_cell($data_for_empl['loan']);
                    //label_cell($employee_leave_record);
                    //label_cell($data_for_empl['lop_amount']);
                    //label_cell($data_for_empl['misc']);					
                    //label_cell($data_for_empl['deduct_tot']);
                    //label_cell($data_for_empl['net_pay']);
                    if ($i != 1) {
                        if($j != 0)
                        $rep->TextCol($row++, ++$col, $epf_wage);
                        $epf_contri = round(($epf_wage * 3.67) / 100);
                        $rep->TextCol($row++, ++$col, $epf_contri);
                        $eps_contri = round(($epf_wage * 8.33) / 100);
                        $rep->TextCol($row++, ++$col, $eps_contri);
                        $edli_contri = round(($epf_wage * 0.5) / 100);
                        $rep->TextCol($row++, ++$col, $edli_contri);
                    } elseif ($i == 1) {
                         $rep->TextCol($row++, ++$col, '--');
                        $rep->TextCol($row++, ++$col, '--');
                        $epf_contri = round(($data_for_empl[52]));
                        $rep->TextCol($row++, ++$col, $epf_contri);
                         $rep->TextCol($row++, ++$col, '--');
                         $rep->TextCol($row++, ++$col, '--');
                    }
                    $Total_gross += $data_for_empl['gross'];
                    $total_net += $data_for_empl['net_pay'];
                    //label_cell($data_for_empl['other_deduction']);
                    $arrear_q_string = '';
                    if ($data_for_empl['is_arrear'] == 1) {
                        $arrear_q_string = '&is_arrear=1';
                    }
                    //label_cell('<a href="'.$path_to_root.'/modules/ExtendedHRM/payslip.php?employee_id='.$data_for_empl['empl_id'].'&month='.$month.'&                                                      year='.$year.'&slip_id='.$data_for_empl['id'].$arrear_q_string.'" onclick="javascript:openWindow(this.href,this.target); return false;"  target="_blank" > <img src="'.$path_to_root.'/themes/default/images/gl.png" width="12" height="12" border="0" title="GL"></a>');


                    end_row();
                    if($i!=1)
                    $k++;
                }
                
            }
            
        }
        
    }
}
    
   // $result = kv_get_employees_list_based_on_dept_rep($dept_id);	

	/*while ($myrow = db_fetch($result))	{		
			$rep->NewLine(1, 2);
			$rep->TextCol(0, 1, $myrow['empl_id']);
			$rep->TextCol(1, 2,	$myrow['empl_firstname'].' '.$myrow['empl_lastname']);
			$rep->TextCol(2, 3,	$kv_empl_gender[$myrow['gender']]);
			$rep->TextCol(3, 4, get_department_name($myrow['department']));
			$rep->TextCol(4, 5,	$myrow['email']);
			$rep->TextCol(5, 6,	$myrow['mobile_phone']);
			$rep->TextCol(6, 7,	$myrow['desig']);
			$rep->TextCol(7, 8,	$myrow['addr_line1']);
			$rep->NewLine();    	
	}*/
			
	if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
		$rep->NewPage();	
	$rep->End();
	//}else{ 
	//	display_warning("No Employee Found in it. Please add some employees.");
	//}
?>