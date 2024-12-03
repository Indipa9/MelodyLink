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
        } elseif (strlen($userData['password']) < 8) {
            $errors['password'] = "Password must be at least 8 characters long";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MelodyLink Registration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7ff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: #617dff;
            color: white;
            padding: 1rem 0;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 2rem;
            margin: 0;
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .container {
            background-color: white;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(97, 125, 255, 0.1);
            width: 100%;
            max-width: 500px;
            position: relative;
        }

        .container h2 {
            color: #617dff;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e1e5ff;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #617dff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(97, 125, 255, 0.1);
        }

        .conditional-fields {
            background-color: #f8faff;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            border: 1px solid #e1e5ff;
        }

        .error {
            color: #ff4444;
            font-size: 0.85rem;
            margin-top: 0.3rem;
        }

        button {
            width: 100%;
            padding: 1rem;
            background-color: #617dff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 1rem;
        }

        button:hover {
            background-color: #4b66ff;
        }

        .signup-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #617dff;
            text-decoration: none;
            font-weight: 500;
        }

        .signup-link:hover {
            text-decoration: underline;
        }

        .footer {
            background-color: #617dff;
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: auto;
        }

        /* Password strength indicator */
        .password-strength {
            height: 4px;
            background-color: #e1e5ff;
            margin-top: 0.5rem;
            border-radius: 2px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>MelodyLink</h1>
    </header>

    <div class="main-content">
        <div class="container">
            <h3><center><a href="./login/login.php">Already have an account? Login!</a></h3></center>
            <h2>Create Your Account</h2>
            
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
<!-- <a href="./login/login.php" class="signup-link">Already have an account? Login</a> -->


<script>
// Password validation criteria
const passwordCriteria = {
    minLength: 8,
    hasUpperCase: /[A-Z]/,
    hasLowerCase: /[a-z]/,
    hasNumbers: /[0-9]/,
    hasSpecialChar: /[!@#$%^&*(),.?":{}|<>]/
};

// Add password strength indicators to password field
document.getElementById('password').insertAdjacentHTML('afterend', `
    <div class="password-strength">
        <div class="password-strength-bar"></div>
    </div>
    <div class="password-criteria">
        <p class="criteria length">✘ At least 8 characters</p>
        <p class="criteria uppercase">✘ At least one uppercase letter</p>
        <p class="criteria lowercase">✘ At least one lowercase letter</p>
        <p class="criteria number">✘ At least one number</p>
        <p class="criteria special">✘ At least one special character</p>
    </div>
`);

// Add password match indicator after confirm password field
document.getElementById('confirm_password').insertAdjacentHTML('afterend', `
    <p class="password-match-indicator"></p>
`);

// Add styles for password validation
const style = document.createElement('style');
style.textContent = `
    .password-criteria {
        margin-top: 10px;
        font-size: 0.85rem;
        color: #666;
    }
    .criteria {
        margin: 5px 0;
    }
    .criteria.valid {
        color: #2ecc71;
    }
    .criteria.invalid {
        color: #e74c3c;
    }
    .password-match-indicator {
        font-size: 0.85rem;
        margin-top: 5px;
    }
    .password-strength-bar {
        height: 100%;
        width: 0;
        transition: width 0.3s ease, background-color 0.3s ease;
    }
`;
document.head.appendChild(style);

// Function to validate password strength
function validatePassword(password) {
    const criteria = {
        length: password.length >= passwordCriteria.minLength,
        uppercase: passwordCriteria.hasUpperCase.test(password),
        lowercase: passwordCriteria.hasLowerCase.test(password),
        number: passwordCriteria.hasNumbers.test(password),
        special: passwordCriteria.hasSpecialChar.test(password)
    };

    // Update criteria indicators
    document.querySelector('.criteria.length').className = 
        `criteria length ${criteria.length ? 'valid' : 'invalid'}`;
    document.querySelector('.criteria.uppercase').className = 
        `criteria uppercase ${criteria.uppercase ? 'valid' : 'invalid'}`;
    document.querySelector('.criteria.lowercase').className = 
        `criteria lowercase ${criteria.lowercase ? 'valid' : 'invalid'}`;
    document.querySelector('.criteria.number').className = 
        `criteria number ${criteria.number ? 'valid' : 'invalid'}`;
    document.querySelector('.criteria.special').className = 
        `criteria special ${criteria.special ? 'valid' : 'invalid'}`;

    // Calculate password strength
    const strength = Object.values(criteria).filter(Boolean).length;
    const strengthBar = document.querySelector('.password-strength-bar');
    strengthBar.style.width = `${(strength / 5) * 100}%`;

    // Set color based on strength
    const colors = ['#e74c3c', '#e67e22', '#f1c40f', '#2ecc71', '#27ae60'];
    strengthBar.style.backgroundColor = colors[strength - 1] || '#e74c3c';

    return strength === 5; // Returns true if all criteria are met
}

// Function to check if passwords match
function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const matchIndicator = document.querySelector('.password-match-indicator');

    if (confirmPassword) {
        if (password === confirmPassword) {
            matchIndicator.textContent = '✓ Passwords match';
            matchIndicator.style.color = '#2ecc71';
            return true;
        } else {
            matchIndicator.textContent = '✘ Passwords do not match';
            matchIndicator.style.color = '#e74c3c';
            return false;
        }
    } else {
        matchIndicator.textContent = '';
        return false;
    }
}

// Add event listeners
document.getElementById('password').addEventListener('input', function() {
    validatePassword(this.value);
    checkPasswordMatch();
});

document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

// Update form submission to include password validation
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const isPasswordValid = validatePassword(password);
    const doPasswordsMatch = checkPasswordMatch();

    if (!isPasswordValid || !doPasswordsMatch) {
        e.preventDefault();
        alert('Please ensure your password meets all requirements and matches the confirmation.');
        return;
    }

    // Continue with existing form submission code
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