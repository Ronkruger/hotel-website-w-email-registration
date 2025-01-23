<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('includes/dbconnection.php');

if (strlen($_SESSION['hbmsuid']) == 0) {
    header('location:logout.php');
} else {
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking Management System | View Booking Detail</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .header {
            background-color: #343a40;
            color: white;
            padding: 10px 0;
        }

        .section-title {
            font-size: 1.8em;
            font-weight: bold;
            margin: 20px 0;
            color: #343a40;
            text-align: center;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #dc3545;
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .table th {
            background-color: #e9ecef;
            vertical-align: middle;
        }

        .room-image {
            max-width: 150px;
            border-radius: 10px;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            transition: background-color 0.3s;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .alert-danger {
            margin: 20px 0;
        }

        @media (max-width: 576px) {
            .room-image {
                max-width: 100px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <?php include_once('includes/header.php'); ?>
        </div>
    </div>

    <div class="container my-4">
        <div id="booking-details">
            <h2 class="section-title">My Hotel Booking Detail</h2>
            <?php
            // Capture the BookingNumber from the URL
            $vid = $_GET['viewid'];

            // Fetch booking details for the logged-in user and specific booking
            $sql = "SELECT tblbooking.BookingNumber, tbluser.FullName, tbluser.MobileNumber, 
                        tbluser.Email, tblbooking.ID as tid, tblbooking.IDType, tblbooking.Gender, 
                        tblbooking.Address, tblbooking.CheckinDate, tblbooking.CheckoutDate, 
                        tblbooking.BookingDate, tblbooking.Remark, tblbooking.Status, 
                        tblbooking.UpdationDate, tblbooking.downPay, tblbooking.payment_mode, 
                        tblbooking.totalCost, tblbooking.currentBalance, 
                        tblbooking.FoodBeveragesCost, tblbooking.PoolUsageCost,
                        tblcategory.CategoryName, tblcategory.Price, 
                        tblroom.RoomName, tblroom.MaxAdult, tblroom.MaxChild, 
                        tblroom.RoomDesc, tblroom.NoofBed, tblroom.Image, 
                        tblroom.RoomFacility 
                    FROM tblbooking 
                    JOIN tblroom ON tblbooking.RoomId = tblroom.ID 
                    JOIN tblcategory ON tblcategory.ID = tblroom.RoomType 
                    JOIN tbluser ON tblbooking.UserID = tbluser.ID  
                    WHERE tblbooking.BookingNumber = :vid 
                    AND tblbooking.UserID = :userID"; // Added filter for UserID

            $query = $dbh->prepare($sql);
            $query->bindParam(':vid', $vid, PDO::PARAM_STR);
            $query->bindParam(':userID', $_SESSION['hbmsuid'], PDO::PARAM_INT); // Get the UserID from session
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);

            if ($query->rowCount() > 0) {
                foreach ($results as $row) {
            ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">Booking Number: <?php echo $row->BookingNumber; ?></h4>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Booking Detail</h5>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Customer Name</th>
                                        <td><?php echo $row->FullName; ?></td>
                                        <th>Mobile Number</th>
                                        <td><?php echo $row->MobileNumber; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><?php echo $row->Email; ?></td>
                                        <th>ID Type</th>
                                        <td><?php echo $row->IDType; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td><?php echo $row->Gender; ?></td>
                                        <th>Address</th>
                                        <td><?php echo $row->Address; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Check-in Date</th>
                                        <td><?php echo $row->CheckinDate; ?></td>
                                        <th>Check-out Date</th>
                                        <td><?php echo $row->CheckoutDate; ?></td>
                                    </tr>
                                </tbody>
                            </table>

                            <h5 class="card-title">Room Detail</h5>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Room Type</th>
                                        <td><?php echo $row->CategoryName; ?></td>
                                        <th>Room Price (per day)</th>
                                        <td>₱<?php echo number_format($row->Price, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Room Name</th>
                                        <td><?php echo $row->RoomName; ?></td>
                                        <th>Room Description</th>
                                        <td><?php echo $row->RoomDesc; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Max Adults</th>
                                        <td><?php echo $row->MaxAdult; ?></td>
                                        <th>Max Children</th>
                                        <td><?php echo $row->MaxChild; ?></td>
                                    </tr>
                                    <tr>
                                        <th>No. of Beds</th>
                                        <td><?php echo $row->NoofBed; ?></td>
                                        <th>Room Image</th>
                                        <td><img src="admin/images/<?php echo $row->Image; ?>" class="img-fluid room-image"></td>
                                    </tr>
                                    <tr>
                                        <th>Room Facility</th>
                                        <td colspan="3"><?php echo $row->RoomFacility; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Booking Date</th>
                                        <td><?php echo $row->BookingDate; ?></td>
                                        <th>Payment Mode</th>
                                        <td><?php echo $row->payment_mode; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Down Payment</th>
                                        <td>₱<?php echo number_format($row->downPay, 2); ?></td>
                                        <th>Total Cost</th>
                                        <td>₱<?php echo number_format($row->totalCost, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Current Balance</th>
                                        <td colspan="3">₱<?php echo number_format($row->currentBalance, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-info">Food & Beverages Cost:</th>
                                    </tr>
                                    <tr>
                                        <th>Food & Beverages Cost</th>
                                        <td colspan="3">₱<?php echo number_format($row->FoodBeveragesCost, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Pool Usage Cost</th>
                                        <td colspan="3">₱<?php echo number_format($row->PoolUsageCost, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Order Final Status</th>
                                        <td colspan="3"><?php echo $row->Status == "Approved" ? "Your Booking has been approved" : 
                                                ($row->Status == "Cancelled" ? "Your Booking has been cancelled" : "Not Responded Yet"); 
                                        ?></td>
                                    </tr>
                                    <tr>
                                        <th>Admin Remarks</th>
                                        <td colspan="3"><?php echo $row->Remark ?: "Not Updated Yet"; ?></td>
                                    </tr>
                                </tbody>
                            </table>

                            <h5 class="card-title">Added Transactions</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch added transactions related to this booking and user
                                    $sqlTransactions = "SELECT * FROM tbltransactions WHERE booking_id = :booking_id AND user_id = :user_id";
                                    $queryTransactions = $dbh->prepare($sqlTransactions);
                                    $queryTransactions->bindParam(':booking_id', $row->tid, PDO::PARAM_INT);  // Use booking ID here
                                    $queryTransactions->bindParam(':user_id', $_SESSION['hbmsuid'], PDO::PARAM_INT); // User ID filter
                                    $queryTransactions->execute();
                                    $transactions = $queryTransactions->fetchAll(PDO::FETCH_OBJ);

                                    if (count($transactions) > 0) {
                                        foreach ($transactions as $transaction) {
                                    ?>
                                            <tr>
                                                <td>Room Payment</td>
                                                <td>₱<?php echo number_format($transaction->room_payment, 2); ?></td>
                                                <td><?php echo $transaction->payment_date; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Pool Payment</td>
                                                <td>₱<?php echo number_format($transaction->pool_payment, 2); ?></td>
                                                <td><?php echo $transaction->payment_date; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Food Payment</td>
                                                <td>₱<?php echo number_format($transaction->food_payment, 2); ?></td>
                                                <td><?php echo $transaction->payment_date; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Total Amount</td>
                                                <td>₱<?php echo number_format($transaction->total_amount, 2); ?></td>
                                                <td><?php echo $transaction->payment_date; ?></td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center'>No transactions found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <div class="text-center mb-3">
                                <a href="invoice.php?invid=<?php echo htmlentities($row->tid); ?>" class="btn btn-success"><i class="fas fa-file-invoice"></i> Generate Invoice</a>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<div class='alert alert-danger'>No booking details available.</div>";
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once('includes/footer.php'); ?>
</body>

</html>
<?php } ?>
