<?php
error_reporting(1);
$page_security = 'SA_DRIVER';
if (!@$_GET['popup'])
	$path_to_root = "../..";
else	
	$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/transport_managment/admin/includes/config.php");

if (!@$_GET['popup'])
	page(_($help_context = "Add Driver"));
if(strlen($_SESSION["wa_current_user"]->email)==0)
  {
header('location:index.php');
}
else{ 

if(isset($_POST['submit']))
  {
	  echo "<pre>";
	  print_r($_POST);
$name=$_POST['name'];
$father_name=$_POST['father_name'];
$address=$_POST['address'];
$city=$_POST['city'];
$state=$_POST['state'];
$pin_no=$_POST['pin_no'];
$phone_no=$_POST['phone_no'];
$emergency_contact=$_POST['emg_contact'];
/*$vimage2=$_FILES["img2"]["name"];
$vimage3=$_FILES["img3"]["name"];
$vimage4=$_FILES["img4"]["name"];
$vimage5=$_FILES["img5"]["name"];*/
$email=$_POST['email'];
$licence_no=$_POST['licence_no'];
$valid_upto=$_POST['valid_upto'];
$adhar_no=$_POST['adhar_no'];
/*$powersteering=$_POST['powersteering'];
$driverairbag=$_POST['driverairbag'];
$passengerairbag=$_POST['passengerairbag'];
$powerwindow=$_POST['powerwindow'];
$cdplayer=$_POST['cdplayer'];
$centrallocking=$_POST['centrallocking'];
$crashcensor=$_POST['crashcensor'];
$leatherseats=$_POST['leatherseats'];*/
//move_uploaded_file($_FILES["img1"]["tmp_name"],"img/vehicleimages/".$_FILES["img1"]["name"]);
// move_uploaded_file($_FILES["img3"]["tmp_name"],"img/vehicleimages/".$_FILES["img3"]["name"]);
//move_uploaded_file($_FILES["img4"]["tmp_name"],"img/vehicleimages/".$_FILES["img4"]["name"]);
//move_uploaded_file($_FILES["img5"]["tmp_name"],"img/vehicleimages/".$_FILES["img5"]["name"]);

$sql="INSERT INTO driver_details(name,father_name,address,city,state,pin_no,phone_no,emergency_contact,email,licence_no,valid_upto,adhar_no) VALUES(:name,:father_name,:address,:city,:state,:pin_no,:phone_no,:emergency_contact,:email,:licence_no,:valid_upto,:adhar_no)";
echo $sql;

$query = $dbh->prepare($sql);
$query->bindParam(':name',$name,PDO::PARAM_STR);
$query->bindParam(':father_name',$father_name,PDO::PARAM_STR);
$query->bindParam(':address',$address,PDO::PARAM_STR);
//$query->bindParam(':priceperday',$priceperday,PDO::PARAM_STR);
$query->bindParam(':city',$city,PDO::PARAM_STR);
$query->bindParam(':state',$state,PDO::PARAM_STR);
$query->bindParam(':pin_no',$pin_no,PDO::PARAM_STR);
/*$query->bindParam(':vimage1',$vimage1,PDO::PARAM_STR);
$query->bindParam(':vimage2',$vimage2,PDO::PARAM_STR);
$query->bindParam(':vimage3',$vimage3,PDO::PARAM_STR);
$query->bindParam(':vimage4',$vimage4,PDO::PARAM_STR);
$query->bindParam(':vimage5',$vimage5,PDO::PARAM_STR);*/
$query->bindParam(':phone_no',$phone_no,PDO::PARAM_STR);
$query->bindParam(':emergency_contact',$emergency_contact,PDO::PARAM_STR);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':licence_no',$licence_no,PDO::PARAM_STR);
$query->bindParam(':valid_upto',$valid_upto,PDO::PARAM_STR);
$query->bindParam(':adhar_no',$adhar_no,PDO::PARAM_STR);
/*$query->bindParam(':passengerairbag',$passengerairbag,PDO::PARAM_STR);
$query->bindParam(':powerwindow',$powerwindow,PDO::PARAM_STR);
$query->bindParam(':cdplayer',$cdplayer,PDO::PARAM_STR);
$query->bindParam(':centrallocking',$centrallocking,PDO::PARAM_STR);
$query->bindParam(':crashcensor',$crashcensor,PDO::PARAM_STR);
$query->bindParam(':leatherseats',$leatherseats,PDO::PARAM_STR);*/
$query->execute();
$lastInsertId = $dbh->lastInsertId();
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
									<div class="panel-heading">Basic Info</div>
<?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data">
<div class="form-group">
	<label class="col-sm-2 control-label">Name<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="name" class="form-control" required>
	</div>
	<label class="col-sm-2 control-label">Father Name<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="father_name" class="form-control" required>
	</div>
</div>									
<div class="hr-dashed"></div>
<div class="form-group">
	<label class="col-sm-2 control-label">Address<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="address" class="form-control" required>
	</div>
	<label class="col-sm-2 control-label">City<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="city" class="form-control" required>
	</div>
</div>
<div class="form-group">
<!--<label class="col-sm-2 control-label">Price Per Day(in USD)<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="priceperday" class="form-control" required>
</div>-->
	
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">State<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="state" class="form-control" required>
	</div>
	<label class="col-sm-2 control-label">Pin Code<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="pin_no" class="form-control" required>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Contact No.<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="phone_no" class="form-control" required>
	</div>
	<label class="col-sm-2 control-label">Emergency Contact No.<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="emg_contact" class="form-control" required>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Valid Up To<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="date" name="valid_upto" class="form-control" style="line-height:19px !important;" required>
	</div>
	<label class="col-sm-2 control-label">Adhar No.<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="adhar_no" class="form-control" required>
	</div>
</div>
<div class="hr-dashed"></div>
<!--<div class="form-group">
	<div class="col-sm-12">
		<h4><b>Upload</b></h4>
	</div>
</div>-->
<!--<div class="form-group">
	<div class="col-sm-4">
		Profile <span style="color:red">*</span><input type="file" name="img1" required>
	</div>
	<div class="col-sm-4">
		Image 2<span style="color:red">*</span><input type="file" name="img2" required>
	</div>
	<div class="col-sm-4">
		Image 3<span style="color:red">*</span><input type="file" name="img3" required>
	</div>
</div>-->


<!--<div class="form-group">
<div class="col-sm-4">
Image 4<span style="color:red">*</span><input type="file" name="img4" required>
</div>
<div class="col-sm-4">
Image 5<input type="file" name="img5">
</div>

</div>-->
<div class="hr-dashed"></div>	
<div class="form-group">
	<div class="col-sm-8 col-sm-offset-2">
		<button class="btn btn-default" type="reset">Cancel</button>
		<button class="btn btn-primary" name="submit" type="submit">Save changes</button>
	</div>
</div>								
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