<?php
include "kurt_dbConn.php"; // Ensure this file is included

// Get the data from POST request
$device_token = isset($_POST['device_token']) ? $_POST['device_token'] : '';
$user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
$department = isset($_POST['department']) ? $_POST['department'] : '';
$profile_pic = isset($_FILES['profile_pic']) ? $_FILES['profile_pic'] : null;

// Check if the connection is valid
if (!$mysqli) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle image upload if provided
$imagePath = "";
if ($profile_pic) {
    $targetDir = "profilePic/";
    $fileName = basename($profile_pic["name"]);
    $targetFilePath = $targetDir . $fileName;
    
    // Move the uploaded file to the target directory
    if (move_uploaded_file($profile_pic["tmp_name"], $targetFilePath)) {
        $imagePath = $targetFilePath;
    } else {
        echo json_encode(["success" => false, "message" => "Image upload failed."]);
        exit();
    }
}

// Check for existing record with the device token
$stmt = $mysqli->prepare("SELECT user_name, department, profile_pic FROM tbl_kurtDevice WHERE device_token = ?");
$stmt->bind_param("s", $device_token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Record exists, fetch current user name, department, and profile pic
    $row = $result->fetch_assoc();
    $current_user_name = $row['user_name'];
    $current_department = $row['department'];
    $current_profile_pic = $row['profile_pic'];

    // If user name, department, or profile pic is changed, update the record
    if ($user_name !== $current_user_name || $department !== $current_department || !empty($imagePath)) {
        $update_stmt = $mysqli->prepare("UPDATE tbl_kurtDevice SET user_name = ?, department = ?, profile_pic = ? WHERE device_token = ?");
        $profilePicToSave = !empty($imagePath) ? $imagePath : $current_profile_pic; // Keep old image if not updated
        $update_stmt->bind_param("ssss", $user_name, $department, $profilePicToSave, $device_token);

        if ($update_stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Profile updated successfully."]);
        } else {
            echo json_encode(["success" => false, "error" => $update_stmt->error]);
        }
        $update_stmt->close();
    } else {
        echo json_encode(["success" => true, "message" => "No changes detected."]);
    }
} else {
    // No record found, insert new record
    $insert_stmt = $mysqli->prepare("INSERT INTO tbl_kurtDevice (device_token, user_name, department, profile_pic) VALUES (?, ?, ?, ?)");
    $insert_stmt->bind_param("ssss", $device_token, $user_name, $department, $imagePath);

    if ($insert_stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Profile created successfully."]);
    } else {
        echo json_encode(["success" => false, "error" => $insert_stmt->error]);
    }
    $insert_stmt->close();
}

// Close the connection
$stmt->close();
$mysqli->close();
?>
