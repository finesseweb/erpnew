<?php
error_reporting(1);
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

page(_($help_context = "Tour Bill List"), @$_REQUEST['popup'], false, "", $js);
simple_page_mode(true);
$selected_component = $selected_id;
?>

<html lang="en">
    <head>
	
		 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src='<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>'></script>
		<link href="<?php echo $path_to_root?>/transport_managment/assets/css/bootstrap-datetimepicker.css" rel="stylesheet" media="screen">
        <link rel="stylesheet" href='<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>'>
        <script src='<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js" ?>'></script>
		<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js'></script>

		<style>
			.select_resource_id {
				text-align: center;
				margin: 18px;
			}
			li {
				list-style: none;
				float: left;
			}
			#dsgsdghsdh {
				display: none;
			}
			#bill_sec_view {
				background:#ccc;
			}
			.tableheader2 {
				background-color: #a6a6a6 !important;
			}
			#hide {
				font-size: 18px;
				font-weight: bold;
				border: 2px solid #000;
				border-radius: 7px;
			}
			table.tablestyle td {
				border-collapse: collapse;
				border: 1px solid #e1e1e1;
				padding: 7px;
			}
			.tbl-bill {
				padding: 13px;
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
				color: #000 !important;
				text-align: left;
				white-space: nowrap;
				vertical-align: baseline;
				border-radius: .25em;
			}
			 .tooltip {
				position: relative;
				display: inline-block;
				border-bottom: 1px dotted black;
			}

			.tooltip .tooltiptext {
				visibility: hidden;
				width: 120px;
				background-color: #555;
				color: #fff;
				text-align: center;
				border-radius: 6px;
				padding: 5px 0;
				position: absolute;
				z-index: 1;
				bottom: 125%;
				left: 50%;
				margin-left: -60px;
				opacity: 0;
				transition: opacity 0.3s;
			}

			.tooltip .tooltiptext::after {
				content: "";
				position: absolute;
				top: 100%;
				left: 50%;
				margin-left: -5px;
				border-width: 5px;
				border-style: solid;
				border-color: #555 transparent transparent transparent;
			}

			.tooltip:hover .tooltiptext {
				visibility: visible;
				opacity: 1;
			}
			.tooltip {
				display: inline !important;
				opacity: 1 !important;
			}
			.admin_remark {
				padding: 12px;
				color: Green;
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
			.user_bill_tb {
				padding: 13px;
				border: 1px solid #ccc;
				margin-top: 12px;
				min-height: 201px;
				background: #E1E1E1;
				overflow-x: auto;
			}
		</style>
    </head>
</html>
<table class="tablestyle" width="60%" cellspacing="0" cellpadding="2">
<tbody>
<tr>
    
	<td class="tableheader">SL No</td>
	<td class="tableheader">Tour Request Id</td>
	<td class="tableheader">Submission Date</td>
	<td class="tableheader">From Date</td>
	<td class="tableheader">To Date</td>
	<td class="tableheader">Place of Visit</td>
	<td class="tableheader">Purpose of Visit</td>
	<td class="tableheader">Billed Amount</td>
	<td class="tableheader">Approve Amount</td>
	<td class="tableheader">Status</td>
	<td class="tableheader">Action</td>
</tr>
<?php 
$employee_id=$_SESSION['wa_current_user']->empl_id;
$limit = 10;  
if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };  
$start_from = ($page-1) * $limit; 
$sql2 = "SELECT * FROM " . TB_PREF . "tour_request_details WHERE emp_id='" . $employee_id . "' GROUP BY tour_id ASC LIMIT $start_from, $limit";
$data2=db_query($sql2);

