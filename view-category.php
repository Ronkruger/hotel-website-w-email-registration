<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/dbconnection.php');

function formatPrice($price) {
    return '₱' . number_format($price, 2);
}

function getRoomStatus($dbh, $rmid) {
    $sql = "SELECT COUNT(*) as booked
            FROM tblbooking
            WHERE RoomId = :rmid
            AND (Status = 'Approved' OR Status IS NULL)
            AND CheckinDate <= CURRENT_DATE
            AND CheckoutDate >= CURRENT_DATE";
   
    $query = $dbh->prepare($sql);
    $query->bindParam(':rmid', $rmid, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
   
    return $result['booked'] > 0 ? 'Booked' : 'Available';
}

function isRoomBookedForDates($dbh, $rmid, $checkin, $checkout) {
    $sql = "SELECT COUNT(*) as booked
            FROM tblbooking
            WHERE RoomId = :rmid
            AND (Status = 'Approved' OR Status IS NULL)
            AND (
                (CheckinDate <= :checkout AND CheckoutDate >= :checkin)
                OR (CheckinDate BETWEEN :checkin AND :checkout)
                OR (CheckoutDate BETWEEN :checkin AND :checkout)
            )";
   
    $query = $dbh->prepare($sql);
    $query->bindParam(':rmid', $rmid, PDO::PARAM_INT);
    $query->bindParam(':checkin', $checkin, PDO::PARAM_STR);
    $query->bindParam(':checkout', $checkout, PDO::PARAM_STR);
    $query->execute();
   
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result['booked'] > 0;
}

if (isset($_GET['rmid'])) {
    $rmid = intval($_GET['rmid']);
   
    try {
        $sql = "SELECT r.*, c.CategoryName, c.Price, c.DownPayment,
                (SELECT COUNT(*) FROM tblbooking WHERE RoomId = r.ID AND Status = 'Approved') as total_bookings
                FROM tblroom r
                LEFT JOIN tblcategory c ON c.ID = r.RoomType
                WHERE r.ID = :rmid";
       
        $query = $dbh->prepare($sql);
        $query->bindParam(':rmid', $rmid, PDO::PARAM_INT);
        $query->execute();
        $room = $query->fetch(PDO::FETCH_OBJ);

        $sql = "SELECT BookingNumber, CheckinDate, CheckoutDate, Status
                FROM tblbooking
                WHERE RoomId = :rmid
                AND CheckoutDate >= CURRENT_DATE
                AND (Status = 'Approved' OR Status IS NULL)
                ORDER BY CheckinDate ASC
                LIMIT 10";
       
        $query = $dbh->prepare($sql);
        $query->bindParam(':rmid', $rmid, PDO::PARAM_INT);
        $query->execute();
        $upcomingBookings = $query->fetchAll(PDO::FETCH_ASSOC);

        $events = array();
        foreach ($upcomingBookings as $booking) {
            $events[] = array(
                'title' => $booking['BookingNumber'],
                'start' => date('Y-m-d', strtotime($booking['CheckinDate'])),
                'end' => date('Y-m-d', strtotime($booking['CheckoutDate'] . ' +1 day')),
                'allDay' => true,
                'className' => $booking['Status'] == 'Approved' ? 'approved' : 'pending',
                'extendedProps' => array(
                    'status' => $booking['Status'] ?: 'Pending',
                    'bookingNumber' => $booking['BookingNumber']
                )
            );
        }

    } catch(PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        echo "An error occurred. Please try again later.";
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>HBMS | <?php echo htmlentities($room->RoomName); ?></title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

    <style>
        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .room-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .status-available {
            background: #d4edda;
            color: #155724;
        }

        .status-booked {
            background: #f8d7da;
            color: #721c24;
        }

        .price-details {
            background: #e9ecef;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }

        .booking-timeline {
            margin: 20px 0;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .timeline-item {
            padding: 10px;
            margin: 5px 0;
            border-left: 3px solid #007bff;
            background: #f8f9fa;
        }

        .date-selection-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .booking-section {
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .btn-danger:disabled {
            background-color: #dc3545;
            border-color: #dc3545;
            opacity: 1;
        }

        .alert-info {
            background-color: #e3f2fd;
            border-color: #bee5eb;
            color: #0c5460;
        }

        .calendar-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .fc-event {
            cursor: pointer;
            padding: 2px 5px;
        }

        .fc-event.approved {
            background-color: #28a745;
            border-color: #28a745;
        }

        .fc-event.pending {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .book-now-section {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .book-now-btn {
            padding: 15px 40px;
            font-size: 1.2em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>
    <?php include_once('includes/header.php'); ?>

    <div class="container">
        <div class="room-info">
            <div class="room-status <?php echo getRoomStatus($dbh, $rmid) == 'Available' ? 'status-available' : 'status-booked'; ?>">
                <?php echo getRoomStatus($dbh, $rmid); ?>
            </div>

            <img src="admin/images/<?php echo htmlentities($room->Image); ?>"
                 alt="<?php echo htmlentities($room->RoomName); ?>"
                 style="max-width: 100%; height: auto; border-radius: 8px;">

            <h2><?php echo htmlentities($room->RoomName); ?></h2>

            <!-- Book Now Button Section -->
            <div class="book-now-section">
                <?php if (isset($_SESSION['login'])): ?>
                    <a href="book-room.php?rmid=<?php echo $rmid; ?>" class="btn btn-primary btn-lg book-now-btn">
                        Book Now
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-lg book-now-btn">
                        Login to Book
                    </a>
                <?php endif; ?>
            </div>
           
            <div class="price-details">
                <h4>Price Details</h4>
                <p>Room Rate: <?php echo formatPrice($room->Price); ?> per night</p>
                <p>Down Payment Required: <?php echo formatPrice($room->DownPayment); ?></p>
            </div>

            <div class="room-description">
                <h4>Room Description</h4>
                <p><?php echo htmlentities($room->RoomDesc); ?></p>
            </div>

            <div class="booking-section">
                <?php
                $selectedCheckin = isset($_GET['checkin']) ? $_GET['checkin'] : '';
                $selectedCheckout = isset($_GET['checkout']) ? $_GET['checkout'] : '';
               
                if ($selectedCheckin && $selectedCheckout) {
                    $isBooked = isRoomBookedForDates($dbh, $rmid, $selectedCheckin, $selectedCheckout);
                   
                    if ($isBooked) {
                        echo '<button class="btn btn-danger btn-lg" disabled>
                                Unavailable for Selected Dates
                              </button>';
                    } else {
                        echo '<a href="book-room.php?rmid=' . $rmid . '&checkin=' . $selectedCheckin . '&checkout=' . $selectedCheckout . '"
                                class="btn btn-primary btn-lg">
                                Book Now
                              </a>';
                    }
                } else {
                    echo '<div class="alert alert-info">
                            Please select check-in and check-out dates to check availability
                          </div>';
                }
                ?>
               
                <form class="date-selection-form mt-3">
                    <input type="hidden" name="rmid" value="<?php echo $rmid; ?>">
                    <div class="row">
                        <div class="col-md-5">
                            <label>Check-in Date:</label>
                            <input type="date" name="checkin" class="form-control"
                                   min="<?php echo date('Y-m-d'); ?>"
                                   value="<?php echo $selectedCheckin; ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label>Check-out Date:</label>
                            <input type="date" name="checkout" class="form-control"
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                   value="<?php echo $selectedCheckout; ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-info btn-block">Check Availability</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="booking-timeline">
                <h4>Upcoming Bookings</h4>
                <?php if (!empty($upcomingBookings)): ?>
                    <?php foreach ($upcomingBookings as $booking): ?>
                        <div class="timeline-item">
                            <strong>
                                <?php echo date('M d, Y', strtotime($booking['CheckinDate'])); ?> -
                                <?php echo date('M d, Y', strtotime($booking['CheckoutDate'])); ?>
                            </strong>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No upcoming bookings</p>
                <?php endif; ?>
            </div>

            <div class="calendar-container">
                <div id="calendar"></div>
            </div>
        </div>
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

        var calendarEl = document.getElementById('calendar');
        var events = <?php echo json_encode($events); ?>;
       
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: 'today'
            },
            events: events,
            eventDidMount: function(info) {
                info.el.classList.add(info.event.extendedProps.status.toLowerCase());
            }
        });
       
        calendar.render();
    });
    </script>
</body>
</html>
