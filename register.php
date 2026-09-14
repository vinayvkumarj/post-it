<?php
require 'config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'Email already registered';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword]);

            $success = 'Registration successful! Redirecting to login...';
            header('refresh:2;url=login.php');
        }
    }
}

$pageTitle = "Register";
include 'includes/header-auth.php';
?>

<div class="form-container">
    <h2 class="form-title">Register</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="name">Username</label>
            <input
                type="text"
                id="name"
                name="name"
                required
                value="<?php echo e($_POST['name'] ?? ''); ?>"
                placeholder="Choose a username"
                autocomplete="username">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                required
                value="<?php echo e($_POST['email'] ?? ''); ?>"
                placeholder="your@email.com"
                autocomplete="email">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                placeholder="At least 6 characters"
                autocomplete="new-password">
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                required
                placeholder="Re-enter password"
                autocomplete="new-password">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Register</button>
        </div>
    </form>

    <p class="text-center mt-2">
        Already have an account? <a href="login.php" class="btn-link">Login</a>
    </p>
</div>

<?php include 'includes/footer-auth.php'; ?>