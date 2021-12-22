<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/


$page_security = 'HR_SELATTENDANCE';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/attendance/function/function.php");
add_access_extensions();
$version_id = get_company_prefs('version_id');

$js = '';
if($version_id['version_id'] == '2.4.1'){
	if ($SysPrefs->use_popup_windows) 
		$js .= get_js_open_window(900, 500);	

	if (user_use_date_picker()) 
		$js .= get_js_date_picker();
	
}else{
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
}
 
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/db_pager.inc");
page(_("Attendance Inquiry"));
 
 check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));
 
 simple_page_mode(true);
//----------------------------------------------------------------------------------------
	$new_item = get_post('selected_id')=='' || get_post('cancel') ;
	$month = get_post('month','');
	$year = get_post('year','');
	if (isset($_GET['selected_id'])){
		$_POST['selected_id'] = $_GET['selected_id'];
	}
	$selected_id = get_post('selected_id');
	 if (list_updated('selected_id')) {
		$_POST['empl_id'] = $selected_id = get_post('selected_id');
	    $Ajax->activate('details');
	}
	if (isset($_GET['month'])){
		$_POST['month'] = $_GET['month'];
	}
	if (isset($_GET['year'])){
		$_POST['year'] = $_GET['year'];
	}

	
	if (list_updated('month')) {
		$month = get_post('month');   
		$Ajax->activate('details');
	}

