<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['hbmsaid']) == 0) {
    header('location:logout.php');
} else {
    // Function to generate a random 11-digit transaction ID
    function generateRandomTransactionId() {
        return str_pad(rand(0, 99999999999), 11, '0', STR_PAD_LEFT);
    }

    // Fetch bookings based on the selected user (if action is 'fetch_bookings')
    if (isset($_POST['action']) && $_POST['action'] == 'fetch_bookings' && isset($_POST['user_id'])) {
        $user_id = (int)$_POST['user_id'];

        // Query to get bookings associated with the user, ordered by check-in date
        $sql = "SELECT ID, BookingNumber, CheckinDate FROM tblbooking WHERE UserID = :user_id ORDER BY CheckinDate DESC";
        $query = $dbh->prepare($sql);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();

        // Fetch results and output them as options
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        if (count($results) > 0) {
            foreach ($results as $row) {
                $formatted_date = date('d-m-Y', strtotime($row['CheckinDate'])); // Format the date to display
                echo "<option value='" . $row['ID'] . "'>" . htmlspecialchars($row['BookingNumber']) . " (" . $formatted_date . ")</option>";
            }
        } else {
            echo "<option value=''>No bookings found</option>";
        }
        exit; // Ensure the script stops executing after the response is sent
    }

?>

<!DOCTYPE HTML>
<html>

<head>
    <title>Hotel Booking Management System | Add Transaction</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
    <!-- Toastr CSS (Toast Notification Library) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel='stylesheet' type='text/css' />
    <!-- Font Awesome and other CSS -->
    <link href="css/font-awesome.css" rel="stylesheet"> 
    <script src="js/jquery-1.10.2.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // When the "Search" button is clicked, make an AJAX call to load the bookings
            $('#search_button').click(function() {
                var userId = $('#user_id').val();  // Get selected user ID

                if (userId) {
                    $.ajax({
                        type: 'POST',
                        url: '',  // Current page
                        data: { action: 'fetch_bookings', user_id: userId },
                        success: function(response) {
                            $('#booking_id').html(response); // Populate the bookings dropdown
                            if (response.includes("No bookings found")) {
                                toastr.warning('No bookings found for this user.', 'Warning');
                            } else {
                                toastr.success('Bookings loaded successfully.', 'Success');
                            }
                        }
                    });
                } else {
                    toastr.error('Please select a customer first!', 'Error');
                }
            });
        });
    </script>
</head>

<body>
    <div class="page-container">
        <div class="left-content">
            <div class="inner-content">
                <?php include_once('includes/header.php'); ?>
                
                <div class="content">
                    <h2 class="text-center">Add Transaction</h2>

                    <div class="col-md-6 col-md-offset-3">
                        <form method="post" name="addtransaction" action="add-transaction.php">
                            <!-- Customer Selection -->
                            <div class="form-group">
                                <label for="user_id">Select Customer:</label>
                                <select class="form-control" id="user_id" name="user_id" required>
                                    <option value="">Select Customer</option>
                                    <?php
                                    $sql = "SELECT ID, FullName FROM tbluser WHERE isBlocked = 0";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($results as $row) {
                                        echo "<option value ='" . $row['ID'] . "'>" . htmlspecialchars($row['FullName']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Search Button -->
                            <div class="form-group">
                                <button type="button" id="search_button" class="btn btn-primary">Search</button>
                            </div>

                            <!-- Booking Selection -->
                            <div class="form-group">
                                <label for="booking_id">Select Booking:</label>
                                <select class="form-control" id="booking_id" name="booking_id" required>
                                    <option value="">Select Booking</option>
                                </select>
                            </div>

                            <!-- Payment Fields -->
                            <div class="form-group">
                                <label for="room_payment">Room Payment:</label>
                                <input type="number" step="0.01" class="form-control" id="room_payment" name="room_payment" required>
                            </div>

                            <div class="form-group">
                                <label for="pool_payment">Pool Payment:</label>
                                <input type="number" step="0.01" class="form-control" id="pool_payment" name="pool_payment" required>
                            </div>

                            <div class="form-group">
                                <label for="food_payment">Food/Beverage Payment:</label>
                                <input type="number" step="0.01" class="form-control" id="food_payment" name="food_payment" required>
                            </div>

                            <div class="form-group">
                                <button type="submit" name="submit" class="btn btn-success">Add Transaction</button>
                            </div>
                        </form>
                    </div>

                    <?php
                    // Handle form submission
                    if (isset($_POST['submit'])) {
                        // Retrieve and sanitize form data
                        $user_id = (int)$_POST['user_id'];
                        $booking_id = (int)$_POST['booking_id'];
                        $room_payment = (float)$_POST['room_payment'];
                        $pool_payment = (float)$_POST['pool_payment'];
                        $food_payment = (float)$_POST['food_payment'];

                        // Calculate total amount
                        $total_amount = $room_payment + $pool_payment + $food_payment;

                        // Generate a random transaction ID
                        $transaction_id = generateRandomTransactionId();

                        try {
                            // Insert the transaction into the database
                            $sql = "INSERT INTO tbltransactions (transaction_id, user_id, booking_id, room_payment, pool_payment, food_payment, total_amount) 
                                    VALUES (:transaction_id, :user_id, :booking_id, :room_payment, :pool_payment, :food_payment, :total_amount)";
                            $query = $dbh->prepare($sql);
                            $query->bindParam(':transaction_id', $transaction_id, PDO::PARAM_STR);
                            $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                            $query->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
                            $query->bindParam(':room_payment', $room_payment, PDO::PARAM_STR);
                            $query->bindParam(':pool_payment', $pool_payment, PDO::PARAM_STR);
                            $query->bindParam(':food_payment', $food_payment, PDO::PARAM_STR);
                            $query->bindParam(':total_amount', $total_amount, PDO::PARAM_STR);

                            if ($query->execute()) {
                                echo "<div class='alert alert-success'>Transaction added successfully!</div>";
                            } else {
                                echo "<div class='alert alert-danger'>Error adding the transaction.</div>";
                            }
                        } catch (Exception $e) {
                            echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                        }
                    }
                    ?>

                </div>
            </div>
        </div>

        <?php include_once('includes/sidebar.php'); ?>
        <div class="clearfix"></div>        
    </div>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>

<?php
}
?>
