<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

$conn = new mysqli("localhost", "root", "", "hbmsdb");

// Fetch font color from tblsettings
$get_font_color_sql = "SELECT font_color FROM tblsettings WHERE id = 1";
$get_font_color_result = $conn->query($get_font_color_sql);

if ($get_font_color_result->num_rows > 0) {
    $row = $get_font_color_result->fetch_assoc();
    $font_color = $row['font_color'];
} else {
    $font_color = '#000000'; // Default black color
}

// Function to check room availability
function isRoomAvailable($roomId, $dbh) {
    $sql = "SELECT COUNT(*) as booking_count
            FROM tblbooking
            WHERE RoomId = :roomId
            AND (Status IS NULL OR Status = 'Approved')
            AND (
                (CheckinDate <= CURRENT_DATE() AND CheckoutDate >= CURRENT_DATE())
                OR
                (CheckinDate = CURRENT_DATE())
            )";

    try {
        $query = $dbh->prepare($sql);
        $query->bindParam(':roomId', $roomId, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['booking_count'] == 0;
    } catch (PDOException $e) {
        error_log("Error checking room availability: " . $e->getMessage());
        return false;
    }
}

// Function to get next available date
function getNextAvailableDate($roomId, $dbh) {
    $sql = "SELECT MIN(CheckoutDate) as next_available
            FROM tblbooking
            WHERE RoomId = :roomId
            AND (Status IS NULL OR Status = 'Approved')
            AND CheckoutDate >= CURRENT_DATE()";
           
    try {
        $query = $dbh->prepare($sql);
        $query->bindParam(':roomId', $roomId, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['next_available'];
    } catch (PDOException $e) {
        error_log("Error fetching next available date: " . $e->getMessage());
        return null;
    }
}

// Function to get booking dates
function getBookingDates($roomId, $dbh) {
    $sql = "SELECT CheckinDate, CheckoutDate, Status
            FROM tblbooking
            WHERE RoomId = :roomId
            AND (Status IS NULL OR Status = 'Approved')
            AND CheckoutDate >= CURRENT_DATE()
            ORDER BY CheckinDate ASC";
           
    try {
        $query = $dbh->prepare($sql);
        $query->bindParam(':roomId', $roomId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching booking dates: " . $e->getMessage());
        return array();
    }
}
?>

<!DOCTYPE HTML>
<html>
<head>
<title>Hotel Booking Management System | Hotel :: Single Rooms</title>
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" href="css/lightbox.css">

<script type="application/x-javascript"> 
    addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); 
    function hideURLbar(){ window.scrollTo(0,1); } 
</script>
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/responsiveslides.min.js"></script>
<script>
    $(function () {
      $("#slider").responsiveSlides({
        auto: true,
        nav: true,
        speed: 500,
        namespace: "callbacks",
        pager: true,
      });
    });
</script>

<style>
    * {
        font-family: Arial, sans-serif;
        color: <?php echo $font_color; ?>;
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
        padding-bottom: 15px;
    }

    .room-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        position: relative;
    }

    .status-tag {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 8px;
        color: white;
        font-weight: bold;
        text-align: center;
    }

    .status-available {
        background-color: #28a745;
    }

    .status-booked {
        background-color: #dc3545;
    }

    .room-info {
        padding: 15px;
    }

    .room-title {
        font-size: 18px;
        margin: 0 0 10px 0;
        color: #333;
    }

    .room-price {
        color: #666;
        font-size: 16px;
        margin-bottom: 15px;
    }

    .current-booking {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 8px;
        margin-bottom: 10px;
        border-radius: 4px;
    }

    .future-bookings {
        background-color: #f8f9fa;
        border-left: 4px solid #6c757d;
        padding: 8px;
        border-radius: 4px;
    }

    .booking-date {
        padding: 4px 0;
        font-size: 14px;
    }

    .booking-date.current {
        color: #856404;
        font-weight: 500;
    }

    .booking-date.upcoming {
        color: #721c24;
    }

    .bookings-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 12px;
        color: #495057;
    }

    .upcoming-bookings {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 15px;
        margin: 15px;
    }

    .upcoming-bookings strong {
        display: block;
        margin-bottom: 5px;
        color: #495057;
    }

    .view-details-btn {
        display: block;
        margin: 0 15px;
        padding: 10px;
        background-color: #28a745;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        text-align: center;
        transition: background-color 0.2s;
    }

    .view-details-btn:hover {
        background-color: #218838;
        color: white;
        text-decoration: none;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: not-allowed;
        width: calc(100% - 30px);
        margin: 0 15px;
    }

    .btn-danger:disabled {
        opacity: 1;
    }
