<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'melodylink');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to join community
if (isset($_POST['join']) && isset($_POST['community_id'])) {
    $member_id = 1; // Replace this with the logged-in member's ID, or fetch dynamically
    $community_id = $_POST['community_id'];

    // Ensure the community ID and member ID are set
    if ($community_id && $member_id) {
        $query = "INSERT INTO community_members (member_id, community_id) VALUES ($member_id, $community_id)";
        if ($conn->query($query) === TRUE) {
            echo "<script>alert('Successfully joined the community!');</script>";
        } else {
            echo "<script>alert('Error joining the community: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Invalid community or member ID.');</script>";
    }
}

// Search functionality
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $conn->real_escape_string($_GET['search']);
}

// Fetch all communities or filter based on search
$query = "SELECT * FROM communities";
if ($searchTerm) {
    $query .= " WHERE name LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%'";
}
$result = $conn->query($query);

$communities = [];
while ($row = $result->fetch_assoc()) {
    $communities[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Communities</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f3f4f6, #e0e7ff);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 30px;
            color: #4C8BF5;
            text-align: center;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }

        .search-container {
            margin-bottom: 30px;
            text-align: center;
        }

        .search-container input {
            width: 300px;
            padding: 12px;
            border-radius: 20px;
            border: 1px solid #ccc;
            font-size: 1rem;
            outline: none;
            transition: 0.3s ease;
        }

        .search-container input:focus {
            border-color: #4C8BF5;
        }

        .search-container button {
            background-color: #4C8BF5;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-container button:hover {
            background-color: #3a7bdf;
        }

        .communities-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            width: 80%;
            margin: 0 auto;
            justify-items: center;
        }

        .community {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            width: 100%;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease;
        }

        .community:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .community h2 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 10px;
            transition: color 0.3s ease;
        }

        .community h2:hover {
            color: #4C8BF5;
        }

        .community p {
            color: #555;
            font-size: 1rem;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .community img {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .community img:hover {
            transform: scale(1.05);
        }

        button {
            background-color: #4C8BF5;
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #3a7bdf;
            transform: scale(1.05);
        }

        button:focus {
            outline: none;
        }

        .alert {
            font-size: 1.2rem;
            color: #fff;
            background-color: #28a745;
            padding: 10px 20px;
            border-radius: 5px;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            animation: alertAnimation 3s forwards;
        }

        @keyframes alertAnimation {
            0% {
                opacity: 0;
                top: 10px;
            }
            50% {
                opacity: 1;
                top: 20px;
            }
            100% {
                opacity: 0;
                top: 30px;
            }
        }
    </style>
</head>
<body>

    <h1>Available Communities</h1>

 <!-- Search Form -->
 <div class="search-container">
        <form action="communities.php" method="GET">
            <input type="text" name="search" placeholder="Search Communities..." value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Communities List -->
    <div class="communities-container">
        <?php if (empty($communities)): ?>
            <p>No communities found.</p>
        <?php else: ?>
            <?php foreach ($communities as $community): ?>
                <div class="community">
                    <?php if (!empty($community['image'])): ?>
                        <img src="<?php echo $community['image']; ?>" alt="<?php echo htmlspecialchars($community['name']); ?>">
                    <?php endif; ?>
                    <h2><?php echo htmlspecialchars($community['name']); ?></h2>
                    <p><?php echo htmlspecialchars($community['description']); ?></p>
                    <form action="communities.php" method="POST">
    <input type="hidden" name="community_id" value="<?php echo $community['id']; ?>">
    <?php if (!empty($community['is_member']) && $community['is_member'] > 0): ?>
        <button type="button" disabled style="background-color: gray; cursor: not-allowed;">Joined</button>
    <?php else: ?>
        <button type="submit" name="join">Join</button>
    <?php endif; ?>
</form>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
