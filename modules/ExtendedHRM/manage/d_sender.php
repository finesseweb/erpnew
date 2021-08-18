<?php
/**********************************************************************
  
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
$page_security = 'SA_ITEMDISPATCH';
$path_to_root="../../..";
include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");

 

include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );


$version_id = get_company_prefs('version_id');

$state_id = "1479";
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
page(_($help_context = "Dispatch Management"));
if (isset($_GET['vw']))
	$view_id = $_GET['vw'];
else
	$view_id = find_submit('view');
if ($view_id != -1)
{
	$row = get_employee_cv($view_id);
	if ($row['filename'] != "")
	{
		if(in_ajax()) {
			$Ajax->popup($_SERVER['PHP_SELF'].'?vw='.$view_id);
		} else {
			$type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';	
    		header("Content-type: ".$type);
    		header('Content-Length: '.$row['filesize']);
	    	//if ($type == 'application/octet-stream')
    		//	header('Content-Disposition: attachment; filename='.$row['filename']);
    		//else
	 			header("Content-Disposition: inline");
	    	echo file_get_contents(company_path(). "/attachments/emplcv/".$row['unique_name']);
    		exit();
		}
	}	
}

if (isset($_GET['dl']))
	$download_id = $_GET['dl'];
else
	$download_id = find_submit('download');

if ($download_id != -1)
{
	
	$row = get_employee_cvdwnld($download_id);
	
	if ($row['filename'] != "")
	{
		if(in_ajax()) {
			$Ajax->redirect($_SERVER['PHP_SELF'].'?dl='.$download_id);
		} else {
			
			$type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';	
    		header("Content-type: ".$type);
	    	header('Content-Length: '.$row['filesize']);
    		header('Content-Disposition: attachment; filename='.$row['filename']);
    		echo file_get_contents(company_path()."/attachments/emplcv/".$row['empl_id']."/".$row['unique_name']);
	    	exit();
		}
	}	
}

simple_page_mode(true);
/*if(get_post('_name_changed'))
{
		$regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('name')) ==0) {
			display_error( _("Accept only Alphabets."));
			set_focus('name');
			
		} else{
			// $input_error = 0;
		}
}*/	
?>

 <?php
function can_process() 
{
	
        if (strlen($_POST['line1']) == 0) 
	{
		display_error(_("Line 1 can not be empty."));
		set_focus('line1');
		return false;
	}
        if (strlen($_POST['city']) == 0) 
	{
		display_error(_("City can not be empty."));
		set_focus('city');
		return false;
	}
        if (strlen($_POST['pin_no']) == 0) 
	{
		display_error(_("Pin Code can not be empty."));
		set_focus('pin_code');
		return false;
	}
         if (strlen($_POST['c_number']) == 0) 
	{
             
                 $regex = "/[a-zA-Z*@$%#^&!~()+-\/><.,{};'?<>]/";
		display_error(_("Contact Number can not be empty."));
		set_focus('c_number');
		return false;
	}
        
  
      
        
        
       /*  if (strlen($_POST['email']) == 0) 
	{
		display_error(_("Father's name can not be empty."));
		set_focus('email');
		return false;
	}
	if(!empty($_POST['room_des']))
	{
		$regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('name')) ==0) {
			display_error( _("Accept only Alphabets."));
			set_focus('name');
			return false;
		} 	
	}
	
	if (strlen($_POST['description']) == 0) 
	{
		display_error(_("The Designation description cannot be empty."));
		set_focus('description');
		return false;
	}	*/
	 
	return true;
}



