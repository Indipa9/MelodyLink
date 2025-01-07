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
// Add this function to your existing database connection section
function getCurrentColorPreference($userId) {
    $pdo = connectDatabase();
    try {
        $stmt = $pdo->prepare("SELECT background_color FROM member WHERE member_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        // Default color if no preference set
        return $result['background_color'] ?? '#f4f6f9';
    } catch (PDOException $e) {
        // Fallback to default color in case of any database error
        return '#f4f6f9';
    }
}

// Check if user is logged in
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ./login/login.php');
        exit;
    }
}

// Fetch dashboard data
function getDashboardData($userId) {
    $pdo = connectDatabase();

    // Fetch member details including profile picture
    $stmt = $pdo->prepare("SELECT Username, email, Phone_number, profile_pic FROM member WHERE member_id = ?");
    $stmt->execute([$userId]);
    $memberDetails = $stmt->fetch();

    // Set default avatar if no profile picture is set
    $profilePic = !empty($memberDetails['profile_pic']) 
        ? './uploads/' . htmlspecialchars($memberDetails['profile_pic'])
        : '/images/default-avatar.png';

    // Prepare dashboard data (similar to the MVC implementation)
    $data = [
        'member_info' => [
            'username' => $memberDetails['Username'],
            'email' => $memberDetails['email'],
            'phone' => $memberDetails['Phone_number'],
            'profile_pic' => $profilePic  // Add profile picture to member info
        ],
        'recent_activities' => [
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
        ],
        'playlists' => [
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
        ],
        'recently_played' => [
            [
                'cover' => 'https://example.com/cover1.jpg',
                'title' => 'Echoes of Silence',
                'artist' => 'The Midnight',
            ],
            [
                'cover' => 'https://example.com/cover2.jpg',
                'title' => 'Neon Nights',
                'artist' => 'Synth Wave',
            ],
            [
                'cover' => 'https://example.com/cover3.jpg',
                'title' => 'Digital Love',
                'artist' => 'Electric Dreams',
            ]
        ],
        'recommended' => [
            [
                'cover' => 'https://example.com/cover4.jpg',
                'title' => 'Cyber City',
                'artist' => 'Retro Fusion',
            ],
            [
                'cover' => 'https://example.com/cover5.jpg',
                'title' => 'Pixel Dreams',
                'artist' => 'Synthwave Heroes',
            ],
            [
                'cover' => 'https://example.com/cover6.jpg',
                'title' => 'Retrowave Sunrise',
                'artist' => 'Neon Pulse',
            ]
        ]
    ];

    return $data;
}

