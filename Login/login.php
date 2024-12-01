<?php
session_start(); // Start the session

// Connect to the database
$servername = "localhost";
$username = "root"; // Use your DB credentials
$password = "";
$dbname = "melodylink"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare a more comprehensive login query that checks multiple user tables
    $tables = [
        'admin' => 'admin_id',
        'member' => 'member_id',
        'supplier' => 'supplier_id',
        'event_organiser' => 'organizer_id',
        'artist' => 'Artist_id'
    ];

    $login_successful = false;

    foreach ($tables as $table => $id_column) {
        $stmt = $conn->prepare("SELECT $id_column, Username, Password FROM $table WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['Password'])) {
                // Store user data in session
                $_SESSION['user_type'] = $table;
                $_SESSION['user_id'] = $user[$id_column];
                $_SESSION['username'] = $user['Username'];

                $login_successful = true;

                // Redirect based on user type
                switch ($table) {
                    case 'admin':
                        header("Location: ../admin/admin.html");

                        break;
                    case 'member':
                        header("Location: ../Community/display_community.php");
                        break;
                    case 'supplier':
                        header("Location: ../supplier/My supplies.php");
                        break;
                    case 'event_organiser':
                        header("Location: ../org/eventm/public/index.php");
                        break;
                    case 'artist':
                        header("Location: ../artist-dashboard/artist-dashboard.php");
                        break;
                }
                exit;
            }
        }
    }

    // If no successful login was found
    if (!$login_successful) {
        $error_message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MelodyLink - Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .login-container h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-size: 14px;
            color: #555;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .error-message {
            color: red;
            font-size: 14px;
            text-align: center;
            margin-bottom: 10px;
        }
        .login-button {
            width: 100%;
            padding: 10px;
            background-color: #0079d3;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .login-button:hover {
            background-color: #005fa3;
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
            display: block;
            color: #0079d3;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-button">Login</button>
        </form>

        <a href="../registration.html" class="signup-link">Don't have an account? Sign up</a>
    </div>
</body>
</html>