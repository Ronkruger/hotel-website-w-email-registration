<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/dbconnection.php');

try {
    $sql = "SELECT tblroom.*, tblcategory.Price 
            FROM tblroom 
            LEFT JOIN tblcategory ON tblroom.RoomType = tblcategory.ID";
    $stmt = $dbh->query($sql);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking Management System | Rooms</title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all">
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #28a745;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--light-color);
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        header {
            background-color: var(--primary-color);
            color: white;
            text-align: center;
            padding: 20px;
        }

        header h1 {
            font-size: 2.5em;
            margin: 0;
        }
  
@media (max-width: 768px) {
    .hotel-room-card {
        max-width: calc(50% - 20px); /* Change width to 50% for smaller screens */
    }
}

@media (max-width: 576px) {
    .hotel-room-card {
        max-width: calc(100% - 20px); /* Change width to 100% for mobile screens */
    }
}     .hotel-room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .hotel-room-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .hotel-room-info {
            padding: 20px;
            flex-grow: 1; /* Allow info section to grow and take available space */
        }

        .hotel-room-info h4 {
            font-size: 1.4em;
            color: var(--dark-color);
            margin-bottom: 10px;
        }

        .hotel-room-description {
            font-size: 1em;
            color: var(--secondary-color);
            margin-bottom: 15px;
        }

        .hotel-room-price {
            font-size: 1.2em;
            color: var(--success-color);
            margin-bottom: 10px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1em;
            text-decoration: none;
            transition: background-color 0.3s;
            align-self: flex-start; /* Align the button at the start */
        }

        .btn-primary:hover {
            background-color: darken(var(--primary-color), 10%);
        }

        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        footer a {
            color: #fff;
            text-decoration: none;
            padding: 5px;
        }

        footer a:hover {
            color: var(--primary-color);
        }
        
@media (max-width: 768px) {
    .hotel-room-card {
        flex: 1 0 calc(50% - 10px); /* Change width to 50% minus the gap for smaller screens */
    }
}

@media (max-width: 576px) {
    .hotel-room-card {
        flex: 1 0 calc(100% - 10px); /* Change width to 100% minus the gap for mobile screens */
    }
}
    </style>
</head>
<body>

<?php include_once('includes/header.php'); ?>       

<main class="container">
    <div class="hotel-room-container">
        <?php if (!empty($rooms)): ?>
            <?php foreach ($rooms as $row): ?>
                <div class="hotel-room-card">
                    <img src="admin/images/<?= htmlspecialchars($row['Image']) ?>" 
                         alt="<?= htmlspecialchars($row['RoomName']) ?> Image" 
                         class="hotel-room-image">
                    <div class="hotel-room-info">
                        <p><?= htmlspecialchars($row['RoomName']) ?></p>
                    
                        <div class="hotel-room-price">₱<?= htmlspecialchars($row['Price']) ?> per night</div>
                        <a href="view-category.php?rmid=<?= htmlspecialchars($row['ID']) ?>" 
                           class="btn-primary">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No rooms available.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    <?php include_once('includes/footer.php'); ?>
</footer>

<script type="text/javascript">
    $(function () {
        // Your JavaScript functionality can go here
    });
</script>



</body>
</html>