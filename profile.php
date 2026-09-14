<?php
require 'config/config.php';
requireLogin();

// Get current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([getCurrentUserId()]);
$user = $stmt->fetch();

$error = '';
$success = '';

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $uploadDir = UPLOAD_DIR . 'profiles/';

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $file = $_FILES['profile_photo'];
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        if (in_array($fileExt, $allowedExts)) {
            if ($file['size'] <= MAX_FILE_SIZE) {
                // Get image info and compress
                $imageInfo = getimagesize($file['tmp_name']);
                $width = $imageInfo[0];
                $height = $imageInfo[1];
                $type = $imageInfo[2];

                // Calculate new dimensions (max 400x400)
                $maxWidth = 400;
                $maxHeight = 400;
                $ratio = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = (int)($width * $ratio);
                $newHeight = (int)($height * $ratio);

                // Load source image
                $sourceImage = match ($type) {
                    IMAGETYPE_JPEG => imagecreatefromjpeg($file['tmp_name']),
                    IMAGETYPE_PNG => imagecreatefrompng($file['tmp_name']),
                    IMAGETYPE_GIF => imagecreatefromgif($file['tmp_name']),
                    default => null
                };

                if ($sourceImage) {
                    // Create new image
                    $newImage = imagecreatetruecolor($newWidth, $newHeight);

                    // Preserve transparency for PNG/GIF
                    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
                        imagealphablending($newImage, false);
                        imagesavealpha($newImage, true);
                        $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
                        imagefill($newImage, 0, 0, $transparent);
                    }

                    // Resize
                    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    // Generate filename
                    $filename = 'user_' . getCurrentUserId() . '.jpg'; // Always save as JPG for consistency
                    $filepath = $uploadDir . $filename;

                    // Delete old profile pics
                    $oldFiles = glob($uploadDir . 'user_' . getCurrentUserId() . '.*');
                    foreach ($oldFiles as $oldFile) {
                        unlink($oldFile);
                    }

                    // Save compressed image (75% quality for JPG)
                    imagejpeg($newImage, $filepath, 75);

                    imagedestroy($sourceImage);
                    imagedestroy($newImage);

                    // Update database
                    $stmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
                    $stmt->execute([$filename, getCurrentUserId()]);

                    $success = 'Profile picture updated!';
                    $user['profile_photo'] = $filename;
                } else {
                    $error = 'Failed to process image';
                }
            } else {
                $error = 'File size must be less than 2MB';
            }
        } else {
            $error = 'Only JPG, PNG, and GIF allowed';
        }
    }
}

// Handle profile picture deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_photo'])) {
    $uploadDir = UPLOAD_DIR . 'profiles/';

    // Delete all profile pics for this user
    $oldFiles = glob($uploadDir . 'user_' . getCurrentUserId() . '.*');
    foreach ($oldFiles as $oldFile) {
        unlink($oldFile);
    }

    // Update database
    $stmt = $conn->prepare("UPDATE users SET profile_photo = NULL WHERE id = ?");
    $stmt->execute([getCurrentUserId()]);

    $success = 'Profile picture removed!';
    $user['profile_photo'] = null;
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name) || empty($email)) {
        $error = 'All fields are required';
    } elseif (!isValidEmail($email)) {
        $error = 'Invalid email format';
    } elseif (!isValidUsername($name)) {
        $error = 'Username must be 3-50 characters';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, getCurrentUserId()]);

        if ($stmt->fetch()) {
            $error = 'Email already registered';
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, getCurrentUserId()]);
            $success = 'Profile updated!';
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'All fields are required';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([getCurrentUserId()]);
        $userData = $stmt->fetch();

        if (!password_verify($currentPassword, $userData['password'])) {
            $error = 'Current password is incorrect';
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, getCurrentUserId()]);
            $success = 'Password changed!';
        }
    }
}

$pageTitle = "Profile";
include 'includes/header.php';
?>

<div class="profile-page">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-pic-wrapper">
            <img src="<?php echo !empty($user['profile_photo']) ? SITE_URL . 'assets/uploads/profiles/' . e($user['profile_photo']) : SITE_URL . 'assets/images/default.png'; ?>"
                alt="Profile"
                class="profile-pic-large"
                id="profilePic">

            <?php if (!empty($user['profile_photo'])): ?>
                <form method="POST" class="delete-photo-form" onsubmit="return confirm('Remove profile picture?')">
                    <button type="submit" name="delete_photo" class="delete-photo-btn" title="Remove photo">✕</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="profile-info">
            <h1 class="profile-name"><?php echo e($user['username']); ?></h1>
            <p class="profile-email"><?php echo e($user['email']); ?></p>
            <p class="profile-member">Joined <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
        </div>
    </div>

    <!-- Hidden upload form -->
    <form method="POST" enctype="multipart/form-data" id="profilePicForm" style="display: none;">
        <input type="file"
            id="profilePicUpload"
            name="profile_photo"
            accept="image/*"
            onchange="this.form.submit()">
    </form>

    <?php if ($error): ?>
        <div class="alert error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <!-- Quick Stats -->
    <div class="stats-row">
        <?php
        $stmt = $conn->prepare("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN is_pinned = 1 THEN 1 ELSE 0 END) as pinned,
            SUM(CASE WHEN is_archived = 1 THEN 1 ELSE 0 END) as archived
            FROM notes WHERE user_id = ?");
        $stmt->execute([getCurrentUserId()]);
        $stats = $stmt->fetch();
        ?>

        <div class="stat-item">
            <div class="stat-value"><?php echo $stats['total']; ?></div>
            <div class="stat-text">Notes</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <div class="stat-value"><?php echo $stats['pinned']; ?></div>
            <div class="stat-text">Pinned</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <div class="stat-value"><?php echo $stats['archived']; ?></div>
            <div class="stat-text">Archived</div>
        </div>
    </div>

    <!-- Edit Profile -->
    <div class="profile-card">
        <h2 class="card-title">Edit Profile</h2>
        <form method="POST" class="edit-form">
            <div class="form-row">
                <div class="form-col">
                    <label>Username</label>
                    <input type="text" name="name" required value="<?php echo e($user['username']); ?>">
                </div>
                <div class="form-col">
                    <label>Email</label>
                    <input type="email" name="email" required value="<?php echo e($user['email']); ?>">
                </div>
            </div>
            <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="profile-card">
        <h2 class="card-title">Change Password</h2>
        <form method="POST" class="edit-form">
            <div class="form-col">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label>New Password</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-col">
                    <label>Confirm</label>
                    <input type="password" name="confirm_password" required>
                </div>
            </div>
            <button type="submit" name="change_password" class="btn btn-primary">Update Password</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('profilePic').addEventListener('click', function() {
        document.getElementById('profilePicUpload').click();
    });
</script>

<?php include 'includes/footer.php'; ?>