//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'  && can_process()) 
{
    
   if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '') {
          
           // $selected_id = $kv_empl_id;
            $result = $_FILES['pic']['error'];
            $upload_file = 'Yes'; //Assume all is well to start off with
            $filename = company_path() . '/images/dispatch/' . $_POST['sender_person'] .'/';

            //if(!isset($max_image_size))
            $max_image_size = 2048;

            if (!file_exists($filename)) {
                mkdir($filename);
            }
            $filename .=  $_FILES['pic']['name'];
            $filename1 = $_FILES['pic']['name'];

            if ((list($width, $height, $type, $attr) = getimagesize($_FILES['pic']['tmp_name'])) !== false) {

                $imagetype = $type;
            } else
                $imagetype = false;

            //display_error($_FILES['pic']['size']);
            //display_error(strtoupper(substr(trim($_FILES['pic']['name']), strlen($_FILES['pic']['name']) - 3)));die;
        if (!in_array(strtoupper(substr(trim($_FILES['pic']['name']), strlen($_FILES['pic']['name']) - 3)), array('JPG', 'PNG', 'GIF','PDF'))) {
                display_warning(_('Only  files  supported  are - a file extension of .jpg, .png , .gif,.pdf is expected'));
                $upload_file = 'No';
            } else if ($_FILES['pic']['size'] > ($max_image_size * 1024)) { //File Size Check
                display_warning(_('The file size is over the maximum allowed. The maximum size allowed in MB is') . ' ' . $max_image_size);
                $upload_file = 'No';
            } elseif (file_exists($filename)) {
                $result = unlink($filename);
                if (!$result) {
                    display_error(_('The existing image could not be removed'));
                    $upload_file = 'No';
                }
            }
            if ($_FILES['pic']['error'] === UPLOAD_ERR_INI_SIZE) {
                // Handle the error
                echo 'Your file is too large.';
                $upload_file = 'No';
                die();
            }

            if ($upload_file == 'Yes') {
             $update_pager = false;   
                if ($Mode == 'ADD_ITEM')
			{
                    $state_id = $_POST['state'];
                    if(is_issue($_POST['issue_no'])==0){
                            $result = move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
                             
                            add_guest1($_POST['ref_id'],$filename,$_POST['issue_no'],$_POST['line1'],$_POST['line2'],$_POST['city'],$_POST['country'],$_POST['state'],$_POST['pin_no'],$_POST['subject_title'],$_POST['dispatch_date'],$_POST['designation'],$_POST['department'],$_POST['dispatch_mode'],$_POST['document_type'],$_POST['dispatched_reciept_number_if_any'],$_POST['sender_person'],$_POST['sender_designation'],$_POST['sender_department'],$_POST['email'],$_POST['c_number'],$_POST['person_org_name'],$filename1,$unique_name,$_POST['remarks']);
	display_notification(_('New Record  has been added'));
	$Mode = 'RESET';
      $update_pager = true;
                             }
                             else
                             {
                                 display_warning(_("Duplicate isuue no"));
                             }
			}
			else
			{
				$state_id = $_POST['state'];
                                
                                
                                	if(is_issue1($_POST['issue_no'])==$_POST['ref_id']){
                                              $result = move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
					//update_cv($selected_id,$_POST['empl_id'], $_POST['empl_firstname'], $_POST['cv_title'], $filename,$unique_name);
				//display_notification(_("Attachment has been updated.")); 
                                  update_guest1($selected_id,$_POST['ref_id'],$filename,$_POST['issue_no'],$_POST['line1'],$_POST['line2'],$_POST['city'],$_POST['country'],$_POST['state'],$_POST['pin_no'],$_POST['subject_title'],$_POST['dispatch_date'],$_POST['designation'],$_POST['department'],$_POST['dispatch_mode'],$_POST['document_type'],$_POST['dispatched_reciept_number_if_any'],$_POST['sender_person'],$_POST['sender_designation'],$_POST['sender_department'],$_POST['email'],$_POST['c_number'],$_POST['person_org_name'],$filename1,$unique_name,$_POST['remarks']);
                                
	$Mode = 'RESET';
        $update_pager = true;
	display_notification(_('Record has been updated'));
                                            
                                        }
                                
                            else if(is_issue($_POST['issue_no'])==0){
                            $result = move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
					//update_cv($selected_id,$_POST['empl_id'], $_POST['empl_firstname'], $_POST['cv_title'], $filename,$unique_name);
				//display_notification(_("Attachment has been updated.")); 
                                  update_guest1($selected_id,$_POST['ref_id'],$filename,$_POST['issue_no'],$_POST['line1'],$_POST['line2'],$_POST['city'],$_POST['country'],$_POST['state'],$_POST['pin_no'],$_POST['subject_title'],$_POST['dispatch_date'],$_POST['designation'],$_POST['department'],$_POST['dispatch_mode'],$_POST['document_type'],$_POST['dispatched_reciept_number_if_any'],$_POST['sender_person'],$_POST['sender_designation'],$_POST['sender_department'],$_POST['email'],$_POST['c_number'],$_POST['person_org_name'],$filename1,$unique_name,$_POST['remarks']);
                                
	$Mode = 'RESET';
        $update_pager = true;
	display_notification(_('Record has been updated'));
                            }
                             else
                             {
                                 display_warning(_("Duplicate isuue no"));
                             }
                            
					
			}
                
                
                
            }
            $Ajax->activate('details');
       }
       else
        {
                  $update_pager = false;   
                if ($Mode == 'ADD_ITEM')
			{
                    $state_id = $_POST['state'];
                    if(is_issue($_POST['issue_no'])==0){
                            $result = '';
                             
                            add_guest1($_POST['ref_id'],$filename,$_POST['issue_no'],$_POST['line1'],$_POST['line2'],$_POST['city'],$_POST['country'],$_POST['state'],$_POST['pin_no'],$_POST['subject_title'],$_POST['dispatch_date'],$_POST['designation'],$_POST['department'],$_POST['dispatch_mode'],$_POST['document_type'],$_POST['dispatched_reciept_number_if_any'],$_POST['sender_person'],$_POST['sender_designation'],$_POST['sender_department'],$_POST['email'],$_POST['c_number'],$_POST['person_org_name'],$filename1,$unique_name,$_POST['remarks']);
	display_notification(_('New Record  has been added'));
	$Mode = 'RESET';
      $update_pager = true;
                             }
                             else
                             {
                                 display_warning(_("Duplicate isuue no"));
                             }
			}
			else
			{
				$state_id = $_POST['state'];
                                
                                
                                	if(is_issue1($_POST['issue_no'])==$_POST['ref_id']){
                                              $result = '';
					//update_cv($selected_id,$_POST['empl_id'], $_POST['empl_firstname'], $_POST['cv_title'], $filename,$unique_name);
				//display_notification(_("Attachment has been updated.")); 
                                  update_guest1($selected_id,$_POST['ref_id'],$filename,$_POST['issue_no'],$_POST['line1'],$_POST['line2'],$_POST['city'],$_POST['country'],$_POST['state'],$_POST['pin_no'],$_POST['subject_title'],$_POST['dispatch_date'],$_POST['designation'],$_POST['department'],$_POST['dispatch_mode'],$_POST['document_type'],$_POST['dispatched_reciept_number_if_any'],$_POST['sender_person'],$_POST['sender_designation'],$_POST['sender_department'],$_POST['email'],$_POST['c_number'],$_POST['person_org_name'],$filename1,$unique_name,$_POST['remarks']);
                                
	$Mode = 'RESET';
        $update_pager = true;
	display_notification(_('Record has been updated'));
                                            
                                        }
                                
                            else if(is_issue($_POST['issue_no'])==0){
                            $result = move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
					//update_cv($selected_id,$_POST['empl_id'], $_POST['empl_firstname'], $_POST['cv_title'], $filename,$unique_name);
				//display_notification(_("Attachment has been updated.")); 
                                  update_guest1($selected_id,$_POST['ref_id'],$filename,$_POST['issue_no'],$_POST['line1'],$_POST['line2'],$_POST['city'],$_POST['country'],$_POST['state'],$_POST['pin_no'],$_POST['subject_title'],$_POST['dispatch_date'],$_POST['designation'],$_POST['department'],$_POST['dispatch_mode'],$_POST['document_type'],$_POST['dispatched_reciept_number_if_any'],$_POST['sender_person'],$_POST['sender_designation'],$_POST['sender_department'],$_POST['email'],$_POST['c_number'],$_POST['person_org_name'],$filename1,$unique_name,$_POST['remarks']);
                                
	$Mode = 'RESET';
        $update_pager = true;
	display_notification(_('Record has been updated'));
                            }
                             else
                             {
                                 display_warning(_("Duplicate isuue no"));
                             }
                            
					
			}
                
          
            $Ajax->activate('details');
      }
    
    
    
    
    
    
    
   
	
} 

