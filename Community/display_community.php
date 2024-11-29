<?php
// Connect to the database
$servername = "localhost";
$username = "root"; // Use your DB credentials
$password = "";
$dbname = "melodylink"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session to get logged-in member's ID
session_start();
$logged_in_member_id = $_SESSION['member_id'] ?? null;

// Query to get all communities with membership status
$sql = "SELECT c.id, c.name, c.description, c.image, c.member_count, c.posts_count, 
               CASE WHEN cm.member_id IS NOT NULL THEN 1 ELSE 0 END AS is_member
        FROM communities c
        LEFT JOIN community_members cm ON c.id = cm.community_id AND cm.member_id = ?
        ORDER BY c.member_count DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $logged_in_member_id);
$stmt->execute();
$result = $stmt->get_result();

$communities = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $communities[] = $row;
    }
}

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MelodyLink - Communities</title>
    <style>



/* Sticky Navbar at the Top */
.navbar {
    display: flex; 
    justify-content: space-between;
    align-items: center;
    padding: 5px;
    background-color: #4E433F;
    position: sticky;
    top: 0;
    z-index: 1000;
    width: 100%;
}


.navbar .logo {
    font-size: 24px;
    font-weight: bold;
    color: #ffffff;
    margin-left: -500px;
    align-items: left;
    font-family: "The Season";
}





.nav-links {
    list-style: none;
    display: flex;
    gap: 30px;
    align-items: left;
}

.nav-links li {
    margin-left: 0;
}

.nav-links a {
    text-decoration: none;
    color: #D4C5B9;
    transition: color 0.3s ease;
    font-size: 16px;
}

