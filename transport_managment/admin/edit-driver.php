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
	page(_($help_context = "Edit Driver"));
if(strlen($_SESSION["wa_current_user"]->email)==0)
  {
header('location:index.php');
}
else{ 
if(isset($_POST['submit'])){ 
	$name=$_POST['name'];
	$father_name=$_POST['father_name'];
	$address=$_POST['address'];
	$city=$_POST['city'];
	$state=$_POST['state'];
	$pin_no=$_POST['pin_no'];
	$phone_no=$_POST['phone_no'];
	$emergency_contact=$_POST['emg_contact'];
	if($_POST['img11']){
		echo $file1=$_FILES["img11"]["tmp_name"];
		echo $vimage1=$_FILES["img11"]["name"];
	}else {
		$file1=$_FILES["img1"]["tmp_name"];
		$vimage1=$_FILES["img1"]["name"];
	}
	if($_POST['img12']){
		$file2=$_FILES["img12"]["tmp_name"];
		$vimage2=$_FILES["img12"]["name"];
	}else {
		$file2=$_FILES["img2"]["tmp_name"];
		$vimage2=$_FILES["img2"]["name"];
	}
	if($_POST['img13']){
		$file3=$_FILES["img13"]["tmp_name"];
		$vimage3=$_FILES["img13"]["name"];
	}else {
		$file3=$_FILES["img3"]["tmp_name"];
		$vimage3=$_FILES["img3"]["name"];
	}
	$email=$_POST['email'];
	$licence_no=$_POST['licence_no'];
	$valid_upto=$_POST['valid_upto'];
	$aadhar_no =$_POST['aadhar_no'];
	$id=intval($_GET['id']);
	move_uploaded_file($file1,"img/vehicleimages/".$vimage1);
	move_uploaded_file($file2,"img/vehicleimages/".$vimage2);
	move_uploaded_file($file3,"img/vehicleimages/".$vimage3);
	$sql="update driver_details set name=:name,father_name =:father_name ,address =:address,city=:city,state=:state,pin_no =:pin_no,phone_no=:phone_no,emergency_contact=:emergency_contact,email =:email,licence_no =:licence_no ,valid_upto=:valid_upto,aadhar_no=:aadhar_no,aadhar_copy=:aadhar_copy,licence_copy=:licence_copy,profile_pic=:profile_pic where id=:id ";
	$query = $dbh->prepare($sql);
	$query->bindParam(':name',$name,PDO::PARAM_STR);
	$query->bindParam(':father_name',$father_name,PDO::PARAM_STR);
	$query->bindParam(':address',$address,PDO::PARAM_STR);
	$query->bindParam(':city',$city,PDO::PARAM_STR);
	$query->bindParam(':state',$state,PDO::PARAM_STR);
	$query->bindParam(':pin_no',$pin_no,PDO::PARAM_STR);
	$query->bindParam(':phone_no',$phone_no,PDO::PARAM_STR);
	$query->bindParam(':emergency_contact',$emergency_contact,PDO::PARAM_STR);
	$query->bindParam(':email',$email,PDO::PARAM_STR);
	$query->bindParam(':licence_no',$licence_no,PDO::PARAM_STR);
	$query->bindParam(':valid_upto',$valid_upto,PDO::PARAM_STR);
	$query->bindParam(':aadhar_no',$aadhar_no ,PDO::PARAM_STR);
	$query->bindParam(':aadhar_copy',$vimage1 ,PDO::PARAM_STR);
	$query->bindParam(':licence_copy',$vimage2 ,PDO::PARAM_STR);
	$query->bindParam(':profile_pic',$vimage3 ,PDO::PARAM_STR);
	$query->bindParam(':id',$id,PDO::PARAM_STR);
	$query->execute();
    $msg="Updated successfully";
	

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
									<?php 
$id=intval($_GET['id']);
$sql ="SELECT * FROM driver_details where id=:id";
$query = $dbh -> prepare($sql);
$query-> bindParam(':id', $id, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{	?>
<form method="post" action="" class="form-horizontal" enctype="multipart/form-data">
<input type="hidden" name="img11" class="form-control" value="<?php echo htmlentities($result->aadhar_copy)?>">
<input type="hidden" name="img12" class="form-control" value="<?php echo htmlentities($result->licence_copy)?>">
<input type="hidden" name="img13" class="form-control" value="<?php echo htmlentities($result->profile_pic)?>">
<div class="form-group">
	<label class="col-sm-2 control-label">Name<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="name" class="form-control" value="<?php echo htmlentities($result->name)?>" required>
	</div>
	<label class="col-sm-2 control-label">Father Name<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="father_name" class="form-control" value="<?php echo htmlentities($result->father_name)?>" required>
	</div>
</div>									
<div class="hr-dashed"></div>
<div class="form-group">
	<label class="col-sm-2 control-label">Address<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="address" class="form-control" value="<?php echo htmlentities($result->address )?>" required>
	</div>
	<label class="col-sm-2 control-label">City<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="city" class="form-control" value="<?php echo htmlentities($result->city )?>" required>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">State<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="state" class="form-control" value="<?php echo htmlentities($result->state)?>" required>
	</div>
	<label class="col-sm-2 control-label">Pin Code<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="pin_no" class="form-control" value="<?php echo htmlentities($result->pin_no)?>" required>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Contact No.<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="phone_no" class="form-control" value="<?php echo htmlentities($result->phone_no)?>" required>
	</div>
	<label class="col-sm-2 control-label">Emergency Contact No.<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="emg_contact" class="form-control" value="<?php echo htmlentities($result->emergency_contact)?>" required>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">EmailID</label>
	<div class="col-sm-4">
		<input type="text" name="email" value="<?php echo htmlentities($result->email)?>" class="form-control">
	</div>
	<label class="col-sm-2 control-label">Liecence No.<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="licence_no" value="<?php echo htmlentities($result->licence_no)?>" class="form-control" required>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Valid Up To<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="date" name="valid_upto" class="form-control" value="<?php echo htmlentities($result->valid_upto)?>" style="line-height:19px !important;" required>
	</div>
	<label class="col-sm-2 control-label">Aadhar No.<span style="color:red">*</span></label>
	<div class="col-sm-4">
		<input type="text" name="aadhar_no" class="form-control" value="<?php echo htmlentities($result->aadhar_no)?>" required>
	</div>
</div>
<div class="hr-dashed"></div>
<div class="form-group">
	<div class="col-sm-12">
		<h4><b>Upload</b></h4>
	</div>
</div>
<div class="form-group">
	<div class="col-sm-4">
		Profile Piture: <span style="color:red">*</span><input type="file" name="img1">
	</div>
	<div class="col-sm-4">
		Scaned Aadhar Card<span style="color:red">*</span><input type="file" name="img2">
	</div>
	<div class="col-sm-4">
		Scaned Licence Copy<span style="color:red">*</span><input type="file" name="img3">
	</div>
</div>
<?php }} ?>
<div class="form-group">
	<div class="col-sm-8 col-sm-offset-2" style="text-align:center;">
		<button class="btn btn-default btn-sm" type="reset">Cancel</button>
		<button class="btn btn-primary btn-sm" name="submit" type="submit">Save changes</button>
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