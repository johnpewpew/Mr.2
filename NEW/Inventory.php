<?php
$conn = new mysqli('localhost', 'root', '', 'database_pos');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Handle form submission for adding, updating, and deleting items
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'];
    $category_id = $_POST['category_id'];
    $medium_size = $_POST['medium_size'];
    $large_size = $_POST['large_size'];
    $medium_price = $_POST['medium_price'];
    $large_price = $_POST['large_price'];
    $status = isset($_POST['status']) ? 1 : 0; // Get the availability status
    $imagePath = '';

    // For updating items, fetch the existing image if no new image is uploaded
    if (isset($_POST['update_item']) && empty($_FILES['image']['name'])) {
        $item_id = $_POST['item_id'];
        $stmt = $conn->prepare("SELECT image FROM items WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stmt->bind_result($imagePath);
        $stmt->fetch();
        $stmt->close();
    }

    // Handle image upload if a new image is provided
    if (!empty($_FILES['image']['name'])) {
        $targetDir = 'uploads/';
        $imageName = basename($_FILES['image']['name']);
        $imagePath = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        // Validate the uploaded file is an image
        if (getimagesize($_FILES['image']['tmp_name']) === false) {
            die("File is not an image.");
        }

        // Limit the size to 5MB
        if ($_FILES['image']['size'] > 5000000) {
            die("Sorry, your file is too large.");
        }

        // Allow only certain image formats
        $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedFormats)) {
            die("Sorry, only JPG, JPEG, PNG, and GIF files are allowed.");
        }

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            die("Sorry, there was an error uploading your file.");
        }
    }

    // Handle adding or updating item
    if (isset($_POST['add_item']) || isset($_POST['update_item'])) {
        if (isset($_POST['add_item'])) {
            $stmt = $conn->prepare("INSERT INTO items (name, category_id, medium_size, large_size, medium_price, large_price, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("siiddssi", $item_name, $category_id, $medium_size, $large_size, $medium_price, $large_price, $imagePath, $status);
        } elseif (isset($_POST['update_item'])) {
            $item_id = $_POST['item_id'];
            $stmt = $conn->prepare("UPDATE items SET name=?, category_id=?, medium_size=?, large_size=?, medium_price=?, large_price=?, image=?, status=? WHERE id=?");
            $stmt->bind_param("siiddssii", $item_name, $category_id, $medium_size, $large_size, $medium_price, $large_price, $imagePath, $status, $item_id);
        }
        $stmt->execute();
        $stmt->close();
    }

    // Handle updating medium and large sizes for all items
    if (isset($_POST['update_sizes'])) {
        // Updating medium and large sizes for all items in the database
        $stmt = $conn->prepare("UPDATE items SET medium_size=?, large_size=?");
        $stmt->bind_param("ii", $medium_size, $large_size);
        $stmt->execute();
        $stmt->close();
    }

    // Handle deleting an item
    if (isset($_POST['delete_item'])) {
        $item_id = $_POST['item_id'];

        // Delete related sales records first
        $stmtSales = $conn->prepare("DELETE FROM sales WHERE item_id=?");
        $stmtSales->bind_param("i", $item_id);
        $stmtSales->execute();
        $stmtSales->close();

        // Now delete the item from the items table
        $stmt = $conn->prepare("DELETE FROM items WHERE id=?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: Inventory.php");
    exit();
}

// Fetch items and categories
$items = $conn->query("SELECT items.*, categories.name as category_name FROM items JOIN categories ON items.category_id = categories.id");
$categories = $conn->query("SELECT * FROM categories");

// Fetch all items with remaining medium and large sizes
$items = $conn->query("
    SELECT items.id, items.name, items.image, items.medium_size, items.large_size,
    COALESCE((SELECT SUM(quantity) FROM sales WHERE sales.item_id = items.id AND sales.size = 'Medium'), 0) AS total_medium_sold,
    COALESCE((SELECT SUM(quantity) FROM sales WHERE sales.item_id = items.id AND sales.size = 'Large'), 0) AS total_large_sold,
    COALESCE((SELECT SUM(quantity * CASE WHEN size = 'Medium' THEN medium_price ELSE large_price END) 
              FROM sales 
              WHERE sales.item_id = items.id), 0) AS total_sales,
    COALESCE((SELECT SUM(quantity) FROM sales WHERE sales.item_id = items.id), 0) AS total_items_sold
    FROM items
");

// Calculate total cups sold for all items
$totalMediumSold = $conn->query("SELECT SUM(quantity) AS total_medium_sold FROM sales WHERE size = 'Medium'")->fetch_assoc()['total_medium_sold'] ?? 0;
$totalLargeSold = $conn->query("SELECT SUM(quantity) AS total_large_sold FROM sales WHERE size = 'Large'")->fetch_assoc()['total_large_sold'] ?? 0;

$topProducts = $conn->query("
    SELECT items.name, 
    COALESCE((SELECT SUM(quantity * CASE WHEN size = 'Medium' THEN medium_price ELSE large_price END) 
              FROM sales 
              WHERE sales.item_id = items.id), 0) AS total_sales,
    COALESCE((SELECT SUM(quantity) FROM sales WHERE sales.item_id = items.id), 0) AS total_items_sold
    FROM items
    ORDER BY total_sales DESC
    LIMIT 3
");

$topLabels = [];
$topData = [];
while ($topItem = $topProducts->fetch_assoc()) {
    $topLabels[] = htmlspecialchars($topItem['name']);
    $topData[] = (int) htmlspecialchars($topItem['total_sales']);
}

$items->data_seek(0);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Sales Dashboard</title>
    <link rel="stylesheet" href="Inventory.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" type="text/css" href="./css/main.css">
    <link rel="stylesheet" type="text/css" href="./css/admin.css">
    <link rel="stylesheet" type="text/css" href="./css/util.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <div class="container">
        <div class="scrollable-table">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Name of Products</th>
                        <th>Medium Sales</th>
                        <th>Large Sales</th>
                        <th>Profit Accumulation</th>
                        <th>Total Items Sold</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $items->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= htmlspecialchars($item['total_medium_sold']) ?></td>
                            <td><?= htmlspecialchars($item['total_large_sold']) ?></td>
                            <td><?= htmlspecialchars($item['total_sales']) ?></td>
                            <td><?= htmlspecialchars($item['total_items_sold']) ?></td>
                            <td><img src="<?= htmlspecialchars($item['image']) ?>" alt="Image" width="50"></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="cups-info">
            <div class="cups-left">
                <h4>Remaining Cups</h4>
                <form method="POST">
            <div class="cup-size">
                <label for="large-size">Large</label>
                <input type="number" name="large_size" id="large-size" required>
            </div>
            <div class="cup-size">
                <label for="medium-size">Medium</label>
                <input type="number" name="medium_size" id="medium-size" required>
            </div>
            <button type="submit" name="update_sizes" id="update-quantities-btn">Update Sizes</button>
        </form>
            </div>

            <div class="cups-sold">
                <h4>Number of Cups Sold</h4>
                <div class="cup-size">
                    <label>Large</label>
                    <input type="number" value="<?= htmlspecialchars($totalLargeSold) ?>" readonly>
                </div>
                <div class="cup-size">
                    <label>Medium</label>
                    <input type="number" value="<?= htmlspecialchars($totalMediumSold) ?>" readonly>
                </div>
            </div>
            <div class="chart">
                <h4>Top Products</h4>
                <canvas id="myChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <script>
        const labels = <?= json_encode($topLabels) ?>;
        const data = {
            labels: labels,
            datasets: [{
                label: 'Total Sales',
                backgroundColor: '#FF4D00',
                borderWidth: 1,
                data: <?= json_encode($topData) ?>,
            }]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        const myChart = new Chart(
            document.getElementById('myChart'),
            config
        );
    </script>
</body>

</html>