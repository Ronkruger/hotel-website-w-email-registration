<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/dbconnection.php');
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\htdocs\PHPMailer\PHPMailer\src\Exception.php';
require 'C:\xampp\htdocs\PHPMailer\PHPMailer\src\PHPMailer.php';
require __DIR__ . '/vendor/autoload.php';

if (strlen($_SESSION['hbmsuid']) == 0) {
    header('location:logout.php');
    exit();
}

// Check if form data exists
if (!isset($_POST['room_id']) || !isset($_POST['checkindate']) || !isset($_POST['checkoutdate'])) {
    header('location:index.php');
    exit();
}

$dbh = new PDO("mysql:host=localhost;dbname=hbmsdb", "root", "");

// Fetch room details
$rid = intval($_POST['room_id']);
$sql = "SELECT r.RoomName, c.Price, c.CategoryName 
        FROM tblroom r 
        JOIN tblcategory c ON r.RoomType = c.ID 
        WHERE r.ID = :rid";
$query = $dbh->prepare($sql);
$query->bindParam(':rid', $rid, PDO::PARAM_STR);
$query->execute();
$roomDetails = $query->fetch(PDO::FETCH_ASSOC);

if (!$roomDetails) {
    header('location:index.php');
    exit();
}

// Calculate number of nights
$checkin = new DateTime($_POST['checkindate']);
$checkout = new DateTime($_POST['checkoutdate']);
$nights = $checkout->diff($checkin)->days;

// Calculate room total
$roomTotal = $roomDetails['Price'] * $nights;

// Calculate food and beverages total
$foodTotal = 0;
if (isset($_POST['food_beverages']) && is_array($_POST['food_beverages'])) {
    foreach ($_POST['food_beverages'] as $item => $quantity) {
        if ($quantity > 0) {
            $sql = "SELECT Price FROM foodbeveragestbl WHERE item_name = :item";
            $query = $dbh->prepare($sql);
            $query->bindParam(':item', $item, PDO::PARAM_STR);
            $query->execute();
            $price = $query->fetch(PDO::FETCH_ASSOC)['Price'];
            $foodTotal += $price * $quantity;
        }
    }
}

// Calculate pool usage fee if applicable
$poolFee = (isset($_POST['pool_usage']) && $_POST['pool_usage'] === 'yes') ? 500 : 0;

// Calculate grand total
$grandTotal = $roomTotal + $foodTotal + $poolFee;

// Calculate required payment based on payment option
$paymentOption = isset($_POST['paymentOptions']) ? $_POST['paymentOptions'] : 'full_payment';
$downPayment = isset($_POST['downPay']) ? floatval($_POST['downPay']) : 0;
$requiredPayment = ($paymentOption === 'full_payment') ? $grandTotal : $downPayment;

