<?php
session_start();
include('includes/dbconnection.php');
require_once('TCPDF-main/tcpdf.php'); // Include TCPDF library

// Ensure the user is logged in
if (strlen($_SESSION['hbmsaid']) == 0) {
    header('location:logout.php');
    exit;
}

// Fetch and generate PDF for a specific transaction or booking
if (isset($_GET['download_pdf'])) {
    $type = $_GET['type']; // 'transaction' or 'booking'
    $id = $_GET['id'];

    // Start output buffering to prevent data being sent before TCPDF
    ob_start();

    // Initialize TCPDF
    $pdf = new TCPDF();
    $pdf->AddPage();

    if ($type == 'transaction') {
        // Fetch transaction details
        $transactionQuery = $dbh->prepare("SELECT t.*, u.FullName FROM tbltransactions t JOIN tbluser u ON t.user_id = u.ID WHERE t.transaction_id = :id");
        $transactionQuery->bindValue(':id', $id);
        $transactionQuery->execute();
        $transaction = $transactionQuery->fetch(PDO::FETCH_ASSOC);

        // Add transaction details to the PDF
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Transaction Details', 0, 1, 'C');
        $pdf->Ln(5);

        // Output all transaction details
        $pdf->Cell(40, 10, 'Transaction ID:', 0, 0);
        $pdf->Cell(0, 10, $transaction['transaction_id'], 0, 1);
        $pdf->Cell(40, 10, 'User:', 0, 0);
        $pdf->Cell(0, 10, $transaction['FullName'], 0, 1);
        $pdf->Cell(40, 10, 'Room Payment:', 0, 0);
        $pdf->Cell(0, 10, '$' . number_format($transaction['room_payment'], 2), 0, 1);
        $pdf->Cell(40, 10, 'Pool Payment:', 0, 0);
        $pdf->Cell(0, 10, '$' . number_format($transaction['pool_payment'], 2), 0, 1);
        $pdf->Cell(40, 10, 'Food Payment:', 0, 0);
        $pdf->Cell(0, 10, '$' . number_format($transaction['food_payment'], 2), 0, 1);
        $pdf->Cell(40, 10, 'Total Amount:', 0, 0);
        $pdf->Cell(0, 10, '$' . number_format($transaction['total_amount'], 2), 0, 1);
        $pdf->Cell(40, 10, 'Payment Date:', 0, 0);
        $pdf->Cell(0, 10, date('Y-m-d', strtotime($transaction['payment_date'])), 0, 1);
    } elseif ($type == 'booking') {
        // Fetch booking details
        $bookingQuery = $dbh->prepare("SELECT b.*, u.FullName FROM tblbooking b JOIN tbluser u ON b.UserID = u.ID WHERE b.ID = :id");
        $bookingQuery->bindValue(':id', $id);
        $bookingQuery->execute();
        $booking = $bookingQuery->fetch(PDO::FETCH_ASSOC);

        // Add booking details to the PDF
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Booking Details', 0, 1, 'C');
        $pdf->Ln(5);

        // Output all booking details
        $pdf->Cell(40, 10, 'Booking Number:', 0, 0);
        $pdf->Cell(0, 10, $booking['BookingNumber'], 0, 1);
        $pdf->Cell(40, 10, 'User:', 0, 0);
        $pdf->Cell(0, 10, $booking['FullName'], 0, 1);
        $pdf->Cell(40, 10, 'Room ID:', 0, 0);
        $pdf->Cell(0, 10, $booking['RoomId'], 0, 1);
        $pdf->Cell(40, 10, 'Check-in Date:', 0, 0);
        $pdf->Cell(0, 10, date('Y-m-d', strtotime($booking['CheckinDate'])), 0, 1);
        $pdf->Cell(40, 10, 'Checkout Date:', 0, 0);
        $pdf->Cell(0, 10, date('Y-m-d', strtotime($booking['CheckoutDate'])), 0, 1);
        $pdf->Cell(40, 10, 'Booking Date:', 0, 0);
        $pdf->Cell(0, 10, date('Y-m-d', strtotime($booking['BookingDate'])), 0, 1);
        $pdf->Cell(40, 10, 'Total Cost:', 0, 0);
        $pdf->Cell(0, 10, '$' . number_format($booking['totalCost'], 2), 0, 1);
        $pdf->Cell(40, 10, 'Food & Beverages Cost:', 0, 0);
        $pdf->Cell(0, 10, '$' . number_format($booking['FoodBeveragesCost'], 2), 0, 1);
        $pdf->Cell(40, 10, 'Pool Usage Cost:', 0, 0);
        $pdf->Cell(0, 10, '$' . number_format($booking['PoolUsageCost'], 2), 0, 1);
    }

    // Output the PDF
    $pdf->Output('record.pdf', 'I');
    
    // End the script to prevent further HTML output
    ob_end_flush();
    exit;
}
?>
