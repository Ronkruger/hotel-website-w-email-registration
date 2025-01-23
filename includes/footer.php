<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer with Custom Modals Example</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .footer-section {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
        }
        .footer-section a {
            color: white;
            text-decoration: none;
        }
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%;
            overflow: auto; 
            background-color: rgba(0,0,0,0.4);
        }
        p{
            color: black;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            max-width: 600px;
        }
        .modal-header, .modal-footer {
            padding: 10px 20px;
            background-color: #343a40;
            color: black;
        }
        .modal-header h5 {
            margin: 0;
        }
        .close {
            cursor: pointer;
        }
    </style>
</head>
<body>


    <!-- Footer Section -->
    <div class="footer-section text-center">
        <p>© Hotel <?php echo date("Y"); ?>. All Rights Reserved.</p>
        <ul class="list-inline">
            <li class="list-inline-item">
                <a href="#" onclick="openModal('privacyModal')">Privacy Policy</a>
            </li>
            <li class="list-inline-item">
                <a href="#" onclick="openModal('termsModal')">Terms of Service</a>
            </li>
            <li class="list-inline-item">
                <a href="#">Contact Us</a>
            </li>
        </ul>
    </div>

    <!-- Privacy Policy Modal -->
    <div id="privacyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeModal('privacyModal')">&times;</span>
                <h5>Privacy Policy</h5>
            </div>
            <div class="modal-body">
            <p><strong>Effective Date:</strong> [Insert Date]</p>
            <p>
                Welcome to [Hotel Name]! By accessing or using our website ([Hotel URL]), you agree to comply with these Terms of Service. Please read them carefully before using our services.
            </p>
            <h6>1. Acceptance of Terms</h6>
            <p>
                By using our website or making a reservation, you agree to be bound by these Terms of Service and our Privacy Policy. If you do not agree, please refrain from using our website.
            </p>
            <h6>2. Services Provided</h6>
            <p>
                Our website allows you to:  
                - Browse information about our hotel, amenities, and policies.  
                - Make room reservations.  
                - Request additional services (e.g., transportation, event bookings).  
                - Access promotional offers and packages.  
            </p>
            <h6>3. User Responsibilities</h6>
            <p>
                You agree to:  
                - Provide accurate and complete information when making a reservation.  
                - Not use the website for illegal or unauthorized purposes.  
                - Refrain from interfering with the operation of the website.  
            </p>
            <h6>4. Booking and Cancellation Policies</h6>
            <p>
                - <strong>Reservations:</strong> All bookings are subject to availability and confirmation.  
                - <strong>Payment:</strong> Full or partial payment may be required to secure your booking.  
                - <strong>Cancellation:</strong> Cancellations must be made according to the policy outlined during booking. Refunds, if applicable, will be processed within [insert number] days.  
            </p>
            <h6>5. Pricing and Promotions</h6>
            <p>
                All prices are subject to change without prior notice. Promotional offers may have specific terms and conditions that must be adhered to.
            </p>
            <h6>6. Intellectual Property</h6>
            <p>
                All content on this website, including but not limited to text, images, logos, and videos, is the property of [Hotel Name]. Unauthorized use, reproduction, or distribution is prohibited.
            </p>
            <h6>7. Third-Party Services</h6>
            <p>
                Our website may contain links to third-party services or websites. We are not responsible for their content, policies, or practices.
            </p>
            <h6>8. Limitation of Liability</h6>
            <p>
                [Hotel Name] is not liable for any direct, indirect, incidental, or consequential damages arising from:  
                - The use or inability to use our website.  
                - Errors, inaccuracies, or interruptions in our services.  
                - Unauthorized access to your personal data.  
            </p>
            <h6>9. Privacy</h6>
            <p>
                We value your privacy. Please refer to our Privacy Policy for details on how we collect, use, and protect your personal information.
            </p>
            <h6>10. Modification of Terms</h6>
            <p>
                We reserve the right to modify these Terms of Service at any time. Changes will be effective immediately upon posting. Continued use of the website constitutes your acceptance of the revised terms.
            </p>
            <h6>11. Governing Law</h6>
            <p>
                These Terms of Service are governed by the laws of [Your Country/State]. Any disputes will be resolved in the courts of [Your Jurisdiction].
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('privacyModal')" style="background: none; border: none; color: white;">Close</button>
            </div>
        </div>
    </div>

    <!-- Terms of Service Modal -->
    <div id="termsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeModal('termsModal')">&times;</span>
                <h5>Terms of Service</h5>
            </div>
            <div class="modal-body">
            <p><strong>Effective Date:</strong> [Insert Date]</p>
            <p>
                Welcome to [Hotel Name]! By accessing or using our website ([Hotel URL]) or our services, you agree to comply with the following Terms of Service. Please read them carefully before making any reservations or using our services.
            </p>
            <h6>1. Acceptance of Terms</h6>
            <p>
                By accessing this website or booking with us, you confirm your agreement to these Terms of Service and our Privacy Policy. If you do not accept these terms, you must refrain from using our services.
            </p>
            <h6>2. Services Offered</h6>
            <p>
                Our website provides:
                <ul>
                    <li>Information about our accommodations, amenities, and policies.</li>
                    <li>Online booking for rooms and other services.</li>
                    <li>Access to promotions and special offers.</li>
                    <li>Communication channels for additional inquiries.</li>
                </ul>
            </p>
            <h6>3. Reservations and Payments</h6>
            <p>
                <strong>Bookings:</strong> Reservations are subject to availability and confirmation.<br>
                <strong>Payments:</strong> Full or partial payment may be required to confirm bookings. Payment details will be communicated during the booking process.<br>
                <strong>Modifications and Cancellations:</strong> Cancellation and modification policies vary. Refund eligibility depends on the terms displayed at the time of booking.
            </p>
            <h6>4. User Conduct</h6>
            <p>
                You agree to:
                <ul>
                    <li>Provide accurate and complete information when booking.</li>
                    <li>Avoid using the website for unauthorized or illegal activities.</li>
                    <li>Respect hotel policies during your stay, including check-in and check-out times, safety rules, and other guidelines.</li>
                </ul>
                We reserve the right to refuse service if any of these terms are violated.
            </p>
            <h6>5. Pricing and Promotions</h6>
            <p>
                Prices displayed are subject to change without prior notice. Promotions are limited-time offers and are subject to specific terms and conditions.
            </p>
            <h6>6. Liability</h6>
            <p>
                [Hotel Name] is not responsible for:
                <ul>
                    <li>Issues caused by incorrect booking information provided by you.</li>
                    <li>Temporary unavailability of the website due to technical issues.</li>
                    <li>Loss, damage, or theft of personal belongings during your stay.</li>
                </ul>
            </p>
            <h6>7. Intellectual Property</h6>
            <p>
                All content on our website, including text, images, and branding, is the intellectual property of [Hotel Name]. Reproduction or redistribution without permission is strictly prohibited.
            </p>
            <h6>8. Third-Party Links</h6>
            <p>
                Our website may include links to third-party services or websites. We are not responsible for their content, policies, or services.
            </p>
            <h6>9. Privacy</h6>
            <p>
                We are committed to protecting your privacy. Please review our <a href="#privacyModal" onclick="openModal('privacyModal')">Privacy Policy</a> to understand how we handle your personal information.
            </p>
            <h6>10. Changes to Terms</h6>
            <p>
                We may update these Terms of Service at any time without prior notice. Continued use of the website or our services after updates constitutes acceptance of the revised terms.
            </p>
            <h6>11. Governing Law</h6>
            <p>
                These Terms of Service are governed by the laws of [Your Country/State]. Disputes will be resolved in the courts of [Your Jurisdiction].
            </p>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('termsModal')" style="background: none; border: none; color: white;">Close</button>
            </div>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target.className === "modal") {
                closeModal(event.target.id);
            }
        }
    </script>
</body>
</html>