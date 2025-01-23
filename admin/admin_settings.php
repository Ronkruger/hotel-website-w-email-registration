<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "hbmsdb");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if logo form is submitted
if (isset($_POST["submit_logo"])) {
    // Retrieve logo data
    $logo_name = $_FILES['logo']['name'];
    $logo_tmp = $_FILES['logo']['tmp_name'];
    $logo_path = "../uploads/" . $logo_name;
    move_uploaded_file($logo_tmp, $logo_path);

    // Check if settings already exist
    $check_sql = "SELECT * FROM tblsettings WHERE id=1";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Settings exist, update logo
        $update_logo_sql = "UPDATE tblsettings SET logo='$logo_name' WHERE id=1";
        if ($conn->query($update_logo_sql) === TRUE) {
            echo "Logo updated successfully.";

            // Store the scroll position in session
            $_SESSION['scroll_position'] = $_POST['scroll_position'];

            // Redirect to the same page to prevent form resubmission
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        } else {
            echo "Error updating logo: " . $conn->error;
        }
    } else {
        // No settings found, insert new settings
        $insert_sql = "INSERT INTO tblsettings (id, logo) VALUES (1, '$logo_name')";
        if ($conn->query($insert_sql) === TRUE) {
            echo "Logo inserted successfully.";

            // Store the scroll position in session
            $_SESSION['scroll_position'] = $_POST['scroll_position'];

            // Redirect to the same page to prevent form resubmission
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        } else {
            echo "Error inserting logo: " . $conn->error;
        }
    }
}

// Check if scroll position is stored in session
$scroll_position = isset($_SESSION['scroll_position']) ? $_SESSION['scroll_position'] : 0;


if (isset($_POST["submit_bg_image"])) {
    // Retrieve background image data
    $bg_image_name = $_FILES['background_image']['name'];
    $bg_image_tmp = $_FILES['background_image']['tmp_name'];
    $bg_image_path = "../uploads/" . $bg_image_name;
    move_uploaded_file($bg_image_tmp, $bg_image_path);

    // Check if settings already exist
    $check_sql = "SELECT * FROM tblsettings WHERE id=1";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Settings exist, update background image
        $update_bg_image_sql = "UPDATE tblsettings SET background_image='$bg_image_name' WHERE id=1";
        if ($conn->query($update_bg_image_sql) === TRUE) {
            echo "Background image updated successfully.";

            // Store the scroll position in session
            $_SESSION['scroll_position'] = $_POST['scroll_position'];

            // Redirect to the same page to prevent form resubmission
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        } else {
            echo "Error updating background image: " . $conn->error;
        }
    } else {
        // No settings found, insert new settings
        $insert_sql = "INSERT INTO tblsettings (id, background_image) VALUES (1, '$bg_image_name')";
        if ($conn->query($insert_sql) === TRUE) {
            echo "Background image inserted successfully.";

            // Store the scroll position in session
            $_SESSION['scroll_position'] = $_POST['scroll_position'];

            // Redirect to the same page to prevent form resubmission
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        } else {
            echo "Error inserting background image: " . $conn->error;
        }
    }
}

// Check if scroll position is stored in session
$scroll_position = isset($_SESSION['scroll_position']) ? $_SESSION['scroll_position'] : 0;

// Check if background color form is submitted
if (isset($_POST["submit_bg_color"])) {
    // Retrieve background color data
    $background_color = $_POST['background_color'];

    // Check if settings already exist
    $check_sql = "SELECT * FROM tblsettings WHERE id=1";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Settings exist, update background color
        $update_bg_color_sql = "UPDATE tblsettings SET background_color='$background_color' WHERE id=1";
        if ($conn->query($update_bg_color_sql) === TRUE) {
            echo "Background color updated successfully.";

            // Store the scroll position in session
            $_SESSION['scroll_position'] = $_POST['scroll_position'];

            // Redirect to the same page to prevent form resubmission
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        } else {
            echo "Error updating background color: " . $conn->error;
        }
    } else {
        // No settings found, insert new settings
        $insert_sql = "INSERT INTO tblsettings (id, background_color) VALUES (1, '$background_color')";
        if ($conn->query($insert_sql) === TRUE) {
            echo "Background color inserted successfully.";

            // Store the scroll position in session
            $_SESSION['scroll_position'] = $_POST['scroll_position'];

            // Redirect to the same page to prevent form resubmission
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        } else {
            echo "Error inserting background color: " . $conn->error;
        }
    }
}

// Check if scroll position is stored in session
$scroll_position = isset($_SESSION['scroll_position']) ? $_SESSION['scroll_position'] : 0;
// Check if reset form is submitted
if (isset($_POST["reset"])) {
    // Reset settings to default values
    $reset_sql = "UPDATE tblsettings SET logo='', background_image='', background_color='#ffffff', font_color='#000000' WHERE id=1";
    if ($conn->query($reset_sql) === TRUE) {
        echo "Settings reset to default successfully.";

        // Store the scroll position in session
        $_SESSION['scroll_position'] = $_POST['scroll_position'];

        // Redirect to the same page to prevent form resubmission
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    } else {
        echo "Error resetting settings: " . $conn->error;
    }
}

// Check if scroll position is stored in session
$scroll_position = isset($_SESSION['scroll_position']) ? $_SESSION['scroll_position'] : 0;

