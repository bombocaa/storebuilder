<?php
session_start();
include('db.php');

$user_initials = "U"; 
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT full_name FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $full_name = $row['full_name'];

        $name_parts = explode(" ", $full_name);
        $user_initials = strtoupper(substr($name_parts[0], 0, 1)); 
        if (count($name_parts) > 1) {
            $user_initials .= strtoupper(substr($name_parts[count($name_parts) - 1], 0, 1)); 
        }
    }
    $stmt->close();
}

$sql = "SELECT p.id, p.title, p.description, p.price, p.shop_location, p.media, i.stock_quantity 
        FROM products p
        LEFT JOIN inventory i ON p.id = i.product_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="css/user_homes.css">
    <script src="js/script.js" defer></script>
</head>
<body>
    <nav class="navbar">
        <div class="logo">StoreBuilder</div>
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search...">
        </div>
        <div class="user-dropdown">
            <button class="user-btn"><?php echo $user_initials; ?></button>
            <div class="dropdown-content">
                <a href="settings.php">Settings</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="sidebar">
            <h2>Order Management</h2>
            <a href="product_information.php" class="sidebar-items">Add Product</a>
            <a href="user_home.php" class="sidebar-items">Products</a>
            <a href="inventory.php" class="sidebar-items">Inventory</a>
        </div>

        <div class="main-content">
            <h2>Product List</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock Quantity</th>
                        <th>Shop Location</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td id="stock-<?php echo $row['id']; ?>"><?php echo $row['stock_quantity']; ?></td>
                        <td><?php echo $row['shop_location']; ?></td>
                        <td><img src="<?php echo $row['media']; ?>" width="50" height="50"></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
