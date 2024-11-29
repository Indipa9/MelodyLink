<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit;
}

// Connect to the database
$servername = "localhost";
$username = "root"; // Use your DB credentials
$password = "";
$dbname = "melodylink"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get community ID from the URL and validate it
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: communities.php");
    exit;
}
$community_id = (int)$_GET['id'];

// Fetch community details with prepared statement
$community_query = "SELECT * FROM communities WHERE id = ?";
$stmt = $conn->prepare($community_query);
$stmt->bind_param("i", $community_id);
$stmt->execute();
$community_result = $stmt->get_result();

if ($community_result->num_rows === 0) {
    header("Location: communities.php");
    exit;
}

$community = $community_result->fetch_assoc();

// Check if user is already a member of the community
$member_check_query = "SELECT * FROM community_members WHERE community_id = ? AND member_id = ?";
$stmt = $conn->prepare($member_check_query);
$stmt->bind_param("ii", $community_id, $_SESSION['member_id']);
$stmt->execute();
$is_member = $stmt->get_result()->num_rows > 0;

// Fetch posts from the community
$posts_query = "SELECT p.post_id, p.post_content, p.image, p.created_at, p.member_id, m.Username 
                FROM posts p
                JOIN member m ON p.member_id = m.member_id
                WHERE p.community_id = ?
                ORDER BY p.created_at DESC";
$stmt = $conn->prepare($posts_query);
$stmt->bind_param("i", $community_id);
$stmt->execute();
$posts_result = $stmt->get_result();
$posts = [];
while ($row = $posts_result->fetch_assoc()) {
    $posts[] = $row;
}

// Check if form is submitted for a post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify user is a member before allowing posts
    if (!$is_member) {
        die("You must be a member to post in this community");
    }

    $content = trim($_POST['content']);
    if (empty($content)) {
        die("Post content cannot be empty");
    }

    $imagePath = NULL; // Initialize as null

    // Check if an image was uploaded
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['post_image']['type'], $allowedTypes)) {
            die("Invalid file type. Only JPG, PNG, and GIF are allowed.");
        }

        if ($_FILES['post_image']['size'] > $maxFileSize) {
            die("File is too large. Maximum size is 5MB.");
        }

        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Generate a safe filename
        $imageExtension = pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid() . '_' . time() . '.' . $imageExtension;
        $targetFilePath = $targetDir . $imageName;

        if (!move_uploaded_file($_FILES['post_image']['tmp_name'], $targetFilePath)) {
            die("Error uploading the image.");
        }
        
        $imagePath = $targetFilePath;
    }

    // Insert the post with the content and image (if any)
    $stmt = $conn->prepare("INSERT INTO posts (community_id, member_id, post_content, image, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiss", $community_id, $_SESSION['member_id'], $content, $imagePath);
    
    if (!$stmt->execute()) {
        die("Error creating post: " . $stmt->error);
    }

    // Redirect to avoid form resubmission
    header("Location: community.php?id=$community_id");
    exit;
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($community['name']); ?> - MelodyLink</title>
    <style>
        /* ... existing styles ... */
        .error-message {
            color: #ff0000;
            margin: 10px 0;
            padding: 10px;
            background-color: #ffe6e6;
            border-radius: 4px;
        }
        .success-message {
            color: #008000;
            margin: 10px 0;
            padding: 10px;
            background-color: #e6ffe6;
            border-radius: 4px;
        }
        .post-form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .post-form textarea {
            width: 100%;
            min-height: 100px;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .file-input {
            margin: 10px 0;
        }
        .post-meta {
            color: #666;
            font-size: 0.9em;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($community['name']); ?></h1>
        <div class="community-details">
            <p><?php echo htmlspecialchars($community['description']); ?></p>
            <?php if (!empty($community['image'])): ?>
                <img src="<?php echo htmlspecialchars($community['image']); ?>" 
                     alt="<?php echo htmlspecialchars($community['name']); ?>" 
                     class="community-image">
            <?php endif; ?>
            <p><?php echo number_format($community['member_count']); ?> members • 
               <?php echo number_format($community['posts_count']); ?> posts</p>

            <!-- Join Community Button -->
            <?php if (!$is_member): ?>
                <button class="btn-join" data-community-id="<?php echo $community_id; ?>">
                    Join Community
                </button>
            <?php else: ?>
                <button class="btn-leave" data-community-id="<?php echo $community_id; ?>">
                    Leave Community
                </button>
            <?php endif; ?>
        </div>

        <?php if ($is_member): ?>
            <!-- Post Form -->
            <div class="post-form">
                <h3>Create a Post</h3>
                <form action="community.php?id=<?php echo $community_id; ?>" 
                      method="POST" 
                      enctype="multipart/form-data">
                    <textarea name="content" 
                              placeholder="What's on your mind?" 
                              required></textarea>
                    <div class="file-input">
                        <label for="post_image">Add an image (optional):</label>
                        <input type="file" 
                               id="post_image" 
                               name="post_image" 
                               accept="image/jpeg,image/png,image/gif">
                    </div>
                    <button type="submit" class="submit-button">Post</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- List of Posts -->
        <div class="posts-list">
            <?php if (empty($posts)): ?>
                <p>No posts in this community yet. Be the first to post!</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <h4><?php echo htmlspecialchars($post['Username']); ?></h4>
                        <p class="post-content">
                            <?php echo nl2br(htmlspecialchars($post['post_content'])); ?>
                        </p>

                        <?php if (!empty($post['image'])): ?>
                            <img src="<?php echo htmlspecialchars($post['image']); ?>" 
                                 alt="Post Image">
                        <?php endif; ?>

                        <div class="post-meta">
                            <span>Posted on: <?php echo date('F j, Y \a\t g:i a', 
                                  strtotime($post['created_at'])); ?></span>
                            
                            <?php if ($_SESSION['member_id'] == $post['member_id']): ?>
                                <a href="edit_post.php?post_id=<?php echo $post['post_id']; ?>&community_id=<?php echo $community_id; ?>">
                                    Edit
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Join/Leave community functionality
        document.addEventListener('DOMContentLoaded', function() {
            const joinButton = document.querySelector('.btn-join');
            const leaveButton = document.querySelector('.btn-leave');

            if (joinButton) {
                joinButton.addEventListener('click', function() {
                    const communityId = this.getAttribute('data-community-id');
                    joinCommunity(communityId);
                });
            }

            if (leaveButton) {
                leaveButton.addEventListener('click', function() {
                    const communityId = this.getAttribute('data-community-id');
                    leaveCommunity(communityId);
                });
            }
        });

        async function joinCommunity(communityId) {
            try {
                const response = await fetch('join_community.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ community_id: communityId })
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to join community');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while joining the community');
            }
        }

        async function leaveCommunity(communityId) {
            if (!confirm('Are you sure you want to leave this community?')) {
                return;
            }

            try {
                const response = await fetch('leave_community.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ community_id: communityId })
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to leave community');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while leaving the community');
            }
        }
    </script>
</body>
</html>