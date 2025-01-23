<?php
include('includes/dbconnection.php');
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer
require 'C:\xampp\htdocs\PHPMailer\PHPMailer\src\Exception.php';
require 'C:\xampp\htdocs\PHPMailer\PHPMailer\src\PHPMailer.php';
require __DIR__ . '/vendor/autoload.php';

if (strlen($_SESSION['hbmsuid']) == 0) {
    header('location:logout.php');
} else {
    $dbh = new PDO("mysql:host=localhost;dbname=hbmsdb", "root", "");
    // Fetch food items before the form
    $sql = "SELECT item_name, Price as price FROM foodbeveragestbl";
    $query = $dbh->prepare($sql);
    $query->execute();
    $food_items = $query->fetchAll(PDO::FETCH_ASSOC);


        // Calculate number of days
        $checkinDateTime = new DateTime($checkindate);
        $checkoutDateTime = new DateTime($checkoutdate);
        $interval = $checkinDateTime->diff($checkoutDateTime);
        $num_days = $interval->days;

     // PDO connection
     $dbh = new PDO("mysql:host=localhost;dbname=hbmsdb", "root", "");

             // Fetch room price
             $sql = "SELECT c.Price FROM tblroom r 
             JOIN tblcategory c ON r.RoomType = c.ID 
             WHERE r.ID = :rid";
     $query = $dbh->prepare($sql);
     $query->bindParam(':rid', $rid, PDO::PARAM_STR);
     $query->execute();
     $result = $query->fetch(PDO::FETCH_ASSOC);
     $room_price = $result['Price'];

     // Calculate total cost
     $total_price = $room_price * $num_days;
     $min_downpayment = $total_price / 2;

     // Calculate food and beverages cost
     $food_beverages_cost = 0;
     if (isset($_POST['food_beverages'])) {
         foreach ($_POST['food_beverages'] as $item => $quantity) {
             $sql = "SELECT Price FROM foodbeveragestbl WHERE item_name = :item";
             $query = $dbh->prepare($sql);
             $query->bindParam(':item', $item, PDO::PARAM_STR);
             $query->execute();
             $food_price = $query->fetch(PDO::FETCH_ASSOC)['Price'];
             $food_beverages_cost += $food_price * $quantity;
         }
     }
    if (isset($_POST['submit'])) {
        $booknum = mt_rand(100000000, 999999999);
        $rid = intval($_GET['rmid']);
        $uid = $_SESSION['hbmsuid'];
        $address = $_POST['address'];
        $idtype = $_POST['idtype'];
        $checkindate = $_POST['checkindate'];
        $checkoutdate = $_POST['checkoutdate'];
        $payment_mode = $_POST['payment_mode'];
        $payment_option = $_POST['paymentOptions'];
        $downPay = ($payment_option == 'full_payment') ? 0 : $_POST['downPay'];
        $cdate = date('Y-m-d');

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
                    echo "File uploaded successfully to $file_path";
                } else {
                    echo "Failed to move the uploaded file.";
                    exit();
                }
            } else {
                echo "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
                exit();
            }
        } else {
            echo "Error: " . $_FILES['proof_of_payment']['error'];
            exit();
        }

        $sql = "INSERT INTO tblbooking(RoomId, BookingNumber, UserID, Address, IDType, CheckinDate, CheckoutDate, downPay, payment_mode, paymentOptions, proof_of_payment) 
                VALUES (:rid, :booknum, :uid, :address, :idtype, :checkindate, :checkoutdate, :downPay, :payment_mode, :payment_option, :proof_of_payment)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':rid', $rid, PDO::PARAM_STR);
        $query->bindParam(':booknum', $booknum, PDO::PARAM_STR);
        $query->bindParam(':uid', $uid, PDO::PARAM_STR);
        $query->bindParam(':address', $address, PDO::PARAM_STR);
        $query->bindParam(':idtype', $idtype, PDO::PARAM_STR);
        $query->bindParam(':checkindate', $checkindate, PDO::PARAM_STR);
        $query->bindParam(':checkoutdate', $checkoutdate, PDO::PARAM_STR);
        $query->bindParam(':downPay', $downPay, PDO::PARAM_STR);
        $query->bindParam(':payment_mode', $payment_mode, PDO::PARAM_STR);
        $query->bindParam(':payment_option', $payment_option, PDO::PARAM_STR);
        $query->bindParam(':proof_of_payment', $file_path, PDO::PARAM_STR);
        $query->execute();

        $LastInsertId = $dbh->lastInsertId();
        if ($LastInsertId > 0) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'luxestay4@gmail.com';
                    $mail->Password = 'bzgs zncv snjv srwb';
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port = 465;

                    $mail->setFrom('luxestay4@gmail.com');

                    // Fetch user email
                    $sql = "SELECT Email FROM tbluser WHERE ID = :uid";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
                    $query->execute();
                    $result = $query->fetch(PDO::FETCH_ASSOC);
                    $userEmail = $result['Email'];
                    $mail->addAddress($userEmail);

                    $mail->isHTML(true);
                    $mail->Subject = 'Room Booking Confirmation';
                    $template = '<h2>Booking Confirmation</h2>' .
                        '<p>Booking ID: ' . $booknum . '</p>' .
                        '<p>Total Cost: ₱' . $total_price . '</p>';
                    $mail->addAttachment($file_path, 'Proof of Payment');

                    $mail->Body = $template;

                    $mail->send();

                echo '<script>alert("Booking Successful!")</script>';
            } catch (Exception $e) {
                echo "Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo '<script>alert("Something went wrong. Please try again.")</script>';
        }
    }
}
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form - Hotel Website</title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.js"></script>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            font-size: 1.1rem;
            color: #444;
            display: block;
            margin: 15px 0 5px;
        }

        input[type="text"],
        input[type="date"],
        select,
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        input[type="number"] {
            width: 50%;
        }

        button {
            width: 100%;
            padding: 15px;
            background-color: #4CAF50;
            color: white;
            font-size: 1.1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
            }
        }
    </style>