$num=1;
while ($myrow2 = db_fetch($data2)) { ?>
	<tr class="evenrow<?php if($_POST['id']==$myrow2['tour_id']){ ?> bill_sec_view <?php } ?>" rel="tbl_bill" id="evenrow<?=$myrow2['tour_id']?>">
		<td><?=$myrow2['id']?></td>
		<td><?=$myrow2['tour_id']?></td>
		<td><?=$myrow2['submit_date']?></td>
		<td><?=$myrow2['departure_date']?></td>
		<td><?=$myrow2['arrival_date']?></td>
		<td><?=$myrow2['place']?></td>
		<td><?=$myrow2['purpose']?></td>
		<td>
		<?php 
			$sql22 = "SELECT amount ,updated_amount  FROM " . TB_PREF . "kv_tour_requestform WHERE tour_id='" . $myrow2['tour_id'] . "' AND status !=2";
			$data22=db_query($sql22);
			$total_amount=0;
			$updated_amount=0;
			while ($myrow22 = db_fetch($data22)) { ?>
		
			<?php 
				 $total_amount +=$myrow22['amount'];
				 $updated_amount +=$myrow22['updated_amount'];
			?>
		
		<?php } ?>
		<?=$total_amount?>
		</td>
		<td>
		
		<?php 
			 $updated_amount +=$myrow22['updated_amount'];
		?>
		
		<?php if($myrow2['status']==1 || $myrow2['status']==3){?>
		<?=$updated_amount?>
		<?php }else{ ?>
		---
		<?php } ?>
		</td>
		<?php 
			$sql_count = "SELECT count(*) as num_row FROM fa_kv_tour_requestform WHERE tour_id='" . $myrow2['tour_id'] . "' AND status =0";
			$data_count=db_query($sql_count);
			$myrow_count = db_fetch($data_count);
			$count_val=$myrow_count['num_row'];
		?>
		<?php if($count_val <=0){?>
		<?php if($myrow2['status']==1){ ?>
		<td><span class="status_ap">Approved</span></td>
		<?php }elseif ($myrow2['status']==3){ ?>
		<td><span class="status_ap">Billed Under Process</span></td>
		<?php }elseif ($myrow2['status']==0){ ?>
		<td><span class="status_pn">Pending</span></td>
		<?php }}else{ ?>
		<td><span class="status_pn">Pending</span></td>
		<?php } ?>
		<td align="center" class="view_sec"><a href="javascript:void(0)" class="btn btn-success btn-sm" rel="<?=$myrow2['submit_date']?>" id="<?=$myrow2['tour_id']?>" onclick="updateTbl(this)">View</a></td>
	</tr>

<?php } ?>
</tbody></table>
<div class="tbl-bill">
		
</div>
	<?php  
        $sql = "SELECT * FROM " . TB_PREF . "tour_request_details WHERE emp_id='" . $employee_id . "' GROUP BY tour_id"; 
		$res = db_query($sql);
		$row=db_num_rows($res)	;
        $total_records = $row;  
        $total_pages = ceil($total_records / $limit);  
        $pagLink = "<nav><ul class='pagination'>";  
        for ($i=1; $i<=$total_pages; $i++) {  
                     $pagLink .= "<li><a href='tour_bill_list.php?page=".$i."'>".$i."</a></li>";  
        };  
        echo $pagLink . "</ul></nav>";  
        ?>
	

<script type="text/javascript" src="<?=$path_to_root?>/transport_managment/assets/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?=$path_to_root?>/transport_managment/assets/js/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>

<script type="text/javascript">
$(document).ready(function(){
$('.pagination').pagination({
		items: <?php echo $total_records;?>,
		itemsOnPage: <?php echo $limit;?>,
		cssStyle: 'light-theme',
		currentPage : <?php echo $page;?>,
		hrefTextPrefix : 'index.php?page='
	});
	});
</script>
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
	
	/*$('.evenrow').on('click', function(){
		var tr_id= $(this).attr('id');
		/*if($('#'+tr_id).hasClass('bill_sec_view')){
			$('#'+tr_id).removeClass('bill_sec_view');
		}else{
			$('#'+tr_id).addClass('bill_sec_view');
		}
		*/
		
		/*$(this).removeClass('bill_sec_view');
		if($(this).hasClass('check')){
			$(this).addClass('bill_sec_view');
		}*/
	/*}); */
	function updateTbl(elem){
		var tour_id = $(elem).attr("id");
		//$('#evenrow'+tour_id).addClass('check');
		//$('#evenrow'+tour_id).parents('.evenrow').removeClass('bill_sec_view');
		//$('#evenrow'+tour_id).children('.evenrow').removeClass('bill_sec_view');
		var submission_date1 = $(elem).attr("rel");
		$.ajax({
			url: '<?php echo $path_to_root ?>/modules/ExtendedHRM/manage/tour_bill_details.php',
			type: 'POST',
			data:{id:tour_id,submission_date:submission_date1} ,
			success: function (data) {
				$('.tbl-bill').html(data);
				//location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
			   console.log(textStatus, errorThrown);
			}
		});
	}
