<?php
// session_start();
// Database connection
$conn = new mysqli("localhost", "root", "", "hbmsdb");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch settings
$sql = "SELECT * FROM tblsettings WHERE id=1";
$result = $conn->query($sql);

// Default values for logo and background
$default_logo = "uploads/default_logo.png"; // Ensure to have a default logo image in the uploads directory
$default_bg_image = "uploads/default_bg.jpg"; 
$default_bg_color = "#ffffff";
$default_logo_text = "Phantom Hive";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $logo = $row['logo'] ? "uploads/" . $row['logo'] : $default_logo;
    $logo_text = $row['logo'] ? "" : $default_logo_text; 
    $background_image = $row['background_image'] ? "uploads/" . $row['background_image'] : $default_bg_image;
    $background_color = $row['background_color'] ? $row['background_color'] : $default_bg_color;
} else {
    $logo = $default_logo;
    $logo_text = $default_logo_text;
    $background_image = $default_bg_image;
    $background_color = $default_bg_color;
}

// Fetch font color
$get_font_color_sql = "SELECT font_color FROM tblsettings WHERE id = 1";
$get_font_color_result = $conn->query($get_font_color_sql);

if ($get_font_color_result->num_rows > 0) {
    $row = $get_font_color_result->fetch_assoc();
    $font_color = $row['font_color'];
} else {
    $font_color = '#000000'; // Use black if no color found
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking Management System</title>
    
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: <?php echo $background_color; ?>;
            color: <?php echo $font_color; ?>;
        }

        .header-top {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px 0;
            position: relative;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        #logo {
            max-height: 60px; 
            width: auto; 
            position: absolute;
            left: -60px;
            top: -30px;
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

        .nav-links a {
            text-decoration: none;
            color: <?php echo $font_color; ?>;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #007bff; /* Change color on hover */
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
    </style>
</head>
<body>
  
    <div class="header-top">
        <nav class="navbar">
            <div class="navbar-brand">
                <?php if (file_exists($logo)): ?>
                    <a href="index.php">
                        <img id="logo" src="<?php echo $logo; ?>" alt="Logo">
                    </a>
                <?php else: ?>
                    <a href="index.php">
                        <span style="font-size: 24px; font-weight: bold;"><?php echo $logo_text; ?></span>
                    </a>
                <?php endif; ?>
            </div>

            <ul class="nav-links">
                <li><a href="about.php">About</a></li>
                <li class="dropdown">
                    <a href="#">Rooms</a>
                    <ul class="dropdown-menu">
                        <?php
                        $ret = "SELECT * from tblcategory";
                        $query1 = $dbh->prepare($ret);
                        $query1->execute();
                        $resultss = $query1->fetchAll(PDO::FETCH_OBJ);
                        foreach ($resultss as $rows) { ?>
                            <li><a href="category-details.php?catid=<?php echo htmlentities($rows->ID)?>"><?php echo htmlentities($rows->CategoryName) ?></a></li>
                        <?php } ?> 
                    </ul>
                </li>
                <li><a href="contact.php">Contact</a></li>
                <?php if (isset($_SESSION['hbmsuid'])): ?>
                    <li class="dropdown">
                        <a href="#">My Account</a>
                        <ul class="dropdown-menu">
                            <li><a href="profile.php">Profile</a></li>
                            <li><a href="my-booking.php">My Booking</a></li>
                            <li><a href="change-password.php">Change Password</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="signup.php">Sign Up</a></li>
                    <li><a href="signin.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</body>
</html> 