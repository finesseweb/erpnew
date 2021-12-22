<?php

function add_attendance_admin_login($in_time, $empl_id){
    $current_date=date('Y-m-d');
    $out=explode(' ',$out_time);
    $in=explode(' ',$in_time);
   
    $sql = "INSERT INTO " . TB_PREF . "user_attendance (a_in_time,empl_id) VALUES (" . db_escape($in_time) . "," . db_escape($empl_id) . ")";
    if($in[0] <= $current_date){
     db_query($sql, "The sales department could not be added");
    }else {
       display_error('Please check your date time');
    }
   // display_error($sql);
   // db_query($sql, "The sales department could not be added");
}
function add_attendance_admin_user_login($in_time, $empl_id){
   $current_date=date('Y-m-d');
   $out=explode(' ',$out_time);
   $in=explode(' ',$in_time);
   
        $sql = "UPDATE " . TB_PREF . "users  SET in_time=" . db_escape($in_time) . " WHERE empl_id = " . db_escape($empl_id);
   
   if($in[0] <= $current_date){
       db_query($sql, "The sales department could not be added");
   }else {
       display_error('Please check your date time');
   }
    //db_query($sql, "The sales department could not be added");
}
function add_attendance_admin_logout($out_time,$in_time, $empl_id,$working_hours){
    $current_date=date('Y-m-d');
    $out=explode(' ',$out_time);
    $in=explode(' ',$in_time);
    
    $sql = "UPDATE " . TB_PREF . "user_attendance  SET a_out_time=" . db_escape($out_time) . ", working_hours=" . db_escape($working_hours) . "  WHERE empl_id = " . db_escape($empl_id)." AND a_in_time LIKE ". db_escape("%$out[0]%");

    if($out[0] <= $current_date){
      db_query($sql, "The sales department could not be added");
   }else {
       display_error('Please check your date time');
   }
    //display_error($sql);
   // db_query($sql, "The sales department could not be added");
}
function add_attendance_admin_user_logout($out_time,$in_time, $empl_id){
   $current_date=date('Y-m-d');
   $out=explode(' ',$out_time);
    
        $sql = "UPDATE " . TB_PREF . "users  SET out_time=" . db_escape($out_time) . " WHERE empl_id = " . db_escape($empl_id);
  
   if($out[0] <= $current_date){
       db_query($sql, "The sales department could not be added");
   }else {
       display_error('Please check your date time');
   }
   // db_query($sql, "The sales department could not be added");
}
function add_attendance_admin($in_time,$out_time, $empl_id,$working_hours){
    $current_date=date('Y-m-d');
    $out=explode(' ',$out_time);
    $In=explode(' ',$in_time);
   /* if($current_date == $out[0]){
    $sql = "INSERT INTO " . TB_PREF . "user_attendance (a_in_time,empl_id) VALUES (" . db_escape($in_time) . "," . db_escape($empl_id) . ")";
    }else {
        $sql = "INSERT INTO " . TB_PREF . "user_attendance (a_in_time,a_out_time,empl_id,working_hours) VALUES (" . db_escape($in_time) . "," . db_escape($out_time) . "," . db_escape($empl_id) . "," . db_escape($working_hours) . ")";
    }*/
    /*  $sql1 = "SELECT MAX(a_out_time) FROM " . TB_PREF . "user_attendance  WHERE empl_id = " . db_escape($empl_id)." ORDER BY a_out_time DESC";
      $val_t = db_query($sql1);
    $t = db_fetch_row($val_t);
   
    if($out_time == $t[0]){
        $sql = "UPDATE " . TB_PREF . "user_attendance  SET a_out_time=" . db_escape($out_time) . ", a_in_time=" . db_escape($in_time) . ", working_hours=" . db_escape($working_hours) . " WHERE empl_id = " . db_escape($empl_id)." AND a_out_time =" . db_escape($t[0]);
  
    }else{
        
        $sql = "INSERT INTO " . TB_PREF . "user_attendance (a_in_time,a_out_time,empl_id,working_hours) VALUES (" . db_escape($in_time) . "," . db_escape($out_time) . "," . db_escape($empl_id) . "," . db_escape($working_hours) . ")";
    }*/
    
   $sql = "INSERT INTO " . TB_PREF . "user_attendance (a_in_time,a_out_time,empl_id,working_hours) VALUES (" . db_escape($in_time) . "," . db_escape($out_time) . "," . db_escape($empl_id) . "," . db_escape($working_hours) . ")";
   
    if($In[0] <= $current_date && $out[0] <= $current_date){
         db_query($sql, "The sales department could not be updated");
     }else {
         display_error('Please check date and time');
     }
   // db_query($sql, "The sales department could not be added");
}
function add_attendance_admin_user($in_time,$out_time, $empl_id){
   $current_date=date('Y-m-d');
   $out=explode(' ',$out_time);
  /* if($current_date == $out[0]){
   $sql = "UPDATE " . TB_PREF . "users  SET in_time=" . db_escape($in_time) . " WHERE empl_id = " . db_escape($empl_id);
   }else {
       $sql = "UPDATE " . TB_PREF . "users  SET out_time=" . db_escape($out_time) . ",in_time=" . db_escape($in_time) . " WHERE empl_id = " . db_escape($empl_id);
   }*/
    $current_date=date('Y-m-d');
    $out=explode(' ',$out_time);
    $In=explode(' ',$in_time);
    $sql = "UPDATE " . TB_PREF . "users  SET out_time=" . db_escape($out_time) . ",in_time=" . db_escape($in_time) . " WHERE empl_id = " . db_escape($empl_id);
     //display_error($sql);
     if($In[0] <= $current_date && $out[0] <= $current_date){
         db_query($sql, "The sales department could not be updated");
     }else {
         display_error('Please check date and time');
     }
    
}
function add_userAttendance($in_time, $empl_id){
    $sql = "INSERT INTO " . TB_PREF . "user_attendance (a_in_time,empl_id) VALUES (" . db_escape($in_time) . "," . db_escape($empl_id) . ")";
    
    //display_error($sql);
    db_query($sql, "The sales department could not be added");
}
function update_Userattendance($out_time, $empl_id, $working_hours){
    $sql = "UPDATE " . TB_PREF . "user_attendance  SET a_out_time=" . db_escape($out_time) . ",working_hours=" . db_escape($working_hours) . " WHERE empl_id = " . db_escape($empl_id)." AND a_out_time IS NULL";
   // display_error($sql);
    db_query($sql, "The sales department could not be updated");
}
function fetchInTime($empl_id){
    $sql = "SELECT  a_in_time FROM " . TB_PREF . "user_attendance  WHERE empl_id = " . db_escape($empl_id)." AND a_out_time IS NULL";
    return $sql ;
    db_query($sql, "The sales department could not be updated");
}
function fetchInTimeUserTable($empl_id){
    $date=date('Y-m-d');
    $sql = "SELECT  in_time FROM " . TB_PREF . "users  WHERE empl_id = " . db_escape($empl_id)." AND in_time LIKE ". db_escape("%$date%");
   
    return $sql ;
    db_query($sql, "The sales department could not be updated");
    
}
function fetchInTimeUserTableOut($empl_id){
    $date=date('Y-m-d');
    $sql = "SELECT  in_time FROM " . TB_PREF . "users  WHERE empl_id = " . db_escape($empl_id)." AND in_time LIKE ". db_escape("%$date%");
    // display_error($sql);
    return $sql ;
    db_query($sql, "The sales department could not be updated");
}
function update_UserattendanceAdmin($workin_hours,$in_time,$out_time, $empl_id,$selected_id){
    $sql = "UPDATE " . TB_PREF . "user_attendance  SET a_out_time=" . db_escape($out_time) . ", a_in_time=" . db_escape($in_time) . ", working_hours=" . db_escape($workin_hours) . " WHERE w_id = " . db_escape($selected_id);
     //display_error($sql);
    db_query($sql, "The sales department could not be updated");
}


