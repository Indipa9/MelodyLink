<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Welcome to the Music Page</h1>
    </header>
    <main>
        <section class="music-container">
            <?php
            // List of music video URLs (YouTube)
            $videos = [
                "https://www.youtube.com/embed/jeqirdekufg?si=ZyHT2aVDNUb0nhoa",
                "https://www.youtube.com/embed/LXb3EKWsInQ",
                "https://www.youtube.com/embed/kJQP7kiw5Fk"
            ];

            // Dynamically generate iframes for each video
            foreach ($videos as $video) {
                echo '<div class="video">';
                echo "<iframe src=\"$video\" frameborder=\"0\" allowfullscreen allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\"></iframe>";
                echo '</div>';
            }
            ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Music Page. All rights reserved.</p>
    </footer>
</body>
</html>
