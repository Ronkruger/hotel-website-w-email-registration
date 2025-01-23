<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['hbmsaid']) == 0) {
    header('location:logout.php');
} else {
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking Management System | Dashboard</title>
    <link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
    <link href="css/style.css" rel='stylesheet' type='text/css' />
    <link rel="stylesheet" href="css/font-awesome.css">
    <link href='//fonts.googleapis.com/css?family=Roboto:400,500,700' rel='stylesheet' type='text/css' />
    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <style>
        .card {
            transition: transform 0.2s;
            cursor: pointer;
            background-color: white; /* Ensure solid background */
            border: 1px solid transparent; /* Prevent border from being semi-transparent */
            height: 250px; /* Set uniform height */
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect on hover */
        }
        .card-header {
            font-size: 1.5em;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
        }
        .icon {
            font-size: 3em; /* Adjust icon size */
        }
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: calc(100% - 60px); /* Adjust for header height */
        }
        /* Solid background colors for cards */
        .bg-warning { background-color: #ffc107; }
        .bg-success { background-color: #28a745; }
        .bg-danger { background-color: #dc3545; }
        .bg-info { background-color: #17a2b8; }
        .bg-secondary { background-color: #6c757d; }
        .bg-light { background-color: #f8f9fa; }
        .bg-primary { background-color: #007bff; }

        /* Ensuring text color is white or dark based on card background */
        .text-dark { color: #212529 !important; } /* Dark text for light backgrounds */
        .text-light { color: #ffffff !important; } /* Light text for dark backgrounds */
    </style>
</head>
<body>

<div class="page-container">
    <div class="left-content">
        <div class="inner-content">
            <!-- Header -->
            <?php include_once('includes/header.php'); ?>

            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="text-center my-4">Dashboard Overview</h2>
                        </div>
                    </div>
                    <div class="row text-center">

                        <!-- New Booking Card -->
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-4">
                            <div class="card bg-warning text-dark" onclick="window.location='new-booking.php';">
                                <div class="card-header">
                                    <i class="fa fa-plus-circle icon"></i>
                                    New Booking
                                </div>
                                <div class="card-body">
                                    <?php 
                                        // Query for new bookings
                                        $sql2 = "SELECT * FROM tblbooking WHERE Status IS NULL";
                                        $query2 = $dbh->prepare($sql2);
                                        $query2->execute();
                                        $totnewbooking = $query2->rowCount();
                                    ?>
                                    <h3 class="card-title"><?php echo htmlentities($totnewbooking); ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Approved Booking Card -->
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-4">
                            <div class="card bg-success text-light" onclick="window.location='approved-booking.php';">
                                <div class="card-header">
                                    <i class="fa fa-check-circle icon"></i>
                                    Approved Booking
                                </div>
                                <div class="card-body">
                                    <?php 
                                        // Query for approved bookings
                                        $sql2 = "SELECT * FROM tblbooking WHERE Status='Approved'";
                                        $query2 = $dbh->prepare($sql2);
                                        $query2->execute();
                                        $totappbooking = $query2->rowCount();
                                    ?>
                                    <h3 class="card-title"><?php echo htmlentities($totappbooking); ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Cancelled Booking Card -->
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-4">
                            <div class="card bg-danger text-light" onclick="window.location='cancelled-booking.php';">
                                <div class="card-header">
                                    <i class="fa fa-times-circle icon"></i>
                                    Cancelled Booking
                                </div>
                                <div class="card-body">
                                    <?php 
                                        // Query for cancelled bookings
                                        $sql2 = "SELECT * FROM tblbooking WHERE Status='Cancelled'";
                                        $query2 = $dbh->prepare($sql2);
                                        $query2->execute();
                                        $totcanbooking = $query2->rowCount();
                                    ?>
                                    <h3 class="card-title"><?php echo htmlentities($totcanbooking); ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Registered Users Card -->
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-4">
                            <div class="card bg-info text-light" onclick="window.location='reg-users.php';">
                                <div class="card-header">
                                    <i class="fa fa-users icon"></i>
                                    Registered Users
                                </div>
                                <div class="card-body">
                                    <?php 
                                        // Query for registered users
                                        $sql1 = "SELECT * FROM tbluser";
                                        $query1 = $dbh->prepare($sql1);
                                        $query1->execute();
                                        $totregusers = $query1->rowCount();
                                    ?>
                                    <h3 class="card-title"><?php echo htmlentities($totregusers); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row text-center">
                        <!-- Read Enquiries Card -->
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-4">
                            <div class="card bg-secondary text-light" onclick="window.location='read-enquiry.php';">
                                <div class="card-header">
                                    <i class="fa fa-envelope-open icon"></i>
                                    Read Enquiries
                                </div>
                                <div class="card-body">
                                    <?php 
                                        // Query for read enquiries
                                        $sql1 = "SELECT * FROM tblcontact WHERE Isread='1'";
                                        $query1 = $dbh->prepare($sql1);
                                        $query1->execute();
                                        $totreadqueries = $query1->rowCount();
                                    ?>
                                    <h3 class="card-title"><?php echo htmlentities($totreadqueries); ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Unread Enquiries Card -->
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-4">
                            <div class="card bg-light text-dark" onclick="window.location='unread-enquiry.php';">
                                <div class="card-header">
                                    <i class="fa fa-envelope icon"></i>
                                    Unread Enquiries
                                </div>
                                <div class="card-body">
                                    <?php 
                                        // Query for unread enquiries
                                        $sql1 = "SELECT * FROM tblcontact WHERE Isread IS NULL";
                                        $query1 = $dbh->prepare($sql1);
                                        $query1->execute();
                                        $totunreadqueries = $query1->rowCount();
                                    ?>
                                    <h3 class="card-title"><?php echo htmlentities($totunreadqueries); ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Walk-In Reservations Card -->
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-4">
                            <div class="card bg-warning text-dark" onclick="window.location='walk_in_reservation.php';">
                                <div class="card-header">
                                    <i class="fa fa-user-circle icon"></i>
                                    Walk-In Reservations
                                </div>
                                <div class="card-body">
                                    <h3 class="card-title">0</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Add Transaction Card -->
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-4">
                            <div class="card bg-primary text-light" onclick="window.location='add-transaction.php';">
                                <div class="card-header">
                                    <i class="fa fa-plus icon"></i>
                                    Add Transaction
                                </div>
                                <div class="card-body">
                                    <h3 class="card-title">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                </div> <!-- End of container-fluid -->
            </div> <!-- End of content -->
        </div>
        
        <!-- Sidebar -->
        <?php include_once('includes/sidebar.php'); ?>
        <div class="clearfix"></div>
    </div> <!-- //inner-content -->
</div> <!-- //page-container -->

<script>
    var toggle = true;
    $(".sidebar-icon").click(function() {
        if (toggle) {
            $(".page-container").addClass("sidebar-collapsed").removeClass("sidebar-collapsed-back");
            $("#menu span").css({ "position": "absolute" });
            $(".brand-title").css({ "display": "none" });
        } else {
            $(".page-container").removeClass("sidebar-collapsed").addClass("sidebar-collapsed-back");
            setTimeout(function() {
                $("#menu span").css({ "position": "relative" });
            }, 400);
            $(".brand-title").css({ "display": "block" });
        }
        toggle = !toggle;
    });
</script>

</body>
</html>
<?php } ?>