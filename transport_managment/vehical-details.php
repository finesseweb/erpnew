<?php 
$page_security = 'SA_MAINTAINPREVENTIVE';
if (!@$_GET['popup'])
	$path_to_root = "..";
else	
	$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/transport_managment/includes/config.php");

if (!@$_GET['popup'])
	page(_($help_context = "Vehical Details"));

error_reporting(0);
  //echo "<pre>";
  //print_r($_SESSION);

if(isset($_POST['submit']))
{
$booked_by=$_SESSION["wa_current_user"]->name; 
$department=$_POST["department"]; 
$designation=$_POST["designation"]; 
$fromdate=$_POST['fromdate'];
$fromplace=$_POST['fromplace'];
$todate=$_POST['todate']; 
$toplace=$_POST['toplace'];
$message=$_POST['message'];
$useremail=$_SESSION["wa_current_user"]->email;
$status=0;
$name=$_POST['name'];
$vhid=$_GET['vhid'];
$sql="INSERT INTO  tblbooking(userEmail,VehicleId,FromDate,fromplace,ToDate,toplace,message,Status,name,booked_by,department,designation) VALUES(:useremail,:vhid,:fromdate,:fromplace,:todate,:toplace,:message,:status,:name,:booked_by,:department,:designation)";
$query = $dbh->prepare($sql);
$query->bindParam(':useremail',$useremail,PDO::PARAM_STR);
$query->bindParam(':vhid',$vhid,PDO::PARAM_STR);
$query->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
$query->bindParam(':fromplace',$fromplace,PDO::PARAM_STR);
$query->bindParam(':todate',$todate,PDO::PARAM_STR);
$query->bindParam(':toplace',$toplace,PDO::PARAM_STR);
$query->bindParam(':message',$message,PDO::PARAM_STR);
$query->bindParam(':status',$status,PDO::PARAM_STR);
$query->bindParam(':name',$name,PDO::PARAM_STR);
$query->bindParam(':booked_by',$booked_by,PDO::PARAM_STR);
$query->bindParam(':department',$department,PDO::PARAM_STR);
$query->bindParam(':designation',$designation,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
echo "<script>alert('Booking successfull.');</script>";
}
else 
{
echo "<script>alert('Something went wrong. Please try again');</script>";
}

}

?>


<!DOCTYPE HTML>
<html lang="en">
<head>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<!--bootstrap-slider -->
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<!--FontAwesome Font Style -->
<link href="assets/css/font-awesome.min.css" rel="stylesheet">

<!-- SWITCHER -->
<link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/red.css" title="red" media="all" data-default-color="true" />
<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/orange.css" title="orange" media="all" />
<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/blue.css" title="blue" media="all" />
<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/pink.css" title="pink" media="all" />
<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/green.css" title="green" media="all" />
<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/purple.css" title="purple" media="all" />
<link href="assets/css/bootstrap-datetimepicker.css" rel="stylesheet" media="screen">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
<style>
	.main_features {
		overflow: hidden;
	}
	.main_features ul {
		padding: 0px;
		margin: 0px;
	}
	.main_features ul li {
		border: 1px solid #dcd9d9;
		float: left;
		list-style: outside none none;
		margin: 0 17px 0 0;
		min-height: 113px;
		min-width: 123px;
		padding: 10px;
		text-align: center;
	}
	.main_features ul li i {
		color: #c0c0c0;
		font-size: 36px;
		margin: 0px;
	}
	.main_features ul li h5 {
		font-weight: 300;
		margin: 0px;
	}
	.listing_more_info {
		overflow: hidden;
		padding: 50px 0;
	}
	.listing_detail_wrap {
		border: #dcd9d9 solid 1px;
	}
	.gray-bg {
		background: #eeeeee;
	}
	.listing_detail_wrap .nav-tabs > li.active a, .listing_detail_wrap .nav-tabs > li:hover a {
		color: #fff;
		background: #fa2837;
	}
	.listing_detail_wrap .nav-tabs > li a {
		color: #000;
	}
	table thead {
		background: #eee;
	}
	.listing_more_info table td, .listing_more_info table th {
		font-size: 16px;
	}
	table thead th, table thead td {
		text-transform: uppercase;
		font-weight: 900;
		color: #111;
	}
	table th, table td {
		border: 1px solid #cccccc;
		padding: 15px;
		padding: 18px;
	}
	.listing_detail_wrap .tab-content {
		padding: 30px;
	}
	table {
		margin: 0 0 30px;
		width: 100%;
	}
	.sidebar_widget {
		border: 1px solid #e6e6e6;
		margin: 0 auto 40px;
		padding: 20px 16px 30px;
		position: relative;
	}
	.widget_heading {
		margin-bottom: 26px;
	}
	.widget_heading h5 {
		color: #111111;
		font-weight: 900;
		font-size: 20px;
		margin: 0 a15px;
	}
	.widget_heading i {
		margin: 0 5px 0 0;
	}
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
		width: 34%;
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
	li{
		list-style:none;
	}
	.booking_form .form-control {
		margin: 8px;
	}
	.booking_form .form-control {
		margin: 8px;
	}
	.book_button {
		margin-left: 112px;
		color: #fff;
	}
	.booking_form label {
		margin-top: 15px;
	}