/*function Insert($table_name, $data){    
    $sql0 = "INSERT INTO ".TB_PREF.$table_name."(";
    $sql1 = " VALUES (";
    foreach($data as $key=>$value){
        $sql0 .= "`".$key."`,";
		if(is_array($value)) { 
			if($value[1] == 'date')				
				$sql1 .=  db_escape(date2sql($value[0])).",";
			if($value[1] == 'float')
				$sql1 .= $value.",";
		}else 
			$sql1 .= db_escape($value).",";
    }
    $sql0 = substr($sql0, 0, -1).")";
    $sql1 = substr($sql1, 0, -1).")";
    
//display_notification($sql0.$sql1);exit;
    
db_query($sql0.$sql1, "Could not insert data to table {$table_name}");
	return  db_insert_id();
}*/




function update_attendance($username,$password,$intime){
    
    //$IP_address=$_SESSION['IPaddress'];
    //$entered_by=$_SESSION['wa_current_user']->name;
   // $date=date('Y-m-d H:i:s');
    
    $sql = "UPDATE " . TB_PREF . "users  SET in_time=" . db_escape($intime) . ", out_time=NULL  WHERE user_id = " . db_escape($username)." AND password = ". db_escape($password);
   // display_error($sql);
    db_query($sql);
}
function logout_attendance($username,$password,$out_time){
    
    //$IP_address=$_SESSION['IPaddress'];
    //$entered_by=$_SESSION['wa_current_user']->name;
    $date=date('Y-m-d');
      $sql11 = "SELECT in_time FROM " . TB_PREF . "users  WHERE empl_id = " . db_escape($username)." AND in_time LIKE " . db_escape("%$date%");
      $val_t1 = db_query($sql11);
    $t1= db_fetch_row($val_t1);
    
    //display_error($sql11);
    
    $sql = "UPDATE " . TB_PREF . "users  SET out_time=" . db_escape($out_time) . " WHERE user_id = " . db_escape($username)." AND password = ". db_escape($password);
    
     //display_error($t1[0]);
     //db_query($sql, "The sales department could not be updated");
    if($t1[0]){
        db_query($sql, "The sales department could not be updated");
    }else {
        display_error('Please Contact with Administrator');
    }
}
function getAttendanceList($username){
    //$sql = "SELECT * from " . TB_PREF . "books";
   // return $sql ;db_query($sql, "could not get designation_master");
    if($_SESSION['wa_current_user']->empl_id){
        $empl_id=$_SESSION['wa_current_user']->empl_id;
    }else{
        $empl_id='';
    }
    $date=date('Y-m-d');
    $sql = "SELECT in_time,out_time from " . TB_PREF . "users WHERE empl_id = " . db_escape($empl_id)."AND in_time LIKE " . db_escape("%$date%");
    //display_error($sql);
    return $sql ;db_query($sql, "could not get designation_master");
}
function get_hours_list($status) {
    $sql = "SELECT * FROM " . TB_PREF . "user_workinghours where status=$status";
    
    //$result = db_query($sql, "could not get designation_master");
    return $sql;
}
function get_hours_list_Id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "user_workinghours WHERE id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
    //display_error($sql); 
    return db_fetch($result);
}
function update_workingHours($selected_id,$full_day,$half_day){
    $sql = "UPDATE " . TB_PREF . "user_workinghours  SET full_day=" . db_escape($full_day). " , half_day=" . db_escape($half_day). " WHERE id = " . db_escape($selected_id);
     //display_error($sql);
    // display_error($tr_todate);
    db_query($sql, "The sales department could not be updated");
}


