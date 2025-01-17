<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="settings-container">
    <h1>Settings</h1>

    <form action="<?php echo URLROOT; ?>/users/settings" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?php echo $data['username']; ?>" class="<?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>">
            <div class="invalid-feedback"><?php echo $data['username_err']; ?></div>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo $data['email']; ?>" class="<?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>">
            <div class="invalid-feedback"><?php echo $data['email_err']; ?></div>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="<?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>">
            <div class="invalid-feedback"><?php echo $data['password_err']; ?></div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="<?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>">
            <div class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></div>
        </div>

        <div class="form-group">
            <label for="profile_pic">Profile Picture</label>
            <input type="file" name="profile_pic" id="profile_pic">
            <div class="invalid-feedback"><?php echo $data['profile_pic_err']; ?></div>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>