//-----------------------------------------------------------------------------------
/*
if ($Mode=='UPDATE_ITEM' && can_process()) 
{
	//display_error("sdff");die;
	update_guest($selected_id,$_POST['g_name'],$_POST['f_name'],$_POST['gender'],$_POST['marital_status'],$_POST['pic'],$_POST['porpose'],$_POST['line1'],$_POST['line2'],$_POST['city'],$_POST['country'],$_POST['state'],$_POST['pin_code'],$_POST['email'],$_POST['c_number']);
	$Mode = 'RESET';
	display_notification(_('Record has been updated'));
}*/


//-----------------------------------------------------------------------------------

 function can_delete($selected_id)
{
	if (key_in_foreign_table($selected_id, 'debtors_master', 'credit_status'))
	{
		display_error(_("Cannot delete this credit status because customer accounts have been created referring to it."));
		return false;
	}
	
	return true;
} 


//-----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{

	//if (can_delete($selected_id))
	//{
		delete_guest($selected_id);
		//display_notification(_(' '));
	//}
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}
//-----------------------------------------------------------------------------------
//===[Working]=====//

        
        
        
        
       
        
        
start_form(true);
$sql = getDispatchInfo($status);
	
$cols =
	array(
		_("#") => array('align'=> 'center'),
		_("Ref No") => array('align'=> 'center'),
		_("Issue No") =>array('align'=> 'center'),
		_("Subject Title") => array('align'=> 'center'),
		_("Dispatch Date") =>  array('align'=> 'center','fun'=>'fordate'),
            _("Person Org Name") => array('align'=> 'center'),  
             _("Designation") => array('align'=> 'center'), 
             _("Department") => array('align'=> 'center'), 
            _("Address") => array('align'=> 'center'), 
            _("Dispatch Mode") => array('align'=> 'center','fun'=>'document_mode'),
            _("Document Type") => array('align'=> 'center','fun'=>'document_type'),
            _("Dispatch Reciept") => array('align'=> 'center' ,'fun' => 'reciept'),
            _("Download Document") => array('align'=> 'center','fun'=>'down'),
	    _("Sender Person") =>  array('align'=> 'center','fun'=>'sen_person'),
             _("Sender Designation") => array('align'=> 'center','fun'=>'sen_desig'),
            _("Sender Department") => array('align'=> 'center','fun'=>'sen_dept'),
            _("Edit") => array('align'=> 'center','fun'=>'edit'),
	
	   );
	$table =&new_db_pager('dispatch_management', $sql, $cols);

	$table->width = "80%";
	display_db_pager($table);
