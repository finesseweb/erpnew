<?php
$page_security = 'SA_OPEN';
$path_to_root = "../../..";
$tour_request_document_folder = 'tour_request_attachments';
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");

//page(_($help_context = "Allocation Request")); 
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_tour_db.inc");

//echo "<pre>";
//print_r($_SESSION);
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
?>
<script src='<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>'></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link href="<?php echo $path_to_root?>/transport_managment/assets/css/bootstrap-datetimepicker.css" rel="stylesheet" media="screen">
<style>
#hide {
	font-size: 18px;
	font-weight: bold;
	border: 2px solid #000;
	border-radius: 7px;
}
.label {
	display: block;
	padding: .2em .6em .3em;
	font-size: 12px;
	font-weight: 700; 
	line-height: 2;
	color: #000 !important;
	text-align: left;
	white-space: nowrap;
	vertical-align: baseline;
	border-radius: .25em;
}
</style>
<div>
<h4>Billed Details</h4>
</div>
<span id="hide" style="float:right;">X</span>
<div class="user_bill_tb">
	<table class="tablestyle my_bill_list" width="60%" cellspacing="0" cellpadding="2">
		<tbody>
		<tr>
			<td class="tableheader2">SL NO.</td>
			<td class="tableheader2">Tour Request Id</td>
			<td class="tableheader2">Submission Date</td>
			<td class="tableheader2">From</td>
			<td class="tableheader2">From Date</td>
			<td class="tableheader2">To</td>
			<td class="tableheader2">To Date</td>
			<td class="tableheader2">Description/Mode</td>
			<td class="tableheader2">Class</td>
			<td class="tableheader2">Amount(Rs.)</td>
			<td class="tableheader2">Remark</td>
			<td class="tableheader2">Purpose</td>
			<td class="tableheader2">Uploaded file</td>
			<td class="tableheader2">Admin Updated Amount</td>
			<td class="tableheader2">Admin Remark</td>
			<td class="tableheader2">Status</td>
			<td class="tableheader2">Action</td>
		</tr>
		<?php 
		$tour_id=$_POST['id'];
		$submission_date=$_POST['submission_date'];
		$sql2 = "SELECT * FROM " . TB_PREF . "kv_tour_requestform WHERE tour_id='" . $tour_id . "'";
		$data2=db_query($sql2);
		$num=1;
		while ($myrow2 = db_fetch($data2)) { ?>
			<tr class="evenrow" id="row<?=$myrow2['id']?>">
				<td><?=$num++;?></td>
				<td class="tour_id" id="tour_id<?=$myrow2['id']?>"><?=$myrow2['tour_id']?></td>
				<td id="submit_date<?=$myrow2['id']?>"><?=$myrow2['submit_date']?></td>
				<td id="from_place<?=$myrow2['id']?>"><?=$myrow2['from_place']?></td>
				<td id="fdate_time<?=$myrow2['id']?>"><?=$myrow2['fdate_time']?></td>
				<td id="to_place<?=$myrow2['id']?>"><?=$myrow2['to_place']?></td>
				<td id="tdate_time<?=$myrow2['id']?>"><?=$myrow2['tdate_time']?></td>
				<td id="des_mode<?=$myrow2['id']?>"><?=$myrow2['des_mode']?></td>
				<td id="class<?=$myrow2['id']?>"><?=$myrow2['class']?></td>
				<td id="uamount<?=$myrow2['id']?>">
				<?=$myrow2['amount']?>
				<input type="text" name="admin_remark" style="display:none;">
				</td>
				<td id="remark<?=$myrow2['id']?>"><?=$myrow2['remark']?></td>
				<td id="purpose<?=$myrow2['id']?>"><?=$myrow2['purpose']?></td>
				<td><?php if($myrow2['file'] !=""){?><a href="<?=$path_to_root?>/modules/ExtendedHRM/upload/<?=$myrow2['file']?>" target="_blank" download>Download</a><?php }else{ ?><a href="#">No file Uploaded</a><?php } ?></td>
				<td id="amount<?=$myrow2['id']?>"><?=$myrow2['updated_amount']?> 
				</td>
				<td id="admin_remark<?=$myrow2['id']?>">
					<?php if($myrow2['admin_remark']){?>
					<?=$myrow2['admin_remark']?>
					<?php }else{ ?>
						Put your remark
					<?php } ?>
				</td>
				<?php if($myrow2['status']==1){ ?>
				<td><span class="status_ap">Approve</span></td>
				<?php }elseif ($myrow2['status']==3){ ?>
				<td><span class="status_ap">Billed Under Process</span></td>
				<?php }elseif ($myrow2['status']==2){ ?>
				<td><button type="button" class="btn btn-link" style="color:red" onclick="update_status(<?=$myrow2['id']?>)"><b>Billed Canceled</b></button></td>
				<?php }else{ ?>
				<td><button type="button" class="btn btn-link" style="color:red" onclick="approve(<?=$myrow2['id']?>)"><b>Pending</b></button></td>
				<?php } ?>
				<td>
				<?php if($myrow2['status']==3){ ?>
			
				<span class="">---</span>
				
				<?php }else{ ?>
				<!--<input type="button" id="edit_button<?=$myrow2['id']?>" value="Edit" class="edit" onclick="edit_row(<?=$myrow2['id']?>)">-->
				<button type="button" title="edit" class="edit" id="edit_button<?=$myrow2['id']?>" onclick="edit_row(<?=$myrow2['id']?>)"><span class="glyphicon glyphicon-edit"></span></button>
				<!--<input type="button" id="save_button<?=$myrow2['id']?>" value="Save" class="save" onclick="save_row(<?=$myrow2['id']?>)">-->
				<button type="button" title="save" class="save save_data" id="save_button<?=$myrow2['id']?>" onclick="save_row(<?=$myrow2['id']?>)"><span class="glyphicon glyphicon-saved"></span></button>
				<!--<input type="button" value="Delete" class="delete" onclick="delete_row(<?=$myrow2['id']?>)">-->
				<button type="button" title="delete" class="delete" onclick="delete_row(<?=$myrow2['id']?>)"><span class="glyphicon glyphicon-remove"></span></button>
				<?php } ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
		<?php 
			$sql22 = "SELECT * FROM " . TB_PREF . "kv_tour_requestform WHERE tour_id='" . $tour_id . "'";
			$data22=db_query($sql22);
			$total_amount=0;
			while ($myrow22 = db_fetch($data22)) { ?>
		
			<?php 
				 $total_amount +=$myrow22['amount'];
			?>
		
		<?php } ?>
		<?php 
			$sql22 = "SELECT * FROM " . TB_PREF . "kv_tour_requestform WHERE tour_id='" . $tour_id . "'";
			$data22=db_query($sql22);
			$updated_amount=0;
			while ($myrow22 = db_fetch($data22)) { ?>
		
			<?php 
				$updated_amount +=$myrow22['updated_amount'];
			?>
		
		<?php } ?>
	<div class="user_total_amt">
		<strong><?php echo $total_amount?></strong>
	</div>
	<div class="admin_total_amt">
		<strong><?php echo $updated_amount?></strong>
	</div>
	
	<div style="float:right;padding:6px">
		<?php 
			$sql221 = "SELECT * FROM " . TB_PREF . "tour_request_details WHERE tour_id='" . $tour_id . "' GROUP BY tour_id";
			$data221=db_query($sql221);
			while ($myrow221 = db_fetch($data221)) { ?>	
				<?php if($myrow221['status']==1 || $myrow221['status']==3){ ?>
				<button type="button" class="btn-success btn-sm" disabled>Approved</button>
				<?php }else{ ?>
					<button type="button" class="btn-success btn-sm"  onclick="approveBill()">Approve</button>				
				<?php
				}
			} 
		?>
	</div>
</div>
<script type="text/javascript" src="<?=$path_to_root?>/transport_managment/assets/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?=$path_to_root?>/transport_managment/assets/js/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
<script type="text/javascript">
function updateTbl123(elem){
	var id = $(elem).attr("id");
	$('#'+id).datetimepicker();
}
$("#hide").click(function(){
    $(".tbl-bill").hide();
	location.reload(); 
});

function approveBill(){
	var tour_id='<?php echo $tour_id; ?>'; 
	if (confirm('Are you sure you want to approve this?')) {
		$.ajax({
			url: '<?php echo $path_to_root ?>/modules/ExtendedHRM/manage/tour_bill_update_status.php',
			type: 'POST',
			data:{tour_id_val:tour_id} ,
			success: function (data) {
				
			  
			},
			error: function(jqXHR, textStatus, errorThrown) {
			   console.log(textStatus, errorThrown);
			}
		});
	}
	
}

function approve(no)
{
	var id=no;
 
 $.ajax({
		url: '<?php echo $path_to_root ?>/modules/ExtendedHRM/manage/approve_bill.php',
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
</script>
