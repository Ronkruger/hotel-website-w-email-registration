<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['hbmsaid'] == 0)) {
    header('location:logout.php');
} else {
    // Fetch walk-in reservations from the database
    $sql = "SELECT * FROM tblwalkin"; // Adjust this query if necessary
    $query = $dbh->prepare($sql);
    $query->execute();
    $reservations = $query->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Walk-In Reservations</title>
    <link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
    <link href="css/style.css" rel='stylesheet' type='text/css' />
</head>
<body>

    <div class="container">
        <h2>Walk-In Reservations</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>People</th>
                    <th>Room(s)</th>
                    <th>Food/Beverage Requests</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reservations)): ?>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?php echo htmlentities($reservation['ID']); ?></td>
                            <td><?php echo htmlentities($reservation['name']); ?></td>
                            <td><?php echo htmlentities($reservation['contact']); ?></td>
                            <td><?php echo htmlentities($reservation['check_in']); ?></td>
                            <td><?php echo htmlentities($reservation['check_out']); ?></td>
                            <td><?php echo htmlentities($reservation['people']); ?></td>
                            <td>
                                <?php
                                // Fetch room names for the reservation
                                $roomIds = explode(',', $reservation['RoomId']); // Assuming RoomId is a comma-separated string
                                $roomNames = [];

                                foreach ($roomIds as $roomId) {
                                    $roomSql = "SELECT RoomName FROM tblroom WHERE ID = :room_id";
                                    $roomQuery = $dbh->prepare($roomSql);
                                    $roomQuery->bindParam(':room_id', $roomId);
                                    $roomQuery->execute();
                                    $room = $roomQuery->fetch(PDO::FETCH_ASSOC);
                                    if ($room) {
                                        $roomNames[] = $room['RoomName'];
                                    }
                                }
                                echo implode(', ', $roomNames); // Display room names as a comma-separated list
                                ?>
                            </td>
                            <td><?php echo htmlentities($reservation['food_beverage_request']); ?></td>
                            <td><?php echo htmlentities($reservation['total_price']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No reservations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="js/bootstrap.min.js"></script>
</body>
</html>
