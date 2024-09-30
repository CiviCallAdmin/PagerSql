<?php
include_once 'kurt_dbConn.php'; // Database connection

header("Content-Type: application/json");

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'sendMessage') {
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];
    $location = $_POST['location'];

    $query = "INSERT INTO tbl_kurtMessage (sender_device_id, receiver_device_id, location) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('iis', $sender_id, $receiver_id, $message);

    if ($stmt->execute()) {
        $response = array("status" => "success", "message" => "Message sent successfully.");
    } else {
        $response = array("status" => "error", "message" => "Failed to send message.");
    }

    echo json_encode($response);
}

if ($action == 'getMessages') {
    $device_id = $_GET['device_id'];

    $query = "SELECT m.message_id, d.user_name AS sender, m.location, m.sent_at 
              FROM tbl_kurtMessage m 
              JOIN tbl_kurtDevice d ON m.sender_device_id = d.device_id 
              WHERE m.receiver_device_id = ? ORDER BY m.sent_at DESC";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $device_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = array();

    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode($messages);
}
?>
