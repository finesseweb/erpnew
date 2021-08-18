<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'HR_ATTACHMENT_VIEW';
$path_to_root="../../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();
include_once($path_to_root . "/admin/db/attachments_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

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
if(get_post('year')){
	 $empl_id = $_SESSION['wa_current_user']->empl_id;;
         $f_year = $_POST['year'];
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
	//employee_list_cells(_("Select an Employee: "), 'empl_id', null,	_('New Employee'), true, check_value('show_inactive'));
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

function display_rows($empl_id,$fyear){
	$sql = "SELECT id,empl_id,empl_firstname,cv_title FROM ".TB_PREF."kv_empl_cv WHERE empl_id=".db_escape($empl_id)." And f_year =".db_escape($fyear); 
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
	    	//array('insert'=>true, 'fun'=>'delete_link')
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
		
		//label_row(_("Employee Id"), $row['empl_id']);
		//label_row(_("Employee Name"), $row['empl_firstname']);
}
//text_row_ex(_("Document Title").':', 'cv_title', 40);

//kv_doc_row(_("Upload Document") . ":", 'kv_attach_name', 'kv_attach_name');

end_table(1);

//submit_add_or_update_center($selected_id == -1, '', 'process');

end_form();

end_page();
?>