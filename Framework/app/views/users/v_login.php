
<!DOCTYPE html>
<html>
<head>
 <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f5f7ff 0%, #c3e3ff 100%);
}

.login-container {
    display: flex;
    width: 1000px;
    height: 600px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.illustration-side {
    flex: 1;
    background: linear-gradient(45deg, #4e54c8, #8f94fb);
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: relative;
    color: white;
}

.circles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    opacity: 0.2;
}

.circles div {
    position: absolute;
    border: 4px solid white;
    border-radius: 50%;
}

.circles div:nth-child(1) {
    width: 200px;
    height: 200px;
    top: 10%;
    left: 60%;
}

.circles div:nth-child(2) {
    width: 150px;
    height: 150px;
    top: 60%;
    left: 20%;
}

.circles div:nth-child(3) {
    width: 100px;
    height: 100px;
    top: 40%;
    left: 40%;
}

.illustration-text {
    position: relative;
    z-index: 1;
    text-align: center;
}

.illustration-text h2 {
    font-size: 36px;
    margin-bottom: 20px;
}

.illustration-text p {
    font-size: 18px;
    opacity: 0.8;
}

.form-side {
    flex: 1;
    padding: 50px;
}

.login-form {
    max-width: 320px;
    margin: 0 auto;
}

.login-form h2 {
    font-size: 24px;
    color: #333;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #666;
    font-size: 14px;
}

.form-group input {
    width: 100%;
    padding: 12px;
    border: 2px solid #e1e1e1;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #4e54c8;
}

.form-links {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
    font-size: 14px;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 5px;
}

.forgot-password {
    color: #4e54c8;
    text-decoration: none;
}

button {
    width: 100%;
    padding: 12px;
    background: #4e54c8;
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease;
}

button:hover {
    background: #3f44a0;
}

.register-link {
    text-align: center;
    margin-top: 20px;
    font-size: 14px;
    color: #666;
}

.register-link a {
    color: #4e54c8;
    text-decoration: none;
    font-weight: bold;
}

@media (max-width: 768px) {
    .login-container {
        flex-direction: column;
        width: 90%;
        height: auto;
    }
    
    .illustration-side {
        display: none;
    }
    
    .form-side {
        padding: 30px;
    }
}
</style> 
</head>
<body>
    <div class="login-container">
        <div class="illustration-side">
            <div class="circles">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="illustration-text">
                <h2>Welcome Back</h2>
                <p>We're glad to see you again!</p>
            </div>
        </div>
        
        <div class="form-side">
            <form class="login-form" action="<?php echo URLROOT; ?>/users/login" method="POST">
                <h2>Login to Your Account</h2>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-links">
                    <div class="remember-me">
                        <input type="checkbox" id="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="<?php echo URLROOT; ?>/users/forgotpassword" class="forgot-password">Forgot Password?</a>
                </div>
                
                <button type="submit">Login</button>
                
                <div class="register-link">
                    Don't have an account? 
                    <a href="<?php echo URLROOT; ?>/users/register">Sign up</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>