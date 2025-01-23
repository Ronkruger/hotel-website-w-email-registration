<?php
include('includes/dbconnection.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['hbmsuid'])) {
    header('location:logout.php');
    exit();
}

// Get booking number from either GET parameter or session
$bookingNumber = $_GET['bookingNumber'] ?? $_SESSION['booking_number'] ?? '';

if (empty($bookingNumber)) {
    echo '<script>alert("No booking number provided.");</script>';
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}

try {
    // Fetch booking details with joined tables
    $sql = "SELECT 
                b.BookingNumber, 
                b.CheckinDate, 
                b.CheckoutDate, 
                b.totalCost AS TotalCost, 
                b.FoodBeveragesCost, 
                b.PoolUsageCost,
                b.currentBalance AS CurrentBalance, 
                b.payment_mode, 
                b.paymentOptions,
                u.FullName, 
                u.Email,
                c.CategoryName AS RoomType
            FROM tblbooking b
            JOIN tbluser u ON b.UserID = u.ID
            JOIN tblroom r ON b.RoomId = r.ID
            JOIN tblcategory c ON r.RoomType = c.ID
            WHERE b.BookingNumber = :bookingNumber 
            AND b.UserID = :userId";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':bookingNumber', $bookingNumber, PDO::PARAM_STR);
    $stmt->bindParam(':userId', $_SESSION['hbmsuid'], PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        throw new Exception("Booking not found or unauthorized access.");
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        throw new Exception("Failed to fetch booking details.");
    }
} catch (Exception $e) {
    echo '<script>alert("' . htmlspecialchars($e->getMessage()) . '");</script>';
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}
if (!$result) {
    throw new Exception("Failed to fetch booking details.");
}

// Continue with HTML output only if we have results
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proof of Payment</title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table th, table td {
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #f9f9f9;
            width: 40%;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .btn:hover {
            background-color: #45a049;
            text-decoration: none;
            color: #fff;
        }
    </style>
</head>

<body>
    <?php include_once('includes/header.php'); ?>
    
    <div class="container">
        <h2>Proof of Payment</h2>
        <p>Below are your booking details:</p>
        <table>
            <tr>
                <th>Booking Number</th>
                <td><?php echo htmlspecialchars($result['BookingNumber']); ?></td>
            </tr>
            <tr>
                <th>Full Name</th>
                <td><?php echo htmlspecialchars($result['FullName']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($result['Email']); ?></td>
            </tr>
            <tr>
                <th>Room Type</th>
                <td><?php echo htmlspecialchars($result['RoomType']); ?></td>
            </tr>
            <tr>
                <th>Check-in Date</th>
                <td><?php echo date('F j, Y', strtotime($result['CheckinDate'])); ?></td>
            </tr>
            <tr>
                <th>Check-out Date</th>
                <td><?php echo date('F j, Y', strtotime($result['CheckoutDate'])); ?></td>
            </tr>
            <tr>
                <th>Total Cost</th>
                <td>₱<?php echo number_format((float)$result['TotalCost'], 2); ?></td>
            </tr>
            <tr>
                <th>Food and Beverages</th>
                <td>₱<?php echo number_format((float)$result['FoodBeveragesCost'], 2); ?></td>
            </tr>
            <tr>
                <th>Pool Usage</th>
                <td>₱<?php echo number_format((float)$result['PoolUsageCost'], 2); ?></td>
            </tr>
            <tr>
                <th>Payment Mode</th>
                <td><?php echo ucwords(str_replace('_', ' ', $result['payment_mode'])); ?></td>
            </tr>
            <tr>
                <th>Payment Option</th>
                <td><?php echo ucwords(str_replace('_', ' ', $result['paymentOptions'])); ?></td>
            </tr>
            <tr>
                <th>Current Balance</th>
                <td>₱<?php echo number_format((float)$result['CurrentBalance'], 2); ?></td>
            </tr>
        </table>
        <a class="btn" href="index.php">Back to Home</a>
    </div>
</body>
</html>
