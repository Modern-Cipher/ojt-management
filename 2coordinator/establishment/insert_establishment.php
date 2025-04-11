<?php
include("../../0config/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['establishmentName']);
    $address = trim($_POST['establishmentAddress']);
    $status = trim($_POST['establishmentStatus']);
    $coordinator_id = intval($_POST['coordinator_id']);

    $stmt = $conn->prepare("INSERT INTO hte (hte_name, hte_address, hte_status, coordinator_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $address, $status, $coordinator_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>
