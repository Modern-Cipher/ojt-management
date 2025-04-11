<?php
include("../../0config/database.php");

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['uploadsId'], $data['status'], $data['checkedby_id'])) {
    $id = $data['uploadsId'];
    $status = $data['status'];
    $checkedby = $data['checkedby_id'];

    $stmt = $conn->prepare("UPDATE uploads SET upload_status = ?, checkedby_id = ? WHERE uploads_id = ?");
    $stmt->bind_param("sii", $status, $checkedby, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No rows affected.']);
    }
    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Missing parameters.',
        'received_data' => $data
    ]);
}

$conn->close();
?>
