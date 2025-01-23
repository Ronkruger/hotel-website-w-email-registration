<?php
// print-booking.php

// Include TCPDF library
require_once('../TCPDF-main/tcpdf.php');

// Fetch the booking number from the query string
if (isset($_GET['bookingNumber'])) {
    $bookingNumber = $_GET['bookingNumber'];

    // Include database connection file
    include('includes/dbconnection.php');

    // Fetch booking details from the database based on the booking number
    $sql = "SELECT b.*, u.FullName AS UserName
            FROM tblbooking b
            INNER JOIN tbluser u ON b.UserID = u.ID
            WHERE b.BookingNumber = :bookingNumber";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':bookingNumber', $bookingNumber, PDO::PARAM_STR);
    $stmt->execute();
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($booking) {
        // Create new PDF document
        $pdf = new TCPDF();

        // Set document information
        $pdf->SetCreator('Hotel Booking Management System');
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Booking Details');
        $pdf->SetSubject('Booking Details');

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 12);

        // Add logo to the top right corner
        $logo = 'images/logos/logo1.png';
        $pdf->Image($logo, 150, 10, 40, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Generate HTML table for booking details
        $html = '<h1>Hotel Website</h1>';
        $html .= '<table border="1">';
        foreach ($booking as $key => $value) {
            $html .= '<tr>';
            $html .= '<td>' . ucfirst(str_replace('_', ' ', $key)) . '</td>';
            $html .= '<td>' . $value . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        // Add HTML table to PDF
        $pdf->writeHTML($html);

        // Output PDF as attachment
        $pdf->Output('booking_details.pdf', 'D');
    } else {
        echo "<h1>Error: Booking not found</h1>";
    }
} else {
    echo "<h1>Error: Booking number not provided</h1>";
}
?>
