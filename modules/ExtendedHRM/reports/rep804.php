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

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = 'L';

	$selected_fiscal_year = get_fiscalyear($year);

    $start = date("Y", strtotime($selected_fiscal_year['begin']));

    $end = 	date("Y", strtotime($selected_fiscal_year['end']));

    $get_year_frm_fiscalyear = date("Y-m-d", strtotime($start.'-'.$month.'-01'));

    if(is_date_in_fiscalyear($get_year_frm_fiscalyear)){ 

    	$total_days =  date("t", strtotime($start.'-'.$month.'-01'));
    	$rep_year = $start;
    } else {

    	$total_days =  date("t", strtotime($start.'-'.$month.'-01'));
    	$rep_year = $end;
    }

	$cols = array(0, 25, 100);

	$headers = array(_('ID#'), _('Name'));
	$aligns = array('left',	'left');

	$vj= 1;
	for($kv=3; $kv<=$total_days+2; $kv++){	
		$zv = $kv-1; 
		$col_span = 100+($vj*11); 	
		$cols[$kv] =  $col_span;
		$headers[$zv] =  _($vj);
		$aligns[$zv] =  'left';
		$vj++;
	}	
	$headers[] = _("Working Days");
	$headers[] = _("LOP Days");
	$headers[] = _("Payable Days");
	$aligns[] =  'left';
	$aligns[] =  'left';
	$aligns[] =  'left';
	$cols[] = 100+$col_span-50;
	$cols[] = 100+$col_span-10;
	$cols[] = 100+$col_span+30;

    $rep = new FrontReport(_('Month of Attendance: '.kv_month_name_by_id($month).' - Dept: '.get_department_name($dept_id)), "Attendance", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);
		
    $rep->Font();
    $rep->Info(null, $cols, $headers, $aligns);
    $rep->NewPage();

    $selected_empl = kv_get_employees_list_based_on_dept($dept_id);
	while ($row = db_fetch_assoc($selected_empl)) {
		
		$details_single_empl = GetRow('kv_empl_attendancee', array('month' => $month, 'year' => $year, 'empl_id' => $row['empl_id'])); 
	
		$rep->NewLine(1, 2);
		$rep->TextCol(0, 1, $row['empl_id']);
		$rep->TextCol(1, 2, $row['empl_firstname']);
		$leave_Day = 0 ;
		$j = 2;
		$v = 3; 
		for($kv=3; $kv<=$total_days+2; $kv++){							
			$rep->TextCol($j, $v, ($details_single_empl[$kv]? $details_single_empl[$kv]: '-'));
			
			if($details_single_empl[$kv] == 'A')
				$leave_Day += 1;
			if($details_single_empl[$kv] == 'HD')
				$leave_Day += 0.5;
			$j++; $v++;
		}
		$Payable_days=$total_days-$leave_Day;

		$rep->TextCol($j, $v, $total_days);
		$j++;
		$v++;
		$rep->TextCol($j, $v, $leave_Day);

		$j++;
		$v++;
		$rep->TextCol($j, $v, $Payable_days);
		$rep->NewLine();  				
	}	

	if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
		$rep->NewPage();	
	$rep->End();

?>