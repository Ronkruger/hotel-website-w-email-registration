<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

// Database connection
$conn = new mysqli("localhost", "root", "", "hbmsdb");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch settings
$sql = "SELECT * FROM tblsettings WHERE id=1";
$result = $conn->query($sql);

// Default values
$default_logo = "default_logo.png";
$default_bg_image = "default_bg.jpg";
$default_bg_color = "#ffffff";
$default_logo_text = "Phantom Hive";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $logo = $row['logo'] ? "uploads/" . $row['logo'] : $default_logo;
    $logo_text = $row['logo'] ? "" : $default_logo_text;
    $background_image = $row['background_image'] ? "uploads/" . $row['background_image'] : $default_bg_image;
    $background_color = $row['background_color'] ? $row['background_color'] : $default_bg_color;
} else {
    $logo = $default_logo;
    $logo_text = $default_logo_text;
    $background_image = $default_bg_image;
    $background_color = $default_bg_color;
}

$get_font_color_sql = "SELECT font_color FROM tblsettings WHERE id = 1";
$get_font_color_result = $conn->query($get_font_color_sql);

if ($get_font_color_result->num_rows > 0) {
    $row = $get_font_color_result->fetch_assoc();
    $font_color = $row['font_color'];
} else {
    $font_color = '#000000';
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Hotel Booking Management System | Home</title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: <?php echo $background_color; ?>;
            color: <?php echo $font_color; ?>;
            margin: 0;
            padding: 0;
        }
        .header {
            background-image: url('<?php echo $background_image; ?>');
            background-color: <?php echo $background_color; ?>;
            color: <?php echo $font_color; ?>;
            text-align: center;
            padding: 50px 0;
        }
        .search-section {
            background: linear-gradient(rgba(255,255,255,0.9), rgba(255,255,255,0.9));
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin: 20px 0;
            padding: 30px 0;
        }
        .search-box {
            padding: 20px;
        }
        .search-form .form-group {
            margin-bottom: 15px;
        }
        .search-form label {
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
        }
        .search-form .form-control {
            height: 45px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .search-form .btn-primary {
            height: 45px;
            margin-top: 24px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .category-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }
        .category-card:hover {
            transform: translateY(-5px);
        }
        .room-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }
        .room-card:hover {
            transform: translateY(-5px);
        }
        .room-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .room-details {
            padding: 15px;
        }
        .amenities {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 0.9em;
            color: #666;
        }
        .price {
            font-size: 1.2em;
            color: #28a745;
            font-weight: bold;
            margin: 10px 0;
        }
        .categories-section, .rooms-section {
            padding: 40px 0;
            background: #f8f9fa;
        }
        .categories-section h3, .rooms-section h3 {
            margin-bottom: 30px;
            text-align: center;
        }
        .clear {
            height: 180px;
        }
        .feature-grid {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include_once('includes/header.php'); ?>
        <div class="clear"></div>
        <h1>Welcome to Our Hotel</h1>
        <button class="btn btn-primary" id="explore">
            <a class="explore" style="color: white; text-decoration: none;" href="room.php">Explore Our Rooms</a>
        </button>
    </div>

    <div class="search-section">
        <div class="container">
            <div class="search-box">
                <h3 class="text-center mb-4">Find Your Perfect Room</h3>
                <form action="search-rooms.php" method="GET" class="search-form">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Check-in Date</label>
                                <input type="date" name="checkin" class="form-control" 
                                       min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Check-out Date</label>
                                <input type="date" name="checkout" class="form-control" 
                                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Adults</label>
                                <select name="adults" class="form-control">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Children</label>
                                <select name="children" class="form-control">
                                    <?php for($i = 0; $i <= 4; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include_once('gallery.php'); ?>

    <!-- Categories Section -->
    <div class="categories-section">
        <div class="container">
            <h3>Room Categories</h3>
            <div class="row">
                <?php
                $sql = "SELECT * FROM tblcategory";
                $query = $dbh->prepare($sql);
                $query->execute();
                $categories = $query->fetchAll(PDO::FETCH_OBJ);
                
                foreach($categories as $category): ?>
                    <div class="col-md-4">
                        <div class="category-card">
                            <h4><?php echo htmlentities($category->CategoryName); ?></h4>
                            <div class="price">From ₱<?php echo number_format($category->Price, 2); ?>/night</div>
                            <a href="category-rooms.php?cat_id=<?php echo $category->ID; ?>" 
                               class="btn btn-outline-primary">View Rooms</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Rooms Section -->
    <div class="rooms-section">
        <div class="container">
            <h3>Featured Rooms</h3>
            <div class="row">
                <?php
                $sql = "SELECT r.*, c.CategoryName, c.Price 
                        FROM tblroom r 
                        LEFT JOIN tblcategory c ON r.RoomType = c.ID 
                        ORDER BY RAND() LIMIT 6";
                $query = $dbh->prepare($sql);
                $query->execute();
                $rooms = $query->fetchAll(PDO::FETCH_OBJ);
                
                foreach($rooms as $room): ?>
                    <div class="col-md-4">
                        <div class="room-card">
                            <div class="room-image">
                                <img src="admin/images/<?php echo htmlentities($room->Image); ?>" 
                                     alt="<?php echo htmlentities($room->RoomName); ?>">
                            </div>
                            <div class="room-details">
                                <h4><?php echo htmlentities($room->RoomName); ?></h4>
                                <p class="category"><?php echo htmlentities($room->CategoryName); ?></p>
                                <div class="amenities">
                                    <span><i class="fa fa-user"></i> <?php echo $room->MaxAdult; ?> Adults</span>
                                    <span><i class="fa fa-child"></i> <?php echo $room->MaxChild; ?> Children</span>
                                    <span><i class="fa fa-bed"></i> <?php echo $room->NoofBed; ?> Beds</span>
                                </div>
                                <div class="price">₱<?php echo number_format($room->Price, 2); ?>/night</div>
                                <a href="view-category.php?rmid=<?php echo $room->ID; ?>" 
                                   class="btn btn-primary btn-block">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <div class="content">
        <div class="features">
            <div class="container">
                <h3>Our Services</h3>
                <div class="row">
                    <?php
                    $sql = "SELECT * FROM tblfacility ORDER BY RAND() LIMIT 4";
                    $query = $dbh->prepare($sql);
                    $query->execute();
                    $results = $query->fetchAll(PDO::FETCH_OBJ);

                    if ($query->rowCount() > 0) {
                        foreach ($results as $row) { ?>
                            <div class="col-md-3 feature-grid">
                                <div class="feature text-center">
                                    <div class="feature1">
                                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                                        <h4><?php echo htmlentities($row->FacilityTitle); ?></h4>
                                    </div>
                                    <div class="feature2">
                                        <p><?php echo htmlentities($row->Description); ?></p>
                                    </div>
                                </div>
                            </div>
                    <?php }
                    } ?>
                </div>
            </div>
        </div>
    </div>

    <?php include_once('includes/getintouch.php'); ?>
    
    <div class="footer">
        <?php include_once('includes/footer.php'); ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkinInput = document.querySelector('input[name="checkin"]');
        const checkoutInput = document.querySelector('input[name="checkout"]');
        
        checkinInput.addEventListener('change', function() {
            const minCheckout = new Date(this.value);
            minCheckout.setDate(minCheckout.getDate() + 1);
            checkoutInput.min = minCheckout.toISOString().split('T')[0];
            
            if (checkoutInput.value && checkoutInput.value <= this.value) {
                checkoutInput.value = minCheckout.toISOString().split('T')[0];
            }
        });
    });
    </script>
</body>
</html>
