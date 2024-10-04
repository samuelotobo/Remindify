<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "remindify";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Fetch reminder details
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM reminders WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reminder = $result->fetch_assoc();

    if ($reminder) {
        echo json_encode($reminder);
    } else {
        echo json_encode(["error" => "Reminder not found."]);
    }
    $stmt->close();
} else {
    echo json_encode(["error" => "No ID provided."]);
}

$conn->close();
?>
