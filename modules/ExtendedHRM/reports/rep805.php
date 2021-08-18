<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
/* Loan PDF
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
   
    $empl_id = (isset($_POST['PARAM_0']) ? $_POST['PARAM_0'] : (isset($_GET['PARAM_0']) ? $_GET['PARAM_0'] : 0));
	$comment = (isset($_POST['PARAM_1']) ? $_POST['PARAM_1'] : (isset($_GET['PARAM_1']) ? $_GET['PARAM_1'] : ''));;
    //$destination = (isset($_POST['PARAM_2']) ? $_POST['PARAM_2'] : (isset($_GET['PARAM_2']) ? $_GET['PARAM_2'] : ''));
    //$destination = (isset($_POST['PARAM_3']) ? $_POST['PARAM_3'] : (isset($_GET['PARAM_3']) ? $_GET['PARAM_3'] : ''));
    $_POST['REP_ID'] = 805; 

if(db_has_empl_loan_all_status($empl_id)){
	//if ($destination)
	//	include_once($path_to_root . "/reporting/includes/excel_report.inc");
	//else
		include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

	$orientation = 'P';

	$cols = array(10,75,150,225, 300, 375, 450, 525);
		
	$headers = array(_("Loan Type"), _("Loan Amount"), _("Monthly Pay"), _("Periods"), _("Periods Paid"), _("Start Date"), _("End Date"), _("Status"));

	$aligns = array('left',	'left', 'left', 'left','left',	'left', 'left', 'left');

    $rep = new FrontReport(_('Loan Details'), "Loan Details", user_pagesize(), 9, $orientation);

	$name_and_dept = get_empl_name_dept($empl_id);
	$employee_info = array(
					'id' => $empl_id,
					'title'  => 'Loan Details', 
					'empl_id' => $empl_id,
					'empl_name' => $name_and_dept['name'],
					'department' => get_department_name($name_and_dept ['deptment']),
					'desig' => kv_get_empl_desig($empl_id),
					'joining' => sql2date(get_employee_join_date($empl_id))  );

			$baccount = get_empl_bank_acc_details($empl_id);
			//if ($destination == 0)
				$rep->SetHeaderType('Header2');	
		    $rep->Font();
		    $rep->Info(null, $cols, $headers, $aligns);
		    
		    $contacts = array( 
		    			'email' => $name_and_dept['email'],
		    			'name2'	=>	null,
		    			'name'  => 	$name_and_dept['name'],
		    			'lang'	=> null,
		    		);
			//if ($destination == 0)
				$rep->SetCommonData($employee_info, $baccount, array($contacts),  'loan');
			$loans = get_empl_loan_details_Complete($empl_id);	
			$rep->NewPage();
			$rep->NewLine();
			$Value = -70;
			foreach($loans as $loan_single){
					
				$line_value=670;
				$date_of_end = date('Y-m-d', strtotime("+".$loan_single[5]." months", strtotime($loan_single[7]))); 
				echo '<tr style="text-align:center"><td>'.$loan_single[2].'</td>
				<td>'.$loan_single[3].'</td>
				<td>'.$loan_single[4].'</td>
				<td>'.$loan_single[5].'</td>
				<td>'.$loan_single[6].'</td>
				<td>'.sql2date($loan_single[7]).'</td>
				<td>'.sql2date($date_of_end).'</td>
				<td>'.$loan_single[8].'</td><tr>';
				$rep->Text(40, $loan_single[2],0,0,$Value);
				$rep->Text(115, $loan_single[3],0,0,$Value++);
				$rep->Text(190, $loan_single[4],0,0,$Value);
				$rep->Text(265, $loan_single[5],0,0,$Value);
				$rep->Text(350, $loan_single[6],0,0,$Value);
				$rep->Text(405,sql2date($loan_single[7]),0,0,$Value);
				$rep->Text(480, sql2date($date_of_end),0,0,$Value);
				$rep->Text(545, $loan_single[8],0,0,$Value);
				$rep->NewLine(2);
				$Value++;
			}
				
			$rep->NewLine(1);
			$rep->Line($line_value-535, 0.00001,0,0);	
			$rep->row = 180;		
			if($comment){				
				$rep->SetTextColor(0, 0, 0);
				$rep->Text(40, 'Comments',0,0,65);
				$rep->Text(200, $comment,0,0,65);
				//$rep->NewLine(2);	
			}		
			$rep->Line($line_value-585, 0.00001,0,0);		
				
	if ($rep->row < $rep->bottomMargin )
		$rep->NewPage();	
	$rep->End(); //1, 'Payslip ');
}else{
	display_warning("No Loan Details found for the Selected Employee.");
}
?>