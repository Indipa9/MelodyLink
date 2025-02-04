<?php
class m_users {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Generic method to find a user by email across different tables
    public function findUserByEmail($email, $userType) {
        // Determine the correct table based on user type
        switch($userType) {
            case 'member':
                $table = 'member';
                break;
            case 'artist':
                $table = 'artist';
                break;
            case 'organizer':
                $table = 'event_organiser';
                break;
            case 'supplier':
                $table = 'supplier';
                break;
            default:
                return false;
        }

        // Prepare and execute query
        $this->db->query("SELECT * FROM $table WHERE email = :email");
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Generic login method for different user types
    public function login($email, $password, $userType) {
        // Determine the correct table based on user type
        switch($userType) {
            case 'member':
                $table = 'member';
                break;
            case 'artist':
                $table = 'artist';
                break;
            case 'organizer':
                $table = 'event_organiser';
                break;
            case 'supplier':
                $table = 'supplier';
                break;
            default:
                return false;
        }
    
        // Prepare and execute query
        $this->db->query("SELECT * FROM $table WHERE email = :email");
        $this->db->bind(':email', $email);
    
        $row = $this->db->single();
    
        // Verify password
        if($row && password_verify($password, $row->Password)) {
            // Set user session
            $_SESSION['user_id'] = $row->member_id;
            $_SESSION['user_type'] = $userType;
            $_SESSION['username'] = $row->Username;
    
            return $row;
        } else {
            return false;
        }
    }

    // Generic registration method for different user types
    public function register($data, $userType) {
        // Determine the correct table based on user type
        switch($userType) {
            case 'member':
                $table = 'member';
                $columns = '(Username, email, Password,Phone_number)';
                break;
            case 'artist':
                $table = 'artist';
                $columns = '(username, email, Password, Specialty)';
                break;
            case 'organizer':
                $table = 'event_organiser';
                $columns = '(username, email, Password, Organization)';
                break;
            case 'supplier':
                $table = 'supplier';
                $columns = '(username, email, Password, BusinessType)';
                break;
            default:
                return false;
        }

        // Prepare query with dynamic table and columns
        $this->db->query("INSERT INTO $table $columns VALUES (:name, :email, :password, :extra)");
        
        // Bind parameters based on user type
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        
        // Add extra field based on user type
        switch($userType) {
            case 'artist':
                $this->db->bind(':extra', $data['specialty'] ?? '');
                break;
            case 'organizer':
                $this->db->bind(':extra', $data['organization'] ?? '');
                break;
            case 'supplier':
                $this->db->bind(':extra', $data['business_type'] ?? '');
                break;
            default:
                $this->db->bind(':extra', $data['Phone_number'] ?? '');
        }
        
        // Execute and return result
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }




    public function getDashboardData($userId, $userType) {
        $data = [];
    
        // Fetch member-specific details
        $this->db->query("SELECT Username, email, Phone_number FROM member WHERE member_id = :userId");
        $this->db->bind(':userId', $userId);
        $memberDetails = $this->db->single();
    
        $data['member_info'] = [
            'username' => $memberDetails->Username,
            'email' => $memberDetails->email,
            'phone' => $memberDetails->Phone_number
        ];
    
        // Fetch recent activities or interactions
        $data['recent_activities'] = [
            [
                'type' => 'playlist_created',
                'details' => 'Created "Summer Vibes" Playlist',
                'date' => '2024-01-15'
            ],
            [
                'type' => 'song_liked',
                'details' => 'Liked "Electric Dreams" by Synth Wave',
                'date' => '2024-01-10'
            ],
            [
                'type' => 'event_attended',
                'details' => 'Attended "Retrowave Night" Concert',
                'date' => '2024-01-05'
            ]
        ];
    
        // Fetch playlists
        $data['playlists'] = [
            [
                'name' => 'Summer Vibes',
                'songs_count' => 25,
                'created_at' => '2024-01-15'
            ],
            [
                'name' => 'Workout Mix',
                'songs_count' => 18,
                'created_at' => '2024-01-10'
            ],
            [
                'name' => 'Chill Tracks',
                'songs_count' => 30,
                'created_at' => '2024-01-02'
            ]
        ];
    
        // Continue with existing recommendations and top charts
        $data['recently_played'] = [
            [
                'cover' => 'https://storage.googleapis.com/a1aa/image/UNiXQ3pVXbKfF6Nq2i4S0wBjyWOAgLLDfbqfjT20epvM1DTPB.jpg',
                'title' => 'Echoes of Silence',
                'artist' => 'The Midnight',
            ],
            [
                'cover' => 'https://storage.googleapis.com/a1aa/image/it0H83K661KoEZAxd0G6q2kWvTZEfhu3An4P2rJHyk0rew0TA.jpg',
                'title' => 'Neon Nights',
                'artist' => 'Synth Wave',
            ],
            [
                'cover' => 'https://storage.googleapis.com/a1aa/image/7p2A7EFbpJqmO1105bBKYtchX6fBfEfjiwUmanhTfaJk1DTPB.jpg',
                'title' => 'Digital Love',
                'artist' => 'Electric Dreams',
            ]
        ];
    
        $data['recommended'] = [
            [
                'cover' => 'https://storage.googleapis.com/a1aa/image/f6F67hEA5YRaXqw7zHsMt3VKedgFaiRvfaWZ9HtThNhp6hpnA.jpg',
                'title' => 'Cyber City',
                'artist' => 'Retro Fusion',
            ],
            [
                'cover' => 'https://storage.googleapis.com/a1aa/image/6pzRGWYpFkZoDxXVUBe0HdXN5TDAKUQxiE9nlx5uWyhoew0TA.jpg',
                'title' => 'Pixel Dreams',
                'artist' => 'Synthwave Heroes',
            ],
            [
                'cover' => 'https://storage.googleapis.com/a1aa/image/3l5ujMaucSLGMNycDU7rvYz0BTZiyNhBZvJUAElg9eOtew0TA.jpg',
                'title' => 'Retrowave Sunrise',
                'artist' => 'Neon Pulse',
            ]
        ];
    
        return $data;
    }
    public function getUserData($userId) {
        $this->db->query("SELECT * FROM member WHERE member_id = :userId");
        $this->db->bind(':userId', $userId);
    
        return $this->db->single();
    }


    // Update user's profile picture
    public function updateProfilePic($userId, $profilePic) {
        $this->db->query("UPDATE member SET profile_pic = :profilePic WHERE member_id = :userId");
        $this->db->bind(':profilePic', $profilePic);
        $this->db->bind(':userId', $userId);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update user's basic information (username, email, password)
    public function updateUserInfo($userId, $username, $email, $password) {
        $this->db->query("UPDATE member SET Username = :username, email = :email, Password = :password WHERE member_id = :userId");
        $this->db->bind(':username', $username);
        $this->db->bind(':email', $email);
        $this->db->bind(':password', $password);
        $this->db->bind(':userId', $userId);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function loginAcrossAllTables($email, $password) {
        // Define all possible user tables and their corresponding types
        $tables = [
            'member' => 'member',
            'artist' => 'artist',
            'event_organiser' => 'organizer',
            'supplier' => 'supplier'
        ];
        
        // Try each table
        foreach ($tables as $table => $userType) {
            $this->db->query("SELECT * FROM $table WHERE email = :email");
            $this->db->bind(':email', $email);
            
            $row = $this->db->single();
            
            if ($row && password_verify($password, $row->Password)) {
                // Set session variables
                $_SESSION['user_id'] = $row->{$table . '_id'}; // Use appropriate ID column
                $_SESSION['user_type'] = $userType;
                $_SESSION['username'] = $row->Username ?? $row->username; // Handle different column names
                
                return $row;
            }
        }
        
        return false;
    }











}
?>