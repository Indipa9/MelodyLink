<?php
// Database Connection
class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $dbname = 'melodylink';

    private $conn;
    private $stmt;
    private $error;

    public function __construct() {
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        
        // Set options
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        // Create PDO instance
        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    // Prepare statement
    public function query($sql) {
        $this->stmt = $this->conn->prepare($sql);
    }

    // Bind values
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // Execute the prepared statement
    public function execute() {
        return $this->stmt->execute();
    }

    // Get result set as array of objects
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get single record as object
    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Get row count
    public function rowCount() {
        return $this->stmt->rowCount();
    }
}

// User Registration Class
class UserRegistration {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Validate and sanitize input
    private function validateInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Check if email exists
    public function emailExists($email, $userType) {
        // Determine the correct table based on user type
        $table = match($userType) {
            'member' => 'member',
            'artist' => 'artist',
            'organizer' => 'event_organiser',
            'supplier' => 'supplier',
            default => null
        };

        if (!$table) return false;

        $this->db->query("SELECT * FROM $table WHERE email = :email");
        $this->db->bind(':email', $email);
        $this->db->single();

        return $this->db->rowCount() > 0;
    }

    // Register user
    public function register($userData) {
        // Validate input
        $errors = [];

        // Name validation
        if (empty($userData['name'])) {
            $errors['name'] = "Name is required";
        }

        // Email validation
        if (empty($userData['email'])) {
            $errors['email'] = "Email is required";
        } elseif ($this->emailExists($userData['email'], $userData['userType'])) {
            $errors['email'] = "Email is already registered";
        }

        // Password validation
        if (empty($userData['password'])) {
            $errors['password'] = "Password is required";
        } elseif (strlen($userData['password']) < 6) {
            $errors['password'] = "Password must be at least 6 characters long";
        }

        // Confirm password validation
        if ($userData['password'] !== $userData['confirm_password']) {
            $errors['confirm_password'] = "Passwords do not match";
        }

        // User type validation
        if (empty($userData['userType'])) {
            $errors['userType'] = "User type is required";
        }

        // If there are errors, return them
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Hash password
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);

        // Determine table and columns based on user type
        switch($userData['userType']) {
            case 'member':
                $table = 'member';
                $columns = '(Username, email, Password, Phone_number)';
                $extraField = $userData['phone'] ?? '';
                break;
            case 'artist':
                $table = 'artist';
                $columns = '(username, email, Password, Specialty)';
                $extraField = $userData['specialty'] ?? '';
                break;
            case 'organizer':
                $table = 'event_organiser';
                $columns = '(username, email, Password, Organization)';
                $extraField = $userData['organization'] ?? '';
                break;
            case 'supplier':
                $table = 'supplier';
                $columns = '(username, email, Password, BusinessType)';
                $extraField = $userData['business_type'] ?? '';
                break;
            default:
                return ['success' => false, 'errors' => ['userType' => 'Invalid user type']];
        }

        // Prepare and execute insert query
        try {
            $this->db->query("INSERT INTO $table $columns VALUES (:name, :email, :password, :extra)");
            $this->db->bind(':name', $userData['name']);
            $this->db->bind(':email', $userData['email']);
            $this->db->bind(':password', $hashedPassword);
            $this->db->bind(':extra', $extraField);

            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'Registration successful'];
            } else {
                return ['success' => false, 'errors' => ['general' => 'Registration failed']];
            }
        } catch(PDOException $e) {
            return ['success' => false, 'errors' => ['general' => $e->getMessage()]];
        }
    }
}

// Registration Form Handler
class RegistrationHandler {
    private $userRegistration;

    public function __construct() {
        $this->userRegistration = new UserRegistration();
    }

    public function handleRegistration() {
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $userData = [
                'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                'password' => $_POST['password'], // Do not sanitize password
                'confirm_password' => $_POST['confirm_password'], // Do not sanitize password
                'userType' => filter_input(INPUT_POST, 'userType', FILTER_SANITIZE_STRING)
            ];

            // Add optional fields based on user type
            switch($userData['userType']) {
                case 'artist':
                    $userData['specialty'] = filter_input(INPUT_POST, 'specialty', FILTER_SANITIZE_STRING);
                    break;
                case 'organizer':
                    $userData['organization'] = filter_input(INPUT_POST, 'organization', FILTER_SANITIZE_STRING);
                    break;
                case 'supplier':
                    $userData['business_type'] = filter_input(INPUT_POST, 'business_type', FILTER_SANITIZE_STRING);
                    break;
                case 'member':
                    $userData['phone'] = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
                    break;
            }

            // Attempt registration
            $result = $this->userRegistration->register($userData);

            if ($result['success']) {
                // Redirect or show success message
                echo json_encode([
                    'status' => 'success', 
                    'message' => $result['message']
                ]);
                exit;
            } else {
                // Return errors
                echo json_encode([
                    'status' => 'error', 
                    'errors' => $result['errors']
                ]);
                exit;
            }
        }
    }
}

// Usage Example
$registrationHandler = new RegistrationHandler();
$registrationHandler->handleRegistration();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 8px; }
        .error { color: red; font-size: 0.8em; }
        button { width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .error {
            color: red;
            font-size: 0.8em;
            margin-top: 5px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .conditional-fields {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <form id="registrationForm" method="POST" action="register.php">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <div class="form-group">
            <label for="userType">User Type</label>
            <select id="userType" name="userType" required>
                <option value="">Select User Type</option>
                <option value="member">Member</option>
                <option value="artist">Artist</option>
                <option value="organizer">Event Organizer</option>
                <option value="supplier">Supplier</option>
            </select>
        </div>

        <!-- Conditional Fields -->
        <div id="artistFields" style="display:none;">
            <div class="form-group">
                <label for="specialty">Artist Specialty</label>
                <input type="text" id="specialty" name="specialty">
            </div>
        </div>

        <div id="organizerFields" style="display:none;">
            <div class="form-group">
                <label for="organization">Organization Name</label>
                <input type="text" id="organization" name="organization">
            </div>
        </div>

        <div id="supplierFields" style="display:none;">
            <div class="form-group">
                <label for="business_type">Business Type</label>
                <input type="text" id="business_type" name="business_type">
            </div>
        </div>

        <button type="submit">Register</button>
    </form>
</div>

    <script>
        document.getElementById('userType').addEventListener('change', function() {
            // Hide all additional fields
            document.getElementById('artistFields').style.display = 'none';
            document.getElementById('organizerFields').style.display = 'none';
            document.getElementById('supplierFields').style.display = 'none';

            // Show appropriate fields based on user type
            switch(this.value) {
                case 'artist':
                    document.getElementById('artistFields').style.display = 'block';
                    break;
                case 'organizer':
                    document.getElementById('organizerFields').style.display = 'block';
                    break;
                case 'supplier':
                    document.getElementById('supplierFields').style.display = 'block';
                    break;
            }
        });

        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch('', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    // Redirect or reset form
                } else {
                    // Display errors
                    console.log(data.errors);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>