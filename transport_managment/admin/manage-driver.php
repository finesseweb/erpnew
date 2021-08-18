<?php
$page_security = 'SA_MANAGEVEHICAL';
if (!@$_GET['popup'])
	$path_to_root = "../..";
else	
	$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/transport_managment/admin/includes/config.php");
if (!@$_GET['popup'])
	page(_($help_context = "Manage Driver"));
if(strlen($_SESSION["wa_current_user"]->email)==0)
  {
header('location:index.php');
}
else{

if(isset($_REQUEST['del']))
{
	$delid=intval($_GET['del']);
	$status=1;
	$sql = "delete from driver_details WHERE  id=:delid";
	$query = $conn->prepare($sql);
	$query -> bindParam(':delid',$delid, PDO::PARAM_STR);
	$query -> execute();
	$msg="Vehicle record deleted successfully";
}


 ?>

<!doctype html>
<html lang="en" class="no-js">

<head>
	<!-- Font awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Sandstone Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Bootstrap Datatables -->
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<!-- Bootstrap social button library -->
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<!-- Bootstrap select -->
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<!-- Bootstrap file input -->
	<link rel="stylesheet" href="css/fileinput.min.css">
	<!-- Awesome Bootstrap checkbox -->
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<!-- Admin Stye -->
	<link rel="stylesheet" href="css/style.css">
  <style>
	.errorWrap {
		padding: 10px;
		margin: 0 0 20px 0;
		background: #fff;
		border-left: 4px solid #dd3d36;
		-webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
		box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
	}
	.succWrap{
		padding: 10px;
		margin: 0 0 20px 0;
		background: #fff;
		border-left: 4px solid #5cb85c;
		-webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
		box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
	}
		</style>

</head>

<body>
	<div class="">
		<div class="">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">
						<!-- Zero Configuration Table -->
						<div class="panel panel-default">
							<div class="panel-heading">
								Vehicle Details
								<div class="pull-right">
									+<a href="<?php echo $path_to_root ?>/transport_managment/admin/add_deriver.php">Add Driver</a>
								</div>
							</div>
							<div class="panel-body">
							<?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
								<table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
										<th>#</th>
											<th>Name</th>
											<th>Vehicle</th>
											<th>Address </th>
											<th>Phone No.</th>
											<th>Licence NO.</th>
											<th>Valid Upto</th>
											<th>Aadhar NO.</th>
											<th>Aadhar Copy</th>
											<th>Licence Copy</th>
											<th>Status</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>

<?php $sql = "SELECT * FROM fa_driver_details";
$query = $conn->prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{		
		?>	
										<tr>
											<!--Driver Booked -->
											<?php   
											
												$sql_booked = "SELECT * FROM tblvehicles WHERE DriverId=$result->id";
												$result_booked = mysqli_query($sql_booked);
												$value = mysqli_fetch_object($result_booked);
												
											?>
											<td><?php echo htmlentities($cnt);?></td>
											<td><?php echo htmlentities($result->name);?></td>
											<?php if($value->VehiclesTitle){?>
											<td><?php echo htmlentities($value->VehiclesTitle);?></td>
											<?php }else { ?>
											<td>---</td>
											<?php }?>
											<td><?php echo htmlentities($result->address);?></td>
											<!--<td><?php //echo htmlentities($result->PricePerDay);?></td>-->
											<td><?php echo htmlentities($result->phone_no);?></td>
											
												<td><?php echo htmlentities($result->licence_no);?></td>
												<td><?php echo htmlentities($result->valid_upto);?></td>
												<td><?php echo htmlentities($result->aadhar_no);?></td>
												<td style="width:3%"><a target="_blank" href="<?=$path_to_root?>/transport_managment/admin/img/vehicleimages/<?php echo $result->aadhar_copy;?>">Aadhar Copy</a></td>
											    <td style="width:3%"><a target="_blank" href="<?=$path_to_root?>/transport_managment/admin/img/vehicleimages/<?php echo $result->licence_copy;?>">Licence Copy</a></td>

												
												<td><?php if($result->status==1){?><button type="button" class="btn btn-success btn-sm available_id" rel="<?=$result->status;?>" id="<?=$result->id;?>">Available</button><?php }else{ ?><button type="button" class="btn btn-danger btn-sm available_id" rel="<?=$result->status;?>" id="<?=$result->id;?>">Not Available</button><?php } ?>|<?php if($value->DriverId == $result->id){?><button type="button" class="btn btn-info btn-sm">Booked</button><?php }else{ ?><button type="button" class="btn btn-warning btn-sm">Not Booked</button><?php } ?></td>
		<td><a href="<?php echo $path_to_root ?>/transport_managment/admin/edit-driver.php?id=<?php echo $result->id;?>"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
<a href="<?php echo $path_to_root ?>/transport_managment/admin/manage-driver.php?del=<?php echo $result->id;?>" onclick="return confirm('Do you want to delete');"><i class="fa fa-close"></i></a></td>
										</tr>
										<?php $cnt=$cnt+1; }} ?>
										
									</tbody>
								</table>

						

							</div>
						</div>

					

					</div>
				</div>

			</div>
		</div>
	</div>

	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
</body>
<script>

$(".available_id").click( function()
   {
	var id=$(this).attr('id');
	var status=$(this).attr('rel');
	 var data = 'number=' + id;
	//alert(id);
	$.ajax({
        url: '<?php echo $path_to_root ?>/transport_managment/admin/status_page.php',
        type: 'POST',
        data:{ids:id,status_val:status} ,
        success: function (data) {
            setTimeout(function(){// wait for 5 secs(2)
				   location.reload(); // then reload the page.(3)
			  }, 2000);            

        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }


    });
   }
);
function getStatus(){
	alert('love');
	
	//var id=$(this).attr('id');
	//alert(id);
}
</script>
</html>
<?php } ?>
