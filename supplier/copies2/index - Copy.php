<?php
include 'config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $name = $conn->real_escape_string($_POST['name']);
    $quantity = intval($_POST['quantity']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = floatval($_POST['price']);

    if ($id) {
        // Update existing product
        $query = "UPDATE equipment SET Name='$name', Quantity=$quantity, Category='$category', Price=$price WHERE ID=$id";
        if ($conn->query($query)) {
            header('Location: My Supplies.php');
        } else {
            echo "Error updating product: " . $conn->error;
        }
    } else {
        // Add new product
        $query = "INSERT INTO equipment (Name, Quantity, Category, Price) VALUES ('$name', $quantity, '$category', $price)";
        if ($conn->query($query)) {
            header('Location: My Supplies.php');
        } else {
            echo "Error adding product: " . $conn->error;
        }
    }
}
?>
