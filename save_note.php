<?php

require 'config/config.php';
requireLogin();

header('Content-Type: application/json');

try {

    // Read JSON request
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    if (!is_array($input)) {

        echo json_encode([
            'success' => false,
            'error' => 'Invalid data'
        ]);

        exit;
    }

    $note_id = $input['note_id'] ?? null;
    $title = trim($input['title'] ?? '');
    $description = trim($input['description'] ?? '');

    // Note colors
    $allowedColors = [
        '#ffffff',
        '#FFF8E1',
        '#FFF475',
        '#CCFF90',
        '#AECBFA',
        '#FDCFE8'
    ];

    $color = $input['color'] ?? '#ffffff';

    if (!in_array($color, $allowedColors, true)) {
        $color = '#ffffff';
    }

    // Note must contain either title or description
    if ($title === '' && $description === '') {

        echo json_encode([
            'success' => false,
            'error' => 'Note must have a title or description'
        ]);

        exit;
    }

    $userId = getCurrentUserId();


    
    //UPDATE EXISTING NOTE
     
    if (!empty($note_id)) {

        // Verify that the note belongs to the logged-in user
        $stmt = $conn->prepare("
            SELECT id
            FROM notes
            WHERE id = ?
              AND user_id = ?
            LIMIT 1
        ");

        $stmt->execute([
            $note_id,
            $userId
        ]);

        if (!$stmt->fetch()) {

            echo json_encode([
                'success' => false,
                'error' => 'Note not found'
            ]);

            exit;
        }


        // Update note
        $stmt = $conn->prepare("
            UPDATE notes
            SET
                title = ?,
                description = ?,
                color = ?,
                updated_at = NOW()
            WHERE id = ?
              AND user_id = ?
        ");

        $stmt->execute([
            $title,
            $description,
            $color,
            $note_id,
            $userId
        ]);


        echo json_encode([
            'success' => true,
            'message' => 'Note updated',
            'note_id' => $note_id
        ]);

        exit;
    }


    
    //CREATE NEW NOTE

    $stmt = $conn->prepare("
        INSERT INTO notes (
            user_id,
            title,
            description,
            color,
            is_pinned,
            is_archived,
            created_at,
            updated_at
        )
        VALUES (
            ?, ?, ?, ?, 0, 0, NOW(), NOW()
        )
    ");

    $stmt->execute([
        $userId,
        $title,
        $description,
        $color
    ]);

    $newNoteId = $conn->lastInsertId();


    echo json_encode([
        'success' => true,
        'message' => 'Note created',
        'note_id' => $newNoteId
    ]);
} catch (PDOException $e) {

    error_log(
        'save_note.php: ' . $e->getMessage()
    );

    echo json_encode([
        'success' => false,
        'error' => 'Failed to save note'
    ]);
}
