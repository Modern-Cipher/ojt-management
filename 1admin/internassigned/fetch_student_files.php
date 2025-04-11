<?php
include("../../0config/database.php");

if (!isset($_GET['student_id'])) {
    echo json_encode(["error" => "Student ID missing"]);
    exit();
}

$student_id = $_GET['student_id'];

$sql = "
    SELECT 
        f.category,
        f.filename,
        u.file_name,
        u.filepath,
        u.upload_status,
        u.updated_on
    FROM uploads u
    INNER JOIN filename f ON f.filename_id = u.filename_id
    WHERE u.uploadedby_id = ?
    ORDER BY f.category, u.updated_on DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$files = [
    "pre" => [],
    "post" => [],
    "journal" => []
];

while ($row = $result->fetch_assoc()) {
    $category = $row['category'];
    if (in_array($category, ['pre', 'post', 'journal'])) {
        $files[$category][] = [
            "filename" => $row['filename'] ?? "",
            "file_name" => $row['file_name'] ?? "",
            "filepath" => $row['filepath'] ?? "",
            "upload_status" => $row['upload_status'] ?? "",
            "updated_on" => !empty($row['updated_on']) ? date("F d, Y - h:i A", strtotime($row['updated_on'])) : ""
        ];
    }
}

$stmt->close();
$conn->close();

echo json_encode($files);
