<?php
include "kurt_dbConn.php"; // Ensure this file is included

// Get the data from POST request
$user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
$profile_pic = isset($_POST['profile_pic']) ? $_POST['profile_pic'] : '';
$device_token = isset($_POST['device_token']) ? $_POST['device_token'] : '';
$department = isset($_POST['department']) ? $_POST['department'] : '';

// Check if the connection is valid
if (!$mysqli) {
    die("Connection failed: " . mysqli_connect_error());
}

// Prepare and bind
$stmt = $mysqli->prepare("INSERT INTO tbl_kurtDevice (user_name, profile_pic, device_token, department) VALUES (?, ?, ?, ?)");
if ($stmt === false) {
    die("Prepare failed: " . $mysqli->error);
}
$stmt->bind_param("ssss", $user_name, $profile_pic, $device_token, $department);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$mysqli->close();
?>
