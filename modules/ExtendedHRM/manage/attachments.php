<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'HR_ATTACHMENT';
$path_to_root="../../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/admin/db/attachments_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
add_access_extensions();
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
                    if(ini_get('zlib.output_compression')){//Off comression
                        ini_set('zlib.output_compression', 'Off');
                    }
			$ext = pathinfo($row['filename'], PATHINFO_EXTENSION);//get file extension
                        //echo company_path()."/attachments/emplcv/".$row['empl_id']."/".$row['unique_name'].'.'.$doc_ext;exit;
                        $fullPath = company_path()."/attachments/emplcv/".$row['empl_id']."/".$row['unique_name'].'.'.$ext;
                        $fsize = filesize($fullPath);
                        if( file_exists($fullPath) ){
                            switch ($ext) {
                                case "pdf": $ctype="application/pdf"; break;
                                case "exe": $ctype="application/octet-stream"; break;
                                case "zip": $ctype="application/zip"; break;
                                case "doc": $ctype="application/msword"; break;
                                case "xls": $ctype="application/vnd.ms-excel"; break;
                                case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                                case "gif": $ctype="image/gif"; break;
                                case "png": $ctype="image/png"; break;
                                case "jpeg": $ctype="image/jpg"; break;
                                case "jpg": $ctype="image/jpg"; break;
                                default: $ctype="application/force-download";
                            }

                            header("Pragma: public"); // required
                            header("Expires: 0");
                            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                            header("Cache-Control: private",false); // required for certain browsers
                            header("Content-Type: $ctype");
                            header("Content-Disposition: attachment; filename=\"".basename($fullPath)."\";" );
                            header("Content-Transfer-Encoding: binary");
                            header("Content-Length: ".$fsize);
                            ob_clean();
                            flush();
                            readfile( $fullPath );
                        }
                        
                        exit();
		}
	}	
}

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
page(_($help_context = "Attach Employee Document"), false, false, "", $js);

simple_page_mode(true);

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));

//---------------------------------------------------------------------------------------

if (isset($_POST['empl_id']) && !empty($_POST['empl_id']) || get_post('year')){
	 $empl_id = $_POST['empl_id'];
         $f_year = $_POST['year'];
}
         

//echo  $empl_id;
if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM'){

		if(!isset($max_image_size))
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
		
		$dir = company_path().'/attachments/emplcv/'.$_POST['empl_id'];
		if (!file_exists($dir)){
			mkdir($dir);
		}
		$filename = basename($_FILES['kv_attach_name']['name']);
		$filesize = $_FILES['kv_attach_name']['size'];
		$filetype = $_FILES['kv_attach_name']['type'];

		// file name compatible with POSIX
		// protect against directory traversal
		if ($Mode == 'UPDATE_ITEM')
		{
		    $row = get_employee_cvdwnld($empl_id);
		    if ($row['filename'] == "")
        		exit();
			$unique_name = $row['unique_name'];
			if ($filename && file_exists($dir."/".$unique_name)){
				unlink($dir."/".$unique_name);
				$unique_name = uniqid('');
			}
			
		}
		else
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
				$kv_file_name = $_POST['empl_id'].'.'.$doc_ext;
			
			if ( $_FILES['kv_attach_name']['size'] > ($max_image_size * 1024)) { //File Size Check
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
			
		if ((list($width, $height, $type, $attr) = getimagesize($_FILES['kv_attach_name']['tmp_name'])) !== false){
				
				$imagetype = $type;
			}
			else
				$imagetype = false;
			
                         
		if ($imagetype != IMAGETYPE_GIF && $imagetype != IMAGETYPE_JPEG && $imagetype != IMAGETYPE_PNG && $imagetype == '' && ($_FILES["kv_attach_name"]["type"] != "application/pdf")){	
				display_warning( _($_FILES["kv_attach_name"]["type"].'Only graphics files and pdf files can be uploaded'));
				$upload_file ='No';
			}
		
		if ($upload_file == 'Yes'){	
			if ($Mode == 'ADD_ITEM')
			{
				
				$result  =  move_uploaded_file($tmpname, $dir."/".$unique_name.'.'.$doc_ext);
				//display_error($result);die;
					add_cv($_POST['empl_id'], $_POST['empl_firstname'], $_POST['cv_title'], $filename,$unique_name,$_POST['year']); 
                                        $selected_id = -1;
					display_notification(_("Employee Document has been attached!."));
			}
			else
			{
				
					//display_error($unique_name);die;
				/*	if ($filename && file_exists($dir."/".$unique_name)){
						unlink($dir."/".$unique_name);
						$unique_name = uniqid('');
					} */
			//	display_error($dir);
				
				$result  =  move_uploaded_file($tmpname, $dir."/".$unique_name.'.'.$doc_ext);
					update_cv($selected_id,$_POST['empl_id'], $_POST['empl_firstname'], $_POST['cv_title'], $filename,$unique_name,$_POST['year']);
                                        $selected_id = -1;
				
				display_notification(_("Attachment has been updated.")); 
					
			}
			
	     }
			
		// }
        $Ajax->activate('_page_body');
         //$Mode = 'RESET';		
}