</script>
<script>
function edit_row(no)
{
 document.getElementById("edit_button"+no).style.display="none";
 document.getElementById("save_button"+no).style.display="block";
	
 var tour_id=document.getElementById("tour_id"+no);
 var submit_date=document.getElementById("submit_date"+no);
 var from_place=document.getElementById("from_place"+no);
 var fdate_time=document.getElementById("fdate_time"+no);
 var to_place=document.getElementById("to_place"+no);
 var tdate_time=document.getElementById("tdate_time"+no);
 var des_mode=document.getElementById("des_mode"+no);
 var tour_class=document.getElementById("class"+no);
 var amount=document.getElementById("amount"+no);
 var remark=document.getElementById("remark"+no);
 var purpose=document.getElementById("purpose"+no);
	
 var tour_id_data=tour_id.innerHTML;
 var submit_date_data=submit_date.innerHTML;
 var from_place_data=from_place.innerHTML;
 var fdate_time_data=fdate_time.innerHTML;
 var to_place_data=to_place.innerHTML;
 var tdate_time_data=tdate_time.innerHTML;
 var des_mode_data=des_mode.innerHTML;
 var tour_class_data=tour_class.innerHTML;
 var amount_data=amount.innerHTML;
 var remark_data=remark.innerHTML;
 var purpose_data=purpose.innerHTML;
 
	
 tour_id.innerHTML="<input type='text' id='tour_id_text"+no+"' value='"+tour_id_data+"' readonly>";
 submit_date.innerHTML="<input type='text' id='submit_date_text"+no+"' value='"+submit_date_data+"' readonly>";
 from_place.innerHTML="<input type='text' id='from_place_text"+no+"' value='"+from_place_data+"'>";
 fdate_time.innerHTML="<input type='text' id='fdate_time_text"+no+"' class='form_datetime' value='"+fdate_time_data+"' onclick='updateTbl123(this)'>";
 to_place.innerHTML="<input type='text' id='to_place_text"+no+"' value='"+to_place_data+"'>";
 tdate_time.innerHTML="<input type='text' id='tdate_time_text"+no+"' value='"+tdate_time_data+"' onclick='updateTbl123(this)'>";
 des_mode.innerHTML="<input type='text' id='des_mode_text"+no+"' value='"+des_mode_data+"'>";
 tour_class.innerHTML="<input type='text' id='tour_class_text"+no+"' value='"+tour_class_data+"'>";
 amount.innerHTML="<input type='number' min=0 id='amount_text"+no+"' value='"+amount_data+"' data-validation='number' data-validation-allowing='float'>";
 remark.innerHTML="<input type='text' id='remark_text"+no+"' value='"+remark_data+"'>";
 purpose.innerHTML="<input type='text' id='purpose_text"+no+"' value='"+purpose_data+"' readonly>";
}
function save_row(no)
{
 var id= no;
 var tour_id=document.getElementById("tour_id_text"+no).value;
 var submit_date=document.getElementById("submit_date_text"+no).value;
 var from_place=document.getElementById("from_place_text"+no).value;
 var fdate_time=document.getElementById("fdate_time_text"+no).value;
 var to_place=document.getElementById("to_place_text"+no).value;
 var tdate_time=document.getElementById("tdate_time_text"+no).value;
 var des_mode=document.getElementById("des_mode_text"+no).value;
 var tour_class=document.getElementById("tour_class_text"+no).value;
 var amount=document.getElementById("amount_text"+no).value;
 if(Math.sign(amount)=='-1'){
	alert('Please input valid Number');
	document.getElementById("amount_text"+no).style.border="2px solid red";
	return false; 
 }
 var remark=document.getElementById("remark_text"+no).value;
 var purpose=document.getElementById("purpose_text"+no).value;
 document.getElementById("tour_id"+no).innerHTML=tour_id;
 document.getElementById("submit_date"+no).innerHTML=submit_date;
 document.getElementById("from_place"+no).innerHTML=from_place;
 document.getElementById("fdate_time"+no).innerHTML=fdate_time;
 document.getElementById("to_place"+no).innerHTML=to_place;
 document.getElementById("tdate_time"+no).innerHTML=tdate_time;
 document.getElementById("des_mode"+no).innerHTML=des_mode;
 document.getElementById("class"+no).innerHTML=tour_class;
 document.getElementById("amount"+no).innerHTML=amount;
 document.getElementById("remark"+no).innerHTML=remark;
 document.getElementById("purpose"+no).innerHTML=purpose;
 document.getElementById("edit_button"+no).style.display="block";
 document.getElementById("save_button"+no).style.display="none";
	$.ajax({
		url: '<?php echo $path_to_root ?>/modules/ExtendedHRM/manage/tour_bill_update.php',
		type: 'POST',
		data:{id_val:id,tour_id_val:tour_id,submit_date_val:submit_date,from_place_val:from_place,fdate_time_val:fdate_time,to_place_val:to_place,tdate_time_val:tdate_time,des_mode_val:des_mode,tour_class_val:tour_class,amount_val:amount,remark_val:remark,purpose_val:purpose} ,
		success: function (data) {
			document.getElementById("edit_button"+no).style.display="block";
			document.getElementById("save_button"+no).style.display="block";
		  
		},
		error: function(jqXHR, textStatus, errorThrown) {
		   console.log(textStatus, errorThrown);
		}
	});
}
function delete_row(no)
{
	var id=no;
 
 $.ajax({
		url: '<?php echo $path_to_root ?>/modules/ExtendedHRM/manage/tour_bill_delete.php',
		type: 'POST',
		data:{delete_id:id} ,
		success: function (data) {		
		  alert('Deleted Successfully');
		  //document.getElementById("row"+no+"").outerHTML="";
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
</script>
<script>
$.validate({
decimalSeparator : ','
});
</script>