function text_row_attendance($label, $name, $value, $size, $max, $title = null, $params = "", $post_label = "", $submit_on_change = false, $readonly = true) {
    echo "<tr><td class='label'>$label</td>";
    text_cells(null, $name, $value, $size, $max, $title, $params, $post_label, $submit_on_change, $readonly);

    echo "</tr>\n";
}
function submit_add_or_update_attendance($add = true, $title = false, $async = false, $clone = false) {
    $cancel = $async;

    if ($async === 'both') {
        $async = 'default';
        $cancel = 'cancel';
    } else if ($async === 'default')
        $cancel = true;
    else if ($async === 'cancel')
        $async = true;

    if ($add)
        submit('ADD_ITEM', _("LOGIN"), true, $title, $async);
   
}

function submit_add_or_update_center_attendance($add = true, $title = false, $async = false, $clone = false) {
    echo "<center>";
    submit_add_or_update_attendance($add, $title, $async, $clone);
    echo "</center>";
}
function submit_add_or_update_attendance34($add = true, $title = false, $async = false, $clone = false) {
    $cancel = $async;

    if ($async === 'both') {
        $async = 'default';
        $cancel = 'cancel';
    } else if ($async === 'default')
        $cancel = true;
    else if ($async === 'cancel')
        $async = true;

    
        submit('LOGIN_ITEM', _("Update"), true, _('Submit changes'), $async);
        submit('RESET', _("Cancel"), true, _('Cancel edition'), $cancel);
   
}

function submit_add_or_update_center_attendance34($add = true, $title = false, $async = false, $clone = false) {
    echo "<center>";
    submit_add_or_update_attendance34($add, $title, $async, $clone);
    echo "</center>";
}
function submit_add_or_update_attendance35($add = true, $title = false, $async = false, $clone = false) {
    $cancel = $async;

    if ($async === 'both') {
        $async = 'default';
        $cancel = 'cancel';
    } else if ($async === 'default')
        $cancel = true;
    else if ($async === 'cancel')
        $async = true;

    
        submit('LOGOUT_ITEM', _("Update"), true, _('Submit changes'), $async);
        submit('RESET', _("Cancel"), true, _('Cancel edition'), $cancel);
   
}

