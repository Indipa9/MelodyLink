<?php
include './includes/connect.php';

// Fetch albums for the artist
function getAlbumsForArtist($conn) {
    $sql = "SELECT * FROM albums";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Fetch albums
$albums = getAlbumsForArtist($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Artist Dashboard</title>
<link rel="stylesheet" href="artist-dashboard.css?v=1.0">
</head>
<body>
  <header class="header">
    <div class="logo">
      <img src="logo.png" alt="MelodyLink Logo">
      <h1>MelodyLink</h1>
    </div>
    <button class="add-new-btn" onclick="openModal()">+ Add New Album</button>

     <!-- Small Popup Window -->
  <div class="small-modal-overlay" id="small-modal-overlay">
    <div class="small-modal">
      <h2>Add New Album</h2>
      <form id="add-album-form" enctype="multipart/form-data" method="post" action= "add-album.php">
        <div class="form-group">
          <label for="album_name">Album Name:</label>
          <input type="text" id="album_name" name="album_name" placeholder="Enter album name" required>
        </div>
 
        <div class="form-group">
          <label for="release_date">Release Date:</label>
          <input type="date" id="release_date" name="release_date" required>
        </div>

        <div class="form-group">
          <label for="genre">Genre:</label>
          <select name="genre" id="genre" required>
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
          <input type="text" id="artist_name" name="artist_name" placeholder="Enter artist name" required >
        </div>

        <div class="form-group">
          <label for="featured_artists">Featured Artists:</label>
          <input type="textarea" id="featured_artists" name="featured_artists" placeholder="Enter featured artists" >
        </div>

        <div class="form-group">
          <label for="artist_id">Artist ID:</label>
          <input type="int" id="artist_id" name="artist_id" placeholder="Enter artist ID" required>
        </div>

        <div class="form-group">
          <label for="album_cover">Album Image:</label>
          <input type="file" id="album_cover" name="album_cover" accept="image/*"  required>
        </div>

        <div class="form-group">
          <label for="music_track">Music Track (Audio File .mp3/.wav/.AAC/FLAC/ALAC):</label>
          <input type="file" id="music_track" name="music_track" accept="audio/*" required>
        </div>

        <div class="modal-actions">
          <button type="button" class="close-btn" onclick="closeModal()">Cancel</button>
          <button type="submit" class="add-btn">Add Album</button>

        </div>
      </form>
    </div>
  </div>

  <script>
    function openModal() {
      document.getElementById('small-modal-overlay').style.display = 'flex';
    }

    function closeModal() {
      document.getElementById('small-modal-overlay').style.display = 'none';
    }
  </script>
    <nav class="navbar">
      <a href="../../home/Landingpage.html">Home</a>
      <a href="sub parts/Communities.html">Communities</a>
      <a href="sub parts/Requests.html">Requests</a>
      <a href="#">Reviews & Ratings</a>
      <a href="#" class="logout">Logout</a>
    </nav>
    <div class="profile-icon">
      <a href="artist_profile.html">
          <img src="profile-icon.png" alt="Profile Icon">
      </a>
  </div>
  </header>

  <main class="main-container">
    <section class="dashboard-overview">
      <h1>Welcome to Your Creative Hub!</h1>
      <div class="stats">
        <div class="stat-item2">
          <h3>Which Album Dominates the Spotlight Each Month?</h3>
          <div class="graph">
          <img src="graph.png" alt="graph" style="max-width: 100%; max-height: 100%; object-fit: contain;"><br>
          <div class="checkbox-group">
            <label><input type="checkbox" value="Album A"> Hip Hop</label>
            <label><input type="checkbox" value="Album B"> Rhythm and Blues</label>
            <label><input type="checkbox" value="Album C"> Pop</label>
          </div>
          </div>
        </div>

        <div class="stat-item">
          <h3>Popularity Ranking</h3>
          <img src="rank.png" alt="graph" style="max-width: 50%; max-height: 50%; object-fit: contain;"><br>
          <h1>#005</h1>
          <P style ="font-size:10pt;">MelodyLink Rank is calculated based on the engagement metrics of social media and music channels, including factors such as likes, shares, comments, and streaming counts. This comprehensive analysis provides a clear picture of an artist's popularity and influence across various platforms.</P>
        </div>
        
        <div class="stat-item">
          <h3>Audience Overview</h3>
          <img src="audience.jpg" alt="audience" style="max-width: 100%; max-height: 100%; object-fit: contain;">
          <h4 style ="text-align:left;">Top Fanbase</h4><hr><br>
          <p style ="font-size:10pt; text-align:left;">YOUTUBE - 277K Subscribers</p>
          <p style ="font-size:10pt; text-align:left;">FACEBOOK - 187.6K Followers</p>
          <p style ="font-size:10pt; text-align:left;">SPOTIFY - 158.9K Followers</p>
          <p style ="font-size:10pt; text-align:left;">INSTAGRAM - 79.4K Followers</p>
          <p style ="font-size:10pt; text-align:left;">TIKTOK - 14.6K Followers</p>
        </div>
       
      </div>
    </section>
    <section class="published-songs">
      <h2>My Albums</h2>
      <div class="table-container">
        <table class="albums-table">
          <thead>
            <tr>
              <th>Album Cover</th>
              <th>Album ID</th>
              <th>Album Name</th>
              <th>Genre</th>
              <th>Featured Artists</th>
              <th>Release Date</th>
              <th>Track</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($albums as $album): ?>
            <tr>
              <td><img src="<?php echo htmlspecialchars($album['album_cover']); ?>" alt="<?php echo htmlspecialchars($album['album_name']); ?>" class="album-thumbnail"></td>
              <td><?php echo htmlspecialchars($album['album_id']); ?></td>
              <td><?php echo htmlspecialchars($album['album_name']); ?></td>
              <td><?php echo htmlspecialchars($album['genre']); ?></td>
              <td><?php echo htmlspecialchars($album['featured_artists']); ?></td>
              <td><?php echo htmlspecialchars($album['release_date']); ?></td>
              <td><?php echo htmlspecialchars($album['music_track']); ?></td>

              <td class="action-buttons">
              <button class="edit-btn" onclick="editAlbum(<?php echo $album['album_id']; ?>)">Edit</button>
              <button class="delete-btn" onclick="deleteAlbum(<?php echo $album['album_id']; ?>)">Delete</button>
              </td>
            </tr>
            <!-- Add more rows as needed -->
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>


  <script src="artist-dashboard.js"></script>
  <footer>
      <div class="footer-container">
          <!-- About Section -->
          <div class="footer-about">
              <h4>About MelodyLink</h4>
              <p>MelodyLink is an innovative web application designed to revolutionize theway we engage with music by offering a comprehensive, all-in-one platformfor artists, fans, event organizers, merchandise vendors and eventequipment renters.</p>
              <img src="logo.png" alt="MelodyLink Logo">
          </div>
          
          <!-- Quick Links -->
          <div class="footer-links">
              <h4>Quick Links</h4>
              <ul>
                  <li><a href="#home">Home</a></li>
                  <li><a href="#artists">Music</a></li>
                  <li><a href="#artists">Artists</a></li>
                  <li><a href="#albums">Events</a></li>
                  <li><a href="#genres">Store</a></li>
                  <li><a href="#artists">Communities</a></li>
                  <li><a href="#contact">Contact Us</a></li>
              </ul>
          </div>
          
          <!-- Social Media Section -->
          <div class="footer-social">
              <h4>Follow Us</h4>
              <div class="social-icons">
                  <a href="#"><img src="facebook-icon.png" alt="Facebook"></a>
                  <a href="#"><img src="twitter-icon.png" alt="Twitter"></a>
                  <a href="#"><img src="instagram-icon.png" alt="Instagram"></a>
                  <a href="#"><img src="youtube-icon.png" alt="YouTube"></a>
              </div>
          </div>
      </div>
      <!-- Copyright Section -->
      <div class="footer-bottom">
          <p>&copy; 2024 MelodyLink. All Rights Reserved.</p>
      </div>
  </footer> 
</body>
</html>
<script>
function editAlbum(albumId) {
    const width = 600;
    const height = 700;
    const left = (screen.width - width) / 2;
    const top = (screen.height - height) / 2;

    window.open('update-album.php?album_id=' + albumId, 'Edit Album', 
        `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`);
}
</script>
<script>
  function deleteAlbum(albumId) {
    if (confirm("Are you sure you want to delete this album?")) {
        // Send an AJAX request to delete the album
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete-album.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Reload the page to reflect the changes
                location.reload();
            }
        };
        xhr.send('album_id=' + albumId);
    }
}
</script>

