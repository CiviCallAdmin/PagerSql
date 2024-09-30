<?php
include "kurt_dbConn.php"; // Ensure this file is included

$message_id = isset($_POST['message_id']) ? $_POST['message_id'] : '';

// Update the message status to true (1)
$stmt = $mysqli->prepare("UPDATE tbl_kurtMessage SET status = 1 WHERE message_id = ?");
$stmt->bind_param("i", $message_id);
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Message status updated."]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

// Close the connection
$stmt->close();
$mysqli->close();
?>
