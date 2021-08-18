<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
/*  Payslip PDF
*****************************************/
$page_security = 'SA_OPEN';

$path_to_root="../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

global $path_to_root, $systypes_array, $kv_empl_gender;

	if(isset($_GET['rep_v'])){
		include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
	}
    $year = (isset($_POST['PARAM_0']) ? $_POST['PARAM_0'] : (isset($_GET['PARAM_0']) ? $_GET['PARAM_0'] : 1));
    $month = (isset($_POST['PARAM_1']) ? $_POST['PARAM_1'] : (isset($_GET['PARAM_1']) ? $_GET['PARAM_1'] : 01));
    $comment = (isset($_POST['PARAM_2']) ? $_POST['PARAM_2'] : (isset($_GET['PARAM_2']) ? $_GET['PARAM_2'] : 0));
	//$comment = (isset($_POST['PARAM_3']) ? $_POST['PARAM_3'] : (isset($_GET['PARAM_3']) ? $_GET['PARAM_3'] : ''));;
    //$destination = (isset($_POST['PARAM_4']) ? $_POST['PARAM_4'] : (isset($_GET['PARAM_4']) ? $_GET['PARAM_4'] : ''));
    $_POST['REP_ID'] = 806; 

//if(db_has_employee_payslip($empl_id, $month, $year)){
//	if ($destination)
	//	include_once($path_to_root . "/reporting/includes/excel_report.inc");
	//else
		include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

	$orientation = 'P';

	$cols = array(10,215,300,490);	
	
	$headers = array(_("Earnings"), _("Amount"), _("Deductions"), _("Amount"));

	$aligns = array('left',	'left', 'left', 'left');

    $rep = new FrontReport(_('Payroll Summary'), "Payroll Summary", user_pagesize(), 9, $orientation);
	
    $result = kv_get_sal_details_file($month, $year);	
	
	if ($myrow = db_fetch($result))	{
		//$name_and_dept = get_empl_name_dept($myrow['empl_id']);
		$employee_info = array( 'id' => '', 'empl_id' => $month, 'year' => $year);

			$baccount = array();
			
			$rep->SetHeaderType('Header2');	
		    $rep->Font();
		    $rep->Info(null, $cols, $headers, $aligns);
		    
		   //display_error(json_encode($myrow)."ygukgygug".json_encode($employee_info));

		    $contacts = array( );

		    $rep->SetCommonData($employee_info, $baccount, array( $contacts),  'paysummary');
		    $rep->NewPage();
			$rep->NewLine();		

			$text_value=40;
			$line_value=670;
			$earallows = array();
			$EarAllowance = get_allowances('Earnings');
			while($EarAllow = db_fetch($EarAllowance)){
				$earallows[] = $EarAllow;
			}

			$DedAllowance = get_allowances('Deductions');
			$dedallows=  array();
			while($DedAllow = db_fetch($DedAllowance)){
				$dedallows[] = $DedAllow;
			}
			$earnings_count = get_all_allowances_count('Earnings');
			$deductions_count = get_all_allowances_count('Deductions');

			//display_error(json_encode($dedallows));
			if($earnings_count > $deductions_count){
				$count_final  = $earnings_count;
			}else{
				$count_final = $deductions_count;
			}
			//display_error($count_final);
			$Value = -70;
			$total_deduction = 0;
			$count_difference = $count_final- $deductions_count;
			if($count_difference >= 2)
				$else_deduct = 0;
			else
				$else_deduct = 3;
			for($vj=0; $vj<$count_final;$vj++){
				if(isset($earallows[$vj]) && $myrow[$earallows[$vj]['id'].'_sum']>0){
					$rep->Text($text_value, $earallows[$vj]['description'],0,0,$Value);
					$rep->Text(250, $myrow[$earallows[$vj]['id'].'_sum'],0,0,$Value);					
				}
				if(isset($dedallows[$vj]) && $myrow[$dedallows[$vj]['id'].'_sum'] > 0){
					$rep->Text(330, $dedallows[$vj]['description'],0,0,$Value);
					$rep->Text(530, $myrow[$dedallows[$vj]['id'].'_sum'],0,0,$Value);
					$total_deduction += $myrow[$dedallows[$vj]['id'].'_sum'];
				}elseif($else_deduct==0){
					$rep->Text(330, 'Loan Amount ',0,0,$Value);
					$rep->Text(530, $myrow['loan_sum'],0,0,$Value);
					$total_deduction += $myrow['loan_sum'];
					$else_deduct++;
				}elseif($else_deduct==1){
					$rep->Text(330, 'LOP Amount',0,0,$Value);
					$rep->Text(530, $myrow['lop_amount_sum'],0,0,$Value);
					$total_deduction += $myrow['lop_amount_sum'];
					$else_deduct++;
				}
				
				//$rep->Line($line_value, 0.00001,0,0);
				$rep->NewLine(2);
				$Value++;
			}			
			
			$rep->Text($text_value, 'OT & Other Allowance',0,0,$Value);
			$rep->Text(250, $myrow['ot_other_allowance_sum'],0,0,$Value);
			$rep->Text(330, 'Misc',0,0,$Value);
			$rep->Text(530, $myrow['misc_sum'],0,0,$Value);
			$total_deduction += $myrow['misc_sum'];
		//	$rep->Line($line_value-125, 0.00001,0,0);
			$rep->NewLine(2);
			$Value++;
			if($else_deduct == 3){
				$rep->Text($text_value, ' ',0,0,-$Value);
				$rep->Text(250,  ' ',0,0,$Value);
				$rep->Text(330, 'Loan Amount ',0,0,$Value);
				$rep->Text(530, $myrow['loan_sum'],0,0,$Value);
				$total_deduction += $myrow['loan_sum'];
			//	$rep->Line($line_value-125, 0.00001,0,0);
				$rep->NewLine(2);
				$Value++;

				$rep->Text($text_value, '',0,0,$Value);
				$rep->Text(250, '',0,0,-65);
				$rep->Text(330, 'LOP Amount',0,0,$Value);
				$rep->Text(530, $myrow['lop_amount_sum'],0,0,$Value);
				$total_deduction += $myrow['lop_amount_sum'];
			//	$rep->Line($line_value-125, 0.00001,0,0);
				$rep->NewLine(2);
				$Value++;
			}

			$rep->row = 120;
			$rep->Line(205, 0.00001,0,0);	
			/* Gross pay*/
			$rep->SetTextColor(255, 152, 0);
			$rep->Text($text_value, 'Gross Pay(Total Earnings)',0,0,$Value);
			$rep->Text(250, $myrow['gross_sum'],0,0,$Value);
			$rep->SetTextColor(203, 0, 0);
			$rep->Text(330, 'Total Deduction',0,0,$Value);
			$rep->Text(530, $total_deduction,0,0,$Value);
		//	$rep->Line($line_value-150, 0.00001,0,0);
			$rep->NewLine(1);
			$rep->SetTextColor(0, 0, 0);		
			
			/* $rep->Text($text_value, 'Advance Salary',0,0,1);
			$rep->Text(400, $myrow['adv_sal'],0,0,1);
			$rep->Line($line_value-225, 0.00001,0,0);
			$rep->NewLine(2);
			*/				
			$rep->Line(165, 0.00001,0,0);	
			$rep->SetTextColor(16, 123, 15);
			$rep->Text($text_value, 'Net Amount ( Total Earnings - Total Deduction)',0,0,-40);
			$rep->Text(530, $myrow['net_pay_sum'],0,0,-40);			
			$rep->NewLine(1);
			$rep->Line(135, 0.00001,0,0);
			$rep->Line($line_value-585, 0.00001,0,0);	
			$rep->row = 180;	
			if($comment){				
				$rep->SetTextColor(0, 0, 0);
				$rep->Text($text_value, 'Comments',0,0,65);
				$rep->Text(200, $comment,0,0,65);  //$rep->NewLine(2);	
			}		
			$rep->Line($line_value-635, 0.00001,0,0);		
	}
			
	if ($rep->row < $rep->bottomMargin )
		$rep->NewPage();	
	$rep->End(); //1, 'Payslip ');
//}else{
//	display_warning("No Payroll Entry Found For Selected Period.");
//}
?>