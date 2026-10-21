<?php
// Start session on every page that includes this
session_start();

// DATABASE CONNECTION
require_once __DIR__ . '/database.php';

// SITE CONFIG
define('SITE_NAME', 'Post It');
define('SITE_URL', 'https://postit.infinityfree.me/');
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('MAX_FILE_SIZE', 2097152); // 2MB

// HELPER FUNCTIONS

// Redirect to a URL
function redirect($url)
{
    header("Location: " . $url);
    exit;
}

// Check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Redirect to login if not logged in
function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . 'login.php');
        exit;
    }
}

// Redirect to dashboard if already logged in
function requireGuest()
{
    if (isLoggedIn()) {
        header('Location: ' . SITE_URL . 'index.php');
        exit;
    }
}

// Escape HTML to prevent XSS attacks
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Validate email format
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate username (letters, numbers, underscore, 3-50 chars)
function isValidUsername($username)
{
    return preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username);
}

// Validate password strength
function isValidPassword($password)
{
    return strlen($password) >= 4 &&
        preg_match('/[A-Za-z]/', $password) &&
        preg_match('/[0-9]/', $password);
}

// Get current user ID
function getCurrentUserId()
{
    if (is_array($_SESSION) && isset($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    }
    return null;
}

// Get current username
function getCurrentUsername()
{
    if (is_array($_SESSION) && isset($_SESSION['username'])) {
        return $_SESSION['username'];
    }
    return null;
}

// In config.php or header.php
function getCurrentUserData($conn) {
    if (!isset($_SESSION['user_data'])) {
        $stmt = $conn->prepare("SELECT id, username, email, profile_photo FROM users WHERE id = ?");
        $stmt->execute([getCurrentUserId()]);
        $_SESSION['user_data'] = $stmt->fetch();
    }
    return $_SESSION['user_data'];
}

// Usage in header.php
$userData = getCurrentUserData($conn);
$profilePic = !empty($userData['profile_photo']) ? SITE_URL . 'assets/uploads/profiles/' . e($userData['profile_photo']) : SITE_URL . 'assets/uploads/default.png';
$username = $userData['username'];