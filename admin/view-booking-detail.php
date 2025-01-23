<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/PHPMailer/src/Exception.php';
require '../PHPMailer/PHPMailer/src/PHPMailer.php';
require '../PHPMailer/PHPMailer/src/SMTP.php';

// Check if user is logged in
if (strlen($_SESSION['hbmsaid']==0)) {
    header('location:logout.php');
    exit();
}

// Fetch booking details first
$bookid = $_GET['bookingid'];
$sql = "SELECT 
        b.BookingNumber, b.IDType, b.Gender, b.Address, 
        b.CheckinDate, b.CheckoutDate, b.BookingDate,
        b.Remark, b.Status, b.UpdationDate, b.downPay,
        b.payment_mode, b.totalCost, b.currentBalance,
        b.paymentOptions, b.FoodBeveragesCost, b.PoolUsageCost,
        u.FullName, u.MobileNumber, u.Email,
        c.CategoryName, c.Description, c.Price,
        r.RoomName, r.MaxAdult, r.MaxChild, r.RoomDesc,
        r.NoofBed, r.Image, r.RoomFacility
        FROM tblbooking b
        LEFT JOIN tbluser u ON b.UserID = u.ID
        LEFT JOIN tblroom r ON b.RoomId = r.ID
        LEFT JOIN tblcategory c ON r.RoomType = c.ID
        WHERE b.BookingNumber = :bookid";

