<?php
session_start();

// Check if the user is already logged in, and redirect to the appropriate dashboard
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

// Database connection function
function getConnection() {
    $servername = "localhost";
    $username = "root";
    $password = ""; 
    $dbname = "inventory_db";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $roleID = $_POST['role'];
    $registrationCode = isset($_POST['registration_code']) ? $_POST['registration_code'] : '';
    
    // Initialize errors array
    $errors = [];
    
    // Validate username (at least 4 characters)
    if (strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters long";
    }
    
    // Check if username already exists
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT UserID FROM users WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Username already exists";
    }
    $stmt->close();
    
    // Validate password (at least 8 characters, with at least one uppercase, one lowercase, and one number)
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    
    // Check if passwords match
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";   
    }
    
    // Validate registration codes based on role
    if ($roleID == 1) { // Admin role
        $adminCode = "ADMIN123"; // Admin registration code
        if ($registrationCode !== $adminCode) {
            $errors[] = "Invalid admin registration code";
        }
    } elseif ($roleID == 2) { // Staff role
        $staffCode = "STAFF456"; // Staff registration code
        if ($registrationCode !== $staffCode) {
            $errors[] = "Invalid staff registration code";
        }
    }
    // No registration code needed for customer/user role (3)
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO users (Username, Password, RoleID) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $username, $hashedPassword, $roleID);
        
        if ($stmt->execute()) {
            // Registration successful, redirect to login page with success message
            header("Location: login.php?success=Registration successful! You can now log in.");
            exit();
        } else {
            $errors[] = "Registration failed: " . $conn->error;
        }
        $stmt->close();
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Inventory System</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --text-color: #374151;
            --light-text: #6b7280;
            --border-color: rgba(255, 255, 255, 0.2);
            --background: rgba(255, 255, 255, 0.05);
            --card-bg: rgba(255, 255, 255, 0.1);
            --error-bg: rgba(239, 68, 68, 0.15);
            --error-text: #ef4444;
            --success-bg: rgba(34, 197, 94, 0.15);
            --success-text: #22c55e;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #4158D0, #C850C0, #FFCC70);
            background-size: 200% 200%;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            color: white;
            animation: gradientBG 15s ease infinite;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .register-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 16px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 500px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .register-logo {
            font-size: 2.2rem;
            color: white;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .register-header h1 {
            color: white;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }
        
        .register-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
            font-weight: 400;
        }
        
        .error-message, .success-message {
            padding: 0.875rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        
        .error-message {
            background-color: var(--error-bg);
            color: white;
            border-left: 4px solid var(--error-text);
        }
        
        .success-message {
            background-color: var(--success-bg);
            color: white;
            border-left: 4px solid var(--success-text);
        }
        
        .error-list {
            list-style-type: none;
            padding: 0;
            text-align: left;
            margin-top: 0.5rem;
        }
        
        .error-list li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .error-list li:before {
            content: "\f071";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            margin-right: 0.5rem;
            color: var(--error-text);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            z-index: 10;
        }
        
        input, select {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.5rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }
        
        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }
        
        select option {
            background-color: #4f46e5;
            color: white;
        }
        
        input:focus, select:focus {
            border-color: rgba(255, 255, 255, 0.5);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.15);
        }
        
        input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            transition: color 0.2s;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: white;
        }
        
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .password-strength-meter {
            height: 5px;
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            margin-top: 5px;
            position: relative;
            overflow: hidden;
        }
        
        .password-strength-meter::before {
            content: '';
            position: absolute;
            left: 0;
            height: 100%;
            width: 0%;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--error-text) 0%, var(--primary-color) 50%, var(--success-text) 100%);
            transition: width 0.3s ease;
        }
        
        .password-strength-meter.weak::before {
            width: 33.33%;
        }
        
        .password-strength-meter.medium::before {
            width: 66.66%;
        }
        
        .password-strength-meter.strong::before {
            width: 100%;
        }
        
        .code-container {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            border: 1px dashed rgba(255, 255, 255, 0.3);
        }
        
        button {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.95rem;
            width: 100%;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        button i {
            margin-right: 0.5rem;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .login-link a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .login-link a:hover {
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
            color: rgba(255, 255, 255, 0.9);
        }
        
        .login-link i {
            margin-right: 0.4rem;
        }
        
        .register-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.7);
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Glass morphism effect elements */
        .glass-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: -1;
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
        
        @media (max-width: 480px) {
            .register-container {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Glass morphism decorative elements -->
        <div class="glass-circle glass-circle-1"></div>
        <div class="glass-circle glass-circle-2"></div>
    
        <div class="register-header">
            <div class="register-logo">
                <i class="fas fa-box-open"></i>
            </div>
            <h1>Create an Account</h1>
            <p>Join the Inventory Management System</p>
        </div>
        
        <!-- Display error messages if there are any -->
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form id="registerForm" method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-group">
                    <i class="input-icon fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Choose a username" required
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <i class="input-icon fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                    <i class="password-toggle fas fa-eye" id="togglePassword"></i>
                </div>
                <div class="password-strength">
                    <span id="passwordStrengthText">Password strength: Not entered</span>
                    <div class="password-strength-meter" id="passwordStrengthMeter"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-group">
                    <i class="input-icon fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                    <i class="password-toggle fas fa-eye" id="toggleConfirmPassword"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label for="role">Account Type</label>
                <div class="input-group">
                    <i class="input-icon fas fa-user-shield"></i>
                    <select id="role" name="role" required>
                        <option value="" disabled selected>Select account type</option>
                        <option value="3" <?php echo (isset($_POST['role']) && $_POST['role'] == '3') ? 'selected' : ''; ?>>Customer</option>
                        <option value="2" <?php echo (isset($_POST['role']) && $_POST['role'] == '2') ? 'selected' : ''; ?>>Staff</option>
                        <option value="1" <?php echo (isset($_POST['role']) && $_POST['role'] == '1') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
            </div>
            
            <div id="adminCodeContainer" class="code-container">
                <div class="form-group">
                    <label for="registration_code">Admin Registration Code</label>
                    <div class="input-group">
                        <i class="input-icon fas fa-key"></i>
                        <input type="password" id="registration_code" name="registration_code" placeholder="Enter admin registration code">
                        <i class="password-toggle fas fa-eye" id="toggleRegistrationCode"></i>
                    </div>
                    <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                        <i class="fas fa-info-circle"></i> Admin registration requires a special code. Please contact your system administrator if you don't have this code.
                    </small>
                </div>
            </div>
            
            <div id="staffCodeContainer" class="code-container">
                <div class="form-group">
                    <label for="registration_code">Staff Registration Code</label>
                    <div class="input-group">
                        <i class="input-icon fas fa-key"></i>
                        <input type="password" id="staff_registration_code" name="registration_code" placeholder="Enter staff registration code">
                        <i class="password-toggle fas fa-eye" id="toggleStaffRegistrationCode"></i>
                    </div>
                    <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                        <i class="fas fa-info-circle"></i> Staff registration requires a special code. Please contact your manager if you don't have this code.
                    </small>
                </div>
            </div>
            
            <button type="submit" id="registerButton"><i class="fas fa-user-plus"></i> Create Account</button>
        </form>
        
        <div class="login-link">
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Already have an account? Login</a>
        </div>
        
        <div class="register-footer">
            <p>Inventory Management System &copy; <?php echo date('Y'); ?></p>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            togglePasswordVisibility('password', this);
        });
        
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            togglePasswordVisibility('confirm_password', this);
        });
        
        document.getElementById('toggleRegistrationCode').addEventListener('click', function() {
            togglePasswordVisibility('registration_code', this);
        });
        
        document.getElementById('toggleStaffRegistrationCode').addEventListener('click', function() {
            togglePasswordVisibility('staff_registration_code', this);
        });
        
        function togglePasswordVisibility(inputId, icon) {
            const passwordInput = document.getElementById(inputId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Show/hide registration code fields based on role selection
        document.getElementById('role').addEventListener('change', function() {
            const adminCodeContainer = document.getElementById('adminCodeContainer');
            const staffCodeContainer = document.getElementById('staffCodeContainer');
            
            // Hide all code containers first
            adminCodeContainer.style.display = 'none';
            staffCodeContainer.style.display = 'none';
            
            // Remove required attribute from all code input fields
            document.getElementById('registration_code').removeAttribute('required');
            document.getElementById('staff_registration_code').removeAttribute('required');
            
            if (this.value === '1') { // Admin role
                adminCodeContainer.style.display = 'block';
                document.getElementById('registration_code').setAttribute('required', 'required');
            } else if (this.value === '2') { // Staff role
                staffCodeContainer.style.display = 'block';
                document.getElementById('staff_registration_code').setAttribute('required', 'required');
            }
            // Customer/User role (3) doesn't need any code
        });
        
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const meter = document.getElementById('passwordStrengthMeter');
            const strengthText = document.getElementById('passwordStrengthText');
            
            // Remove previous classes
            meter.className = 'password-strength-meter';
            
            if (password.length === 0) {
                strengthText.textContent = 'Password strength: Not entered';
                return;
            }
            
            let strength = 0;
            
            // Criteria for strength
            if (password.length >= 8) strength += 1;
            if (password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[a-z]/)) strength += 1;
            if (password.match(/[0-9]/)) strength += 1;
            if (password.match(/[^A-Za-z0-9]/)) strength += 1;
            
            // Update meter and text based on strength
            if (strength <= 2) {
                meter.classList.add('weak');
                strengthText.textContent = 'Password strength: Weak';
            } else if (strength <= 4) {
                meter.classList.add('medium');
                strengthText.textContent = 'Password strength: Medium';
            } else {
                meter.classList.add('strong');
                strengthText.textContent = 'Password strength: Strong';
            }
        });
        
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword === '') {
                this.setCustomValidity('');
            } else if (confirmPassword !== password) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>