.nav-links a:hover {
    color: #ffffff;
}
.logoimg {
    width: 50px;
    height: 60px;
    display: flex;
}



        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #B9B096;
            color: #1a1a1b;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2 {
            color: #1a1a1b;
        }
        .communities-list {
            background-color: #ffffff;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .community-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #edeff1;
        }
        .community-item:last-child {
            border-bottom: none;
        }
        .community-rank {
            font-size: 14px;
            font-weight: bold;
            color: #1a1a1b;
            width: 30px;
        }
        .community-image {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .community-details {
            flex-grow: 1;
        }
        .community-name {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1b;
            margin: 0;
        }
        .community-stats {
            font-size: 12px;
            color: #7c7c7c;
        }
        .btn-join {
            background-color: #0079d3;
            color: #ffffff;
            border: none;
            border-radius: 9999px;
            padding: 4px 16px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-join:hover {
            background-color: #005fa3;
        }
        .btn-joined {
            background-color: #edeff1;
            color: #0079d3;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        #communitySearch {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        /* Footer Styling */
footer {
    background-color: #393228; /* Dark background for contrast */
    color: #fff;
    padding: 40px 20px;
    font-family: Arial, sans-serif;
}

.footer-container {
    display: flex;
    flex-wrap: wrap; /* Make it responsive */
    justify-content: space-between;
    gap: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.footer-about,
.footer-links,
.footer-social {
    flex: 1;
    min-width: 200px; /* Ensures proper spacing on small screens */
}

.footer-about p,
.footer-links ul,
.footer-social h4 {
    margin-bottom: 15px;
}

.footer-about img {
    width: 100px; /* Adjust size as needed */
    height: auto; /* Maintain aspect ratio */
    margin-top: 15px; /* Space between the text and logo */
    border-radius: 8px; /* Optional: Rounded corners */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2); /* Add subtle shadow */
    transition: transform 0.3s, opacity 0.3s; /* Smooth hover effect */
}

.footer-links ul {
    list-style: none;
    padding: 0;
}

.footer-links li {
    margin-bottom: 10px;
}

.footer-links a {
    color: #fff;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
}

.footer-links a:hover {
    color: #FFD700; /* Highlight on hover */
}

.footer-social .social-icons {
    display: flex;
    gap: 15px;
}

.footer-social a img {
    width: 25px;
    height: 25px;
     /* filter: invert(100%); Ensures icons are visible on dark background */
    transition: transform 0.3s;
}

.footer-social a:hover img {
    transform: scale(1.2); /* Enlarge on hover */
}

.footer-bottom {
    text-align: center;
    margin-top: 20px;
    font-size: 12px;
    border-top: 1px solid #444;
    padding-top: 10px;
}


    </style>
</head>
<body>
   <!-- Sticky Navbar at the Top -->
   <nav class="navbar">
    <div class="logoimg">
        <img src="../home/logo.png" alt="MelodyLink Logo">
    </div>
    <div class="logo">MelodyLink</div>
    <ul class="nav-links">
        <li><a href="../Logout.php" class="btn1">Logout</a></li>
        <li><a href="../home/landingpage.html">Home</a></li>
        <li><a href="../Music Page/music.html">Music</a></li>
        <li><a href="../Artists Page/artists.html">Artists </a></li>
        <li><a href="#">Explore</a></li>
        <li><a href="../merch/mercha.html">Merchandise</a></li>
        <li><a href="#">Contact</a></li>
    </ul>
</nav>



    <div class="container">
        <h1>Communities</h1>
        <div class="search-bar">
            <input type="text" id="communitySearch" placeholder="Search for a community">
        </div>
        <div class="communities-list">
            <?php foreach ($communities as $index => $community): ?>
                <div class="community-item">
                    <span class="community-rank"><?php echo $index + 1; ?></span>
                    <img src="<?php echo htmlspecialchars($community['image']); ?>" alt="<?php echo htmlspecialchars($community['name']); ?>" class="community-image">
                    <div class="community-details">
                        <h2 class="community-name">
                            <a href="community.php?id=<?php echo $community['id']; ?>">
                                <?php echo htmlspecialchars($community['name']); ?>
                            </a>
                        </h2>
                        <p class="community-stats">
                            <?php echo number_format($community['member_count']); ?> members • 
                            <?php echo number_format($community['posts_count']); ?> posts
                        </p>
                    </div>
                    <?php if ($logged_in_member_id): ?>
                        <button class="btn-join <?php echo $community['is_member'] ? 'btn-joined' : ''; ?>" 
                                data-community-id="<?php echo $community['id']; ?>">
                            <?php echo $community['is_member'] ? 'Joined' : 'Join'; ?>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>


    <footer>
    <div class="footer-container">
        <!-- About Section -->
        <div class="footer-about">
            <h4>About MelodyLink</h4>
            <p>MelodyLink is an innovative web application designed to revolutionize theway we engage with music by offering a comprehensive, all-in-one platformfor artists, fans, event organizers, merchandise vendors and eventequipment renters.</p>
            <img src="../home/logo.png" alt="MelodyLink Logo">
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
                <a href="#"><img src="../home/facebook-icon.png" alt="Facebook"></a>
                <a href="#"><img src="../home/twitter-icon.png" alt="Twitter"></a>
                <a href="#"><img src="../home/instagram-icon.png" alt="Instagram"></a>
                <a href="#"><img src="../home/youtube-icon.png" alt="YouTube"></a>
            </div>
        </div>
    </div>
    <!-- Copyright Section -->
    <div class="footer-bottom">
        <p>&copy; 2024 MelodyLink. All Rights Reserved.</p>
    </div>
</footer>


    <script>
        // Search functionality
        const searchInput = document.getElementById('communitySearch');
        const communityItems = document.querySelectorAll('.community-item');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            communityItems.forEach(item => {
                const communityName = item.querySelector('.community-name').textContent.toLowerCase();
                if (communityName.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Join button functionality
        const joinButtons = document.querySelectorAll('.btn-join');

        joinButtons.forEach(button => {
            button.addEventListener('click', function() {
                const communityId = this.getAttribute('data-community-id');
                const action = this.classList.contains('btn-joined') ? 'leave' : 'join';

                fetch('join_community.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `community_id=${communityId}&action=${action}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'Joined') {
                        this.textContent = 'Joined';
                        this.classList.add('btn-joined');
                    } else if (data === 'Left') {
                        this.textContent = 'Join';
                        this.classList.remove('btn-joined');
                    } else {
                        console.log(data); // Show any additional error messages (if needed)
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>
</body>
</html>