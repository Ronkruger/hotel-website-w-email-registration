<?php
session_start();
include('../includes/dbconnection.php');

// Check if the user is an admin
if (!isset($_SESSION['hbmsaid'])) {
    header('location:logout.php');
    exit;
}

// Handle adding a new food/beverage
if (isset($_POST['add'])) {
    $item_name = $_POST['item_name'];
    $item_type = $_POST['item_type'];
    $price = $_POST['price'];

    // Handle image upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../uploads/"; // Ensure this directory exists and is writable
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = basename($_FILES["image"]["name"]); // Store the image name
            } else {
                echo "<div class='alert alert-danger'>Sorry, there was an error uploading your file.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>File is not an image.</div>";
        }
    }

    // Insert into the database
    $sql = "INSERT INTO foodbeveragestbl (item_name, item_type, price, image_path) VALUES (:item_name, :item_type, :price, :image_path)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':item_name', $item_name);
    $query->bindParam(':item_type', $item_type);
    $query->bindParam(':price', $price);
    $query->bindParam(':image_path', $image_path);
    $query->execute();
}

// Handle deleting a food/beverage
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $sql = "DELETE FROM foodbeveragestbl WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id);
    $query->execute();
}

// Fetch all food and beverages
$sql = "SELECT * FROM foodbeveragestbl";
$query = $dbh->prepare($sql);
$query->execute();
$items = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Food and Beverages</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="../css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="container">
        <h2 class="text-center">Manage Food and Beverages</h2>

        <!-- Add Food/Beverage Form -->
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="item_name">Item Name:</label>
                <input type="text" class="form-control" id="item_name" name="item_name" required>
            </div>
            <div class="form-group">
                <label for="item_type">Item Type:</label>
                <select class="form-control" id="item_type" name="item_type" required>
                    <option value="food">Food</option>
                    <option value="beverage">Beverage</option>
                </select>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" class="form-control " id="image" name="image" accept="image/*" required>
            </div>
            <button type="submit" name="add" class="btn btn-success">Add Item</button>
        </form>

        <h3 class="mt-4">Existing Food and Beverages</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Item Type</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['id']); ?></td>
                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['item_type']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?></td>
                    <td>
                        <?php if ($item['image_path']): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" width="100">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?delete=<?php echo $item['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>