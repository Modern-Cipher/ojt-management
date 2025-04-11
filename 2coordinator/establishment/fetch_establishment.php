<?php
session_start();
include("../../0config/database.php");

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access!";
    exit();
}

$user_id = $_SESSION['user_id'];

// 🔥 1. Get current coordinator details
$detailsQuery = "SELECT institute, course FROM users WHERE users_id = ?";
$detailsStmt = $conn->prepare($detailsQuery);
$detailsStmt->bind_param("i", $user_id);
$detailsStmt->execute();
$detailsResult = $detailsStmt->get_result();
$details = $detailsResult->fetch_assoc();
$detailsStmt->close();

// Safety check (if user not found)
if (!$details) {
    echo "No user data found.";
    exit();
}

$institute = $details['institute'];
$course = $details['course'];

// 🔥 2. Fetch filtered establishments
$sql = "SELECT 
            hte.hte_id, 
            hte.hte_name, 
            hte.hte_address, 
            hte.hte_status, 
            users.fname, 
            users.lname, 
            users.institute, 
            users.course 
        FROM hte 
        LEFT JOIN users ON hte.coordinator_id = users.users_id 
        WHERE users.institute = ? AND users.course = ?
        ORDER BY hte.hte_id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $institute, $course);
$stmt->execute();
$result = $stmt->get_result();

$output = "";
$counter = 1;

while ($row = $result->fetch_assoc()) {
    $statusColor = match ($row['hte_status']) {
        'approved' => 'success',
        'pending' => 'dark',
        'rejected' => 'danger',
        default => 'secondary',
    };

    $tooltipText = "Added by: " . htmlspecialchars($row['fname'] . " " . $row['lname']) .
        "<br>Institute: " . htmlspecialchars($row['institute']) .
        "<br>Course: " . htmlspecialchars($row['course']);

    $output .= "<tr>
        <td>{$counter}</td>
        <td>" . htmlspecialchars($row['hte_name']) . "</td>
        <td>
            <a href='https://www.google.com/maps/search/" . urlencode($row['hte_address']) . "' 
            target='_blank' 
            data-bs-toggle='tooltip' 
            title='Click to search location'
            style='text-decoration: none; color: black; font-weight: bold;'>
            <i class='fa-solid fa-location-dot text-danger'> </i>
            " . htmlspecialchars($row['hte_address']) . " 
            
            </a>
        </td>


        <td><span class='badge bg-{$statusColor}'>" . ucfirst($row['hte_status']) . "</span></td>
        <td>
            <button class='btn btn-outline-secondary btn-sm edit-btn' 
                data-id='{$row['hte_id']}' 
                data-name='" . htmlspecialchars($row['hte_name']) . "' 
                data-address='" . htmlspecialchars($row['hte_address']) . "' 
                data-status='" . $row['hte_status'] . "' 
                data-tooltip='Update Info'>
                <i class='fas fa-edit'></i>
            </button>

            <button class='btn btn-outline-secondary btn-sm' data-tooltip='Remove Establishment' onclick='deleteEstablishment({$row["hte_id"]})'>
                <i class='fas fa-trash'></i>
            </button>

        </td>
    </tr>";
    $counter++;
}

echo $output;
$stmt->close();
$conn->close();
