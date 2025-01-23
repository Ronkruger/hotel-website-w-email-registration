<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['hbmsaid']==0)) {
    header('location:logout.php');
} else{
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Hotel Booking Management System | All Booking</title>

    <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Custom CSS -->
    <link href="css/style.css" rel='stylesheet' type='text/css' />
    <!-- Graph CSS -->
    <link href="css/font-awesome.css" rel="stylesheet"> 
    <!-- jQuery -->
    <link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet' type='text/css'/>
    <link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <!-- lined-icons -->
    <link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
    <script src="js/simpleCart.min.js"> </script>
    <script src="js/amcharts.js"></script>    
    <script src="js/serial.js"></script>    
    <script src="js/light.js"></script>    
    <!-- //lined-icons -->
    <script src="js/jquery-1.10.2.min.js"></script>
    <!--pie-chart--->
    <script src="js/pie-chart.js" type="text/javascript"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');
        *{
            font-family: "Ubuntu", sans-serif;
        }
    </style>
</head> 
<body>
    <div class="page-container">
    <!--/content-inner-->
    <div class="left-content">
       <div class="inner-content">
        <!-- header-starts -->
            <?php include_once('includes/header.php');?>
                
             <!-- Content -->
<div class="content">
    <div class="booking-container">
        <header class="header">
            <h2>All Bookings</h2>
        </header>

        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>S.No</th>
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
                        if (isset($_GET['pageno'])) {
                            $pageno = $_GET['pageno'];
                        } else {
                            $pageno = 1;
                        }
                        
                        // Pagination logic
                        $no_of_records_per_page = 10;
                        $offset = ($pageno - 1) * $no_of_records_per_page;

                        $query1 = $dbh->prepare("SELECT ID FROM tblbooking");
                        $query1->execute();
                        $total_rows = $query1->rowCount();
                        $total_pages = ceil($total_rows / $no_of_records_per_page);

                        $sql = "SELECT tbluser.*, tblbooking.BookingNumber, tblbooking.ID, tblbooking.Status, tblbooking.BookingDate 
                                FROM tblbooking 
                                JOIN tbluser ON tblbooking.UserID = tbluser.ID 
                                LIMIT $offset, $no_of_records_per_page";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);

                        $cnt = 1;
                        if ($query->rowCount() > 0) {
                            foreach ($results as $row) { ?>
                                <tr>
                                    <td class="text-center"><?php echo htmlentities($cnt); ?></td>
                                    <td><?php echo htmlentities($row->BookingNumber); ?></td>
                                    <td><?php echo htmlentities($row->FullName); ?></td>
                                    <td><?php echo htmlentities($row->Email); ?></td>
                                    <td><?php echo htmlentities($row->MobileNumber); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo htmlentities($row->BookingDate); ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                        $status = $row->Status;
                                        if ($status == '') { ?>
                                            <span class="badge bg-warning">Not Updated Yet</span>
                                        <?php } elseif ($status == 'Cancelled') { ?>
                                            <span class="badge bg-danger"><?php echo htmlentities($row->Status); ?></span>
                                        <?php } elseif ($status == 'Approved') { ?>
                                            <span class="badge bg-success"><?php echo htmlentities($row->Status); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <a href="view-booking-detail.php?bookingid=<?php echo htmlentities($row->BookingNumber); ?>" class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i> View Details
                                        </a>
                                        <a href="javascript:void(0);" onclick="printBooking('<?php echo htmlentities($row->BookingNumber); ?>');" class="btn btn-primary btn-sm">
                                            <i class="fa fa-print"></i> Print
                                        </a>
                                    </td>
                                </tr>
                        <?php $cnt++; }
                        } else { ?>
                            <tr>
                                <td colspan="8" class="text-center text-danger" style="font-size: 22px;">No record found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div class="pagination">
                    <ul class="pagination-list">
                        <li class="<?php if ($pageno <= 1) echo 'disabled'; ?>">
                            <a href="<?php if ($pageno <= 1) echo '#'; else echo "?pageno=" . ($pageno - 1); ?>" class="page-link">Prev</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                            <li class="<?php if ($pageno == $i) echo 'active'; ?>">
                                <a href="?pageno=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a>
                            </li>
                        <?php } ?>
                        <li class="<?php if ($pageno >= $total_pages) echo 'disabled'; ?>">
                            <a href="<?php if ($pageno >= $total_pages) echo '#'; else echo "?pageno=" . ($pageno + 1); ?>" class="page-link">Next</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Unique CSS Styles -->
<style>
    .booking-container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .header {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007bff;
    }

    .header h2 {
        color: #333;
        margin: 0;
    }

    .card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    .table {
        margin-bottom: 0;
        border-collapse: collapse;
        width: 100%;
    }

    .table th, .table td {
        padding: 12px;
        text-align: left;
    }

    .table th {
        background-color: #f2f2f2;
        border-bottom: 2px solid #e0e0e0;
    }

    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 5px;
        color: white;
        font-size: 0.85em;
    }

    .pagination {
        text-align: center;
        margin-top: 20px;
    }

    .pagination-list {
        display: inline-flex;
        list-style: none;
        padding: 0;
    }

    .pagination-list li {
        margin: 0 5px;
    }

    .pagination-list a.page-link {
        padding: 10px 15px;
        border: 1px solid #007bff;
        border-radius: 5px;
        color: #007bff;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .pagination-list li.active a.page-link {
        background-color: #007bff;
        color: white;
    }

    .pagination-list li.disabled a.page-link {
        pointer-events: none;
        color: #6c757d;
    }

    .pagination-list a.page-link:hover {
        background-color: #e9ecef;
    }
</style>

<!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
            <!--content-->
        </div>
</div>
                <!--//content-inner-->
            <!--/sidebar-menu-->
            <?php include_once('includes/sidebar.php');?>
                              <div class="clearfix"></div>        
                            </div>
                });
                            </script>
                            <script>
                            var toggle = true;
                                        
                            $(".sidebar-icon").click(function() {                
                              if (toggle)
                              {
                                $(".page-container").addClass("sidebar-collapsed").removeClass("sidebar-collapsed-back");
                                $("#menu span").css({"position":"absolute"});
                                $(".brand-title").css({"display":"none"})
                              }
                              else
                              {
                                $(".page-container").removeClass("sidebar-collapsed").addClass("sidebar-collapsed-back");
                                setTimeout(function() {
                                  $("#menu span").css({"position":"relative"});
                                }, 400);
                                $(".brand-title").css({"display":"block"})
                              }
                                            
                                            toggle = !toggle;
                                        });
                            </script>