start_table(TABLESTYLE, "width=40%");
//$th = array(_("#"),_("Ref No"),_("Issue No."),_("Subject Title"),_("Dispatch Date"),_("Person Org Name"),_("Designation"),_("Department"),_("Address"),_("Dispatch Mode"),_("Document Type"),_("Dispatch Reciept"),_("Download Document"),_("Sender Person"),_("Sender Designation"),_("Sender Department"),'','');
inactive_control_column($th);
table_header($th);

$k = 0;

$nos=db_num_rows($result);
 function fordate($row){
     return date('d-m-Y', strtotime($row['dispatch_date']));
 }
 function sen_person($row){
             return $row['sender_person'];
        }

        function sen_desig($row){
             return $row['sender_designation'];
        }

           function sen_dept($row){
             return $row['sender_department'];
        }
        
        
 function document_type($row){
      $document_mode = document_id($row["document_type"]);
      return $document_mode['name'];
 }        
        
  
  function document_mode($row){
      $document_mode = document_id($row["dispatch_mode"]);
      return $document_mode['name'];
 } 
 
 function down($row){
     return  "<a href='".$row["upload_scanned_copy"]."' download>".$row["filename"]."</a>";
 }
 
 
 function edit($row){
     return edit_button_cell("Edit".$row['id'], _("Edit"));
 }

 function reciept($row){
            return $row['dispatched_reciept_number_if_any'];
        }
   
        
       
//inactive_control_row($th);
end_table();
echo '<br>';


