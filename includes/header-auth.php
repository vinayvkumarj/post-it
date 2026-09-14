<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle ?? SITE_NAME); ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>

<body>
    <header class="header header-simple">
        <div class="header-content">
            <a href="<?php echo SITE_URL; ?>">
                <div class="logo-container">
                    <img src="logo.png" class="logo" alt="postit-logo">
                    <span class="logo-name">Post It</span>
                </div>
            </a>
            <div class="header-right">
                <span class="dark-mode-toggle" id="darkModeToggleAuth">
                    <span>Dark Mode</span>
                </span>
            </div>
        </div>
    </header>

    <main class="main-content">