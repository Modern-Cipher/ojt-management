<?php
session_start();
include("../../0config/database.php");

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['uploadsId'], $data['comment'], $data['commenter_id'], $data['uploadedby_id'])) {
    $uploadsId = $data['uploadsId'];
    $comment = trim($data['comment']);
    $commenterId = $data['commenter_id']; // coordinator_id
    $uploadedById = $data['uploadedby_id']; // student_id

    // Get filename_id and institute from uploads table
    $stmt = $conn->prepare("SELECT filename_id, institute FROM uploads 
                            INNER JOIN users ON uploads.uploadedby_id = users.users_id 
                            WHERE uploads_id = ?");
    $stmt->bind_param("i", $uploadsId);
    $stmt->execute();
    $result = $stmt->get_result();
    $file = $result->fetch_assoc();
    $stmt->close();

    if ($file) {
        $filenameId = $file['filename_id'];
        $studentInstitute = $file['institute'];

        // Check if coordinator & student belong to same institute
        $stmt = $conn->prepare("SELECT institute FROM users WHERE users_id = ?");
        $stmt->bind_param("i", $commenterId);
        $stmt->execute();
        $result = $stmt->get_result();
        $coordinator = $result->fetch_assoc();
        $stmt->close();

        if ($coordinator && $coordinator['institute'] === $studentInstitute) {
            // Insert comment
            $stmt = $conn->prepare("INSERT INTO file_comments (filename_id, commenter_id, uploadedby_id, comment, created_at) 
                                    VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("iiis", $filenameId, $commenterId, $uploadedById, $comment);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to insert comment.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Institute mismatch.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'File not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
}

$conn->close();
?>
