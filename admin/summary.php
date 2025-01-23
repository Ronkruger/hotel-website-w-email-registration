<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

// Fetch the latest reservation from the database
$stmt = $dbh->prepare("SELECT * FROM tblwalkin ORDER BY id DESC LIMIT 1");
$stmt->execute();
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

// Start the HTML output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Summary</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace; /* Use a monospaced font for a receipt look */
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            line-height: 1.5; /* Increase line height for readability */
        }
        .receipt {
            max-width: 400px; /* Narrow width for receipt */
            margin: 0 auto;
            padding: 15px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            font-size: 14px; /* Standard receipt font size */
        }
        h1 {
            text-align: center;
            font-size: 18px; /* Slightly larger for title */
            margin-bottom: 10px;
            border-bottom: 2px solid #000; /* Underline for the title */
        }
        p {
            margin: 2px 0;
            color: #333;
        }
        .total {
            font-weight: bold;
            font-size: 16px; /* Make the total larger */
            margin-top: 15px;
            text-align: right;
            color: #000; /* Darker color for total */
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px; /* Smaller font for footer */
            color: #555;
            border-top: 1px dashed #ccc; /* Dashed line above footer */
            padding-top: 10px;
        }

        /* Print styles */
        @media print {
            body {
                background: none; /* Remove background for print */
                padding: 0; /* Remove padding */
            }
            .receipt {
                border: none; /* No border on print */
                box-shadow: none; /* No shadow on print */
            }
            h1, p, .total, .footer {
                color: #000; /* Ensure text is black */
            }
            /* Hide print button if included */
            .print-button {
                display: none;
            }
        }
    </style>
    <script>
        function printReceipt() {
            window.print(); // Print the current window
        }
    </script>
</head>
<body>

<div class="receipt">
    <?php
    if ($reservation) {
        // Display reservation details
        echo "<h1>Reservation Summary</h1>";
        echo "<p>Reservation ID: {$reservation['id']}</p>";
        echo "<p>Name: {$reservation['name']}</p>";
        echo "<p>Contact: {$reservation['contact']}</p>";

        echo "<p>Number of People: {$reservation['people']}</p>";
        echo "<p>Room ID: {$reservation['RoomId']}</p>";
        echo "<p>Check-in Date: {$reservation['check_in']}</p>";
        echo "<p>Check-out Date: {$reservation['check_out']}</p>";
        echo "<p>Food/Beverages Requested: {$reservation['food_beverages']}</p>";
        echo "<div class='total'>Total Price: ₱{$reservation['TotalPrice']}</div>";
    } else {
        echo "<p>No reservation found.</p>";
    }
    ?>
    <div class="footer">
        <p>Thank you for choosing us!</p>
        <p>Visit us again!</p>
    </div>
</div>

<!-- Print button -->
<button class="print-button" onclick="printReceipt()">Print Receipt</button>

</body>
</html>
