<?php
session_start();
include('includes/dbconnection.php');
require_once('TCPDF-main/tcpdf.php'); // Include TCPDF library

// Ensure the user is logged in
if (strlen($_SESSION['hbmsaid']) == 0) {
    header('location:logout.php');
    exit;
}

// Initialize search variables
$searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';
$filterPeriod = isset($_POST['filter_period']) ? $_POST['filter_period'] : 'all';
$filterMonth = isset($_POST['filter_month']) ? $_POST['filter_month'] : '';
$filterYear = isset($_POST['filter_year']) ? $_POST['filter_year'] : '';

// Define static list of months
$allMonths = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

// Define date filter conditions for transactions and bookings
$dateConditionTransactions = '';
$dateConditionBookings = '';
if ($filterPeriod === 'day') {
    $dateConditionTransactions = "AND DATE(t.payment_date) = CURDATE()";
    $dateConditionBookings = "AND DATE(b.BookingDate) = CURDATE()";
} elseif ($filterPeriod === 'week') {
    $dateConditionTransactions = "AND WEEK(t.payment_date) = WEEK(CURDATE()) AND YEAR(t.payment_date) = YEAR(CURDATE())";
    $dateConditionBookings = "AND WEEK(b.BookingDate) = WEEK(CURDATE()) AND YEAR(b.BookingDate) = YEAR(CURDATE())";
} elseif ($filterPeriod === 'month') {
    if ($filterMonth) {
        // Filter by selected month and current year
        $dateConditionTransactions = "AND MONTH(t.payment_date) = :filterMonth AND YEAR(t.payment_date) = YEAR(CURDATE())";
        $dateConditionBookings = "AND MONTH(b.BookingDate) = :filterMonth AND YEAR(b.BookingDate) = YEAR(CURDATE())";
    } else {
        // Default to current month
        $dateConditionTransactions = "AND MONTH(t.payment_date) = MONTH(CURDATE()) AND YEAR(t.payment_date) = YEAR(CURDATE())";
        $dateConditionBookings = "AND MONTH(b.BookingDate) = MONTH(CURDATE()) AND YEAR(b.BookingDate) = YEAR(CURDATE())";
    }
} elseif (is_numeric($filterPeriod) && strlen($filterPeriod) === 4) { // Year filter
    $dateConditionTransactions = "AND YEAR(t.payment_date) = :filterYear";
    $dateConditionBookings = "AND YEAR(b.BookingDate) = :filterYear";
    $filterYear = $filterPeriod;
} else {
    $filterYear = null;
}

// Fetch distinct years from transactions and bookings
$sqlDistinctYears = "
    SELECT DISTINCT YEAR(payment_date) AS year FROM tbltransactions
    UNION
    SELECT DISTINCT YEAR(BookingDate) AS year FROM tblbooking
    ORDER BY year DESC
";
$queryDistinctYears = $dbh->prepare($sqlDistinctYears);
$queryDistinctYears->execute();
$distinctYears = $queryDistinctYears->fetchAll(PDO::FETCH_OBJ);

// Pagination variables
$recordsPerPage = 10;
$pageNumber = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pageNumber - 1) * $recordsPerPage;

// Get total number of records for transactions and bookings combined
$sqlCount = "
    SELECT COUNT(*) AS total 
    FROM (
        SELECT t.transaction_id 
        FROM tbltransactions t 
        JOIN tbluser u ON t.user_id = u.ID 
        WHERE (:searchTerm = '' OR u.FullName LIKE :searchWildcard OR t.transaction_id LIKE :searchWildcard) $dateConditionTransactions
        UNION ALL
        SELECT b.BookingNumber 
        FROM tblbooking b 
        JOIN tbluser u ON b.UserID = u.ID 
        WHERE (:searchTerm = '' OR u.FullName LIKE :searchWildcard OR b.BookingNumber LIKE :searchWildcard) $dateConditionBookings
    ) combined
