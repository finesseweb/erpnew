<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'SA_OPEN';

$path_to_root="../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

global $path_to_root, $systypes_array, $kv_empl_gender;

    $year = $_POST['PARAM_0'];
    $month = $_POST['PARAM_1'];
    $dept_id = $_POST['PARAM_2'];
    $comment = $_POST['PARAM_3'];
    $destination = $_POST['PARAM_4'];

   // if(db_has_sal_for_selected_dept($dept_id,$month, $year)){
    	if ($destination)
			include_once($path_to_root . "/reporting/includes/excel_report.inc");
		else
			include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

		$orientation = 'L';


		$th = array(_("Id"), _("Name") );

	    $Allowance = get_allowances('Earnings');
		while ($single = db_fetch($Allowance)) {	
			$th[] = substr($single['description'], 0, 5);
		}
		$th[] = _("Ot.Allo.");
		
		$Allowance = get_allowances('Deductions');
		while ($single = db_fetch($Allowance)) {	
			$th[] = substr($single['description'], 0, 7);
		}

	   	$th1 = array(_("Loan"),_("LOP Days"),_("LOP Amt"),_("Misc."),_("Gross"),_("Total Ded."),_("Net Sal"));
	   	$headers = array_merge($th, $th1);
	   	$count_header = count($headers);

		//$cols = array(0, 35, 90, 130, 150, 170, 190, 220, 250, 280, 310, 340, 370, 400, 430, 460, 490, 530, 580);
			
		$aligns = $cols = array();
		$Col_count = 570/$count_header;
		for($vj=0; $vj<=$count_header; $vj++){
			$aligns[] ='left';
			$cols[] = $vj*$Col_count;
		}

	    $rep = new FrontReport(_('Month of Payroll: '.kv_month_name_by_id($month).' - Dept: '.get_department_name($dept_id)), "Payroll History", user_pagesize(), 9, $orientation);
	    
	    if ($orientation == 'L')
	    	recalculate_cols($cols);
			
	    $rep->Font();
	    $rep->Info(null, $cols, $headers, $aligns);
	    $rep->NewPage();
	   

	   $get_employees_list = get_empl_ids_from_dept_id($dept_id);
				
				//$Total_gross = $total_net = 0; 
				foreach($get_employees_list as $single_empl) { 
					
					$data_for_empl = GetRow('kv_empl_salary', array('empl_id' => $single_empl, 'month' => $month, 'year' => $year));
					if($data_for_empl) {
						$rep->NewLine(1, 2);
											
						$vj = 0; $jv = 1; 
						$rep->TextCol($vj, $jv, $data_for_empl['empl_id']);
						$vj++; $jv++;
					   $rep->TextCol($vj,$jv,kv_get_empl_name($data_for_empl['empl_id']));

					    $Allowance = get_allowances('Earnings');
						while ($single = db_fetch($Allowance)) {
							$vj++; $jv++;
							$rep->TextCol($vj, $jv, $data_for_empl[$single['id']]);
						}
						$vj++; $jv++;
						$rep->TextCol($vj, $jv, $data_for_empl['ot_other_allowance']);
						$total_ded = 0; 
						$DAllowance = get_allowances('Deductions');
						while ($single = db_fetch($DAllowance)) {
							$vj++; $jv++;
							$rep->TextCol($vj, $jv, $data_for_empl[$single['id']]);
							$total_ded += $data_for_empl[$single['id']]; 
						}
						$vj++; $jv++;
						$rep->TextCol($vj, $jv, $data_for_empl['loan']);
						$total_ded += $data_for_empl['loan']; 

						$vj++; $jv++;
						$rep->TextCol($vj, $jv, get_empl_attendance_for_month($data_for_empl['empl_id'], $month, $year));

						$vj++; $jv++;
						$rep->TextCol($vj, $jv, $data_for_empl['lop_amount']);
						$total_ded += $data_for_empl['lop_amount']; 

						$vj++; $jv++;
						$rep->TextCol($vj, $jv, $data_for_empl['misc']);
						$total_ded += $data_for_empl['misc']; 

						$vj++; $jv++;
						$rep->TextCol($vj, $jv, $data_for_empl['gross']);

						$vj++; $jv++;
						$rep->TextCol($vj, $jv, $total_ded);

						$vj++; $jv++;
						$rep->TextCol($vj, $jv, $data_for_empl['net_pay']);

						//$Total_gross += $data_for_empl['gross'];
						//$total_net += $data_for_empl['net_pay'];
						
						$rep->NewLine(); 
					}
				}

				
		if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
			$rep->NewPage();	
		$rep->End();
	//} else{
	//	display_warning("No Data found for the selected Period for the Selected Department"//);
	//}
	

?>