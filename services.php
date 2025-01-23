<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hotel Booking Management System | Hotel :: Facilities</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        .service-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            transition: box-shadow 0.3s ease;
            overflow: hidden;
        }

        .service-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .service-img {
            height: 200px;
            overflow: hidden;
        }

        .service-img img {
            width: 100%;
            height: auto;
            transition: transform 0.3s ease;
        }

        .service-card:hover .service-img img {
            transform: scale(1.05);
        }

        .service-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 15px;
        }

        .service-desc {
            color: #6c757d;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <!-- Header -->
    <div class="header head-top bg-light">
        <div class="container">
            <!-- Static Header Content -->
            <h1>Hotel Booking Management System</h1>
            <!-- Here you would include the header if needed -->
        </div>
    </div>
    <!-- Header End -->

    <!-- Services -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Facilities</h2>
        <div class="row">
            <!-- Facility 1 -->
            <div class="col-md-6 mb-4">
                <div class="service-card">
                    <div class="service-img">
                        <img src="https://via.placeholder.com/500x200?text=Swimming+Pool" alt="Swimming Pool" class="img-fluid">
                    </div>
                    <div class="p-3">
                        <h4 class="service-title">
                            <i class="fas fa-check-circle"></i> Swimming Pool
                        </h4>
                        <p class="service-desc">Enjoy a refreshing swim in our outdoor swimming pool.</p>
                    </div>
                </div>
            </div>

            <!-- Facility 2 -->
            <div class="col-md-6 mb-4">
                <div class="service-card">
                    <div class="service-img">
                        <img src="https://via.placeholder.com/500x200?text=Spa+Services" alt="Spa Services" class="img-fluid">
                    </div>
                    <div class="p-3">
                        <h4 class="service-title">
                            <i class="fas fa-check-circle"></i> Spa Services
                        </h4>
                        <p class="service-desc">Pamper yourself with our relaxing spa services.</p>
                    </div>
                </div>
            </div>

            <!-- Facility 3 -->
            <div class="col-md-6 mb-4">
                <div class="service-card">
                    <div class="service-img">
                        <img src="https://via.placeholder.com/500x200?text=Gym" alt="Gym" class="img-fluid">
                    </div>
                    <div class="p-3">
                        <h4 class="service-title">
                            <i class="fas fa-check-circle"></i> Gym
                        </h4>
                        <p class="service-desc">Stay fit with our state-of-the-art gym equipment.</p>
                    </div>
                </div>
            </div>

            <!-- Facility 4 -->
            <div class="col-md-6 mb-4">
                <div class="service-card">
                    <div class="service-img">
                        <img src="https://via.placeholder.com/500x200?text=Restaurant" alt="Restaurant" class="img-fluid">
                    </div>
                    <div class="p-3">
                        <h4 class="service-title">
                            <i class="fas fa-check-circle"></i> Restaurant
                        </h4>
                        <p class="service-desc">Savor delicious meals at our fine dining restaurant.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Services End -->

    <!-- Get In Touch Section -->
    <div class="container mt-5">
        <h3 class="text-center">Get in Touch</h3>
        <p class="text-center">For inquiries and reservations, contact us at:</p>
        <p class="text-center">Email: <a href="mailto:info@example.com">info@example.com</a></p>
        <p class="text-center">Phone: +1234567890</p>
    </div>


</body>


    <!-- Footer -->
    <?php include_once('includes/footer.php'); ?>


</html>