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
	page(_($help_context = "Post Vehicle"));
if(strlen($_SESSION["wa_current_user"]->email)==0)
  {
header('location:index.php');
}
else{ 

if(isset($_POST['submit']))
  {
$vehicletitle=$_POST['vehicletitle'];
$brand=$_POST['brandname'];
$driver=$_POST['driver'];
$vehicleoverview=$_POST['vehicalorcview'];
$priceperday=$_POST['priceperday'];
$fueltype=$_POST['fueltype'];
$modelyear=$_POST['modelyear'];
$seatingcapacity=$_POST['seatingcapacity'];
$vimage1=$_FILES["img1"]["name"];
/*$vimage2=$_FILES["img2"]["name"];
$vimage3=$_FILES["img3"]["name"];
$vimage4=$_FILES["img4"]["name"];
$vimage5=$_FILES["img5"]["name"];*/
$airconditioner=$_POST['airconditioner'];
$powerdoorlocks=$_POST['powerdoorlocks'];
$antilockbrakingsys=$_POST['antilockbrakingsys'];
$brakeassist=$_POST['brakeassist'];
$powersteering=$_POST['powersteering'];
$driverairbag=$_POST['driverairbag'];
$passengerairbag=$_POST['passengerairbag'];
$powerwindow=$_POST['powerwindow'];
$cdplayer=$_POST['cdplayer'];
$centrallocking=$_POST['centrallocking'];
$crashcensor=$_POST['crashcensor'];
$leatherseats=$_POST['leatherseats'];
move_uploaded_file($_FILES["img1"]["tmp_name"],"img/vehicleimages/".$_FILES["img1"]["name"]);
move_uploaded_file($_FILES["img2"]["tmp_name"],"img/vehicleimages/".$_FILES["img2"]["name"]);
move_uploaded_file($_FILES["img3"]["tmp_name"],"img/vehicleimages/".$_FILES["img3"]["name"]);
move_uploaded_file($_FILES["img4"]["tmp_name"],"img/vehicleimages/".$_FILES["img4"]["name"]);
move_uploaded_file($_FILES["img5"]["tmp_name"],"img/vehicleimages/".$_FILES["img5"]["name"]);

$sql="INSERT INTO tblvehicles(VehiclesTitle,VehiclesBrand,VehiclesOverview,FuelType,ModelYear,SeatingCapacity,Vimage1,AirConditioner,PowerDoorLocks,AntiLockBrakingSystem,BrakeAssist,PowerSteering,DriverAirbag,PassengerAirbag,PowerWindows,CDPlayer,CentralLocking,CrashSensor,LeatherSeats,DriverId) VALUES(:vehicletitle,:brand,:vehicleoverview,:fueltype,:modelyear,:seatingcapacity,:vimage1,:airconditioner,:powerdoorlocks,:antilockbrakingsys,:brakeassist,:powersteering,:driverairbag,:passengerairbag,:powerwindow,:cdplayer,:centrallocking,:crashcensor,:leatherseats,:driver)";
$query = $conn->prepare($sql);
$query->bindParam(':vehicletitle',$vehicletitle,PDO::PARAM_STR);
$query->bindParam(':brand',$brand,PDO::PARAM_STR);
$query->bindParam(':vehicleoverview',$vehicleoverview,PDO::PARAM_STR);
//$query->bindParam(':priceperday',$priceperday,PDO::PARAM_STR);
$query->bindParam(':fueltype',$fueltype,PDO::PARAM_STR);
$query->bindParam(':modelyear',$modelyear,PDO::PARAM_STR);
$query->bindParam(':seatingcapacity',$seatingcapacity,PDO::PARAM_STR);
$query->bindParam(':vimage1',$vimage1,PDO::PARAM_STR);
$query->bindParam(':airconditioner',$airconditioner,PDO::PARAM_STR);
$query->bindParam(':powerdoorlocks',$powerdoorlocks,PDO::PARAM_STR);
$query->bindParam(':antilockbrakingsys',$antilockbrakingsys,PDO::PARAM_STR);
$query->bindParam(':brakeassist',$brakeassist,PDO::PARAM_STR);
$query->bindParam(':powersteering',$powersteering,PDO::PARAM_STR);
$query->bindParam(':driverairbag',$driverairbag,PDO::PARAM_STR);
$query->bindParam(':passengerairbag',$passengerairbag,PDO::PARAM_STR);
$query->bindParam(':powerwindow',$powerwindow,PDO::PARAM_STR);
$query->bindParam(':cdplayer',$cdplayer,PDO::PARAM_STR);
$query->bindParam(':centrallocking',$centrallocking,PDO::PARAM_STR);
$query->bindParam(':crashcensor',$crashcensor,PDO::PARAM_STR);
$query->bindParam(':leatherseats',$leatherseats,PDO::PARAM_STR);
$query->bindParam(':driver',$driver,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $conn->lastInsertId();
if($lastInsertId)
{
$msg="Vehicle posted successfully";
}
else 
{
$error="Something went wrong. Please try again";
}

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
		.col-sm-4.vehicle-botton button {
			padding: 3px;
		}
		.hr-dashed {
			background-color: transparent;
			border-top: 1px dotted #edf0f5;
			color: transparent;
			height: 1px;
			margin: 9px 0;
		}
		.form-control {
			padding: 1px 1px;
			
		}
	</style>

</head>

<body>
	<div class="ts-main-content">
		<div class="">
			<div class="">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
<?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data">
<div class="form-group">
	<label class="col-sm-2">Vehicle Title<span style="color:red">*</span></label>
	<div class="col-sm-4">
	<input type="text" name="vehicletitle" class="" required>
	</div>
	<label class="col-sm-2">Select Brand<span style="color:red">*</span></label>
	<div class="col-sm-4 vehicle-botton">
		<select class="selectpicker" name="brandname" required>
		<option value=""> Select </option>
		<?php $ret="select id,BrandName from tblbrands";
		$query= $conn -> prepare($ret);
		//$query->bindParam(':id',$id, PDO::PARAM_STR);
		$query-> execute();
		$results = $query -> fetchAll(PDO::FETCH_OBJ);
		if($query -> rowCount() > 0)
		{
		foreach($results as $result)
		{
		?>
		<option value="<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->BrandName);?></option>
		<?php }} ?>

		</select>
	</div>
</div>
												
	<div class="hr-dashed"></div>
	<div class="form-group">
		<label class="col-sm-2">Vehical Overview<span style="color:red">*</span></label>
		<div class="col-sm-4">
		<textarea class="form-control" name="vehicalorcview" rows="3" required></textarea>
		</div>
		<label class="col-sm-2">Select Fuel Type<span style="color:red">*</span></label>
		<div class="col-sm-4 vehicle-botton">
			<select class="selectpicker" name="fueltype" required>
				<option value=""> Select </option>

				<option value="Petrol">Petrol</option>
				<option value="Diesel">Diesel</option>
				<option value="CNG">CNG</option>
			</select>
		</div>
	</div>

<div class="form-group">
<!--<label class="col-sm-2 control-label">Price Per Day(in USD)<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="priceperday" class="form-control" required>
</div>-->
<!--<label class="col-sm-2 control-label">Select Fuel Type<span style="color:red">*</span></label>
<div class="col-sm-4">
<select class="selectpicker" name="fueltype" required>
<option value=""> Select </option>

<option value="Petrol">Petrol</option>
<option value="Diesel">Diesel</option>
<option value="CNG">CNG</option>
</select>
</div>-->
</div>


<div class="form-group">
	<label class="col-sm-1">Reg No.<span style="color:red">*</span></label>
	<div class="col-sm-2">
		<input type="text" name="modelyear" class="" required>
	</div>
	<label class="col-sm-2">Seating Capacity<span style="color:red">*</span></label>
	<div class="col-sm-2">
		<input type="text" name="seatingcapacity" class="" required>
	</div>
	<label class="col-sm-1">Assign Driver<span style="color:red">*</span></label>
	<div class="col-sm-4 vehicle-botton">
		<select class="selectpicker" name="driver" required>
		<option value=""> Select </option>
		<?php 
		
		$ret12="select DriverId from tblvehicles";
		$query12= $conn -> prepare($ret12);
		$query12-> execute();
		$result12 = $query12 -> fetchAll(PDO::FETCH_OBJ);
		
		foreach($result12 as $val12)
		{
			$drive_id=$val12->DriverId;
		}
		
		$ret="select id,name from fa_driver_details where id!=$drive_id";
             //  echo  $ret = "SELECT * from driver_details join tblvehicles on driver_details.id=tblvehicles.DriverId where driver_details.id!=tblvehicles.DriverId";

		$query= $conn -> prepare($ret);
		//$query->bindParam(':id',$id, PDO::PARAM_STR);
		$query-> execute();
		$results = $query -> fetchAll(PDO::FETCH_OBJ);
		if($query -> rowCount() > 0)
		{
		foreach($results as $result)
		
		{
		?>
		<option value="<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->name);?></option>
		<?php }} ?>
		
		</select>
	</div>
</div>
<div class="hr-dashed"></div>


<div class="form-group">
<div class="col-sm-12">
<h4><b>Upload Images</b></h4>
</div>
</div>


<div class="form-group">
<div class="col-sm-4">
Image 1 <span style="color:red">*</span><input type="file" name="img1" required>
</div>
</div>
<div class="hr-dashed"></div>									
</div>
</div>
</div>
</div>
							

<div class="row">
<div class="col-md-12">
<div class="panel panel-default">
<div class="panel-heading">Accessories</div>
<div class="panel-body">


<div class="form-group">
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="airconditioner" name="airconditioner" value="1">
<label for="airconditioner"> Air Conditioner </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="powerdoorlocks" name="powerdoorlocks" value="1">
<label for="powerdoorlocks"> Power Door Locks </label>
</div></div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="antilockbrakingsys" name="antilockbrakingsys" value="1">
<label for="antilockbrakingsys"> AntiLock Braking System </label>
</div></div>
<div class="checkbox checkbox-inline">
<input type="checkbox" id="brakeassist" name="brakeassist" value="1">
<label for="brakeassist"> Brake Assist </label>
</div>
</div>



<div class="form-group">
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="powersteering" name="powersteering" value="1">
<input type="checkbox" id="powersteering" name="powersteering" value="1">
<label for="inlineCheckbox5"> Power Steering </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="driverairbag" name="driverairbag" value="1">
<label for="driverairbag">Driver Airbag</label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="passengerairbag" name="passengerairbag" value="1">
<label for="passengerairbag"> Passenger Airbag </label>
</div></div>
<div class="checkbox checkbox-inline">
<input type="checkbox" id="powerwindow" name="powerwindow" value="1">
<label for="powerwindow"> Power Windows </label>
</div>
</div>


<div class="form-group">
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="cdplayer" name="cdplayer" value="1">
<label for="cdplayer"> CD Player </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox h checkbox-inline">
<input type="checkbox" id="centrallocking" name="centrallocking" value="1">
<label for="centrallocking">Central Locking</label>
</div></div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="crashcensor" name="crashcensor" value="1">
<label for="crashcensor"> Crash Sensor </label>
</div></div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="leatherseats" name="leatherseats" value="1">
<label for="leatherseats"> Leather Seats </label>
</div>
</div>
</div>




											<div class="form-group">
												<div class="col-sm-8 col-sm-offset-2" style="text-align:center;">
													<button class="btn btn-default btn-sm" type="reset">Cancel</button>
													<button class="btn btn-primary btn-sm" name="submit" type="submit">Save changes</button>
												</div>
											</div>

										</form>
									</div>
								</div>
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