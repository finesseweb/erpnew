<?php
$page_security = 'SA_OPEN';
$path_to_root = "../../..";
$tour_request_document_folder = 'tour_request_attachments';
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
add_access_extensions();
//page(_($help_context = "Allocation Request")); 
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_tour_db.inc");


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

page(_($help_context = "Tour Approval"), @$_REQUEST['popup'], false, "", $js);
simple_page_mode(true);
$selected_component = $selected_id;
?>
<html lang="en">
    <head>
        <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
        <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js" ?>"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js'></script>

    </head>
	<style>
	.tbl-bill {
		padding: 13px;
	}
	.user_bill_tb {
		padding: 13px;
		border: 1px solid #ccc;
		margin-top: 12px;
		min-height: 201px;
		background: #E1E1E1;
		overflow-x: auto;
	}
	table.tablestyle td {
		border-collapse: collapse;
		border: 1px solid #e1e1e1;
		padding: 7px;
	}
	.label {
		display: block;
		padding: .2em .6em .3em;
		font-size: 12px;
		font-weight: 700; 
		line-height: 2;
		color: #000;
		text-align: left;
		white-space: nowrap;
		vertical-align: baseline;
		border-radius: .25em;
	}
	
	.admin_remark {
		padding: 12px;
		color: Green;
	}
	.tableheader2 {
		background-color: #a6a6a6 !important;
	}
	.user_total_amt {
		text-align: center;
		margin-left: 160px;
	}
	.admin_total_amt {
		float: right;
		margin-right: 317px;
		margin-top: -20px;
	}
	.bill_process {
		margin-left:12px;
	}
	#bill_sec_view {
		background:#ccc;
	}
	.status_ap {
		color: #449D44;
		font-size: 14px;
		font-weight: bold;
	}
	.status_pn {
		color: #9B131F;
		font-size: 14px;
		font-weight: bold;
	}
	</style>
</html>
<?php

/*function displayEmployeeTourRequests($filter_tr_status, $selected_employee, $selected_department, $session_access_id,$selected_from_date,$selected_to_date) {
    $result = getTourRequestsByFilter($filter_tr_status, $selected_employee, $selected_department, $session_access_id,$selected_from_date,$selected_to_date);
    global $path_to_root, $tour_request_document_folder;
	echo "<pre>";
	print_r($_POST);
	
}*/
?>
<?php
start_form(false, true);

start_table(TABLESTYLE_NOBORDER);
start_row();
date_row(_("Submit From Date") . ":", 'from_date',20,null,'','','',null,true);
date_row(_("Submit To Date") .":", 'to_date',20,null,'','','',null,true);
department_list_row(_("Select a Department: "), 'department_id', null,	true, true, check_value('show_inactive'));
employee_list_cells1(_("Select an Employee: "), 'employee_id', null,_('All Employees'), true, check_value('show_inactive'),false,$_POST["department_id"]);
	
//custom_list_row(_("Tour Request Status:"),'filter_tr_status',null,true);
custom_list_row(_("Tour Request Status"), 'filter_tr_status', null, TRUE, false, 'tour_bill_status');
end_row();

if(list_updated('department_id') || list_updated('employee_id') || list_updated('filter_tr_status') || get_post('_from_date_changed') || get_post('_to_date_changed')){
    $Ajax->activate('_page_body');
}
end_table();
br();

end_form();
//echo "<pre>";
//print_r($_POST);
$filter_tr_status = $_POST['filter_tr_status'];
$selected_department = $_POST['department_id'];
$selected_employee = $_POST['employee_id'];
$selected_from_date = $_POST['from_date'];
$selected_to_date = $_POST['to_date'];
if($filter_tr_status =='1'){
	$status=0;
}elseif($filter_tr_status =='2'){
	$status=1;
}else{
	$status=3;
}
?>
<div id="myDIV">
<table class="tablestyle" width="100%" cellspacing="0" cellpadding="2">
<tbody>
<tr>
    
	<td class="tableheader">SL No</td>
	<td class="tableheader">Tour Request Id</td>
	<td class="tableheader">Submission Date</td>
	<td class="tableheader">From Date</td>
	<td class="tableheader">To Date</td>
	<td class="tableheader">Place of Visit</td>
	<td class="tableheader">Purpose of Visit</td>
	<td class="tableheader">Billed Amount (Rs.)</td>
	<td class="tableheader">Approved Amount (Rs.)</td>
	<td class="tableheader">Status</td>
	<td class="tableheader">Action</td>
</tr>
<?php 
$original_date = $selected_from_date;
$original_date2 = $selected_to_date;

$new_date = date("Y-m-d",strtotime($original_date));
$new_date2 = date("Y-m-d",strtotime($original_date2));