<!--js -->
<script src="js/jquery.nicescroll.js"></script>
<script src="js/scripts.js"></script>
<!-- Bootstrap Core JavaScript -->
   <script src="js/bootstrap.min.js"></script>
   <!-- /Bootstrap Core JavaScript -->
   <!-- real-time -->
<script language="javascript" type="text/javascript" src="js/jquery.flot.js"></script>
    <script type="text/javascript">

    $(function() {

        // We use an inline data source in the example, usually data would
        // be fetched from a server

        var data = [],
            totalPoints = 300;

        function getRandomData() {

            if (data.length > 0)
                data = data.slice(1);

            // Do a random walk

            while (data.length < totalPoints) {

                var prev = data.length > 0 ? data[data.length - 1] : 50,
                    y = prev + Math.random() * 10 - 5;

                if (y < 0) {
                    y = 0;
                } else if (y > 100) {
                    y = 100;
                }

                data.push(y);
            }

            // Zip the generated y values with the x values

            var res = [];
            for (var i = 0; i < data.length; ++i) {
                res.push([i, data[i]])
            }

            return res;
        }

        // Set up the control widget

        var updateInterval = 30;
        $("#updateInterval").val(updateInterval).change(function () {
            var v = $(this).val();
            if (v && !isNaN(+v)) {
                updateInterval = +v;
                if (updateInterval < 1) {
                    updateInterval = 1;
                } else if (updateInterval > 2000) {
                    updateInterval = 2000;
                }
                $(this).val("" + updateInterval);
            }
        });

        var plot = $.plot("#placeholder", [ getRandomData() ], {
            series: {
                shadowSize: 0    // Drawing is faster without shadows
            },
            yaxis: {
                min: 0,
                max: 100
            },
            xaxis: {
                show: false
            }
        });

        function update() {

            plot.setData([getRandomData()]);

            // Since the axes don't change, we don't need to call plot.setupGrid()

            plot.draw();
            setTimeout(update, updateInterval);
        }

        update();

        // Add the Flot version string to the footer

        $("#footer").prepend("Flot " + $.plot.version + " &ndash; ");
    });

    </script>
