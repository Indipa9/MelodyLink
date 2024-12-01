<?php
include 'config.php'; // Database connection

// Initialize variables
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$name = '';
$quantity = '';
$category = '';
$price = '';
$imagePath = '';

// Fetch product details for editing if an ID is provided
if ($id) {
    $query = "SELECT * FROM equipment WHERE ID = $id";
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $name = htmlspecialchars($row['Name']);
        $quantity = htmlspecialchars($row['Quantity']);
        $category = htmlspecialchars($row['Category']);
        $price = htmlspecialchars($row['Price']);
        $imagePath = htmlspecialchars($row['Image']);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $name = $conn->real_escape_string($_POST['name']);
    $quantity = intval($_POST['quantity']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = floatval($_POST['price']);
    $imagePath = isset($_POST['existingImage']) ? $_POST['existingImage'] : '';

    // Image upload handling
    if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
        $imageName = basename($_FILES['image']['name']);
        $targetDir = "uploads/";
        $targetFilePath = $targetDir . $imageName;

        // Move the uploaded file to the server
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $imagePath = $targetFilePath;
        } else {
            echo "Failed to upload image.";
        }
    }

    if ($id) {
        // Update existing record
        $query = "UPDATE equipment SET Name='$name', Quantity=$quantity, Category='$category', Price=$price, Image='$imagePath' WHERE ID=$id";
    } else {
        // Insert new record
        $query = "INSERT INTO equipment (Name, Quantity, Category, Price, Image) VALUES ('$name', $quantity, '$category', $price, '$imagePath')";
    }

    if ($conn->query($query)) {
        header('Location: My Supplies.php');
        exit;
    } else {
        echo "Error: " . $conn->error;
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
    <script>
        // JavaScript Form Validation
        function validateForm() {
            const name = document.getElementById("name").value.trim();
            const quantity = document.getElementById("quantity").value.trim();
            const category = document.getElementById("category").value.trim();
            const price = document.getElementById("price").value.trim();

            if (!name || !quantity || !category || !price) {
                alert("All fields except image are required.");
                return false;
            }

            if (isNaN(quantity) || quantity <= 0) {
                alert("Quantity must be a positive number.");
                return false;
            }

            if (isNaN(price) || price <= 0) {
                alert("Price must be a positive number.");
                return false;
            }

            return true;
        }
    </script>
    <style>
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
    </style>
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
                <form action="Form.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input type="hidden" name="existingImage" value="<?php echo $imagePath; ?>">

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
                        <select id="category" name="category" required>
                            <option value="">Select a category</option>
                            <option value="Merchandise" <?php echo $category == 'Merchandise' ? 'selected' : ''; ?>>Merchandise</option>
                            <option value="Equipment" <?php echo $category == 'Equipment' ? 'selected' : ''; ?>>Equipment</option>
                        </select>
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

                    <div>
                        <label for="image">Add Image</label>
                        <input type="file" 
                               id="image" 
                               name="image" 
                               accept="image/*">
                        <?php if ($imagePath): ?>
                            <div>
                                <img src="<?php echo $imagePath; ?>" alt="Product Image" style="max-width: 100px; max-height: 100px;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <button type="submit">
                            <?php echo $id ? 'Update Product' : 'Add Product'; ?>
                        </button>
                        <button type="button" 
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