$employee_id=$_POST['employee_id'];
if($new_date && $new_date2){
	//$sql2 = "SELECT * FROM " . TB_PREF . "tour_request_details WHERE submit_date = '".$new_date."' GROUP BY tour_id";
	$sql2 = "SELECT * FROM " . TB_PREF . "tour_request_details WHERE submit_date BETWEEN '".$new_date."' AND '".$new_date2."' AND status = '".$status."' GROUP BY tour_id";
}else{
	$sql2 = "SELECT * FROM " . TB_PREF . "tour_request_details WHERE submit_date BETWEEN '".$new_date."' AND '".$new_date2."' AND status = '".$status."' AND emp_id='" . $employee_id . "' GROUP BY tour_id";
}

//echo $sql2;


//echo"jhjhj". $sql2;
$data2=db_query($sql2);
$num=1;
while ($myrow2 = db_fetch($data2)) { ?>
	<tr class="evenrow" rel="tbl_bill" id="evenrow<?=$myrow2['tour_id']?>">
		<td><?=$num++; ?></td>
		<td><?=$myrow2['tour_id']?></td>
		<td><?=$myrow2['submit_date']?></td>
		<td><?=$myrow2['departure_date']?></td>
		<td><?=$myrow2['arrival_date']?></td>
		<td><?=$myrow2['place']?></td>
		<td><?=$myrow2['purpose']?></td>
		<td><?php 
			$sql22 = "SELECT amount ,updated_amount FROM " . TB_PREF . "kv_tour_requestform WHERE tour_id='" . $myrow2['tour_id'] . "' AND status !=2";
			$data22=db_query($sql22);
			$total_amount=0;
			$updated_amount=0;
			while ($myrow22 = db_fetch($data22)) { ?>
		
			<?php 
				 $total_amount +=$myrow22['amount'];
				 $updated_amount +=$myrow22['updated_amount'];
			?>
		
		<?php } ?>
		<?=$total_amount?></td>
		<td>
		<?=$updated_amount?></td>
		<?php 
			$sql_count = "SELECT count(*) as num_row FROM fa_kv_tour_requestform WHERE tour_id='" . $myrow2['tour_id'] . "' AND status =0";
			$data_count=db_query($sql_count);
			$myrow_count = db_fetch($data_count);
			$count_val=$myrow_count['num_row'];
			
			//$sql_count23 = "SELECT count(*) as num_row2 FROM fa_kv_tour_requestform WHERE tour_id='" . $myrow2['tour_id'] . "' AND status=0 AND status=2";
			$sql_count23 = "select id, sum(case when status = 0 then 1 else 0 end) as published, sum(case when status = 2 then 1 else 0 end) as unpublished, count(*) from fa_kv_tour_requestform WHERE tour_id='" . $myrow2['tour_id'] . "'";
			$data_count23=db_query($sql_count23);
			$myrow_count23 = db_fetch($data_count23);
			//echo "<pre>";
			//print_r($myrow_count23);
			//echo 'p'.$myrow_count23['published'];
			//echo 'u'.$myrow_count23['unpublished'];
			$count_val23=$myrow_count23['num_row2'];
			if($myrow_count23['published'] == 0 && $myrow_count23['unpublished'] >=0){ 
			
				$update_query="UPDATE `fa_tour_request_details` SET status= 1 WHERE `tour_id` = '" . $myrow2['tour_id'] . "'";
				$update_data = db_query($update_query);
				
			}
		?>
		<?php if($count_val <=0){?>
		<?php if($myrow2['status']==1){ ?>
		<td><span class="status_ap">Approved</span></td>
		<?php }elseif ($myrow2['status']==3){ ?>
		<td><span class="status_ap">Billed Under Process</span></td>
		<?php }else{ ?>
		<td><span class="status_pn" >Pending</span></td>
		<?php }}else{ ?>
		<td><span class="status_pn" >Pending</span></td>
		<?php } ?>
		<td align="center" class="view_sec"><a href="javascript:void(0)" class="btn btn-success btn-sm" rel="<?=$myrow2['submit_date']?>" id="<?=$myrow2['tour_id']?>" onclick="updateTbl(this)"><b>View</b></a><?php if($count_val <=0){?><?php if($myrow2['status']==1){ ?> |<a href="javascript:void(0)" class="btn btn-primary btn-sm billedProcess" rel="<?=$myrow2['submit_date']?>" id="<?=$myrow2['tour_id']?>" onclick="BilledProcess(this)"><b>Bill Process</b></a> <?php } } ?> 
		</td>
	</tr>
<?php } ?>
</tbody>
</table>
</div>
<div class="tbl-bill">
		
</div>
<script>
var $prevLink = null, prevId = null; 
	$('.evenrow').stop(true).click(function (e) {
		e.preventDefault();
		
		if (prevId) {
			// restore current link and id if not previously cached
			$prevLink.attr('id', prevId);
			console.log($prevLink.text() + ' restored to ' + $prevLink.attr('id'));
		}
		
		// cache current link and id
		$prevLink = $(this);
		prevId = this.id;
		
		console.log($(this).text() + ' cached with ' + prevId);
		
		// change id of the element to newId
		this.id = "bill_sec_view";
		
		console.log($(this).text() + ' changed to ' + this.id);
		
		// carry out your operation on this link here
		
	});
