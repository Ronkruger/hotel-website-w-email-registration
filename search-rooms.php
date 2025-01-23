<?php
session_start();
include('includes/dbconnection.php');

$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "SELECT r.*, c.CategoryName, c.Price 
        FROM tblroom r 
        LEFT JOIN tblcategory c ON r.RoomType = c.ID 
        WHERE r.ID NOT IN (
            SELECT RoomId 
            FROM tblbooking 
            WHERE (CheckinDate <= :checkout AND CheckoutDate >= :checkin)
            AND (Status = 'Approved' OR Status IS NULL)
        )";

if ($category) {
    $sql .= " AND r.RoomType = :category";
}

try {
    $query = $dbh->prepare($sql);
    $query->bindParam(':checkin', $checkin, PDO::PARAM_STR);
    $query->bindParam(':checkout', $checkout, PDO::PARAM_STR);
    if ($category) {
        $query->bindParam(':category', $category, PDO::PARAM_INT);
    }
    $query->execute();
    $rooms = $query->fetchAll(PDO::FETCH_OBJ);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Available Rooms</title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- Add your existing header includes here -->
    
    <style>
        .results-container {
            padding: 30px 0;
        }
        
        .room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .room-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .room-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .room-details {
            padding: 15px;
        }

        .room-price {
            color: #28a745;
            font-size: 1.2em;
            font-weight: bold;
            margin: 10px 0;
        }

        .book-now-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }

        .book-now-btn:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <?php include_once('includes/header.php'); ?>

    <div class="container">
        <div class="results-container">
            <h2>Available Rooms for <?php echo date('M d, Y', strtotime($checkin)); ?> - <?php echo date('M d, Y', strtotime($checkout)); ?></h2>
            
            <div class="room-grid">
                <?php foreach($rooms as $room): ?>
                    <div class="room-card">
                        <img src="admin/images/<?php echo htmlentities($room->Image); ?>" 
                             alt="<?php echo htmlentities($room->RoomName); ?>" 
                             class="room-image">
                        
                        <div class="room-details">
                            <h3><?php echo htmlentities($room->RoomName); ?></h3>
                            <p><?php echo htmlentities($room->CategoryName); ?></p>
                            <div class="room-price">₱<?php echo number_format($room->Price, 2); ?> per night</div>
                            <a href="book-room.php?rmid=<?php echo $room->ID; ?>&checkin=<?php echo $checkin; ?>&checkout=<?php echo $checkout; ?>" 
                               class="book-now-btn">Book Now</a>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($rooms)): ?>
                    <div class="alert alert-info">
                        No rooms available for the selected dates. Please try different dates.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include_once('includes/footer.php'); ?>
</body>
</html>
