<?php
require 'config/config.php';
requireLogin();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$note_id = $input['note_id'] ?? null;

if (!$note_id) {
    echo json_encode(['success' => false, 'error' => 'Note ID required']);
    exit;
}

try {
    // Verify note belongs to user
    $stmt = $conn->prepare("SELECT id FROM notes WHERE id = ? AND user_id = ?");
    $stmt->execute([$note_id, getCurrentUserId()]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'error' => 'Note not found']);
        exit;
    }

    // Toggle pin
    $stmt = $conn->prepare("UPDATE notes SET is_pinned = NOT is_pinned WHERE id = ? AND user_id = ?");
    $stmt->execute([$note_id, getCurrentUserId()]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to toggle pin']);
}
