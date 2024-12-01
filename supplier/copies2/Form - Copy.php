<?php
include 'config.php'; // Database connection

// Initialize variables
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$name = '';
$quantity = '';
$category = '';
$price = '';

// Fetch product details for editing if an ID is provided
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
    <title>Music Product Dashboard</title>
    <link rel="stylesheet" href="styles3.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Music Product Dashboard</h1>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">My Supplies</a></li>
                <li><a href="#">Profile</a></li>
                <li><a href="#">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="mb-6"><?php echo $id ? 'Edit Product' : 'Add New Product'; ?></h2>
                <form action="index.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <div>
                        <label for="name">Product Name</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="<?php echo $name; ?>" 
                               placeholder="Enter product name" 
                               required>
                    </div>

                    <div>
                        <label for="quantity">Quantity</label>
                        <input type="number" 
                               id="quantity" 
                               name="quantity" 
                               value="<?php echo $quantity; ?>" 
                               placeholder="Enter Quantity" 
                               required>
                    </div>

                    <div>
                        <label for="category">Category</label>
                        <input type="text" 
                               id="category" 
                               name="category" 
                               value="<?php echo $category; ?>" 
                               placeholder="Enter category" 
                               required>
                    </div>

                    <div>
                        <label for="price">Price</label>
                        <input type="number" 
                               id="price" 
                               name="price" 
                               value="<?php echo $price; ?>" 
                               step="0.01" 
                               placeholder="Enter price" 
                               required>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <button type="submit">
                            <?php echo $id ? 'Update Product' : 'Add Product'; ?>
                        </button>
                        <button type="cancel" 
                                onclick="window.location.href='My Supplies.php'" 
                                style="background-color: #6b7280;">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Music Product Dashboard. All rights reserved.</p>
    </footer>
</body>
</html>