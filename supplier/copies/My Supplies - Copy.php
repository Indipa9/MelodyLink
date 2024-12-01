<?php
include 'config.php'; // include database connection file
$id = isset($_GET['id']) ? $_GET['id'] : '';
$product = null;

if (!empty($id)) {
    $result = $conn->query("SELECT * FROM equipment WHERE ID=$id");
    $product = $result->fetch_assoc();
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
            <a href="Form.html">
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
                    $sql = "SELECT id, name, quantity, category, price FROM equipment";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        // Output data for each row
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . (isset($row['id']) ? htmlspecialchars($row['id']) : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['name']) ? htmlspecialchars($row['name']) : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['quantity']) ? htmlspecialchars($row['quantity']) : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['category']) ? htmlspecialchars($row['category']) : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['price']) ? htmlspecialchars($row['price']) : 'N/A') . "</td>";
                            echo "<td>
                                    <a href='Form.html?id=" . htmlspecialchars($row['id']) . "'>
                                        <button class='edit-btn'>
                                            <i class='fas fa-edit'></i> Edit
                                        </button>
                                    </a>
                                    <a href='delete.php?id=" . htmlspecialchars($row['id']) . "' onclick='return confirm(\"Are you sure you want to delete this product?\");'>
                                        <button class='delete-btn'>
                                            <i class='fas fa-trash'></i> Delete
                                        </button>
                                    </a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        // Display a message if no data found
                        echo "<tr><td colspan='6'>No data found</td></tr>";
                    }

                    // Close the database connection
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <p>&copy; 2023 Music Product Dashboard. All rights reserved.</p>
    </footer>
</body>
</html>