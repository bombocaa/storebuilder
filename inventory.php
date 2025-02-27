<?php
session_start();
include('db.php');

$user_initials = "U"; 
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT full_name FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
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
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_stock'])) {
    $product_id = $_POST['id'];
    $new_stock = $_POST['stock_quantity'];
    $availability = ($new_stock > 0) ? 'Available' : 'Out of Stock';
    $sql = "UPDATE inventory SET stock_quantity = ?, availability = ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("isi", $new_stock, $availability, $product_id);
        if ($stmt->execute()) {
            echo "<script>alert('Stock updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating inventory: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error preparing statement: " . $conn->error . "');</script>";
    }

    $sql = "UPDATE products SET quantity = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ii", $new_stock, $product_id);
        if (!$stmt->execute()) {
            echo "<script>alert('Error updating products: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error preparing statement for products: " . $conn->error . "');</script>";
    }
}

$sql = "SELECT i.product_id, p.title AS product_name, i.stock_quantity, i.availability 
        FROM inventory i 
        LEFT JOIN products p ON i.product_id = p.id";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="css/inventory.css">
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
        <h1>Inventory Management</h1>
        <table border="1">
            <tr>
                <th>Product Name</th>
                <th>Stock Quantity</th>
                <th>Availability</th>
                <th>Update Stock</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo $row['stock_quantity']; ?></td>
                    <td><?php echo $row['availability']; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $row['product_id']; ?>">
                            <input class="stock_input"type="number" name="stock_quantity" value="<?php echo $row['stock_quantity']; ?>" required>
                            <button class="button-stock" type="submit" name="update_stock">Update</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
</body>
</html>
