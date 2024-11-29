<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "melodylink";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$error_message = '';
$success_message = '';
$post = null;
$community_id = null;

// Validate post_id and community_id from URL
if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id']) || 
    !isset($_GET['community_id']) || !is_numeric($_GET['community_id'])) {
    header("Location: index.php");
    exit;
}

$post_id = (int)$_GET['post_id'];
$community_id = (int)$_GET['community_id'];

// Fetch the post and verify ownership
$stmt = $conn->prepare("
    SELECT p.*, c.name as community_name 
    FROM posts p
    JOIN communities c ON p.community_id = c.id
    WHERE p.post_id = ? AND p.member_id = ?
");
$stmt->bind_param("ii", $post_id, $_SESSION['member_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: community.php?id=" . $community_id);
    exit;
}

$post = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);
    
    if (empty($content)) {
        $error_message = "Post content cannot be empty";
    } else {
        $imagePath = $post['image']; // Keep existing image by default
        
        // Handle image upload if new image is provided
        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($_FILES['post_image']['type'], $allowedTypes)) {
                $error_message = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
            } elseif ($_FILES['post_image']['size'] > $maxFileSize) {
                $error_message = "File is too large. Maximum size is 5MB.";
            } else {
                $targetDir = "uploads/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                // Generate safe filename
                $imageExtension = pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION);
                $imageName = uniqid() . '_' . time() . '.' . $imageExtension;
                $targetFilePath = $targetDir . $imageName;

                if (move_uploaded_file($_FILES['post_image']['tmp_name'], $targetFilePath)) {
                    // Delete old image if it exists
                    if (!empty($post['image']) && file_exists($post['image'])) {
                        unlink($post['image']);
                    }
                    $imagePath = $targetFilePath;
                } else {
                    $error_message = "Error uploading the image.";
                }
            }
        }

        // Remove image if requested
        if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
            if (!empty($post['image']) && file_exists($post['image'])) {
                unlink($post['image']);
            }
            $imagePath = NULL;
        }

        if (empty($error_message)) {
            // Update the post
            $stmt = $conn->prepare("
                UPDATE posts 
                SET post_content = ?, image = ?, updated_at = NOW() 
                WHERE post_id = ? AND member_id = ?
            ");
            $stmt->bind_param("ssii", $content, $imagePath, $post_id, $_SESSION['member_id']);
            
            if ($stmt->execute()) {
                $success_message = "Post updated successfully!";
                // Refresh post data
                $stmt = $conn->prepare("SELECT * FROM posts WHERE post_id = ?");
                $stmt->bind_param("i", $post_id);
                $stmt->execute();
                $post = $stmt->get_result()->fetch_assoc();
            } else {
                $error_message = "Error updating post: " . $stmt->error;
            }
        }
    }
}

// Handle post deletion
if (isset($_POST['delete_post'])) {
    // Delete the post's image if it exists
    if (!empty($post['image']) && file_exists($post['image'])) {
        unlink($post['image']);
    }

    // Delete the post
    $stmt = $conn->prepare("DELETE FROM posts WHERE post_id = ? AND member_id = ?");
    $stmt->bind_param("ii", $post_id, $_SESSION['member_id']);
    
    if ($stmt->execute()) {
        header("Location: community.php?id=" . $community_id);
        exit;
    } else {
        $error_message = "Error deleting post: " . $stmt->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - MelodyLink</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #dae0e6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #1a1a1b;
            margin-bottom: 20px;
        }
        .error-message {
            color: #ff0000;
            background-color: #ffe6e6;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .success-message {
            color: #008000;
            background-color: #e6ffe6;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        textarea {
            width: 100%;
            min-height: 150px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }
        .current-image {
            margin: 20px 0;
        }
        .current-image img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .button-group {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary {
            background-color: #0079d3;
            color: #ffffff;
        }
        .btn-danger {
            background-color: #ff4444;
            color: #ffffff;
        }
        .btn-secondary {
            background-color: #808080;
            color: #ffffff;
        }
        .checkbox-group {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Post in <?php echo htmlspecialchars($post['community_name']); ?></h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form action="edit_post.php?post_id=<?php echo $post_id; ?>&community_id=<?php echo $community_id; ?>" 
              method="POST" 
              enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="content">Post Content:</label>
                <textarea name="content" id="content" required><?php echo htmlspecialchars($post['post_content']); ?></textarea>
            </div>

            <?php if (!empty($post['image'])): ?>
                <div class="current-image">
                    <p>Current Image:</p>
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Current post image">
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="remove_image" value="1">
                            Remove current image
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="post_image">Update Image (optional):</label>
                <input type="file" 
                       id="post_image" 
                       name="post_image" 
                       accept="image/jpeg,image/png,image/gif">
                <p><small>Maximum file size: 5MB. Allowed formats: JPG, PNG, GIF</small></p>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">Update Post</button>
                <a href="community.php?id=<?php echo $community_id; ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" 
                        name="delete_post" 
                        class="btn btn-danger" 
                        onclick="return confirm('Are you sure you want to delete this post? This action cannot be undone.')">
                    Delete Post
                </button>
            </div>
        </form>
    </div>

    <script>
        // Prevent form submission if both remove_image is checked and new image is selected
        document.querySelector('form').addEventListener('submit', function(e) {
            const removeImageCheckbox = document.querySelector('input[name="remove_image"]');
            const newImageInput = document.querySelector('input[name="post_image"]');
            
            if (removeImageCheckbox && removeImageCheckbox.checked && newImageInput.files.length > 0) {
                e.preventDefault();
                alert('Please either remove the current image or upload a new one, not both.');
            }
        });
    </script>
</body>
</html>