<?php
function add_visitor($ref_id, $first_name, $last_name, $to_meet, $company, $coming_from, $purpose, $contact_number, $email, $tr_fromdate,$tr_ftime, $tr_todate,$tr_totime,$remarks){
     date_default_timezone_set('Asia/Kolkata');
        //display_error($date= date('H:i')) ;
    $tr_ftime1=date('H:i');
    $check_in=date('Y-m-d H:i', strtotime($tr_fromdate.' '.$tr_ftime1));
   // $check_out=date('Y-m-d H:i', strtotime($tr_todate.' '.$tr_totime));
    $check_out='---';
    $date=date('Y-m-d');
    $sql = "INSERT INTO " . TB_PREF . "visitor_management (ref_id,first_name,last_name,to_meet,company,coming_from,purpose,contact_number,email,tr_fromdate,remarks,inserted_date) VALUES (" . db_escape($ref_id) . "," . db_escape($first_name) . "," . db_escape($last_name) . "," . db_escape($to_meet) . "," . db_escape($company) . "," . db_escape($coming_from) . "," . db_escape($purpose) . "," . db_escape($contact_number) . "," . db_escape($email) . "," . db_escape($check_in) . "," . db_escape($remarks) ."," . db_escape($date) .")";
    
    //display_error($sql);
    db_query($sql, "The sales department could not be added");
}
function update_visitor($selected_id,$ref_id, $first_name, $last_name, $to_meet, $company, $coming_from, $purpose, $contact_number, $email, $tr_fromdate,$tr_ftime, $tr_todate,$tr_totime,$remarks){
   
   // $check_in=date('Y-m-d H:i', strtotime($tr_fromdate.' '.$tr_ftime));
    $check_in='---';
    date_default_timezone_set('Asia/Kolkata');
        //display_error($date= date('H:i')) ;
    $tr_totime1=date('H:i');
    $check_out=date('Y-m-d H:i', strtotime($tr_todate.' '.$tr_totime1));
  
    $sql = "UPDATE " . TB_PREF . "visitor_management  SET ref_id=" . db_escape($ref_id) . ",first_name=" . db_escape($first_name). ",last_name=" . db_escape($last_name) . ",to_meet=" . db_escape($to_meet) . ",company=" . db_escape($company) .",coming_from=" . db_escape($coming_from) .",purpose=" . db_escape($purpose) .",contact_number=" . db_escape($contact_number) .",email=" . db_escape($email) .",tr_todate=" . db_escape($check_out)  .",remarks=" . db_escape($remarks) ." WHERE vistitor_id = " . db_escape($selected_id);
     //display_error($tr_totime1);
    // display_error($tr_todate);
    db_query($sql, "The sales department could not be updated");
}
function getVisitorInfo(){
    $date=date('Y-m-d');
    $sql = "SELECT * from " . TB_PREF . "visitor_management Where inserted_date = " . db_escape($date)." ORDER BY tr_todate DESC";
    //display_error($sql); 
	return $sql ;db_query($sql, "could not get designation_master");
}
function get_visitor_id($selected_id) {
    $sql = "SELECT * FROM " . TB_PREF . "visitor_management WHERE vistitor_id=" . db_escape($selected_id);
    $result = db_query($sql, "could not get department");
      //display_error($sql); 
    return db_fetch($result);
}
function kv_get_visitor_list($first_name,$last_name,$to_meet,$coming_from,$company,$tr_fromdate,$tr_todate,$tr_ftime,$tr_totime){

	//display_error($status); die;
	
    $sql ="SELECT * FROM ".TB_PREF."visitor_management WHERE 1=1";
	$check_in=date('Y-m-d', strtotime($tr_fromdate));
        $check_out=date('Y-m-d', strtotime($tr_todate));
	if ($first_name) {
		$sql .= " AND first_name LIKE ". db_escape("%$first_name%");
	}
	if ($last_name) {
		$sql .= " AND last_name LIKE ". db_escape("%$last_name%");
	}
	if ($to_meet) {
		$sql .= " AND to_meet LIKE ". db_escape("%$to_meet%");
	}
	if ($coming_from) {
		$sql .= " AND coming_from=".db_escape($coming_from);
	}
        if ($check_in) {
		//$sql .= " GETDATE tr_fromdate=".db_escape($check_in);
           // $sql .= " AND date(tr_fromdate)";
            $sql .= " AND tr_fromdate LIKE". db_escape("%$check_in%");
	}
       /* if ($check_out) {
		$sql .= " GETDATE tr_todate=".db_escape($check_out);
	}*/
	//display_error($sql);
	return $sql;
}
function datetime_row_v($label, $name, $title, $check, $inc_days, $inc_months, $inc_years, $params, $submit_on_change,$disabled=false) {
    echo "<tr><td class='label'>$label</td>";
    datetime_cells_v(null, $name, $title, $check, $inc_days, $inc_months, $inc_years, $params, $submit_on_change,$disabled);

    echo "</tr>\n";
}
function datetime_cells_v($label, $name, $title = null, $check = null, $inc_days = 0, $inc_months = 0, $inc_years = 0, $params = null, $submit_on_change = false,$disabled=false) {
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
    if ($use_date_picker) {
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
    if(!$disabled){
    echo "<input type=\"text\" name=\"$name\" class=\"$class\" $aspect size=\"$size\" maxlength=\"12\" value=\""
    . $_POST[$name] . "\""
    . ($title ? " title='$title'" : '') . " > $post_label";
    if (isset($_POST['time_' . $name])) {
        $time1 = $_POST['time_' . $name];
    } else {
        $time1 = '12:00';
    }
    echo '<input type="time" name="time_' . $name . '" value="' . $time1 . '">';
    }
    else
    {
        $post_label = "";
       echo "<input type=\"text\"  class=\"$class\" $aspect size=\"$size\" maxlength=\"12\" disabled value=\""
    . $_POST[$name] . "\""
    . ($title ? " title='$title'" : '') . " > $post_label";
       echo "<input type=\"text\" name=\"$name\" style='display:none;' class=\"$class\" $aspect size=\"$size\" maxlength=\"12\"  value=\""
    . $_POST[$name] . "\""
    . ($title ? " title='$title'" : '') . " >";
    if (isset($_POST['time_' . $name])) {
        $time1 = $_POST['time_' . $name];
    } else {
        date_default_timezone_set('Asia/Kolkata');
        //display_error($date= date('H:i')) ;
        $date= date('H:i');
        $time1 = $date;
    }
    echo '<input type="time"  disabled value="' . $time1 . '">';  
    echo '<input type="time" name="time_' . $name . '" style="display:none;"  value="' . $time1 . '">';  
    }
    echo "</td>\n";
    $Ajax->addUpdate($name, $name, $_POST[$name]);
}
?>