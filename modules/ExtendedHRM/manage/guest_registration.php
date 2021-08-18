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
$page_security = 'GUEST_REGISTRATION';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
page(_($help_context = "Guest Registration")); 

include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");


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
	if (strlen($_POST['g_name']) == 0) 
	{
		display_error(_("Name can not be empty."));
		set_focus('g_name');
		return false;
	}
        
        if (strlen($_POST['f_name']) == 0) 
	{
		display_error(_("Father's name can not be empty."));
		set_focus('f_name');
		return false;
	}
       /* if (strlen($_POST['pic']) == 0) 
	{
		display_error(_("Please Upload Id Proof."));
		set_focus('pic');
		return false;
	}*/
        if (strlen($_POST['porpose']) == 0) 
	{
		display_error(_("Porpose For Stay can not be empty."));
		set_focus('porpose');
		return false;
	}
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
        if (strlen($_POST['pin_code']) == 0) 
	{
		display_error(_("Pin Code can not be empty."));
		set_focus('pin_code');
		return false;
	}
         if (strlen($_POST['c_number']) == 0) 
	{
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
    if(!isset($max_image_size))
		$max_image_size = 50;
		$upload_file = "";
		$result = $_FILES['pic']['error'];
		$upload_file = 'Yes'; 
		$tmpname = $_FILES['pic']['tmp_name'];
                $dir = company_path().'/attachments/';
		if (!file_exists($dir)){
			mkdir($dir);
		}
		$filename = basename($_FILES['pic']['name']);
		$filesize = $_FILES['pic']['size'];
		$filetype = $_FILES['pic']['type'];
               
                /*if ($Mode == 'UPDATE_ITEM')
		{
		    $row = get_employee_cvdwnld($guest_id);
		    if ($row['filename'] == "")
        		exit();
			$unique_name = $row['unique_name'];
			if ($filename && file_exists($dir."/".$unique_name)){
				unlink($dir."/".$unique_name);
				$unique_name = uniqid('');
			}
			
		}
		else*/
			$unique_name = uniqid('');

                $doc_ext = substr(trim($_FILES['pic']['name']), strlen($_FILES['pic']['name']) - 3) ; 
			if($doc_ext == 'ocx' ) {
				$doc_ext = substr(trim($_FILES['pic']['name']), strlen($_FILES['pic']['name']) - 4) ; 
			}
			//$filename .= "/".empl_img_name($empl_id).'.'.$doc_ext;
				$kv_file_name = $_POST['g_name'].'.'.$doc_ext;
			
			if ( $_FILES['pic']['size'] > ($max_image_size * 1024)) { //File Size Check
				display_warning(_('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $max_image_size);
				$upload_file ='No';
			} 
			elseif (file_exists($filename)){
				$result = unlink($filename);
				if (!$result) {
					display_error(_('The existing Document could not be removed'));
					$upload_file ='No';
				}
			}
			
		if ((list($width, $height, $type, $attr) = getimagesize($_FILES['pic']['tmp_name'])) !== false){
				
				$imagetype = $type;
			}
			else
				$imagetype = false;
			 
		if ($imagetype != IMAGETYPE_GIF && $imagetype != IMAGETYPE_JPEG && $imagetype != IMAGETYPE_PNG && $imagetype == '' && ($_FILES["file"]["type"] != "application/pdf")){	
				display_warning( _('Only graphics files and pdf files can be uploaded'));
				$upload_file ='No';
			}
			elseif (!in_array(strtoupper(substr(trim($_FILES['pic']['name']), strlen($_FILES['pic']['name']) - 3)), array('JPG','PNG','GIF'))){
				display_warning(_('Only graphics files are supported - a file extension of .jpg, .png or .gif is expected'));
				$upload_file ='No';
			} 
                        if ($upload_file == 'Yes'){	
			if ($Mode == 'ADD_ITEM')
			{
                            $result  =  move_uploaded_file($tmpname, $dir."/".$unique_name);
                            add_guest($_POST['g_name'],$_POST['f_name'],$_POST['gender'],$_POST['marital_status'],$_POST['pic'],$_POST['porpose'],$_POST['line1'],$_POST['line2'],$_POST['city'],$_POST['country'],$_POST['state'],$_POST['pin_code'],$_POST['email'],$_POST['c_number'],$filename,$unique_name);
	display_notification(_('New Record  has been added'));
	$Mode = 'RESET';
			}
			else
			{
				$result  =  move_uploaded_file($tmpname, $dir."/".$unique_name);
					//update_cv($selected_id,$_POST['empl_id'], $_POST['empl_firstname'], $_POST['cv_title'], $filename,$unique_name);
				//display_notification(_("Attachment has been updated.")); 
                                update_guest($selected_id,$_POST['g_name'],$_POST['f_name'],$_POST['gender'],$_POST['marital_status'],$_POST['pic'],$_POST['porpose'],$_POST['line1'],$_POST['line2'],$_POST['city'],$_POST['country'],$_POST['state'],$_POST['pin_code'],$_POST['email'],$_POST['c_number'],$filename,$unique_name);
	$Mode = 'RESET';
	display_notification(_('Record has been updated'));
					
			}
                            
			
	     }
	//display_error("afdd");die;
	
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

$result = get_Guest_info(check_value('show_inactive'));
	//display_error($result);die;
start_form(true);
start_table(TABLESTYLE, "width=40%");
$th = array(_("#"),_("Name"),_("Father's Name"),_("Gender"),_("Address"),_("Contact No."),_("Email Id"),_("Purpose For Stay"),_("Download Document"),_("Date"),'','');
inactive_control_column($th);
table_header($th);

$k = 0;

$nos=db_num_rows($result);
if($nos !=0){
$i=1;	
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
        label_cell($i,"align='center'");
	label_cell($myrow["guest_name"]);
	label_cell($myrow["fathers_name"],"align='center'");
	label_cell($myrow["gender"]== 1 ? 'Male' :  'Femail',"align='center'");
	label_cell($myrow["address"],"align='center'");
        label_cell($myrow["contact_number"],"align='center'");
        label_cell($myrow["email_id"],"align='center'");
        label_cell($myrow["porpose"],"align='center'");
        label_cell($myrow["filename"],"align='center'");
        label_cell(date('d - m - Y',strtotime($myrow['registraion_date'])),"align='center'");
	//inactive_control_cell($myrow["guest_id"], $myrow["deleted"], 'guest_registration', 'guest_id');
        inactive_control_cell2($myrow["guest_id"], $myrow["deleted"], 'guest_registration', 'guest_id','bed');
 	edit_button_cell("Edit".$myrow['guest_id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['guest_id'], _("Delete"));
	submit_js_confirm("Delete".$myrow["guest_id"], sprintf(_("You are about to Inactive Record?"), $myrow['guest_id']));
	end_row();
        $i++;
 }
 
}else{
	label_cell('No Records Found','colspan=10 align="center" size="15"');
}

inactive_control_row($th);
end_table();
echo '<br>';

//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
    
 	if ($Mode == 'Edit') {
            
		$myrow = get_guest_id($selected_id);
                
		$_POST['g_name']  = $myrow["guest_name"];
		$_POST['f_name']  = $myrow["fathers_name"];
                $_POST['gender']  = $myrow["gender"];
                $_POST['marital_status']  = $myrow["marital_status"];
                $_POST['pic']  = $myrow["id_proof"];
                $_POST['porpose']  = $myrow["porpose"];
		$_POST['line1']  = $myrow["line1"];
                $_POST['line2']  = $myrow["line2"];
                $_POST['city']  = $myrow["city"];
                $_POST['country']  = $myrow["country"];
                $_POST['state']  = $myrow["state"];
		$_POST['pin_code']  = $myrow["pin_code"];
                $_POST['email']  = $myrow["email_id"];
                $_POST['c_number']  = $myrow["contact_number"];
	}
	hidden('selected_id', $selected_id);
} 


 table_section_title(_("Guest Details"));
room_des_row_ex(_("Name *"), 'g_name', 30,null,null,null,null,null,FALSE);

room_no_row_ex(_("Father's Name *"), 'f_name', 30,null,null,null,null,null,FALSE);

kv_empl_gender_list_row(_("Gender:"), 'gender', null, true);

hrm_empl_marital_list_row(_("Marital Status:"), 'marital_status', null, true);

kv_image_row(_("Id Proof * (.jpg)") . ": " . $disp_max, 'pic', 'pic');

room_no_row_ex(_("Porpose *"), 'porpose', 30,null,null,null,null,null,FALSE);
 
 table_section_title(_("Address"));
room_des_row_ex(_("Line 1*"), 'line1', 30,null,null,null,null,null,FALSE);

room_no_row_ex(_("Line 2"), 'line2', 30,null,null,null,null,null,FALSE);

room_des_row_ex(_("City *"), 'city', 30,null,null,null,null,null,FALSE);

 country_list_row(_("Country:"), 'country', null, true, false);

state_list_row(_("State / Union Territories:"), 'state', null, $_POST['country'], true, false);

room_no_row_ex(_("Pin Code *"), 'pin_code', 30,null,null,null,null,null,FALSE);

room_no_row_ex(_("Email Id *"), 'email', 30,null,null,null,null,null,FALSE);

room_no_row_ex(_("Contact Number *"), 'c_number', 30,null,null,null,null,null,FALSE);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
