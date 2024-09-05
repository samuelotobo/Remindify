<?php
// Start the session to access user ID
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized";
    exit();
}

$user_id = $_SESSION['user_id'];
$group_name = isset($_POST['group_name']) ? trim($_POST['group_name']) : '';

if (empty($group_name)) {
    echo "Group name cannot be empty";
    exit();
}

// Database connection
$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "reminder_app";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['group_name'])) {
    $group_name = trim($_POST['group_name']);
    if (!empty($group_name)) {
        $stmt = $conn->prepare("INSERT INTO groups (group_name, user_id) VALUES (?, ?)");
        $stmt->bind_param("si", $group_name, $user_id);
        
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Group name cannot be empty";
    }
} else {
    echo "Invalid request";
}

?>