<!-- /real-time -->
<script src="js/jquery.fn.gantt.js"></script>
    <script>

        $(function() {

            "use strict";

            $(".gantt").gantt({
                source: [{
                    name: "Sprint 0",
                    desc: "Analysis",
                    values: [{
                        from: "/Date(1320192000000)/",
                        to: "/Date(1322401600000)/",
                        label: "Requirement Gathering", 
                        customClass: "ganttRed"
                    }]
                },{
                    name: " ",
                    desc: "Scoping",
                    values: [{
                        from: "/Date(1322611200000)/",
                        to: "/Date(1323302400000)/",
                        label: "Scoping", 
                        customClass: "ganttRed"
                    }]
                },{
                    name: "Sprint 1",
                    desc: "Development",
                    values: [{
                        from: "/Date(1323802400000)/",
                        to: "/Date(1325685200000)/",
                        label: "Development", 
                        customClass: "ganttGreen"
                    }]
                },{
                    name: " ",
                    desc: "Showcasing",
                    values: [{
                        from: "/Date(1325685200000)/",
                        to: "/Date(1325695200000)/",
                        label: "Showcasing", 
                        customClass: "ganttBlue"
                    }]
                },{
                    name: "Sprint 2",
                    desc: "Development",
                    values: [{
                        from: "/Date(1326785200000)/",
                        to: "/Date(1325785200000)/",
                        label: "Development", 
                        customClass: "ganttGreen"
                    }]
                },{
                    name: " ",
                    desc: "Showcasing",
                    values: [{
                        from: "/Date(1328785200000)/",
                        to: "/Date(1328905200000)/",
                        label: "Showcasing", 
                        customClass: "ganttBlue"
                    }]
                },{
                    name: "Release Stage",
                    desc: "Training",
                    values: [{
                        from: "/Date(1330011200000)/",
                        to: "/Date(1336611200000)/",
                        label: "Training", 
                        customClass: "ganttOrange"
                    }]
                },{
                    name: " ",
                    desc: "Deployment",
                    values: [{
                        from: "/Date(1336611200000)/",
                        to: "/Date(1338711200000)/",
                        label: "Deployment", 
                        customClass: "ganttOrange"
                    }]
                },{
                    name: " ",
                    desc: "Warranty Period",
                    values: [{
                        from: "/Date(1336611200000)/",
                        to: "/Date(1349711200000)/",
                        label: "Warranty Period", 
                        customClass: "ganttOrange"
                    }]
                }],
                navigate: "scroll",
                scale: "weeks",
                maxScale: "months",
                minScale: "days",
                itemsPerPage: 10,
                onItemClick: function(data) {
                    alert("Item clicked - show some details");
                },
                onAddClick: function(dt, rowId) {
                    alert("Empty space clicked - add an item!");
                },
                onRender: function() {
                    if (window.console && typeof console.log === "function") {
                        console.log("chart rendered");
                    }
                }
            });

            $(".gantt").popover({
                selector: ".bar",
                title: "I'm a popover",
                content: "And I'm the content of said popover.",
                trigger: "hover"
            });

            prettyPrint();

        });

    </script>
           <script src="js/menu_jquery.js"></script>

           <script src="js/pages/be_tables_datatables.js"></script>

           <!-- Print Functionality -->
           <script>
               function printBooking(bookingNumber) {
                   window.open('print-booking.php?bookingNumber=' + bookingNumber, '_blank');
               }
           </script>
</body>
</html>
<?php }  ?>
