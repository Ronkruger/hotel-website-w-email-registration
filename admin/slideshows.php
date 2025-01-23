<?php
   // admin/slideshows.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hbmsdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Maximum number of images allowed in the slideshow
$max_images = 6;


// Add or update carousel image
if(isset($_POST['submit'])) {
    $images = $_FILES['image'];

    // Check if there are existing images
    $sql_check_existing = "SELECT * FROM slideshowtbl";
    $result_check_existing = $conn->query($sql_check_existing);
    $existing_images = $result_check_existing->fetch_all(MYSQLI_ASSOC);

    // Process each uploaded image
    foreach ($images["tmp_name"] as $key => $tmp_name) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($images["name"][$key]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($tmp_name);
        if($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($images["size"][$key] > 10000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" && $imageFileType != "jfif" ) {
            echo "Sorry, only JPG, JPEG, PNG, jfif & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($tmp_name, $target_file)) {
                // Check if there are existing images to replace
                if (!empty($existing_images[$key])) {
                    // Update existing image
                    $image_id = $existing_images[$key]['id'];
                    $sql = "UPDATE slideshowtbl SET image_path='$target_file' WHERE id=$image_id";
                } else {
                    // Insert new image
                    $sql = "INSERT INTO slideshowtbl (image_path) VALUES ('$target_file')";
                }

                if ($conn->query($sql) === TRUE) {
                    // Store the scroll position in session
                    $_SESSION['scroll_position'] = $_POST['scroll_position'];

                    // Redirect to the same page to prevent form resubmission
                    header("Location: {$_SERVER['REQUEST_URI']}");
                    exit();
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
}
// Check if scroll position is stored in session
$scroll_position = isset($_SESSION['scroll_position']) ? $_SESSION['scroll_position'] : 0;


?>



<!DOCTYPE HTML>
<html>
<head>
<title>Hotel Booking Management System | Add Category</title>

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
  table {
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
  }
  .submit-button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .submit-button:hover {
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
                 
                                <div class="form-body">
<h2>Add Slideshow Images</h2>
<form method="post" enctype="multipart/form-data">
    <table border="1">
        <tr>
            <td>Image</td>
            <td>Current Image</td>
            <td>Caption</td>
        </tr>
        <?php 
        // Fetch existing slideshow images from the database
        $sql_fetch_images = "SELECT * FROM slideshowtbl";
        $result_fetch_images = $conn->query($sql_fetch_images);
        $existing_images = $result_fetch_images->fetch_all(MYSQLI_ASSOC);

        // Loop through each image slot
        for ($i = 0; $i < $max_images; $i++) {
            echo "<tr>";
            echo "<td><input type='file' name='image[]' id='image_$i'></td>";
            echo "<td>";
            // Display the currently uploaded image, if it exists
            if (!empty($existing_images[$i]['image_path'])) {
                echo "<img src='" . $existing_images[$i]['image_path'] . "' style='max-width: 100px; max-height: 100px;' />";
            } else {
                echo "No image uploaded";
            }
            echo "</td>";
            echo "<td><input type='text' name='caption[]' id='caption_$i'></td>";
            echo "</tr>";
        }
        ?>
        <tr>
            <td colspan="3"><input class="submit-button" type="submit" value="Submit" name="submit"></td>
        </tr>
    </table>
</form>

<?php
// Close connection
$conn->close();
?>


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
</body>
</html>
