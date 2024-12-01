<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];
    $price = $_POST['price'];

    if (empty($id)) {
        // Add new product
        $sql = "INSERT INTO equipment (Name, Quantity, Category, Price) VALUES ('$name', $quantity, '$category', $price)";
    } else {
        // Update existing product
        $sql = "UPDATE equipment SET name='$name', quantity=$quantity, category='$category', price=$price WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: My Supplies.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
