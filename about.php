<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking Management System | About Us</title>
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

	
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        #logo {
            max-height: 70px; 
            width: auto; 
            border-radius: 20px;
            transition: transform 0.3s ease;
        }

        #logo:hover {
            transform: scale(1.05); /* Slight hover effect */
        }

        .nav-links {
            list-style-type: none;
            display: flex;
            gap: 20px;
        }

        .nav-links li {
            position: relative;
        }

        .nav-links a {
            text-decoration: none;
            color: <?php echo $font_color; ?>;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #007bff; /* Change color on hover (can adjust as needed) */
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            z-index: 1;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,.15);
            border-radius: 4px;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-menu a {
            font-weight: normal;
            display: block;
            padding: 10px;
            color: <?php echo $font_color; ?>;
            transition: background-color 0.3s ease;
        }

        .dropdown-menu a:hover {
            background-color: #f1f1f1; /* Light grey on hover */
        }
        .content {
            padding: 20px;
        }

   

        .about-section h2 {
            color: #007bff;
            margin-bottom: 20px;
        }

        .about-section img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .about-section h5 {
            color: #007bff;
            margin: 10px 0;
        }

        .about-section p {
            color: #343a40;
            margin-bottom: 20px;
        }

        footer {
            margin-top: 30px;
            text-align: center;
            padding: 20px 0;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <!--header-->

        <div class="container">
            <?php include_once('includes/header.php');?>
        </div>

    <!--header-->

    <!-- About Section -->
    <div class="content">
        <div class="about-section">
            <div class="container">
                <?php
                $sql="SELECT * from tblpage where PageType='aboutus'";
                $query = $dbh->prepare($sql);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);

                if ($query->rowCount() > 0) {
                    foreach ($results as $row) {
                        ?>
                        <h2><?php echo htmlspecialchars($row->PageTitle); ?></h2>
                        <img src="images/vex.jpg" class="img-responsive" alt="About Us">
                        <h5><?php echo htmlspecialchars($row->PageTitle); ?></h5>
                        <p><?php echo htmlspecialchars($row->PageDescription); ?></p>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <!-- About Section -->

 
    <?php include_once('includes/footer.php'); ?>
</body>
</html>