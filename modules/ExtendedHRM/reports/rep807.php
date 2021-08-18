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
    //$dept_id = $_POST['PARAM_2'];
    $comment = $_POST['PARAM_2'];
    //$destination = $_POST['PARAM_4'];
	$allowance_id = $_POST['allowanc_id']; 
	
	//if(db_has_some_entry_to_this_allowance($allowance_id, $month, $year)) {
	//if ($destination)
	//	include_once($path_to_root . "/reporting/includes/excel_report.inc");
	//else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = 'P';

	//$selected_fiscal_year = get_fiscalyear($year);

   // $start = date("Y", strtotime($selected_fiscal_year['begin']));

    //$end = 	date("Y", strtotime($selected_fiscal_year['end']));

   // $get_year_frm_fiscalyear = date("Y-m-d", strtotime($start.'-'.$month.'-01'));

  //  if(is_date_in_fiscalyear($get_year_frm_fiscalyear)){ 
   // 	$total_days =  date("t", strtotime($start.'-'.$month.'-01'));
    //	$rep_year = $start;
   // } else {
    ////	$total_days =  date("t", strtotime($start.'-'.$month.'-01'));
    //	$rep_year = $end;
   // }

	$cols = array(0, 50, 140, 250, 500, 580);

	$headers = array(_('ID'), _('Date'), _('Empl ID'), _('Employee Name'),  _('Amount') );
	$aligns = array('left',	'left', 'left', 'left', 'left');

    $rep = new FrontReport(_('Allowance : '.get_allowance_name($allowance_id).' - '.kv_month_name_by_id($month)), "Allowance", user_pagesize(), 9, $orientation);
  		
    $rep->Font();
    $rep->Info(null, $cols, $headers, $aligns);
    $rep->NewPage();
    $summary_start_row = $rep->bottomMargin + (5 * $rep->lineHeight);
    $selected_empl = kv_get_allowance_transactions($allowance_id, $month, $year);
	$GrandTotal = 0; 
	$line_value = 670;
	while ($row = db_fetch_assoc($selected_empl)) {		
		$rep->NewLine(1, 2);		
		$rep->TextCol(0, 1, $row['id']);
		$rep->TextCol(1, 2, $row['date']);
		$rep->TextCol(2, 3, $row['empl_id']);
		$rep->TextCol(3, 4, kv_get_empl_name($row['empl_id']));		
		$rep->TextCol(4, 5, $row[$allowance_id]);
		$GrandTotal += $row[$allowance_id];
		$rep->NewLine();  
		if ($rep->row < $summary_start_row)
			$rep->NewPage();				
	}	
	$rep->NewLine(); 
	$rep->row = $summary_start_row;
	$rep->cols[2] += 20;
	$rep->cols[3] += 20;
	$rep->aligns[3] = 'right';
	$rep->Line($line_value-555, 0.00001,0,0);	
	$rep->TextCol(3, 4, _("Total     "), -20);
	$rep->TextCol(4, 5,	$GrandTotal, -20);
	$rep->row = 100;	
			if($comment){				
				$rep->SetTextColor(0, 0, 0);
				$rep->Text(40, 'Comments',0,0,65);
				$rep->Text(200, $comment,0,0,65);  //$rep->NewLine(2);	
			}	
	if ($rep->row < $rep->bottomMargin )
		$rep->NewPage();	
	$rep->End();
//} else{
//	display_warning("No Data found for the selected Allowance for the Selected Duration"//);
//}
?>