<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['hbmsaid'] == 0)) {
    header('location:logout.php');
} else {
    if (isset($_POST['blockUnblock'])) {
        $userID = $_POST['userID'];
        $isBlocked = $_POST['isBlocked'];
        $sql = "UPDATE tbluser SET isBlocked = :isBlocked WHERE ID = :userID";
        $query = $dbh->prepare($sql);
        $query->bindParam(':userID', $userID, PDO::PARAM_INT);
        $query->bindParam(':isBlocked', $isBlocked, PDO::PARAM_INT);
        $query->execute();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
        $user_id = $_POST['userID'];

        $conn = new mysqli("localhost", "root", "", "hbmsdb");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "DELETE FROM tbluser WHERE ID = $user_id";

        if ($conn->query($sql) === TRUE) {
            echo "User deleted successfully";
        } else {
            echo "Error deleting user: " . $conn->error;
        }

        $conn->close();
    }
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Hotel Booking Management System | Registered Users</title>
    <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet' type='text/css'/>
    <link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
    <script src="js/jquery-1.10.2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        .users-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-top: 20px;
        }
        .user-card {
            flex: 1 1 300px;
            margin: 10px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s;
        }
        .user-card:hover {
            transform: translateY(-5px);
        }
        .card-details {
            margin-bottom: 15px;
        }
        .card-actions {
            display: flex;
            gap: 10px;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.875em;
            color: white;
        }
        .bg-success {
            background-color: #28a745;
        }
        .bg-warning {
            background-color: #ffc107;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        .pagination-list {
            list-style: none;
            padding: 0;
            display: flex;
            gap: 5px;
        }
        .pagination-list li {
            padding: 10px 15px;
            border: 1px solid #007bff;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .pagination-list li.active {
            background: #007bff;
            color: white;
        }
        .pagination-list li.disabled {
            pointer-events: none;
            opacity: 0.5;
        }
        .pagination-list li:hover:not(.disabled) {
            background: #f8f9fa;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="left-content">
            <div class="inner-content">
                <?php include_once('includes/header.php'); ?>
                <div class="content">
                    <div class="users-container">
                        <div class="header">
                            <h4>Registered Users</h4>
                            <a href="../signup.php" class="btn btn-primary">Register New User</a>
                        </div>
                        <div class="user-cards">
                            <?php
                            if (isset($_GET['pageno'])) {
                                $pageno = $_GET['pageno'];
                            } else {
                                $pageno = 1;
                            }

                            $no_of_records_per_page = 10;
                            $offset = ($pageno - 1) * $no_of_records_per_page;

                            $query1 = $dbh->prepare("SELECT ID FROM tbluser");
                            $query1->execute();
                            $total_rows = $query1->rowCount();
                            $total_pages = ceil($total_rows / $no_of_records_per_page);

                            $sql = "SELECT * FROM tbluser LIMIT $offset, $no_of_records_per_page";
                            $query = $dbh->prepare($sql);
                            $query->execute();
                            $results = $query->fetchAll(PDO::FETCH_OBJ);

                            if ($query->rowCount() > 0) {
                                foreach ($results as $row) { ?>
                                    <div class="user-card">
                                        <div class="card-details">
                                            <h5><?php echo htmlentities($row->FullName); ?></h5>
                                            <p><strong>Email:</strong> <?php echo htmlentities($row->Email); ?></p>
                                            <p><strong>Mobile:</strong> <?php echo htmlentities($row->MobileNumber); ?></p>
                                            <p><strong>Registered on:</strong> <?php echo htmlentities($row->RegDate); ?></p>
                                            <p>
                                                <span class="badge <?php echo $row->isBlocked == 1 ? 'bg-warning' : 'bg-success'; ?>">
                                                    <?php echo $row->isBlocked == 1 ? 'Blocked' : 'Active'; ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="card-actions">
                                            <form action="" method="POST" style="display: inline-block;">
                                                <input type="hidden" name="userID" value="<?php echo htmlentities($row->ID); ?>">
                                                <input type="hidden" name="isBlocked" value="<?php echo $row->isBlocked == 1 ? 0 : 1; ?>">
                                                <button type="submit" name="blockUnblock" class="btn btn-<?php echo $row->isBlocked == 1 ? 'danger' : 'warning'; ?>">
                                                    <i class="fa <?php echo $row->isBlocked == 1 ? 'fa-user-times' : 'fa-user-check'; ?>"></i> <?php echo $row->isBlocked == 1 ? 'Unblock' : 'Block'; ?>
                                                </button>
                                            </form>
                                            <form action="" method="POST" style="display: inline-block;">
                                                <input type="hidden" name="userID" value="<?php echo htmlentities($row->ID); ?>">
                                                <button type="submit" name="delete_user" class="btn btn-danger">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php }
                            } ?>
                        </div>
                        <div class="pagination">
                            <ul class="pagination-list">
                                <li class="<?php if ($pageno <= 1) echo 'disabled'; ?>">
                                    <a href="<?php if ($pageno <= 1) echo '#'; else echo "?pageno=" . ($pageno - 1); ?>">Prev</a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                    <li class="<?php echo $pageno == $i ? 'active' : ''; ?>">
                                        <a href="?pageno=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php } ?>
                                <li class="<?php if ($pageno >= $total_pages) echo 'disabled'; ?>">
                                    <a href="<?php if ($pageno >= $total_pages) echo '#'; else echo "?pageno=" . ($pageno + 1); ?>">Next</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php include_once('includes/sidebar.php'); ?>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.nicescroll.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