</style>
</head>

<body>
    <div class="header head-top">
        <div class="container">
            <?php include_once('includes/header.php'); ?>
        </div>
    </div>

    <div class="content">
        <div class="room-section">
            <div class="container">
                <?php
                $cid = intval($_GET['catid']);
                $sql = "SELECT tblcategory.CategoryName
                        FROM tblcategory
                        WHERE tblcategory.ID = :cid";

                $query = $dbh->prepare($sql);
                $query->bindParam(':cid', $cid, PDO::PARAM_INT);
                $query->execute();
                $result = $query->fetch(PDO::FETCH_OBJ);

                if ($result) {
                    echo "<h1>Category Name: " . htmlentities($result->CategoryName) . "</h1>";
                } else {
                    echo "<h1>No category found.</h1>";
                }
                ?>

                <div class="room-grid">
                    <?php
                    date_default_timezone_set('Asia/Manila');
                    $currentDate = date('Y-m-d');

                    $sql = "SELECT r.*, c.Price FROM tblroom r
                           LEFT JOIN tblcategory c ON r.RoomType = c.ID
                           WHERE r.RoomType=:cid";

                    $query = $dbh->prepare($sql);
                    $query->bindParam(':cid', $cid, PDO::PARAM_INT);
                    $query->execute();
                    $results = $query->fetchAll(PDO::FETCH_OBJ);

                    if ($query->rowCount() > 0) {
                        foreach ($results as $row) {
                            // Get room bookings
                            $bookings = getBookingDates($row->ID, $dbh);
                            $isAvailable = isRoomAvailable($row->ID, $dbh);
                            $nextAvailable = getNextAvailableDate($row->ID, $dbh);
                            ?>

                            <div class="room-card">
                                <div style="position: relative;">
                                    <img src="admin/images/<?php echo htmlentities($row->Image); ?>" 
                                         alt="<?php echo htmlentities($row->RoomName); ?>" 
                                         class="room-image">
                                    
                                    <div class="status-tag <?php echo $isAvailable ? 'status-available' : 'status-booked'; ?>">
                                        <?php
                                        if ($isAvailable) {
                                            echo 'Available Now';
                                        } else {
                                            echo 'Booked Until ' . date('M d, Y', strtotime($nextAvailable));
                                        }
                                        ?>
                                    </div>
                                </div>

                                <div class="room-info">
                                    <h3 class="room-title"><?php echo htmlentities($row->RoomName); ?></h3>
                                    <div class="room-price">Price: ₱<?php echo htmlentities($row->Price); ?></div>
                                </div>

                                <?php if (!empty($bookings)): ?>
                                    <div class="upcoming-bookings">
                                        <h4 class="bookings-title">Booking Schedule:</h4>
                                        <?php foreach ($bookings as $booking): ?>
                                            <div class="booking-date">
                                                <?php 
                                                echo date('M d', strtotime($booking['CheckinDate'])) . 
                                                     ' - ' . 
                                                     date('M d, Y', strtotime($booking['CheckoutDate']));
                                                ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!$isAvailable): ?>
                                    <button class="btn btn-danger" disabled>
                                        Booked Until <?php echo date('M d, Y', strtotime($nextAvailable)); ?>
                                    </button>
                                <?php else: ?>
                                    <a href="view-category.php?rmid=<?php echo htmlentities($row->ID); ?>" 
                                       class="view-details-btn">
                                        View Details
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php }
                    } ?>
                </div>
            </div>
        </div>
    </div>

    <?php include_once('includes/footer.php'); ?>
</body>
</html>