function submit_add_or_update_center_attendance35($add = true, $title = false, $async = false, $clone = false) {
    echo "<center>";
    submit_add_or_update_attendance35($add, $title, $async, $clone);
    echo "</center>";
}
function submit_add_or_update_attendance2($add = true, $title = false, $async = false, $clone = false) {
    $cancel = $async;

    if ($async === 'both') {
        $async = 'default';
        $cancel = 'cancel';
    } else if ($async === 'default')
        $cancel = true;
    else if ($async === 'cancel')
        $async = true;

    if ($add)
        submit('UPDATE_ITEM', _("LOGOUT"), true, $title, $async);
    
   
}

function submit_add_or_update_center_attendance2($add = true, $title = false, $async = false, $clone = false) {
    echo "<center>";
    submit_add_or_update_attendance2($add, $title, $async, $clone);
    echo "</center>";
}

function get_in_out_time_by_date($empl_id,$date){
    
    if ($empl_id && $date) {
        $sql = "SELECT date_format(min(a_in_time),'%H:%i:%s') as login,date_format(max(a_out_time),'%H:%i:%s') as logout,sum(working_hours) as totalworktime FROM ".TB_PREF."users JOIN ".TB_PREF."user_attendance ON ".TB_PREF."users.empl_id=".TB_PREF."user_attendance.empl_id";
		$sql .= " WHERE fa_user_attendance.empl_id = ". db_escape($empl_id);
                $sql .= " AND fa_user_attendance.a_in_time LIKE ". db_escape("%$date%");
	}
        
        $sql_query = db_query($sql);
       return db_fetch_row($sql_query);
}

