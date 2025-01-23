<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['hbmsaid']) == 0) {
    header('location:logout.php');
} else {
?>
<div class="header-section">
    <!-- Top Background -->
    <div class="top_bg">
        <div class="header_top">
            <div class="top_right">
                <?php
                $sql = "SELECT * FROM tbladmin WHERE ID=:aid";
                $query = $dbh->prepare($sql);
                $query->bindParam(':aid', $_SESSION['hbmsaid'], PDO::PARAM_STR);
                $query->execute();
                $row = $query->fetch(PDO::FETCH_OBJ);
                ?>
                <ul>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="change-password.php">Change Password</a></li>
                    <li><a href="logout.php">Logout</a></li>
                    <li class="profile-image dropdown">
                        <img src="../uploads/pfp/<?php echo !empty($row->profile_image) ? $row->profile_image : 'default_pfp.png'; ?>" alt="Profile Image" />
                        <div class="dropdown-content">
                            <a href="profile.php">View Profile</a>
                            <a href="change-password.php">Change Password</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="clearfix"></div>
    <!-- /Top Background -->
</div>

<style>
    .header-section {
        background-color: #343a40; /* Dark background color */
        padding: 10px 0;
        color: white;
        font-family: 'Arial', sans-serif; /* Changed font */
    }

    .top_bg {
        display: flex;
        justify-content: center;
        padding: 10px;
    }

    .header_top {
        width: 100%;
        max-width: 1200px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
    }

    .top_right {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .top_right ul {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .top_right ul li {
        margin-right: 15px;
        position: relative;
    }

    .top_right ul li a {
        text-decoration: none;
        color: white;
        padding: 8px 10px; /* Add padding for better clickable area */
        border-radius: 5px; /* Rounded corners */
        transition: background-color 0.3s, color 0.3s;
        font-weight: 500; /* Slightly bolder font */
    }

    .top_right ul li a:hover {
        background-color: #007bff; /* Change background on hover */
        color: white; /* Ensure text remains white */
    }

    .profile-image {
        position: relative;
    }

    .profile-image img {
        width: 40px;
        height: 40px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        margin-left: 10px;
        cursor: pointer;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #fff;
        min-width: 160px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
        border-radius: 4px;
        margin-top: 10px; /* Add space between image and dropdown */
    }

    .dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none; /* No underline */
    display: block;
    transition: background-color 0.3s;
    font-family: 'Arial', sans-serif; /* Explicitly set the font-family */
}

    .dropdown-content a:hover {
        background-color: #f1f1f1; /* Change background on hover */
    }

    .profile-image:hover .dropdown-content {
        display: block; /* Show dropdown on hover */
    }

    .clearfix {
        clear: both;
    }
</style>

<?php } ?>  