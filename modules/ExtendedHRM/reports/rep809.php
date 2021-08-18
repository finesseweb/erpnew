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
 
	$destination = $_POST['PARAM_1'];
   // $month = $_POST['PARAM_1'];
  //  $dept_id = $_POST['PARAM_2'];
  //  $comment = $_POST['PARAM_3'];
  //  $destination = $_POST['PARAM_4'];

    	if ($destination)
			include_once($path_to_root . "/reporting/includes/excel_report.inc");
		else
			include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

		$orientation = 'L';

	   	$th1 = array(_("Name"),_("Description"),_("From Date"),_("To Date"));
	   	$headers =  $th1;
	   	$count_header = count($headers);
         
		//display_error($count_header);
		//$cols = array(0, 35, 90, 130, 150, 170, 190, 220, 250, 280, 310, 340, 370, 400, 430, 460, 490, 530, 580);
			
		$aligns = $cols = array();
		$Col_count = 570/$count_header;
		for($vj=0; $vj<=$count_header; $vj++){
			$aligns[] ='left';
			$cols[] = $vj*$Col_count;
		}
        $year_of_selection = kv_fiscal_year_by_id($year);

	    $rep = new FrontReport(_('Year of Holidays: '.$year_of_selection[0]), "Holidays", user_pagesize(), 9, $orientation);
	    
	    if ($orientation == 'L')
	    	recalculate_cols($cols);
			
	    $rep->Font();
	    $rep->Info(null, $cols, $headers, $aligns);
	    $rep->NewPage();
	   

	   $get_holidays_list = get_holidays_from_fiscalyear($year);
		
				//$Total_gross = $total_net = 0; 
				foreach($get_holidays_list as $single_empl) { 
					
					$data_for_empl = GetRow('kv_holiday_master', array('holiday_id' => $single_empl,'fisc_year' => $year));
					if($data_for_empl) {
						$rep->NewLine(1, 2);
											
						$vj = 0; $jv = 1; 
						$rep->TextCol($vj, $jv, $data_for_empl['name']);
						$vj++; $jv++;
					   $rep->TextCol($vj,$jv,$data_for_empl['descpt']);

					    // = get_allowances('Earnings');
						//while ($single = db_fetch($Allowance)) {
							$vj++; $jv++;
							$rep->TextCol($vj, $jv, $data_for_empl['from_date']);
						//}
						$vj++; $jv++;
						$rep->TextCol($vj, $jv, $data_for_empl['to_date']);
					

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