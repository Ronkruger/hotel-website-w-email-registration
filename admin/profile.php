<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['hbmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_POST['submit'])) {
        $adminid = $_SESSION['hbmsaid'];
        $AName = $_POST['adminname'];
        $mobno = $_POST['mobilenumber'];
        $email = $_POST['email'];

        // Check if a file is uploaded
        if (!empty($_FILES['profile_image']['name'])) {
            $file_name = $_FILES['profile_image']['name'];
            $file_tmp = $_FILES['profile_image']['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_extensions = array("jpeg", "jpg", "png");

            if (!in_array($file_ext, $allowed_extensions)) {
                echo "Extension not allowed, please choose a JPEG or PNG file.";
                exit;
            }

            // Define the target directory to store the uploaded image
            $target_dir = "../uploads/pfp/";
            $target_file = $target_dir . basename($file_name);

            if (!move_uploaded_file($file_tmp, $target_file)) {
                // Display an error message if file upload fails
                echo "Failed to upload file.";
                exit;
            }
        }

        // Update the database query
        $sql = "UPDATE tbladmin SET AdminName=:adminname, MobileNumber=:mobilenumber, Email=:email";
        if (!empty($file_name)) {
            $sql .= ", profile_image=:profile_image";
        }
        $sql .= " WHERE ID=:aid";

        // Prepare and execute the query
        $query = $dbh->prepare($sql);
        $query->bindParam(':adminname', $AName, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':mobilenumber', $mobno, PDO::PARAM_STR);
        $query->bindParam(':aid', $adminid, PDO::PARAM_STR);
        if (!empty($file_name)) {
            $query->bindParam(':profile_image', $file_name, PDO::PARAM_STR);
        }
        $query->execute();

        // Display success message
        echo '<script>alert("Profile has been updated")</script>';
        echo "<script>window.location.href ='profile.php'</script>";
    }
}
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>Hotel Booking Management System | Profile</title>
    <script type="application/x-javascript">
        addEventListener("load", function() {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
    <!-- Custom CSS -->
    <link href="css/style.css" rel='stylesheet' type='text/css' />
    <!-- Graph CSS -->
    <link href="css/font-awesome.css" rel="stylesheet">
    <!-- jQuery -->
    <link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet' type='text/css' />
    <link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <!-- lined-icons -->
    <link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
    <script src="js/simpleCart.min.js"></script>
    <script src="js/amcharts.js"></script>
    <script src="js/serial.js"></script>
    <script src="js/light.js"></script>
    <!-- //lined-icons -->
    <script src="js/jquery-1.10.2.min.js"></script>
</head>

<body>
    <div class="page-container">
        <!--/content-inner-->
        <div class="left-content">
            <div class="inner-content">
                <!-- header-starts -->
                <?php include_once('includes/header.php'); ?>
                <!--content-->
                <div class="content">
                    <div class="women_main">
                        <!-- start content -->
                        <div class="grids">
                      
                            <div class="panel panel-widget forms-panel">
                                <div class="forms">
                                    <div class="form-grids widget-shadow" data-example-id="basic-forms">
                                        <!-- <div class="form-title">
                                            <h4>Admin Profile :</h4>
                                        </div> -->
                                        <div class="form-body">
                                            <?php
                                            $sql = "SELECT * FROM tbladmin WHERE ID=:aid";
                                            $query = $dbh->prepare($sql);
                                            $query->bindParam(':aid', $_SESSION['hbmsaid'], PDO::PARAM_STR);
                                            $query->execute();
                                            $row = $query->fetch(PDO::FETCH_OBJ);
                                            ?>
                                            <form method="post" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Admin Name</label>
                                                    <input type="text" class="form-control" name="adminname" value="<?php echo $row->AdminName; ?>" required='true'>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">User Name</label>
                                                    <input type="text" class="form-control" name="username" value="<?php echo $row->UserName; ?>" readonly="true">
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Email</label>
                                                    <input type="email" class="form-control" name="email" value="<?php echo $row->Email; ?>" required='true'>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Contact Number</label>
                                                    <input type="text" class="form-control" name="mobilenumber" value="<?php echo $row->MobileNumber; ?>" required='true' maxlength='10'>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Admin Registration Date</label>
                                                    <input type="text" class="form-control" id="email2" name="" value="<?php echo $row->AdminRegdate; ?>" readonly="true">
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Profile Image</label>
                                                    <input type="file" class="form-control" id="profile_image" name="profile_image">
                                                </div>
                                                <button type="submit" class="btn btn-default" name="submit">Submit</button>
                                            </form>
                                        </div>
                                     
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end content -->

                </div>
            </div>
            <!--content-->
        </div>
        </div>
        <!--//content-inner-->
        <!--/sidebar-menu-->
        <?php include_once('includes/sidebar.php'); ?>
        <div class="clearfix"></div>
    </div>

    <script src="js/jquery.nicescroll.js"></script>
    <script src="js/scripts.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <!-- /Bootstrap Core JavaScript -->
    <!-- real-time -->
    <script language="javascript" type="text/javascript" src="js/jquery.flot.js"></script>

    <script src="js/menu_jquery.js"></script>
</body>

</html>