//$month = date("m");
//----------------------------------------------------------------------------------------
                                    $queryres = db_query(get_hours_list(1));
                                    $dayhr = db_fetch_assoc($queryres);
                                    $perdaytotaltimeinseconds = time_to_seconds(decimal_to_time($dayhr['full_day']));
	start_form(true);
		start_table(TABLESTYLE_NOBORDER);
			echo '<tr>';
				kv_fiscalyears_list_cells(_("Fiscal Year:"), 'year', null, true);
			 	kv_current_fiscal_months_list_cell("Months", "month", null, true);
			 	department_list_cells(_("Select a Department: "), 'selected_id', null,	_('All Departments'), true, check_value('show_inactive'));
				$new_item = get_post('selected_id')=='';
		 	echo '</tr>';
	 	end_table(1);

	 	if (get_post('_show_inactive_update')) {
			$Ajax->activate('month');
			$Ajax->activate('details');
			$Ajax->activate('selected_id');		
			set_focus('month');
		}
		if($month==null){			 
			$month = $_POST['month'];
		}
		if($year==null){			 
			$year = $_POST['year'];
		}
		//echo $month;
		$total_days =  date("t", strtotime($year."-".$month."-01"));
		div_start('details');
		
			$selected_empl = kv_get_employees_list_based_on_dept1($selected_id);
			//$selected_empl_attend_details=kv_get_attend_details($selected_id);
			//start_table(TABLESTYLE);
			echo "<div style='overflow:scroll'>";
			echo "<table class='tablestyle'>";
			$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
 			$ext_year = date("Y", strtotime($months_with_years_list[get_post('month')]));
				
				echo  "<tr>
					<td rowspan=2 class='tableheader'>" . _("Empl ID") . "</td>
					<td rowspan=2 class='tableheader'>" . _("Empl Name") . "</td>	
                                            <td rowspan=2 class='tableheader'>#</td>
					<td colspan=".$total_days." class='tableheader'>" . _(date("Y - F", strtotime($ext_year."-".$month."-01"))) . "</td>

                                            <td rowspan=2 class='tableheader'>" . _("#") . "</td>
                                        <td rowspan=2 class='tableheader'>" . _("Wrk<br/>Days") . "</td>
					<td rowspan=2 class='tableheader'>" . _("P") . "</td>
					<td rowspan=2 class='tableheader'>" . _("HD") . "</td>
					<td rowspan=2 class='tableheader'>" . _("CL") . "</td>
					<td rowspan=2 class='tableheader'>" . _("VL") . "</td>
					<td rowspan=2 class='tableheader'>" . _("OD") . "</td>
					<td rowspan=2 class='tableheader'>" . _("ML") . "</td>
					<td rowspan=2 class='tableheader'>" . _("SCL") . "</td>
					<td rowspan=2 class='tableheader'>" . _("H") . "</td>
					<td rowspan=2 class='tableheader'>" . _("HP") . "</td>
					<td rowspan=2 class='tableheader'>" . _("WOP") . "</td>
					<td rowspan=2 class='tableheader'>" . _("MTL") . "</td>
					<td rowspan=2 class='tableheader'>" . _("PTL") . "</td>
					<td rowspan=2 class='tableheader'>" . _("OTP") . "</td>
					<td rowspan=2 class='tableheader'>" . _("LOP") . "</td>
					<td rowspan=2 class='tableheader'>" . _("Srt<br/>Attendance") . "</td>
					<td rowspan=2 class='tableheader'>" . _("Pay<br/>Days") . "</td>
					</tr><tr>";
					$weekly_off = GetSingleValue('kv_empl_option','option_value', array('option_name'=>'weekly_off'));
                                       
					$weekly_offdate= 0 ; 
                                        $weekly_off_arr = explode(',',$weekly_off);
                                        
                                        $company_year = get_company_pref('f_year');
                                        $fiscal_year = get_fiscalyear($company_year);
                                           
					for($kv=1; $kv<=$total_days; $kv++){
                                            $attendance_date = strtotime($ext_year."-".$month."-".$kv);
                                             $holiday_date =  get_holiday_check(date("Y-m-d",$attendance_date), date("Y-m-d",$attendance_date), $fiscal_year[0]);
						////[COMENTED TO MAKE SAT SUN WEEKLY OF BY ASHUTOSH 15-01-2019]if(date("D", strtotime($ext_year."-".$month."-".$kv))  == $weekly_off){
                                            if(in_array(date("D", strtotime($ext_year."-".$month."-".$kv)), $weekly_off_arr)){
							echo "<td style='background-color:#e0db98' class='tableheader'>"._(date("d", strtotime($ext_year."-".$month."-".$kv)))."<br/>". _(date("D", strtotime($ext_year."-".$month."-".$kv))) . "</td>";
							if($weekly_offdate==0)
								$weekly_offdate=$kv;
						}
                                                else if($holiday_date){
                                                    echo "<td style='background-color:#aadeaa' class='tableheader'>"._(date("d", strtotime($ext_year."-".$month."-".$kv)))."<br/>". _(date("D", strtotime($ext_year."-".$month."-".$kv))) . "</td>";
                                                }
                                                else{
							echo "<td class='tableheader'>"._(date("d", strtotime($ext_year."-".$month."-".$kv)))."<br/>". _(date("D", strtotime($ext_year."-".$month."-".$kv))) . "</td>";
						}
					}
					
					echo "</tr>";
				//$sql = kv_hrm_get_employee_list();
		while ($row = db_fetch_assoc($selected_empl)) {
                                     //------------------------------------------------Manual Day Deduction ------------------------------------------------ 
                                                $id = getManualSalaryId1($row['empl_id'], date("Y-m", strtotime($months_with_years_list[get_post('month')])));
                                                $man_sal_result = getValueFromManualSalary1($id);
                                                $man_sal_row =  db_fetch($man_sal_result);
                                            //---------------------------------------------------------------------------------------------------------------------
					
						$details_single_empl = GetRow('kv_empl_attendancee', array('month' => $month, 'year' => $year, 'empl_id' => $row['empl_id'])); 
							
						echo '<tr style="text-align:center"><td>'.$row['empl_id'].'</td>'
                                                        . '<td>'.$row['empl_firstname'].'</td>';
                                                echo '<td><table class="tablestyle" width="100%">'
                                                . '<tr style="text-align:center"><td>In</td></tr>'
                                                . '<tr style="text-align:center"><td>Out</td></tr>'
                                                . '<tr style="text-align:center"><td>wrk&nbsp;hrs.</td></tr>'
                                                . '</table></td>';
						$leave_Day = 0 ;
						$present_days = 0;
						$casual_leaves = 0;
						$half_casual_leaves = 0;
						$vacation_leaves = 0;
						$on_duty = 0;
						$medical_leaves = 0;
						$spl_casual_leaves = 0;
						$holidays = 0;
						$holidays_present = 0;
						$week_off_present = 0;
						$half_leaves_count = 0;
						$maternity_leaves_present = 0;
						$paternity_leaves_present = 0;
						$official_tour_present = 0;
						$week_end=1;
						$weekly_offdat = $weekly_offdate;
                                                $working_days = $total_days;
                                                $totalworkinghrs = 0;
						for($kv=5; $kv<=$total_days+4; $kv++){
							
							//if($weekly_offdat == $week_end || $weekly_offdat2 == $week_end2){
                                                    $vj = $kv-4; 
                                                     $attendance_date = strtotime($ext_year."-".$month."-".$vj);
                                                     $holiday_date =  get_holiday_check(date("Y-m-d",$attendance_date), date("Y-m-d",$attendance_date), $fiscal_year[0]);
                                                  $logindetails = get_in_out_time_by_date($row['empl_id'], date("Y-m-d",$attendance_date));
                                                 
                                                  
                                                   if(in_array(date("D", strtotime($ext_year."-".$month."-".$vj)),$weekly_off_arr)){
								$style="style='background-color: #fda8a8; text-align:center; font-size:10px;font-weight:bolder;border-color:white;'"; 
                                                                $week_end2 = 0;
								$week_end=1;
								$weekly_offdat = 7;
                                                                $weekly_offdat2 = 7;
                                                                $working_days--;
                                                                $str = 'WO';
							}
                                                        else if($holiday_date){
                                                            $style="style='background-color: #cddeaa; text-align:center;font-size:10px;font-weight:bolder;border-color:white;'"; 
                                                            $working_days--;
                                                            $str = 'H';
                                                        }
                                                        else{
								$style="style='text-align:center;font-size:10px;font-weight:bolder;border-color:white;'"; 
								$week_end++;
                                                                $week_end2+2;
                                                                $str = '00:00:00';
							}
							$details_single_empl[$vj] = $details_single_empl[$vj]=='WOP'?'WOP':$details_single_empl[$vj];
							$details_single_empl[$vj] = $details_single_empl[$vj]=='WO'?'CO':$details_single_empl[$vj];
							$logindetail['a_in_time'] = $logindetails[0]?$logindetails[0]:0;
                                                        $logindetail['a_out_time'] = $logindetails[1]?$logindetails[1]:0;
                                                        $workingsec =  $logindetails[2]?$logindetails[2]:0;
                                                        $timestr = decimal_to_time($workingsec/3600);
                                                        if(in_array($details_single_empl[$vj],array("P","HD","HP","WOP"))){
							echo '<td><table class="tablestyle" width="100%"><tr style="text-align:center"><td '.$style.' >'. _($logindetails[0]?$logindetails[0]:$str).'</td></tr>';
                                                        echo '<tr><td '.$style.' >'._($logindetails[1]?$logindetails[1]:$str).'</td></tr>';
                                                       echo '<tr><td '.$style.' >'.$timestr.'</td>'
                                                                . '</tr></table></td>';
                                                        }
                                                        else{
                                                            echo '<td><table class="tablestyle" width="100%"><tr style="text-align:center"><td '.$style.' >'. _($details_single_empl[$vj]?$details_single_empl[$vj]:$str).'</td></tr>';
                                                            echo '<tr><td '.$style.' >'._($details_single_empl[$vj]?$details_single_empl[$vj]:$str).'</td></tr>';
                                                            echo '<tr><td '.$style.' >'._($details_single_empl[$vj]?$details_single_empl[$vj]:$str).'</td>'
                                                                . '</tr></table></td>';
                                                        }
                                                       $totalworkinghrs+=time_to_seconds($timestr);
							if($details_single_empl[$vj] == 'A')
								$leave_Day += 1;
							if($details_single_empl[$vj] == 'HD')
								$leave_Day += 0.5;
							if($details_single_empl[$vj] == 'HD')
								$half_leaves_count++;
							if($details_single_empl[$vj] == 'CL')
								$casual_leaves++;
							if($details_single_empl[$vj] == 'HCL')
								$half_casual_leaves += 0.5;
							if($details_single_empl[$vj] == 'P')
								$present_days++;
							if($details_single_empl[$vj] == 'VL')
								$vacation_leaves++;
							if($details_single_empl[$vj] == 'OD')
								$on_duty++;
							if($details_single_empl[$vj] == 'ML')
								$medical_leaves++;
							if($details_single_empl[$vj] == 'SCL')
								$spl_casual_leaves++;
							if($details_single_empl[$vj] == 'H')
								$holidays++;
							if($details_single_empl[$vj] == 'HP')
								$holidays_present++;
								
							if($details_single_empl[$vj] == 'WOP')
								$week_off_present++;
							if($details_single_empl[$vj] == 'MTL')
								$maternity_leaves_present++;
							if($details_single_empl[$vj] == 'PTL')
								$paternity_leaves_present++;
							if($details_single_empl[$vj] == 'OTP')
								$official_tour_present++;
						}
						$Payable_days=$total_days-$leave_Day;
						$tot_cls = $casual_leaves+$half_casual_leaves;
                                                $totalhours = decimal_to_time(($perdaytotaltimeinseconds*$working_days)/3600);
                                                $otpHours = decimal_to_time(($totalworkinghrs - ($perdaytotaltimeinseconds*$working_days))/3600);
                                                  echo '<td><table class="tablestyle" width="100%">'
                                                . '<tr style="text-align:center;"><td style="font-size:10px;font-weight:bolder;">'.$totalhours.'</td></tr>'
                                                . '<tr style="text-align:center;"><td style="font-size:10px;font-weight:bolder;">'.$otpHours.'</td></tr>'
                                                . '<tr style="text-align:center;"><td style="font-size:10px;font-weight:bolder;">'. decimal_to_time($totalworkinghrs/3600).'</td></tr>'
                                                . '</table></td>';
						echo '<td>'.$working_days.' </td>'
                                                        . '<td >'.$present_days.'</td>'
                                                        . '<td >'.$half_leaves_count.'</td>'
                                                        . '<td >'.$tot_cls.'</td>'
                                                        . '<td >'.$vacation_leaves.'</td> '
                                                        . '<td >'.$on_duty.'</td>  '
                                                        . '<td >'.$medical_leaves.'</td> '
                                                        . '<td >'.$spl_casual_leaves.'</td> '
                                                        . '<td >'.$holidays.'</td> '
                                                        . '<td >'.$holidays_present.'</td> '
                                                        . '<td >'.$week_off_present.'</td>  '
                                                        . '<td >'.$maternity_leaves_present.'</td> '
                                                        . '<td >'.$paternity_leaves_present.'</td> '
                                                        . '<td >'.$official_tour_present.'</td>'
                                                        . '<td >'. $leave_Day .'</td> '
                                                         . '<td >'. (float)$man_sal_row['days_deducted'] .'</td> '
                                                        . '<td >'.($Payable_days - (float)$man_sal_row['days_deducted']).' </td>';
						echo '<tr>';
					}
					echo '</table>';
					echo '</div>';
		//	end_table(1);
		function working_hours($row) {
    $logout = strtotime($row['a_out_time']);
    $login = strtotime($row['a_in_time']);
   
    $diff = $logout - $login;
    $h_v =  explode(':', timeDiffrence($row['a_out_time'], $row['a_in_time']));
  
    if ($logout) {
        $working_hours = $h_v[0] . ":" . $h_v[1] . ":" . $h_v[2];
    } else {
        $working_hours = '-';
    }
    return $working_hours;
   
}
		div_end();
	end_form();
 
end_page(); ?>