</head>

<body>
<?php include_once('includes/header.php'); ?>

<div class="container">
    <h2>Booking Form</h2>

    <form id="bookingForm" method="POST" enctype="multipart/form-data">


        <label for="address">Address:</label>
        <input type="text" name="address" id="address" required>

        <label for="idtype">ID Type:</label>
        <select name="idtype" id="idtype" required>
            <option value="passport">Passport</option>
            <option value="driver_license">Driver's License</option>
            <option value="national_id">National ID</option>
        </select>

        <label for="checkindate">Check-in Date:</label>
        <input type="date" name="checkindate" id="checkindate" required>

        <label for="checkoutdate">Check-out Date:</label>
        <input type="date" name="checkoutdate" id="checkoutdate" required>

        <h3>Food and Beverages</h3>
        <label for="food_beverages">Select items:</label>
        <div class="food-items">
            <?php if (!empty($food_items)): ?>
                <?php foreach ($food_items as $item): ?>
                    <label for="food_<?php echo htmlspecialchars($item['item_name']); ?>">
                        <?php echo htmlspecialchars($item['item_name']); ?> (₱<?php echo htmlspecialchars($item['price']); ?>) Quantity:
                    </label>
                    <input type="number" name="food_beverages[<?php echo htmlspecialchars($item['item_name']); ?>]" 
                           id="food_<?php echo htmlspecialchars($item['item_name']); ?>" 
                           value="0" min="0">
                    <br>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No food items available</p>
            <?php endif; ?>
        </div>
        <h3>Pool Usage</h3>
        <label>Do you want to use the pool?</label>
        <input type="radio" name="pool_usage" value="yes" id="pool_yes"> Yes
        <input type="radio" name="pool_usage" value="no" id="pool_no" checked> No
        <br>

        <h3>Payment Options</h3>
        <label for="payment_mode">Payment Mode:</label>
        <select name="payment_mode" id="payment_mode" required>
            <option value="Gcash">Gcash</option>
            <option value="Maya">Maya</option>
        </select>

        <label for="paymentOptions">Payment Options:</label>
        <select name="paymentOptions" id="paymentOptions" required>
            <option value="full_payment">Full Payment</option>
            <option value="down_payment">Down Payment</option>
        </select>

        <div id="downPaymentSection" style="display: none;">
            <label for="downPay">Down Payment Amount:</label>
            <input type="number" name="downPay" id="downPay" min="0">
        </div>
        <label for="proof_of_payment">Proof of Payment:</label>
        <input type="file" id="proof_of_payment" name="proof_of_payment" accept="image/*" required>
        <button type="submit" name="submit">Book Now</button>
    </form>
</div>

<script>
    document.getElementById('paymentOptions').addEventListener('change', function() {
        var downPaymentSection = document.getElementById('downPaymentSection');
        if (this.value === 'down_payment') {
            downPaymentSection.style.display = 'block';
        } else {
            downPaymentSection.style.display = 'none';
        }
    });


    document.getElementById("bookingForm").addEventListener("submit", function (e) {
    const proofOfPayment = document.getElementById("proof_of_payment").files[0];
    if (!proofOfPayment) {
        alert("Proof of payment is required!");
        e.preventDefault();
    }
});

</script>

</body>
</html>
