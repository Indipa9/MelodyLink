<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="profile-section">
            <img src="/images/default-avatar.png" alt="Profile" class="profile-avatar">
            <h2><?php echo $data['member_info']['username']; ?></h2>
            <p><?php echo $data['member_info']['email']; ?></p>
        </div>
        <nav class="dashboard-nav">
            <ul>
                <li class="active"><a href="#">Dashboard</a></li>
                <li><a href="#">My Playlists</a></li>
                <li><a href="#">Profile</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </nav>
    </div>

    <main class="main-content">
        <section class="member-info">
            <h1>Welcome, <?php echo $data['member_info']['username']; ?>!</h1>
            <div class="quick-stats">
                <div class="stat-card">
                    <h3>Playlists</h3>
                    <p><?php echo count($data['playlists']); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Recent Activity</h3>
                    <p><?php echo count($data['recent_activities']); ?></p>
                </div>
            </div>
        </section>

        <section class="playlists">
            <div class="section-header">
                <h2>My Playlists</h2>
                <a href="#" class="btn-create">Create Playlist</a>
            </div>
            <div class="playlist-grid">
                <?php foreach($data['playlists'] as $playlist): ?>
                    <div class="playlist-card">
                        <div class="playlist-info">
                            <h3><?php echo $playlist['name']; ?></h3>
                            <p><?php echo $playlist['songs_count']; ?> Songs</p>
                            <small>Created on <?php echo $playlist['created_at']; ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="recent-activity">
            <h2>Recent Activity</h2>
            <div class="activity-list">
                <?php foreach($data['recent_activities'] as $activity): ?>
                    <div class="activity-item">
                        <span class="activity-icon <?php echo $activity['type']; ?>"></span>
                        <div class="activity-details">
                            <p><?php echo $activity['details']; ?></p>
                            <small><?php echo $activity['date']; ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="recommendations">
            <h2>Recommended for You</h2>
            <div class="music-grid">
                <?php foreach($data['recommended'] as $track): ?>
                    <div class="music-card">
                        <img src="<?php echo $track['cover']; ?>" alt="<?php echo $track['title']; ?>">
                        <div class="track-info">
                            <h3><?php echo $track['title']; ?></h3>
                            <p><?php echo $track['artist']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>