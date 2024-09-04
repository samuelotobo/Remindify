<?php
include 'auth.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reminder_app";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reminder_id = $_POST['id'];

    $sql = "DELETE FROM reminders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $reminder_id);

    if ($stmt->execute()) {
        echo "Reminder deleted successfully.";
    } else {
        echo "Error deleting reminder: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
