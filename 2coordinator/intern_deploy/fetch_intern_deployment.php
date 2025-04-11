<?php
session_start();
include("../../0config/database.php");
header('Content-Type: application/json');

try {
    $sql = "SELECT 
                u.users_id,
                u.school_id,
                CONCAT(u.fname, ' ', u.lname) AS fullname,
                u.username,
                u.course,
                u.address,
                u.email,
                u.phone,
                u.ojt_status,
                h.hte_name
            FROM users u
            LEFT JOIN hte h ON u.assigned_hte = h.hte_id
            WHERE u.role = 'student'";

    $result = $conn->query($sql);
    $data = [];

    if ($result) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'no' => $count++,
                'users_id' => $row['users_id'],
                'school_id' => $row['school_id'] ?? '-',
                'fullname' => $row['fullname'] ?? '-',
                'username' => $row['username'] ?? '-',
                'course' => $row['course'] ?? '-',
                'address' => $row['address'] ?? '-',
                'email' => $row['email'] ?? '-',
                'phone' => $row['phone'] ?? '-',
                'ojt_status' => $row['ojt_status'] ?? '-',
                'hte_name' => $row['hte_name'] ?? '-'
            ];
        }
    }

    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
$conn->close();
?>
