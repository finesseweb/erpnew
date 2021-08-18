<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */

$page_security = 'SA_STATUTORY_REPORT';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");

//page(_($help_context = "statutory")); 
include_once($path_to_root . "/admin/db/attachments_db.inc");
include($path_to_root . "/statutory/includes/db/statutory_db.inc");
include($path_to_root . "/statutory/includes/db/nameReturn_db.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");

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
            $dir = company_path() . '/statutory/' . $_POST['statutory'] . $_POST['return_name'] . $_POST['freq_name'] . $_POST['year'];
            echo file_get_contents($dir . $row['unique_name']);
            exit();
        }
    }
}

 $empl_id = $_SESSION['wa_current_user']->empl_id;
if (isset($_GET['dl']))
    $download_id = $_GET['dl'];
else
    $download_id = find_submit('download');
if ($download_id != -1) {

    $row = get_all_statutory_upload_by_id($download_id);

    if ($row['file_name'] != "") {
        if (in_ajax()) {
            $Ajax->redirect($_SERVER['PHP_SELF'] . '?dl=' . $download_id);
        } else {
            if (ini_get('zlib.output_compression')) {//Off comression
                ini_set('zlib.output_compression', 'Off');
            }
            $ext = pathinfo($row['filename'], PATHINFO_EXTENSION); //get file extension
            //echo company_path()."/attachments/emplcv/".$row['empl_id']."/".$row['unique_name'].'.'.$doc_ext;exit;
              $dir = company_path() . '/statutory/' . $_POST['statutory'] . $_POST['return_name'] . $_POST['freq_name'] . $_POST['year'];
            $fullPath = $dir. $row['unique_name'] . '.' . $ext;
           // display_error($fullPath);
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
page(_($help_context = "Statutory Report"), false, false, "", $js);

simple_page_mode(true);

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));

//---------------------------------------------------------------------------------------

//--------------------message alert ---------------------------

$result = get_all_matser();

while ($myrow = db_fetch($result)) {
     $freq_days = getFrequencyDays($myrow["freq_id"]);
     $NewDate = $myrow['due_date'];
     $day = 0;
    if(date('Y-m-d') >= $myrow['due_date'])
    {$NewDate = date('Y-m-d', strtotime(date('Y-m-d') . " +$freq_days days"));
       $date_diff =  getDaysDiff($NewDate, $myrow['effective_date']);
           $day = getDays($myrow['effective_date']);
       if(($date_diff % $freq_days)==0){
           update_statutory_master_date($myrow['id'],$NewDate);
       }
         if ($day <= $myrow['remider_days']){
        $statutory = get_statutory_name(1, $myrow["statutory_id"]);
        $return = get_all_return_name(1, $myrow["return_id"]);
        $frequency = get_frequency_name(1, $myrow["freq_id"]);
       // display_warning($day . " days left to complete $statutory  $return $frequency");
    }
    }
     //display_error($NewDate."===".$day.'====='.$date_diff.'==='.$date_diff / $freq_days.'==='.$date_diff % $freq_days);
    // display_warning($myrow['remider_days']);
}
///----------------------------program start --------------------




$_POST['dynamic'] = 1;
if (isset($_POST['empl_id']) && !empty($_POST['empl_id']))
    $empl_id = $_POST['empl_id'];

//echo  $empl_id;

if ($Mode == 'UPDATE_ITEM') {
    $last_id = update_statutory_main($selected_id,$_POST['statutory'], $_POST['return_name'], $_POST['freq_name'], $_POST['description'], $_POST['year']);
    
    
    
    
    
        if (!isset($max_image_size))
        $max_image_size = 50;
    $upload_file = "";


    $dir = company_path() . '/statutory' ;

    if (!file_exists($dir)) {
        mkdir($dir, 0777, TRUE);
    }
    if ($selected_id) {
      
        for ($i = 0; $i < $_POST['upload']; $i++) {
            $result = $_FILES["kv_attach_name$i"]['error'];
            $upload_file = 'Yes';
            $tmpname = $_FILES["kv_attach_name$i"]['tmp_name'];
            $filename = basename($_FILES["kv_attach_name$i"]['name']);
            $filesize = $_FILES["kv_attach_name$i"]['size'];
            $filetype = $_FILES["kv_attach_name$i"]['type'];

            // file name compatible with POSIX
            // protect against directory traversal
           
                $unique_name = uniqid('');

            $doc_ext = pathinfo($_FILES["kv_attach_name$i"]['name'], PATHINFO_EXTENSION);


            if ($_FILES["kv_attach_name$i"]['size'] > ($max_image_size * 1024)) { //File Size Check
                display_warning(_('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $max_image_size);
               // $upload_file = 'No';
                continue;
            } elseif (file_exists($filename)) {
                $result = unlink($filename);
                if (!$result) {
                    display_error(_('The existing Document could not be removed'));
                    continue;
                 //   $upload_file = 'No';
                }
            }

            if ((list($width, $height, $type, $attr) = getimagesize($_FILES["kv_attach_name$i"]['tmp_name'])) !== false) {

                $imagetype = $type;
            } else
                $imagetype = false;

//echo $doc_ext;
           if ($doc_ext!='png' && $doc_ext!='jpg' && $doc_ext!='doc' && $doc_ext!='docx'){	
             // display_warning( _('Only .png, .doc, .png, .jpg format are uploaded'));
                continue;
//$upload_file ='No';
              } 

                    $result = move_uploaded_file($tmpname, $dir . "/" . $unique_name . '.' . $doc_ext);
                      add_upload($selected_id, $_POST['title'], $filename, $unique_name);
                    display_notification(_("Employee Document has been updated!."));
               
            }
       display_notification(_("Employee Document has been updated!."));
    }
    $Mode = 'RESET';
     $_POST['upload'] =1;
}
function canprocess(){          
    
                 
}
function isDuplicate() {
    $row_count = hasStatutory($_POST['statutory'], $_POST['return_name'], $_POST['freq_name'], $_POST['year']); 
    if ($row_count > 0)
        return false;
    return true;
}

function isAssigned1(){
        $row_count = isAssigned($_POST['statutory'], $_POST['return_name'], $_POST['freq_name']); 
    if ($row_count > 0)
        return true;
    return false;
    
}





if ($Mode == "PLUS_ITEM") {

    $_POST['upload'] ++;
}

if ($Mode == "REMOVE_ITEM") {
    if (!$_POST['upload'] || $_POST['upload'] == 0)
        $_POST['upload'] = 1;
    else if ($_POST['upload'] > 1)
        $_POST['upload'] --;
}
//display_error($Mode);
 
if ($Mode == 'ADD_ITEM') {
echo "<pre>"; print_r($_POST);
if(isDuplicate()){
    if(isAssigned1()){
    $last_id = add_statutory_main($_POST['statutory'], $_POST['return_name'], $_POST['freq_name'], $_POST['description'], $_POST['year'],$empl_id);
    if (!isset($max_image_size))
        $max_image_size = 50;
    $upload_file = "";


    $dir = company_path() . '/statutory';

    if (!file_exists($dir)) {
        mkdir($dir, 0777, TRUE);
    }
    if ($last_id) {
        for ($i = 0; $i < $_POST['upload']; $i++) {
            $result = $_FILES["kv_attach_name$i"]['error'];
            $upload_file = 'Yes';
            $tmpname = $_FILES["kv_attach_name$i"]['tmp_name'];
            $filename = basename($_FILES["kv_attach_name$i"]['name']);
            $filesize = $_FILES["kv_attach_name$i"]['size'];
            $filetype = $_FILES["kv_attach_name$i"]['type'];

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
            $doc_ext = pathinfo($_FILES["kv_attach_name$i"]['name'], PATHINFO_EXTENSION);


            /*
              if($doc_ext == 'ocx' ) {
              $doc_ext = substr(trim($_FILES['kv_attach_name']['name']), strlen($_FILES['kv_attach_name']['name']) - 4) ;
              }
             * 
             */
            //$filename .= "/".empl_img_name($empl_id).'.'.$doc_ext;
            $kv_file_name = $_POST['empl_id'] . '.' . $doc_ext;

            if ($_FILES["kv_attach_name$i"]['size'] > ($max_image_size * 1024)) { //File Size Check
                display_warning(_('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $max_image_size);
               // $upload_file = 'No';
                continue;
            } elseif (file_exists($filename)) {
                $result = unlink($filename);
                if (!$result) {
                    display_error(_('The existing Document could not be removed'));
                    continue;
                 //   $upload_file = 'No';
                }
            }

            if ((list($width, $height, $type, $attr) = getimagesize($_FILES["kv_attach_name$i"]['tmp_name'])) !== false) {

                $imagetype = $type;
            } else
                $imagetype = false;

//echo $doc_ext;
           if ($doc_ext!='png' && $doc_ext!='jpg' && $doc_ext!='doc' && $doc_ext!='docx'){	
              display_warning( _('Only .png, .doc, .png, .jpg format are uploaded'));
                continue;
//$upload_file ='No';
              } 

            
                if ($Mode == 'ADD_ITEM') {

                    $result = move_uploaded_file($tmpname, $dir . "/" . $unique_name . '.' . $doc_ext);
                    add_upload($last_id, $_POST['title'], $filename, $unique_name);
                    //add_upload($_POST['empl_id'], $_POST['empl_firstname'], $_POST['cv_title'], $filename,$unique_name );
                    display_notification(_("Employee Document has been attached!."));
                } else {
                    //display_error($unique_name);die;
                    /* 	if ($filename && file_exists($dir."/".$unique_name)){
                      unlink($dir."/".$unique_name);
                      $unique_name = uniqid('');
                      } */
                    //	display_error($dir);
                    $result = move_uploaded_file($tmpname, $dir . "/" . $unique_name . '.' . $doc_ext);
                 //   update_cv($selected_id, $_POST['empl_id'], $_POST['empl_firstname'], $_POST['cv_title'], $filename, $unique_name);
                    $selected_id = -1;

                   // display_notification(_("Attachment has been updated."));
                }
           
       
    }
}
    }
    else
      display_error("This task is not Assigned Yet !");  
    $_POST['upload'] =1;
}
else
   display_error("duplicate value cannot be inserted !");

    // }
    $Ajax->activate('_page_body');


 // $Mode = 'RESET';		
}


//display_error($Mode);
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
     $_POST['upload'] =1;
    $selected_id = -1;
}

start_form(true);
 start_table(TABLESTYLE_NOBORDER);
    start_row();
 //date_cells(_("Date") . ":", 'report_date', null, null, 0, 0, 0, null,true);
 kv_fiscalyears_list_cells(_("Fiscal Year:"), 'year', null, true);
 end_row();
 end_table();
 $result = get_all_statutory_main_report(check_value('show_inactive'),$empl_id,$_POST['year'],$_POST['report_date']);
start_table(TABLESTYLE);

$th = array(_("F-Year"), _('Stat-Body'), _("Name Of Compliance"), ("Freq"), ("Status(Y/N)"), ("Processed On"), ("Document(View)"),);
table_header($th);

$k = 0;

//$result = get_all_return(check_value('show_inactive'));
//echo $result["return_name"][0];
while ($myrow = db_fetch($result)) {

    alt_table_row_color($k);

    /* 	if ($myrow["dissallow_invoices"] == 0) 
      {
      $disallow_text = _("Invoice OK");
      }
      else
      {
      $disallow_text = "<b>" . _("NO INVOICING") . "</b>";
      } */
    label_cell(get_f_name($myrow["f_year"]));
    label_cell(get_statutory_name(1, $myrow["statutory_id"]),'',null,'center');
    label_cell(get_all_return_name(1, $myrow["return_id"]),'',null,'center');
    label_cell(get_frequency_name(1, $myrow["freq_id"]),'',null,'center');
    label_cell($myrow["status"] == 0 ? 'Yes' : 'NO','',null,'center');
    label_cell($myrow["updated_date"],'',null,'center');
    
    view_button_cell("View" . $myrow['id'], _("View"));
   
     //view_button_cell("view" . $myrow['id'], _("View"));
   // label_cell($myrow["statutory_desc"]);
//	label_cell($disallow_text);
    inactive_control_cell($myrow["id"], $myrow["inactive"], 'statutory_main', 'id');
   
   // edit_button_cell("Edit" . $myrow['id'], _("Edit"));
  //delete_button_cell("Delete".$myrow['id'], _("Delete"));
    end_row();
}

//inactive_control_row($th);


// inactive_control_row($th);
end_table();


//---------------------------------------------------------------------------------------



//viewing_controls();


if($Mode == 'View' || $Mode == 'Remove'){
   $_POST['upload'] = 1;
   if($Mode == 'View')
       $_SESSION['view_id'] = $selected_id;
   
   viewingControl($selected_id);

}




function viewingControl($selected_id){
      $result = get_all_statutory_upload_by_id($selected_id);
 // $_POST['remo'] = $selected_id;
    start_table(TABLESTYLE);
$th = array(_("Document name"), _('Uploaded date'),_('Title'),_('Download'));
table_header($th);


$k = 0;

//$result = get_all_return(check_value('show_inactive'));
//echo $result["return_name"][0];
while ($myrow = db_fetch($result)) {
    $res = get_all_statutory_main_by_id($myrow["statutory_main_id"]);
    $row = db_fetch($res);
    $dir = company_path() . '/statutory' ;
    alt_table_row_color($k);
    label_cell($myrow["file_name"],'',null,'center');
    label_cell($myrow["updated_date"],'',null,'center');
    label_cell($myrow["title"],'',null,'center');
    $ext = explode('.',$myrow["file_name"]);
    label_cell("<a href='".$dir."/".$myrow['unique_name'].".".$ext[count($ext)-1]."' download = '".$myrow['unique_name']."'>Download</a>",'',null,'center');
    inactive_control_cell($myrow["id"], $myrow["inactive"], 'statutory_uploads', 'id','',null,'center');
    //download_button_cell("Download" . $myrow['id'], _("Download"));
  // edit_button_cell("Uploadedit" . $myrow['id'], _("Uploadedit"));
  // Reset_button_cell("Reset" . $myrow['id'], _("Reset"));
   
  //delete_button_cell("Delete".$myrow['id'], _("Delete"));
    end_row();
}
end_table();
   $selected_id = -1;
    hidden('selected_id', $selected_id);
}


//display_error($Mode);
if($Mode == 'Uploadedit' ){
    $_POST['upload'] = 1;
   
      $result = get_all_statutory_upload_by_id2($selected_id);
      $myrow = db_fetch($result);
     // echo "<pre>";print_r($myrow);
    start_table(TABLESTYLE);
$th = array(_("Upload New Document"), _("Previous Document"), _('Uploaded Date'), _('Title'), _('Save'), 'Back');
table_header($th);
    $k = 0;
      alt_table_row_color($k);
      kv_doc_cells($label, 'kv_attach_name0');
    label_cell($myrow["file_name"],'',null,'center');
    label_cell($myrow["updated_date"],'',null,'center');
    text_cells('','title',$myrow['title'],'',null,'center');
    
    inactive_control_cell($myrow["id"], $myrow["inactive"], 'statutory_uploads', 'id');
    //download_button_cell("Download" . $myrow['id'], _("Download"));
   save_button_cell("Save" . $myrow['id'], _("Save"),'',null,'center');
   remove_button_cell("Remove" . $_POST['remo'], _("Remove"),'',null,'center');
   end_row();
  end_table();
   // $selected_id = -1;
    hidden('selected_id', $selected_id);
}








if($Mode == 'Save'){
    $_POST['upload'] = 1;
       if (!isset($max_image_size))
        $max_image_size = 50;
    $upload_file = "";


    $dir = company_path() . '/statutory' ;

    if (!file_exists($dir)) {
        mkdir($dir, 0777, TRUE);
    }
   
    if ($selected_id) {
         update_title($selected_id, $_POST['title']);
      
        for ($i = 0; $i < $_POST['upload']; $i++) {
            $result = $_FILES["kv_attach_name$i"]['error'];
            $upload_file = 'Yes';
            $tmpname = $_FILES["kv_attach_name$i"]['tmp_name'];
            $filename = basename($_FILES["kv_attach_name$i"]['name']);
            $filesize = $_FILES["kv_attach_name$i"]['size'];
            $filetype = $_FILES["kv_attach_name$i"]['type'];

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

            $doc_ext = pathinfo($_FILES["kv_attach_name$i"]['name'], PATHINFO_EXTENSION);



            if ($_FILES["kv_attach_name$i"]['size'] > ($max_image_size * 1024)) { //File Size Check
                display_warning(_('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $max_image_size);
               // $upload_file = 'No';
                continue;
            } elseif (file_exists($filename)) {
                $result = unlink($filename);
                if (!$result) {
                    display_error(_('The existing Document could not be removed'));
                    continue;
                 //   $upload_file = 'No';
                }
            }

            if ((list($width, $height, $type, $attr) = getimagesize($_FILES["kv_attach_name$i"]['tmp_name'])) !== false) {

                $imagetype = $type;
            } else
                $imagetype = false;

//echo $doc_ext;
           if ($doc_ext!='png' && $doc_ext!='jpg' && $doc_ext!='doc' && $doc_ext!='docx'){	
             // display_warning( _('Only .png, .doc, .png, .jpg format are uploaded'));
                continue;
//$upload_file ='No';
              } 

            
               

                    $result = move_uploaded_file($tmpname, $dir . "/" . $unique_name . '.' . $doc_ext);
                    update_upload($selected_id, $_POST['title'], $filename, $unique_name);
                    //add_upload($_POST['empl_id'], $_POST['empl_firstname'], $_POST['cv_title'], $filename,$unique_name );
                    display_notification(_("Employee Document has been updated!."));
               
            }
       display_notification(_("Employee Document has been updated!."));
    }
    viewingControl($_SESSION['view_id']);
    $_POST['upload'] =1;
    $Ajax->activate('_page_body');
    $Mode = 'View';
    
    
}

if ($selected_id != -1) 
{
    
 	if ($Mode == 'Edit'){
           
            $res = get_all_statutory_main_by_id($selected_id);
          //  echo "<pre>"; print_r($row['statutory_id']);
            //`fa_statutory_main`
            $row = db_fetch($res);
             $dir = company_path() . '/statutory';
             $result = get_all_statutory_upload_by_id($selected_id);
             $j = 0;
             while ($myrow = db_fetch($result)) {
                $ext = explode('.',$myrow["file_name"]);
                    $_POST["attach_name$j"] = $dir.'/'.$myrow['unique_name'].".".$ext[count($ext)-1];
                    $j++;
             }
            $_POST['year'] = $row['f_year'];
            $_POST['statutory'] =$row['statutory_id'];
            $_POST['return_name'] =$row['return_id'];
            $_POST['freq_name'] = $row['freq_id'];
           // $_POST[''] = $row[];
            $_POST['description'] =$row['statutory_desc'];
            $_POST['yes_no'] =$row['status'];
      

 }
 hidden('selected_id', $selected_id);
}

br(2);
//echo $_POST['upload'];



end_form();

function getAge($birth_date){

    $d1 = new DateTime($birth_date);
    $d2 = new DateTime(date('Y-m-d'));

    $diff = $d2->diff($d1);

    return $diff->y;
}


end_page();
?>