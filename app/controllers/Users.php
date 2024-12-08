<?php
class Users extends Controller {
    public function __construct() {
        $this->userModel = $this->model('m_users');
    }

    public function index() {
        echo "This is the index method of the Users controller.";
    }

    public function register() {
        // Check if it's a POST request
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // Determine user type
            $userType = trim($_POST['userType'] ?? '');

            // Prepare data
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'userType' => $userType,
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'userType_err' => ''
            ];

            // Additional fields based on user type
            switch($userType) {
                case 'artist':
                    $data['specialty'] = trim($_POST['specialty'] ?? '');
                    break;
                case 'organizer':
                    $data['organization'] = trim($_POST['organization'] ?? '');
                    break;
                case 'supplier':
                    $data['business_type'] = trim($_POST['business_type'] ?? '');
                    break;
            }

            // Validate name
            if (empty($data['name'])) {
                $data['name_err'] = 'Please enter a name';
            }

            // Validate user type
            if (empty($userType)) {
                $data['userType_err'] = 'Please select a user type';
            }

            // Validate email
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter an email';
            } else {
                // Check if email is already registered for this user type
                if ($this->userModel->findUserByEmail($data['email'], $userType)) {
                    $data['email_err'] = 'Email is already registered for this user type';
                }
            }

            // Validate password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter a password';
            } elseif (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm the password';
            } else {
                if ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }
            }

            // Check if all errors are empty
            if (empty($data['name_err']) && empty($data['email_err']) 
                && empty($data['password_err']) && empty($data['confirm_password_err']) 
                && empty($data['userType_err'])) {
                
                // Hash the password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Register user with specific user type
                if ($this->userModel->register($data, $userType)) {
                    die('register_success,You are now registered and can log in');
                } else {
                    die('register_error,Something went wrong');
                }
            } else {
                // Load view with errors
                $this->view('users/v_register', $data);
            }
        } else {
            // Initial form load
            $data = [
                'name' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'userType' => '',
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'userType_err' => ''
            ];

            // Load view
            $this->view('users/v_register', $data);
        }
    }

    public function login() {
        // Check if it's a POST request (form submission)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    
            // Determine user type
            $userType = trim($_POST['userType'] ?? '');
    
            // Prepare data
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'userType' => $userType,
                'email_err' => '',
                'password_err' => '',
                'userType_err' => ''
            ];
    
            // Validate user type
            if (empty($userType)) {
                $data['userType_err'] = 'Please select a user type';
            }
    
            // Validate email
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            }
    
            // Validate password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }
    
            // Check if email exists for this user type
            if (!$this->userModel->findUserByEmail($data['email'], $userType)) {
                $data['email_err'] = 'No user found with this email for the selected user type';
            }
    
            // If no errors, attempt login
            if (empty($data['email_err']) && empty($data['password_err']) && empty($data['userType_err'])) {
                // Check and log in user
                $loggedInUser = $this->userModel->login($data['email'], $data['password'], $userType);
    
                if ($loggedInUser) {
                    // Redirect to the member dashboard
                    header('Location: ' . URLROOT . '/users/dashboard');
                    exit;
                } else {
                    $data['password_err'] = 'Password incorrect';
                    // Load view with errors
                    $this->view('users/v_login', $data);
                }
            } else {
                // Load view with errors
                $this->view('users/v_login', $data);
            }
        } else {
            // Initial form load
            $data = [
                'email' => '',
                'password' => '',
                'userType' => '',
                'email_err' => '',
                'password_err' => '',
                'userType_err' => ''
            ];
    
            // Load login view
            $this->view('users/v_login', $data);
        }
    }







    public function dashboard() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
            // Redirect to login if not logged in
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }
    
        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];
    
        // Fetch dashboard data based on user type
        $data = $this->userModel->getDashboardData($userId, $userType);
    
        // Add user information to the data
        $data['user_type'] = $userType;
        $data['username'] = $_SESSION['username'];
    
        // Load the appropriate dashboard view
        $this->view('users/v_member_dashboard', $data);
    }
    public function logout() {
        // Unset all session variables
        $_SESSION = array();

        // Destroy the session
        session_destroy();

        // Redirect to login page
        header('Location: ' . URLROOT . '/users/login');
        exit;
    }





public function getUserData($userId) {
    $this->db->query("SELECT * FROM member WHERE member_id = :userId");
    $this->db->bind(':userId', $userId);

    return $this->db->single();
}


    public function settings() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            // Redirect to login if not logged in
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Fetch user's current information
        $userData = $this->userModel->getUserData($userId);

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // Prepare data
            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'profile_pic' => $_FILES['profile_pic']['name'] ?? $userData->profile_pic,
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'profile_pic_err' => ''
            ];

            // Validate user input
            if (empty($data['username'])) {
                $data['username_err'] = 'Please enter a username.';
            }

            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter an email.';
            } elseif ($this->userModel->findUserByEmail($data['email'], 'member') && $data['email'] != $userData->email) {
                $data['email_err'] = 'Email is already registered.';
            }

            if (!empty($data['password'])) {
                if (empty($data['confirm_password'])) {
                    $data['confirm_password_err'] = 'Please confirm the password.';
                } elseif ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match.';
                }
            }

            // Handle profile picture upload
            if (!empty($_FILES['profile_pic']['name'])) {
                $target_dir = APPROOT . '/public/uploads/';
                $target_file = $target_dir . basename($_FILES['profile_pic']['name']);
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
                    $data['profile_pic'] = basename($_FILES['profile_pic']['name']);
                } else {
                    $data['profile_pic_err'] = 'There was an error uploading your profile picture.';
                }
            }

            // If no errors, update user information
            if (empty($data['username_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err']) && empty($data['profile_pic_err'])) {
                // Hash the password if it was changed
                if (!empty($data['password'])) {
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                } else {
                    $data['password'] = $userData->Password;
                }

                if ($this->userModel->updateUserInfo($userId, $data['username'], $data['email'], $data['password'])) {
                    if (!empty($_FILES['profile_pic']['name'])) {
                        $this->userModel->updateProfilePic($userId, $data['profile_pic']);
                    }
                    $_SESSION['username'] = $data['username'];
                    die('profile_updated, Your profile has been updated.');
                    // flash('profile_updated', 'Your profile has been updated.');
                    redirect('/users/settings');
                } else {
                    die('Something went wrong.');
                }
            } else {
                // Load the settings view with the data
                $this->view('users/v_settings', $data);
            }
        } else {
            // Initial form load
            $data = [
                'username' => $userData->Username,
                'email' => $userData->email,
                'profile_pic' => $userData->profile_pic,
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'profile_pic_err' => ''
            ];

            // Load the settings view
            $this->view('users/v_settings', $data);
        }
    }








}
?>