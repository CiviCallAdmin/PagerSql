<?php
include "kurt_dbConn.php"; // Ensure this file is included

// Fetch all user data from tbl_kurtDevice
$stmt = $mysqli->prepare("SELECT user_name, profile_pic, device_id, department FROM tbl_kurtDevice");
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = [
        "user_name" => $row['user_name'],
        "profile_pic" => $row['profile_pic'],
        "device_id" => $row['device_id'],
        "department" => $row['department']
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($users);

// Close the connection
$stmt->close();
$mysqli->close();
?>
