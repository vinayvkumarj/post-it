<?php
// Start session on every page that includes this
session_start();

// DATABASE CONNECTION
require_once __DIR__ . '/database.php';

// SITE CONFIG
define('SITE_NAME', 'Post It');
define('SITE_URL', 'http://localhost/notes-app/');
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
    return $_SESSION['user_id'] ?? null;
}

// Get current username
function getCurrentUsername()
{
    return $_SESSION['username'] ?? null;
}
