<?php
session_start();
include('includes/dbconnection.php');

if (strlen($_SESSION['hbmsaid'] == 0)) {
    header('location:logout.php');
} else {
    // Get search query if available
    $search = isset($_POST['search']) ? $_POST['search'] : '';

    // Fetch rooms based on search query
    $sql_rooms = "SELECT r.ID, r.RoomName, r.RoomDesc, r.Image, c.Price 
                  FROM tblroom r 
                  JOIN tblcategory c ON r.RoomType = c.ID 
                  WHERE r.RoomName LIKE :search OR r.RoomDesc LIKE :search";
    $query_rooms = $dbh->prepare($sql_rooms);
    $query_rooms->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    $query_rooms->execute();
    $rooms = $query_rooms->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Catalogue</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
            font-family: Arial, sans-serif;
        }
        h2 {
            text-align: center;
            margin: 20px 0;
            color: #007bff;
        }
        .room-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 0 20px;
        }
        .room-card {
            border: 1px solid #007bff;
            border-radius: 8px;
            background-color: #fff;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .room-card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .room-image {
            height: 180px;
            overflow: hidden;
        }
        .room-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .room-details {
            padding: 15px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .room-title {
            font-size: 18px;
            color: #007bff;
            margin: 0;
        }
        .room-desc {
            font-size: 14px;
            margin: 10px 0;
            flex-grow: 1;
        }
        .room-price {
            font-size: 16px;
            font-weight: bold;
            color: #d9534f;
        }
        .btn-view {
            margin-top: 15px;
            width: 100%;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            font-size: 14px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-view:hover {
            background-color: #0056b3;
        }
        .search-bar {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            display: flex;
            justify-content: center;
        }
        .search-bar input {
            width: 80%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .search-bar button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Room Catalogue</h2>

        <!-- Search Bar -->
        <form method="POST" class="search-bar">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search for rooms..." />
            <button type="submit">Search</button>
        </form>

        <!-- Room Cards -->
        <div class="room-container">
            <?php if (count($rooms) > 0): ?>
                <?php foreach ($rooms as $room) : ?>
                    <div class="room-card">
                        <div class="room-image">
                            <img src="images/<?php echo $room['Image']; ?>" alt="<?php echo $room['RoomName']; ?>">
                        </div>
                        <div class="room-details">
                            <h4 class="room-title"><?php echo $room['RoomName']; ?></h4>
                            <p class="room-desc"><?php echo $room['RoomDesc']; ?></p>
                            <div class="room-price">₱<?php echo $room['Price']; ?></div>
                            <a href="room_details.php?room_id=<?php echo $room['ID']; ?>" class="btn-view">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No rooms found matching your search.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
