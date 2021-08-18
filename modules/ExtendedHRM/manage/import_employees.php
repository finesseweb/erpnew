<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */

$page_security = 'HR_EMPL_BULK';
$path_to_root = "../../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/admin/db/attachments_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . '/company/0/spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
include_once($path_to_root . '/company/0/spreadsheet-reader-master/SpreadsheetReader.php');
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

if (isset($_GET['vw']))
    $view_id = $_GET['vw'];
else
    $view_id = find_submit('view');
if ($view_id != -1) {
    $row = get_employee_cv($view_id);
    if ($row['filename'] != "") {
        if (in_ajax()) {
            $Ajax->popup($_SERVER['PHP_SELF'] . '?vw=' . $view_id);
        } else {
            $type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';
            header("Content-type: " . $type);
            header('Content-Length: ' . $row['filesize']);
            //if ($type == 'application/octet-stream')
            //	header('Content-Disposition: attachment; filename='.$row['filename']);
            //else
            header("Content-Disposition: inline");
            echo file_get_contents(company_path() . "/attachments/emplcv/" . $row['unique_name']);
            exit();
        }
    }
}

if (isset($_GET['dl']))
    $download_id = $_GET['dl'];
else
    $download_id = find_submit('download');

if ($download_id != -1) {

    $row = get_employee_cvdwnld($download_id);

    if ($row['filename'] != "") {
        if (in_ajax()) {
            $Ajax->redirect($_SERVER['PHP_SELF'] . '?dl=' . $download_id);
        } else {
            if (ini_get('zlib.output_compression')) {//Off comression
                ini_set('zlib.output_compression', 'Off');
            }
            $ext = pathinfo($row['filename'], PATHINFO_EXTENSION); //get file extension
            //echo company_path()."/attachments/emplcv/".$row['empl_id']."/".$row['unique_name'].'.'.$doc_ext;exit;
            $fullPath = company_path() . "/attachments/emplcv/" . $row['empl_id'] . "/" . $row['unique_name'] . '.' . $ext;
            $fsize = filesize($fullPath);
            if (file_exists($fullPath)) {
                switch ($ext) {
                    case "pdf": $ctype = "application/pdf";
                        break;
                    case "exe": $ctype = "application/octet-stream";
                        break;
                    case "zip": $ctype = "application/zip";
                        break;
                    case "doc": $ctype = "application/msword";
                        break;
                    case "xls": $ctype = "application/vnd.ms-excel";
                        break;
                    case "ppt": $ctype = "application/vnd.ms-powerpoint";
                        break;
                    case "gif": $ctype = "image/gif";
                        break;
                    case "png": $ctype = "image/png";
                        break;
                    case "jpeg": $ctype = "image/jpg";
                        break;
                    case "jpg": $ctype = "image/jpg";
                        break;
                    default: $ctype = "application/force-download";
                }

                header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false); // required for certain browsers
                header("Content-Type: $ctype");
                header("Content-Disposition: attachment; filename=\"" . basename($fullPath) . "\";");
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: " . $fsize);
                ob_clean();
                flush();
                readfile($fullPath);
            }

            exit();
        }
    }
}

$version_id = get_company_prefs('version_id');

$js = '';
if ($version_id['version_id'] == '2.4.1') {
    if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);

    if (user_use_date_picker())
        $js .= get_js_date_picker();
}else {
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
}
page(_($help_context = "Bulk Import Facility for Employees"), false, false, "", $js);

simple_page_mode(true);

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));

//---------------------------------------------------------------------------------------
 $_POST['dynamic'] = 1;
if (isset($_POST['empl_id']) && !empty($_POST['empl_id']))
    $empl_id = $_POST['empl_id'];

//echo  $empl_id;
tableHeader();

