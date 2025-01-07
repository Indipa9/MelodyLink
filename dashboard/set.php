<?php
session_start();

// Include database connection function from dashboard.php
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
        header('Location: ./login/login.php');
        exit;
    }
}

// Fetch current color preference
function getCurrentColorPreference($userId) {
    $pdo = connectDatabase();
    $stmt = $pdo->prepare("SELECT background_color FROM member WHERE member_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    
    // Default color if no preference set
    return $result['background_color'] ?? '#f4f6f9';
}

// Update color preference
function updateColorPreference($userId, $color) {
    // Validate color (basic hex color validation)
    if (!preg_match('/^#([0-9A-Fa-f]{3}){1,2}$/', $color)) {
        return false;
    }

    $pdo = connectDatabase();
    $stmt = $pdo->prepare("UPDATE member SET background_color = ? WHERE member_id = ?");
    return $stmt->execute([$color, $userId]);
}

// Render settings page
function renderSettingsPage() {
    // Check login
    checkLogin();

    // Get user ID from session
    $userId = $_SESSION['user_id'];

    // Handle color update if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['background_color'])) {
        $color = $_POST['background_color'];
        $updateSuccess = updateColorPreference($userId, $color);
    }

    // Get current color preference
    $currentColor = getCurrentColorPreference($userId);

    // Predefined color palette
    $colorPalette = [
        '#f4f6f9',   // Default Light Gray
        '#E6F2FF',   // Light Blue
        '#F0F4F8',   // Soft Blue Gray
        '#FFF5F5',   // Light Pink
        '#F0FFF4',   // Mint Green
        '#FDF0D5',   // Light Peach
        '#E8F5E9',   // Soft Green
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MelodyLink - Settings</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/lucide-icons/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg p-8">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">Dashboard Settings</h1>
            
            <form method="post" class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold mb-4 text-gray-700">Background Color Preference</h2>
                    <p class="text-gray-500 mb-4">Choose a background color that suits your mood and style.</p>
                    
                    <div class="grid grid-cols-4 gap-4 mb-4">
                        <?php foreach ($colorPalette as $color): ?>
                            <label class="relative">
                                <input 
                                    type="radio" 
                                    name="background_color" 
                                    value="<?php echo $color; ?>" 
                                    class="absolute opacity-0"
                                    <?php echo ($currentColor === $color) ? 'checked' : ''; ?>
                                >
                                <div 
                                    class="w-full h-16 rounded-lg border-2 cursor-pointer transition-all 
                                    <?php echo ($currentColor === $color) 
                                        ? 'border-indigo-500 ring-2 ring-indigo-300' 
                                        : 'border-gray-200 hover:border-gray-300'; ?>"
                                    style="background-color: <?php echo $color; ?>"
                                ></div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mb-4">
                        <label for="custom_color" class="block text-gray-700 mb-2">
                            Or choose a custom color
                        </label>
                        <div class="flex items-center space-x-4">
                            <input 
                                type="color" 
                                id="custom_color" 
                                name="background_color" 
                                value="<?php echo $currentColor; ?>" 
                                class="w-16 h-16 rounded-lg border-2 border-gray-200 cursor-pointer"
                            >
                            <span class="text-gray-600">
                                Current selected color: 
                                <span class="font-bold" id="color_display"><?php echo $currentColor; ?></span>
                            </span>
                        </div>
                    </div>

                    <?php if (isset($updateSuccess) && $updateSuccess): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            Background color updated successfully!
                        </div>
                    <?php elseif (isset($updateSuccess) && !$updateSuccess): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            Invalid color selected. Please try again.
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition"
                    >
                        Save Preferences
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Optional: Update color display in real-time
        document.getElementById('custom_color').addEventListener('input', function() {
            document.getElementById('color_display').textContent = this.value;
        });
    </script>
</body>
</html>
<?php
}

// Handle settings page request
renderSettingsPage();
?>