//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
   // display_error($selected_id);
    
 	if ($Mode == 'Edit') {
            
		$myrow = get_dispatch_id($selected_id);
                $line = explode(',',$myrow["address"]);

		$_POST['ref_id']  = $myrow["ref_id"];
		$_POST['issue_no']  = $myrow["issue_no"];
                $_POST['subject_title']  = $myrow["subject_title"];
                $_POST['dispatch_date']  = date("d/m/Y", strtotime($myrow["dispatch_date"]));
                $_POST['designation']  = $myrow["designation"];
                $_POST['department']  = $myrow["department"];
                $_POST['dispatch_mode'] = $myrow["dispatch_mode"];
                $_POST['document_type'] = $myrow["document_type"];
                $_POST['dispatched_reciept_number_if_any'] = $myrow["dispatched_reciept_number_if_any"];
                 $_POST['sender_person'] = $myrow["sender_person"];
                  $_POST['sender_designation'] = $myrow["sender_designation"];
                  $_POST['sender_department'] = $myrow["sender_department"];
                  $_POST['email'] = $myrow["email"];
                  $_POST['c_number'] = $myrow["contact_no"];
                  $_POST['person_org_name'] = $myrow["person_org_name"];
		$_POST['line1']  = $line[0];
                $_POST['line2']  = $line[1];;
                $_POST['city']  = $myrow["city"];
                $_POST['country']  = $myrow["country"];
                $_POST['state']  = $myrow["state"];
		$_POST['pin_no']  = $myrow["pin_no"];
                $_POST['email']  = $myrow["email_id"];
                $_POST['c_number']  = $myrow["contact_no"];
                $_POST['remarks'] = $myrow["remarks"];
                $state_id = $_POST['state'];
	}
	hidden('selected_id', $selected_id);
} 


 table_section_title(_("Dispatch Details"));
 
     
 if ($Mode != 'Edit' && !isset($_POST['sender_person'])) {
       $sql1 = "SELECT MAX(ref_id) FROM fa_dispatch_management WHERE ref_id LIKE 'ref-%'";

                    $empl_data = db_query($sql1);
                    $empid_result = db_fetch_row($empl_data);

                    //$last_max_emp_id = $empid_result[0];
                    $last_emp1 = substr($empid_result[0], 5);

                    $emp_inc_id = $last_emp1 + 1;
                    if (strlen($emp_inc_id) == 1) {
                        $_POST['ref_id'] = 'ref-00' . $emp_inc_id;
                    } else if (strlen($emp_inc_id) == 2) {
                        $_POST['ref_id'] = 'ref-0' . $emp_inc_id;
                    } else {
                        $_POST['ref_id'] = 'ref-' . $emp_inc_id;
                    }
 }
 
  label_row(_("Ref No.:*"), $_POST['ref_id']);

        hidden('ref_id', $_POST['ref_id']);
        
        text_row(_('Letter No./Issue No.'), 'issue_no', $_POST['issue_no'], 30,50);
        text_row(_('Subject Title'), 'subject_title', $_POST['subject_title'], 30,50);
        date_row(_("Dispatch Date") . ":", 'dispatch_date');
 



 
 table_section_title(_("To whom Its Dispatched"));
 text_row(_('Person org Name'), 'person_org_name', $_POST['person_org_name'], 30,50);
 
 text_row(_('Designation'), 'designation', $_POST['designation'], 30,50);
 
 text_row(_('Department'), 'department', $_POST['department'], 30,50);
 
room_des_row_ex(_("Line 1*"), 'line1', 30,null,null,null,null,null,FALSE);

room_no_row_ex(_("Line 2"), 'line2', 30,null,null,null,null,null,FALSE);

room_des_row_ex(_("City *"), 'city', 30,null,null,null,null,null,FALSE);

country_list_row(_("Country:"), 'country', null, true, false);

state_list_row(_("State / Union Territories:"), 'state', $state_id, $_POST['country'], true, false);

room_no_row_ex(_("Pin Code *"), 'pin_no', 30,null,null,null,null,null,FALSE);

room_no_row_ex(_("Email Id *"), 'email', 30,null,null,null,null,null,FALSE);

room_no_row_ex(_("Contact Number *"), 'c_number', 30,null,null,null,null,null,FALSE);

dispatch_mode_list_row(_("Dispatch Mode: "),'dispatch_mode',$_POST['dispatch_mode'],false);

document_type_list_row(_("Document Type: "),'document_type',$_POST['document_type'],false);

text_row(_('Dispatched Reciept Number (If Any)'), 'dispatched_reciept_number_if_any', $_POST['dispatched_reciept_number_if_any'], 30,50);

kv_image_row(_("Upload Scanned Copy  (.jpg/.pdf)") . ": " . $disp_max, 'pic', 'pic');

 table_section_title(_("Sender Details"));
 
   employee_list_cells(_("Person: "), 'sender_person', null, _('Select'), true, check_value('show_inactive'));
   
   if(isset($_POST['sender_person'])){
      $result =  getDepartment($_POST['sender_person']);
        $dep_desig = db_fetch_row($result);
       $_POST['sender_department'] = $dep_desig[1];
        $_POST['sender_designation'] = $dep_desig[3];
   }
   

        label_row(_('Department'), $_POST['sender_department']);
        hidden('sender_department', $_POST['sender_department']);
        label_row(_('Designation'), $_POST['sender_designation']);
        hidden('sender_designation', $_POST['sender_designation']);
         table_section_title(_("Remarks"));
   textarea_row(_("Remarks"), 'remarks', $_POST['remarks'], 34, 6);
        
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
