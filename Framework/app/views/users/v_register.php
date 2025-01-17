<?php require APPROOT.'/views/inc/header.php';?>
<!-- TOP NAVIGATION -->
<?php require APPROOT .'/views/inc/components/topnavbar.php'; ?>
<!-- REGISTER-CONTAINER -->
<body background>
<div class="register-container">
    <h2>Register</h2>
    <form action="<?php echo URLROOT; ?>/users/register" method="POST">
        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="name" value="<?php echo $data['name']; ?>" required>
            <span class="form-invalid"><?php echo $data['name_err']; ?></span>
        </div>
    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" value="<?php echo $data['email']; ?>" required>
        <span class="form-invalid"><?php echo $data['email_err']; ?></span>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
        <span class="form-invalid"><?php echo $data['password_err']; ?></span>
    </div>

    <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <span class="form-invalid"><?php echo $data['confirm_password_err']; ?></span>
    </div>

    <div class="form-group">
        <label for="userType">Register as</label>
        <select id="userType" name="userType" required>
            <option value="" <?php echo empty($data['userType']) ? 'selected' : ''; ?>>Select User Type</option>
            <option value="member" <?php echo $data['userType'] == 'member' ? 'selected' : ''; ?>>Member (Subscriber)</option>
            <option value="artist" <?php echo $data['userType'] == 'artist' ? 'selected' : ''; ?>>Artist</option>
            <option value="organizer" <?php echo $data['userType'] == 'organizer' ? 'selected' : ''; ?>>Event Organizer</option>
            <option value="supplier" <?php echo $data['userType'] == 'supplier' ? 'selected' : ''; ?>>Merchandise/Equipment Supplier</option>
        </select>
        <span class="form-invalid"><?php echo $data['userType_err']; ?></span>
    </div>

    <!-- Conditional fields for specific user types -->
    <div id="artistFields" class="form-group" style="display: <?php echo $data['userType'] == 'artist' ? 'block' : 'none'; ?>">
        <label for="specialty">Artist Specialty</label>
        <input type="text" id="specialty" name="specialty" value="<?php echo $data['specialty'] ?? ''; ?>">
    </div>

    <div id="organizerFields" class="form-group" style="display: <?php echo $data['userType'] == 'organizer' ? 'block' : 'none'; ?>">
        <label for="organization">Organization Name</label>
        <input type="text" id="organization" name="organization" value="<?php echo $data['organization'] ?? ''; ?>">
    </div>

    <div id="supplierFields" class="form-group" style="display: <?php echo $data['userType'] == 'supplier' ? 'block' : 'none'; ?>">
        <label for="business_type">Business Type</label>
        <input type="text" id="business_type" name="business_type" value="<?php echo $data['business_type'] ?? ''; ?>">
    </div>

    <button type="submit">Register</button>
</form>
</div>

<script src="<?php echo URLROOT; ?>/js/register.js"></script>
</body>
<?php require APPROOT.'/views/inc/footer.php';?>