<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Room Availability</title>
    <style>
        .container1 {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 28px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .alert {
            padding: 20px;
                    /* From https://css.glass */
            background: rgba(255, 255, 255, 0.2);
            border-radius: 28px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            margin-bottom: 15px;
        }
        .box{
            border: 1px solid black;
            height: 8rem;
     
        }
        li{
            list-style: none;
        }
    </style>
</head>
<body>
    <div class="container1">
        <h2>Check Room Availability</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="check_in_date">Check-in Date:</label>
            <input type="date" id="check_in_date" name="check_in_date" required><br><br>
            <label for="check_out_date">Check-out Date:</label>
            <input type="date" id="check_out_date" name="check_out_date" required><br><br>
            <input type="submit" value="Check Availability">
        </form>

        <?php
        // Include database connection
        include('includes/dbconnection.php');

        // Function to check room availability
        function checkRoomAvailability($check_in_date, $check_out_date, $conn) {
            // SQL query to check room availability
            $sql = "SELECT r.ID AS RoomID, r.RoomName, r.Image, COUNT(b.ID) AS Bookings
            FROM tblroom r
            LEFT JOIN tblbooking b ON r.ID = b.RoomId
            AND (
                (b.CheckinDate <= '$check_in_date' AND b.CheckoutDate >= '$check_in_date') OR
                (b.CheckinDate <= '$check_out_date' AND b.CheckoutDate >= '$check_out_date')
            )
            GROUP BY r.ID";
    

            $result = $conn->query($sql);

            $available_rooms = array();

            if ($result->rowCount() > 0) {
                while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    if ($row['Bookings'] == 0) {
                        $available_rooms[] = array(
                            'RoomID' => $row['RoomID'],
                            'RoomName' => $row['RoomName']
                        );
                    }
                }
            }

            return $available_rooms;
        }

        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $check_in_date = $_POST["check_in_date"];
            $check_out_date = $_POST["check_out_date"];

            // Establish database connection.
            try {
                $dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
            } catch (PDOException $e) {
                exit("Error: " . $e->getMessage());
            }

            $available_rooms = checkRoomAvailability($check_in_date, $check_out_date, $dbh);

            if (!empty($available_rooms)) {
                echo '<div class="alert">Available Rooms:<ul>';
                foreach ($available_rooms as $room) {
                    echo "
                    <div class='box'>
                    <li>Room ID: {$room['RoomID']}, Room Name: {$room['RoomName']} 
                    </li>

                    <button class='btn btn-success'><a href='book-room.phprmid={$room['RoomID']}'>Book</a></button>
           
                    </div>
                   ";

                }
                echo "</ul></div>";
            } else {
                echo '<div class="alert">No rooms available for the selected dates.</div>';
            }

            // Close database connection
            $dbh = null;
        }
        ?>

    </div>
    
</body>
</html>