if ($Mode == 'Delete')
{
	$row = get_employee_cvdwnld($selected_id);
	$dir =  company_path()."/attachments/emplcv/".$row['filename'];
	if (file_exists($dir."/".$row['unique_name']))
		unlink($dir."/".$row['unique_name']);
	delete_cv($selected_id);	
	display_notification(_("Employee Document has been deleted.")); 
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	unset($_POST['empl_id']);
	unset($_POST['empl_firstname']);
	$selected_id = -1;
}

function viewing_controls()
{
	global $selected_id;
start_form(true);	
    start_table(TABLESTYLE_NOBORDER);

	start_row();
        kv_fiscalyears_list_cells(_("Fiscal Year:"), 'year', null, true);
	employee_list_cells(_("Select an Employee: "), 'empl_id', null,	_('New Employee'), true, check_value('show_inactive'));
	if (list_updated('selected_id'))
		$selected_id = -1;

	end_row();
    end_table(1);
    end_form();

}

function edit_link($row){
  	return button('Edit'.$row["id"], _("Edit"), _("Edit"), ICON_EDIT);
	hidden('id',$row["id"]);
}

function view_link($row){
  	return button('view'.$row["id"], _("View"), _("View"), ICON_VIEW);
}

function download_link($row){
		
  	return button('download'.$row["id"], _("Download"), _("Download"), ICON_DOWN);
}

function delete_link($row){
	submit_js_confirm("Delete".$row["id"], sprintf(_("You are about to delete a Department Do you want to continue?"), $row["id"]));
  	return button('Delete'.$row["id"], _("Delete"), _("Delete"), ICON_DELETE);
	hidden('id',$row["id"]);
}

function display_rows($empl_id,$f_year){
	$sql = "SELECT id,empl_id,empl_firstname,cv_title FROM ".TB_PREF."kv_empl_cv WHERE empl_id=".db_escape($empl_id)." AND f_year =". db_escape($f_year); 
        
	//display_error($sql);
	$cols = array(
		_("Ref") => array(''),
		_("Employee Id") => array('fun'=>'empl_id', 'ord'=>''),
		_("Employee Id") => array('name'=>'empl_id'),
	    _("Employee Name") => array('name'=>'empl_firstname'),
	    _("Document Title") => array('name'=>'cv_title'),	    
	    	//array('insert'=>true, 'fun'=>'edit_link'),
	    	//array('insert'=>true, 'fun'=>'view_link'),
	    	array('insert'=>true, 'fun'=>'download_link'),
	    	array('insert'=>true, 'fun'=>'delete_link')
	    );	
		$table =& new_db_pager('cv_table', $sql, $cols);

		$table->width = "60%";

		display_db_pager($table);
}

//---------------------------------------------------------------------------------------



viewing_controls();


start_form(true);

display_rows($empl_id,$f_year);
hidden('empl_id',$empl_id);
   hidden('year', $f_year);

br(2);

if (db_has_employees()) {
	start_table(TABLESTYLE_NOBORDER);
	start_row();
    //stock_items_list_cells(_("Select an item:"), 'selected_id', null, _('New item'), true, check_value('show_inactive'));
	
	$new_item = get_post('selected_id')=='';
	//check_cells(_("Show inactive:"), 'show_inactive', null, true);
	end_row();
	end_table();

	if (get_post('_show_inactive_update')) {
		$Ajax->activate('selected_id');
		set_focus('selected_id');
	} 
}
else{
	hidden('selected_id', get_post('selected_id'));
}
start_table(TABLESTYLE2);

if ($selected_id != -1){
	
	if($Mode == 'Edit')	{	
	   
		hidden('selected_id',$selected_id);
		$row = get_employee_cvdwnld($selected_id);
		$_POST['empl_id']  = $row["empl_id"];
		$_POST['empl_firstname']  = $row["empl_firstname"];
		$_POST['cv_title']  = $row["cv_title"];
		hidden('empl_id', $row['empl_id']);
                   hidden('year', $row['f_year']);
		hidden('empl_firstname', $row['empl_firstname']);	
		label_row(_("Employee Id"), $row['empl_id']);
		label_row(_("Employee Name"), $row['empl_firstname']);
	} else {
		$row = get_employee($empl_id);	
        	
		$_POST['empl_id']  = $row["empl_id"];
		$_POST['empl_firstname']  = $row["empl_firstname"];
		hidden('empl_id', $row['empl_id']);
		
		hidden('empl_firstname', $row['empl_firstname']);
		$_POST['cv_title'] = ''; 
		label_row(_("Employee Id"), $row['empl_id']);
		label_row(_("Employee Name"), $row['empl_firstname']);
	}
}
if($selected_id == -1){
$row = get_employee($empl_id);	
        	
		$_POST['empl_id']  = $row["empl_id"];
		$_POST['empl_firstname']  = $row["empl_firstname"];
		hidden('empl_id', $row['empl_id']);
                hidden('year', $row['f_year']);
		
		hidden('empl_firstname', $row['empl_firstname']);
		
		label_row(_("Employee Id"), $row['empl_id']);
		label_row(_("Employee Name"), $row['empl_firstname']);
}
text_row_ex(_("Document Title").':', 'cv_title', 40);

kv_doc_row(_("Upload Document") . ":", 'kv_attach_name', 'kv_attach_name');

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'process');

end_form();

end_page();
?>