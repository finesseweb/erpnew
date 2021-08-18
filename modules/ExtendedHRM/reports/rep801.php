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

    	$dept_id = $_POST['PARAM_0'];
    	$comment = $_POST['PARAM_1'];
    	$orientation = $_POST['PARAM_2'];
    	$destination = $_POST['PARAM_3'];


    if(kv_get_employees_count_based_on_dept($dept_id)){


	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');

	$cols = array(0, 45, 95, 145, 230, 325, 395, 475, 585);

	$headers = array(_('ID#'), _('Name'), _('Gender'), _('Department'), _('Email'), 
		_('Phone Number'), _('Designation'),_('Address'));

	$aligns = array('left',	'left',	'left',	'left',	'left', 'left', 'left', 'left');

    $rep = new FrontReport(_('Employees - Dept: '.get_department_name($dept_id)), "Employees", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);
		
    $rep->Font();
    $rep->Info(null, $cols, $headers, $aligns);
    $rep->NewPage();
   
    $result = kv_get_employees_list_based_on_dept_rep($dept_id);	

	while ($myrow = db_fetch($result))	{		
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
	}
			
	if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
		$rep->NewPage();	
	$rep->End();
	}else{ 
		display_warning("No Employee Found in it. Please add some employees.");
	}
?>