// Check if font color form is submitted
if (isset($_POST["submit_font_color"])) {
    // Retrieve font color data
    $font_color = $_POST['font_color']; // This is a hexadecimal color code, e.g., #RRGGBB

    // Your database connection and query to update the font color
    // Make sure to sanitize user input to prevent SQL injection

    // For demonstration purposes, let's assume you have $conn as your database connection

    // Your SQL to update the font color in the database
    $update_font_color_sql = "UPDATE tblsettings SET font_color='$font_color' WHERE id=1";

    if ($conn->query($update_font_color_sql) === TRUE) {
        echo "Font color updated successfully.";
        
            // Store the scroll position in session
            $_SESSION['scroll_position'] = $_POST['scroll_position'];

            // Redirect to the same page to prevent form resubmission
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
    } else {
        echo "Error updating font color: " . $conn->error;
    }
}
// Check if scroll position is stored in session
$scroll_position = isset($_SESSION['scroll_position']) ? $_SESSION['scroll_position'] : 0;
// Retrieve the current font color from the database
// For demonstration purposes, let's assume you have $conn as your database connection

$current_font_color = ''; // Initialize the variable

$get_font_color_sql = "SELECT font_color FROM tblsettings WHERE id=1";
$get_font_color_result = $conn->query($get_font_color_sql);

if ($get_font_color_result->num_rows > 0) {
    // Font color settings exist
    $row = $get_font_color_result->fetch_assoc();
    $current_font_color = $row['font_color'];
}



// Close connection
$conn->close();
?>

<!DOCTYPE HTML>
<html>
<head>
<title>Hotel Booking Management System | Add Category</title>

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
		@import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');
		*{
			font-family: "Ubuntu", sans-serif;
		}
  /* table {
    border-collapse: collapse;
    width: 100%;
  }
  th, td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }
  tr:nth-child(even) {
    background-color: #f2f2f2;
  } */
  .btn {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .btn:hover {
    background-color: #45a049;
  }
  .btn-reset {
    background-color: black;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .btn-reset:hover {
    background-color: #45a049;
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
         <!--content-->
         <div class="content">
            <div class="women_main">
               <!-- start content -->
               <div class="grids">
                  <div class="progressbar-heading grids-heading">
                     <h2 style="text-align:center; color: black">Edit Home Page</h2>
                  </div>
         
<!-- Font Color Form -->
<div class="panel panel-widget forms-panel">
    <div class="forms">
        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
            <div class="form-body">
                <!-- HTML form to input font color -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <table class="table table-bordered table-hover">
                        <!-- Font Color -->
                        <tr>
                            <td>Font Color:</td>
                            <td>
                            <input type="hidden" name="scroll_position" value="<?php echo $scroll_position; ?>">
                                <input type="color" name="font_color" value="<?php echo $current_font_color; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><input class="btn" type="submit" name="submit_font_color" value="Save"></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>




                  <!-- Logo Form -->
                  <div class="panel panel-widget forms-panel">
                     <div class="forms">
                        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
                           <!-- <div class="form-title">
                              <h4>Edit Home Page :</h4>
                           </div> -->
                           <div class="form-body">
                              <!-- HTML form to input logo -->
                              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                                 <table class="table table-bordered table-hover">
                                    <tr>
                                       <td>Logo:</td>
                                       <input type="hidden" name="scroll_position" value="<?php echo $scroll_position; ?>">
                                       <td><input type="file" name="logo"></td>
                                    </tr>
                                    <tr>
                                       <td colspan="2"><input class="btn" type="submit" name="submit_logo" value="Save"></td>
                                    </tr>
                                 </table>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- Background Image Form -->
                  <div class="panel panel-widget forms-panel">
                     <div class="forms">
                        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
                           <div class="form-body">
                              <!-- HTML form to input background image -->
                              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                                 <table class="table table-bordered table-hover">
                                    <tr>
                                       <td>Background Image:</td>
                                       <input type="hidden" name="scroll_position" value="<?php echo $scroll_position; ?>">
                                       <td><input type="file" name="background_image"></td>
                                    </tr>
                                    <tr>
                                       <td colspan="2"><input class="btn" type="submit" name="submit_bg_image" value="Save"></td>
                                    </tr>
                                 </table>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- Background Color Form -->
                  <div class="panel panel-widget forms-panel">
                     <div class="forms">
                        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
                           <div class="form-body">
                              <!-- HTML form to input background color -->
                              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                 <table class="table table-bordered table-hover">
                                    <tr>
                                       <td>Background Color:</td>
                                       <input type="hidden" name="scroll_position" value="<?php echo $scroll_position; ?>">
                                       <td><input type="color" name="background_color"></td>
                                    </tr>
                                    <tr>
                                       <td colspan="2"><input class="btn" type="submit" name="submit_bg_color" value="Save"></td>
                                    </tr>
                                 </table>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               

               <div class="panel panel-widget forms-panel">
                     <div class="forms">
                        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
                           <div class="form-body">
                              <!-- HTML form to input background color -->
                              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                 <table class="table table-bordered table-hover">
                                 <tr>
                                 <input type="hidden" name="scroll_position" value="<?php echo $scroll_position; ?>">
                                       <td colspan="2"><input class="btn" type="submit" name="reset" value="Reset settings"></td>
                                    </tr>
                                 </table>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- end content -->
            </div>
            <!--content-->
         </div>
      </div>
      <!--/content-inner-->
      <!--sidebar-menu-->
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
            $(".brand-title").css({"display":"block"})
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
								  $(".brand-title").css({"display":"block"})
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
