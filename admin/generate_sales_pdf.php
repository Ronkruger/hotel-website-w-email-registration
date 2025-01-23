<?php
session_start();
require_once 'TCPDF-main/tcpdf.php';
include('includes/dbconnection.php');

// Ensure the user is logged in
if (strlen($_SESSION['hbmsaid']) == 0) {
    header('location:logout.php');
    exit;
}

// Fetch the filter criteria from URL
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

// Create the PDF instance
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle("Sales Summary");

$pdf->AddPage();

// Set the font
$pdf->SetFont("helvetica", "", 12);

// Add a title
$pdf->Cell(0, 10, 'Sales Summary', 0, 1, 'C');

// Add the filter period and year info
$pdf->Ln(10);
$pdf->Cell(0, 10, "Filter Period: " . ucfirst($filterPeriod) . ($filterYear ? " " . $filterYear : ""), 0, 1, 'L');

// Add the sales data
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Category', 1, 0, 'C');
$pdf->Cell(0, 10, 'Total Sales', 1, 1, 'C');

// Add the booking sales
$pdf->Cell(0, 10, 'Total Booking Sales', 1, 0, 'L');
$pdf->Cell(0, 10, number_format($bookingTotals['total_booking_sales'], 2), 1, 1, 'R');

// Add the transaction sales
$pdf->Cell(0, 10, 'Total Room Payment', 1, 0, 'L');
$pdf->Cell(0, 10, number_format($transactionTotals['room_payment'], 2), 1, 1, 'R');

$pdf->Cell(0, 10, 'Total Pool Payment', 1, 0, 'L');
$pdf->Cell(0, 10, number_format($transactionTotals['pool_payment'], 2), 1, 1, 'R');

$pdf->Cell(0, 10, 'Total Food Payment', 1, 0, 'L');
$pdf->Cell(0, 10, number_format($transactionTotals['food_payment'], 2), 1, 1, 'R');

$pdf->Cell(0, 10, 'Total Transaction Sales', 1, 0, 'L');
$pdf->Cell(0, 10, number_format($transactionTotals['total_sales'], 2), 1, 1, 'R');

// Output PDF (download as file)
$pdf->Output("sales_summary_" . date("Y_m_d_H_i_s") . ".pdf", 'D');
?>
