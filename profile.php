<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/vendor/autoload.php';

// Function to generate OTP
function generateOTP() {
    return sprintf('%06d', rand(0, 999999));
}

// Function to send verification email
function sendmail_verify($name, $email, $otp, $purpose) {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'romslantano@gmail.com';
    $mail->Password   = 'yupg ivih bvzu fyxx';
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    
    $mail->setFrom('romslantano@gmail.com');
    $mail->addAddress($email);
    
    $mail->isHTML(true);
    
    if ($purpose == 'email_change') {
        $mail->Subject = 'Email Change Verification';
        $template = "<h2>Email Change Verification</h2>
                     <p>Your OTP for changing email is: $otp</p>";
    } else {
        $mail->Subject = 'Account Activation';
        $template = "<h2>You have registered with hotel reservation</h2>
                     <h2>Your OTP for verification is: $otp</h5>";
    }
    
    $mail->Body = $template;
    
    try {
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Check if user is logged in
if (strlen($_SESSION['hbmsuid']) == 0) {
    header('location:logout.php');
    exit;
}

$uid = $_SESSION['hbmsuid'];

// Handle profile image and basic info update
if (isset($_POST['submit'])) {
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

        $target_dir = "uploads/pfp/";
        $target_file = $target_dir . basename($file_name);

        if (move_uploaded_file($file_tmp, $target_file)) {
            $sql = "UPDATE tbluser SET FullName=:name, MobileNumber=:mobilenumber, profile_image=:profile_image WHERE ID=:uid";
        } else {
            echo "Failed to upload file.";
            exit;
        }
    } else {
        $sql = "UPDATE tbluser SET FullName=:name, MobileNumber=:mobilenumber WHERE ID=:uid";
    }

    $query = $dbh->prepare($sql);
    $query->bindParam(':name', $AName, PDO::PARAM_STR);
    $query->bindParam(':mobilenumber', $mobno, PDO::PARAM_STR);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);

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

// Handle email change request
if (isset($_POST['change_email'])) {
    $current_password = $_POST['current_password'];
    $new_email = $_POST['new_email'];

    // Verify current password
    $sql_verify = "SELECT * FROM tbluser WHERE ID=:uid";
    $query_verify = $dbh->prepare($sql_verify);
    $query_verify->bindParam(':uid', $uid, PDO::PARAM_STR);
    $query_verify->execute();
    $user = $query_verify->fetch(PDO::FETCH_ASSOC);

    if (password_verify($current_password, $user['Password'])) {
        // Generate OTP
        $otp = generateOTP();

        // Store OTP and new email in session for verification
        $_SESSION['email_change_otp'] = $otp;
        $_SESSION['new_email'] = $new_email;

        // Send OTP to new email
        if (sendmail_verify($user['FullName'], $new_email, $otp, 'email_change')) {
            echo '<script>alert("OTP sent to new email. Please verify.")</script>';
            echo '<script>window.location.href = "email_verify.php";</script>';
            exit;
        } else {
            echo '<script>alert("Failed to send OTP")</script>';
        }
    } else {
        echo '<script>alert("Incorrect current password")</script>';
    }
}

// Fetch user's current profile image
$sql_fetch_image = "SELECT profile_image FROM tbluser WHERE ID=:uid";
$query_fetch_image = $dbh->prepare($sql_fetch_image);
$query_fetch_image->bindParam(':uid', $uid, PDO::PARAM_STR);
$query_fetch_image->execute();
$row_fetch_image = $query_fetch_image->fetch(PDO::FETCH_ASSOC);
$profile_image = $row_fetch_image['profile_image'];

// Fetch user details
$sql = "SELECT * FROM tbluser WHERE ID=:uid";
$query = $dbh->prepare($sql);
$query->bindParam(':uid', $uid, PDO::PARAM_STR);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Hotel Booking Management System</title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <style>
        /* Previous CSS styles remain the same */
        .email-change-section {
            background-color: #f4f4f4;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .show-password {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-top: 10px;
        }

        .show-password input {
            margin-right: 5px;
        }

    </style>
</head>
<body>
    <div class="container">
        <?php include_once('includes/header.php'); ?>
    </div>

    <div class="content">
        <div class="container">
            <h2>View Your Profile</h2>
            <form method="post" enctype="multipart/form-data">
                <?php
                if ($query->rowCount() > 0) {
                    foreach ($results as $row) {
                ?>
                    <div class="profile-img">
                        <h3>Current Profile Picture</h3>
                        <?php if (!empty($profile_image)) { ?>
                            <img src="uploads/pfp/<?php echo htmlspecialchars($profile_image); ?>" width="150" height="150" />
                        <?php } else { ?>
                            <img src="uploads/pfp/default_pfp.png" width="150" height="150" />
                        <?php } ?>
                    </div>
                    <div class="form-group">
                        <label for="fname">Full Name</label>
                        <input type="text" class="form-control" id="fname" name="fname" value="<?php echo htmlspecialchars($row->FullName); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="mobno">Mobile Number</label>
                        <input type="text" class="form-control" id="mobno" name="mobno" value="<?php echo htmlspecialchars($row->MobileNumber); ?>" required maxlength="10" pattern="[0-9]+">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row->Email); ?>" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="regdate">Registration Date</label>
                        <input type="text" class="form-control" id="regdate" name="regdate" value="<?php echo htmlspecialchars($row->RegDate); ?>" readonly>
                    </div>
                <?php
                    }
                }
                ?>
                <div class="form-group">
                    <label for="profile_image">Profile Image</label>
                    <input type="file" class="form-control" id="profile_image" name="profile_image">
                </div>
                <button type="submit" name="submit" class="btn-update">Update Profile</button>
            </form>

            <!-- Email Change Section -->
            <div class="email-change-section">
                <h3>Change Email</h3>
                <form method="post">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="show-password">
            <input type="checkbox" id="show_pass">
            <label for="show_pass" style="color: #333;">Show Password</label>
        </div>
                    <div class="form-group">
                        <label for="new_email">New Email Address</label>
                        <input type="email" class="form-control" id="new_email" name="new_email" required>
                    </div>
    
                    <button type="submit" name="change_email" class="btn-update">Change Email</button>
                </form>
            </div>
        </div>
    </div>
    <script>
    const showPasswordToggle = document.getElementById('show_pass');
    const passwordInput = document.getElementById('current_password');

    showPasswordToggle.addEventListener('change', function() {
        // Toggle the type of the password field between 'password' and 'text'
        passwordInput.type = this.checked ? 'text' : 'password';
    });
</script>
    <?php include_once('includes/footer.php'); ?>
</body>
</html>