function kv_get_attendance_list($empl_id,$in_time,$out_time,$working_hours, $monthly){
     $sql = "SELECT * FROM ".TB_PREF."users JOIN ".TB_PREF."user_attendance ON ".TB_PREF."users.empl_id=".TB_PREF."user_attendance.empl_id";
        
        $check_in=date('Y-m-d', strtotime($in_time));
        $check_out=date('Y-m-d', strtotime($out_time));
        $date=date('Y-m-d');
        $check_in12=date('Y-m', strtotime($in_time));
        $h=(strtotime($working_hours));
        if ($empl_id) {
		$sql .= " WHERE fa_user_attendance.empl_id = ". db_escape($empl_id);
	}
	if ($in_time && $monthly =='') {
                $sql .= " AND fa_user_attendance.a_in_time LIKE ". db_escape("%$check_in%");
	}if($in_time && $monthly ==1){
            $sql .= " AND fa_user_attendance.a_in_time LIKE ". db_escape("%$check_in12%");
        }
        $sql .= " ORDER BY ".TB_PREF."user_attendance.a_in_time DESC";
        
	/*if ($out_time) {
		$sql .= " AND fa_user_attendance.a_out_time LIKE ". db_escape("%$check_out%");
	}else {
            $sql .= " AND fa_user_attendance.a_out_time LIKE ". db_escape("%$date%");
        }*/
       /* if ($working_hours) {
		$sql .= ", DATE_SUB (fa_user_attendance.working_hours,INTERVAL 1 HOUR)";
	}*/
      /*  if ($working_hours) {
		$sql .= " AND fa_user_attendance.working_hours = ".$h;
	}*/
        

        
      //display_error($sql);
	return $sql;
	
}
function UserAttendanceList($in_time,$empl_id,$monthly){
    
    $sql = "SELECT * FROM ".TB_PREF."user_attendance WHERE empl_id =". db_escape($empl_id);
        
       $check_in=date('Y-m-d', strtotime($in_time));
       $check_inm=date('m', strtotime($in_time));
       $check_iny=date('Y', strtotime($in_time));
       $check_in12=date('Y-m', strtotime($in_time));
        $date=date('Y-m-d');
        
	/*if ($check_in) {
            $sql .= " AND a_in_time LIKE ".db_escape("%$check_in12%");
	}else{
            $sql .= " AND a_in_time LIKE ". db_escape("%$date%");
        }*/
        
	if ($in_time && $monthly =='') {
                $sql .= " AND a_in_time LIKE ". db_escape("%$check_in%");
	}if($in_time && $monthly ==1){
            $sql .= " AND a_in_time LIKE ". db_escape("%$check_in12%");
        }
        $sql .= " ORDER BY a_in_time DESC";
        //display_error($sql);
	return $sql;
	
}
function get_working_hours($empl_id,$in_time,$monthly_val){
    $check_in=date('Y-m-d',strtotime($in_time));
    $check_in12=date('Y-m', strtotime($in_time));
   
    if($monthly_val){
    $sql = "SELECT SUM(working_hours) FROM ".TB_PREF."user_attendance WHERE empl_id=". db_escape($empl_id)." "
            . "AND a_in_time LIKE". db_escape("%$check_in12%");
    }else{
        $sql = "SELECT SUM(working_hours) FROM ".TB_PREF."user_attendance WHERE empl_id=". db_escape($empl_id)." "
            . "AND a_in_time LIKE". db_escape("%$check_in%");
    }
        
    //display_error($sql);
    return $sql;
	
}
function get_working_hours12($empl_id,$in_time){
    $check_in=date('Y-m-d',strtotime($in_time));
    $check_in12=date('Y-m-d', strtotime($in_time));
   
    $sql = "SELECT SUM(working_hours) FROM ".TB_PREF."user_attendance WHERE empl_id=". db_escape($empl_id)." "
            . "AND a_in_time LIKE". db_escape("%$check_in12%");
        
  // display_error($sql);
    return $sql;
	
}
function admin_get_attendance_list($empl_id){
     $sql = "SELECT * FROM ".TB_PREF."users";
        
        if ($empl_id) {
		$sql .= " WHERE empl_id = ". db_escape($empl_id);
	}else{
            $sql .= " WHERE empl_id IS NOT NULL";
        }
	
	return $sql;
	
}
function get_defined_working_hours(){
     $sql = "SELECT full_day FROM ".TB_PREF."user_workinghours";
        
	return $sql;
	
}
function admin_get_attendance_list2($username){
    $sql = "SELECT empl_id FROM ".TB_PREF."users  WHERE user_id = ". db_escape($username);
    return $sql;
	
}
function kv_get_attendance_list2(){
    $sql = "SELECT * FROM ".TB_PREF."users";
    //display_error($sql);
    return $sql;
	
}
function kv_get_attendance_listLogin($empl_id){
    $sql = "SELECT a_out_time FROM ".TB_PREF."user_attendance WHERE empl_id=". db_escape($empl_id)." AND a_out_time IS NULL ";
    //display_error($sql);
    return $sql;
	
}
function kv_get_attendance_list_by_id($selected_id){
     $sql = "SELECT * FROM ".TB_PREF."users JOIN ".TB_PREF."user_attendance ON ".TB_PREF."users.empl_id=".TB_PREF."user_attendance.empl_id WHERE ".TB_PREF."user_attendance.w_id=". db_escape($selected_id);
        
    $result = db_query($sql, "could not get department");
    //display_error($sql); 
    return db_fetch($result);
	
}
function admin_get_attendance_list_by_id($selected_id){
     $sql = "SELECT * FROM ".TB_PREF."users WHERE empl_id=". db_escape($selected_id);
        
    $result = db_query($sql, "could not get department");
    //display_error($sql); 
    return db_fetch($result);
	
}
function datetime_cells_a($label, $name, $title = null, $check = null, $inc_days = 0, $inc_months = 0, $inc_years = 0, $params = null, $submit_on_change = false) {
    global $use_date_picker, $path_to_root, $Ajax;

    if (!isset($_POST[$name]) || $_POST[$name] == "") {
        if ($inc_years == 1001)
            $_POST[$name] = null;
        else {
            $dd = Today();
            if ($inc_days != 0)
                $dd = add_days($dd, $inc_days);
            if ($inc_months != 0)
                $dd = add_months($dd, $inc_months);
            if ($inc_years != 0)
                $dd = add_years($dd, $inc_years);
            $_POST[$name] = $dd;
        }
    }
    if (user_use_date_picker()) {
        $calc_image = (file_exists("$path_to_root/themes/" . user_theme() . "/images/cal.gif")) ?
                "$path_to_root/themes/" . user_theme() . "/images/cal.gif" : "$path_to_root/themes/default/images/cal.gif";
        $post_label = "<a tabindex='-1' href=\"javascript:date_picker(document.getElementsByName('$name')[0]);\">"
                . "	<img src='$calc_image' width='16' height='16' border='0' alt='" . _('Click Here to Pick up the date') . "'></a>\n";
    }
    else if ($use_date_picker) {
        $calc_image = (file_exists("$path_to_root/themes/" . user_theme() . "/images/cal.gif")) ?
                "$path_to_root/themes/" . user_theme() . "/images/cal.gif" : "$path_to_root/themes/default/images/cal.gif";
        $post_label = "<a tabindex='-1' href=\"javascript:date_picker(document.getElementsByName('$name')[0]);\">"
                . "	<img src='$calc_image' width='16' height='16' border='0' alt='" . _('Click Here to Pick up the date') . "'></a>\n";
    } else
        $post_label = "";

    if ($label != null)
        label_cell($label, $params);

    echo "<td>";

    $class = $submit_on_change ? 'date active' : 'date';

    $aspect = $check ? 'aspect="cdate"' : '';
    if ($check && (get_post($name) != Today()))
        $aspect .= ' style="color:#FF0000"';

    default_focus($name);
    $size = (user_date_format() > 3) ? 11 : 10;
    $attendace_val= explode(' ',$_POST[$name]);
    $att_date=date('m/d/Y', strtotime($attendace_val[0]));
    echo "<input type=\"text\" name=\"$name\" class=\"$class\" $aspect size=\"$size\" maxlength=\"12\" value=\""
    . $att_date . "\""
    . ($title ? " title='$title'" : '') . " > $post_label";
   
    /*if (isset($_POST['time_' . $name])) {
        $time1 = $_POST['time_' . $name];
    } else {
        $time1 = 'gfhhhhhh12:00';
    }*/
   
    echo '<input type="time" name="time_' . $name . '" value="' . $attendace_val[1] . '">';
    echo "</td>\n";
    $Ajax->addUpdate($name, $name, $_POST[$name]);
}

