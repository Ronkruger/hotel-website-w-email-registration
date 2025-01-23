<?php
session_start();
error_reporting(0);
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
    <title>Hotel Booking Management System | Invoice</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }

        .header {
 
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-bottom: 20px;
        }

        h2 {
            color: #212529;
            margin-bottom: 20px;
            text-align: center;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        .invoice-table {
            margin: 0 auto;
            width: 90%;
        }

        .invoice-table th,
        .invoice-table td {
            text-align: center;
            padding: 15px;
            border: 1px solid #e0e0e0;
        }

        .invoice-header {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            color: #dc3545;
            text-align: center;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 10px;
        }

        .grand-total {
            color: #007bff;
            font-weight: bold;
            font-size: 20px;
            text-align: right;
        }

        button.btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button.btn:hover {
            background-color: #218838;
        }

        .room-image {
            max-width: 120px;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <!-- Header -->

   
        <?php include_once('includes/header.php'); ?>

    <!-- Header -->

    <!-- Invoice Section -->
    <div class="container">
        <h2>Invoice</h2>
        <p>My Hotel Booking Detail</p>

        <div class="bs-docs-example">
            <?php
            $invid = $_GET['invid'];
            $sql = "SELECT 
                        tblbooking.BookingNumber,
                        tbluser.FullName,
                        tbluser.MobileNumber,
                        tbluser.Email,
                        tblbooking.ID as tid,
                        tblbooking.CheckinDate,
                        tblbooking.CheckoutDate,
                        tblbooking.BookingDate,
                        tblcategory.CategoryName,
                        tblcategory.Price,
                        tblroom.RoomName,
                        tblroom.Image,
                        tblbooking.FoodBeveragesCost,
                        tblbooking.PoolUsageCost,
                        tblbooking.totalCost,
                        tblbooking.downPay,
                        tblbooking.currentBalance,
                        tblbooking.Remark,
                        tblbooking.Status
                    FROM tblbooking
                    JOIN tblroom ON tblbooking.RoomId = tblroom.ID
                    JOIN tblcategory ON tblcategory.ID = tblroom.RoomType
                    JOIN tbluser ON tblbooking.UserID = tbluser.ID  
                    WHERE tblbooking.ID = :invid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':invid', $invid, PDO::PARAM_STR);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);
            if ($query->rowCount() > 0) {
                foreach ($results as $row) {
                    ?>
                    <div class="invoice-header">Booking Number: <?php echo $row->BookingNumber; ?></div>
                    <h5 class="invoice-header">Customer Details</h5>
                    <table class="table table-bordered invoice-table">
                        <tr>
                            <th>Customer Name</th>
                            <td><?php echo $row->FullName; ?></td>
                        </tr>
                        <tr>
                            <th>Mobile Number</th>
                            <td><?php echo $row->MobileNumber; ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?php echo $row->Email; ?></td>
                        </tr>
                        <tr>
                            <th>Check-in Date</th>
                            <td><?php echo $row->CheckinDate; ?></td>
                        </tr>
                        <tr>
                            <th>Check-out Date</th>
                            <td><?php echo $row->CheckoutDate; ?></td>
                        </tr>
                    </table>

                    <h5 class="invoice-header">Room Details</h5>
                    <table class="table table-bordered invoice-table">
                        <tr>
                            <th>Room Type</th>
                            <td><?php echo $row->CategoryName; ?></td>
                        </tr>
                        <tr>
                            <th>Room Name</th>
                            <td><?php echo $row->RoomName; ?></td>
                        </tr>
                        <tr>
                            <th>Room Image</th>
                            <td><img src="admin/images/<?php echo $row->Image; ?>" class="img-fluid room-image"></td>
                        </tr>
                        <tr>
                            <th>Room Price (per day)</th>
                            <td>₱<?php echo number_format($row->Price, 2); ?></td>
                        </tr>
                    </table>

                    <h5 class="invoice-header">Cost Details</h5>
                    <table class="table table-bordered invoice-table">
                        <tr>
                            <th>Food & Beverages Cost</th>
                            <td>₱<?php echo number_format($row->FoodBeveragesCost, 2); ?></td>
                        </tr>
                        <tr>
                            <th>Pool Usage Cost</th>
                            <td>₱<?php echo number_format($row->PoolUsageCost, 2); ?></td>
                        </tr>
                        <tr>
                            <th>Down Payment</th>
                            <td>₱<?php echo number_format($row->downPay, 2); ?></td>
                        </tr>
                        <tr>
                            <th>Total Cost</th>
                            <td>₱<?php echo number_format($row->totalCost, 2); ?></td>
                        </tr>
                        <tr>
                            <th>Current Balance</th>
                            <td>₱<?php echo number_format($row->currentBalance, 2); ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><?php echo $row->Status; ?></td>
                        </tr>
                        <tr>
                            <th>Admin Remarks</th>
                            <td><?php echo !empty($row->Remark) ? $row->Remark : "Not Updated Yet"; ?></td>
                        </tr>
                    </table>

                    <?php 
                }
            } else {
                echo "<p class='text-danger'>No invoice details available.</p>";
            }
            ?>
            <div class="text-center">
                <button class="btn" onClick="return window.print();">Print Invoice</button>
            </div>
        </div>
    </div>
    <!-- // Invoice Section -->

    <!-- Footer -->
    <?php include_once('includes/footer.php'); ?>
</body>
</html>
<?php } ?>