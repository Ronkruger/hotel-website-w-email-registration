<?php
// Include the TCPDF library from the correct folder
require_once('TCPDF-main/tcpdf.php');  // Since TCPDF is in the same folder as this file
include('includes/dbconnection.php');

if (isset($_GET['booking_id']) || isset($_GET['transaction_id'])) {
    // Initialize TCPDF
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Hotel Booking Management System');
    $pdf->SetTitle('Record Details');

    // Set header (no logo in this case)
    $pdf->SetHeaderData('', 0, 'Record Details', '');

    // Set font for header and footer
    $pdf->setHeaderFont(Array('helvetica', '', 12));  // Ensure you use a valid font
    $pdf->setFooterFont(Array('helvetica', '', 8));  // Footer font setting

    // Set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Set general font
    $pdf->setFont('helvetica', '', 12);

    // Add a new page
    $pdf->AddPage();

    // Generate content for Booking or Transaction
    $html = "";
    if (isset($_GET['booking_id'])) {
        $booking_id = intval($_GET['booking_id']);
        $sql = "SELECT b.*, u.FullName 
                FROM tblbooking b 
                JOIN tbluser u ON b.UserID = u.ID 
                WHERE b.ID = :booking_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_OBJ);

        if ($row) {
            // Create a table for booking details with borders
            $html .= "<h2>Booking Details</h2>";
            $html .= "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
            $html .= "<tr><th style='border: 1px solid black;'>Booking Number</th><td style='border: 1px solid black;'>" . htmlentities($row->BookingNumber) . "</td></tr>";
            $html .= "<tr><th style='border: 1px solid black;'>Customer Name</th><td style='border: 1px solid black;'>" . htmlentities($row->FullName) . "</td></tr>";
            $html .= "<tr><th style='border: 1px solid black;'>Check-In Date</th><td style='border: 1px solid black;'>" . htmlentities($row->CheckinDate) . "</td></tr>";
            $html .= "<tr><th style='border: 1px solid black;'>Check-Out Date</th><td style='border: 1px solid black;'>" . htmlentities($row->CheckoutDate) . "</td></tr>";
            $html .= "<tr><th style='border: 1px solid black;'>Total Cost</th><td style='border: 1px solid black;'>$" . htmlentities($row->totalCost) . "</td></tr>";
            $html .= "<tr><th style='border: 1px solid black;'>Booking Date</th><td style='border: 1px solid black;'>" . htmlentities($row->BookingDate) . "</td></tr>";
            $html .= "</table>";
        } else {
            $html .= "<p>No record found for Booking ID: $booking_id</p>";
        }
    } elseif (isset($_GET['transaction_id'])) {
        $transaction_id = $_GET['transaction_id'];
        $sql = "SELECT t.*, u.FullName 
                FROM tbltransactions t 
                JOIN tbluser u ON t.user_id = u.ID 
                WHERE t.transaction_id = :transaction_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':transaction_id', $transaction_id, PDO::PARAM_STR);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_OBJ);

        if ($row) {
            // Create a table for transaction details with borders
            $html .= "<h2>Transaction Details</h2>";
            $html .= "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
            $html .= "<tr><th style='border: 1px solid black;'>Transaction ID</th><td style='border: 1px solid black;'>" . htmlentities($row->transaction_id) . "</td></tr>";
            $html .= "<tr><th style='border: 1px solid black;'>Customer Name</th><td style='border: 1px solid black;'>" . htmlentities($row->FullName) . "</td></tr>";
            $html .= "<tr><th style='border: 1px solid black;'>Room Payment</th><td style='border: 1px solid black;'>$" . htmlentities($row->room_payment) . "</td></tr>";
            $html .= "<tr><th style='border: 1px solid black;'>Pool Payment</th><td style='border: 1px solid black;'>$" . htmlentities($row->pool_payment) . "</td></tr>";
            $html .= "<tr><th style='border: 1px solid black;'>Food Payment</th><td style='border: 1px solid black;'>$" . htmlentities($row->food_payment) . "</td></tr>";
            $html .= "<tr><th style='border: 1px solid black;'>Total Amount</th><td style='border: 1px solid black;'>$" . htmlentities($row->total_amount) . "</td></tr>";
            $html .= "<tr><th style='border: 1px solid black;'>Payment Date</th><td style='border: 1px solid black;'>" . htmlentities($row->payment_date) . "</td></tr>";
            $html .= "</table>";
        } else {
            $html .= "<p>No record found for Transaction ID: $transaction_id</p>";
        }
    }

    // Write the HTML content to the PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Output the PDF as a downloadable file
    $pdf->Output('record_details.pdf', 'D');  // 'D' for download
} else {
    echo "Invalid request.";
}
?>