function datetime_row_a($label, $name, $title, $check, $inc_days, $inc_months, $inc_years, $params, $submit_on_change) {
    echo "<tr><td class='label'>$label</td>";
    datetime_cells_a(null, $name, $title, $check, $inc_days, $inc_months, $inc_years, $params, $submit_on_change);

    echo "</tr>\n";
}
function button_login($name, $value, $title = false, $icon = false, $aspect = '') {

    // php silently changes dots,spaces,'[' and characters 128-159
    // to underscore in POST names, to maintain compatibility with register_globals
    $rel = '';
    if ($aspect == 'selector') {
        $rel = " rel='$value'";
        $value = _("Select");
    }
    if (user_graphic_links() && $icon) {
        if ($value == _("Delete")) // Helper during implementation
            $icon = ICON_DELETE;
        return "<button type='submit' class='editbutton' name='"
                . htmlentities(strtr($name, array('.' => '=2E', '=' => '=3D', // ' '=>'=20','['=>'=5B'
                )))
                . "' value='1'" . ($title ? " title='$title'" : " title='$value'")
                . ($aspect ? " aspect='$aspect'" : '')
                . $rel
                . " >" . set_icon($icon) . "</button>\n";
    } else
        return "<input type='submit' class='editbutton' name='"
                . htmlentities(strtr($name, array('.' => '=2E', '=' => '=3D', // ' '=>'=20','['=>'=5B'
                )))
                . "' value='$value'"
                . ($title ? " title='$title'" : '')
                . ($aspect ? " aspect='$aspect'" : '')
                . $rel
                . "  >\n";
}
function button_cell_login($name, $value, $title = false, $icon = false, $aspect = '') {
    echo "<td align='center'>";
    echo button_login($name, $value, $title, $icon, $aspect);
    echo "</td>";
}
function login_button_cell($name, $value, $title = false) {
    button_cell_login($name, $value, $title, ICON_EDIT);
}
function button_cell2($name, $value, $title = false, $icon = false, $aspect = '') {
    echo "<td align='center' class='b_custome'>";
    echo button($name, $value, $title, $icon, $aspect);
    echo "</td>";
}
function edit2_button_cell($name, $value, $title = false) {
    button_cell2($name, $value, $title, ICON_EDIT);
}
function decimal_to_time($decimal) {
    $hours = (int)($decimal);
    $formin = $decimal-$hours;
    $minutes = (int)($formin * 60);
    $forsec = ($formin * 60) - $minutes;
    $seconds = round($forsec * 60);
 
    return str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT);
}
function time_to_seconds($timestr) {
$parts = explode(':', $timestr);
$seconds = ($parts[0] * 60 * 60) + ($parts[1] * 60) + $parts[2];
return $seconds;
}
?>