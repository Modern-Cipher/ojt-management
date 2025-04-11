<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = [];

$sql = "SELECT users_id, fname, lname, email, username, designation, users_account 
        FROM users 
        WHERE coordinator_id = ? AND role = 'trainer'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$no = 1;

while ($row = $result->fetch_assoc()) {
    $fullname = $row['fname'] . " " . $row['lname'];
    $email = !empty($row['email']) ? $row['email'] : "-";
    $designation = !empty($row['designation']) ? $row['designation'] : "-";

    // Condition: HTE assigned to coordinator AND has trainer_id
    $hte_sql = "SELECT hte_name, hte_address 
                FROM hte 
                WHERE coordinator_id = ? AND trainer_id = ?";
    $hte_stmt = $conn->prepare($hte_sql);
    $hte_stmt->bind_param("ii", $user_id, $row['users_id']);
    $hte_stmt->execute();
    $hte_result = $hte_stmt->get_result();

    if ($hte_result->num_rows > 0) {
        $hte = $hte_result->fetch_assoc();
        $hte_name = $hte['hte_name'];
        $hte_address = $hte['hte_address'];
    } else {
        $hte_name = "No HTE Assigned";
        $hte_address = "No HTE Assigned";
    }
    $hte_stmt->close();

    $data[] = [
        "no" => $no++,
        "users_id" => $row['users_id'], // 🔥 ADD THIS
        "fullname" => $fullname,
        "email" => $email,
        "username" => $row['username'],
        "designation" => $designation,
        "establishment_name" => $hte_name,
        "establishment_address" => $hte_address,
        "status" => $row['users_account']
    ];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
