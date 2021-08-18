<?php

$page_security = 'SA_ATTENDANCELIST';
if (!@$_GET['popup'])
	$path_to_root = "..";
else	
	$path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/attendance/function/function.php");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
if (!@$_GET['popup'])
{
	$js = "";
	//if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	//if ($use_date_picker)
		$js .= get_js_date_picker();
	page(("Attendance"), @$_GET['popup'], false, "", $js);
}

simple_page_mode(true);

// Ajax updates
//
$ids = '';
if (get_post('Search'))
{ 
	$Ajax->activate('title');
}
//--------------------------------------------------------------------------------------
if (!isset($_POST['filterType']))
	$_POST['filterType'] = -1;

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
date_row(_("Date") . "", 'in_time');
//date_row(_("Log Out Date") . "", 'out_time');
check_cells(_("Show Monthly Attendance:"), 'monthly_val', null, true);
submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();


function display_rows(){
        if(!isset($_POST['user_attendance_page_next']))
        $_SESSION['fun']['id'] = 0;
        $empl_id=$_SESSION['wa_current_user']->empl_id;
	$sql = UserAttendanceList(get_post('in_time'),$empl_id,get_post('monthly_val'));
	$cols = array(
		_("Id") => array('name'=>'id','fun'=>'id'),
		_("Empl Id") => array('name'=>'empl_id', 'fun'=>'empId'),
                _("Log In Date") => array('align' => 'center', 'fun'=>'datelist'),
		_("Log In Time") => array('align' => 'center', 'fun'=>'checking'),
                _("Log Out Date") => array('align' => 'center', 'fun'=>'datelist2'),
                _("Log Out Time") => array('align' => 'center', 'fun'=>'checkout'),
             _("Working Hours") => array('align' => 'center', 'fun' => 'working_hours'),
             
		//array('')
	);
	$table =& new_db_pager('user_attendance', $sql, $cols);
        
        totalCompanyHour();
        
        totalhours();
	$table->width = "90%";
	display_db_pager($table);
        
}



function totalCompanyHour(){
	
	
	
	//-------------------------------------------this is for total company hors---------------//
    $sq_w = get_defined_working_hours();
    $val_w = db_query($sq_w);
    $total_w_d = db_fetch_row($val_w);
	 $_POST['company_working_hour'] = $total_w_d[0];
	 //-------------------------------------END ----------------------------------------------
	    $empl_id=$_SESSION['wa_current_user']->empl_id;
	$sql = UserAttendanceList(get_post('in_time'),$empl_id,get_post('monthly_val'));
	 

	 
	 $sql_query = db_query($sql);
	  while ($row = db_fetch($sql_query)) {
		   if ($row['a_in_time']) {
		   if (empty($_POST['last_date']))
                $_POST['last_date'] = date('Y-m-d', strtotime($row['a_in_time']));
            $_POST['month_time_arr'][] = date('Y-m-d', strtotime($row['a_in_time']));
		   }
		   working_difference($row);
	  }
	 
	 $total_company_work_hours = count(array_unique($_POST['month_time_arr'])) * $_POST['company_working_hour'];
         $total_company_work_hours = convertTime($total_company_work_hours);

        /*
         * cal shortage hr
         * cal shortage min
         * cal shortage sec
         * calculating array of timing
         */


        $total_arr = $_POST['short_time_arr'];

        $sum = strtotime('00:00:00');
        $sum2 = 0;
        foreach ($total_arr as $v) {

            $sum1 = strtotime($v) - $sum;

            $sum2 = $sum2 + $sum1;
        }

        $sum3 = $sum + $sum2;
         $_POST['short_hr'] = date("H:i:s", $sum3);
         $_POST['total_work_hour'] = $total_company_work_hours;
      
}

function working_difference($row) {
    $sq_w = get_defined_working_hours();
    $val_w = db_query($sq_w);
    $total_w_d = db_fetch_row($val_w);
    $_POST['company_working_hour'] = $total_w_d[0];

    
       $logout = strtotime($row['a_out_time']);
    $login = strtotime($row['a_in_time']);
   
    $diff = $logout - $login;
    $h_v =   timeDiffrence($row['a_out_time'], $row['a_in_time']);
    $tot_work_hr = convertTime($total_w_d[0]);
    if($tot_work_hr >= $h_v){
        $short_hour = timeDiffrence($h_v, $tot_work_hr);
         $_POST['short_time_arr'][date('Y-m-d', strtotime($row['a_in_time']))] = "$short_hour";
        return  "<span class='time_difference'>".$short_hour."</span>";
    } 
}

