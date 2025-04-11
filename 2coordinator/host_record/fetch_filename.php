<?php
include("../../0config/database.php");

if (isset($_GET['trainer_id'])) {
    $trainerId = $_GET['trainer_id'];

    $sql = "SELECT 
                f.filename, 
                u.upload_status, 
                u.updated_on, 
                u.filepath,
                u.uploads_id
            FROM uploads u
            INNER JOIN filename f ON u.filename_id = f.filename_id
            WHERE u.uploadedby_id = ? AND f.category = 'hte'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $trainerId);
    $stmt->execute();
    $result = $stmt->get_result();

    $files = [];
    while ($row = $result->fetch_assoc()) {
        $files[] = $row;
    }

    echo json_encode($files);
}
?>
