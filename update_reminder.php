<?php
header('Content-Type: application/json');
include 'db_connect.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Check if form data is received
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $description = $conn->real_escape_string($_POST['description']);
    $reminder_date = $conn->real_escape_string($_POST['reminder_date']);
    $reminder_time = $conn->real_escape_string($_POST['reminder_time']);
    $location = $conn->real_escape_string($_POST['location']);

    $stmt = $conn->prepare("UPDATE reminders SET description=?, reminder_date=?, reminder_time=?, location=? WHERE id=?");
    $stmt->bind_param('ssssi', $description, $reminder_date, $reminder_time, $location, $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Reminder updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update reminder"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>
