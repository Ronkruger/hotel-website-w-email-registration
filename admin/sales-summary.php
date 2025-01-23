<?php
session_start();
include('includes/dbconnection.php');

// Ensure the user is logged in
if (strlen($_SESSION['hbmsaid']) == 0) {
    header('location:logout.php');
    exit;
}

// Fetch the filter criteria from URL or POST
$filterPeriod = isset($_GET['filter_period']) ? $_GET['filter_period'] : 'all';
$filterYear = isset($_GET['filter_year']) ? $_GET['filter_year'] : null;

// Define date filter conditions for transactions and bookings
$dateConditionTransactions = '';
$dateConditionBookings = '';
$monthNameToNumber = [
    'january' => 1,
    'february' => 2,
    'march' => 3,
    'april' => 4,
    'may' => 5,
    'june' => 6,
    'july' => 7,
    'august' => 8,
    'september' => 9,
    'october' => 10,
    'november' => 11,
    'december' => 12,
];

if ($filterPeriod === 'day') {
    $dateConditionTransactions = "AND DATE(t.payment_date) = CURDATE()";
    $dateConditionBookings = "AND DATE(b.BookingDate) = CURDATE()";
} elseif ($filterPeriod === 'week') {
    $dateConditionTransactions = "AND WEEK(t.payment_date) = WEEK(CURDATE()) AND YEAR(t.payment_date) = YEAR(CURDATE())";
    $dateConditionBookings = "AND WEEK(b.BookingDate) = WEEK(CURDATE()) AND YEAR(b.BookingDate) = YEAR(CURDATE())";
} elseif ($filterPeriod === 'month') {
    $dateConditionTransactions = "AND MONTH(t.payment_date) = MONTH(CURDATE()) AND YEAR(t.payment_date) = YEAR(CURDATE())";
    $dateConditionBookings = "AND MONTH(b.BookingDate) = MONTH(CURDATE()) AND YEAR(b.BookingDate) = YEAR(CURDATE())";
} elseif ($filterPeriod && array_key_exists($filterPeriod, $monthNameToNumber)) {
    $monthNumber = $monthNameToNumber[strtolower($filterPeriod)];
    $dateConditionTransactions = "AND MONTH(t.payment_date) = $monthNumber AND YEAR(t.payment_date) = YEAR(CURDATE())";
    $dateConditionBookings = "AND MONTH(b.BookingDate) = $monthNumber AND YEAR(b.BookingDate) = YEAR(CURDATE())";
} elseif ($filterYear) { // Year filter
    $dateConditionTransactions = "AND YEAR(t.payment_date) = :filterYear";
    $dateConditionBookings = "AND YEAR(b.BookingDate) = :filterYear";
}

// Fetch total sales data from the database
$sqlTransactions = "
    SELECT SUM(t.room_payment) AS room_payment, SUM(t.pool_payment) AS pool_payment, 
           SUM(t.food_payment) AS food_payment, SUM(t.total_amount) AS total_sales
    FROM tbltransactions t 
    WHERE 1=1 $dateConditionTransactions
";
$queryTransactions = $dbh->prepare($sqlTransactions);
if ($filterYear) {
    $queryTransactions->bindValue(':filterYear', $filterYear, PDO::PARAM_INT);
}
$queryTransactions->execute();
$transactionTotals = $queryTransactions->fetch(PDO::FETCH_ASSOC);

$sqlBookings = "
    SELECT SUM(b.totalCost) AS total_booking_sales
    FROM tblbooking b
    WHERE 1=1 $dateConditionBookings
";
$queryBookings = $dbh->prepare($sqlBookings);
if ($filterYear) {
    $queryBookings->bindValue(':filterYear', $filterYear, PDO::PARAM_INT);
}
$queryBookings->execute();
$bookingTotals = $queryBookings->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking Management System | Sales Summary</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="css/font-awesome.css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <style>
        .btn {
            padding: 5px 10px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 3px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .content {
            margin-left: 18rem;
        }
        .table {
            width: 600px;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="sidebar">
            <?php include_once('includes/sidebar.php'); ?>
        </div>
        <div class="content">
            <div class="container">
                <h2>Sales Summary</h2>
                <form method="GET" action="">
                    <select name="filter_period">
                        <option value="all" <?php echo $filterPeriod === 'all' ? 'selected' : ''; ?>>All Time</option>
                        <option value="day" <?php echo $filterPeriod === 'day' ? 'selected' : ''; ?>>Today</option>
                        <option value="week" <?php echo $filterPeriod === 'week' ? 'selected' : ''; ?>>This Week</option>
                        <option value="month" <?php echo $filterPeriod === 'month' ? 'selected' : ''; ?>>This Month</option>
                        <option value="january" <?php echo $filterPeriod === 'january' ? 'selected' : ''; ?>>January</option>
                        <option value="february" <?php echo $filterPeriod === 'february' ? 'selected' : ''; ?>>February</option>
                        <option value="march" <?php echo $filterPeriod === 'march' ? 'selected' : ''; ?>>March</option>
                        <option value="april" <?php echo $filterPeriod === 'april' ? 'selected' : ''; ?>>April</option>
                        <option value="may" <?php echo $filterPeriod === 'may' ? 'selected' : ''; ?>>May</option>
                        <option value="june" <?php echo $filterPeriod === 'june' ? 'selected' : ''; ?>>June</option>
                        <option value="july" <?php echo $filterPeriod === 'july' ? 'selected' : ''; ?>>July</option>
                        <option value="august" <?php echo $filterPeriod === 'august' ? 'selected' : ''; ?>>August</option>
                        <option value="september" <?php echo $filterPeriod === 'september' ? 'selected' : ''; ?>>September</option>
                        <option value="october" <?php echo $filterPeriod === 'october' ? 'selected' : ''; ?>>October</option>
                        <option value="november" <?php echo $filterPeriod === 'november' ? 'selected' : ''; ?>>November</option>
                        <option value="december" <?php echo $filterPeriod === 'december' ? 'selected' : ''; ?>>December</option>
                    </select>
                    <select name="filter_year">
                        <option value="2024" <?php echo $filterYear === '2024' ? 'selected' : ''; ?>>2024</option>
                        <option value="2025" <?php echo $filterYear === '2025' ? 'selected' : ''; ?>>2025</option>
                        <!-- Add other years as needed -->
                    </select>
                    <button type="submit" class="btn">Filter</button>
                </form>

                <h3>Sales Summary for <?php echo ucfirst($filterPeriod); ?> <?php echo $filterYear ? $filterYear : ''; ?></h3>
                
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Booking Sales</td>
                            <td><?php echo number_format($bookingTotals['total_booking_sales'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Total Room Payment</td>
                            <td><?php echo number_format($transactionTotals['room_payment'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Total Pool Payment</td>
                            <td><?php echo number_format($transactionTotals['pool_payment'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Total Food Payment</td>
                            <td><?php echo number_format($transactionTotals['food_payment'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Total Transaction Sales</td>
                            <td><?php echo number_format($transactionTotals['total_sales'], 2); ?></td>
                        </tr>
                    </tbody>
                </table>

                <a href="generate_sales_pdf.php?filter_period=<?php echo $filterPeriod; ?>&filter_year=<?php echo $filterYear; ?>" class="btn">Generate PDF</a>
            </div>
        </div>
    </div>
</body>
</html>