if($Mode == 'UPDATE_ITEM'){
   
    
   
     $empl_arr = explode(',',$_POST['all_empl']);
     // echo "<pre>";print_r($_POST);
 for($i = 0; $i < count(array_unique($empl_arr))-1; $i++){
     
     add_employee($_POST[$empl_arr[$i].'empl_id'], $_POST[$empl_arr[$i].'empl_salutation'], $_POST[$empl_arr[$i].'salutation_text'], $_POST[$empl_arr[$i].'f_name'], $_POST[$empl_arr[$i].'m_name'], $_POST[$empl_arr[$i].'l_lastname'], $_POST[$empl_arr[$i].'addr_line1'], $_POST[$empl_arr[$i].'addr_line2'], $_POST[$empl_arr[$i].'correspondence_address'], $_POST[$empl_arr[$i].'permanent_address'], $_POST[$empl_arr[$i].'same_as_correspond_address'], $_POST[$empl_arr[$i].'home_phone'], $_POST[$empl_arr[$i].'mobile_phone'], $_POST[$empl_arr[$i].'email'], $_POST[$empl_arr[$i].'gender'], $_POST[$empl_arr[$i].'date_of_birth'], $_POST[$empl_arr[$i].'age'], $_POST[$empl_arr[$i].'pf_number'], $_POST[$empl_arr[$i].'pan_no'], base64_encode($_POST[$empl_arr[$i].'aadhaar_no']), $_POST[$empl_arr[$i].'esi_no'], $_POST[$empl_arr[$i].'pran_no'], $_POST[$empl_arr[$i].'marital_status'], $_POST[$empl_arr[$i].'no_of_children'], $_POST[$empl_arr[$i].'status'], $_POST[$empl_arr[$i].'empl_city'], $_POST[$empl_arr[$i].'empl_state'], $_POST[$empl_arr[$i].'pincode'], $_POST[$empl_arr[$i].'country'], $_POST[$empl_arr[$i].'per_addr_line1'], $_POST[$empl_arr[$i].'per_addr_line2'], $_POST[$empl_arr[$i].'empl_per_city'], $_POST[$empl_arr[$i].'per_country'], $_POST[$empl_arr[$i].'empl_per_state'], $_POST['per_pincode']);
            $kv_empl_id = $_POST[$empl_arr[$i].'empl_id'];
            //add_empl_department($kv_empl_id, $_POST['department']); 
            $jobs_arr = array('empl_id' => $_POST[$empl_arr[$i].'empl_id'],
               // 'grade' => $_POST['grade'],
                'department' => $_POST[$empl_arr[$i].'department'],
                'desig_group' => $_POST[$empl_arr[$i].'desig_group'],
                'employee_type' => $_POST[$empl_arr[$i].'employee_type'],
                'desig' => $_POST[$empl_arr[$i].'desig'],
                'joining' => array($_POST[$empl_arr[$i].'joining'], 'date'),
                'empl_type' => $_POST[$empl_arr[$i].'empl_type'],
                //'effective_date' => $_POST['effective_date'],
                //'contract_end_date' => $_POST['contract_end_date'],
                //'contract_duration' => $_POST['contract_duration'],
                'eligible_hra' => $_POST[$empl_arr[$i].'eligible_hra'],
                'eligible_esi' => $_POST[$empl_arr[$i].'eligible_esi'],
                'working_branch' => 1,
                'mod_of_pay' => $_POST[$empl_arr[$i].'mod_of_pay'],
                'ifsc_code' => $_POST[$empl_arr[$i].'ifsc_code'],
                'bank_name' => $_POST[$empl_arr[$i].'bank_name'],
                'act_holder_name' => $_POST[$empl_arr[$i].'act_holder_name'],
                'acc_no' => $_POST[$empl_arr[$i].'acc_no']);
            //display_error($jobs_arr); die;				
            $Allowance = get_allowances();
            $gross_Earnings = 0;
            while ($single = db_fetch($Allowance)) {
                if (isset($_POST[$empl_arr[$i].$single['id']]))
                    $jobs_arr[$single['id']] = $_POST[$empl_arr[$i].$single['id']];
                if ($single['type'] == 'Earnings')
                    $gross_Earnings += $_POST[$empl_arr[$i].$single['id']];
            }
            $jobs_arr['gross'] = $gross_Earnings;
            $jobs_arr['gross_pay_annum'] = $gross_Earnings * 12;
            $jobs_arr['1'] = $_POST[$empl_arr[$i].'1'];
            $jobs_arr['12'] = $_POST[$empl_arr[$i].'52'];
                         // $jobs_arr['country'] = get_country_code($_POST[$empl_arr[$i].'country'])?get_country_code($_POST[$empl_arr[$i].'country']):99;
       //    echo "<pre>";print_r($jobs_arr);
         Insert('kv_empl_job', $jobs_arr);
                
            $kv_empl_id = $_POST[$empl_id[$i].'empl_id'];
          if (db_has_empl_id()) {
            kv_update_next_empl_id_new($kv_empl_id);
            } else {
             kv_add_next_empl_id_new($kv_empl_id);
           }
     
 }
  meta_forward($path_to_root . '/modules/ExtendedHRM/manage/employeeswithctc.php','');
 
}




