<?php
session_start();

// Connect to the database
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "melodylink"; // database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assume member is logged in and you have their member_id in the session
$member_id = $_SESSION['member_id']; // Get the logged-in member ID
$community_id = $_POST['community_id'];
$action = $_POST['action']; // Join or Leave

if ($action === 'join') {
    // Check if the member is already in the community
    $check_query = "SELECT * FROM community_members WHERE member_id = ? AND community_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $member_id, $community_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Add member to the community
        $insert_query = "INSERT INTO community_members (member_id, community_id, joined_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ii", $member_id, $community_id);
        $stmt->execute();

        // Optionally increment the member_count in the communities table
        $update_query = "UPDATE communities SET member_count = member_count + 1 WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $community_id);
        $stmt->execute();

        echo "Joined";
    } else {
        echo "Already a member";
    }
} elseif ($action === 'leave') {
    // Remove member from the community
    $delete_query = "DELETE FROM community_members WHERE member_id = ? AND community_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("ii", $member_id, $community_id);
    $stmt->execute();

    // Optionally decrement the member_count in the communities table
    $update_query = "UPDATE communities SET member_count = member_count - 1 WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $community_id);
    $stmt->execute();

    echo "Left";
}

// Close connection
$conn->close();
?>
