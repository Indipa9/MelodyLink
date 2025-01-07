<?php
include 'config.php'; // Include database connection file
$id = isset($_GET['id']) ? $_GET['id'] : '';
$product = null;

if (!empty($id)) {
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM equipment WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Product Dashboard</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <header>
        <div class="navbar">
            <h1 class="logo">MelodyLink</h1>
            <nav>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">My Supplies</a></li>
                    <li><a href="#">Profile</a></li>
                    <li><a href="#">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div>
            <h2>Music Equipments and Merchandise</h2>
            <a href="Form.php">
                <button>
                    <i class="btn"></i> Add Product
                </button>
            </a>
        </div>
        <br>

        <!-- Wrapper for the table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch data from the database
                    $sql = "SELECT * FROM equipment";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        // Output data for each row
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . (isset($row['ID']) ? htmlspecialchars($row['ID']) : 'N/A') . "</td>";
                            echo "<td>";
                            if (isset($row['Image']) && !empty($row['Image'])) {
                                echo "<img src='" . htmlspecialchars($row['Image']) . "' alt='Product Image' style='width: 100px; height: auto;'>";
                            } else {
                                echo "No Image";
                            }
                            echo "</td>";
                            echo "<td>" . (isset($row['Name']) ? htmlspecialchars($row['Name']) : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['Quantity']) ? htmlspecialchars($row['Quantity']) : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['Category']) ? htmlspecialchars($row['Category']) : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['Price']) ? htmlspecialchars($row['Price']) : 'N/A') . "</td>";
                            echo "<td>
                                    <a href='Form.php?id=" . htmlspecialchars($row['ID']) . "'>
                                        <button class='edit-btn'>
                                            <i class='fas fa-edit'></i> Edit
                                        </button>
                                    </a>
                                    <a href='delete.php?id=" . htmlspecialchars($row['ID']) . "' onclick='return confirm(\"Are you sure you want to delete this product?\");'>
                                        <button class='delete-btn'>
                                            <i class='fas fa-trash'></i> Delete
                                        </button>
                                    </a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        // Display a message if no data found
                        echo "<tr><td colspan='7'>No data found</td></tr>";
                    }

                    // Close the database connection
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Music Product Dashboard. All rights reserved.</p>
    </footer>
</body>
</html>
