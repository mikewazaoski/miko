<?php
include 'includes/auth.php';
include 'includes/db.php';

// Fetch all users
try {
    $stmt = $pdo->query("SELECT UserID, Username, RoleID FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}

// Fetch login history (most recent first)
try {
    $stmt = $pdo->query("
        SELECT h.id, h.user_id, h.login_time, h.logout_time, u.Username 
        FROM user_login_history h
        JOIN users u ON h.user_id = u.UserID
        ORDER BY h.login_time DESC
        LIMIT 50
    ");
    $login_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching login history: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #fff7fb;
        font-family: 'Segoe UI', sans-serif;
        color: #5a3e36;
        position: relative;
    }

    h2 {
        color: #a64d79;
        font-weight: bold;
    }

    .btn-primary {
        background-color: #a64d79;
        border-color: #a64d79;
    }

    .btn-primary:hover {
        background-color: #923d6b;
        border-color: #923d6b;
    }

    .btn-secondary {
        background-color: #dec7d6;
        border-color: #dec7d6;
        color: #6b4a9c;
    }

    .btn-secondary:hover {
        background-color: #f6e7ef;
        color: #6b4a9c;
    }

    .btn-warning {
        background-color: #f0b9c6;
        border-color: #f0b9c6;
        color: #5a3e36;
    }

    .btn-warning:hover {
        background-color: #eaa5b5;
    }

    .btn-danger {
        background-color: #d36d6d;
        border-color: #d36d6d;
    }

    .btn-danger:hover {
        background-color: #b95151;
    }

    .nav-tabs .nav-link.active {
        background-color: #f8ddee;
        color: #a64d79;
        font-weight: 600;
        border-color: #e8bcd6 #e8bcd6 #fff;
    }

    .nav-tabs .nav-link {
        color: #6b4a9c;
    }

    .table thead {
        background-color: #fbe6f2;
        color: #5a3e36;
    }

    .badge.bg-success {
        background-color: #a4d6b3 !important;
    }

    .badge.bg-warning {
        background-color: #ffe7a3 !important;
        color: #5a3e36;
    }

    .table-bordered td, .table-bordered th {
        vertical-align: middle;
    }

    /* Cute flower background accent */
    body::after {
        content: "🌸 🌷 🌼 🌺";
        font-size: 2rem;
        position: absolute;
        bottom: 1rem;
        right: 2rem;
        opacity: 0.2;
        pointer-events: none;
    }
</style>

</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">User Management</h2>
    
    <div class="d-flex justify-content-between mb-3">
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        <a href="register.php" class="btn btn-primary">Add New User</a>
    </div>

    <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true">Users</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="login-history-tab" data-bs-toggle="tab" data-bs-target="#login-history" type="button" role="tab" aria-controls="login-history" aria-selected="false">Login History</button>
        </li>
    </ul>
    
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Role ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['UserID']); ?></td>
                                <td><?php echo htmlspecialchars($user['Username']); ?></td>
                                <td><?php echo htmlspecialchars($user['RoleID']); ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $user['UserID']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete_user.php?id=<?php echo $user['UserID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="tab-pane fade" id="login-history" role="tabpanel" aria-labelledby="login-history-tab">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Login Time</th>
                        <th>Logout Time</th>
                        <th>Session Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($login_history): ?>
                        <?php foreach ($login_history as $entry): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($entry['id']); ?></td>
                                <td><?php echo htmlspecialchars($entry['Username']); ?></td>
                                <td><?php echo htmlspecialchars($entry['login_time']); ?></td>
                                <td>
                                    <?php 
                                    if ($entry['logout_time']) {
                                        echo htmlspecialchars($entry['logout_time']);
                                    } else {
                                        echo '<span class="badge bg-success">Active</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($entry['logout_time']) {
                                        $login = new DateTime($entry['login_time']);
                                        $logout = new DateTime($entry['logout_time']);
                                        $duration = $logout->diff($login);
                                        
                                        if ($duration->d > 0) {
                                            echo $duration->format('%d days, %h hrs, %i mins');
                                        } else if ($duration->h > 0) {
                                            echo $duration->format('%h hrs, %i mins, %s secs');
                                        } else {
                                            echo $duration->format('%i mins, %s secs');
                                        }
                                    } else {
                                        $login = new DateTime($entry['login_time']);
                                        $now = new DateTime();
                                        $duration = $now->diff($login);
                                        
                                        if ($duration->d > 0) {
                                            echo $duration->format('%d days, %h hrs, %i mins');
                                        } else if ($duration->h > 0) {
                                            echo $duration->format('%h hrs, %i mins, %s secs');
                                        } else {
                                            echo $duration->format('%i mins, %s secs');
                                        }
                                        
                                        echo ' <span class="badge bg-warning text-dark">Ongoing</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">No login history found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>