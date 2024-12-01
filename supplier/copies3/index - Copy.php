<?php
include 'config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $name = $conn->real_escape_string($_POST['name']);
    $quantity = intval($_POST['quantity']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = floatval($_POST['price']);
    $imagePath = '';

    // Handle file upload for the image
    if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
        $image = $_FILES['image'];
        $imageName = time() . '_' . basename($image['name']); // Unique name for the image
        $targetDir = "uploads/";
        $targetFilePath = $targetDir . $imageName;

        // Create the uploads directory if it doesn't exist
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
            $imagePath = $targetFilePath; // Save the file path for database entry
        } else {
            echo "Error uploading image. Please check the uploads directory permissions.";
            exit;
        }
    }

    if ($id) {
        // Update existing product with image (if uploaded)
        $query = $imagePath
            ? "UPDATE equipment SET Name='$name', Quantity=$quantity, Category='$category', Price=$price, Image='$imagePath' WHERE ID=$id"
            : "UPDATE equipment SET Name='$name', Quantity=$quantity, Category='$category', Price=$price WHERE ID=$id";

        if ($conn->query($query)) {
            header('Location: My Supplies.php');
        } else {
            echo "Error updating product: " . $conn->error;
        }
    } else {
        // Add new product
        $query = "INSERT INTO equipment (Name, Quantity, Category, Price, Image) VALUES ('$name', $quantity, '$category', $price, '$imagePath')";
        if ($conn->query($query)) {
            header('Location: My Supplies.php');
        } else {
            echo "Error adding product: " . $conn->error;
        }
    }
}
?>
