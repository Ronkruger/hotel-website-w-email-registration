<?php
session_start();
// error_reporting(E_ALL);
error_reporting(0);
// ini_set('display_errors', 1);
include('includes/dbconnection.php');

// Redirect to login if the session is not set
if (strlen($_SESSION['hbmsaid'] == 0)) {
    header('location:logout.php');
} else {
    // Process the reservation form submission
    if (isset($_POST['submit'])) {
        // Collect form data
        $name = $_POST['name'];
        $contact = $_POST['contact'];
        $check_in = $_POST['check_in'];
        $check_out = $_POST['check_out'];
        $people = $_POST['people'];
        $food_beverages = $_POST['food_beverages'];

        // Calculate total days
        $checkInDate = new DateTime($check_in);
        $checkOutDate = new DateTime($check_out);
        $interval = $checkInDate->diff($checkOutDate);
        $total_days = $interval->days;

        // Get selected room IDs
        $room_ids = isset($_POST['room_ids']) ? $_POST['room_ids'] : [];
        
        // Initialize total price
        $total_price = 0;

        // Fetch room prices for selected rooms
        $room_prices = [];
        if (!empty($room_ids)) {
            $room_ids_placeholder = implode(',', array_fill(0, count($room_ids), '?'));
            $sql_prices = "
                SELECT c.Price 
                FROM tblroom r 
                JOIN tblcategory c ON r.RoomType = c.ID 
                WHERE r.ID IN ($room_ids_placeholder)";
            $query_prices = $dbh->prepare($sql_prices);
            $query_prices->execute($room_ids);
            $room_prices = $query_prices->fetchAll(PDO::FETCH_COLUMN);
        }

        // Calculate total price for the stay
        foreach ($room_prices as $price) {
            $total_price += $price * $total_days;
        }

        // Insert each room reservation into the database
        foreach ($room_ids as $room_id) {
            $sql = "INSERT INTO tblwalkin (name, contact, check_in, check_out, people, RoomId, food_beverages, TotalPrice, created_at) VALUES (:name, :contact, :check_in, :check_out, :people, :room_id, :food_beverages, :total_price, NOW())";
            $query = $dbh->prepare($sql);
            $query->bindParam(':name', $name);
            $query->bindParam(':contact', $contact);
            $query->bindParam(':check_in', $check_in);
            $query->bindParam(':check_out', $check_out);
            $query->bindParam(':people', $people);
            $query->bindParam(':room_id', $room_id);
            $query->bindParam(':food_beverages', $food_beverages);
            $query->bindParam(':total_price', $total_price);
            $query->execute();
        }

        // Redirect to summary page
        header("Location: summary.php?total_price=$total_price&days=$total_days&food_beverages=" . urlencode($food_beverages));
        exit;
    }

    // Fetch rooms from database
    $sql_rooms = "SELECT r.ID, r.RoomName, r.RoomDesc, r.Image, c.Price 
                  FROM tblroom r 
                  JOIN tblcategory c ON r.RoomType = c.ID";
    $query_rooms = $dbh->prepare($sql_rooms);
    $query_rooms->execute();
    $rooms = $query_rooms->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In Check-Out Reservation</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
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
 <script type="text/javascript">

        $(document).ready(function () {
            $('#demo-pie-1').pieChart({
                barColor: '#3bb2d0',
                trackColor: '#eee',
                lineCap: 'round',
                lineWidth: 8,
                onStep: function (from, to, percent) {
                    $(this.element).find('.pie-value').text(Math.round(percent) + '%');
                }
            });

            $('#demo-pie-2').pieChart({
                barColor: '#fbb03b',
                trackColor: '#eee',
                lineCap: 'butt',
                lineWidth: 8,
                onStep: function (from, to, percent) {
                    $(this.element).find('.pie-value').text(Math.round(percent) + '%');
                }
            });

            $('#demo-pie-3').pieChart({
                barColor: '#ed6498',
                trackColor: '#eee',
                lineCap: 'square',
                lineWidth: 8,
                onStep: function (from, to, percent) {
                    $(this.element).find('.pie-value').text(Math.round(percent) + '%');
                }
            });

           
        });

    </script>
  <style>
        body {
            background-color: #f9fafe;
            color: #343a40;
            font-family: 'Roboto', sans-serif;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            color: #0d6efd;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .room-box {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .room-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .image-container {
            height: 150px;
            overflow: hidden;
            border-radius: 8px;
        }

        .room-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .room-details {
            text-align: left;
            margin-top: 10px;
        }

        .room-details h4 {
            font-size: 18px;
            color: #0d6efd;
            margin-bottom: 5px;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }

        .room-details p {
            font-size: 14px;
            color: #6c757d;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }

        .room-price {
            font-weight: bold;
            font-size: 16px;
            color: #dc3545;
        }

        .btn-primary {
            width: 100%;
            font-weight: bold;
            padding: 12px;
            background-color: #0d6efd;
            border: none;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #084298;
        }
        .container{
            margin-left: 15rem;
        }
    </style>
</head>
<body>
    <?php include_once('includes/header.php'); ?>
    <div class="container my-5">
        <h2>Walk-in Reservation</h2>
        <form method="post" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact Number:</label>
                        <input type="text" class="form-control" id="contact" name="contact" required>
                    </div>
                    <div class="form-group">
                        <label for="check_in">Check-In Date:</label>
                        <input type="date" class="form-control" id="check_in" name="check_in" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="check_out">Check-Out Date:</label>
                        <input type="date" class="form-control" id="check_out" name="check_out" required>
                    </div>
                    <div class="form-group">
                        <label for="people">Number of People:</label>
                        <input type="number" class="form-control" id="people" name="people" required>
                    </div>
                    <div class="form-group">
                        <label for="food_beverages">Food/Beverages Requested:</label>
                        <textarea class="form-control" id="food_beverages" name="food_beverages" placeholder="Enter food/beverage requests (comma-separated)"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-group my-3">
                <label for="room_search">Search Rooms:</label>
                <input type="text" class="form-control" id="room_search" placeholder="Enter room name or description">
            </div>

            <label>Select Room(s):</label>
            <div class="row" id="rooms-container">
                <?php foreach ($rooms as $room) : ?>
                    <div class="col-md-4 mb-3">
                        <div class="room-box" data-room-name="<?php echo $room['RoomName']; ?>" data-room-desc="<?php echo $room['RoomDesc']; ?>">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="room_ids[]" value="<?php echo $room['ID']; ?>" id="room-<?php echo $room['ID']; ?>">
                                <label class="form-check-label" for="room-<?php echo $room['ID']; ?>">
                                    Select
                                </label>
                            </div>
                            <div class="image-container">
                                <img src="images/<?php echo $room['Image']; ?>" alt="<?php echo $room['RoomName']; ?>">
                            </div>
                            <div class="room-details">
                                <h4><?php echo $room['RoomName']; ?></h4>
                                <p><?php echo $room['RoomDesc']; ?></p>
                                <div class="room-price">₱<?php echo $room['Price']; ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Submit Reservation</button>
        </form>
    </div>
    <script>
        document.getElementById('room_search').addEventListener('input', function() {
            const searchQuery = this.value.toLowerCase();
            const rooms = document.querySelectorAll('.room-box');
            
            rooms.forEach(room => {
                const roomName = room.getAttribute('data-room-name').toLowerCase();
                const roomDesc = room.getAttribute('data-room-desc').toLowerCase();

                if (roomName.includes(searchQuery) || roomDesc.includes(searchQuery)) {
                    room.style.display = 'block';
                } else {
                    room.style.display = 'none';
                }
            });
        });
    </script>
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
</html>
