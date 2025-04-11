<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$trainer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT users_id, fname, lname, email, username, designation, sex 
        FROM users 
        WHERE users_id = ? AND role = 'trainer'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $trainer = $result->fetch_assoc();

    // Get assigned HTE
    $hte_sql = "SELECT hte_id FROM hte WHERE trainer_id = ?";
    $hte_stmt = $conn->prepare($hte_sql);
    $hte_stmt->bind_param("i", $trainer_id);
    $hte_stmt->execute();
    $hte_result = $hte_stmt->get_result();
    $hte = $hte_result->fetch_assoc();
    $hte_id = $hte ? $hte['hte_id'] : null;

    $trainer['hte_id'] = $hte_id;

    echo json_encode($trainer);
} else {
    echo json_encode(["error" => "Trainer not found"]);
}

$stmt->close();
$conn->close();
?>
