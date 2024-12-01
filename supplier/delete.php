<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM equipment WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: My Supplies.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