function updateTbl(elem){
	var tour_id = $(elem).attr("id");
	var submission_date1 = $(elem).attr("rel");
	$.ajax({
		url: '<?php echo $path_to_root ?>/modules/ExtendedHRM/manage/tour_bill_admindetail.php',
		type: 'POST',
		data:{id:tour_id,submission_date:submission_date1} ,
		success: function (data) {
			//alert(data);
			$('.tbl-bill').html(data);
		  
		},
		error: function(jqXHR, textStatus, errorThrown) {
		   console.log(textStatus, errorThrown);
		}


	});
	
}
</script>
<script>

//Admin amount updateTbl
function myTrim(x) {
    return x.replace(/^\s+|\s+$/gm,'');
}
function edit_row(no)
{
 document.getElementById("edit_button"+no).style.display="none";
 document.getElementById("save_button"+no).style.display="block";

 var tour_id=document.getElementById("tour_id"+no); 
 var name=document.getElementById("amount"+no);
 var admin_remark=document.getElementById("admin_remark"+no);
	
 var amount=myTrim(name.innerHTML);
 var tour_id_data=tour_id.innerHTML;
 var admin_remark_data=admin_remark.innerHTML;
 var admin_remark_data_trim=myTrim(admin_remark_data);
 
 tour_id.innerHTML="<input type='text' id='tour_id_text"+no+"' value='"+tour_id_data+"' readonly style='border: none; background: none;'>"; 
 name.innerHTML="<input type='number' min=0 id='amount_text"+no+"' value='"+amount+"' data-validation='number' data-validation-allowing='float'>";
 admin_remark.innerHTML="<input type='text' id='admin_remark_text"+no+"' value='"+admin_remark_data_trim+"'>";

}

function save_row(no)
{
 var id= no;
 var user_amount=$('#uamount'+no).text();
 var tour_id=document.getElementById("tour_id_text"+no).value;
 var name_val=document.getElementById("amount_text"+no).value;

 if(Math.sign(name_val)=='-1'){
	alert('Please input valid Number');
	document.getElementById("amount_text"+no).style.border="2px solid red";
	return false; 
 }
 if(+name_val > +user_amount){
	alert('Please check your amount more than billed amount');
	document.getElementById("amount_text"+no).style.border="2px solid red";
	return false;
 }
 var admin_remark_val=document.getElementById("admin_remark_text"+no).value;
 document.getElementById("amount"+no).innerHTML=name_val;
 document.getElementById("admin_remark"+no).innerHTML=admin_remark_val;
 
 
 $.ajax({
		url: '<?php echo $path_to_root ?>/modules/ExtendedHRM/manage/tour_bill_update_admin.php',
		type: 'POST',
		data:{id_val:id,tour_id_val:tour_id,amount_val:name_val,remark:admin_remark_val} ,
		success: function (data) {
			document.getElementById("edit_button"+no).style.display="block";
			document.getElementById("save_button"+no).style.display="block";
		  
		},
		error: function(jqXHR, textStatus, errorThrown) {
		   console.log(textStatus, errorThrown);
		}
	});

 document.getElementById("edit_button"+no).style.display="block";
 document.getElementById("save_button"+no).style.display="none";
}


function delete_row(no)
{
	var id=no;
 
 $.ajax({
		url: '<?php echo $path_to_root ?>/modules/ExtendedHRM/manage/tour_bill_admin_delete.php',
		type: 'POST',
		data:{delete_id:id} ,
		success: function (data) {		
		  alert('Deleted Successfully');
		  document.getElementById("row"+no+"").outerHTML="";
		},
		error: function(jqXHR, textStatus, errorThrown) {
		   console.log(textStatus, errorThrown);
		}
	});
}

function BilledProcess(elem){
var id=$(elem).attr('id');

$.ajax({
		url: '<?php echo $path_to_root ?>/modules/ExtendedHRM/manage/tour_bill_process.php',
		type: 'POST',
		data:{update_id:id} ,
		success: function (data) {		
		 alert('Successfully '+id+' move to account section');
		},
		error: function(jqXHR, textStatus, errorThrown) {
		   console.log(textStatus, errorThrown);
		}
	});
}

function update_status(no)
{
	var id=no;
 
 $.ajax({
		url: '<?php echo $path_to_root ?>/modules/ExtendedHRM/manage/tour_bill_updateuserstatus.php',
		type: 'POST',
		data:{delete_id:id} ,
		success: function (data) {		
		  alert('Status Updated Successfully');
		 // document.getElementById("row"+no+"").outerHTML="";
		},
		error: function(jqXHR, textStatus, errorThrown) {
		   console.log(textStatus, errorThrown);
		}
	});
}

$.validate({
decimalSeparator : ','
});
</script>
<?php
end_page();