</style>
</head>
<body>
<!--Listing-Image-Slider-->

<?php 
$vhid=intval($_GET['vhid']);
$sql = "SELECT tblvehicles.*,tblbrands.BrandName,tblbrands.id as bid  from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand where tblvehicles.id=:vhid";
$query = $dbh -> prepare($sql);
$query->bindParam(':vhid',$vhid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{  
$_SESSION['brndid']=$result->bid;  
?>  
<!--Listing-detail-->
<section class="listing-detail">
  <div class="container">
    <div class="listing_detail_head row">
      <div class="col-md-9">
        <h2><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></h2>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="main_features">
          <ul>
          
            <li> <i class="fa fa-calendar" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->ModelYear);?></h5>
              <p>Reg.Year</p>
            </li>
            <li> <i class="fa fa-cogs" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->FuelType);?></h5>
              <p>Fuel Type</p>
            </li>
       
            <li> <i class="fa fa-user-plus" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->SeatingCapacity);?></h5>
              <p>Seats</p>
            </li>
          </ul>
        </div>
        <div class="listing_more_info">
          <div class="listing_detail_wrap"> 
            <!-- Nav tabs -->
            <ul class="nav nav-tabs gray-bg" role="tablist">
                <li role="presentation" class="active"><a href="#vehicle-overview " aria-controls="vehicle-overview" role="tab" data-toggle="tab">Vehicle Overview </a></li>
                <li role="presentation"><a href="#accessories" aria-controls="accessories" role="tab" data-toggle="tab">Accessories</a></li>
                <li role="presentation"><a href="#driver" aria-controls="driver" role="tab" data-toggle="tab">Driver Detail</a></li>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content"> 
              <!-- vehicle-overview -->
              <div role="tabpanel" class="tab-pane active" id="vehicle-overview">
                
                <p><?php echo htmlentities($result->VehiclesOverview);?></p>
              </div>
              
              
              <!-- Accessories -->
              <div role="tabpanel" class="tab-pane" id="accessories"> 
                <!--Accessories-->
                <table>
                  <thead>
                    <tr>
                      <th colspan="2">Accessories</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Air Conditioner</td>
<?php if($result->AirConditioner==1)
{
?>
                      <td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?> 
   <td><i class="fa fa-close" aria-hidden="true"></i></td>
   <?php } ?> </tr>

