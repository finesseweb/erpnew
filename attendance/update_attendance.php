<?php
$page_security = 'SA_UPDATEATTENDANCE';
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
	if (user_use_date_picker())
		$js .= get_js_date_picker();
	page(("User Attendance Update"), @$_GET['popup'], false, "", $js);
}
//else {
//     
//    if ($SysPrefs->use_popup_windows)
//        $js .= get_js_open_window(900, 500);
//
//   if (user_use_date_picker()) 
//      
//     $js .= get_js_date_picker();
//}

simple_page_mode(true);

 //display_error($Mode);


if ($Mode == 'UPDATE_ITEM') {
        $update_pager = false;
        if ($Mode == 'UPDATE_ITEM') {
                $date= $_POST['in_time'] ;
                $time=$_POST['time_in_time'];
                $date1= $_POST['out_time'] ;
                $time1=$_POST['time_out_time'];
                $in_date_time=date('Y-m-d H:i:s', strtotime("$date $time"));
                $out_date_time=date('Y-m-d H:i:s', strtotime("$date1 $time1"));
                $Wor_In=strtotime("$date $time");
                $Wor_OUT=strtotime("$date1 $time1");
                $working_hours= $Wor_OUT-$Wor_In;
               
                $result = '';
                if($working_hours < 0){
                    display_error(_('Please check your LOGOUT TIME'));
                }else {
                    add_attendance_admin($in_date_time,$out_date_time,$_POST['empl_id'],$working_hours); 
                    add_attendance_admin_user($in_date_time,$out_date_time,$_POST['empl_id']); 
                }
                $_SESSION['login_attendance'] == 'FALSE';
                
                $Mode = 'RESET';
                $update_pager = true;
                display_notification(_('Record has been updated'));
        } 
        
	$Ajax->activate('details');
	
}
if ($Mode == 'LOGIN_ITEM') {
   
        $update_pager = false;
        if ($Mode == 'LOGIN_ITEM') {
                $date= $_POST['in_time'] ;
                $time=$_POST['time_in_time'];
                $in_date_time=date('Y-m-d H:i:s', strtotime("$date $time"));
                $result = '';
                add_attendance_admin_login($in_date_time,$_POST['empl_id']); 
                add_attendance_admin_user_login($in_date_time,$_POST['empl_id']); 
                $_SESSION['login_attendance'] == 'FALSE';
                $Mode = 'RESET';
                $update_pager = true;
                display_notification(_('Record has been updated'));
        } 
	$Ajax->activate('details');
	
}
if ($Mode == 'LOGOUT_ITEM') {
        $update_pager = false;
        if ($Mode == 'LOGOUT_ITEM') {
                $date1= $_POST['out_time'] ;
                $time1=$_POST['time_out_time'];
                $out_date_time=date('Y-m-d H:i:s', strtotime("$date1 $time1"));
                $out=explode(' ', $out_date_time);
                
                $sql1 = "SELECT MAX(a_in_time) FROM " . TB_PREF . "user_attendance  WHERE empl_id = " . db_escape($_POST['empl_id'])." AND a_in_time LIKE ".db_escape("%$out[0]%")." ORDER BY a_in_time DESC";
                
      $val_t = db_query($sql1);
    $t = db_fetch_row($val_t);
    $in_date_time=date('Y-m-d H:i:s', strtotime($t[0]));
                $Wor_In=strtotime($in_date_time);
                $Wor_OUT=strtotime("$date1 $time1");
                $working_hours= $Wor_OUT-$Wor_In;
                $result = '';
                
                add_attendance_admin_logout($out_date_time,$t[0],$_POST['empl_id'],$working_hours); 
                add_attendance_admin_user_logout($out_date_time,$t[0],$_POST['empl_id']); 
                $_SESSION['login_attendance'] == 'FALSE';
                $Mode = 'RESET';
                $update_pager = true;
                display_notification(_('Record has been updated'));
        } 
	$Ajax->activate('details');
	
}
function can_delete($selected_id) {
    if (key_in_foreign_table($selected_id, 'debtors_master', 'credit_status')) {
        display_error(_("Cannot delete this credit status because customer accounts have been created referring to it."));
        return false;
    }

    return true;
}

//-----------------------------------------------------------------------------------

if ($Mode == 'Delete') {
    delete_guest($selected_id);
    $Mode = 'RESET';
}

if ($Mode == 'RESET') {
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    $_POST['show_inactive'] = $sav;
}
//-----------------------------------------------------------------------------------
//===[Working]=====//

$ids = '';
if (get_post('Search'))
{ 
	$Ajax->activate('kv_empl_info');
}
//--------------------------------------------------------------------------------------
if (!isset($_POST['filterType']))
	$_POST['filterType'] = -1;
date_default_timezone_set('Asia/Kolkata');
start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
//ref_cells(_("Empl Id:"), 'empl_id', '',null, _('Enter Employee id fragment or leave empty'));
employee_list_cells(_("Select an Employee: "), 'empl_id', null, _('Select'), true, check_value('show_inactive'));
submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();

