<?php
session_start();

// Check if the user is already logged in, and redirect to the appropriate dashboard
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['RoleID'] == 1 || $_SESSION['user']['RoleID'] == 2) {
        // Redirect to the Admin/Staff dashboard
        header("Location: dashboard.php");
    } elseif ($_SESSION['user']['RoleID'] == 3) {
        // Redirect to the Customer dashboard
        header("Location: customer_dashboard.php");
    }
    exit();
}

// Handle direct form submission (without AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Database connection
    $servername = "localhost";
    $db_username = "root";
    $db_password = ""; 
    $dbname = "inventory_db";
    
    // Create connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        $error = "Connection failed: " . $conn->connect_error;
    } else {
        // Prepare SQL statement to retrieve user data
        $stmt = $conn->prepare("SELECT u.UserID, u.Username, u.Password, u.RoleID, r.RoleName 
                               FROM users u 
                               JOIN roles r ON u.RoleID = r.RoleID 
                               WHERE u.Username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['Password'])) {
                // Password is correct, create session
                $_SESSION['user'] = [
                    'UserID' => $user['UserID'],
                    'Username' => $user['Username'],
                    'RoleID' => $user['RoleID'],
                    'RoleName' => $user['RoleName']
                ];
                
                // ADD THE LOGIN HISTORY CODE RIGHT HERE
                $login_time = date('Y-m-d H:i:s');
                $stmt = $conn->prepare("INSERT INTO user_login_history (user_id, login_time) VALUES (?, ?)");
                $stmt->bind_param("is", $user['UserID'], $login_time);
                $stmt->execute();
                
                // Store the login history ID in the session
                $_SESSION['login_history_id'] = $conn->insert_id;
                
                // Redirect based on role
                if ($user['RoleID'] == 1 || $user['RoleID'] == 2) {
                    // Admin or Staff
                    header("Location: dashboard.php");
                } elseif ($user['RoleID'] == 3) {
                    // Customer
                    header("Location: customer_dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "Username not found";
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ekim's Flower Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff85a2;
            --primary-hover: #ff6b8e;
            --text-color: #5d4037;
            --light-text: #8d6e63;
            --border-color: rgba(255, 192, 203, 0.3);
            --background: rgba(255, 255, 255, 0.15);
            --card-bg: rgba(255, 255, 255, 0.2);
            --error-bg: rgba(239, 68, 68, 0.15);
            --error-text: #ef4444;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #ffcad4, #ffc8dd, #ffe5d9);
            background-size: 200% 200%;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            color: var(--text-color);
            animation: gradientBG 15s ease infinite;
            position: relative;
            overflow: hidden;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            border: 2px solid rgba(255, 192, 203, 0.5);
            z-index: 10;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .login-logo {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .login-header h1 {
            color: var(--text-color);
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-family: 'Pacifico', cursive;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        
        .login-header p {
            color: var(--light-text);
            font-size: 1rem;
            font-weight: 400;
        }
        
        .error-message {
            background-color: var(--error-bg);
            color: var(--text-color);
            padding: 0.875rem;
            border-radius: 14px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            text-align: center;
            border-left: 4px solid var(--error-text);
            backdrop-filter: blur(10px);
        }
        
        .success-message {
            background-color: rgba(34, 197, 94, 0.15);
            color: #3a5a40;
            padding: 0.875rem;
            border-radius: 14px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            text-align: center;
            border-left: 4px solid #22c55e;
            backdrop-filter: blur(10px);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 600;
            font-size: 0.875rem;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            z-index: 10;
        }
        
        input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.5rem;
            border: 2px solid var(--border-color);
            border-radius: 16px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.6);
            color: var(--text-color);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }
        
        input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 133, 162, 0.2);
            background: rgba(255, 255, 255, 0.8);
        }
        
        input::placeholder {
            color: var(--light-text);
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            cursor: pointer;
            transition: color 0.2s;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: var(--primary-hover);
        }
        
        button {
            background: linear-gradient(135deg, #ff85a2, #ffa9c0);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 0.95rem;
            width: 100%;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 15px rgba(255, 133, 162, 0.3);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 133, 162, 0.4);
            background: linear-gradient(135deg, #ff6b8e, #ff95b3);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        button i {
            margin-right: 0.5rem;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.875rem;
            color: var(--light-text);
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 192, 203, 0.3);
        }
        
        /* Registration link styles */
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        }
        
        .register-link a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }
        
        .register-link i {
            margin-right: 0.4rem;
        }
        
        /* Flower decoration elements */
        .flower {
            position: absolute;
            width: 40px;
            height: 40px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cpath d='M50 15c0 0-15 15-15 35s15 35 15 35 15-15 15-35-15-35-15-35z' fill='%23ff85a2'/%3E%3Cpath d='M15 50c0 0 15-15 35-15s35 15 35 15-15 15-35 15-35-15-35-15z' fill='%23ff85a2'/%3E%3Ccircle cx='50' cy='50' r='15' fill='%23ffef9f'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            z-index: 1;
            opacity: 0.7;
            pointer-events: none;
        }
        
        /* Falling flower animation */
        @keyframes falling {
            0% {
                transform: translateY(-50px) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.7;
            }
            100% {
                transform: translateY(calc(100vh + 50px)) rotate(360deg);
                opacity: 0;
            }
        }
        
        /* Glass morphism decorative elements */
        .glass-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 192, 203, 0.2);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: -1;
            border: 1px solid rgba(255, 192, 203, 0.3);
        }
        
        .glass-circle-1 {
            top: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
        }
        
        .glass-circle-2 {
            bottom: -70px;
            right: -70px;
            width: 200px;
            height: 200px;
        }
        
        /* Loading overlay styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 192, 203, 0.5);
            backdrop-filter: blur(5px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        
        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .loading-spinner {
            width: 70px;
            height: 70px;
            border: 5px solid rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 15px;
        }
        
        .loading-text {
            color: var(--text-color);
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 15px;
            text-shadow: 0 1px 3px rgba(255, 255, 255, 0.5);
        }
        
        .loading-progress {
            width: 200px;
            height: 8px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            margin-top: 15px;
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(255, 192, 203, 0.3);
        }
        
        .loading-progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #ff85a2, #ffb3c6);
            border-radius: 10px;
            transition: width 2s ease;
        }
        
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Flower animation container -->
    <div id="flowerContainer"></div>
    
    <div class="login-container">
        <!-- Glass morphism decorative elements -->
        <div class="glass-circle glass-circle-1"></div>
        <div class="glass-circle glass-circle-2"></div>
    
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-flower"></i>
                <svg width="60" height="60" viewBox="0 0 100 100">
                    <path d="M50 15c0 0-15 15-15 35s15 35 15 35 15-15 15-35-15-35-15-35z" fill="#ff85a2"/>
                    <path d="M15 50c0 0 15-15 35-15s35 15 35 15-15 15-35 15-35-15-35-15z" fill="#ff85a2"/>
                    <circle cx="50" cy="50" r="15" fill="#ffef9f"/>
                </svg>
            </div>
            <h1>Ekim's Flower Shop</h1>
            <p>Sign in to your account</p>
        </div>
        
        <!-- Display error message if there is any -->
        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <!-- Display success message if redirected from registration -->
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <?php echo $_GET['success']; ?>
            </div>
        <?php endif; ?>
        
        <form id="loginForm" method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-group">
                    <i class="input-icon fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <i class="input-icon fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <i class="password-toggle fas fa-eye" id="togglePassword"></i>
                </div>
            </div>
            
            <button type="submit" id="loginButton"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>
        
        <!-- Registration link -->
        <div class="register-link">
            <a href="register.php"><i class="fas fa-user-plus"></i> Don't have an account? Create one</a>
        </div>
        
        <div class="login-footer">
            <p>Ekim's Flower Shop &copy; <?php echo date('Y'); ?></p>
        </div>
    </div>

    <!-- Loading overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">Logging in...</div>
        <div class="loading-progress">
            <div class="loading-progress-bar" id="progressBar"></div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = this;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        });

        // Handle form submission with loading animation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            // Prevent the form from submitting immediately
            e.preventDefault();
            
            // Store reference to the form
            const form = this;
            
            // Show loading overlay
            document.getElementById('loadingOverlay').classList.add('active');
            
            // Start progress bar animation
            const progressBar = document.getElementById('progressBar');
            progressBar.style.width = '0%';
            
            // Animate progress bar to 100% over 2 seconds
            setTimeout(() => {
                progressBar.style.width = '100%';
            }, 100);
            
            // Wait for 2 seconds before actually submitting the form
            setTimeout(() => {
                form.submit(); // Submit the form after 2 seconds
            }, 2000);
        });
        
        // Create falling flower animation
        function createFlowers() {
            const flowerContainer = document.getElementById('flowerContainer');
            const flowerCount = 15;
            
            for (let i = 0; i < flowerCount; i++) {
                const flower = document.createElement('div');
                flower.className = 'flower';
                
                // Random position, size, and delay
                const size = Math.random() * 30 + 20; // 20-50px
                const left = Math.random() * 100; // 0-100%
                const animationDuration = Math.random() * 10 + 10; // 10-20s
                const animationDelay = Math.random() * 15; // 0-15s
                
                flower.style.width = `${size}px`;
                flower.style.height = `${size}px`;
                flower.style.left = `${left}%`;
                flower.style.animation = `falling ${animationDuration}s linear ${animationDelay}s infinite`;
                
                flowerContainer.appendChild(flower);
            }
        }
        
        // Initialize flower animation on page load
        document.addEventListener('DOMContentLoaded', function() {
            createFlowers();
        });
    </script>
</body>
</html>