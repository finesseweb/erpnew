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
<div>
<h4>Billed Details of <?=$_POST['id']?></h4>
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
		<td class="tableheader2">Admin Updated Amount(Rs.)</td>
		<td class="tableheader2">Admin Remark</td>
		<td class="tableheader2">Remark</td>
		<td class="tableheader2">Purpose</td>
		<td class="tableheader2">Uploaded file</td>
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
			<td id="tour_id<?=$myrow2['id']?>"><?=$myrow2['tour_id']?></td>
			<td id="submit_date<?=$myrow2['id']?>"><?=$myrow2['submit_date']?></td>
			<td id="from_place<?=$myrow2['id']?>"><?=$myrow2['from_place']?></td>
			<td id="fdate_time<?=$myrow2['id']?>"><?=$myrow2['fdate_time']?></td>
			<td id="to_place<?=$myrow2['id']?>"><?=$myrow2['to_place']?></td>
			<td id="tdate_time<?=$myrow2['id']?>"><?=$myrow2['tdate_time']?></td>
			<td id="des_mode<?=$myrow2['id']?>"><?=$myrow2['des_mode']?></td>
			<td id="class<?=$myrow2['id']?>"><?=$myrow2['class']?></td>
			<td id="amount<?=$myrow2['id']?>"><?=$myrow2['amount']?></td>
			<td id="amount"><?=$myrow2['updated_amount']?></td>
			<td id="amount"><?=$myrow2['admin_remark']?></td>
			<td id="remark<?=$myrow2['id']?>"><?=$myrow2['remark']?></td>
			<td id="purpose<?=$myrow2['id']?>"><?=$myrow2['purpose']?></td>
			<td><?php if($myrow2['file'] !=""){?><a href="<?=$path_to_root?>/modules/ExtendedHRM/upload/<?=$myrow2['file']?>" target="_blank" download>Download bill</a><?php }else{ ?><a href="#">No file Uploaded</a><?php } ?><?php if($myrow2['status']==0){ ?><?php if($myrow2['status']!=3){ ?><input type="file" name="file" class="file" id="file<?=$myrow2['id']?>" rel="<?=$myrow2['id']?>"/><?php } } ?></td>			
			<?php if($myrow2['status']==1){ ?>
				<td><span class="status_ap">Approve</span></td>
				<?php }elseif ($myrow2['status']==3){ ?>
				<td><span class="status_ap">Billed Under Process</span></td>
				<?php }elseif ($myrow2['status']==2){ ?>
				<td><button type="button" class="btn btn-link" style="color:red" onclick="update_status(<?=$myrow2['id']?>)"><b>Billed Canceled</b></button></td>
				<?php }else{ ?>
				<td><span class="status_pn">Pending</span></td>
			<?php } ?>
			<td>
			<?php if($myrow2['status']==1 || $myrow2['status']==3){ ?>
			
			<span class="">---</span>
			
			<?php }else{ ?>
			<button type="button" title="edit" class="edit" id="edit_button<?=$myrow2['id']?>" onclick="edit_row(<?=$myrow2['id']?>)"><span class="glyphicon glyphicon-edit"></span></button>
			<button type="button" title="save" class="save" id="save_button<?=$myrow2['id']?>" onclick="save_row(<?=$myrow2['id']?>)"><span class="glyphicon glyphicon-saved"></span></button>
			<button type="button" title="delete" class="delete" onclick="delete_row(<?=$myrow2['id']?>)"><span class="glyphicon glyphicon-remove"></span></button>
			<?php } ?>
			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>
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
$(document).ready(function(){
 $(document).on('change', '.file', function(){
 // alert('sfag');
  var data_id=$(this).attr('rel');
  var name = document.getElementById("file"+data_id).files[0].name;
  var form_data = new FormData();
  //var ext = name.split('.').pop().toLowerCase();
  //if(jQuery.inArray(ext, ['gif','png','jpg','jpeg']) == -1) 
 // {
  // alert("Invalid Image File");
  //}
  var oFReader = new FileReader();
  oFReader.readAsDataURL(document.getElementById("file"+data_id).files[0]);
  var f = document.getElementById("file"+data_id).files[0];
  var fsize = f.size||f.fileSize;
  if(fsize > 2000000)
  {
   alert("Image File Size is very big");
  }
  else
  {
   form_data.append("file", document.getElementById('file'+data_id).files[0]);
   form_data.append("id", data_id);
   console.log(document.getElementById('file'+data_id).files[0]);
   $.ajax({
    url:"<?php echo $path_to_root ?>/modules/ExtendedHRM/manage/upload.php",
    method:"POST",
    data: form_data,
    contentType: false,
    cache: false,
    processData: false,
    beforeSend:function(){
     $('#uploaded_image').html("<label class='text-success'>Image Uploading...</label>");
    },   
    success:function(data)
    {
     $('#uploaded_image').html(data);
    }
   });
  }
 });
});
</script>
