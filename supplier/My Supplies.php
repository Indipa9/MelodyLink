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
<header class="header">
    <div class="logo">
      <img src="./uploads/logo.png" alt="MelodyLink Logo">
      <h1>MelodyLink</h1>
    </div>
<nav class="navbar">
      <a href="dashboard.php">Home</a>
      <a href="communities.php">Dashboard</a>
      <a href="Requests.php">Pricing</a>
      <a href="Requests.php">Orders</a>
      <a href="logout.php" class="logout">Logout</a>
    </nav>
    <div class="profile-icon">
      <img src="./uploads/profile2.png" alt="Profile Icon">
  </div>
  </header>

    <main>
        <!-- Dashboard Section -->
        <div class="dashboard">
            <div class="dashboard-card blue">
                <h3>Total Products</h3>
                <div class="card-content">
                    <img src="./uploads/list-solid.png" alt="Checklist Icon" class="card-icon">
                    <p>1080</p>
                </div>
            </div>
            <div class="dashboard-card purple">
                <h3>Total Orders</h3>
                <div class="card-content">
                    <img src="./uploads/list-check-solid.png" width="50px" height="auto" alt="Checklist Icon" class="card-icon">
                    <p>1080</p>
                </div>
            </div>
            <div class="dashboard-card orange">
                <h3>Total Profit</h3>
                <div class="card-content">
                    <img src="./uploads/icons8-order-list-67.png" alt="Checklist Icon" class="card-icon">
                    <p>$12,120</p>
                </div>
            </div>
            <div class="dashboard-card green">
                <h3>New Orders</h3>
                <div class="card-content">
                    <img src="./uploads/list-ul-solid.png" alt="Checklist Icon" class="card-icon">
                    <p>320</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts">
            <div class="chart">
                <h4>Statistics of Order</h4>
                <img class="chart-placeholder" src="./uploads/chart5.gif" alt="Checklist Icon">
            </div>
            <div class="chart">
                <h4>Analytics</h4>
                <img class="chart-placeholder" src="./uploads/chart4.gif" alt="Checklist Icon">
            </div>
        </div>

        <!-- Table Section -->
         <br>
         <hr>
        <div>
            <a href="Form.php">
                <button>
                    <i class="btn"></i> Add Product
                </button>
            </a>
        </div>
        <br>

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
                    $sql = "SELECT * FROM equipment";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['ID'] ?? 'N/A') . "</td>";
                            echo "<td>";
                            if (!empty($row['Image'])) {
                                echo "<img src='" . htmlspecialchars($row['Image']) . "' alt='Product Image' style='width: 100px; height: auto;'>";
                            } else {
                                echo "No Image";
                            }
                            echo "</td>";
                            echo "<td>" . htmlspecialchars($row['Name'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row['Quantity'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row['Category'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row['Price'] ?? 'N/A') . "</td>";
                            echo "<td>
                                    <a href='Form.php?id=" . htmlspecialchars($row['ID']) . "'>
                                        <button class='edit-btn'>Edit</button>
                                    </a>
                                    <a href='delete.php?id=" . htmlspecialchars($row['ID']) . "' onclick='return confirm(\"Are you sure?\");'>
                                        <button class='delete-btn'>Delete</button>
                                    </a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No data found</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
  <div class="footer-container">
    <div class="footer-about">
      <h3>About MelodyLink</h3>
      <p>MelodyLink is an innovative web application designed to revolutionize the way we engage with music. We offer a comprehensive, all-in-one platform for artists, fans, event organizers, merchandise vendors, and event equipment renters.</p>
      <img src="./uploads/logo.png" alt="MelodyLink Logo">
    </div>
    
    <div class="footer-links">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Music</a></li>
        <li><a href="#">Artists</a></li>
        <li><a href="#">Events</a></li>
        <li><a href="#">Store</a></li>
        <li><a href="#">Communities</a></li>
        <li><a href="#">Contact Us</a></li>
      </ul>
    </div>
    
    <div class="footer-social">
      <h3>Follow Us</h3>
      <div class="social-icons">
        <a href="#"><img src="path-to-facebook-icon.png" alt="Facebook"></a>
        <a href="#"><img src="path-to-twitter-icon.png" alt="Twitter"></a>
        <a href="#"><img src="path-to-instagram-icon.png" alt="Instagram"></a>
        <a href="#"><img src="path-to-youtube-icon.png" alt="YouTube"></a>
      </div>
    </div>
  </div>
  
  <div class="footer-bottom">
    <p>© 2024 MelodyLink. All Rights Reserved.</p>
  </div>
</footer>

</body>
</html>
