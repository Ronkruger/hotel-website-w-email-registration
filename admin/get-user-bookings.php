<?php
include('includes/dbconnection.php');

if (isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];

    // Query to get bookings associated with the user
    $sql = "SELECT ID, booking_reference FROM tblbookings WHERE user_id = :user_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();

    // Fetch results and output them as options
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($results) > 0) {
        foreach ($results as $row) {
            echo "<option value='" . $row['ID'] . "'>" . htmlspecialchars($row['booking_reference']) . "</option>";
        }
    } else {
        echo "<option value=''>No bookings found</option>";
    }
}
?>
