<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['hbmsaid']==0)) {
  header('location:logout.php');
} else {
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Hotel Booking Management System | New Booking</title>
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- Bootstrap Core CSS -->
<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom CSS -->
<link href="css/style.css" rel='stylesheet' type='text/css' />
<!-- Graph CSS -->
<link href="css/font-awesome.css" rel="stylesheet"> 
<!-- jQuery -->
<link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet' type='text/css'/>
<link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
<!-- lined-icons -->
<link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
<script src="js/simpleCart.min.js"> </script>
<script src="js/amcharts.js"></script>	
<script src="js/serial.js"></script>	
<script src="js/light.js"></script>	
<!-- //lined-icons -->
<script src="js/jquery-1.10.2.min.js"></script>
</head> 
<body>
   <div class="page-container">
   <!--/content-inner-->
	<div class="left-content">
	   <div class="inner-content">
		<!-- header-starts -->
			<?php include_once('includes/header.php');?>
				
				<!--content-->
			<div class="content">
<div class="women_main">
	<!-- start content -->
	<div class="grids">
					<div class="progressbar-heading grids-heading">
						<h2>New Booking</h2>
					</div>
					<div class="panel panel-widget forms-panel">
						<div class="forms">
							<div class="form-grids widget-shadow" data-example-id="basic-forms"> 
								<div class="form-title">
									<h4>New Booking</h4>
								</div>
								<div class="form-body">
									<table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                                <thead>
                                    <tr>
                                        <th class="text-center">S.No</th>
                                        <th>Booking Number</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile Number</th>
                                        <th>Booking Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql="SELECT tbluser.FullName,tbluser.Email,tbluser.MobileNumber,tblbooking.BookingNumber,tblbooking.ID as tid,tblbooking.Status,tblbooking.BookingDate 
                                          FROM tblbooking 
                                          JOIN tbluser ON tbluser.ID=tblbooking.UserID 
                                          ORDER BY tblbooking.BookingDate DESC";
                                    $query = $dbh -> prepare($sql);
                                    $query->execute();
                                    $results=$query->fetchAll(PDO::FETCH_OBJ);

                                    $cnt=1;
                                    if($query->rowCount() > 0) {
                                        foreach($results as $row) {               
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo htmlentities($cnt);?></td>
                                        <td><?php echo htmlentities($row->BookingNumber);?></td>
                                        <td><?php echo htmlentities($row->FullName);?></td>
                                        <td><?php echo htmlentities($row->Email);?></td>
                                        <td><?php echo htmlentities($row->MobileNumber);?></td>
                                        <td><span class="badge bg-primary"><?php echo htmlentities($row->BookingDate);?></span></td>
                                        <td>
                                            <?php
                                            $status = $row->Status;
                                            if ($status == '') {
                                                echo '<span class="badge bg-warning">Not Updated Yet</span>';
                                            } elseif ($status == 'Cancelled') {
                                                echo '<span class="badge bg-danger">Cancelled</span>';
                                            } elseif ($status == 'Approved') {
                                                echo '<span class="badge bg-success">Approved</span>';
                                            } elseif ($status == 'Pending') {
                                                echo '<span class="badge bg-warning">Pending</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="view-booking-detail.php?bookingid=<?php echo htmlentities($row->BookingNumber);?>" class="btn btn-info btn-sm">
                                                <i class="fa fa-eye"></i> View Details
                                            </a>
                                            <a href="javascript:void(0);" onclick="printBooking('<?php echo htmlentities($row->BookingNumber);?>');" class="btn btn-primary btn-sm">
                                                <i class="fa fa-print"></i> Print
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $cnt=$cnt+1;}} ?>
                                </tbody>
                            </table>
								</div>
							</div>
						</div>
					</div>
			</div>
	<!-- end content -->
	
<?php include_once('includes/footer.php');?>
</div>

</div>
			<!--content-->
		</div>
</div>
				<!--//content-inner-->
			<!--/sidebar-menu-->
			<?php include_once('includes/sidebar.php');?>
							  <div class="clearfix"></div>		
							</div>
							<script>
							var toggle = true;
										
							$(".sidebar-icon").click(function() {                
							  if (toggle)
							  {
								$(".page-container").addClass("sidebar-collapsed").removeClass("sidebar-collapsed-back");
								$("#menu span").css({"position":"absolute"});
							  }
							  else
							  {
								$(".page-container").removeClass("sidebar-collapsed").addClass("sidebar-collapsed-back");
								setTimeout(function() {
								  $("#menu span").css({"position":"relative"});
								}, 400);
							  }
											
											toggle = !toggle;
										});
							</script>
<!--js -->
<script src="js/jquery.nicescroll.js"></script>
<script src="js/scripts.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="js/bootstrap.min.js"></script>
<!-- Print Functionality -->
<script>
    function printBooking(bookingNumber) {
        window.open('print-booking.php?bookingNumber=' + bookingNumber, '_blank');
    }
</script>
</body>
</html>
<?php }  ?>
