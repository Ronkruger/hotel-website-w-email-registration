<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';
include('includes/dbconnection.php');
session_start();
error_reporting(0);

if (strlen($_SESSION['hbmsuid']) == 0) {
    header('location:logout.php');
    exit();
} else {
    if (isset($_POST['cancel'])) {
        $selectedBookings = isset($_POST['booking']) ? $_POST['booking'] : array();
        if (!empty($selectedBookings)) {
            $deletedBookings = array();
            foreach ($selectedBookings as $bookingId) {
                $sql = "UPDATE tblbooking SET Status = 'Cancelled' WHERE BookingNumber = :bookingNumber AND UserID = :userId";
                $query = $dbh->prepare($sql);
                $query->bindParam(':bookingNumber', $bookingId, PDO::PARAM_STR);
                $query->bindParam(':userId', $_SESSION['hbmsuid'], PDO::PARAM_INT);
                $query->execute();
                if ($query->rowCount() > 0) {
                    $deletedBookings[] = $bookingId;
                }
            }
            if (!empty($deletedBookings)) {
                foreach ($deletedBookings as $bookingId) {
                    $userId = $_SESSION['hbmsuid'];
                    $userSql = "SELECT Email FROM tbluser WHERE ID = :userId";
                    $userQuery = $dbh->prepare($userSql);
                    $userQuery->bindParam(':userId', $userId, PDO::PARAM_INT);
                    $userQuery->execute();
                    $userResult = $userQuery->fetch(PDO::FETCH_ASSOC);
                    $userEmail = $userResult['Email'];

                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'luxestay4@gmail.com';
    $mail->Password   = 'bzgs zncv snjv srwb'; 
                        $mail->SMTPSecure = 'ssl';
                        $mail->Port       = 465;
                    
                        $mail->setFrom('luxestay4@gmail.com');
                        $mail->addAddress($userEmail);
                        $mail->isHTML(true);
                        $mail->Subject = 'Booking Cancellation Confirmation';
                        $mail->Body = '<html><body><p>Your booking has been successfully canceled.</p></body></html>';
                        $mail->send();
                    } catch (Exception $e) {
                        echo '<script>alert("Error sending email: ' . $mail->ErrorInfo . '");</script>';
                    }
                }
                echo '<script>alert("Selected bookings canceled successfully. Email notifications sent.");window.location.href ="my-booking.php";</script>';
            } else {
                echo '<script>alert("Error canceling bookings. Please try again later.");window.location.href ="my-booking.php";</script>';
            }
        } else {
            echo '<script>alert("Please select at least one booking to cancel.");window.location.href ="my-booking.php";</script>';
        }
    }
}

// Calendar events
$events = [];
$uid = $_SESSION['hbmsuid'];
$sql = "SELECT tblbooking.BookingNumber, tblbooking.Status, tblbooking.CheckInDate, tblbooking.CheckOutDate FROM tblbooking WHERE UserID=:uid";
$query = $dbh->prepare($sql);
$query->bindParam(':uid', $uid, PDO::PARAM_STR);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

foreach ($results as $row) {
    $eventColor = ($row->Status == 'Approved') ? '#4caf50' : (($row->Status == 'Pending') ? '#ff9800' : '#f44336');
    $events[] = [
        'title' => $row->BookingNumber . ' (' . $row->Status . ')',
        'start' => $row->CheckInDate,
        'end' => date('Y-m-d', strtotime($row->CheckOutDate . ' +1 day')),
        'color' => $eventColor
    ];
}
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
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Booking - Hotel Booking Management System</title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: <?php echo $background_color; ?>;
            color: <?php echo $font_color; ?>;
        }
/* 
        .header-top {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px 0;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        } */

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

        .calendar-container {
            margin: 20px auto;
            max-width: 900px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        }

        .table {
            margin-top: 20px;
            font-size: 0.9em;
        }

        .btn-cancel {
            background-color: #f44336;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-cancel:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>

<?php include_once('includes/header.php'); ?>

<div class="container">
    <div class="calendar-container">
        <h2 class="text-center">My Reservation Calendar</h2>
        <div id="calendar"></div>
    </div>

    <div class="table-responsive">
        <h3 class="text-center">My Reservation Details</h3>
        <form method="post">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th><input type="checkbox" id="select_all"> Select All</th>
                    <th>#</th>
                    <th>Booking Number</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $cnt = 1;
                foreach ($results as $row) {
                    echo '<tr>';
                    echo '<td><input type="checkbox" name="booking[]" value="' . htmlentities($row->BookingNumber) . '"></td>';
                    echo '<td>' . htmlentities($cnt) . '</td>';
                    echo '<td>' . htmlentities($row->BookingNumber) . '</td>';
                    echo '<td>' . htmlentities($row->Status) . '</td>';
                    echo '<td><a href="view-application-detail.php?viewid=' . htmlentities($row->BookingNumber) . '" class="btn btn-primary">View</a></td>';
                    echo '</tr>';
                    $cnt++;
                }
                ?>
                </tbody>
            </table>
            <div class="text-center">
                <button type="submit" name="cancel" class="btn-cancel">Cancel Selected Bookings</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: <?php echo json_encode($events); ?>,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            }
        });
        calendar.render();

        // Select all checkboxes functionality
        document.getElementById('select_all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="booking[]"]');
            checkboxes.forEach((checkbox) => {
                checkbox.checked = this.checked;
            });
        });
    });
</script>
</body>
</html>