$query = $dbh->prepare($sql);
$query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
$query->execute();
$bookingData = $query->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if(isset($_POST['submit'])) {
    $status = $_POST['status'];
    $remark = $_POST['remark'];

    try {
        $dbh->beginTransaction();

        // Update booking status
        $sql = "UPDATE tblbooking 
                SET Status = :status,
                    Remark = :remark,
                    currentBalance = CASE WHEN :status = 'Approved' THEN 0 ELSE currentBalance END,
                    UpdationDate = CURRENT_TIMESTAMP
                WHERE BookingNumber = :bookid";
        
        $query = $dbh->prepare($sql);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':remark', $remark, PDO::PARAM_STR);
        $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
        $query->execute();

        // Email notification setup
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'luxestay4@gmail.com';
        $mail->Password = 'bzgs zncv snjv srwb';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('luxestay4@gmail.com', 'Hotel Name');
        $mail->addAddress($bookingData['Email']);
        $mail->isHTML(true);
        
        $subject = $status == 'Approved' ? 'Booking Approved - Payment Complete' : 'Booking Status Update';
        $mail->Subject = $subject;
                // Create email template
                $template = '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .booking-details { margin: 20px 0; }
                        .highlight { color: #4CAF50; font-weight: bold; }
                        .total { background: #f5f5f5; padding: 10px; }
                    </style>
                </head>
                <body>
                    <h2>Booking Status Update</h2>
                    <p>Dear ' . htmlspecialchars($bookingData['FullName']) . ',</p>
                    <div class="booking-details">
                        <p>Your booking details have been updated:</p>
                        <p>Booking ID: <strong>' . htmlspecialchars($bookingData['BookingNumber']) . '</strong></p>
                        <p>Room: <strong>' . htmlspecialchars($bookingData['RoomName']) . '</strong></p>
                        <p>Check-in Date: ' . htmlspecialchars($bookingData['CheckinDate']) . '</p>
                        <p>Check-out Date: ' . htmlspecialchars($bookingData['CheckoutDate']) . '</p>
                        <div class="total">
                            <p>Room Cost: ₱' . number_format($bookingData['totalCost'], 2) . '</p>
                            <p>Food & Beverages: ₱' . number_format($bookingData['FoodBeveragesCost'], 2) . '</p>
                            <p>Pool Usage: ₱' . number_format($bookingData['PoolUsageCost'], 2) . '</p>
                            <p>Amount Paid: ₱' . number_format($bookingData['downPay'], 2) . '</p>
                            <p>Remaining Balance: ₱' . number_format($status == 'Approved' ? 0 : $bookingData['currentBalance'], 2) . '</p>
                        </div>
                        <p>Payment Mode: ' . htmlspecialchars($bookingData['payment_mode']) . '</p>
                        <p>Payment Option: ' . htmlspecialchars($bookingData['paymentOptions']) . '</p>
                        <p>Status: <span class="highlight">' . htmlspecialchars($status) . '</span></p>
                        <p>Remarks: ' . htmlspecialchars($remark) . '</p>
                    </div>';
        
                if($status == 'Approved') {
                    $template .= '
                    <div>
                        <p>Thank you for completing your payment and choosing our hotel!</p>
                        <p>Important Reminders:</p>
                        <ul>
                            <li>Please present a valid ID upon check-in</li>
                            <li>Standard check-in time is 2:00 PM</li>
                            <li>Standard check-out time is 12:00 PM</li>
                            <li>For early check-in or late check-out requests, please contact us in advance</li>
                        </ul>
                    </div>';
                }
        
                $template .= '
                    <p>If you have any questions, please don\'t hesitate to contact us.</p>
                    <p>Best regards,<br>Hotel Management</p>
                </body>
                </html>';
        
                $mail->Body = $template;
                $mail->send();
                
                $dbh->commit();
                echo '<script>alert("Booking status updated successfully. Payment verified and confirmation sent.");</script>';
                
            } catch (Exception $e) {
                $dbh->rollBack();
                echo '<script>alert("Error: ' . $e->getMessage() . '");</script>';
            }
            
            echo "<script>window.location.href ='new-booking.php'</script>";
        }
        ?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Hotel Booking Management System | View Booking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
    <link href="css/style.css" rel='stylesheet' type='text/css' />
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
    <style>
        .booking-details { margin: 20px 0; }
        .status-badge {
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        .status-pending { background: #ffc107; color: #000; }
        .status-approved { background: #28a745; color: #fff; }
        .status-cancelled { background: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="left-content">
            <div class="inner-content">
                <?php include_once('includes/header.php'); ?>
                <div class="content">
                    <div class="women_main">
                        <div class="grids">
                            <div class="panel panel-widget forms-panel">
                                <div class="forms">
                                    <div class="form-grids widget-shadow" data-example-id="basic-forms">
                                        <div class="form-title">
                                            <h4>Booking #<?php echo htmlspecialchars($bookingData['BookingNumber']); ?></h4>
                                        </div>
                                        <div class="form-body">
                                            <div class="booking-details">
                                                <!-- Customer Information -->
                                                <h5>Customer Information</h5>
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>Name</th>
                                                        <td><?php echo htmlspecialchars($bookingData['FullName']); ?></td>
                                                        <th>Mobile</th>
                                                        <td><?php echo htmlspecialchars($bookingData['MobileNumber']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Email</th>
                                                        <td><?php echo htmlspecialchars($bookingData['Email']); ?></td>
                                                        <th>ID Type</th>
                                                        <td><?php echo htmlspecialchars($bookingData['IDType']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Address</th>
                                                        <td colspan="3"><?php echo htmlspecialchars($bookingData['Address']); ?></td>
                                                    </tr>
                                                </table>

                                                <!-- Booking Details -->
                                                <h5>Booking Details</h5>
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>Check-in</th>
                                                        <td><?php echo htmlspecialchars($bookingData['CheckinDate']); ?></td>
                                                        <th>Check-out</th>
                                                        <td><?php echo htmlspecialchars($bookingData['CheckoutDate']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Room Type</th>
                                                        <td><?php echo htmlspecialchars($bookingData['CategoryName']); ?></td>
                                                        <th>Room Name</th>
                                                        <td><?php echo htmlspecialchars($bookingData['RoomName']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Room Price</th>
                                                        <td>₱<?php echo number_format($bookingData['Price'], 2); ?></td>
                                                        <th>Room Capacity</th>
                                                        <td>Adults: <?php echo $bookingData['MaxAdult']; ?>, Children: <?php echo $bookingData['MaxChild']; ?></td>
                                                    </tr>
                                                </table>
                                                <!-- Payment Information -->
                                                <h5>Payment Details</h5>
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>Total Cost</th>
                                                        <td>₱<?php echo number_format($bookingData['totalCost'], 2); ?></td>
                                                        <th>Amount Paid</th>
                                                        <td>₱<?php echo number_format($bookingData['downPay'], 2); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Balance</th>
                                                        <td>₱<?php echo number_format($bookingData['currentBalance'], 2); ?></td>
                                                        <th>Payment Mode</th>
                                                        <td><?php echo htmlspecialchars($bookingData['payment_mode']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status</th>
                                                        <td>
                                                            <?php
                                                            $status = $bookingData['Status'];
                                                            $statusClass = '';
                                                            $statusText = '';
                                                            
                                                            if($status == "") {
                                                                $statusClass = 'status-pending';
                                                                $statusText = 'Pending';
                                                            } elseif($status == "Approved") {
                                                                $statusClass = 'status-approved';
                                                                $statusText = 'Approved';
                                                            } elseif($status == "Cancelled") {
                                                                $statusClass = 'status-cancelled';
                                                                $statusText = 'Cancelled';
                                                            }
                                                            ?>
                                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                                <?php echo $statusText; ?>
                                                            </span>
                                                        </td>
                                                        <th>Last Updated</th>
                                                        <td><?php echo $bookingData['UpdationDate'] ? date('Y-m-d H:i:s', strtotime($bookingData['UpdationDate'])) : 'Not Updated'; ?></td>
                                                    </tr>
                                                </table>
                                                <!-- Additional Services -->
                                                <h5>Additional Services</h5>
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>Food & Beverages</th>
                                                        <td>₱<?php echo number_format($bookingData['FoodBeveragesCost'], 2); ?></td>
                                                        <th>Pool Usage</th>
                                                        <td>₱<?php echo number_format($bookingData['PoolUsageCost'], 2); ?></td>
                                                    </tr>
                                                </table>

                                                <!-- Room Details -->
                                                <h5>Room Details</h5>
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>Room Description</th>
                                                        <td><?php echo htmlspecialchars($bookingData['RoomDesc']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Room Facilities</th>
                                                        <td><?php echo htmlspecialchars($bookingData['RoomFacility']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Number of Beds</th>
                                                        <td><?php echo htmlspecialchars($bookingData['NoofBed']); ?></td>
                                                    </tr>
                                                </table>
                                                
                                                <div class="text-right mb-3">
                                                    <a href="extend-stay.php?bookingid=<?php echo htmlspecialchars($bookingData['BookingNumber']); ?>" class="btn btn-info">Extend Stay</a>
                                                </div>
                                                <!-- Admin Remarks -->
                                                <h5>Administrative Notes</h5>
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>Remarks</th>
                                                        <td>
                                                            <?php
                                                            if($bookingData['Status'] == "" || $bookingData['Status'] == "Pending") {
                                                                echo "Not Updated Yet";
                                                            } else {
                                                                echo htmlspecialchars($bookingData['Remark']);
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <!-- Action Button -->
                                                <?php if($bookingData['Status'] == "" || $bookingData['Status'] == "Pending") { ?>
                                                <div class="text-center mt-4 mb-4">
                                                    <button class="btn btn-primary" data-toggle="modal" data-target="#takeActionModal">
                                                        Take Action
                                                    </button>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <?php include_once('includes/sidebar.php');?>
        <div class="clearfix"></div>
    </div>

    <!-- Take Action Modal -->
    <div class="modal fade" id="takeActionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Take Action on Booking</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" onsubmit="return validateForm()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="status">Update Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="">Select Status</option>
                                <option value="Approved">Approve Booking</option>
                                <option value="Cancelled">Cancel Booking</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="remark">Administrative Remarks</label>
                            <textarea name="remark" id="remark" class="form-control" rows="4" required
                                    placeholder="Enter detailed remarks about this decision..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="submit" class="btn btn-primary">Update Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
    <script>
        function validateForm() {
            var status = document.getElementById('status').value;
            var remark = document.getElementById('remark').value;
            
            if(status == "") {
                alert("Please select a status");
                return false;
            }
            
         
            
            if(remark.trim() == "") {
                alert("Please enter remarks");
                return false;
            }
            
            if(remark.length < 5) {
                alert("Please provide more detailed remarks");
                return false;
            }
            
            var confirmMessage = status == "Approved" ?
                "Confirm approval - payment received in full" :
                "Are you sure you want to " + status.toLowerCase() + " this booking?";
            
            return confirm(confirmMessage);
        }
    </script>
</body>
</html>