";
$queryCount = $dbh->prepare($sqlCount);
$queryCount->bindValue(':searchTerm', $searchTerm);
$queryCount->bindValue(':searchWildcard', '%' . $searchTerm . '%');
if (isset($filterYear)) {
    $queryCount->bindValue(':filterYear', $filterYear, PDO::PARAM_INT);
}
$queryCount->execute();
$totalRecords = $queryCount->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Query for transactions
$sqlTransactions = "
    SELECT t.transaction_id, t.room_payment, t.pool_payment, t.food_payment, t.total_amount, t.payment_date, u.FullName 
    FROM tbltransactions t 
    JOIN tbluser u ON t.user_id = u.ID 
    WHERE (:searchTerm = '' OR u.FullName LIKE :searchWildcard OR t.transaction_id LIKE :searchWildcard) $dateConditionTransactions
    " . ($filterMonth ? "AND MONTH(t.payment_date) = :filterMonth" : "") . "
    ORDER BY t.payment_date DESC 
    LIMIT :limit OFFSET :offset
";
$queryTransactions = $dbh->prepare($sqlTransactions);
$queryTransactions->bindValue(':searchTerm', $searchTerm);
$queryTransactions->bindValue(':searchWildcard', '%' . $searchTerm . '%');
$queryTransactions->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
$queryTransactions->bindValue(':offset', $offset, PDO::PARAM_INT);
if (isset($filterYear)) {
    $queryTransactions->bindValue(':filterYear', $filterYear, PDO::PARAM_INT);
}
if ($filterMonth) {
    $queryTransactions->bindValue(':filterMonth', $filterMonth, PDO::PARAM_INT);
}
$queryTransactions->execute();
$resultsTransactions = $queryTransactions->fetchAll(PDO::FETCH_OBJ);

// Query for bookings
$sqlBookings = "
    SELECT b.BookingNumber, b.totalCost, b.BookingDate, u.FullName, b.ID 
    FROM tblbooking b 
    JOIN tbluser u ON b.UserID = u.ID 
    WHERE (:searchTerm = '' OR u.FullName LIKE :searchWildcard OR b.BookingNumber LIKE :searchWildcard) $dateConditionBookings
    " . ($filterMonth ? "AND MONTH(b.BookingDate) = :filterMonth" : "") . "
    ORDER BY b.BookingDate DESC 
    LIMIT :limit OFFSET :offset
";
$queryBookings = $dbh->prepare($sqlBookings);
$queryBookings->bindValue(':searchTerm', $searchTerm);
$queryBookings->bindValue(':searchWildcard', '%' . $searchTerm . '%');
$queryBookings->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
$queryBookings->bindValue(':offset', $offset, PDO::PARAM_INT);
if (isset($filterYear)) {
    $queryBookings->bindValue(':filterYear', $filterYear, PDO::PARAM_INT);
}
if ($filterMonth) {
    $queryBookings->bindValue(':filterMonth', $filterMonth, PDO::PARAM_INT);
}
$queryBookings->execute();
$resultsBookings = $queryBookings->fetchAll(PDO::FETCH_OBJ);