if (isset($_POST['confirm'])) {
    try {
        $booknum = mt_rand(100000000, 999999999);
        $uid = $_SESSION['hbmsuid'];
        $currentBalance = ($paymentOption === 'full_payment') ? 0 : ($grandTotal - $requiredPayment);

        // File upload handling
        if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] == 0) {
            $file_name = $_FILES['proof_of_payment']['name'];
            $file_tmp = $_FILES['proof_of_payment']['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png'];

            if (in_array($file_ext, $allowed_extensions)) {
                $upload_dir = 'uploads/proof_of_payment/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file_path = $upload_dir . uniqid() . '.' . $file_ext;
                if (move_uploaded_file($file_tmp, $file_path)) {
                    // Database insertion
                    $sql = "INSERT INTO tblbooking (
                        RoomId, BookingNumber, UserID, IDType, Address, CheckinDate, CheckoutDate,
                        Status, downPay, payment_mode, totalCost, currentBalance, paymentOptions,
                        FoodBeveragesCost, PoolUsageCost, proof_of_payment
                    ) VALUES (
                        :rid, :booknum, :uid, :idtype, :address, :checkindate, :checkoutdate,
                        'Pending', :downPay, :payment_mode, :totalCost, :currentBalance, :payment_option,
                        :foodCost, :poolCost, :proof_of_payment
                    )";
                    
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':rid', $rid, PDO::PARAM_INT);
                    $query->bindParam(':booknum', $booknum, PDO::PARAM_STR);
                    $query->bindParam(':uid', $uid, PDO::PARAM_INT);
                    $query->bindParam(':idtype', $_POST['idtype'], PDO::PARAM_STR);
                    $query->bindParam(':address', $_POST['address'], PDO::PARAM_STR);
                    $query->bindParam(':checkindate', $_POST['checkindate'], PDO::PARAM_STR);
                    $query->bindParam(':checkoutdate', $_POST['checkoutdate'], PDO::PARAM_STR);
                    $query->bindParam(':downPay', $requiredPayment, PDO::PARAM_STR);
                    $query->bindParam(':payment_mode', $_POST['payment_mode'], PDO::PARAM_STR);
                    $query->bindParam(':totalCost', $grandTotal, PDO::PARAM_STR);
                    $query->bindParam(':currentBalance', $currentBalance, PDO::PARAM_STR);
                    $query->bindParam(':payment_option', $paymentOption, PDO::PARAM_STR);
                    $query->bindParam(':foodCost', $foodTotal, PDO::PARAM_STR);
                    $query->bindParam(':poolCost', $poolFee, PDO::PARAM_STR);
                    $query->bindParam(':proof_of_payment', $file_path, PDO::PARAM_STR);

                    if ($query->execute()) {
                        // Email sending
                        $mail = new PHPMailer(true);
                        try {
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->Username = 'luxestay4@gmail.com';
                            $mail->Password = 'bzgs zncv snjv srwb';
                            $mail->SMTPSecure = 'ssl';
                            $mail->Port = 465;

                            // Fetch user email
                            $sql = "SELECT Email FROM tbluser WHERE ID = :uid";
                            $query = $dbh->prepare($sql);
                            $query->bindParam(':uid', $uid, PDO::PARAM_STR);
                            $query->execute();
                            $userEmail = $query->fetch(PDO::FETCH_ASSOC)['Email'];

                            $mail->setFrom('luxestay4@gmail.com');
                            $mail->addAddress($userEmail);
                            $mail->isHTML(true);
                            $mail->Subject = 'Room Booking Confirmation';
                            
                            $template = '<h2>Booking Confirmation</h2>' .
                                '<p>Booking ID: ' . $booknum . '</p>' .
                                '<p>Check in Date: ' . $_POST['checkindate'] . '</p>' .
                                '<p>Check out Date: ' . $_POST['checkoutdate'] . '</p>' .
                                '<p>Mode of Payment: ' . $_POST['payment_mode'] . '</p>' .
                                '<p>Total Cost: ₱' . number_format($grandTotal, 2) . '</p>' .
                                '<p>Amount Paid: ₱' . number_format($requiredPayment, 2) . '</p>' .
                                '<p>Remaining Balance: ₱' . number_format($currentBalance, 2) . '</p>' .
                                '<p>Booking Status: <strong>Pending</strong></p>' .
                                '<p>Note: Please wait for the admin to approve your booking.</p>';

                            $mail->Body = $template;
                            $mail->addAttachment($file_path, 'Proof of Payment');
                            
                            if ($mail->send()) {
                                echo "<script>
                                    alert('Booking Successful! Please check your email for confirmation.');
                                    window.location.href = 'my-booking.php';
                                </script>";
                            }
                        } catch (Exception $e) {
                            echo "Mailer Error: " . $mail->ErrorInfo;
                        }
                    } else {
                        $errorInfo = $query->errorInfo();
                        echo "Database Error: " . $errorInfo[2];
                    }
                }
            }
        }
    } catch (Exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Booking - <?php echo $roomDetails ['RoomName']; ?></title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .review-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .review-section {
            margin-bottom: 25px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .review-title {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .total-section {
            background-color: #e8f4ff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .price-highlight {
            color: #2ecc71;
            font-weight: bold;
            font-size: 1.2em;
        }
        .btn-confirm {
            background-color: #2ecc71;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }
        .btn-back {
            background-color: #95a5a6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            margin-top: 10px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include_once('includes/header.php'); ?>

    <div class="review-container">
        <h2 class="review-title text-center">Review Your Booking</h2>

        <div class="review-section">
            <h3>Room Details</h3>
            <p><strong>Room:</strong> <?php echo $roomDetails['RoomName']; ?> (<?php echo $roomDetails['CategoryName']; ?>)</p>
            <p><strong>Price per night:</strong> ₱<?php echo number_format($roomDetails['Price'], 2); ?></p>
            <p><strong>Number of nights:</strong> <?php echo $nights; ?></p>
            <p><strong>Room Total:</strong> ₱<?php echo number_format($roomTotal, 2); ?></p>
        </div>

        <div class="review-section">
            <h3>Booking Dates</h3>
            <p><strong>Check-in:</strong> <?php echo date('F d, Y', strtotime($_POST['checkindate'])); ?></p>
            <p><strong>Check-out:</strong> <?php echo date('F d, Y', strtotime($_POST['checkoutdate'])); ?></p>
        </div>

        <div class="review-section">
            <h3>Guest Information</h3>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($_POST['address']); ?></p>
            <p><strong>ID Type:</strong> <?php echo htmlspecialchars($_POST['idtype']); ?></p>
        </div>

        <?php if ($foodTotal > 0): ?>
        <div class="review-section">
            <h3>Food and Beverages</h3>
            <?php foreach ($_POST['food_beverages'] as $item => $quantity): ?>
                <?php if ($quantity > 0): ?>
                    <?php
                    $sql = "SELECT Price FROM foodbeveragestbl WHERE item_name = :item";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':item', $item, PDO::PARAM_STR);
                    $query->execute();
                    $price = $query->fetch(PDO::FETCH_ASSOC)['Price'];
                    ?>
                    <p><?php echo htmlspecialchars($item); ?> x <?php echo $quantity; ?> 
                       = ₱<?php echo number_format($price * $quantity, 2); ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
            <p><strong>Food & Beverages Total:</strong> ₱<?php echo number_format($foodTotal, 2); ?></p>
        </div>
        <?php endif; ?>

        <?php if ($_POST['pool_usage'] === 'yes'): ?>
        <div class="review-section">
            <h3>Pool Usage</h3>
            <p>Pool access fee: ₱<?php echo number_format($poolFee, 2); ?></p>
        </div>
        <?php endif; ?>

        <div class="review-section">
            <h3>Payment Details</h3>
            <p><strong>Payment Mode:</strong> <?php echo htmlspecialchars($_POST['payment_mode']); ?></p>
            <p><strong>Payment Option:</strong> <?php echo ($_POST['paymentOptions'] === 'full_payment') ? 'Full Payment' : 'Down Payment'; ?></p>
            <p><strong>Required Payment:</strong> ₱<?php echo number_format($requiredPayment, 2); ?></p>
        </div>

        <div class="total-section">
            <h3>Total Summary</h3>
            <p><strong>Grand Total:</strong> <span class="price-highlight">₱<?php echo number_format($grandTotal, 2); ?></span></p>
            <p><strong>Amount to Pay Now:</strong> <span class="price-highlight">₱<?php echo number_format($requiredPayment, 2); ?></span></p>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <!-- Hidden fields -->
            <input type="hidden" name="room_id" value="<?php echo $rid; ?>">
            <input type="hidden" name="checkindate" value="<?php echo $_POST['checkindate']; ?>">
            <input type="hidden" name="checkoutdate" value="<?php echo $_POST['checkoutdate']; ?>">
            <input type="hidden" name="address" value="<?php echo $_POST['address']; ?>">
            <input type="hidden" name="idtype" value="<?php echo $_POST['idtype']; ?>">
            <input type="hidden" name="payment_mode" value="<?php echo $_POST['payment_mode']; ?>">
            <input type="hidden" name="paymentOptions" value="<?php echo $_POST['paymentOptions']; ?>">
            <input type="hidden" name="downPay" value="<?php echo $_POST['downPay']; ?>">
            <input type="hidden" name="pool_usage" value="<?php echo $_POST['pool_usage']; ?>">
            
            <?php if (!empty($_POST['food_beverages'])): ?>
                <?php foreach ($_POST['food_beverages'] as $item => $quantity): ?>
                    <input type="hidden" name="food_beverages[<?php echo htmlspecialchars($item); ?>]" 
                           value="<?php echo htmlspecialchars($quantity); ?>">
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="form-group">
                <label for="proof_of_payment"><strong>Upload Proof of Payment:</strong></label>
                <input type="file" class="form-control" id="proof_of_payment" name="proof_of_payment" required>
                <small class="text-muted">Please upload your proof of payment (JPG, JPEG, or PNG only)</small>
            </div>

            <button type="submit" name="confirm" class="btn-confirm">Confirm Booking</button>
        </form>

        <button onclick="history.back()" class="btn-back">Back to Booking Form</button>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('proof_of_payment');
            if (!fileInput.files[0]) {
                e.preventDefault();
                alert('Please upload proof of payment before confirming the booking.');
            }
        });
    </script>
</body>
</html>