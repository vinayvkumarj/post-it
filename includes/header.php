<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle ?? SITE_NAME); ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <header class="header">
        <div class="header-content">
            <a href="<?php echo SITE_URL; ?>">
                <div class="logo-container">
                    <img src="logo.png" class="logo" alt="postit-logo">
                    <span class="logo-name">Post It</span>
                </div>
            </a>

            <div class="header-right">
                <nav class="main-nav">
                    <?php if (basename($_SERVER['PHP_SELF']) == 'archive.php'): ?>
                        <!-- Notes link only on Archive page -->
                        <a href="index.php" class="nav-link">
                            Notes
                        </a>
                    <?php endif; ?>
                </nav>

                <div class="settings-container">
                    <?php
                    // Get current user profile photo and username
                    $stmt = $conn->prepare("SELECT profile_photo, username, email FROM users WHERE id = ?");
                    $stmt->execute([getCurrentUserId()]);
                    $userData = $stmt->fetch();
                    $profilePic = !empty($userData['profile_photo']) ? SITE_URL . 'assets/uploads/profiles/' . e($userData['profile_photo']) : SITE_URL . 'assets/images/default.png';
                    $username = !empty($userData['username']) ? e($userData['username']) : 'User';
                    $email = !empty($userData['email']) ? e($userData['email']) : 'Email';
                    ?>

                    <img src="<?php echo $profilePic . '?t=' . time(); ?>"
                        alt="Profile"
                        class="profile-icon"
                        id="settingsBtn"
                        title="<?php echo $username; ?> (<?php echo $email; ?>)">

                    <div class="dropdown-menu">
                        <a href="profile.php">
                            <span>Profile</span>
                        </a>
                        <a href="help.php">
                            <span>Help</span>
                        </a>
                        <a href="archive.php">
                            <span>Archive</span>
                        </a>
                        <a href="#" id="darkModeToggle">
                            <span>Dark Mode</span>
                        </a>
                        <a href="logout.php">
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">