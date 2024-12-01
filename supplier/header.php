<head>
<link rel="stylesheet" type="text/css" href="../public/css/styles.css">
<script src="../public/js/event.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<!-- <header class="header">
    <div class="logo"><img src="../public/img/logo.png" alt="Profile Icon" class="profile-icon">MelodyLink</div>
    <nav class="navbar">
        <a href="?action=home">Home</a>
        <a href="?action=event">My Events</a>
        <a href="/rent-equipments">Rent Equipments</a>
        <a href="?action=profile" class="logout">profile</a>
    </nav>
</header> -->
<header class="header">
    <div class="logo">
        <img src="../public/img/logo.png" alt="MelodyLink Logo" class="logo-icon">
        <span>MelodyLink</span>
    </div>
    <nav class="navbar">
        <a href="?action=home">Home</a>
        <a href="?action=event">My Events</a>
        <a href="/rent-equipments">Rent Equipments</a>
        <a href="/support">Artist Requests</a>
        <a href="/about-us">Reports</a> 
        <div class="profile-dropdown">
            <img src="../public/img/profile.png" alt="Profile Icon" class="profile-icon" onclick="toggleDropdown()">
            <div class="dropdown-menu" id="profileDropdown">
                <a href="?action=profile">Go to Profile</a>
                <a href="?action=logout">Sign Out</a>
            </div>
        </div>
    </nav>
</header>
