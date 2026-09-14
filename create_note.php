<?php
require 'config/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $color = $_POST['color'];

    $errors = [];

    if (empty($title) && empty($description)) {
        $errors[] = "Note must have a title or description";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO notes (user_id, title, description, color) 
            VALUES (?, ?, ?, ?)
        ");

        if ($stmt->execute([getCurrentUserId(), $title, $description, $color])) {
            header('Location: index.php');
            exit;
        } else {
            $errors[] = "Failed to create note";
        }
    }
}

$pageTitle = "Create Note";
include 'includes/header.php';
?>

<div class="form-container" style="max-width: 600px;">
    <h1>Create Note</h1>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p>• <?php echo e($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="title">Title (optional)</label>
            <input type="text" id="title" name="title"
                value="<?php echo e($title ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="8" required><?php echo e($description ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Color</label>
            <div class="color-picker">
                <label>
                    <input type="radio" name="color" value="#ffffff" checked>
                    <span class="color-swatch" style="background: #ffffff; border: 2px solid #ddd;"></span>
                </label>
                <label>
                    <input type="radio" name="color" value="#ffeb3b">
                    <span class="color-swatch" style="background: #ffeb3b;"></span>
                </label>
                <label>
                    <input type="radio" name="color" value="#ffcc80">
                    <span class="color-swatch" style="background: #ffcc80;"></span>
                </label>
                <label>
                    <input type="radio" name="color" value="#c8e6c9">
                    <span class="color-swatch" style="background: #c8e6c9;"></span>
                </label>
                <label>
                    <input type="radio" name="color" value="#bbdefb">
                    <span class="color-swatch" style="background: #bbdefb;"></span>
                </label>
            </div>
        </div>

        <button type="submit" class="btn">Create Note</button>
        <a href="index.php" class="btn" style="background: #6c757d;">Cancel</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>