<?php
session_start();
error_reporting(0);

include('includes/dbconnection.php');
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Hotel Booking Management System | Hotel :: Gallery</title>
    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <!-- Lightbox CSS -->
    <link rel="stylesheet" href="css/lightbox.css">
	<style>
        /* Manual Carousel Styles */
        .carousel {
            position: relative;
            width: 800px; /* Set the width of the carousel */
            height: 500px; /* Set the height of the carousel */
            overflow: hidden;
			margin: auto;
        }
        .carousel-inner {
            position: relative;
            width: 100%;
            height: 100%;
        }
        .carousel-item {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .carousel-item.active {
            display: block;
        }
        .carousel-item img {
            width: 800px; /* Set the width of the image */
            height: 500px; /* Set the height of the image */
            object-fit: cover; /* Maintain aspect ratio */
        }
        /* Carousel Navigation Styles */
        .carousel-indicators {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .carousel-indicators li {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: #aaa;
            border-radius: 50%;
            margin-right: 5px;
            cursor: pointer;
        }
        .carousel-indicators li.active {
            background-color: #333;
        }
        .carousel-control-prev,
        .carousel-control-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            display: inline-block;
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            text-align: center;
            line-height: 30px;
            color: #fff;
            text-decoration: none;
        }
        .carousel-control-prev {
            left: 20px;
        }
        .carousel-control-next {
            right: 20px;
        }
    </style>
</head>
<body>
<!--header-->
<div class="header head-top">
    <div class="container">
        <?php include_once('includes/header.php');?>
    </div>
</div>
<!--header-->

<div class="content">
    <!-- gallery -->
    <div class="gallery-top">
        <!-- container -->
        <div class="container">
            <div class="gallery-info">
                <!-- <h2>Gallery</h2> -->
            </div>

            <!-- Image Carousel -->
            <div id="carouselExampleIndicators" class="carousel">
                <div class="carousel-inner">
                    <?php
                    // Database connection
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "hbmsdb";

                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Retrieve images from the database
                    $sql = "SELECT * FROM slideshowtbl";
                    $result = $conn->query($sql);

                    $active = true;
                    while($row = $result->fetch_assoc()) {
                        $active_class = $active ? 'active' : '';
                        echo '<div class="carousel-item ' . $active_class . '">
                                <img class="d-block w-100" src="./uploads/' . $row['image_path'] . '" alt="' . $row['caption'] . '">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5>' . $row['caption'] . '</h5>
                                </div>
                            </div>';
                        $active = false;
                    }
                    ?>
                </div>
                <ol class="carousel-indicators">
                    <?php
                    $result->data_seek(0); // Reset the result set to start from the beginning
                    $indicator_count = $result->num_rows;
                    for ($i = 0; $i < $indicator_count; $i++) {
                        echo '<li data-slide-to="' . $i . '" class="' . ($i == 0 ? 'active' : '') . '"></li>';
                    }
                    ?>
                </ol>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev" onclick="return false;">
                    &#8249;
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next" onclick="return false;">
                    &#8250;
                </a>
            </div>
            <!-- End Image Carousel -->

            <div class="clearfix"> </div>
        </div>
    </div>
    <!-- //gallery -->


<!-- Lightbox JS -->
<script src="js/lightbox-plus-jquery.min.js"></script>

<!-- Initialize Manual Carousel -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var carousel = document.getElementById('carouselExampleIndicators');
        var items = carousel.querySelectorAll('.carousel-item');
        var indicators = carousel.querySelectorAll('.carousel-indicators li');
        var currentIndex = 0;

        function showItem(index) {
            items.forEach(function(item) {
                item.classList.remove('active');
            });
            indicators.forEach(function(indicator) {
                indicator.classList.remove('active');
            });
            items[index].classList.add('active');
            indicators[index].classList.add('active');
            currentIndex = index;
        }

        indicators.forEach(function(indicator, index) {
            indicator.addEventListener('click', function() {
                showItem(index);
            });
        });

        carousel.querySelector('.carousel-control-prev').addEventListener('click', function() {
            var prevIndex = currentIndex - 1;
            if (prevIndex < 0) {
                prevIndex = items.length - 1;
            }
            showItem(prevIndex);
        });

        carousel.querySelector('.carousel-control-next').addEventListener('click', function() {
            var nextIndex = currentIndex + 1;
            if (nextIndex >= items.length) {
                nextIndex = 0;
            }
            showItem(nextIndex);
        });
    });
</script>

</body>
</html>