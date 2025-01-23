<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['hbmsaid']==0)) {
  header('location:logout.php');
} else{
    if (isset($_POST['submit'])) {
        $uid = $_SESSION['hbmsuid'];
        $AName = $_POST['fname'];
        $mobno = $_POST['mobno'];

        // Check if a file is uploaded
        if (!empty($_FILES['profile_image']['name'])) {
            $file_name = $_FILES['profile_image']['name'];
            $file_tmp = $_FILES['profile_image']['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_extensions = array("jpeg", "jpg", "png");

            if (!in_array($file_ext, $allowed_extensions)) {
                echo "extension not allowed, please choose a JPEG or PNG file.";
                exit;
            }

            // Define the target directory to store the uploaded image
            $target_dir = "uploads/pfp/";
            $target_file = $target_dir . basename($file_name);

            // Move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                // Update the database with the profile image file name
                $sql = "UPDATE tbluser SET FullName=:name, MobileNumber=:mobilenumber, profile_image=:profile_image WHERE ID=:uid";
            } else {
                echo "Failed to upload file.";
                exit;
            }
        } else {
            // Update the database without changing the profile image file name
            $sql = "UPDATE tbluser SET FullName=:name, MobileNumber=:mobilenumber WHERE ID=:uid";
        }

        // Prepare and execute the SQL query
        $query = $dbh->prepare($sql);
        $query->bindParam(':name', $AName, PDO::PARAM_STR);
        $query->bindParam(':mobilenumber', $mobno, PDO::PARAM_STR);
        $query->bindParam(':uid', $uid, PDO::PARAM_STR);

        // Bind the profile image file name parameter only if a file is uploaded
        if (!empty($_FILES['profile_image']['name'])) {
            $query->bindParam(':profile_image', $file_name, PDO::PARAM_STR);
        }

        if ($query->execute()) {
            echo '<script>alert("Profile has been updated")</script>';
        } else {
            echo "Failed to execute SQL query.";
            exit;
        }
    }
}

// Fetch the profile image file name
$sql_fetch_image = "SELECT profile_image FROM tbluser WHERE ID=:uid";
$query_fetch_image = $dbh->prepare($sql_fetch_image);
$query_fetch_image->bindParam(':uid', $_SESSION['hbmsuid'], PDO::PARAM_STR);
$query_fetch_image->execute();
$row_fetch_image = $query_fetch_image->fetch(PDO::FETCH_ASSOC);
$profile_image = $row_fetch_image['profile_image']; // Use correct column name here
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Hotel Booking Management System | Reg Users</title>

<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- Bootstrap Core CSS -->
<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
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
                        <!-- <h2>Register Users</h2> -->
                    </div>
                    <div class="panel panel-widget forms-panel">
                        <div class="forms">
                        <form method="post" enctype="multipart/form-data">
                        <?php
                        $uid = $_SESSION['hbmsuid'];
                        $sql = "SELECT * FROM tbluser WHERE ID=:uid";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':uid', $uid, PDO::PARAM_STR);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        $cnt = 1;
                        if ($query->rowCount() > 0) {
                            foreach ($results as $row) {
                                ?>
                                <h5>Full Name</h5>
                                <input type="text" value="<?php echo $row->FullName; ?>"
                                       name="fname" required="true" class="form-control">
                                <h5>Mobile Number</h5>
                                <input type="text" name="mobno" class="form-control" required="true" maxlength="10"
                                       pattern="[0-9]+" value="<?php echo $row->MobileNumber; ?>">
                                <h5>Email Address</h5>
                                <input type="email" class="form-control" value="<?php echo $row->Email; ?>"
                                       name="email" required="true" readonly='true'>
                                <h5>Registration Date</h5>
                                <input type="text" value="<?php echo $row->RegDate; ?>" class="form-control"
                                       name="password" readonly="true">
                                <br/><?php $cnt = $cnt + 1;
                            }
                        } ?>
                        <h5>Profile Image</h5>
                        <input type="file" name="profile_image" class="form-control">
                        <br/>
                        <input type="submit" value="Update" name="submit">
                    </form>

    
                </div>

    <!-- end content -->
    

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
				shadowSize: 0	// Drawing is faster without shadows
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
</body>
</html><?php   ?>
