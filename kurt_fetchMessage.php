<?php
include "kurt_dbConn.php"; // Ensure this file is included

// Get device token from GET request
$device_token = isset($_GET['device_token']) ? $_GET['device_token'] : '';

// Check if the connection is valid
if (!$mysqli) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get device_id based on device_token
$stmt = $mysqli->prepare("SELECT device_id FROM tbl_kurtDevice WHERE device_token = ?");
$stmt->bind_param("s", $device_token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $device_id = $row['device_id'];

    // Fetch messages where the receiver_device_id matches the device_id
    $stmt = $mysqli->prepare("SELECT m.*, d.user_name, d.profile_pic FROM tbl_kurtMessage m
                               JOIN tbl_kurtDevice d ON m.sender_device_id = d.device_id
                               WHERE m.receiver_device_id = ? ORDER BY m.sent_at DESC");
    $stmt->bind_param("i", $device_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($message = $result->fetch_assoc()) {
        $messages[] = $message; // Add each message to the array
    }
    
    echo json_encode($messages);
} else {
    echo json_encode([]);
}

// Close the connection
$stmt->close();
$mysqli->close();
?>
