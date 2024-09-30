<?php
include "kurt_dbConn.php"; // Ensure this file is included

// Get the data from POST request
$device_token = isset($_POST['device_token']) ? $_POST['device_token'] : '';
$receiver_device_id = isset($_POST['receiver_device_id']) ? $_POST['receiver_device_id'] : '';
$message_text = isset($_POST['message_text']) ? $_POST['message_text'] : '';

// Check if the connection is valid
if (!$mysqli) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get sender_device_id based on device_token
$stmt = $mysqli->prepare("SELECT device_id FROM tbl_kurtDevice WHERE device_token = ?");
$stmt->bind_param("s", $device_token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $sender_device_id = $row['device_id'];

    // Insert message into tbl_kurtMessage
    $stmt = $mysqli->prepare("INSERT INTO tbl_kurtMessage (sender_device_id, receiver_device_id, message_text, status) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("iis", $sender_device_id, $receiver_device_id, $message_text);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Message sent successfully."]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Sender device not found."]);
}

// Close the connection
$stmt->close();
$mysqli->close();
?>
