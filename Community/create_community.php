<?php
// Database connection settings
$host = 'localhost';
$dbname = 'melodyLink';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $community_name = $conn->real_escape_string($_POST['community_name']);
    $community_description = $conn->real_escape_string($_POST['community_description']);

    // Image upload handling
    $target_dir = "uploads/"; // Directory where images will be saved
    $target_file = $target_dir . basename($_FILES["community_image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is a valid image type
    $valid_extensions = array("jpg", "jpeg", "png", "gif");
    if (in_array($imageFileType, $valid_extensions)) {
        // Move the uploaded image to the target directory
        if (move_uploaded_file($_FILES["community_image"]["tmp_name"], $target_file)) {
            // Prepare SQL query to insert community data into the database
            $sql = "INSERT INTO communities (name, description, image) VALUES ('$community_name', '$community_description', '$target_file')";

            // Execute the query
            if ($conn->query($sql) === TRUE) {
                echo "New community created successfully!";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your image.";
        }
    } else {
        echo "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
    }
}

// Close the database connection
$conn->close();
?>
