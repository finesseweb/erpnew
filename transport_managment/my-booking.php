<?php
$page_security = 'SA_MYBOOKING';
if (!@$_GET['popup'])
	$path_to_root = "..";
else	
	$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");
//include_once($path_to_root . "/includes/ui.inc");
//include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/transport_managment/includes/config.php");

if (!@$_GET['popup'])
	page(_($help_context = "My Booking History"));
if(strlen($_SESSION["wa_current_user"]->email)==0)
  {
header('location:index.php');
}
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
?><!DOCTYPE HTML>
<html lang="en">
<head>
<!-- Font awesome -->
	<link rel="stylesheet" href="admin/css/font-awesome.min.css">
	<!-- Sandstone Bootstrap CSS -->
	<link rel="stylesheet" href="admin/css/bootstrap.min.css">
	<!-- Bootstrap Datatables -->
	<link rel="stylesheet" href="admin/css/dataTables.bootstrap.min.css">
	<!-- Bootstrap social button library -->
	<link rel="stylesheet" href="admin/css/bootstrap-social.css">
	<!-- Bootstrap select -->
	<link rel="stylesheet" href="admin/css/bootstrap-select.css">
	<!-- Bootstrap file input -->
	<link rel="stylesheet" href="admin/css/fileinput.min.css">
	<!-- Awesome Bootstrap checkbox -->
	<link rel="stylesheet" href="admin/css/awesome-bootstrap-checkbox.css">
	<!-- Admin Stye -->
	<link rel="stylesheet" href="admin/css/style.css">
	<style>
		.my_vehicles_list ul.vehicle_listing {
			padding: 0px;
			margin: 0 0 50px;
		}
		.my_vehicles_list ul.vehicle_listing li {
			list-style: none;
			border-bottom: #e6e6e6 solid 1px;
			padding: 14px 0 22px;
			overflow: hidden;
			position: relative;
		}
		.vehicle_img {
			float: left;
			margin-right: 22px;
			width: 22%;
		}
		.my_vehicles_list ul.vehicle_listing li a {
			color: #111;
			background: none;
			padding: 7px;
			width: 126px;
		}
		.vehicle_img img {
			max-width: 100%;
		}
		.profile_wrap {
			padding: 16px 5px;
		}
		h6 {
			font-size: 18px;
			font-weight:bold;
		}
		.my_vehicles_list ul.vehicle_listing li .btn:hover {
			color: #fff;
		}
		.vehicle_title {
			float: left;
			padding: 12px 0;
			width: 40%;
		}
		.vehicle_status {
			float: right; 
			text-align: right; 
			width: 25%; 
		}
		.btn.outline.active-btn:hover {
			background: #089901;
			color: #fff;
		}
		.btn.outline.active-btn {
			border-color: #089901;
			color: #089901;
		}
		a{
			background:none !important;
		}
		a {
			background: none !important;
			color: #2974BF;
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
											<th>Vehicle</th>
											<th>From</th>
											<th>To</th>
											<th>Message</th>
											<th>Status</th>
											<!--<th>Posting date</th>-->
											<th>Action</th>
										</tr>
									</thead>
									<tbody>

									<?php
										$useremail=$_SESSION["wa_current_user"]->email;
										 $sql = "SELECT tblvehicles.Vimage1 as Vimage1,tblvehicles.VehiclesTitle,tblvehicles.id as vid,tblbrands.BrandName,tblbooking.id,tblbooking.FromDate,tblbooking.fromplace,tblbooking.ToDate,tblbooking.toplace,tblbooking.message,tblbooking.Status  from tblbooking join tblvehicles on tblbooking.VehicleId=tblvehicles.id join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand where tblbooking.userEmail=:useremail";
										$query = $dbh -> prepare($sql);
										$query-> bindParam(':useremail', $useremail, PDO::PARAM_STR);
										$query->execute();
										$results=$query->fetchAll(PDO::FETCH_OBJ);
										$cnt=1;
										if($query->rowCount() > 0)
										{
										foreach($results as $result)
										{  
									 ?>
										<tr>
											<td><?php echo htmlentities($cnt);?></td>
											<td><a href="edit-vehicle.php?id=<?php echo htmlentities($result->vid);?>"><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></td>
											<td><?php echo htmlentities($result->fromplace);?> ,<?php echo htmlentities($result->FromDate);?></td>
											<td><?php echo htmlentities($result->toplace);?> ,<?php echo htmlentities($result->ToDate);?></td>
											<td><?php echo htmlentities($result->message);?> </td>
											<td>
												<?php if($result->Status==1) { ?>
												<a href="#" class="btn outline btn-xs active-btn">Confirmed</a>

												<?php } else if($result->Status==2) { ?>
												<a href="#" class="btn outline btn-xs">Cancelled</a>
												<?php } else { ?>
												<a href="#" class="btn outline btn-xs">Not Confirm yet</a>
												<?php } ?>
											</td>
											<!--<td><?php echo htmlentities($result->PostingDate);?></td>-->
										<td>
											<a href="my-booking.php?eid=<?php echo htmlentities($result->id);?>" onclick="return confirm('Do you really want to Cancel this Booking')"> Cancel</a>
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


<!-- Scripts -->
<!-- Loading Scripts -->
	<script src="admin/js/jquery.min.js"></script>
	<script src="admin/js/bootstrap-select.min.js"></script>
	<script src="admin/js/bootstrap.min.js"></script>
	<script src="admin/js/jquery.dataTables.min.js"></script>
	<script src="admin/js/dataTables.bootstrap.min.js"></script>
	<script src="admin/js/Chart.min.js"></script>
	<script src="admin/js/fileinput.js"></script>
	<script src="admin/js/chartData.js"></script>
	<script src="admin/js/main.js"></script>
</body>
</html>
<?php } ?>
