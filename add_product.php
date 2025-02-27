<?php
session_start();
include('db.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $cost = isset($_POST['cost']) ? floatval($_POST['cost']) : 0;
    $track_quantity = isset($_POST['track_quantity']) ? 1 : 0;
    $stock_quantity = intval($_POST['stock_quantity']);
    $shop_location = trim($_POST['shop_location']);
    $location_quantity = isset($_POST['location_quantity']) ? intval($_POST['location_quantity']) : 0;
    $is_physical = isset($_POST['is_physical']) ? 1 : 0;
    $weight = isset($_POST['weight']) ? floatval($_POST['weight']) : 0;
    $weight_unit = isset($_POST['weight_unit']) ? $_POST['weight_unit'] : "kg";

    $availability = ($quantity > 0) ? 'Available' : 'Out of Stock';
    $media = "";
    if (!empty($_FILES["media"]["name"])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_name = basename($_FILES["media"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name; 
        if (move_uploaded_file($_FILES["media"]["tmp_name"], $target_file)) {
            $media = $target_file;
        } else {
            echo "<script>alert('File upload failed.'); window.history.back();</script>";
            exit();
        }
    }

    $sql = "INSERT INTO products (title, description, media, price, cost, track_quantity, stock_quantity, shop_location, location_quantity, is_physical, weight, weight_unit) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error (products): " . $conn->error);
    }

    $stmt->bind_param("sssddiisiids", $title, $description, $media, $price, $cost, $track_quantity, $stock_quantity, $shop_location, $location_quantity, $is_physical, $weight, $weight_unit);
    if ($stmt->execute()) {
        $product_id = $stmt->insert_id; 
        $inventory_sql = "INSERT INTO inventory (product_id, product_name, stock_quantity, availability) VALUES (?, ?, ?, ?)";
        $inventory_stmt = $conn->prepare($inventory_sql);

        if (!$inventory_stmt) {
            die("SQL Error (inventory): " . $conn->error);
        }

        $inventory_stmt->bind_param("isis", $product_id, $title, $stock_quantity, $availability);
        $inventory_stmt->execute();
        $inventory_stmt->close();

        echo "<script>
                alert('Product successfully added!');
                window.location.href='user_home.php'; 
              </script>";
    } else {
        echo "SQL Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Add a New Product</h2>
    <form action="add_product.php" method="post" enctype="multipart/form-data">
        <label>Title:</label>
        <input type="text" name="title" required>
        
        <label>Description:</label>
        <textarea name="description"></textarea>
        
        <label>Price:</label>
        <input type="number" step="0.01" name="price" required>
        
        <label>Quantity:</label>
        <input type="number" name="quantity" required>
        
        <label>Shop Location:</label>
        <input type="text" name="shop_location" required>
        
        <label>Upload Image:</label>
        <input type="file" name="media">
        
        <button type="submit">Add Product</button>
    </form>
</body>
</html>
