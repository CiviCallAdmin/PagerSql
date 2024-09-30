<?php
include "kurt_dbConn.php"; // Ensure this file is included

// Get the data from GET request
$device_token = isset($_GET['device_token']) ? $_GET['device_token'] : '';

// Check if the connection is valid
if (!$mysqli) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check for existing record with the device token
$stmt = $mysqli->prepare("SELECT user_name, idNumber, profile_pic FROM tbl_kurtDevice WHERE device_token = ?");
$stmt->bind_param("s", $device_token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Record exists, fetch current user name, idNumber, and profile_pic
    $row = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "user_name" => $row['user_name'],
        "idNumber" => $row['idNumber'],
        "profile_pic" => $row['profile_pic'] // Include the profile_pic field
    ]);
} else {
    echo json_encode(["success" => false, "message" => "No user data found."]);
}

// Close the connection
$stmt->close();
$mysqli->close();
?>
