    <?php
    // Replace this with your database connection code
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ecohavendb";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch occupied dates and room names from the database
    $sql = "SELECT b.CheckinDate, b.CheckoutDate, r.RoomName
            FROM tblbooking b
            INNER JOIN tblroom r ON b.RoomId = r.ID
            WHERE b.Status = 'Approved'";
    $result = $conn->query($sql);

    $occupiedDates = array();
    $occupiedRoomNames = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Convert check-in and check-out dates to date range
            $checkinDate = strtotime($row["CheckinDate"]);
            $checkoutDate = strtotime($row["CheckoutDate"]);
            while ($checkinDate <= $checkoutDate) {
                $occupiedDates[] = date("Y-m-d", $checkinDate);
                $occupiedRoomNames[date("Y-m-d", $checkinDate)] = $row["RoomName"];
                $checkinDate = strtotime("+1 day", $checkinDate);
            }
        }
    }

    $conn->close();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Occupied Dates Calendar</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .day {
        border: 2px solid black;
        border-radius:8px;
        padding: 10px;
        text-align: center;
        cursor: pointer;
    }
    
    .empty-cell {
        border: 1px solid transparent;
        padding: 10px;
    }

    .occupied {
        background-color: green;
        color:white;
    }

    /* Style for current date */
    .current-date {
        border: 1px solid black; /* Adjust border color as needed */
        background-color:black;
        color:white;
    }
    .container{
        /* background-color:black;
        color:white; */
        width: 800px;
        height: 400px;
        border-radius: 28px;

    }
    .calendar{
        background-color: white;
        color:black;
        width: 450px;
        height: 380px;
        border-radius: 28px;
        border: 8px solid black;
        padding-bottom: 10px;
    }
</style>
</head>
<body>
<?php
// Replace this with your database connection code
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecohavendb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch occupied dates and room names from the database
$sql = "SELECT b.CheckinDate, b.CheckoutDate, r.RoomName
        FROM tblbooking b
        INNER JOIN tblroom r ON b.RoomId = r.ID
        WHERE b.Status = 'Approved'";
$result = $conn->query($sql);

$occupiedDates = array();
$occupiedRoomNames = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Convert check-in and check-out dates to date range
        $checkinDate = strtotime($row["CheckinDate"]);
        $checkoutDate = strtotime($row["CheckoutDate"]);
        while ($checkinDate <= $checkoutDate) {
            $occupiedDates[] = date("Y-m-d", $checkinDate);
            $occupiedRoomNames[date("Y-m-d", $checkinDate)] = $row["RoomName"];
            $checkinDate = strtotime("+1 day", $checkinDate);
        }
    }
}

$conn->close();

// Get the date from the URL parameter
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m');

// Define the start date as the first day of the current month
$startDate = date('Y-m-01', strtotime($date));

// Get the number of days in the current month
$totalDays = date('t', strtotime($startDate));

// Get the day of the week of the first day of the month (0 = Sunday, 6 = Saturday)
$startDayOfWeek = date('w', strtotime($startDate));
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="calendar">
                <!-- <div align="center">
                <h4>Legend:</h4> 
                <b style="color:red;">RED</b>: occupied date
                <br>
                <b>BLACK</b>: Current Date
                </div> -->
              
                <div class="d-flex justify-content-between mb-3 game">
                    <button class="btn btn-primary" onclick="previousMonth('<?php echo $startDate; ?>')"><</button>
                    <h2><?php echo date('F Y', strtotime($startDate)); ?></h2>
                    <button class="btn btn-primary" onclick="nextMonth('<?php echo $startDate; ?>')">></button>
                </div>
               
                <?php
                // Initialize the day counter
                $dayCount = 1;

                // Loop through each row
                echo '<div class="row">';
                // Loop through each day of the week
                for ($col = 0; $col < 7; $col++) {
                    echo '<div class="col day">' . date('D', strtotime("Sunday +$col day")) . '</div>';
                }
                echo '</div>';

                // Initialize the row
                echo '<div class="row">';
                // Loop through each day of the month
                for ($day = 1; $day <= $totalDays; $day++) {
                    // Check if the current day is occupied
                    $currentDate = date('Y-m-d', strtotime("$startDate +$dayCount day"));
                    $isOccupied = in_array($currentDate, $occupiedDates);
                    echo '<div class="col day';
                    if ($isOccupied) {
                        echo ' occupied" data-room="' . $occupiedRoomNames[$currentDate] . '"';
                    }
                    // Add the "current-date" class to the current date cell
                    if ($currentDate == date('Y-m-d')) {
                        echo ' current-date';
                    }
                    echo '">' . $day . '</div>';
                    // Increment the day counter
                    $dayCount++;
                    // If it's the last day of the week or the last day of the month, end the row
                    if (($dayCount - 1) % 7 == 0 || $day == $totalDays) {
                        // Fill the remaining empty cells in the row if necessary
                        while (($dayCount - 1) % 7 != 0) {
                            echo '<div class="col empty-cell"></div>';
                            $dayCount++;
                        }
                        echo '</div>';
                        // If it's not the last day of the month, start a new row
                        if ($day != $totalDays) {
                            echo '<div class="row">';
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
function previousMonth(currentDate) {
    const previousMonthDate = new Date('<?php echo $startDate; ?>');
    previousMonthDate.setMonth(previousMonthDate.getMonth() - 1);
    const formattedDate = previousMonthDate.toISOString().slice(0, 7);
    window.location.href = `?date=${formattedDate}`;
}

function nextMonth(currentDate) {
    const nextMonthDate = new Date('<?php echo $startDate; ?>');
    nextMonthDate.setMonth(nextMonthDate.getMonth() + 1);
    const formattedDate = nextMonthDate.toISOString().slice(0, 7);
    window.location.href = `?date=${formattedDate}`;
}


    // Show room name on hover over occupied date
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.occupied').forEach(item => {
            item.addEventListener('mouseover', event => {
                const roomName = event.target.getAttribute('data-room');
                if (roomName) {
                    alert('Room Name: ' + roomName);
                }
            });
        });
    });
</script>

</body>
</html>
