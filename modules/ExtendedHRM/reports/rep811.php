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

    	//$dept_id = $_POST['PARAM_0'];
        $dept_id=$_SESSION['my_ids'];
       // display_error($dept_id);
    	$comment = $_POST['PARAM_1'];
    	$orientation = $_POST['PARAM_2'];
    	$destination = 1 ;

    if(kv_get_visitor_count_based_on_dept($dept_id)){


	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
        else
		include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");
        
	$orientation = ($orientation ? 'L' : 'P');

	$cols = array(0, 45, 95, 145, 230, 325, 395, 475, 585, 800, 945, 1045);

	$headers = array(_('ID#'), _('Name'), _('To Meet'), _('Company'), _('Coming From'), 
		_('Purpose'), _('Contact Number'),_('Email'),_('Check In(Date and Time)'),_('Check Out(Date and Time)'),_('Remarks'));

	$aligns = array('left',	'left',	'left',	'left',	'left', 'left', 'left', 'left', 'left', 'left', 'left');

    $rep = new FrontReport(_('Visitor Management'), "visitor-list", user_pagesize(), 12, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);
		
    $rep->Font();
    $rep->Info(null, $cols, $headers, $aligns);
    $rep->NewPage();
   
    $result = kv_get_visitor_list_based_on_dept_rep($dept_id);	
  
	while ($myrow = db_fetch($result))	{		
			$rep->NewLine(1, 2);
			$rep->TextCol(0, 1, $myrow['ref_id']);
			$rep->TextCol(1, 2,	$myrow['first_name'].' '.$myrow['last_name']);
			$rep->TextCol(2, 3,	$myrow['to_meet']);
			$rep->TextCol(3, 4, $myrow['company']);
			$rep->TextCol(4, 5,	$myrow['coming_from']);
			$rep->TextCol(5, 6,	$myrow['purpose']);
			$rep->TextCol(6, 7,	$myrow['contact_number']);
			$rep->TextCol(7, 8,	$myrow['email']);
                        $rep->TextCol(8, 9,	date('Y-m-d h:i A',strtotime($myrow['tr_fromdate'])));
                       // $rep->TextCol(9, 10,	date('Y-m-d h:i A',strtotime($myrow['tr_todate'])));
                        $rep->TextCol(9, 10,	!isset($myrow['tr_todate'])?'----': date('Y-m-d h:i A',strtotime($myrow['tr_todate'])));
                        $rep->TextCol(10, 11,	$myrow['remarks']);
			$rep->NewLine();    	
	}
			
	if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
		$rep->NewPage();	
	$rep->End();
	}else{ 
		display_warning("No Employee Found in it. Please add some employees.");
	}
        
        
        function kv_get_visitor_count_based_on_dept($dept_id) {
    
        if($dept_id == 0){
		//get_all_employees();
		$sql = "SELECT COUNT(vistitor_id) FROM ".TB_PREF."visitor_management";
  
		return check_empty_result($sql);

	} else{
		$sql="SELECT COUNT(vistitor_id) FROM ".TB_PREF."visitor_management WHERE vistitor_id=".db_escape($dept_id);
               
		return check_empty_result($sql);
	}
       
}

function kv_get_visitor_list_based_on_dept_rep($dept_id) {

   	//$dept_id = "18,19,20";
	$sql= "SELECT * FROM ".TB_PREF."visitor_management where vistitor_id in(".$dept_id.")";
       // display_error($sql);
        return db_query($sql, "could not get the selected Employees");
   
}
?>