/////////////////////////////////////////User Attendance List/////////////////////////////
function display_rows(){
   
start_table(TABLESTYLE, "width=90%");
$th1 = array(_("Id"),_("Empl Id"), _("User Name"), _("Log In (Date &amp; Time)"), _("Log Out (Date &amp; Time)"), _("LOGIN"), _("LOGOUT"), _("EDIT"));
table_header($th1, 'style="width:75px;"');

$sql = admin_get_attendance_list(get_post('empl_id'));
$sql_query = db_query($sql);
    $i=1;
  
    
    while ($row = db_fetch($sql_query)) { 
  //print_r($row['out_time']);
       start_row();

        label_cell($i++);
        label_cell($row['empl_id']);
        
        if($row['real_name']){
            $real_name = $row['real_name'];
        }else{
             $real_name = $row['user_id'];
        }
        label_cell($real_name);
        
        label_cell(checking($row));
        
        label_cell(checkout($row));
        label_cell(login($row));
        label_cell(logout($row));
        label_cell(edit($row));
        end_row();   

    }
}


function id($row) {
    return ++$_SESSION['fun']['id'] ;
}

function checking($row) {
        if($row['in_time']){
            $date = date('Y-m-d H:i:s',strtotime($row['in_time']));
            
        }else {
            $date = '---'; 
        }
	return $date ;
}
function checkout($row) {
        if($row['out_time']){
            $date = date('Y-m-d H:i:s',strtotime($row['out_time']));
        }else{
           $date = '---'; 
        }
	//$date = date('Y-m-d h:i A',strtotime($row['tr_todate']));
	return $date ;
}
function empId($row) {
    $empl_id = $row['empl_id'];
	return $empl_id ;
} 
function realname($row) {
    if($row['real_name']){
        $real_name = $row['real_name'];
    }else{
         $real_name = $row['user_id'];
    }
	return $real_name ;
} 

function edit($row) {
    //if($row['out_time']==""){
        return edit2_button_cell("Edit" .$row['empl_id'], _("Edit"));
    //}
}
function login($row) {
   // if($row['in_time']=="" ||$row['in_time']=='0000-00-00 00:00:00'){
        return edit2_button_cell("Login" .$row['empl_id'], _("Login"));
    //}
}
function logout($row) {
   // if($row['out_time']=="" || $row['out_time']=='0000-00-00 00:00:00'){
        return edit2_button_cell("Logout" .$row['empl_id'], _("Logout"));
   // }
}

//----------------------------------------------------------------------------------------
	start_form(true);
        
	display_rows();
        start_table(TABLESTYLE2);
        if ($selected_id != -1) {
            if ($Mode == 'Edit') {
                       
            $myrow = admin_get_attendance_list_by_id($selected_id);
               
            $_POST['empl_id'] = $myrow['empl_id'];
            table_section_title(_("Update user attendance"));
            text_row(_('Empl ID'), 'empl_id', $_POST['empl_id'], 30,50);
            datetime_row_a(_("Log In (Date &amp; Time)*") . ":", 'in_time', 20, null, '', '', '', null, true,false);
            datetime_row_a(_("Log Out (Date &amp; Time)*") . ":", 'out_time', 20, null, '', '', '', null, true,false);
            hidden('w_id', $selected_id); 
            }
            if ($Mode == 'Login') {
                       
                $myrow = admin_get_attendance_list_by_id($selected_id);
                display_error($myrow['in_time']);
                $_POST['empl_id'] = $myrow['empl_id'];
                $_POST['in_time'] = $myrow['in_time'];
                table_section_title(_("LOGIN"));
                text_row(_('Empl ID'), 'empl_id', $_POST['empl_id'], 30);

                datetime_row_a(_("Log In (Date &amp; Time)*") . ":", 'in_time',$_POST['in_time'], 20, null, '', '', '', null, true,false);
               
                hidden('w_id', $selected_id); 
            }
            if ($Mode == 'Logout') {
                       
                $myrow = admin_get_attendance_list_by_id($selected_id);

                $_POST['empl_id'] = $myrow['empl_id'];
                $_POST['out_time'] = $myrow['out_time'];
                table_section_title(_("LOGOUT"));
                text_row(_('Empl ID'), 'empl_id', $_POST['empl_id'], 30);

                datetime_row_a(_("Log Out (Date &amp; Time)*") . ":", 'out_time',$_POST['out_time'], 20, null, '', '', '', null, true,false);
                hidden('w_id', $selected_id); 
            }
            
            hidden('selected_id', $selected_id);


        end_table(1);
        
            if($Mode == 'Login'){
                submit_add_or_update_center_attendance34($selected_id == -1, '', 'both');

            }elseif($Mode == 'Logout'){
                submit_add_or_update_center_attendance35($selected_id == -1, '', 'both');

            }elseif($Mode == 'Edit'){
                 submit_add_or_update_center($selected_id == -1, '', 'both');
            }
        }
        
end_form();
end_page();
?>

<style>
    .ex_tdc {
	display: none;
    }
    .b_custome + td {
      display: none;
    }
   
 </style>
 <script>
    $( document ).ready(function() {
       
        $('.b_custome').next('td').addClass("ex_tdc");
        $('.ex_tdc').css('display:none');
        
        
    });
</script>