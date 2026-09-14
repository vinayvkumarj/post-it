<?php
require 'config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($usernameOrEmail) || empty($password)) {
        $error = 'Please enter both username/email and password';
    } else {
        // Check if input is email or username
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            redirect('index.php');
        } else {
            $error = 'Invalid username/email or password';
        }
    }
}

$pageTitle = "Login";
include 'includes/header-auth.php';
?>

<div class="form-container">
    <h2 class="form-title">Login</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="username_or_email">Username/ Email</label>
            <input
                type="text"
                id="username_or_email"
                name="username_or_email"
                required
                value="<?php echo e($_POST['username_or_email'] ?? ''); ?>"
                placeholder="Username or email id"
                autocomplete="username">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                placeholder="Enter password"
                autocomplete="current-password">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Login</button>
        </div>
    </form>

    <p class="text-center mt-2">
        Don't have an account? <a href="register.php" class="btn-link">Register</a>
    </p>
</div>

<?php include 'includes/footer-auth.php'; ?>