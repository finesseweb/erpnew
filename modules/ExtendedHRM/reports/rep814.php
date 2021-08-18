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
    	$selected_id = $_GET['PARAM_3'];
    	$destination = 1;
        $orientation ='';

   
  //  if(kv_get_employees_count_based_on_dept($dept_id)){
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');

	$cols = array(0,50,150, 300, 450, 830, 1010,1210);
        
	$headers = array(_('#'),_('IP Number'), _('IP Id'),_('IP Number'), _('No of days for which wages paid/payable during month'), _('Total monthly wages'), _('Reason for zero working days'),_('Last working day(dd-mm-yyyy)'));

	$aligns = array('left','center','center','center','center','center','center');
       $dept =  get_department_name($selected_id);
       $dept = !empty($dept)?$dept:'All Department';
       $months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
        $rep = new FrontReport(_('ESI - REPORT : '.date("F", strtotime($months_with_years_list[$month]))), "ESI-".$dept.date('d-m-Y'), user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);
		
    $rep->Font();
    $rep->Info(null, $cols, $headers, $aligns,'');
    $rep->NewPage();
   
    
    if (empty($selected_id))
    $selected_id = 0;
/* if($hrm_year_list[$year]<= date('Y')){}
  else {
  display_error(_('The Selected Year Yet to Born!'));
  $ipt_error = 1;
  } */



$months_with_years_list[get_post('month')];

if ($months_with_years_list[get_post('month')] > date('Y-m-d')) {
    display_error(_('The Selected Month Yet to Born!'));
    $ipt_error = 1;
}
$not_print_arr = array(1, 39, 4, 40, 47, 44, 43, 49, 10, 41, 46, 51, 6, 12, 52, 'leave_encashment');
if ($ipt_error == 0) {
    $get_employees_list = get_empl_ids_from_dept_id($selected_id);
    
        $Total_gross = $total_net = 0;

    $k=$j = 0;
      foreach ($get_employees_list as $single_empl) {
        //echo $single_empl;
        $data_for_empl_group = GetAllEsi('kv_empl_salary', array('empl_id' => $single_empl, 'month' => $month, 'year' => $year));
//echo '<pre>'; print_r($data_for_empl_group);
        foreach ($data_for_empl_group as $data_for_empl) {
            $row = $col = 0;
                $rep->NewLine(1, 2);
            //print_r($data_for_empl);
            if ($data_for_empl) {
                start_row();
                $employee_leave_record = get_empl_attendance_for_month($data_for_empl['empl_id'], $month, $year);

                //=========[get Esi no from empl_info]========//

                $data_column = array('select_column' => 'esi_no',
                    'column_name' => 'empl_id',
                    'match' => $data_for_empl['empl_id']);

                $esi_no = get_empl_info($data_column);

                $total_days = date("t", strtotime($months_with_years_list[get_post('month')]));
                $lop_days = get_empl_attendance_for_month($data_for_empl['empl_id'], $month, $year);
                $rep->TextCol($row++, ++$col, $k+1);
                  $rep->TextCol($row++, ++$col, $esi_no[0] ? $esi_no[0] : 'N/A');
                        $rep->TextCol($row++,++$col,	$data_for_empl['empl_id']);
                         $rep->TextCol($row++,++$col,	kv_get_empl_name($data_for_empl['empl_id']));
                         $rep->TextCol($row++, ++$col,	($total_days - $lop_days) . ' / ' . $total_days);

                $EarAllowance = get_allowances('Earnings');
                while ($single = db_fetch($EarAllowance)) {
                    if (!in_array($single['id'], $not_print_arr))
                             $rep->TextCol($row++,++$col,$data_for_empl[$single['id']]);
                }

                $Allowance = get_allowances('Deductions');
                while ($single = db_fetch($Allowance)) {
                    if (!in_array($single['id'], $not_print_arr))
                             $rep->TextCol($row++,++$col,$data_for_empl[$single['id']]);
                }
                 $rep->TextCol($row++,++$col,$data_for_empl['net_pay']+$data_for_empl['43']+$data_for_empl['49']);

                $Total_gross += $data_for_empl['gross'];
                $total_net += $data_for_empl['net_pay'];
                //label_cell($data_for_empl['other_deduction']);
                $arrear_q_string = '';
                if ($data_for_empl['is_arrear'] == 1) {
                    $arrear_q_string = '&is_arrear=1';
                }
                end_row();
                $k++;
            }
        }
    }
    
}
			
	if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
		$rep->NewPage();	
	$rep->End();
	//}else{ 
	//	display_warning("No Employee Found in it. Please add some employees.");
	//}
?>