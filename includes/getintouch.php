<div class="touch-section">
    <div class="container">
        <h3 class="section-title">Get in Touch</h3>
        <div class="touch-grids">
            <?php
            $sql = "SELECT * FROM tblpage WHERE PageType='contactus'";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);

            if ($query->rowCount() > 0) {
                foreach ($results as $row) {
            ?>
                    <div class="touch-grid">
                        <h4 class="grid-title">Address</h4>
                        <p class="grid-description"><?php echo htmlentities($row->PageDescription); ?></p>
                    </div>
                    <div class="touch-grid">	
                        <h4 class="grid-title">Sales</h4>
                        <p class="grid-description">Sales Enquiries</p>
                        <p class="grid-contact">Telephone : +<?php echo htmlentities($row->MobileNumber); ?></p>
                        <p class="grid-contact">E-mail : <a href="mailto:<?php echo htmlentities($row->Email); ?>"><?php echo htmlentities($row->Email); ?></a></p>
                    </div>
            <?php 
                }
            } 
            ?>

 
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<style>
	.touch-section {
    background-color: #f9f9f9;
    padding: 40px 0;
}

.section-title {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 30px;
}

.touch-grids {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.touch-grid {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    flex: 1 1 calc(33.333% - 20px); /* Adjust for responsiveness */
    min-width: 280px; /* Minimum width */
}

.grid-title {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #333;
}

.grid-description, .grid-contact {
    font-size: 1rem;
    line-height: 1.5;
    color: #555;
}

.grid-contact a {
    color: #007bff;
    text-decoration: none;
}

.grid-contact a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .touch-grid {
        flex: 1 1 100%;
    }
}
</style>