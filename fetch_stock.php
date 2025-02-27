<?php
include('db.php');

$sql = "SELECT id, stock_quantity FROM inventory";  
$result = $conn->query($sql);

$stock_data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $stock_data[$row['id']] = $row['stock_quantity'];
    }
}

echo json_encode($stock_data);
?>
