<div class="sidebar-menu">
    <header class="logo1">
        <span class="brand-title">ADMIN DASHBOARD</span>
        <a href="#" class="sidebar-icon"><span class="fa fa-bars"></span></a>
    </header>
    <div class="menu">
        <ul id="menu">
            <li><a href="dashboard.php"><i class="fa fa-tachometer"></i> <span>Dashboard</span></a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle"><i class="fa fa-table"></i> <span>Room Category</span> <span class="arrow fa fa-angle-right"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="add-category.php">Add Category</a></li>
                    <li><a href="manage-category.php">Manage Category</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle"><i class="lnr lnr-pencil"></i> <span>Rooms</span> <span class="arrow fa fa-angle-right"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="add-room.php">Add Room</a></li>
                    <li><a href="manage-room.php">Manage Room</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle"><i class="lnr lnr-pencil"></i> <span>Food and Beverages</span> <span class="arrow fa fa-angle-right"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="manage-food-beverages.php">Manage Food and Beverages</a></li>
                   
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle"><i class="fa fa-file-text-o"></i> <span>Page</span> <span class="arrow fa fa-angle-right"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="../admin/add-facility.php" id="background-color-link5">Add Facility</a></li>
                    <li><a href="../admin/manage-facility.php" id="background-color-link4">Manage Facilities</a></li>
                    <li><a href="../admin/aboutus.php" id="background-color-link3">About Us Page</a></li>
                    <li><a href="../admin/admin_settings.php" id="background-color-link">Edit Home Page</a></li>
                    <li><a href="../admin/slideshows.php" id="background-color-link2">Add/Edit Slideshow Images</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle"><i class="lnr lnr-book"></i> <span>Booking</span> <span class="arrow fa fa-angle-right"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="all-booking.php">All Booking</a></li>
                    <li><a href="new-booking.php">Pending Booking</a></li>
                    <li><a href="approved-booking.php">Approved Booking</a></li>
                    <li><a href="cancelled-booking.php">Cancelled Booking</a></li>
                </ul>
            </li>
            <li><a href="reg-users.php"><i class="lnr lnr-users"></i> <span>Users</span></a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle"><i class="lnr lnr-book"></i> <span>Enquiry</span> <span class="arrow fa fa-angle-right"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="read-enquiry.php">Read Enquiry</a></li>
                    <li><a href="unread-enquiry.php">Unread Enquiry</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle"><i class="fa fa-file-text-o"></i> <span>Search</span> <span class="arrow fa fa-angle-right"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="search-enquiry.php">Search Enquiry</a></li>
                    <li><a href="search-booking.php">Search Booking</a></li>
                    <li><a href="payment-history.php">Payment History</a></li>
                    <li><a href="sales-summary.php">Sales Summary</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle"><i class="lnr lnr-layers"></i> <span>Reports</span> <span class="arrow fa fa-angle-right"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="enquiry-betdates-reports.php">Enquiry B/W Reports</a></li>
                    <li><a href="booking-betdates-reports.php">Booking B/W Reports</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle"><i class="fa fa-walking"></i> <span>Walk-in</span> <span class="arrow fa fa-angle-right"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="walk_in_reservation.php">Walk-in Booking</a></li>
                    <li><a href="view_walkin_reservations.php">Walk-in Reservations</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- Modal HTML -->
<div id="passwordModal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Password Required</h2>
        <input type="password" id="modalPassword" placeholder="Enter password">
        <button id="modalSubmit">Submit</button>
    </div>
</div>
<style>
    .sidebar-menu {
        width: 250px;
        background: #343a40;
        color: #ffffff;
        height: 100vh;
        position: fixed;
        transition: width 0.3s ease-in-out;
    }

    .logo1 {
        padding: 15px;
        background: #1d2124;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 1.2em;
    }

    .brand-title {
        color: #ffffff;
        font-weight: bold;
    }

    .menu {
        padding: 10px 15px;
    }

    #menu {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    #menu li {
        position: relative;
    }

    #menu li a {
        display: flex;
        align-items: center;
        padding: 10px;
        color: #ffffff;
        text-decoration: none;
        transition: background 0.3s;
        border-radius: 5px;
    }

    #menu li a:hover {
        background: #495057;
    }

    .arrow {
        margin-left: auto;
    }

    .dropdown-menu {
        display: none;
        list-style-type: none;
        padding: 0;
        margin: 0;
        background: #495057;
        border-radius: 5px;
        position: absolute;
        left: 100%;
        top: 0;
        width: 200px;
        z-index: 1;
        transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
    }

    #menu li.dropdown:hover > .dropdown-menu,
    .dropdown-menu:hover {
        display: block;
        opacity: 1;
        visibility: visible;
    }

    .dropdown-menu li a {
        padding: 8px 15px;
        white-space: nowrap;
    }

    .dropdown-menu li a:hover {
        background: #6c757d;
    }
    #passwordModal {
    display: none; /* Hidden by default */
    position: fixed; /* Stays in place */
    z-index: 1000; /* Sits on top of other elements */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    background-color: rgba(0, 0, 0, 0.4); /* Black background with transparency */
    display: flex; /* Flexbox for centering */
    justify-content: center; /* Center horizontally */
    align-items: center; /* Center vertically */
}

.modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Slight shadow for depth */
    width: 300px; /* Width of the modal */
    text-align: center; /* Center text inside the modal */
    position: relative; /* For positioning the close button */
    top: 12rem;
    left: 35rem;
    gap: 10px;
}

</style>
<script>
        document.addEventListener('DOMContentLoaded', function(){
            var modal = document.getElementById("passwordModal");
            var span = document.getElementsByClassName("close")[0];
            var modalPassword = document.getElementById("modalPassword");
            var modalSubmit = document.getElementById("modalSubmit");

            function handlePasswordPrompt(linkId, redirectUrl) {
                var link = document.getElementById(linkId);
                link.addEventListener('click', function(e){
                    e.preventDefault();
                    modal.style.display = "block";
                    modalPassword.value = '';
                    modalSubmit.onclick = function() {
                        if (modalPassword.value === 'hoteladmin') {
                            window.location.href = redirectUrl;
                        } else {
                            alert('Incorrect Password.');
                        }
                        modal.style.display = "none";
                    }
                });
            }

            span.onclick = function() {
                modal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            handlePasswordPrompt('background-color-link', '../admin/admin_settings.php');
            handlePasswordPrompt('background-color-link2', '../admin/slideshows.php');
            handlePasswordPrompt('background-color-link3', '../admin/aboutus.php');
            handlePasswordPrompt('background-color-link4', '../admin/manage-facility.php');
            handlePasswordPrompt('background-color-link5', '../admin/add-facility.php');
            // Add asterisks to required fields
            var requiredFields = document.querySelectorAll('#booking-form label');
            requiredFields.forEach(function(label) {
                var input = document.querySelector(`#${label.getAttribute('for')}`);
                if (input && input.hasAttribute('required')) {
                    label.innerHTML += ' *';
                }
            });
        });
    </script>