// Main dashboard page
function renderDashboard() {
    // Check login
    checkLogin();

    // Get user ID from session
    $userId = $_SESSION['user_id'];
    $backgroundColor = getCurrentColorPreference($userId);

    // Fetch dashboard data
    $data = getDashboardData($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MelodyLink Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/lucide-icons/dist/umd/lucide.min.js"></script>
    <style>
         body {
        background-color: <?php echo htmlspecialchars($backgroundColor); ?> !important;
            /* background-color: #f4f6f9; */
        } 
        .gradient-bg {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }
    </style>
</head>
<body class="bg-gray-100" style="background-color: <?php echo htmlspecialchars($backgroundColor); ?>">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-xl p-6">
            <div class="text-center mb-8">
                <img src="<?php echo $data['member_info']['profile_pic']; ?>" alt="Profile" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover border-4 border-indigo-500">
                <h2 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($data['member_info']['username']); ?></h2>
                <p class="text-gray-500"><?php echo htmlspecialchars($data['member_info']['email']); ?></p>
            </div>

            <nav class="space-y-2">
                <a href="#" class="flex items-center text-indigo-600 bg-indigo-50 px-4 py-2 rounded-lg hover:bg-indigo-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard mr-3">
                        <rect width="7" height="9" x="3" y="3" rx="1"/>
                        <rect width="7" height="5" x="14" y="3" rx="1"/>
                        <rect width="7" height="9" x="14" y="12" rx="1"/>
                        <rect width="7" height="5" x="3" y="16" rx="1"/>
                    </svg>
                    Dashboard
                </a>
                <a href="#" class="flex items-center text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-music mr-3">
                        <path d="M21 15V6"/>
                        <path d="M18.5 18a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                        <path d="M12 12H3"/>
                        <path d="M16 6H3"/>
                        <path d="M12 18H3"/>
                    </svg>
                    My Playlists
                </a>
                <a href="settings.php" class="flex items-center text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user mr-3">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Profile
                </a>

                <a href="../community/communities.php" class="flex items-center text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg transition">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-network mr-3">
        <circle cx="12" cy="12" r="4"/>
        <line x1="12" y1="2" x2="12" y2="8"/>
        <line x1="12" y1="16" x2="12" y2="22"/>
        <line x1="2" y1="12" x2="8" y2="12"/>
        <line x1="16" y1="12" x2="22" y2="12"/>
        <line x1="4.93" y1="4.93" x2="9.17" y2="9.17"/>
        <line x1="14.83" y1="14.83" x2="19.07" y2="19.07"/>
        <line x1="4.93" y1="19.07" x2="9.17" y2="14.83"/>
        <line x1="14.83" y1="9.17" x2="19.07" y2="4.93"/>
    </svg>
    Communities
</a>


<a href="../Music Page/music.html" class="flex items-center text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg transition">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-compass mr-3">
        <circle cx="12" cy="12" r="10"/>
        <polygon points="16 12 12 8 8 12 12 16 16 12"/>
        <line x1="12" y1="2" x2="12" y2="22"/>
        <line x1="2" y1="12" x2="22" y2="12"/>
    </svg>
    Browse
</a>




                <a href="set.php" class="flex items-center text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings mr-3">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.08a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.08a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.08a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Settings
                </a>
                <a href="Logout.php" class="flex items-center text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out mr-3">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" x2="9" y1="12" y2="12"/>
                    </svg>
                    Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10 bg-gray-100">
            <div class="max-w-6xl mx-auto">
                <!-- Welcome Header -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome, <?php echo htmlspecialchars($data['member_info']['username']); ?>!</h1>
                        <p class="text-gray-500">Here's an overview of your musical journey.</p>
                    </div>
                    <div class="flex space-x-4">
                        <div class="text-center bg-indigo-50 p-4 rounded-lg">
                            <h3 class="text-xl font-bold text-indigo-600"><?php echo count($data['playlists']); ?></h3>
                            <p class="text-gray-500">Playlists</p>
                        </div>
                        <div class="text-center bg-green-50 p-4 rounded-lg">
                            <h3 class="text-xl font-bold text-green-600"><?php echo count($data['recent_activities']); ?></h3>
                            <p class="text-gray-500">Recent Activities</p>
                        </div>
                    </div>
                </div>

                <!-- Playlists Section -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold text-gray-800">My Playlists</h2>
                        <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center" >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus mr-2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                           <a href="../community/createcommunity.html" >Create Community</a>
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-6">
                        <?php foreach($data['playlists'] as $playlist): ?>
                            <div class="bg-white shadow-md rounded-lg p-4 hover:shadow-xl transition">
                                <div class="flex justify-between items-center mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($playlist['name']); ?></h3>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-music text-gray-500">
                                        <path d="M9 18V5l12-2v13"/>
                                        <circle cx="6" cy="18" r="3"/>
                                        <circle cx="18" cy="16" r="3"/>
                                    </svg>
                                </div>
                                <p class="text-gray-600 mb-1"><?php echo htmlspecialchars($playlist['songs_count']); ?> Songs</p>
                                <small class="text-gray-500">Created on <?php echo htmlspecialchars($playlist['created_at']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Recent Activity & Recommendations -->
                <div class="grid grid-cols-2 gap-8">
                    <!-- Recent Activity -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Recent Activity</h2>
                        <div class="space-y-4">
                            <?php foreach($data['recent_activities'] as $activity): ?>
                                <div class="bg-white shadow-md rounded-lg p-4 flex items-center">
                                    <div class="mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-activity text-indigo-600">
                                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($activity['details']); ?></p>
                                        <small class="text-gray-500"><?php echo htmlspecialchars($activity['date']); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Recommendations -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Recommended for You</h2>
                        <div class="space-y-4">
                            <?php foreach($data['recommended'] as $track): ?>
                                <div class="bg-white shadow-md rounded-lg p-4 flex items-center hover:shadow-xl transition">
                                    <img src="<?php echo htmlspecialchars($track['cover']); ?>" alt="<?php echo htmlspecialchars($track['title']); ?>" class="w-16 h-16 rounded-lg mr-4 object-cover">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($track['title']); ?></h3>
                                        <p class="text-gray-500"><?php echo htmlspecialchars($track['artist']); ?></p>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-play-circle text-indigo-600">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polygon points="10 8 16 12 10 16 10 8"/>
                                    </svg>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>










<!-- Delete Account Modal -->
<div id="deleteAccountModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete Account</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">
                    This action cannot be undone. This will permanently delete your account and remove all your data from our servers.
                </p>
                <input type="password" id="deleteAccountPassword" placeholder="Enter your password to confirm" class="mt-2 px-3 py-2 border rounded-lg w-full" />
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmDeleteAccount" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Delete My Account
                </button>
                <button id="cancelDeleteAccount" class="mt-3 px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Add Delete Account button to settings section
document.querySelector('a[href="Logout.php"]').insertAdjacentHTML('beforebegin', `
    <button id="openDeleteAccount" class="flex items-center text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg transition w-full text-left">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-x mr-3">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <line x1="17" y1="8" x2="22" y2="13"/>
            <line x1="22" y1="8" x2="17" y2="13"/>
        </svg>
        Delete Account
    </button>
`);

// Modal handling
const modal = document.getElementById('deleteAccountModal');
const openButton = document.getElementById('openDeleteAccount');
const cancelButton = document.getElementById('cancelDeleteAccount');
const confirmButton = document.getElementById('confirmDeleteAccount');
const passwordInput = document.getElementById('deleteAccountPassword');

openButton.onclick = () => modal.classList.remove('hidden');
cancelButton.onclick = () => {
    modal.classList.add('hidden');
    passwordInput.value = '';
};

confirmButton.onclick = () => {
    const password = passwordInput.value;
    
    if (!password) {
        alert('Please enter your password to confirm deletion');
        return;
    }
    
    if (confirm('Are you absolutely sure you want to delete your account? This cannot be undone.')) {
        fetch('delete_account.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_account&password=${encodeURIComponent(password)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Account deleted successfully. You will be redirected to the login page.');
                window.location.href = 'login/login.php';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the account');
        });
    }
};

// Close modal when clicking outside
window.onclick = (event) => {
    if (event.target === modal) {
        modal.classList.add('hidden');
        passwordInput.value = '';
    }
};
</script>









</body>
</html>
<?php
}

// Handle dashboard request
renderDashboard();
?>