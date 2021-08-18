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
$page_security = 'SA_ITEMRECIEVE';
$path_to_root="../../..";
include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");

 

include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");

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
page(_($help_context = "Recieve Managment"), @$_REQUEST['popup'], false, "", $js);
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
<html lang="en">
 <head>
    <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
    <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
   <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js"?>"></script>
</head>
 
 
 </html>
 <?php
function can_process() 
{

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
                $update_pager = false;
                if ($Mode == 'ADD_ITEM')
			{if(is_issu3($_POST['issue_no'])==0){
                            $result = move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
                            recieve_guest1($_POST['ref_id'],$_POST['issue_no'],$_POST['subject_title'],$_POST['recieved_date'],$_POST['recieve_mode'],$_POST['document_type'],$_POST['sender_person'],$_POST['sender_designation'],$_POST['sender_department'],$_POST['remarks']);
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
                           $my =  is_issue2($_POST['issue_no']);
                         
                   
				if($my==$_POST['ref_id']){
                            
                            $result = move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
					//update_cv($selected_id,$_POST['empl_id'], $_POST['empl_firstname'], $_POST['cv_title'], $filename,$unique_name);
				//display_notification(_("Attachment has been updated.")); 
                                update_recieved($selected_id,$_POST['ref_id'],$_POST['issue_no'],$_POST['subject_title'],$_POST['recieved_date'],$_POST['recieve_mode'],$_POST['document_type'],$_POST['sender_person'],$_POST['sender_designation'],$_POST['sender_department'],$_POST['remarks']);
                                
	$Mode = 'RESET';
        $update_pager = true;
	display_notification(_('Record has been updated'));
                                }
                                elseif(is_issu3($_POST['issue_no'])==0){
                                  //  display_error(is_issu3($_POST['issue_no']));
                                     $result = move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
					//update_cv($selected_id,$_POST['empl_id'], $_POST['empl_firstname'], $_POST['cv_title'], $filename,$unique_name);
				//display_notification(_("Attachment has been updated.")); 
                                update_recieved($selected_id,$_POST['ref_id'],$_POST['issue_no'],$_POST['subject_title'],$_POST['recieved_date'],$_POST['recieve_mode'],$_POST['document_type'],$_POST['sender_person'],$_POST['sender_designation'],$_POST['sender_department'],$_POST['remarks']);
                                
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

	//display_error($result);die;
start_form(true);
$sql = getRecieveInfo($status);
$cols =
	array(
		_("#") => array('align'=> 'center'),
		_("Ref No") => array('align'=> 'center'),
		_("Issue No") =>array('align'=> 'center'),
		_("Subject Title") => array('align'=> 'center'),
		_("Recieve Date") =>  array('align'=> 'center','fun'=> 'fordate'),
		_("Recieve Mode") => array('align'=> 'center','fun'=>'document_mode'),
            _("Document Type") => array('align'=> 'center','fun'=>'document_type'),
	    _("Sender Person") =>  array('align'=> 'center'),
             _("Sender Designation") => array('align'=> 'center'),
            _("Sender Department") => array('align'=> 'center'),
            _("Edit") => array('align'=> 'center','fun'=>'edit'),
	
	   );
	$table =& new_db_pager('trans_tbl', $sql, $cols);

	$table->width = "80%";
	display_db_pager($table);
start_table(TABLESTYLE, "width=40%");
//$th = array(_("#"),_("Ref No"),_("Issue No."),_("Subject Title"),_("Recieve Date"),_("Recieve Mode"),_("Document Type"),_("Sender Person"),_("Sender Designation"),_("Sender Department"),'','');
inactive_control_column($th);
table_header($th);

$k = 0;
//_("Debit") => array('align'=>'right', 'fun'=>'fmt_debit'),




        
 function document_type($row){
      $document_mode = document_id($row["document_type"]);
      return $document_mode['name'];
 }        
        
  
  function document_mode($row){
      $document_mode = document_id($row["recieve_mode"]);
      return $document_mode['name'];
 }   
 
 
 function edit($row){
     return edit_button_cell("Edit".$row['id'], _("Edit"));
 }
 
 function fordate($row){
     return date('d-m-Y', strtotime($row['recieved_date']));
 }
        

$nos=db_num_rows($result);
/*if($nos !=0){
$i=1;	
while ($myrow = db_fetch($result)) 
{
    $recieve_mode = depatch_id($myrow["recieve_mode"]);
    $document_mode = document_id($myrow["document_type"]);
	
	alt_table_row_color($k);
        label_cell($i,"align='center'");
	label_cell($myrow["ref_id"]);
	label_cell($myrow["issue_no"],"align='center'");
	label_cell($myrow["subject_title"],"align='center'");
	label_cell(date('d-m-Y',strtotime($myrow["recieved_date"])),"align='center'");
        label_cell($recieve_mode['name'],"align='center'");
        label_cell($document_mode['name'],"align='center'");
       label_cell($myrow["sender_person"],"align='center'");
       label_cell($myrow["sender_designation"],"align='center'");
       label_cell($myrow["sender_department"],"align='center'");
        //label_cell(date('d - m - Y',strtotime($myrow['registraion_date'])),"align='center'");
	//inactive_control_cell($myrow["guest_id"], $myrow["deleted"], 'guest_registration', 'guest_id');
       // inactive_control_cell2($myrow["guest_id"], $myrow["deleted"], 'guest_registration', 'guest_id','bed');
 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
	submit_js_confirm("Delete".$myrow["id"], sprintf(_("You are about to Inactive Record?"), $myrow['id']));
	end_row();
        $i++;
 }
 
}else{
	label_cell('No Records Found','colspan=10 align="center" size="15"');
}*/

//inactive_control_row($th);
end_table();
echo '<br>';

//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
   // display_error($selected_id);
    
 	if ($Mode == 'Edit') {
		$myrow = get_recieve_id($selected_id);
                $line = explode(',',$myrow["address"]);
		$_POST['ref_id']  = $myrow["ref_id"];
		$_POST['issue_no']  = $myrow["issue_no"];
                $_POST['subject_title']  = $myrow["subject_title"];
                $_POST['recieved_date']  = date("d/m/Y", strtotime($myrow["recieved_date"]));
                $_POST['recieve_mode'] = $myrow["recieve_mode"];
                $_POST['document_type'] = $myrow["document_type"];
                 $_POST['sender_person'] = $myrow["sender_person"];
                  $_POST['sender_designation'] = $myrow["sender_designation"];
                  $_POST['sender_department'] = $myrow["sender_department"];
                  $_POST['remarks'] = $myrow["remarks"];
                  
                  $_POST['prev_issue'] = $myrow['issue_no'];
	}
	hidden('selected_id', $selected_id);
} 


 table_section_title(_("Recieved Details"));
  if ($Mode != 'Edit' && !isset($_POST['sender_person'])){
       $sql1 = "SELECT MAX(ref_id) FROM fa_recieved_management WHERE ref_id LIKE 'rev-%'";

                    $empl_data = db_query($sql1);
                    $empid_result = db_fetch_row($empl_data);

                    //$last_max_emp_id = $empid_result[0];
                    $last_emp1 = substr($empid_result[0], 5);

                    $emp_inc_id = $last_emp1 + 1;
                    if (strlen($emp_inc_id) == 1) {
                        $_POST['ref_id'] = 'rev-00' . $emp_inc_id;
                    } else if (strlen($emp_inc_id) == 2) {
                        $_POST['ref_id'] = 'rev-0' . $emp_inc_id;
                    } else {
                        $_POST['ref_id'] = 'rev-' . $emp_inc_id;
                    }
 }
 
  label_row(_("Ref No.:*"), $_POST['ref_id']);

        hidden('ref_id', $_POST['ref_id']);
        
        text_row(_('Letter No./Issue No.'), 'issue_no', $_POST['issue_no'], 30,50);
        text_row(_('Subject Title'), 'subject_title', $_POST['subject_title'], 30,50);
        date_row(_("Recieved Date") . ":", 'recieved_date');
 



 
 table_section_title(_("To whom it is concerned"));


dispatch_mode_list_row(_("Recieved Mode: "),'recieve_mode',$_POST['recieve_mode'],false);

document_type_list_row(_("Document Type: "),'document_type',$_POST['document_type'],false);




 
employee_list_cells(_("Person: "), 'sender_person', $_POST['sender_person'], _('Select'), true,check_value('show_inactive'));
   
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
