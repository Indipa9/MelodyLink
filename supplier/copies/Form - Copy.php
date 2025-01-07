<?php
include 'config.php'; // Database connection

// Initialize variables
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$name = '';
$quantity = '';
$category = '';
$price = '';

// If editing, fetch the product details
if ($id) {
    $query = "SELECT * FROM equipment WHERE ID = $id";
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $name = htmlspecialchars($row['Name']);
        $quantity = htmlspecialchars($row['Quantity']);
        $category = htmlspecialchars($row['Category']);
        $price = htmlspecialchars($row['Price']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $id ? 'Edit Product' : 'Add New Product'; ?></title>
  <link rel="stylesheet" href="styles3.css">
</head>
<body>
  <div class="container">
  <div class="mb-6">
  <h2 class="text-xl font-semibold">
    <?php echo $id ? 'Edit Product' : 'Add New Product'; ?></h2>
    <form action="index.php" method="POST">
      <input type="hidden" name="id" value="<?php echo $id; ?>">
      <label for="name">Product Name:</label>
      <input type="text" name="name" value="<?php echo $name; ?>" required>
      <label for="quantity">Quantity:</label>
      <input type="number" name="quantity" value="<?php echo $quantity; ?>" required>
      <label for="category">Category:</label>
      <input type="text" name="category" value="<?php echo $category; ?>" required>
      <label for="price">Price:</label>
      <input type="number" step="0.01" name="price" value="<?php echo $price; ?>" required>
      <button type="submit"><?php echo $id ? 'Update Product' : 'Add Product'; ?></button>
    </form>
  </div>
</body>
</html>