if ($Mode == 'ADD_ITEM') {

    if (!isset($max_image_size))
        $max_image_size = 50;
    $upload_file = "";
    //if (isset($_FILES['kv_attach_name']) && $_FILES['kv_attach_name']['name'] != '')
    //{
    //$empl_id = $selected_id  ;
    $result = $_FILES['kv_attach_name']['error'];
    $upload_file = 'Yes';
    $tmpname = $_FILES['kv_attach_name']['tmp_name'];

    /* $dir =  company_path()."/attachments/emplcv";
      if (!file_exists($dir))
      {
      mkdir ($dir,0777);
      $index_file = "<?php\nheader(\"Location: ../index.php\");\n?>";
      $fp = fopen($dir."/index.php", "w");
      fwrite($fp, $index_file);
      fclose($fp);
      } */

    $dir = company_path() . '/attachments/emplcv/' . $_POST['empl_id'];
    if (!file_exists($dir)) {
        mkdir($dir);
    }
    $filename = basename($_FILES['kv_attach_name']['name']);
    $filesize = $_FILES['kv_attach_name']['size'];
    $filetype = $_FILES['kv_attach_name']['type'];

    // file name compatible with POSIX
    // protect against directory traversal
    if ($Mode == 'UPDATE_ITEM') {
        $row = get_employee_cvdwnld($empl_id);
        if ($row['filename'] == "")
            exit();
        $unique_name = $row['unique_name'];
        if ($filename && file_exists($dir . "/" . $unique_name)) {
            unlink($dir . "/" . $unique_name);
            $unique_name = uniqid('');
        }
    } else
        $unique_name = uniqid('');

    //save the file
    //$doc_ext = substr(trim($_FILES['kv_attach_name']['name']), strlen($_FILES['kv_attach_name']['name']) - 3) ; 
    $doc_ext = pathinfo($_FILES['kv_attach_name']['name'], PATHINFO_EXTENSION);


    /*
      if($doc_ext == 'ocx' ) {
      $doc_ext = substr(trim($_FILES['kv_attach_name']['name']), strlen($_FILES['kv_attach_name']['name']) - 4) ;
      }
     * 
     */
    //$filename .= "/".empl_img_name($empl_id).'.'.$doc_ext;
    $kv_file_name = $_POST['empl_id'] . '.' . $doc_ext;

    if ($_FILES['kv_attach_name']['size'] > ($max_image_size * 1024)) { //File Size Check
        display_warning(_('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $max_image_size);
        $upload_file = 'No';
    } elseif (file_exists($filename)) {
        $result = unlink($filename);
        if (!$result) {
            display_error(_('The existing Document could not be removed'));
            $upload_file = 'No';
        }
    }

    if ((list($width, $height, $type, $attr) = getimagesize($_FILES['kv_attach_name']['tmp_name'])) !== false) {

        $imagetype = $type;
    } else
        $imagetype = false;


if ($doc_ext!='csv'){	
      display_warning( _('Only csv format can be uploaded'));
      $upload_file ='No';
      } 
  
    if ($upload_file == 'Yes') {
        if ($Mode == 'ADD_ITEM') {

            $result = move_uploaded_file($tmpname, $dir . "/" . $unique_name . '.' . $doc_ext);
            $Reader = new SpreadsheetReader($dir . "/" . $unique_name . '.' . $doc_ext);
            $x = array();
            $j = 0;
            foreach ($Reader as $Row) {
                $x[$j] = $Row;
                $j++;
            }
          
            foreach ($x as $key => $value) {
               // echo $key;
                if ($key > 1) {
                    $max_val = getMaxEmployeeId($key - 2);
                        if (strlen($max_val) == 1) {
                            $empl_id = 'EMP-F-00' . $max_val;
                        } else if (strlen($emp_inc_id) == 2) {
                            $empl_id = 'EMP-F-0' . $max_val;
                        } else {
                            $empl_id = 'EMP-F-' . $max_val;
                        }
                        display_rows($value,$empl_id);
                } else
                    continue;
            }
            $_POST['dynamic'] = 0;
            $selected_id = 1;
       //echo "<pre>"; print_r($x);
            display_notification(_("Employee Document has been attached!."));
        }
        else {

            //display_error($unique_name);die;
            /* 	if ($filename && file_exists($dir."/".$unique_name)){
              unlink($dir."/".$unique_name);
              $unique_name = uniqid('');
              } */
            //	display_error($dir);

            $result = move_uploaded_file($tmpname, $dir . "/" . $unique_name . '.' . $doc_ext);
            update_cv($selected_id, $_POST['empl_id'], $_POST['empl_firstname'], $_POST['cv_title'], $filename, $unique_name);
            $selected_id = -1;

            display_notification(_("Attachment has been updated."));
        }
    }

    // }
    $Ajax->activate('_page_body');
    //$Mode = 'RESET';		
}
tableFooter();
if ($Mode == 'Delete') {
    $row = get_employee_cvdwnld($selected_id);
    $dir = company_path() . "/attachments/emplcv/" . $row['filename'];
    if (file_exists($dir . "/" . $row['unique_name']))
        unlink($dir . "/" . $row['unique_name']);
    delete_cv($selected_id);
    display_notification(_("Employee Document has been deleted."));
    $Mode = 'RESET';
}

if ($Mode == 'RESET') {
    unset($_POST['empl_id']);
    unset($_POST['empl_firstname']);
    $selected_id = -1;
}

function viewing_controls() {
    global $selected_id;
    start_form(true);
    start_table(TABLESTYLE_NOBORDER);

    start_row();
    //employee_list_cells(_("Select an Employee: "), 'empl_id', null,	_('New Employee'), true, check_value('show_inactive'));
    if (list_updated('selected_id'))
        $selected_id = -1;

    end_row();
    end_table(1);
    end_form();
}

function edit_link($row) {
    return button('Edit' . $row["id"], _("Edit"), _("Edit"), ICON_EDIT);
    hidden('id', $row["id"]);
}

function view_link($row) {
    return button('view' . $row["id"], _("View"), _("View"), ICON_VIEW);
}

function download_link($row) {

    return button('download' . $row["id"], _("Download"), _("Download"), ICON_DOWN);
}

function delete_link($row) {
    submit_js_confirm("Delete" . $row["id"], sprintf(_("You are about to delete a Department Do you want to continue?"), $row["id"]));
    return button('Delete' . $row["id"], _("Delete"), _("Delete"), ICON_DELETE);
    hidden('id', $row["id"]);
}

function tableHeader(){
    start_table(TABLESTYLE);
$th = array( _("Employee Id"),_('Employee Name'), _("Actual CTC"),("Food Coupon"),("CTC(After Food coupon)"),("Basic Pay"),("HRA"),("Employee's ESI"),("Employer's ESI"),("Employer's EPF"),("Employee's EPF"),("Special Allowance"),'', '',);
//inactive_control_column($th);
table_header($th);

$k = 0;
}

function display_rows($data,$empl_id) {
    $_POST['all_empl'] .= $empl_id.',';
    alt_table_row_color($k);
    label_cell($empl_id);
    $_POST[$empl_id.'empl_id']=$empl_id;
    label_cell($data["1"].' '.$data[2].' '.$data[3]);
    $_POST[$empl_id.'f_name'] = $data[1];
    $_POST[$empl_id.'m_name'] = $data[2];
    $_POST[$empl_id.'l_name'] = $data[3];
    
    $_POST[$empl_id.'department'] = get_department_code($data[19]?ucfirst($data[19]):'Developer')?get_department_code($data[19]?ucfirst($data[19]):'Developer'):1;
    $_POST[$empl_id.'desig_group'] = get_desiggr_code($data[20]?ucfirst($data[20]):'PHP Developer')?get_desiggr_code($data[20]?ucfirst($data[20]):'PHP Developer'):1;
    $_POST[$empl_id.'employee_type'] = $data[24];
    $_POST[$empl_id.'desig'] = get_desig_code($data[21]?$data[21]:'web developer',$_POST[$empl_id.'desig_group']?$_POST[$empl_id.'desig_group']:1);
    $_POST[$empl_id.'joining'] = $data[22];
    $_POST[$empl_id.'empl_type'] = $data[23];
    $_POST[$empl_id.'ifsc_code'] = $data[37];
    $_POST[$empl_id.'bank_name'] = $data[35];
    $_POST[$empl_id.'acc_no'] = $data[38];
    $_POST[$empl_id.'act_holder_name'] = $data[36];
    
    
    $_POST[$empl_id.'country'] = get_country_code($data[7]?ucfirst($data[7]):'India')?get_country_code($data[7]?ucfirst($data[7]):'India'):99;
    
    $_POST[$empl_id.'per_country'] = get_country_code($data[13]?ucfirst($data[13]):'India')?get_country_code($data[13]?ucfirst($data[13]):'India'):99;
    
    $_POST[$empl_id.'empl_state'] = get_state_code($data[8]?$data[8]:'Bihar',$_POST[$empl_id.'country']?$_POST[$empl_id.'country']:'India')?get_state_code($data[8]?$data[8]:'Bihar',$_POST[$empl_id.'country']?$_POST[$empl_id.'country']:'India'):1479;
    
    $_POST[$empl_id.'empl_city'] = $data[6];
    
    $_POST[$empl_id.'empl_per_city'] = $data[12];
    
    $_POST[$empl_id.'empl_per_state'] = get_state_code($data[14]?$data[14]:'Bihar',$_POST[$empl_id.'per_country']?$_POST[$empl_id.'per_country']:'India')?get_state_code($data[14]?$data[14]:'Bihar',$_POST[$empl_id.'country']?$_POST[$empl_id.'country']:'India'):1479;
    
    $_POST[$empl_id.'pincode'] = $data[9];
    
    $_POST[$empl_id.'per_pincode'] = $data[15];
    
    $_POST[$empl_id.'gender'] = 1;
    
    $_POST[$empl_id.'pf_number'] = $data[28];
    
    $_POST[$empl_id.'pan_no'] = $data[29];
    
    $_POST[$empl_id.'esi_no'] = $data[30];
    
    $_POST[$empl_id.'pran_no'] = $data[31];
    
    $_POST[$empl_id.'aadhaar_no'] = '';
    $_POST[$empl_id.'status'] = 1;
    
    
    
    $_POST[$empl_id.'addr_line1'] = $data[4];
    $_POST[$empl_id.'addr_line2'] = $data[5];
    $_POST[$empl_id.'per_addr_line1'] = $data[10];
    $_POST[$empl_id.'per_addr_line2'] = $data[11];
    
    $_POST[$empl_id.'email'] = $data[18];
    $_POST[$empl_id.'mobile_phone'] = $data[17];
    $_POST[$empl_id.'emergency_phone'] = $data[16];
    
    $_POST[$empl_id.'empl_salutation'] = '6';
    
    $_POST[$empl_id.'salutation_text'] = $data[0];
    
    $_POST[$empl_id.'marital_status'] = 1;
    
    $_POST[$empl_id.'date_of_birth'] = $data[27];
    
    
    $_POST[$empl_id.'age'] = getAge($data[27]);
    
    
    
    
    label_cell($data["33"]);
  //  hidden(33, $data[33]);
    label_cell($data[34]);
    $_POST[$empl_id.'40'] = $data[34];
    $res = calculateSalary($data["33"], $data["34"],$data["32"]);
    if(strtolower($data[32]) == 'yes')
    $_POST[$empl_id.'eligible_esi'] = 1 ;
    else
    $_POST[$empl_id.'eligible_esi'] = 2 ;   
    
    $_POST[$empl_id.'eligible_hra'] = 1 ;   
    
    label_cell($res[39]);
    $_POST[$empl_id.'39'] = $res[39];
    label_cell($res[1]);
    $_POST[$empl_id.'1'] = $res[1];
    label_cell($res[4]);
    $_POST[$empl_id.'4'] = $res[4];
    label_cell($res[41]);
    $_POST[$empl_id.'41'] = $res[41];
    label_cell($res[49]);
    $_POST[$empl_id.'49'] = $res[49];
    label_cell($res[52]);
    $_POST[$empl_id.'52'] = $res[52];
    label_cell($res[52]);
    $_POST[$empl_id.'12'] = $res[52];
    $_POST[$empl_id.'51'] = $res[52];
    label_cell($res[47]);
    $_POST[$empl_id.'47'] = $res[47];
    inactive_control_cell($myrow["leave_id"], $myrow["inactive"], 'kv_leave_master', 'leave_id');
   // edit_button_cell("Edit" . $myrow['leave_id'], _("Edit"));
    //delete_button_cell("Delete".$myrow['leave_id'], _("Delete"));
    //submit_js_confirm("Delete".$myrow["leave_id"], sprintf(_("You are about to delete a Leave Master Do you want to continue?"), $myrow['leave_id']));
    end_row();



}
//echo "<pre>";print_r($_SESSION);

function calculateSalary($act_ctc, $food_coupon,$eligible_esi='yes',$eligible_hra=1){

    
    
     $food_coup = $food_coupon;
    $conveyance = 0;
    $salary_advance = 0;
    $medical = 0;
    $incentive = 0;

    $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{};'?<>]/";

    /*if (preg_match($regex, get_post($basic_id)) != 0) {
        $input_err = 1;
        display_error(_("Only numerical values are allowed."));
        set_focus($basic_id);
    } else {
        $input_err = 0;
    }*/

   // $month = get_post($basic_id);


    $EarAllowance = get_allowances('Earnings');
    $DedAllowance = get_allowances('Deductions');
    $basic_id = kv_get_basic();
    $ctc = $act_ctc - $food_coup;
    $epf_percent = 0;
    $basic_percent = 0;
    while ($single = db_fetch($EarAllowance)) {
        if ($single['id'] == 1) {
            $basic_percent = $single['percentage'];
        }
    }

    while ($single = db_fetch($DedAllowance)) {

        if ($single['id'] == 12) {

            $epf_percent = $single['percentage'];
        }
    }



    $EarAllowance = get_allowances('Earnings');
    $DedAllowance = get_allowances('Deductions');
    while ($single = db_fetch($EarAllowance)) {


        if (($single['value'] == 'Percentage') && ($single['percentage'] > 0)) {

            $sql = "SELECT id FROM fa_kv_allowances WHERE type='Earnings' AND value='Amount' ";
            $res = db_query($sql, 'Return Allowances');
            $basic_amount = 0;


            while ($result = db_fetch($res)) {
                if ($ctc != 0) {
                    $data[39] = $ctc;
                    
                    $basic_amount = (round($ctc) * ($basic_percent / 100));
                    
                    if ($basic_amount >= 15000)
                        $basic_for_pf = 15000;
                    else
                        $basic_for_pf = $basic_amount;
                    $epf = (round($basic_for_pf) * ($epf_percent / 100));
                    $data[1] = $basic_amount;
                    if ($result['id'] != 8 && $result['id'] != 39 && $result['id'] != 2) {//Excluded Conveyance Allowence from calculation
                        $basic_amount = $basic_amount; //+ get_post($result['id']);

                        $_POST[$result['id']] = get_post($result['id']);

                      //  $Ajax->activate($result['id']);
                        $default_value = round(($basic_amount) * ($single['percentage'] / 100));
                    } else{

                        if (strtolower($eligible_esi) == 'yes')
                            $data[41] = round(($ctc - $epf) * 4.75 / 100);
                        else
                            $data[41] = 0;
                     //   $Ajax->activate($result['id']);
                    }
                    
                }
            }
        } else
            $default_value = null;

  
        if (($single['id'] != $basic_id) && ($single['basic'] != '1') && ($single['id'] != '2')) {

            if (($single['id'] == 4) && ($eligible_hra == 2)) {
                $data['4'] = 0;
                //$Ajax->activate($_POST['4']);
            }else if ($single['id'] == 8) {
                $data[$single['id']] = $data['8'];
                //$Ajax->activate($_POST[$single['id']]);
            } else if ($single['id'] == 39) {
                $data[$single['id']] = $ctc;
               // $Ajax->activate($_POST[$single['id']]);
            } else if ($single['id'] == 42) {

                $data[$single['id']] = $epf;

               // $Ajax->activate($_POST[$single['id']]);
            }else if ($single['id'] == 52) {
                        $data[$single['id']] = round($epf);
                     //   $Ajax->activate($_POST[$single['id']]);
                    } else if ($single['id'] == 41) {
               // $Ajax->activate($_POST[$single['id']]);
            } else {
                $data[$single['id']] = $default_value;
              //  $Ajax->activate($_POST[$single['id']]);
            }
        }
    }
    $prof_tax = kv_get_Tax_allowance();
  
    while ($single = db_fetch($DedAllowance)) {
        $tot_sal = get_post('1');

        if ($tot_sal >= 15000)
            $newpf = 15000;
        else
            $newpf = $tot_sal;

        if ($single['id'] == 12) {
            $default_value = round(($newpf) * ($single['percentage'] / 100));
        } else if ($single['value'] == 'Percentage' && $single['percentage'] > 0) {
            //display_error($basic_amount);

            $default_value = round(($tot_sal) * ($single['percentage'] / 100));
        } else if ($single['id'] == 43) {
           if (strtolower($eligible_esi) == 'yes')
                $default_value = round(($ctc - $epf) * 4.75 / 100);
            else
                $default_value = 0;
            $data[103] = $default_value;
        }
        else if ($single['id'] == 49) {
           if (strtolower($eligible_esi) == 'yes')
                $default_value = round(($ctc - $epf) * 1.75 / 100);
            else
                $default_value = 0;
        }
        else if ($single['id'] == 50) {
            $default_value = $data[$single['id']];
        } else if ($single['id'] == 51) {
            $default_value = $data[$single['id']];
        } else {

            $default_value = null;
        }
      

        //if($single['id'] != $prof_tax){//Commented for showing PF value in employee add, Date - 14-Dec-2017
        $data[$single['id']] = $default_value;

        $data[40] = $food_coup;
        $data[8] = $conveyance;
        $data[44] = $salary_advance;
        $data[45] = $medical;
        $data[46] = $incentive;


        $data[50] = $medical;
        $data[51] = $food_coup;


        $special_allowance = $data[39] - ($data[1] + $data[4] + $data[8] + $data[40] + $data[41] + $data[44] + $data[45] + $data[46] +$data[52]);
        $data['47'] = round($special_allowance);

        //}	  
}
//echo "<pre>"; print_r($data);
 return $data;
}




function tableFooter(){
  // inactive_control_row($th);
end_table(); 
}

//---------------------------------------------------------------------------------------



viewing_controls();


start_form(true);

//display_rows($empl_id);
///hidden('empl_id', $empl_id);

 $empl_arr = explode(',',$_POST['all_empl']);
// echo"<pre>";print_r(array_unique($empl_arr));
 hidden('all_empl',$_POST['all_empl'] );
 for($i = 0; $i < count(array_unique($empl_arr))-1; $i++){
     hidden ($empl_arr[$i].'empl_id', $_POST[$empl_arr[$i].'empl_id']);
     hidden ($empl_arr[$i].'f_name', $_POST[$empl_arr[$i].'f_name']);
     hidden ($empl_arr[$i].'m_name', $_POST[$empl_arr[$i].'m_name']);
     hidden ($empl_arr[$i].'l_name', $_POST[$empl_arr[$i].'l_name']);
     hidden ($empl_arr[$i].'40', $_POST[$empl_arr[$i].'40']);
     hidden ($empl_arr[$i].'eligible_esi', $_POST[$empl_arr[$i].'eligible_esi']);
     hidden ($empl_arr[$i].'eligible_hra', $_POST[$empl_arr[$i].'eligible_hra']);
     hidden ($empl_arr[$i].'39', $_POST[$empl_arr[$i].'39']);
     hidden ($empl_arr[$i].'1', $_POST[$empl_arr[$i].'1']);
     hidden ($empl_arr[$i].'4', $_POST[$empl_arr[$i].'4']);
     hidden ($empl_arr[$i].'49', $_POST[$empl_arr[$i].'49']);
     hidden ($empl_arr[$i].'41', $_POST[$empl_arr[$i].'41']);
     hidden ($empl_arr[$i].'52', $_POST[$empl_arr[$i].'52']);
     hidden ($empl_arr[$i].'12', $_POST[$empl_arr[$i].'12']);
     hidden ($empl_arr[$i].'47', $_POST[$empl_arr[$i].'47']);
     hidden ($empl_arr[$i].'51', $_POST[$empl_arr[$i].'51']);
     hidden ($empl_arr[$i].'44', $_POST[$empl_arr[$i].'44']);
     hidden ($empl_arr[$i].'45', $_POST[$empl_arr[$i].'45']);
     hidden ($empl_arr[$i].'46', $_POST[$empl_arr[$i].'46']);
     hidden ($empl_arr[$i].'50', $_POST[$empl_arr[$i].'50']);
     
     
     hidden ($empl_arr[$i].'department', $_POST[$empl_arr[$i].'department']);
     hidden ($empl_arr[$i].'desig_group', $_POST[$empl_arr[$i].'desig_group']);
     hidden ($empl_arr[$i].'employee_type', $_POST[$empl_arr[$i].'employee_type']);
     hidden ($empl_arr[$i].'desig', $_POST[$empl_arr[$i].'desig']);
     hidden ($empl_arr[$i].'joining', $_POST[$empl_arr[$i].'joining']);
     hidden ($empl_arr[$i].'empl_type', $_POST[$empl_arr[$i].'empl_type']);
     hidden ($empl_arr[$i].'ifsc_code', $_POST[$empl_arr[$i].'ifsc_code']);
     hidden ($empl_arr[$i].'bank_name', $_POST[$empl_arr[$i].'bank_name']);
     hidden ($empl_arr[$i].'acc_no', $_POST[$empl_arr[$i].'acc_no']);
     hidden ($empl_arr[$i].'act_holder_name', $_POST[$empl_arr[$i].'act_holder_name']); 
     
     
     
     hidden ($empl_arr[$i].'country', $_POST[$empl_arr[$i].'country']); 
     hidden ($empl_arr[$i].'per_country', $_POST[$empl_arr[$i].'per_country']); 
     hidden ($empl_arr[$i].'empl_state', $_POST[$empl_arr[$i].'empl_state']); 
     hidden ($empl_arr[$i].'empl_city', $_POST[$empl_arr[$i].'empl_city']); 
     hidden ($empl_arr[$i].'empl_per_city', $_POST[$empl_arr[$i].'empl_per_city']); 
     hidden ($empl_arr[$i].'empl_per_state', $_POST[$empl_arr[$i].'empl_per_state']); 
     hidden ($empl_arr[$i].'pincode', $_POST[$empl_arr[$i].'pincode']); 
     hidden ($empl_arr[$i].'per_pincode', $_POST[$empl_arr[$i].'per_pincode']); 
     hidden ($empl_arr[$i].'gender', $_POST[$empl_arr[$i].'gender']); 
     hidden ($empl_arr[$i].'pf_number', $_POST[$empl_arr[$i].'pf_number']); 
     hidden ($empl_arr[$i].'pan_no', $_POST[$empl_arr[$i].'pan_no']); 
     hidden ($empl_arr[$i].'esi_no', $_POST[$empl_arr[$i].'esi_no']); 
     hidden ($empl_arr[$i].'pran_no', $_POST[$empl_arr[$i].'pran_no']); 
     hidden ($empl_arr[$i].'addr_line1', $_POST[$empl_arr[$i].'addr_line1']); 
     hidden ($empl_arr[$i].'addr_line2', $_POST[$empl_arr[$i].'addr_line2']); 
     hidden ($empl_arr[$i].'per_addr_line1', $_POST[$empl_arr[$i].'per_addr_line1']); 
     hidden ($empl_arr[$i].'per_addr_line2', $_POST[$empl_arr[$i].'per_addr_line2']); 
     hidden ($empl_arr[$i].'email', $_POST[$empl_arr[$i].'email']); 
     hidden ($empl_arr[$i].'mobile_phone', $_POST[$empl_arr[$i].'mobile_phone']); 
     hidden ($empl_arr[$i].'emergency_phone', $_POST[$empl_arr[$i].'emergency_phone']); 
     hidden ($empl_arr[$i].'empl_salutation', $_POST[$empl_arr[$i].'empl_salutation']); 
     hidden ($empl_arr[$i].'salutation_text', $_POST[$empl_arr[$i].'salutation_text']); 
     hidden ($empl_arr[$i].'marital_status', $_POST[$empl_arr[$i].'marital_status']); 
     hidden ($empl_arr[$i].'date_of_birth', $_POST[$empl_arr[$i].'date_of_birth']); 
     hidden ($empl_arr[$i].'age', $_POST[$empl_arr[$i].'age']);  
     hidden ($empl_arr[$i].'aadhaar_no', $_POST[$empl_arr[$i].'aadhaar_no']);  
     hidden ($empl_arr[$i].'status', $_POST[$empl_arr[$i].'status']);  
      
 }
    

br(2);

start_table(TABLESTYLE2);
if($_POST['dynamic'] == 1){

kv_doc_row(_("Upload Document") . ":", 'kv_attach_name', 'kv_attach_name');
}
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'process');

end_form();

function getMaxEmployeeId($inc) {
    //create employee id by according to faculty type;

    $sql1 = "SELECT MAX(empl_id) FROM fa_kv_empl_info WHERE empl_id LIKE 'Emp-f%'";

    $empl_data = db_query($sql1);
    $empid_result = db_fetch_row($empl_data);

    //$last_max_emp_id = $empid_result[0];
    $last_emp1 = substr($empid_result[0], 6);

    return $emp_inc_id = $last_emp1 + $inc + 1;
}


function getAge($birth_date){
    
$d1 = new DateTime($birth_date);
$d2 = new DateTime(date('Y-m-d'));

$diff = $d2->diff($d1);

return $diff->y;
}

end_page();
?>