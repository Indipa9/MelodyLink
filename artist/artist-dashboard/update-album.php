<?php
include './includes/connect.php';
// initialize variables
$album = [];

function getAlbum($conn, $album_id) {
    $sql = "SELECT * FROM albums WHERE album_id = :album_id";
    $stmt = $conn-> prepare($sql);
    $stmt->bindParam(':album_id', $album_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
    }

if (!isset($_GET['album_id'])) {
    echo "album id is not provided";
    exit;
}
$album_id = $_GET['album_id'];
$album = getAlbum($conn, $album_id);

//update album
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $album_id = $_POST['album_id'];
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

    $sql = "UPDATE albums SET album_name = :album_name, release_date = :release_date, genre = :genre, featured_artists = :featured_artists, artist_name = :artist_name, artist_id = :artist_id, album_cover = :album_cover, music_track = :music_track WHERE album_id = :album_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':album_id', $album_id);
    $stmt->bindParam(':album_name', $album_name);
    $stmt->bindParam(':release_date', $release_date);
    $stmt->bindParam(':genre', $genre);
    $stmt->bindParam(':featured_artists', $featured_artists);
    $stmt->bindParam(':artist_name', $artist_name);
    $stmt->bindParam(':artist_id', $artist_id);
    $stmt->bindParam(':album_cover', $image_path);
    $stmt->bindParam(':music_track', $track_path);

    try {
        $stmt->execute();
        echo "Album updated successfully!";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=-1">
  <title>Update Album</title>
  <link rel="stylesheet" href="artist-dashboard.css">
</head>
<body>

<div class="form-container">
  <h2>Update Album</h2>
  <form id="album-form" enctype="multipart/form-data" method="POST">
    <input type="hidden" name="album_id" value="<?php echo $album['album_id']; ?>">
     <div class="form-group">
          <label for="album_name">Album Name:</label>
          <input type="text" id="album_name" name="album_name" placeholder="Enter album name" value=<?php echo $album ['album_name'] ?> required>          
      </div>

      <div class="form-group">
          <label for="release_date">Release Date:</label>
          <input type="date" id="release_date" name="release_date" value=<?php echo $album ['release_date'] ?>required>
      </div>

      <div class="form-group">
          <label for="genre">Genre:</label><select name="genre" id="genre" value=<?php echo $album ['genre'] ?> required>
            <option value="Pop">Pop</option>
            <option value="Sri Lankan Classical">Sri Lankan Classical</option>
            <option value="Rock">Rock</option>
            <option value="Indian Classical">Indian Classical</option>
            <option value="Metal">Metal</option>
            <option value="Hip Hop">Hip Hop</option>
            <option value="Rhythm and Blues">Rhythm and Blues</option> 
          </select>
      </div>

      <div class="form-group">
          <label for="artist_name">Artist:</label>
          <input type="text" id="artist_name" name="artist_name" placeholder="Enter artist name" value=<?php echo $album ['artist_name'] ?> required>
      </div>

      <div class="form-group">
          <label for="featured_artists">Featured Artists:</label>
          <input type="textarea" id="featured_artists" name="featured_artists" placeholder="Enter featured artists"  value=<?php echo $album ['featured_artists'] ?>>
        </div>

      <div class="form-group">
          <label for="artist_id">Artist ID:</label>
          <input type="text" id="artist_id" name="artist_id" placeholder="Enter artist id" value=<?php echo $album ['artist_id'] ?> required>
      </div>

      <div class="form-group">
          <label for="album_cover">Album Image:</label>
          <input type="file" id="album_cover" name="album_cover" accept="image/*" value=<?php echo $album ['album_cover'] ?> required>
      </div>

      <div class="form-group">
          <label for="music_track">Music Track (Audio File .mp3/.wav/.AAC/FLAC/ALAC):</label>
          <input type="file" id="music_track" name="music_track" accept="audio/*" value=<?php echo $album ['music_track'] ?>required>
      </div>

      <div class="form-actions">
          <button type="submit" class="submit-btn"> Update Album
              <!-- <?php echo ($mode === 'edit') ? 'Update Album' : 'Add Album'; ?> -->
          </button>
      </div>
  </form>
</div>

<script>
document.getElementById('album-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const albumName = document.getElementById('album_name').value.trim();
    const releaseDate = document.getElementById('release_date').value;
    const genre = document.getElementById('genre').value;
    const artistName = document.getElementById('artist_name').value.trim();
    const artistId = document.getElementById('artist_id').value.trim();
    const albumCover = document.getElementById('album_cover');
    const musicTrack = document.getElementById('music_track');

    let isValid = true;
    const errors = [];

    // Album name validation
    if (albumName.length < 1 || albumName.length > 100) {
        errors.push('Album name must be between 1 and 100 characters');
        isValid = false;
    }

    // Release date validation
    const currentDate = new Date();
    const selectedDate = new Date(releaseDate);
    if (!releaseDate || selectedDate > currentDate) {
        errors.push('Please select a valid release date');
        isValid = false;
    }

    // Genre validation
    const validGenres = ['Pop', 'Sri Lankan Classical', 'Rock', 'Indian Classical', 'Metal', 'Hip Hop', 'Rhythm and Blues'];
    if (!validGenres.includes(genre)) {
        errors.push('Please select a valid genre');
        isValid = false;
    }

    // Artist name validation
    if (artistName.length < 1 || artistName.length > 100) {
        errors.push('Artist name must be between 1 and 100 characters');
        isValid = false;
    }

    // Artist ID validation
    if (!/^\d+$/.test(artistId)) {
        errors.push('Artist ID must contain only numbers');
        isValid = false;
    }

    // // File validations
    // if (albumCover.files.length > 0) {
    //     const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    //     if (!validImageTypes.includes(albumCover.files[0].type)) {
    //         errors.push('Please select a valid image file (JPEG, PNG, or JPG)');
    //         isValid = false;
    //     }
    //     if (albumCover.files[0].size > 5242880) { // 5MB limit
    //         errors.push('Album cover image must be less than 5MB');
    //         isValid = false;
    //     }
    // }

    // if (musicTrack.files.length > 0) {
    //     const validAudioTypes = ['audio/mp3', 'audio/wav', 'audio/aac', 'audio/flac', 'audio/x-m4a'];
    //     if (!validAudioTypes.includes(musicTrack.files[0].type)) {
    //         errors.push('Please select a valid audio file (MP3, WAV, AAC, FLAC, or ALAC)');
    //         isValid = false;
    //     }
    //     if (musicTrack.files[0].size > 20971520) { // 20MB limit
    //         errors.push('Music track must be less than 20MB');
    //         isValid = false;
    //     }
    // }

    if (!isValid) {
        const errorMessage = errors.join('\n');
        alert(errorMessage);
    } else {
        this.submit();
    }
});
</script>


</body>
</html>