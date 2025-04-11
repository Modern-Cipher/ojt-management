<?php
include("../0config/database.php");

// Fetch count of each document category (pre, post, hte, journal) grouped by student
$sql = "
    SELECT 
        u.users_id,
        CONCAT(u.fname, ' ', u.lname) AS full_name,
        SUM(CASE WHEN f.category = 'pre' THEN 1 ELSE 0 END) AS pre,
        SUM(CASE WHEN f.category = 'post' THEN 1 ELSE 0 END) AS post,
        SUM(CASE WHEN f.category = 'hte' THEN 1 ELSE 0 END) AS hte,
        SUM(CASE WHEN f.category = 'journal' THEN 1 ELSE 0 END) AS journal
    FROM users u
    LEFT JOIN uploads up ON u.users_id = up.uploadedby_id
    LEFT JOIN filename f ON up.filename_id = f.filename_id
    WHERE u.role = 'student'
    GROUP BY u.users_id, full_name
";

$result = $conn->query($sql);

$data = [
    "labels" => [],
    "pre" => [],
    "post" => [],
    "hte" => [],
    "journal" => []
];

while ($row = $result->fetch_assoc()) {
    $data["labels"][] = $row["full_name"];
    $data["pre"][] = (int) $row["pre"];
    $data["post"][] = (int) $row["post"];
    $data["hte"][] = (int) $row["hte"];
    $data["journal"][] = (int) $row["journal"];
}

header('Content-Type: application/json');
echo json_encode($data);