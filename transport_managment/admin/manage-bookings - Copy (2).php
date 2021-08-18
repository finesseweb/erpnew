<?php
	$page_security = 'SA_TRANSADMIN';
if (!@$_GET['popup'])
	$path_to_root = "../..";
else	
	$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/transport_managment/admin/includes/config.php");

if (!@$_GET['popup'])
	page(_($help_context = "Booking Managment"));
if(strlen($_SESSION["wa_current_user"]->email)==0)
  {
header('location:index.php');
}
//if(strlen($_SESSION["wa_current_user"]->email)==0){	
	//header('location:index.php');
else{
	if(isset($_REQUEST['eid'])){
		$eid=intval($_GET['eid']);
		$status="2";
		$sql = "UPDATE tblbooking SET Status=:status WHERE  id=:eid";
		$query = $dbh->prepare($sql);
		$query -> bindParam(':status',$status, PDO::PARAM_STR);
		$query-> bindParam(':eid',$eid, PDO::PARAM_STR);
		$query -> execute();
		$msg="Booking Successfully Cancelled";
	}
	if(isset($_REQUEST['aeid'])){
		$aeid=intval($_GET['aeid']);
		$status=1;
		$sql = "UPDATE tblbooking SET Status=:status WHERE  id=:aeid";
		$query = $dbh->prepare($sql);
		$query -> bindParam(':status',$status, PDO::PARAM_STR);
		$query-> bindParam(':aeid',$aeid, PDO::PARAM_STR);
		$query -> execute();

		$msg="Booking Successfully Confirmed";
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
	.booling_tim_his table {
		width: 100%;
	}
	.booling_tim_his td, .booling_tim_his th {
		border: 1px solid #dddddd;
		text-align: left;
		padding: 8px;
	}

	.booling_tim_his tr:nth-child(even) {
		background-color: #dddddd;
	}
	.no_record {
		text-align: center;
		font-size: 15px;
		color: red;
		margin-bottom: 15px;
	}
	</style>

</head>

<body>
	<div class="">
		<div class="content-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<!-- Zero Configuration Table -->
						<div class="panel panel-default">
							<div class="panel-heading">Bookings Info</div>
							<div class="panel-body">
							<?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
								<table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
										<th>#</th>
											<th>Booked By</th>
											<th>Vehicle</th>
											<th>From date</th>
											<th>To date</th>
											<th>Message</th>
											<th>Status</th>
											<th>Booking date</th>
											<th>Action</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
										<th>#</th>
										<th>Booked By</th>
											<th>Vehicle</th>
											<th>From date</th>
											<th>To date</th>
											<th>Message</th>
											<th>Status</th>
											<th>Posting date</th>
											<th>Action</th>
										</tr>
									</tfoot>
									<tbody>

									<?php   
											$sql = "SELECT fa_users.real_name,tblbrands.BrandName,tblvehicles.VehiclesTitle,tblbooking.FromDate,tblbooking.name,tblbooking.ToDate,tblbooking.message,tblbooking.VehicleId as vid,tblbooking.Status,tblbooking.PostingDate,tblbooking.id  from tblbooking join tblvehicles on tblvehicles.id=tblbooking.VehicleId join fa_users on fa_users.email=tblbooking.userEmail join tblbrands on tblvehicles.VehiclesBrand=tblbrands.id ORDER BY Status,FromDate DESC ";
											$query = $dbh -> prepare($sql);
											$query->execute();
											$results=$query->fetchAll(PDO::FETCH_OBJ);
											$cnt=1;
											if($query->rowCount() > 0)
											{
											foreach($results as $result)
											{				?>	
										<tr>
											<td><?php echo htmlentities($cnt);?></td>
											<td><?php echo htmlentities($result->real_name);?></td>
											<td><a href="edit-vehicle.php?id=<?php echo htmlentities($result->vid);?>"><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></td>
											<td><?php echo htmlentities($result->FromDate);?></td>
											<td><?php echo htmlentities($result->ToDate);?></td>
											<td><?php echo htmlentities($result->message);?></td>
											<td><?php 
if($result->Status==0)
{
echo htmlentities('Not Confirmed yet');
} else if ($result->Status==1) {
echo htmlentities('Confirmed');
}
 else{
 	echo htmlentities('Cancelled');
 }
										?>
										
										
										</td>
											<td><?php echo htmlentities($result->PostingDate);?></td>
										<td>
										<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal<?=$result->id?>">Check Availability</button>
										
										<!-- Modal -->
										<div id="myModal<?=$result->id?>" class="modal fade" role="dialog">
										  <div class="modal-dialog">

											<!-- Modal content-->
											<div class="modal-content">
											  <div class="modal-header">
												<button type="button" class="close" data-dismiss="modal">&times;</button>
											  </div>
											  <div class="modal-body">
												<?php  
													$d12=$result->FromDate;
													$data_from_f= explode(' ',$d12);
													$d122=$result->ToDate;
													$data_from_t= explode(' ',$d122); 
													$sql_booked= "SELECT * FROM `tblbooking` WHERE `FromDate` LIKE '%$data_from_f[0]%' AND `ToDate` LIKE '%$data_from_t[0]%' AND `VehicleId` = '$result->vid' AND `Status` = 1";

													$query2 = $dbh -> prepare($sql_booked);
													$query2->execute();
													$value=$query2->fetchAll(PDO::FETCH_OBJ);
													//echo "<pre>";
													//print_r($value);
												?>
												<div class="booling_tim_his">
													<h4>User Name    : <?php echo htmlentities($result->name);?></h4>
													<h4>Vehicle Name : <?php echo htmlentities($result->VehiclesTitle);?></h4>
													<h4>Booking Date : <b>From</b> <?php echo htmlentities($result->FromDate);?> <b>To</b> <?php echo htmlentities($result->ToDate);?> </h4>
													<?php if($value){ ?>
													<table>
														<tr>
															<th>Vehical</th>
															<th>From</th>
															<th>To</th>
														</tr>
														<?php foreach($value as $data_his){ ?>
														<tr>
															<td><?php echo $result->VehiclesTitle;?></td>
															<td><?php echo $data_his->FromDate?></td>
															<td><?php echo $data_his->ToDate?></td>
														</tr>
														<?php } ?>
													</table>
													<?php }else {?>
													<div class="no_record">No history availble on this date</div>
													<?php } ?>
												</div>
												<div class="booling_tim_his" style="text-align:center;"> 
													<a href="manage-bookings.php?aeid=<?php echo htmlentities($result->id);?>" class="btn btn-success btn-sm" onclick="return confirm('Do you really want to Confirm this booking')"> Confirm</a>
													<a href="manage-bookings.php?eid=<?php echo htmlentities($result->id);?>" class="btn btn-danger btn-sm" onclick="return confirm('Do you really want to Cancel this Booking')"> Cancel</a>
												</div>
												
											  </div>
											  <div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
											  </div>
											</div>

										  </div>
										</div>
										
										</td>

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
</html>
<?php } ?>
