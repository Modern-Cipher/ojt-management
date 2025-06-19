<?php
include("../../0config/database.php");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['oldFilename'], $data['newFilename'], $data['newCount'])) {
    echo json_encode(["success" => false, "error" => "Missing parameters"]);
    exit;
}

$oldFilename = trim($data['oldFilename']);
$newFilename = trim($data['newFilename']);
$newCount = trim($data['newCount']);

// Validate count: must be 1–20
if (!preg_match('/^\\d{1,2}$/', $newCount) || (int)$newCount < 1 || (int)$newCount > 20) {
    echo json_encode(["success" => false, "error" => "Invalid week number"]);
    exit;
}

$conn->begin_transaction();

try {
    // Get current filename_id and its count
    $stmt1 = $conn->prepare("SELECT filename_id, count FROM filename WHERE filename = ? AND category = 'journal'");
    $stmt1->bind_param("s", $oldFilename);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $row1 = $result1->fetch_assoc();

    if (!$row1) throw new Exception("Original entry not found");

    $oldId = $row1['filename_id'];
    $oldCount = $row1['count'];

    // Check if another record already uses the new count
    $stmt2 = $conn->prepare("SELECT filename_id FROM filename WHERE count = ? AND category = 'journal' AND filename_id != ?");
    $stmt2->bind_param("si", $newCount, $oldId);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $conflict = $result2->fetch_assoc();

    if ($conflict) {
        // Swap counts: move oldCount to conflicting row
        $conflictId = $conflict['filename_id'];
        $stmtSwap = $conn->prepare("UPDATE filename SET count = ? WHERE filename_id = ?");
        $stmtSwap->bind_param("si", $oldCount, $conflictId);
        $stmtSwap->execute();
    }

    // Update the current record
    $stmt3 = $conn->prepare("UPDATE filename SET filename = ?, count = ? WHERE filename_id = ?");
    $stmt3->bind_param("ssi", $newFilename, $newCount, $oldId);
    $stmt3->execute();

    $conn->commit();
    echo json_encode(["success" => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
