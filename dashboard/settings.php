<?php
session_start();

// Database connection function
function connectDatabase() {
    $host = 'localhost';
    $db   = 'melodylink';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}

// Check if user is logged in
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

// Fetch user data
function getUserData($userId) {
    $pdo = connectDatabase();
    $stmt = $pdo->prepare("SELECT * FROM member WHERE member_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

// Check if email exists (excluding current user)
function findUserByEmail($email, $currentUserId) {
    $pdo = connectDatabase();
    $stmt = $pdo->prepare("SELECT * FROM member WHERE email = ? AND member_id != ?");
    $stmt->execute([$email, $currentUserId]);
    return $stmt->fetch() !== false;
}

// Update user information
function updateUserInfo($userId, $username, $email, $password, $profilePic = null) {
    $pdo = connectDatabase();

    try {
        $pdo->beginTransaction();

        // Prepare base update query
        $query = "UPDATE member SET Username = ?, email = ?";
        $params = [$username, $email];

        // Add password to update if provided
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query .= ", Password = ?";
            $params[] = $hashedPassword;
        }

        // Add profile picture if uploaded
        if ($profilePic) {
            $query .= ", profile_pic = ?";
            $params[] = $profilePic;
        }

        // Add user ID condition
        $query .= " WHERE member_id = ?";
        $params[] = $userId;

        // Prepare and execute the statement
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute($params);

        $pdo->commit();
        return $result;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

// Handle settings page
function renderSettingsPage() {
    // Check login
    checkLogin();

    // Get user ID from session
    $userId = $_SESSION['user_id'];

    // Initialize data array
    $data = [
        'username' => '',
        'email' => '',
        'username_err' => '',
        'email_err' => '',
        'password_err' => '',
        'confirm_password_err' => '',
        'profile_pic_err' => ''
    ];

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Prepare data
        $data['username'] = trim($_POST['username']);
        $data['email'] = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);

        // Validate username
        if (empty($data['username'])) {
            $data['username_err'] = 'Please enter a username.';
        }

        // Validate email
        if (empty($data['email'])) {
            $data['email_err'] = 'Please enter an email.';
        } elseif (findUserByEmail($data['email'], $userId)) {
            $data['email_err'] = 'Email is already registered.';
        }

        // Validate password (if provided)
        if (!empty($password)) {
            if (empty($confirm_password)) {
                $data['confirm_password_err'] = 'Please confirm the password.';
            } elseif ($password !== $confirm_password) {
                $data['confirm_password_err'] = 'Passwords do not match.';
            }
        }

        // Handle profile picture upload
        $profilePic = null;
        if (!empty($_FILES['profile_pic']['name'])) {
            $target_dir = 'uploads/';
            // Create uploads directory if it doesn't exist
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . basename($_FILES['profile_pic']['name']);
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
                $profilePic = basename($_FILES['profile_pic']['name']);
            } else {
                $data['profile_pic_err'] = 'There was an error uploading your profile picture.';
            }
        }

        // Check if there are no errors
        if (empty($data['username_err']) && 
            empty($data['email_err']) && 
            empty($data['password_err']) && 
            empty($data['confirm_password_err']) && 
            empty($data['profile_pic_err'])) {
            
            // Attempt to update user information
            if (updateUserInfo($userId, $data['username'], $data['email'], $password, $profilePic)) {
                // Update session username if changed
                $_SESSION['username'] = $data['username'];
                
                // Redirect or show success message
                $successMessage = "Profile updated successfully!";
            } else {
                $errorMessage = "Something went wrong. Please try again.";
            }
        }
    } else {
        // Initial form load - fetch current user data
        $userData = getUserData($userId);
        $data['username'] = $userData->Username;
        $data['email'] = $userData->email;
    }

    // Render the page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .settings-container {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .is-invalid {
            border-color: red;
        }
        .invalid-feedback {
            color: red;
            font-size: 0.8em;
            margin-top: 5px;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .error-message {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <h1>Profile</h1>

        <?php if(isset($successMessage)): ?>
            <div class="success-message"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>

        <?php if(isset($errorMessage)): ?>
            <div class="error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" 
                       value="<?php echo htmlspecialchars($data['username']); ?>" 
                       class="<?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>">
                <?php if(!empty($data['username_err'])): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($data['username_err']); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" 
                       value="<?php echo htmlspecialchars($data['email']); ?>" 
                       class="<?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>">
                <?php if(!empty($data['email_err'])): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($data['email_err']); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" 
                       class="<?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>">
                <?php if(!empty($data['password_err'])): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($data['password_err']); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" 
                       class="<?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>">
                <?php if(!empty($data['confirm_password_err'])): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($data['confirm_password_err']); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="profile_pic">Profile Picture</label>
                <input type="file" name="profile_pic" id="profile_pic">
                <?php if(!empty($data['profile_pic_err'])): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($data['profile_pic_err']); ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn">Save Changes</button>
        </form>
    </div>
</body>
</html>
<?php
}

// Run the settings page
renderSettingsPage();
?>