$Ajax->activate('_page_body');
function id($row) {
    return ++$_SESSION['fun']['id'] ;
}
function datelist($row) {
	$datelist = date('Y-m-d',strtotime($row['a_in_time']));
	return $datelist ;
}
function datelist2($row) {
        if($row['a_out_time']){
            $datelist = date('Y-m-d',strtotime($row['a_out_time']));
        }else{
           $datelist = '---'; 
        }
	//$datelist = date('Y-m-d',strtotime($row['a_out_time']));
	return $datelist ;
}
function checking($row) {
	$date = date('H:i ',strtotime($row['a_in_time']));
	return $date ;
}
function checkout($row) {
        if($row['a_out_time']){
            $date = date('H:i',strtotime($row['a_out_time']));
        }else{
           $date = '---'; 
        }
	return $date ;
}
function empId($row) {
    $empl_id = $row['empl_id'];
    return $empl_id ;
} 
//-----------[PREVIOUS METHOD]---------------//
/*function working_hours($row){
    $logout = strtotime($row['a_out_time']);
    $login = strtotime($row['a_in_time']);
    $diff=$logout-$login;
    $h=round($diff/3600,1,PHP_ROUND_HALF_UP);
    $h_v=explode('.', $h);
    if($logout)
        $working_hours= $h_v[0]." Hours ".round(($diff / 60) % 60)."Minutes";
    else
        $working_hours = '----';
    return $working_hours ;
}*/
//---------------[END]---------------------//
function working_hours($row) {
    $logout = strtotime($row['a_out_time']);
    $login = strtotime($row['a_in_time']);
   
    $diff = $logout - $login;
    $h_v =  explode(':', timeDiffrence($row['a_out_time'], $row['a_in_time']));
  /* $h = round($diff / 3600, 1, PHP_ROUND_HALF_UP);
    $h_v = explode('.', $h);
    if ($logout) {
        $working_hours = $h_v[0] . " Hours " . round(($diff / 60) % 60) . " Minutes " . round(($diff) % 60) . " Second";
    } else {
        $working_hours = '----';
    }
    return $working_hours;
   * */
    if ($logout) {
        $working_hours = $h_v[0] . " Hours " . $h_v[1] . " Minutes " . $h_v[2]. " Second";
    } else {
        $working_hours = '----';
    }
    return $working_hours;
   
}


function totalhours(){
    
 $sql1=get_working_hours($_SESSION['wa_current_user']->empl_id,get_post('in_time'),get_post('monthly_val'));
    $val_hours = db_query($sql1);
    $total_hours = db_fetch_row($val_hours);
    $h=round($total_hours[0]/3600,1,PHP_ROUND_HALF_UP);
    $h_v=explode('.', $h);
    $userWorkhours= $h_v[0]." Hours ".round(($total_hours[0] / 60) % 60)." Minutes";
    
    $hour_val = $h_v[0];
    $minutes_val = round(($total_hours[0] / 60) % 60);
    
       $short_hr = $_POST['short_hr'];
       $shortage_hr = '00 Hours : 00 Minutes : 00 Seconds';
       $style = "font-size:.9em; padding:.3em; background:#bbee1a;";
       $style_full = "font-size:.9em; padding:.3em; background:#bbee1a; ";
    $hr_min_sec = explode(':',$_POST['total_work_hour']);
       if( !empty($short_hr)  ){
        $shortage_hr_arr = explode(':',$short_hr);
        $hour_val1 = $hour_val;
       
        $hour_val1-=$hr_min_sec[0];
         if($hour_val.':'.$minutes_val.':'.$seconds_val >= $_POST['total_work_hour']){
            
            $hour_min = false;
			if($minutes_val > 0){
                            $min_val1 = $hr_min_sec[1];
                             $min_val1= $min_val1 == 00?60:$min_val1;
			$minutes_val = abs((int)$min_val1 - $minutes_val); 
			$hour_min = true;
			}
		$hour_val1 = $hour_min == true?abs($hour_val1):abs($hour_val1);
		
        $shortage_hr = "$hour_val1"." Hours : {$minutes_val} Minutes ";
        $text = 'Extra';
        }
        else{
            
			$hour_min = false;
			if($minutes_val > 0){
                            $min_val1 = $hr_min_sec[1];
                             $min_val1= $min_val1 == 00?60:$min_val1;
			$minutes_val = (int)$min_val1 - $minutes_val; 
			$hour_min = true;
			}
                        
		if($seconds_val > 0){
			$hour_min = true;
                        $seconds_val1 = $hr_min_sec[2];
                        $seconds_val1= $seconds_val1 == 00?60:$seconds_val1;
			$seconds_val = ((int)$seconds_val1 - (int)$seconds_val);
		}
		$hour_val1 = $hour_min == true?abs($hour_val1) - 1:abs($hour_val1);
		
        $shortage_hr = $hour_val1." Hours : {$minutes_val} Minutes ";
        $text = 'Short';
         // $shortage_hr = "{$shortage_hr_arr[0]} Hours : {$shortage_hr_arr[1]} Minutes : {$shortage_hr_arr[2]} Seconds";
        }
   //---------------------[To change color or short attendance HOur]---------------------//
          if($hour_val.':'.$minutes_val.':'.$seconds_val < $_POST['total_work_hour']){
            $style = "font-size:.9em; padding:.3em; background:#eeca1d; ";
        }
        
    }
    
    
    
    
    echo '<div class="total-hours"><span> Total Working Hours: '.$userWorkhours.'</span><br><br>';
    
    
    echo'<span style="'.$style_full.'">Actual Working Hours: '. $hr_min_sec[0].' Hours :'. $hr_min_sec[1].' Minutes </span>' ; br(2);

     echo '<span style="'.$style.'"> '.$text.' Attendance Hours: '.$shortage_hr.'</span>';br(2);
        
        if( !empty($short_hr)){
            $id = getManualSalaryId1($_SESSION['wa_current_user']->empl_id, date('Y-m',strtotime(get_post('in_time'))));
     }
    
    echo '</div>';
    return $userWorkhours ;
    
}
start_form(true);
    if (isset($_GET['delete_id'])){} else{
            // display_warning(_(" Once you delete the Employee, The whole informations can be removed from the Database"));
    }
   
    display_rows();
       
    end_form();
end_page();
?>
<style>
.tablestyle_noborder tr {
	float: left;
	padding: 12px;
}
.label {
    background-color: #fff;
    color: black;
}

.exp_excel{
    font-size: 11px;
    border: 1px #ccc solid;
    background-image: url(images/footer_bg.png);
    background-repeat: repeat-x;
    padding: 5px 13px;
    float:right;
   background-color: #eee;
    margin-top: 10px;

}
.total-hours {
    float: right;
    font-size: 16px;
}

</style>
