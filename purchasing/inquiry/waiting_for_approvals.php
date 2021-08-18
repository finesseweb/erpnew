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
$page_security = 'SA_SUPPTRANSVIEW';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/purchasing/includes/purchasing_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

if (isset($_GET['type']))
{
  if($_GET['type']==18)
  $trans_type= ST_PURCHORDER;
  if($_GET['type']==60)
  $trans_type= ST_PURCHENQUIRY;
  if($_GET['type']==70)
  $trans_type= ST_PURCHQUOTE;
	if($_GET['type']==90)
  $trans_type= ST_PURCHINDENT;
}

if (!@$_GET['popup'])
{
	$js = "";
	global $trans_type;
	
	
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
	if($trans_type == 90){
		page(_($help_context = "Waiting For Approvals"), false, false, "", $js);
	}else if($trans_type == 60){
		page(_($help_context = "Search RFQ"), false, false, "", $js);
	}else if($trans_type == 70){
		page(_($help_context = "Search Purchase Quotation"), false, false, "", $js);
	}else 
	page(_($help_context = "Search Purchase Orders"), false, false, "", $js);
}
if (isset($_GET['order_number']))
{
	$order_number = $_GET['order_number'];
}

?>
<html lang="en">
<head>
    <link rel="stylesheet" href="<?php echo $path_to_root . "/purchasing/js/jquery-ui.css" ?>">
    <script src="<?php echo $path_to_root . "/purchasing/js/jquery-1.10.2.js" ?>"></script>
   <script src="<?php echo $path_to_root . "/purchasing/js/jquery-ui.js"?>"></script>
  <script>
  $( function() {
    $( "#datepicker" ).datepicker();
  } );
  </script>
</head>
<body>
<table align="center" width="80%">
<thead>
<th class="tableheader">Indent No.</th>
<th  class="tableheader">Reference</th>
<th  class="tableheader">Indent Date</th>
<th  class="tableheader">Status</th>
</thead>
<tbody>
<?php $sql = "SELECT * FROM ".TB_PREF."purch_orders  WHERE trans_type='90' order by order_no DESC ";
$res = db_query($sql,""); 
while($result = db_fetch($res)) { ?>
<tr class="evenrow">
<td align="center"><a href="<?php echo $path_to_root . "/purchasing/view/view_indent.php?trans_no=".$result['order_no'];?>" target="_blank"><?php echo $result['order_no']; ?></a></td>
<td align="center"><?php echo "IND-".$result['reference']; ?></td>
<td align="center"><?php echo sql2date($result['ord_date']); ?></td>

<?php if($result['approved_status'] == '0'){ ?>
<td align="center"><input type="button" name="approved" value="Approve" id="approved" style="padding:4px;margin-right:8px;" onclick="select_approved(<?php echo $result['order_no'];?>,'approved')" />
	<input type="button" name="cancelled" value="Cancel" style="padding:4px;" onclick="select_cancelled(<?php echo $result['order_no'];?>,'cancelled')" /></td>

<?php } else if($result['approved_status'] == '1') { ?>
<td align="center"><?php echo 'Approved'; ?></td>
<?php } else if($result['approved_status'] == '2'){ ?>
<td align="center"><?php echo 'Cancelled'; ?></td>

<?php } ?>

</tr>
<?php } ?>
</tbody>
</table>
</body>
<script>
function select_approved(order_no,type){
	var order_no = order_no;
	var type = type;
	 $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/purchasing/ajax_update_status.php";?>',
			data: {order_no:order_no,type:type}
		}).done(function( data ) { 
			//$( "#cust_name").val( data);
			window.location.reload();
		});
	
}
function select_cancelled(order_no,type){
	var order_no = order_no;
	var type = type;
	 $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/purchasing/ajax_update_status.php";?>',
			data: {order_no:order_no,type:type}
		}).done(function( data ) { 
			//$( "#cust_name").val( data);
			window.location.reload();
		});
	
}

</script>
</html>
