<?php
session_start();
include('includes/dbconnection.php');

if(isset($_GET['bookingid']) && !empty($_GET['bookingid'])) {
    $bookingid = $_GET['bookingid'];

    if (isset($_POST['extend'])) {
        $newCheckoutDate = $_POST['new_checkout_date'];
        $paymentMethod = $_POST['payment_method'];

        // Update the checkout date and payment method in tblbooking
        $sql = "UPDATE tblbooking SET CheckoutDate=:newCheckoutDate, payment_mode=:paymentMethod WHERE BookingNumber=:bookingid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':newCheckoutDate', $newCheckoutDate, PDO::PARAM_STR);
        $query->bindParam(':paymentMethod', $paymentMethod, PDO::PARAM_STR);
        $query->bindParam(':bookingid', $bookingid, PDO::PARAM_STR);
        
        if($query->execute()) {
            echo '<script>alert("Checkout date and payment method have been updated!"); window.location.href="view-booking-detail.php?bookingid='.$bookingid.'";</script>';
        } else {
            echo '<script>alert("Failed to update checkout date or payment method.");</script>';
        }
    }

    // Fetch current booking details to display
    $sql = "SELECT CheckoutDate, payment_mode FROM tblbooking WHERE BookingNumber=:bookingid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':bookingid', $bookingid, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    if($result) {
        $currentCheckoutDate = $result->CheckoutDate;
        $currentPaymentMode = $result->payment_mode;
    } else {
        echo '<script>alert("Booking not found."); window.location.href="new-booking.php";</script>';
    }
} else {
    echo '<script>alert("No booking selected!"); window.location.href="new-booking.php";</script>';
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Extend Stay</title>
    <style>
        /* General body styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Card container */
        .container {
            max-width: 600px;
            width: 100%;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            box-sizing: border-box;
        }

        /* Title styling */
        .container h3 {
            font-size: 1.5em;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Label styling */
        .form-group label {
            display: block;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }

        /* Form input and select styling */
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            font-size: 1em;
            color: #333;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Form buttons */
        .form-group button {
            width: 48%;
            padding: 10px;
            font-size: 1em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: #ffffff;
            margin-top: 10px;
            display: inline-block;
        }

        /* Primary and secondary button colors */
        .form-group .btn-primary {
            background-color: #28a745;
        }
        
        .form-group .btn-secondary {
            background-color: #6c757d;
        }

        /* Alignment for buttons */
        .button-group {
            text-align: center;
        }

        /* Responsive styling */
        @media (max-width: 768px) {
            .container {
                width: 90%;
                padding: 15px;
            }

            .form-group button {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Extend Stay for Booking Number: <?php echo htmlentities($bookingid); ?></h3>
        <form method="POST">
            <div class="form-group">
                <label><strong>Current Checkout Date:</strong></label>
                <p><?php echo htmlentities($currentCheckoutDate); ?></p>
            </div>

            <div class="form-group">
                <label for="new_checkout_date"><strong>New Checkout Date:</strong></label>
                <input type="date" name="new_checkout_date" required>
            </div>

            <div class="form-group">
                <label for="payment_method"><strong>Payment Method:</strong></label>
                <select name="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="cash" <?php echo ($currentPaymentMode == 'cash') ? 'selected' : ''; ?>>Cash</option>
                    <option value="card" <?php echo ($currentPaymentMode == 'card') ? 'selected' : ''; ?>>Card</option>
                    <option value="gcash" <?php echo ($currentPaymentMode == 'gcash') ? 'selected' : ''; ?>>GCash</option>
                    <option value="maya" <?php echo ($currentPaymentMode == 'maya') ? 'selected' : ''; ?>>Maya</option>
                </select>
            </div>

            <div class="button-group">
                <button type="submit" name="extend" class="btn-primary">Extend Stay</button>
                <a href="view-booking-detail.php?bookingid=<?php echo $bookingid; ?>" class="btn-secondary">Back to Booking Details</a>
            </div>
        </form>
    </div>
</body>
</html>