// Generate PDF for a transaction or booking
if (isset($_GET['download_pdf'])) {
    $type = $_GET['type']; // 'transaction' or 'booking'
    $id = $_GET['id'];

    // Start output buffering to prevent data being sent before TCPDF
    ob_start();

    $pdf = new TCPDF();
    $pdf->AddPage();

    if ($type == 'transaction') {
        // Fetch transaction details
        $transaction = $dbh->prepare("SELECT t.*, u.FullName FROM tbltransactions t JOIN tbluser u ON t.user_id = u.ID WHERE t.transaction_id = :id");
        $transaction->bindValue(':id', $id);
        $transaction->execute();
        $transaction = $transaction->fetch(PDO::FETCH_ASSOC);

        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Transaction Details', 0, 1, 'C');
        $pdf->Cell(0, 10, 'Transaction ID: ' . $transaction['transaction_id'], 0, 1);
        $pdf->Cell(0, 10, 'User: ' . $transaction['FullName'], 0, 1);
        $pdf->Cell(0, 10, 'Total Amount: ' . number_format($transaction['total_amount'], 2), 0, 1);
        $pdf->Cell(0, 10, 'Payment Date: ' . date('Y-m-d', strtotime($transaction['payment_date'])), 0, 1);
    } elseif ($type == 'booking') {
        // Fetch booking details
        $booking = $dbh->prepare("SELECT b.*, u.FullName FROM tblbooking b JOIN tbluser u ON b.UserID = u.ID WHERE b.ID = :id");
        $booking->bindValue(':id', $id);
        $booking->execute();
        $booking = $booking->fetch(PDO::FETCH_ASSOC);

        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Booking Details', 0, 1, 'C');
        $pdf->Cell(0, 10, 'Booking Number: ' . $booking['BookingNumber'], 0, 1);
        $pdf->Cell(0, 10, 'User: ' . $booking['FullName'], 0, 1);
        $pdf->Cell(0, 10, 'Total Cost: ' . number_format($booking['totalCost'], 2), 0, 1);
        $pdf->Cell(0, 10, 'Booking Date: ' . date('Y-m-d', strtotime($booking['BookingDate'])), 0, 1);
    }

    // Output the PDF
    $pdf->Output('record.pdf', 'D');
    
    // End the script to prevent further HTML output
    ob_end_flush();
    exit;
}
?>


<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking Management System | Payment History</title>
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
        .table th, .table td {
            text-align: center;
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
                <h2>Payment History</h2>
                <form method="POST" action="">
                    <input type="text" name="search" placeholder="Search..." value="<?php echo htmlentities($searchTerm); ?>">
                    <select name="filter_period">
                        <option value="all" <?php echo $filterPeriod === 'all' ? 'selected' : ''; ?>>All Time</option>
                        <option value="day" <?php echo $filterPeriod === 'day' ? 'selected' : ''; ?>>Today</option>
                        <option value="week" <?php echo $filterPeriod === 'week' ? 'selected' : ''; ?>>This Week</option>
                        <option value="month" <?php echo $filterPeriod === 'month' ? 'selected' : ''; ?>>This Month</option>
                        <?php foreach ($distinctYears as $year): ?>
                            <option value="<?php echo $year->year; ?>" <?php echo $filterPeriod == $year->year ? 'selected' : ''; ?>>
                                <?php echo $year->year; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="filter_month">
                        <option value="">All Months</option>
                        <?php foreach ($allMonths as $monthNumber => $monthName): ?>
                            <option value="<?php echo $monthNumber; ?>" <?php echo $filterMonth == $monthNumber ? 'selected' : ''; ?>>
                                <?php echo $monthName; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Filter</button>
                </form>

                <!-- Transactions Table -->
                <h3>Transactions</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>User</th>
                            <th>Total Amount</th>
                            <th>Payment Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultsTransactions as $transaction): ?>
                            <tr>
                                <td><?php echo htmlentities($transaction->transaction_id); ?></td>
                                <td><?php echo htmlentities($transaction->FullName); ?></td>
                                <td><?php echo number_format($transaction->total_amount, 2); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($transaction->payment_date)); ?></td>
                                <td><a href="?download_pdf=1&type=transaction&id=<?php echo $transaction->transaction_id; ?>" class="btn">Download PDF</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Bookings Table -->
                <h3>Bookings</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Booking Number</th>
                            <th>User</th>
                            <th>Total Cost</th>
                            <th>Booking Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultsBookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlentities($booking->BookingNumber); ?></td>
                                <td><?php echo htmlentities($booking->FullName); ?></td>
                                <td><?php echo number_format($booking->totalCost, 2); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($booking->BookingDate)); ?></td>
                                <td><a href="?download_pdf=1&type=booking&id=<?php echo $booking->ID; ?>" class="btn">Download PDF</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $pageNumber == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</body>
</html>
