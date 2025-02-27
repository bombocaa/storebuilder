<?php
session_start();
include('db.php');

$user_initials = "U"; 
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT full_name FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $full_name = $row['full_name'];

        $name_parts = explode(" ", $full_name);
        $user_initials = strtoupper(substr($name_parts[0], 0, 1)); 
        if (count($name_parts) > 1) {
            $user_initials .= strtoupper(substr($name_parts[count($name_parts) - 1], 0, 1)); 
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Information</title>
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
<form action="add_product.php" method="POST" enctype="multipart/form-data">
    <div class="product-info">
        <h1>PRODUCT INFORMATION</h1>

        <label for="title">Title</label>
        <input type="text" id="title" name="title" placeholder="Short sleeve t-shirt" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" placeholder="Enter product details..." required></textarea>

        <label>Media</label>
        <input type="file" id="media" name="media">
        <p class="hint">Accepts images, videos, or 3D models</p>

        <h2>Pricing</h2>
        <label for="price">Price</label>
        <input type="number" id="price" name="price" placeholder="0.00" required>

        <label for="cost">Cost per item</label>
        <input type="number" id="cost" name="cost" placeholder="0.00">

        <h2>Inventory</h2>
        <div class="checkbox-group">
            <input type="checkbox" id="track-quantity" name="track_quantity">
            <label for="track-quantity">Track quantity</label>
        </div>

        <label for="quantity">Quantity</label>
        <input type="number" id="quantity" name="stock_quantity" placeholder="0" required>

        <label for="shop-location">Shop location</label>
        <input type="text" id="shop-location" name="shop_location" placeholder="Enter shop location" required>

        <label for="location-quantity">Shop location quantity available</label>
        <input type="number" id="location-quantity" name="location_quantity" placeholder="0">

        <h2>Shipping</h2>
        <div class="checkbox-group">
            <input type="checkbox" id="physical-product" name="is_physical">
            <label for="physical-product">This is a physical product</label>
        </div>

        <label for="weight">Weight</label>
        <input type="number" id="weight" name="weight" placeholder="0.0">
        
        <label for="weight-unit">Weight unit</label>
        <select id="weight-unit" name="weight_unit">
            <option value="kg">kg</option>
            <option value="g">g</option>
            <option value="lb">lb</option>
            <option value="oz">oz</option>
        </select>

        <button type="submit" class="add-product-btn">Add Product</button>
    </div>
</form>

</div>
</div>
</body>
</html>