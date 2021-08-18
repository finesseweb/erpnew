<?php 
$page_security = 'SA_BOOKVEHICAL';
if (!@$_GET['popup'])
	$path_to_root = "..";
else	
	$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/transport_managment/includes/config.php");

if (!@$_GET['popup'])
	page(_($help_context = " List of Vehical"));
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<link href="assets/css/slick.css" rel="stylesheet">
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<link href="assets/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/red.css" title="red" media="all" data-default-color="true" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/orange.css" title="orange" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/blue.css" title="blue" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/pink.css" title="pink" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/green.css" title="green" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/purple.css" title="purple" media="all" />
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet"> 
<style>
	#resentnewcar .col-list-3 {
		float: left;
		margin: 15px;
		width: 27%;
		padding: 21px;
		border: 1px solid #ccc;
	}
	.car-info-box {
		position: relative;
	}
	.car-info-box ul {
		background: rgba(0, 0, 0, 0.6) none repeat scroll 0 0;
		bottom: 0;
		margin: 0 auto;
		padding: 0 15px;
		position: absolute;
		width: 100%;
	}
	.car-info-box li {
		color: #ffffff;
		display: inline-block;
		font-size: 13px;
		line-height: 50px;
		list-style: outside none none;
		margin: 0 15px 0 auto;
	}.car-title-m {
		overflow: hidden;
		padding: 20px;
	}
	.car-title-m h6 {
		float: left;
		margin: 0 auto;
		max-width: 245px;
	}
	.carousel-inner > .item > a > img, .carousel-inner > .item > img, .img-responsive, .thumbnail a > img, .thumbnail > img {
		height: 169px !important;

	}.vehicle_avail {
		color: red;
		font-size: 11px;
		margin-left: 66px;
		font-weight: 900;
	}
	
</style>
</head>
<body>

<!-- Start Switcher -->
<?php //include('includes/colorswitcher.php');?>
<!-- /Switcher -->  
        
<!--Header-->
<?php //include('includes/header.php');?>
<!-- /Header --> 
<!-- Resent Cat-->
<section class="section-padding">
  <div class="container">
    <div class="section-header text-center">
      <h2>Book Your Vehicle</h2>
      <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text.</p>
    </div>
    <div class="row"> 
      <!-- Recently Listed New Cars -->
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="resentnewcar">

<?php $sql = "SELECT tblvehicles.VehiclesTitle,tblbrands.BrandName,tblvehicles.PricePerDay,tblvehicles.FuelType,tblvehicles.ModelYear,tblvehicles.id,tblvehicles.SeatingCapacity,tblvehicles.VehiclesOverview,tblvehicles.Vimage1,tblvehicles.available from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{  
?>  

<div class="col-list-3">
	<div class="recent-car-list">
		<div class="car-info-box"> <a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1);?>" class="img-responsive" alt="image"></a>
			<ul>
				<li><i class="fa fa-car" aria-hidden="true"></i><?php echo htmlentities($result->FuelType);?></li>
				<li><i class="fa fa-calendar" aria-hidden="true"></i><?php echo htmlentities($result->ModelYear);?> Model</li>
				<li><i class="fa fa-user" aria-hidden="true"></i><?php echo htmlentities($result->SeatingCapacity);?> seats</li>
			</ul>
		</div>
		<div class="car-title-m">
		<?php if($result->available==0){ ?>
			<h6><a href="javascript:void(0);" style="color:#000;"><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></a></h6>
		<?php }else{ ?>
			<h6><a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></a></h6>

		<?php } ?>
			<span class="vehicle_avail"><?php if($result->available==0){ echo"Not Available";}?></span>
		</div>
		<div class="inventory_info_m">
			<p><?php echo substr($result->VehiclesOverview,0,60);?></p>
		</div>
	</div>
</div>
<?php }}?>
       
      </div>
    </div>
  </div>
</section>
<!--Login-Form -->
<?php include('includes/login.php');?>
<!--/Login-Form --> 

<!--Register-Form -->
<?php include('includes/registration.php');?>

<!--/Register-Form --> 

<!--Forgot-password-Form -->
<?php include('includes/forgotpassword.php');?>
<!--/Forgot-password-Form --> 

<!-- Scripts --> 
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<!--Switcher-->
<script src="assets/switcher/js/switcher.js"></script>
<!--bootstrap-slider-JS--> 
<script src="assets/js/bootstrap-slider.min.js"></script> 
<!--Slider-JS--> 
<script src="assets/js/slick.min.js"></script> 
<script src="assets/js/owl.carousel.min.js"></script>

</body>
</html>