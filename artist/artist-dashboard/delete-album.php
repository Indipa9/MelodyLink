<?php
include './includes/connect.php';
if (!isset($_GET['album_id'])) {
    echo "album id is not provided";
    exit;
}
$album_id = $_GET['album_id'];

$sql = "DELETE FROM albums WHERE album_id = :album_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':album_id', $album_id);

try {
    $stmt->execute();
    echo "Album deleted successfully!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}


?>