<tr>
<td>AntiLock Braking System</td>
<?php if($result->AntiLockBrakingSystem==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else {?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
                    </tr>

<tr>
<td>Power Steering</td>
<?php if($result->PowerSteering==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>
                   

<tr>

<td>Power Windows</td>

<?php if($result->PowerWindows==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>
                   
 <tr>
<td>CD Player</td>
<?php if($result->CDPlayer==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Leather Seats</td>
<?php if($result->LeatherSeats==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Central Locking</td>
<?php if($result->CentralLocking==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Power Door Locks</td>
<?php if($result->PowerDoorLocks==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
                    </tr>
                    <tr>
<td>Brake Assist</td>
<?php if($result->BrakeAssist==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php  } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Driver Airbag</td>
<?php if($result->DriverAirbag==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
 </tr>
 
 <tr>
 <td>Passenger Airbag</td>
 <?php if($result->PassengerAirbag==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else {?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Crash Sensor</td>
<?php if($result->CrashSensor==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

                  </tbody>
                </table>
              </div>
			   <div role="tabpanel" class="tab-pane" id="driver"> 
				<!--Driver-->
					<table>
						<thead>
							<tr>
							  <th colspan="2">Driver Details</th>
							</tr>
						</thead>
						<tbody>
							<ul class="vehicle_listing">
							<?php 
								$id=$result->DriverId ;
								$sql ="SELECT * from driver_details where id=:id";
								$query = $dbh -> prepare($sql);
								$query-> bindParam(':id', $id, PDO::PARAM_STR);
								$query->execute();
								$results=$query->fetchAll(PDO::FETCH_OBJ);
								$cnt=1;
								if($query->rowCount() > 0)
								{
									foreach($results as $result) { ?>
										<li>
											<div class="vehicle_img"> <a href="vehical-details.php?vhid=<?php echo htmlentities($result->vid);?>""><img src="admin/img/vehicleimages/<?php echo htmlentities($result->profile_pic);?>" alt="image"></a> </div>
											<div class="vehicle_title">
											  <h6><a href="javascript:void(0)"> <?php echo htmlentities($result->name);?></a></h6>
											  <p>
												  <b>Address :</b> <?php echo htmlentities($result->address );?> ,<?php echo htmlentities($result->city);?>,<?php echo htmlentities($result->state);?><br />
												  <b>Pin Cpde   :</b> <?php echo htmlentities($result->pin_no);?><br />
												  <b>Phone No   :</b> <?php echo htmlentities($result->phone_no);?><br />  
												  <b>Emergency Contact   :</b> <?php echo htmlentities($result->emergency_contact);?><br /> 
												  <b>Email :</b> <?php echo htmlentities($result->email);?><br />  
												  <b>Licence No.:</b> <?php echo htmlentities($result->licence_no );?><br />  
												  <b>Valid Upto    :</b> <?php echo htmlentities($result->valid_upto);?><br /> 
											  </p>
											</div>
										</li>
							<?php 	
									}
								}							
								
							?>
							</ul>
						</tbody>
					</table>
				</div>
            </div>
          </div>
          
        </div>
<?php }} ?>
      </div>
      
      <!--Side-Bar-->
      <aside class="col-md-6">
        <div class="sidebar_widget booking_form">
          <div class="widget_heading">
            <h5><i class="fa fa-envelope" aria-hidden="true"></i>Book Now</h5>
          </div>
          <form method="post">
		   <div class="form-group">
				<div class=col-md-2>
				  <label>Name:</label>
				</div>
				<div class=col-md-10>
				  <input type="text" class="form-control" name="name" placeholder="Name">
				</div>
            </div>
			 <div class="form-group">
				<div class=col-md-2>
				  <label>Department:</label>
				</div>
				<div class=col-md-10>
				  <input type="text" class="form-control" name="department" placeholder="Department" required>
				</div>
            </div>
			 <div class="form-group">
				<div class=col-md-2>
				  <label>Designation:</label>
				</div>
				<div class=col-md-10>
				  <input type="text" class="form-control designation" name="designation" placeholder="Designation" required>
				</div>
            </div>
            <div class="form-group">
				<div class=col-md-2>
				  <label>From:</label>
				</div>
				<div class=col-md-5>
				  <input type="text" class="form-control" name="fromplace" placeholder="Place" required>
				</div>
				<div class=col-md-5>
				  <input type="text" class="form-control form_datetime fromdate" name="fromdate" placeholder="Date Time" required>
				</div>
            </div>
			<div class="form-group">
				<div class=col-md-2>
				  <label>To:</label>
				</div>
				<div class=col-md-5>
				  <input type="text" class="form-control" name="toplace" placeholder="Place" required>
				</div>
				<div class=col-md-5>
				  <input type="text" class="form-control form_datetime todate" name="todate" placeholder="Date Time" required>
				</div>
            </div>
            <!--<div class="form-group">
              <input type="text" class="form-control" name="todate" placeholder="To Date(dd/mm/yyyy)" required>
            </div>-->
            <div class="form-group">
				<div class=col-md-2>
				  <label>Message:</label>
				</div>
				<div class=col-md-10>
				  <textarea rows="4" class="form-control message_sectiond" name="message" placeholder="Message" required></textarea>
				</div>
            </div>
			<br/>
          <?php if($_SESSION["wa_current_user"]->email)
              {?>
              <div class="book_button">
                <input type="submit" class="btn"  name="submit" value="Book Now">
              </div>
              <?php } else { ?>
<a href="#loginform" class="btn btn-xs uppercase" data-toggle="modal" data-dismiss="modal">Login For Book</a>

              <?php } ?>
          </form>
        </div>
      </aside>
      <!--/Side-Bar--> 
    </div>
    
    <div class="space-20"></div>
    <div class="divider"></div>
    
    <!--Similar-Cars-->
   <?php /* <div class="similar_cars">
      <h3>Similar Cars</h3>
      <div class="row">
<?php 
$bid=$_SESSION['brndid'];
$sql="SELECT tblvehicles.VehiclesTitle,tblbrands.BrandName,tblvehicles.PricePerDay,tblvehicles.FuelType,tblvehicles.ModelYear,tblvehicles.id,tblvehicles.SeatingCapacity,tblvehicles.VehiclesOverview,tblvehicles.Vimage1 from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand where tblvehicles.VehiclesBrand=:bid";
$query = $dbh -> prepare($sql);
$query->bindParam(':bid',$bid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{ ?>      
        <div class="col-md-3 grid_listing">
          <div class="product-listing-m gray-bg">
            <div class="product-listing-img"> <a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1);?>" class="img-responsive" alt="image" /> </a>
            </div>
            <div class="product-listing-content">
              <h5><a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></a></h5>
              <p class="list-price">$<?php echo htmlentities($result->PricePerDay);?></p>
          
              <ul class="features_list">
                
             <li><i class="fa fa-user" aria-hidden="true"></i><?php echo htmlentities($result->SeatingCapacity);?> seats</li>
                <li><i class="fa fa-calendar" aria-hidden="true"></i><?php echo htmlentities($result->ModelYear);?> model</li>
                <li><i class="fa fa-car" aria-hidden="true"></i><?php echo htmlentities($result->FuelType);?></li>
              </ul>
            </div>
          </div>
        </div>
 <?php }} ?>       

      </div>
    </div>*/?>
    <!--/Similar-Cars--> 
    
  </div>
</section>
<!--/Listing-detail--> 

<!--Login-Form -->
<?php include('includes/login.php');?>
<!--/Login-Form --> 

<!--Register-Form -->
<?php include('includes/registration.php');?>

<!--/Register-Form --> 

<!--Forgot-password-Form -->
<?php include('includes/forgotpassword.php');?>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<script src="assets/switcher/js/switcher.js"></script>
<script src="assets/js/bootstrap-slider.min.js"></script> 
<script src="assets/js/owl.carousel.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="assets/js/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
<script type="text/javascript">
    $('.form_datetime').datetimepicker({
        //language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		forceParse: 0,
        showMeridian: 1
    });
	$('.form_date').datetimepicker({
        language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });
	$('.form_time').datetimepicker({
        language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 1,
		minView: 0,
		maxView: 1,
		forceParse: 0
    });
</script>
<script>
$(".message_sectiond").click(function(){
    var firstDate = $('.fromdate').val();
	var strarray1 = firstDate.split(' ');
    var lastDate = $('.todate').val();
	var strarray2 = lastDate.split(' ');
    if (Date.parse(firstDate) > Date.parse(lastDate) || strarray1[1] > strarray2[1] ) {
        alert("From date can not be greater than End date!!");
    } 
});
</script>                       
</script>

</body>
</html>