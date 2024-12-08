<?php require APPROOT.'/views/inc/header.php';?>

<!-- TOP NAVIGATION -->
<?php require APPROOT .'/views/inc/components/topnavbar.php'; ?>

<!-- LOGIN-CONTAINER -->
<div class="login-container">
    <h2>Login</h2>
    <form action="<?php echo URLROOT; ?>/users/login" method="POST">
        <div class="form-group">
            <label for="userType">Login As</label>
            <select id="userType" name="userType" required>
                <option value="">Select User Type</option>
                <option value="member" <?php echo ($data['userType'] == 'member') ? 'selected' : ''; ?>>Member</option>
                <option value="artist" <?php echo ($data['userType'] == 'artist') ? 'selected' : ''; ?>>Artist</option>
                <option value="organizer" <?php echo ($data['userType'] == 'organizer') ? 'selected' : ''; ?>>Event Organizer</option>
                <option value="supplier" <?php echo ($data['userType'] == 'supplier') ? 'selected' : ''; ?>>Merchandise/Equipment Supplier</option>
            </select>
            <span class="form-invalid"><?php echo $data['userType_err']; ?></span>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo $data['email']; ?>" required>
            <span class="form-invalid"><?php echo $data['email_err']; ?></span>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <span class="form-invalid"><?php echo $data['password_err']; ?></span>
        </div>

        <div class="form-group">
            <a href="<?php echo URLROOT; ?>/users/forgotpassword">Forgot Password?</a>
        </div>

        <button type="submit">Login</button>

        <div class="form-group">
            <p>Don't have an account? <a href="<?php echo URLROOT; ?>/users/register">Register here</a></p>
        </div>
    </form>
</div>

<?php require APPROOT.'/views/inc/footer.php';?>