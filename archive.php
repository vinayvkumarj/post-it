<?php
require 'config/config.php';
requireLogin();

// Get all archived notes for current user
$stmt = $conn->prepare("
    SELECT *
    FROM notes
    WHERE user_id = ? AND is_archived = 1
    ORDER BY created_at DESC
");

$stmt->execute([getCurrentUserId()]);
$notes = $stmt->fetchAll();

$pageTitle = "Archive";
include 'includes/header.php';
?>

<?php if (empty($notes)): ?>
    <div class="empty-state">
        <p>No archived notes yet.</p>
    </div>
<?php else: ?>
    <div class="notes-grid">
        <?php foreach ($notes as $note): ?>
            <div
                class="note-card"
                style="background-color: <?php echo e($note['color']); ?>"
                data-note-id="<?php echo (int)$note['id']; ?>"
                data-note-title="<?php echo e($note['title']); ?>"
                data-note-description="<?php echo e($note['description']); ?>"
                data-note-color="<?php echo e($note['color']); ?>"
                data-note-pinned="<?php echo (int)$note['is_pinned']; ?>"
                data-note-archived="<?php echo (int)$note['is_archived']; ?>"
                data-note-created="<?php echo date('M j, Y g:i A', strtotime($note['created_at'])); ?>"
                data-note-updated="<?php echo date('M j, Y g:i A', strtotime($note['updated_at'])); ?>">

                <div class="note-card-content">
                    <?php if ($note['title']): ?>
                        <h3 class="note-title">
                            <?php echo e($note['title']); ?>
                        </h3>
                    <?php endif; ?>

                    <p class="note-description">
                        <?php echo e($note['description'] ?: 'Empty note'); ?>
                    </p>
                </div>

                <div class="note-footer">
                    <div class="note-icons">
                        <span class="icon-wrapper" data-tooltip="Unarchive">
                            <i data-lucide="archive-restore" class="note-icon" data-action="toggle-archive" data-note-id="<?php echo (int)$note['id']; ?>"></i>
                        </span>
                        <span class="icon-wrapper" data-tooltip="Delete">
                            <i data-lucide="trash-2" class="note-icon danger" data-action="delete" data-note-id="<?php echo (int)$note['id']; ?>"></i>
                        </span>
                    </div>
                    <span class="note-date">
                        <?php echo date('M j, Y', strtotime($note['updated_at'])); ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Note Modal -->
<div class="modal" id="noteModal">
    <div class="modal-content">
        <div class="modal-header">
            <input
                type="hidden"
                id="noteId"
                name="note_id"
                value="">

            <input
                type="text"
                id="noteTitle"
                name="title"
                placeholder="Title"
                class="modal-title-input"
                value="">

            <button
                type="button"
                class="close-btn"
                id="closeModal"
                title="Close">
                ✕
            </button>
        </div>

        <div class="modal-body">
            <textarea
                id="noteDescription"
                name="description"
                placeholder="Take a note..."
                rows="6"
                class="form-input"></textarea>
        </div>

        <div class="modal-footer-main">
            <div class="color-picker">
                <button
                    type="button"
                    class="color-circle"
                    data-color="#ffffff"
                    style="background: #ffffff; border: 2px solid #ddd;"
                    title="White"></button>

                <button
                    type="button"
                    class="color-circle"
                    data-color="#FFF8E1"
                    style="background: #FFF8E1;"
                    title="Yellow"></button>

                <button
                    type="button"
                    class="color-circle"
                    data-color="#FFF475"
                    style="background: #FFF475;"
                    title="Orange"></button>

                <button
                    type="button"
                    class="color-circle"
                    data-color="#CCFF90"
                    style="background: #CCFF90;"
                    title="Green"></button>

                <button
                    type="button"
                    class="color-circle"
                    data-color="#AECBFA"
                    style="background: #AECBFA;"
                    title="Blue"></button>

                <button
                    type="button"
                    class="color-circle"
                    data-color="#FDCFE8"
                    style="background: #FDCFE8;"
                    title="Pink"></button>
            </div>

            <div class="modal-icons">
                <span class="icon-wrapper" data-tooltip="Pin">
                    <i data-lucide="pin" id="pinBtn" class="modal-icon"></i>
                </span>
                <span class="icon-wrapper" data-tooltip="Unarchive">
                    <i data-lucide="archive" id="archiveBtn" class="modal-icon"></i>
                </span>
                <span class="icon-wrapper" data-tooltip="Delete">
                    <i data-lucide="trash-2" id="deleteBtn" class="modal-icon danger"></i>
                </span>
            </div>
        </div>

        <div class="modal-footer-info">
            <div class="note-dates" id="noteDates" style="display: none;">
                <small>
                    <div id="createdDate"></div>
                    <div id="updatedDate"></div>
                </small>
            </div>

            <div class="modal-actions-final">
                <button
                    type="button"
                    class="btn-link"
                    id="cancelBtn">
                    Close
                </button>

                <button
                    type="button"
                    class="btn-link btn-save"
                    id="saveBtn">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast" id="toast"></div>

<?php include 'includes/footer.php'; ?>