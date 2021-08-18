<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'SA_OPEN';
$path_to_root="../../..";

include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/admin/db/attachments_db.inc");	
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

if (isset($_GET['dl']))
	$download_id = $_GET['dl'];
else
	$download_id = find_submit('download');


if ($download_id != -1)
{
	
	$row = get_empl_alloc_single($download_id);
	if ($row['upload_file'] != "")
	{
		
		if(in_ajax()) {
			$Ajax->redirect($_SERVER['PHP_SELF'].'?dl='.$download_id);
		} else {
		
			$type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';	
    		header("Content-type: ".$type);
	    	header('Content-Length: '.$row['filesize']);
    		header('Content-Disposition: attachment; filename='.$row['upload_file']);
    		echo file_get_contents(company_path()."/leave_attachments/".$row['unique_name']);
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
page(_("Leaves Request Report"), @$_REQUEST['popup'], false, "", $js); 	


check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));

simple_page_mode(true); 
?>

<html lang="en">
<style>
.panel-body{padding: 15px 0 0 8px;}
.form-control{width:95%;}
textarea{resize:none;}
</style>
<head>
    <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
	
    <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
   <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js"?>"></script>
   <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.min.js"?>"></script>
   
  <script>
  $( function() {
    $( "#datepicker" ).datepicker();
  } );
  </script>
</head>
<div style="display:none">
<?php 
/*
if (empty($_POST['Search']))
{
start_row();
date_cells(_("From:"), 'FromDate', '', null, -1);
date_cells(_("To:"), 'ToDate');
end_row();
start_row();
department_list_cells(_("Select a Department: "), 'department_id', null,	true, true, check_value('show_inactive'));

employee_list_cells1(_("Select an Employee: "), 'employee_id', null,	_('All Employees'), true, check_value('show_inactive'),false,$_POST["department_id"]);
	
leave_status_list_cells(_("Leave Request Status:"),'leave_status',null);

$to=$_POST['ToDate'];
$from=$_POST['FromDate'];
$department_id=$_POST['department_id'];
$employee_id=$_POST['employee_id'];
$status_id=$_POST['leave_status'];
	$query = "SELECT req.*,des_group.name as grp_name,des.name as desig_name,dept.description as department,type.leave_type as leaves,empl.empl_firstname as employee FROM ".TB_PREF."kv_allocation_request AS req  LEFT JOIN ".TB_PREF."kv_desig_group AS des_group ON req.desig_group_id=des_group.id LEFT JOIN ".TB_PREF."designation_master AS des ON req.desig_id=des.id LEFT JOIN ".TB_PREF."kv_departments AS dept ON req.dept_id=dept.id LEFT JOIN ".TB_PREF."kv_type_leave_master AS type ON req.type_leave=type.type_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl  ON empl.id=req.employees_id  WHERE req.from_date >= '" .$from. "'
		AND req.to_date <= '" .$to . "' AND req.status=".db_escape($status_id)."";
	if ($department_id != null)
		$query .= " AND dept_id = ".db_escape($department_id);
	if ($employee_id != null)
		$query .= " AND employees_id = ".db_escape($employee_id);
	
$res=db_query($query);
} */
?>
</div>
<body>
<?php



if (isset($_GET['filterType'])) // catch up external links
	$_POST['filterType'] = $_GET['filterType'];
if (isset($_GET['trans_no']))
	$_POST['trans_no'] = $_GET['trans_no'];

if (isset($_GET['delete_id'])){
	$selected_del_id = $_GET['delete_id'];

	if (key_in_foreign_table($selected_del_id, 'kv_empl_salary', 'empl_id')){
		
		display_error(_("Cannot delete this Employee because Payroll Processed to this employee And it will be  added in the financial Transactions."));
	}else {
		delete_employee($selected_del_id);
		$filename = company_path().'/images/empl/'.empl_img_name($selected_del_id).".jpg";
		if (file_exists($filename))
			unlink($filename);
		display_notification(_("Selected Employee has been deleted."));
		$Ajax->activate('_page_body');	
	}
}

if (isset($_POST['Search']))
{
		
	$to=$_POST['ToDate'];
	$from=$_POST['FromDate'];
	$department_id=$_POST['department_id'];
	$employee_id=$_POST['employee_id'];
	$status_id=$_POST['leave_status'];
	$second_no_of_days=$_POST['second_no_of_days'];
	
	/* if($second_no_of_days ='0'){
	$query = "SELECT req.*,des_group.name as grp_name,des.name as desig_name,dept.description as department,type.leave_type as leaves,empl.empl_firstname as employee FROM ".TB_PREF."kv_allocation_request AS req  LEFT JOIN ".TB_PREF."kv_desig_group AS des_group ON req.desig_group_id=des_group.id LEFT JOIN ".TB_PREF."designation_master AS des ON req.desig_id=des.id LEFT JOIN ".TB_PREF."kv_departments AS dept ON req.dept_id=dept.id LEFT JOIN ".TB_PREF."kv_type_leave_master AS type ON req.type_leave=type.type_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl  ON empl.id=req.employees_id  WHERE req.request_date >= '" .$from. "' AND req.request_date <= '" .$to . "' AND req.status=".db_escape($status_id)."";
	
	}
	else{ */
		
	/*	$query = "SELECT req.allocate_id,req.employees_id,req.from_date,req.to_date,req.no_of_days,req.request_date,req.status,req.type_leave,req.comments,CASE WHEN (rqstatus.from_date=req.second_from_date) THEN rqstatus.checks END AS checks,CASE WHEN (rqstatus.from_date=req.second_from_date) THEN rqstatus.approved_leaves END AS approved_leaves,des_group.name as grp_name,des.name as desig_name,dept.description as department,type.leave_type as leaves,empl.empl_firstname as employee FROM ".TB_PREF."kv_allocation_request AS req,".TB_PREF."kv_allocation_request_status AS rqstatus  LEFT JOIN ".TB_PREF."kv_desig_group AS des_group ON req.desig_group_id=des_group.id LEFT JOIN ".TB_PREF."designation_master AS des ON req.desig_id=des.id LEFT JOIN ".TB_PREF."kv_departments AS dept ON req.dept_id=dept.id LEFT JOIN ".TB_PREF."kv_type_leave_master AS type ON req.type_leave=type.type_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl  ON empl.id=req.employees_id WHERE req.allocate_id==rqstatus.allocate_id AND req.request_date >= '" .$from. "' AND req.request_date <= '" .$to . "' AND req.status=".db_escape($status_id)."
		UNION 
		SELECT req.allocate_id,req.employees_id,req.second_from_date,req.second_to_date,req.second_no_of_days,req.request_date,CASE  WHEN(rqstatus.req_status = 2)  THEN  rqstatus.req_status  WHEN (rqstatus.req_status = 3) THEN rqstatus.req_status WHEN (rqstatus.req_status = 1) THEN req.status END AS status,req.type_leave,rqstatus.comments ,CASE WHEN (rqstatus.from_date=req.second_from_date) THEN rqstatus.checks END AS checks,CASE WHEN (rqstatus.from_date=req.second_from_date) THEN rqstatus.approved_leaves END AS approved_leaves,des_group.name as grp_name,des.name as desig_name,dept.description as department,type.leave_type as leaves,empl.empl_firstname as employee FROM ".TB_PREF."kv_allocation_request AS req,".TB_PREF."kv_allocation_request_status AS rqstatus  LEFT JOIN ".TB_PREF."kv_desig_group AS des_group ON req.desig_group_id=des_group.id LEFT JOIN ".TB_PREF."designation_master AS des ON req.desig_id=des.id LEFT JOIN ".TB_PREF."kv_departments AS dept ON req.dept_id=dept.id LEFT JOIN ".TB_PREF."kv_type_leave_master AS type ON req.type_leave=type.type_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl  ON empl.id=req.employees_id WHERE req.allocate_id==rqstatus.allocate_id AND req.request_date >= '" .$from. "' AND req.request_date <= '" .$to . "' AND req.second_leave_type != '-1' AND req.status=".db_escape($status_id)."";	*/
		if($status_id ==1){
		$query="SELECT req.allocate_id,req.employees_id,req.from_date,req.to_date,req.no_of_days,req.request_date,req.status,req.type_leave,req.comments,null AS checks,null AS approved_leaves,req.reason,des_group.name as grp_name,des.name as desig_name,dept.description as department,type.leave_type as leaves,empl.empl_firstname as employee,null as request_status,req.request_date FROM ".TB_PREF."kv_allocation_request AS req  LEFT JOIN ".TB_PREF."kv_desig_group AS des_group ON req.desig_group_id=des_group.id LEFT JOIN ".TB_PREF."designation_master AS des ON req.desig_id=des.id LEFT JOIN ".TB_PREF."kv_departments AS dept ON req.dept_id=dept.id LEFT JOIN ".TB_PREF."kv_type_leave_master AS type ON req.type_leave=type.type_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl  ON empl.id=req.employees_id WHERE req.request_date >= '" .$from. "' AND req.request_date <= '" .$to . "'  AND req.status=".db_escape($status_id)."
		UNION
		SELECT req.allocate_id,req.employees_id,req.second_from_date AS from_date,req.second_to_date AS to_date,req.second_no_of_days AS no_of_days,req.request_date,req.status,req.second_leave_type AS type_of_leave,req.comments,null AS checks,null AS approved_leaves,req.second_reason AS reason,des_group.name as grp_name,des.name as desig_name,dept.description as department,type.leave_type as leaves,empl.empl_firstname as employee,null as request_status,req.request_date FROM ".TB_PREF."kv_allocation_request AS req  LEFT JOIN ".TB_PREF."kv_desig_group AS des_group ON req.desig_group_id=des_group.id LEFT JOIN ".TB_PREF."designation_master AS des ON req.desig_id=des.id LEFT JOIN ".TB_PREF."kv_departments AS dept ON req.dept_id=dept.id LEFT JOIN ".TB_PREF."kv_type_leave_master AS type ON req.type_leave=type.type_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl ON empl.id=req.employees_id WHERE req.request_date >= '" .$from. "' AND req.request_date <= '" .$to . "' AND req.status=".db_escape($status_id)." AND req.second_leave_type != '-1'";
		}
		else{
		$query="SELECT rqstatus.allocate_id,rqstatus.employees_id,rqstatus.from_date,rqstatus.to_date,rqstatus.no_of_days,null,CASE  WHEN(rqstatus.req_status = 2)  THEN  rqstatus.req_status  WHEN (rqstatus.req_status = 3) THEN rqstatus.req_status WHEN (rqstatus.req_status = 1) THEN '1' END AS status,rqstatus.type_leave,rqstatus.comments ,CASE WHEN (rqstatus.from_date=req.second_from_date) THEN rqstatus.checks END AS checks, rqstatus.approved_leaves AS approved_leaves,rqstatus.reason,null,null,null,null,empl.empl_firstname as employee,rqstatus.status_id AS request_status,rqstatus.request_date FROM ".TB_PREF."kv_allocation_request_status AS rqstatus  LEFT JOIN ".TB_PREF."kv_allocation_request AS req  ON req.allocate_id=rqstatus.allocate_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl  ON empl.id=rqstatus.employees_id WHERE  rqstatus.request_date >= '" .$from. "' AND rqstatus.request_date <= '" .$to . "' AND rqstatus.req_status=".db_escape($status_id)."";
		//display_error($query);
	 }
	if ($department_id != null)
		$query .= " AND dept_id = ".db_escape($department_id);
	if ($employee_id != null)
		$query .= " AND employees_id = ".db_escape($employee_id);

	//display_error($query);
	$res=db_query($query);
	
	$Ajax->activate('trans_tbl');
}


function download_link($row){
			
  	return button('download'.$row, _("Download"), _("Download"), ICON_DOWN);
}

/* function get_request_report($request_status){
	
	$query3 = "SELECT reqst.* FROM ".TB_PREF."kv_allocation_request_status AS reqst  WHERE reqst.status_id=".db_escape($request_status)."";
	// display_error($query3);
	$result3= db_query($query3);
	return db_fetch_row($result3);
}

 if (isset($_GET['id']))
{
	
	//start_table(TABLESTYLE2);
	$myrow3 = get_request_report($_GET['id']);
	$i = 0;
	//if($myrow3['req_status'] == 2){
	start_table(TABLESTYLE);

	//$th = array(_("Name"), _("Employee Name"));
	//_("Type of Leave"), _("Reason"), _("From Date"),_("To Date"), _("No.of Days"), _("Status"),_("Comments"),_(""),_(""),_(""),_("Approved Leaves"),_(""),_(""));
	
	//table_header($th);
		
		div_start('stat_tabl');
		
		label_row(_("Reference"),$myrow3["allocate_id"]);
		label_row(_("Employee Name"),$myrow3["employee"]);
		if ($myrow3["type_leave"] ==1)
		{
			label_row(_("Type of Leave"),'Casual');
		}
		else if($myrow3["type_leave"] ==2){
			label_row(_("Type of Leave"),'Medical Leave');
		}
		else if($myrow3["type_leave"] ==3){
			label_row(_("Type of Leave"),'Vacation Leave');
		}
		else{
		   label_row(_("Type of Leave"),'');
		}
		label_row( _("Reason"),$myrow3["reason"]);
		label_row( _("From Date"),dates2sql($myrow3["from_date"]));
		label_row(_("To Date"),dates2sql($myrow3["to_date"]));
		label_row(_("No.of Days"),$myrow3["no_of_days"]);
		$_POST['comments']  = $myrow3["comments"];
		$_POST['approved_leaves']  = $myrow3["approved_leaves"];
		
		//display_error($_POST['approved_leaves']);
		end_table();
     end_table();
	//}
}  */

if(list_updated('status')){
//display_error($_POST['status']);
	$Ajax->activate('trans_tbl');
}
div_start('trans_tbl');

start_form();
		
start_table(TABLESTYLE_NOBORDER);
start_row();


date_cells(_("From:"), 'FromDate', '', null, -1);
date_cells(_("To:"), 'ToDate');
end_row();
start_row();
department_list_cells(_("Select a Department: "), 'department_id', null,	true, true, check_value('show_inactive'));

employee_list_cells1(_("Select an Employee: "), 'employee_id', null,	_('All Employees'), true, check_value('show_inactive'),false,$_POST["department_id"]);
	
leave_status_list_cells(_("Leave Request Status:"),'leave_status',null);
submit_cells('Search', _("Search"), '', '', 'default');
end_row();
end_table();

echo "<br>";
echo "<br>";

//end
start_table(TABLESTYLE);


$th = array(_("Reference"), _("Employee Name"),
	_("Type of Leave"), _("Reason"), _("From Date"),_("To Date"), _("No.of Days"), _("Status"),_("Comments"),_(""),_(""),_(""),_("Approved Leaves"),_(""),_(""));
table_header($th);


function get_trans($request_id,$type)
{
	$label = $request_id;
	$class ='';
	$id=$request_id;
	$icon = '';
	$viewer = $path_to_root."modules/ExtendedHRM/manage/";
	if ($type == 'Leave Request')
		$viewer .= "leave_request_view.php?id=".$request_id;
	
	return viewer_link($label, $viewer, $class, $id,  $icon);
	
}

$k = 0; 
$i=1; //row colour counter
while ($myrow = db_fetch($res))
{
	if($myrow['status']==1 && $myrow['type_leave'] !=-1){
		$sql2="SELECT * FROM ".TB_PREF."kv_allocation_request_status WHERE type_leave =".db_escape($myrow['type_leave'])." AND allocate_id=".db_escape($myrow['allocate_id'])."";
		$query2=db_query($sql2);
		$req = db_fetch($query2);
		
	if($req['type_leave'] ==''){
	alt_table_row_color($k);

	//label_cell(get_trans_view_str($myrow["type"],$myrow["trans_no"],$myrow['ref']));
	
	label_cell($myrow["allocate_id"]);
	label_cell($myrow["employee"]);
	if ($myrow["type_leave"] ==1)
	{
		label_cell('Casual');
	}
	else if($myrow["type_leave"] ==2){
		label_cell('Medical Leave');
	}
	else if($myrow["type_leave"] ==3){
		label_cell('Vacation Leave');
	}
	else{
	   label_cell('');
	}
	label_cell($myrow["reason"]);
	label_cell(dates2sql($myrow["from_date"]));
	label_cell(dates2sql($myrow["to_date"]));
	label_cell($myrow["no_of_days"]);
	
	
	//label_cell($myrow["upload_file"]);
	
?>


<?php  
	  $query = "SELECT empl_id FROM ".TB_PREF."kv_empl_info WHERE id=".db_escape($myrow['employees_id']);
	  $result1= db_query($query);
	  $results = db_fetch_row($result1);
	  $emp_code = $results[0];

	  $sql ="SELECT status_id,status_name FROM ".TB_PREF."kv_leave_request_status";
	  
      $res1= db_query($sql); 	  
 ?>

<?php  if(($myrow['status'] !=2) && ($myrow['status'] !=3)){ ?>
<td>

<select name="status<?php echo $i;?>" id="status<?php echo $i;?>" onchange="statuschange(this.value,<?php echo $myrow['allocate_id'];?>,<?php echo $i;?>)">
   <!--  <option value="">Select</option>  -->
	<?php while($result = db_fetch($res1)){
	  ?>  
   <option value="<?php echo $result['status_id']; ?>"><?php echo $result['status_name']; ?></option>
   <?php } ?>
</select>
</td>
<?php } else if($myrow['status'] ==2){ ?>
        <td><?php //echo'Approved'; ?>
		<select name="status<?php echo $i;?>" id="status<?php echo $i;?>" onchange="statuschange(this.value,<?php echo $myrow['allocate_id'];?>,<?php echo $i;?>)">
  
		<?php while($result = db_fetch($res1)){
		$selected='';
		if($result['req_status'] == $myrow['status']){
			$selected="selected='selected'";
		?>  
		<option value="<?php echo $result['status_id']; ?>"  <?php echo $selected;?> ><?php echo $result['status_name']; ?></option>
		<?php } 
		} ?>
		</select>
		</td>
<?php } 


 else if($myrow['status'] ==3){ ?>
<td><?php  echo'Rejected'; ?></td>
 <?php  } ?>
<?php if(empty($myrow['comments'])) { ?>
<td style="display:none" id="test<?php echo $i;?>">
<textarea id="comments<?php echo $i;?>" style="display:none"></textarea>
</td>

<?php } else {
 ?>
<?php if(!empty($myrow["comments"]) ){ ?>
    <td><textarea id="comments<?php echo $i;?>" style=""><?php echo $myrow3['comments']; ?></textarea></td>
<?php } 
else if(($myrow['status'] !=2) && ($myrow['status'] !=1)){ ?>
     
   <td><?php // echo ''; ?></td>  
<?php } else{ ?>

<td><?php echo ''; ?></td> 

  
<?php } }?>

<?php //} ?>
</td>
<?php if($myrow['status'] =2) { 
		if(empty($myrow['checks'])) {?>
		<td></td>
 <td style="" id="check<?php echo $i;?>" >
<input type="checkbox" id="cbox<?php echo $i;?>" class="checks<?php echo $i;?>" onclick="edit_request(<?php echo $i?>)" style="display:none;" />
</td>
<?php }else{ ?>

   <td><?php // echo ''; ?></td>  

<?php  } } ?>

<?php 	
//edit_button_cell("Edit".$myrow['allocate_id'], _("Edit"));
//label_cell('');
label_cell('<a onclick="javascript:openWindow(this.href,this.target); return false;" href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep810.php?PARAM_0='.$myrow['request_status'].'&rep_v=yes" target="_blank" class="printlink"> <img src="'.$path_to_root.'/themes/default/images/print.png" width="12" height="12" border="0" title="Print"> </a>'); 

label_cell(download_link($myrow["allocate_id"]));  

?>
<!-- <td id="hidefields2<?php // echo $i;?>"></td>  -->
<!-- <td style="display:none" id="ap_leaves<?php //echo $i;?>">
<input type="text" id="appr_leaves<?php //echo $i;?>" style="display:none">
</td>  -->
<?php if(empty($myrow['approved_leaves'])) { ?>
<td style="" id="ap_leaves<?php echo $i;?>">
<input type="text" id="appr_leaves<?php echo $i;?>" style="display:none">
</td>

<?php } else {
 ?>
<?php if(!empty($myrow["approved_leaves"]) ){
      if($myrow['status'] =2) { ?>
    <td><input type="text" id="appr_leaves<?php echo $i;?>" style="" value="<?php echo $myrow3['approved_leaves']; ?>"></td>
<?php } else if($myrow['status'] =1 || $myrow['status'] =3){ ?>
<td><?php echo ''; ?></td> 
<?php }
 } else{ ?>
<td><?php echo ''; ?></td> 

  
<?php } } ?>

<?php if($myrow['status'] = 1) { ?>
<!-- <td id="hidefield<?php // echo $i;?>"></td>  -->
<td><input type="button" name="submt" value="Submit" id="submt" style="padding:4px;margin-right:8px;" onclick="select_status(<?php echo $myrow['allocate_id'];?>,<?php echo $i;?>)" /></td>
<?php } else { ?>


<td><?php echo 'Submitted'; ?></td> 
<td><?php label_cell(pager_link( _("Edit"),
		"/modules/ExtendedHRM/inquires/leaves_request_report_edit.php?id=".$myrow["request_status"], ICON_EDIT)); ?>
</td>
<?php } ?>

 
<input type="hidden" name="leave_type" id="leave_type<?php echo $i;?>" value="<?php echo $myrow["type_leave"];?>">
<input type="hidden" name="reason" id="reason<?php echo $i;?>" value="<?php echo $myrow["reason"]; ?>">
<input type="hidden" name="from_date" id="from_date<?php echo $i;?>" value="<?php echo $myrow["from_date"];?>">
<input type="hidden" name="to_date" id="to_date<?php echo $i;?>" value="<?php echo $myrow["to_date"];?>">
<input type="hidden" name="num_days" id="num_days<?php echo $i;?>" value="<?php echo $myrow["no_of_days"];?>">
<input type="hidden" name="employees_id" id="employees_id<?php echo $i;?>" value="<?php echo $myrow["employees_id"];?>">
<input type="hidden" name="requested_date" id="requested_date<?php echo $i;?>" value="<?php echo $myrow["request_date"];?>">

<input type="hidden" name="st_id" id="st_id<?php echo $i;?>" value="<?php echo $myrow["request_status"];?>">

<?php	end_row();

	$i++;
	}
}

else{
	
alt_table_row_color($k);

	//$trandate = sql2date($myrow["prevent_id"]);
	//label_cell(get_trans($myrow["allocate_id"],'Leave Request'));
	//label_cell(get_trans_view_str($myrow["type"],$myrow["trans_no"],$myrow['ref']));
	
	label_cell($myrow["allocate_id"]);
	label_cell($myrow["employee"]);
	if ($myrow["type_leave"] ==1)
	{
		label_cell('Casual');
	}
	else if($myrow["type_leave"] ==2){
		label_cell('Medical Leave');
	}
	else if($myrow["type_leave"] ==3){
		label_cell('Vacation Leave');
	}
	else{
	   label_cell('');
	}
	label_cell($myrow["reason"]);
	label_cell(dates2sql($myrow["from_date"]));
	label_cell(dates2sql($myrow["to_date"]));
	label_cell($myrow["no_of_days"]);
	
	
	//label_cell($myrow["upload_file"]);
	
?>


<?php  
	  $query = "SELECT empl_id FROM ".TB_PREF."kv_empl_info WHERE id=".db_escape($myrow['employees_id']);
	  $result1= db_query($query);
	  $results = db_fetch_row($result1);
	  $emp_code = $results[0];

	  $sql ="SELECT status_id,status_name FROM ".TB_PREF."kv_leave_request_status";
	  
      $res1= db_query($sql); 	  
 ?>

<?php  if(($myrow['status'] !=2) && ($myrow['status'] !=3)){ ?>
<td>

<select name="status<?php echo $i;?>" id="status<?php echo $i;?>" onchange="statuschange(this.value,<?php echo $myrow['allocate_id'];?>,<?php echo $i;?>)">
   <!--  <option value="">Select</option>  -->
	<?php while($result = db_fetch($res1)){
	  ?>  
   <option value="<?php echo $result['status_id']; ?>"><?php echo $result['status_name']; ?></option>
   <?php } ?>
</select>
</td>
<?php } else if($myrow['status'] ==2){ ?>
        <td><?php //echo'Approved'; ?>
		<select name="status<?php echo $i;?>" id="status<?php echo $i;?>" onchange="statuschange(this.value,<?php echo $myrow['allocate_id'];?>,<?php echo $i;?>)">
  
		<?php 
		while($result = db_fetch($res1)){
		$selected='';
		//display_error($result['status']);
		if($myrow3['req_status'] = $result['status']){
			$selected="selected='selected'";
		}
		?>  
		<option value="<?php echo $result['status_id']; ?>"  <?php //echo $selected; ?> ><?php echo $result['status_name']; ?></option>
		<?php  
		} ?>
		</select>
		</td>
<?php } 


 else if($myrow['status'] ==3){ ?>
<td><?php  echo'Rejected'; ?></td>
 <?php  } ?>
<?php if(empty($myrow['comments'])) { ?>
<td style="display:none" id="test<?php echo $i;?>">
<textarea id="comments<?php echo $i;?>" style="display:none"></textarea>
</td>

<?php } else {
 ?>
<?php if(!empty($myrow["comments"]) ){ ?>
    <td><textarea id="comments<?php echo $i;?>" style=""><?php echo $myrow3['comments']; ?></textarea></td>
<?php } 
else if(($myrow['status'] !=2) && ($myrow['status'] !=1)){ ?>
     
   <td><?php // echo ''; ?></td>  
<?php } else{ ?>

<td><?php echo ''; ?></td> 

  
<?php } }?>

</td>
<?php  if($myrow['status'] =2) { 
		//if(empty($myrow['checks'])) { ?>
 <td style="display:none;" id="check<?php echo $i;?>" >
<input type="checkbox" id="cbox<?php echo $i;?>" class="checks<?php echo $i;?>" onclick="edit_request(<?php echo $i?>)" style="display:none;"/>
</td>
<?php  }else{ ?>

  <td><?php  echo ''; ?></td> 

<?php  } ?>

<?php 	
//edit_button_cell("Edit".$myrow['allocate_id'], _("Edit"));
//label_cell('');
label_cell('<a onclick="javascript:openWindow(this.href,this.target); return false;" href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep810.php?PARAM_0='.$myrow['request_status'].'&rep_v=yes" target="_blank" class="printlink"> <img src="'.$path_to_root.'/themes/default/images/print.png" width="12" height="12" border="0" title="Print"> </a>'); 

label_cell(download_link($myrow["allocate_id"]));  

?>
<!-- <td id="hidefields2<?php // echo $i;?>"></td>  -->
<!-- <td style="display:none" id="ap_leaves<?php //echo $i;?>">
<input type="text" id="appr_leaves<?php //echo $i;?>" style="display:none">
</td>  -->
<?php if(empty($myrow['approved_leaves'])) { ?>
<td style="" id="ap_leaves<?php echo $i;?>">
<input type="text" id="appr_leaves<?php echo $i;?>" style="display:none">
</td>

<?php } else {
 ?>
<?php if(!empty($myrow["approved_leaves"])){ ?>
<td></td>
    <td><input type="text" id="appr_leaves<?php echo $i;?>" style="" value="<?php echo $myrow3['approved_leaves']; ?>"></td>
<?php } 
else if(($myrow['status'] !=2) && ($myrow['status'] !=1)){ ?>
     
   <td><?php // echo ''; ?></td>  
<?php } else{ ?>

<td><?php echo ''; ?></td> 

<?php } } ?>

<?php if($myrow['status'] == 1) { ?>
<td id="hidefield<?php echo $i;?>"></td>
<td></td>
<td><input type="button" name="submt" value="Submit" id="submt" style="padding:4px;margin-right:8px;" onclick="select_status(<?php echo $myrow['allocate_id'];?>,<?php echo $i;?>)" /></td>
<?php } else { ?>


<td><?php echo 'Submitted'; ?></td>
<td><?php label_cell(pager_link( _("Edit"),
		"/modules/ExtendedHRM/inquires/leaves_request_report_edit.php?id=".$myrow["request_status"], ICON_EDIT)); ?></td> 
<?php } ?>

<input type="hidden" name="leave_type" id="leave_type<?php echo $i;?>" value="<?php echo $myrow["type_leave"];?>">
<input type="hidden" name="reason" id="reason<?php echo $i;?>" value="<?php echo $myrow["reason"]; ?>">
<input type="hidden" name="from_date" id="from_date<?php echo $i;?>" value="<?php echo $myrow["from_date"];?>">
<input type="hidden" name="to_date" id="to_date<?php echo $i;?>" value="<?php echo $myrow["to_date"];?>">
<input type="hidden" name="num_days" id="num_days<?php echo $i;?>" value="<?php echo $myrow["no_of_days"];?>">
<input type="hidden" name="employees_id" id="employees_id<?php echo $i;?>" value="<?php echo $myrow["employees_id"];?>">
<input type="hidden" name="requested_date" id="requested_date<?php echo $i;?>" value="<?php echo $myrow["request_date"];?>">

<input type="hidden" name="st_id" id="st_id<?php echo $i;?>" value="<?php echo $myrow["request_status"];?>">

<?php	end_row();

	$i++;
	
}

	}
	

end_table(2);
div_end();

//---------------------------------------------------------------------------------------
end_page();
?>

</body>
</html>
<script type="text/javascript">
$( document ).ready(function() {
	//alert('dsfsdf');
    //$('.checks').hide();
	//$('#ed'+num+'').hide();
});
function select_status(request_id,num){
	var request_id = request_id;
	//alert(request_id);
	var status = $('#status'+num+'').val();
	var leave_type = $('#leave_type'+num+'').val();
	var reason = $('#reason'+num+'').val();
	var from_date = $('#from_date'+num+'').val();
	var to_date = $('#to_date'+num+'').val();
	var num_days = $('#num_days'+num+'').val();
	var employees_id = $('#employees_id'+num+'').val();
	var requested_date = $('#requested_date'+num+'').val();
	//alert(status);
	
	if(status != 1){
	 var comments = $('#comments'+num+'').val();
	 //alert(comments);
	}
	if(status == 2){
	 //alert(status);
	 var checks = $('#cbox'+num).val();
	 var approve_leaves=$('#appr_leaves'+num+'').val();
	 $('#appr_leaves'+num+'').hide();
	}
	
	 $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/modules/ExtendedHRM/manage/ajax_update_status.php";?>',
			data: {request_id:request_id,status:status,comments : comments,checks : checks,approve_leaves : approve_leaves,leave_type : leave_type,reason : reason,from_date : from_date,to_date : to_date,num_days : num_days,employees_id : employees_id ,requested_date :requested_date}
		}).done(function( data ) { 
			window.location.reload();
		});
	
}
 function statuschange(val,request_id,num){
	var status = val;
//	alert(status);
	
	if(status == 3){
		//alert(status);
	 $('#comments'+num+'').show();
        $('#test'+num+'').show();
		$('#hidefield'+num+'').hide();
		$('#check'+num+'').hide();
		$('#ed'+num+'').hide();
		$('#hidefields2'+num+'').show();
		$('#appr_leaves'+num+'').hide();
		
	}
	else if(status == 2){
		$('#comments'+num+'').hide();
        $('#test'+num+'').hide();
		$('#hidefield'+num+'').show();
		$('#check'+num+'').show();
		$('#ed'+num+'').show();
		$('#hidefields2'+num+'').hide();
		$('.checks'+num+'').show();
	}
	else{
		//alert(status);
		$('#comments'+num+'').hide();
		$('#hidefield'+num+'').show();
		$('#check'+num+'').hide();
		$('#ed'+num+'').hide();
        $('#test'+num+'').show();
		$('#hidefields2'+num+'').hide();
		$('#appr_leaves'+num+'').hide();
	}	
}
  
function edit_request(num){
   //var checks = $('#cbox'+num).val();
   var checks = $('#cbox'+num+'').is(':checked') ? 1 : 0;
   //alert(checks);
   if(checks == 1){
	   $('#ed'+num+'').show();
	   $('#comments'+num+'').show();
        $('#test'+num+'').show();
		$('#appr_leaves'+num+'').show();
		$('#ap_leaves'+num+'').show();
		$('#hidefield'+num+'').show();
		$('#hidefields2'+num+'').show();
		$('#check'+num+'').show();
   }
   else{
		$('#check'+num+'').show();
		$('#ed'+num+'').hide();
		$('#comments'+num+'').hide();
		$('#test'+num+'').hide();
		$('#appr_leaves'+num+'').hide();
		$('#ap_leaves'+num+'').show();
		$('#hidefields2'+num+'').hide();
   }
}	
  
</script>
