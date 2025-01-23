<?php
include('includes/dbconnection.php');
session_start();

if (strlen($_SESSION['hbmsuid']) == 0) {
    header('location:logout.php');
} else {
    $dbh = new PDO("mysql:host=localhost;dbname=hbmsdb", "root", "");
    
    // Fetch room details and price
    $rid = intval($_GET['rmid']);
    $sql = "SELECT r.RoomName, c.Price, c.CategoryName 
            FROM tblroom r 
            JOIN tblcategory c ON r.RoomType = c.ID 
            WHERE r.ID = :rid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':rid', $rid, PDO::PARAM_STR);
    $query->execute();
    $roomDetails = $query->fetch(PDO::FETCH_ASSOC);
    
    $roomPrice = $roomDetails['Price'];
    $minDownPayment = $roomPrice * 0.5;

    // Fetch food items
    $sql = "SELECT item_name, Price as price FROM foodbeveragestbl";
    $query = $dbh->prepare($sql);
    $query->execute();
    $food_items = $query->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Room - <?php echo $roomDetails['RoomName']; ?></title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.js"></script>

    <style>
        body {
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .price-summary {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .food-items {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .form-section {
            margin-bottom: 30px;
        }
        .form-control {
            margin-bottom: 15px;
        }
        .btn-review {
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            margin-top: 20px;
        }
        .btn-review:hover {
            background-color: #0056b3;
        }
        .highlight-price {
            color: #28a745;
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <?php include_once('includes/header.php'); ?>

    <div class="container">
        <h2 class="text-center mb-4">Book Room</h2>

        <div class="price-summary">
            <h3>Room Details</h3>
            <div class="price-details">
                <p><strong>Room:</strong> <?php echo $roomDetails['RoomName']; ?> (<?php echo $roomDetails['CategoryName']; ?>)</p>
                <p><strong>Price per night:</strong> <span class="highlight-price">₱<?php echo number_format($roomPrice, 2); ?></span></p>
                <p><strong>Required Down Payment (50%):</strong> <span class="highlight-price">₱<?php echo number_format($minDownPayment, 2); ?></span></p>
            </div>
        </div>

        <form id="bookingForm" method="POST" action="review-booking.php" enctype="multipart/form-data">
            <input type="hidden" name="room_id" value="<?php echo $rid; ?>">
            <input type="hidden" name="room_price" value="<?php echo $roomPrice; ?>">
            
            <div class="form-section">
                <h3>Guest Information</h3>
                <label for="address">Address:</label>
                <input type="text" name="address" id="address" class="form-control" required>

                <label for="idtype">ID Type:</label>
                <select name="idtype" id="idtype" class="form-control" required>
                    <option value="">Select ID Type</option>
                    <option value="passport">Passport</option>
                    <option value="driver_license">Driver's License</option>
                    <option value="national_id">National ID</option>
                </select>
            </div>

            <div class="form-section">
                <h3>Booking Dates</h3>
                <label for="checkindate">Check-in Date:</label>
                <input type="date" name="checkindate" id="checkindate" class="form-control" 
                       min="<?php echo date('Y-m-d'); ?>" required>

                <label for="checkoutdate">Check-out Date:</label>
                <input type="date" name="checkoutdate" id="checkoutdate" class="form-control" 
                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
            </div>

            <div class="form-section">
                <h3>Food and Beverages</h3>
                <div class="food-items">
                    <?php if (!empty($food_items)): ?>
                        <?php foreach ($food_items as $item): ?>
                            <div class="food-item">
                                <label for="food_<?php echo htmlspecialchars($item['item_name']); ?>">
                                    <?php echo htmlspecialchars($item['item_name']); ?> 
                                    (₱<?php echo number_format($item['price'], 2); ?>)
                                </label>
                                <input type="number" 
                                       name="food_beverages[<?php echo htmlspecialchars($item['item_name']); ?>]"
                                       id="food_<?php echo htmlspecialchars($item['item_name']); ?>"
                                       class="form-control"
                                       value="0" 
                                       min="0">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No food items available</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-section">
                <h3>Pool Usage</h3>
                <div class="form-check">
                    <input type="radio" name="pool_usage" value="yes" id="pool_yes" class="form-check-input">
                    <label class="form-check-label" for="pool_yes">Yes</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="pool_usage" value="no" id="pool_no" class="form-check-input" checked>
                    <label class="form-check-label" for="pool_no">No</label>
                </div>
            </div>

            <div class="form-section">
                <h3>Payment Details</h3>
                <label for="payment_mode">Payment Mode:</label>
                <select name="payment_mode" id="payment_mode" class="form-control" required>
                    <option value="">Select Payment Mode</option>
                    <option value="Gcash">Gcash</option>
                    <option value="Maya">Maya</option>
                </select>

                <label for="paymentOptions">Payment Option:</label>
                <select name="paymentOptions" id="paymentOptions" class="form-control" required>
                    <option value="">Select Payment Option</option>
                    <option value="full_payment">Full Payment</option>
                    <option value="down_payment">Down Payment</option>
                </select>

                <div id="downPaymentSection" style="display: none;">
                    <label for="downPay">Down Payment Amount:</label>
                    <input type="number" 
                           name="downPay" 
                           id="downPay" 
                           class="form-control"
                           min="<?php echo $minDownPayment; ?>" 
                           max="<?php echo $roomPrice; ?>"
                           placeholder="Minimum: ₱<?php echo number_format($minDownPayment, 2); ?>">
                </div>
            </div>

            <button type="submit" name="review" class="btn-review">Review Booking</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkinInput = document.getElementById('checkindate');
            const checkoutInput = document.getElementById('checkoutdate');
            const paymentOptions = document.getElementById('paymentOptions');
            const downPaymentSection = document.getElementById('downPaymentSection');
            const downPayInput = document.getElementById('downPay');
            const minDownPayment = <?php echo $minDownPayment; ?>;
            const roomPrice = <?php echo $roomPrice; ?>;

            // Date validation
            checkinInput.addEventListener('change', function() {
                const minCheckout = new Date(this.value);
                minCheckout.setDate(minCheckout.getDate() + 1);
                checkoutInput.min = minCheckout.toISOString().split('T')[0];
                
                if (checkoutInput.value && checkoutInput.value <= this.value) {
                    checkoutInput.value = minCheckout.toISOString().split('T')[0];
                }
            });

            // Payment option handling
            paymentOptions.addEventListener('change', function() {
                if (this.value === 'down_payment') {
                    downPaymentSection.style.display = 'block';
                    downPayInput.required = true;
                    downPayInput.value = minDownPayment;
                } else {
                    downPaymentSection.style.display = 'none';
                    downPayInput.required = false;
                    downPayInput.value = '';
                }
            });

            // Form validation
            document.getElementById('bookingForm').addEventListener('submit', function(e) {
                if (paymentOptions.value === 'down_payment') {
                    const downPayAmount = parseFloat(downPayInput.value);
                    if (downPayAmount < minDownPayment) {
                        alert(`Down payment must be at least ₱${minDownPayment.toFixed(2)}`);
                        e.preventDefault();
                    }
                    if (downPayAmount > roomPrice) {
                        alert(`Down payment cannot exceed the room price of ₱${roomPrice.toFixed(2)}`);
                        e.preventDefault();
                    }
                }
            });
        });
    </script>
</body>
</html>
