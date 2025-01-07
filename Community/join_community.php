<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'melodylink');

if ($conn->connect_error) {
    die(json_encode(['error' => $conn->connect_error]));
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$member_id = $data['member_id'];
$community_id = $data['community_id'];

// Insert into community_members table
$query = $conn->prepare("INSERT INTO community_members (member_id, community_id) VALUES (?, ?)");
$query->bind_param("ii", $member_id, $community_id);

if ($query->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => $conn->error]);
}

$conn->close();
?>
