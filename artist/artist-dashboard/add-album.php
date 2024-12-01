<?php
include './includes/connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $album_name = $_POST["album_name"];
    $release_date = $_POST["release_date"];
    $genre = $_POST["genre"];
    $featured_artists = $_POST["featured_artists"];
    $artist_name = $_POST["artist_name"];
    $artist_id = $_POST["artist_id"];
     // Handle image upload
     $image_path = '';
     if(isset($_FILES['album_cover']) && $_FILES['album_cover']['error'] == 0) {
         $target_dir = "uploads/images/";
         $image_path = $target_dir . basename($_FILES["album_cover"]["name"]);
         move_uploaded_file($_FILES["album_cover"]["tmp_name"], $image_path);
     }
    // Handle music track upload
    $track_path = '';
    if(isset($_FILES['music_track']) && $_FILES['music_track']['error'] == 0) {
        $target_dir = "uploads/tracks/";
        $track_path = $target_dir . basename($_FILES["music_track"]["name"]);
        move_uploaded_file($_FILES["music_track"]["tmp_name"], $track_path);
    }

    //check if the album already exists
    $check_sql = "SELECT COUNT(*) FROM albums WHERE album_name = :album_name";
    $check_stmt = $conn->prepare($check_sql); 
    $check_stmt->bindParam(':album_name', $album_name);
    $check_stmt->execute();
    $name_count = $check_stmt->fetchColumn();
    if ($name_count > 0) {
        echo "Album already exists";
    }else{

        try {
            $sql = "INSERT INTO albums (album_name, release_date, genre, featured_artists, artist_name, artist_id, album_cover, music_track)
                    VALUES (:album_name, :release_date, :genre, :featured_artists, :artist_name,:artist_id, :album_cover, :music_track)";
                    
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':album_name' => $album_name,
                ':release_date' => $release_date,
                ':genre' => $genre,
                ':featured_artists' => $featured_artists,
                ':artist_name' => $artist_name,
                ':artist_id' => $artist_id,
                ':album_cover' => $image_path,
                ':music_track' => $track_path    
            ]);
            
            echo "Album added successfully!";
        } catch(PDOException $e) {
            echo "Database operation successful: " . $e->getMessage();
        }
    }
}
?>

