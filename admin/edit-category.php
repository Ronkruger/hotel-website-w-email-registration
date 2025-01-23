<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['hbmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_POST['submit'])) {
        $hbmsaid = $_SESSION['hbmsaid'];
        $categoryname = $_POST['categoryname'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $date = $_POST['date'];
        $cid = $_GET['editid'];

        $sql = "UPDATE tblcategory SET CategoryName=:categoryname, Description=:description, Price=:price, Date=:date WHERE ID=:cid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':categoryname', $categoryname, PDO::PARAM_STR);
        $query->bindParam(':description', $description, PDO::PARAM_STR);
        $query->bindParam(':price', $price, PDO::PARAM_STR);
        $query->bindParam(':date', $date, PDO::PARAM_STR);
        $query->bindParam(':cid', $cid, PDO::PARAM_STR);
        $query->execute();

        echo '<script>alert("Category detail has been updated")</script>';
        echo "<script>window.location.href ='manage-category.php'</script>";
    }
}
?>

<!DOCTYPE HTML>
<html>
<head>
<title>Hotel Booking Management System | Edit Category</title>

<!-- Include Bootstrap CSS -->
<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<!-- Custom CSS -->
<link href="css/style.css" rel='stylesheet' type='text/css' />
<!-- Font Awesome -->
<link href="css/font-awesome.css" rel="stylesheet"> 

<!-- jQuery -->
<script src="js/jquery-1.10.2.min.js"></script>
<!-- Bootstrap -->
<script src="js/bootstrap.min.js"></script>
<style>
		@import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');
		*{
			font-family: "Ubuntu", sans-serif;
		}
		.form-group{
			border: 5px solid;
			margin: auto;
			width: 40%;
			padding:70px;
			/* From https://css.glass */
			background: rgba(255, 255, 255, 0.2);
			border-radius: 16px;
			box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
			backdrop-filter: blur(5px);
			-webkit-backdrop-filter: blur(5px);
			border: 1px solid rgba(255, 255, 255, 0.3);
			
		}
		.form-control{
			width: 250px;
			height: 50px;
			margin: auto;
		}
	
		label{
			font-weight: bold;
			fonts-size: 35px;
		}
		.panel{
			background-color: transparent;
			border-radius: 28px;
		}
		</style>
</head> 
<body>
   <div class="page-container">
	<div class="left-content">
	   <div class="inner-content">
		<!-- header-starts -->
		<?php include_once('includes/header.php');?>
				
		<!-- Content -->
		<div class="content">
			<div class="women_main">
				<div class="grids">
					<div class="progressbar-heading grids-heading">
			
					</div>
					<div class="panel panel-widget forms-panel">
						<div class="forms">
							<div class="form-grids widget-shadow" data-example-id="basic-forms"> 
								<!-- <div class="form-title">
									<h4>Edit Category</h4>
								</div> -->
								<div class="form-body">
									<form method="post" enctype="multipart/form-data">
                                 
										<?php
                                        $cid = $_GET['editid'];
                                        $sql = "SELECT * FROM tblcategory WHERE ID=:cid";
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':cid', $cid, PDO::PARAM_STR);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt = 1;
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $row) {
                                                ?>
										<div class="form-group"> 
                                        <h2 style="text-align:center; color:black;">Edit Category</h2>
                                        
                                            <label for="categoryname">Category Name</label> 
                                            <input type="text" class="form-control" name="categoryname" value="<?php echo $row->CategoryName; ?>" required="true"> 
                                       
                                                <br>
                                            <label for="description">Description</label> 
                                            <textarea class="form-control" name="description"><?php echo $row->Description; ?></textarea> 
                                      
                                                <br>
                                            <label for="price">Price</label> 
                                            <input type="text" class="form-control" name="price" value="<?php echo $row->Price; ?>" required="true"> 
                                                <br>
                                            <label for="date">Date</label> 
                                            <input type="text" class="form-control" name="date" value="<?php echo $row->Date; ?>" required="true"> 
                                                <br>
                                        <?php }} ?>
                                        <div align="center">
                                        <button type="submit" class="btn btn-default" name="submit">Update</button> 
                                        </div>
										
									</form> 
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- //Content -->

	</div>
</div>
<!--sidebar-menu-->
<?php include_once('includes/sidebar.php');?>
<div class="clearfix"></div>		
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
</div>
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
</body>
</html>
