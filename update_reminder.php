<?php
include 'auth.php';
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reminder_app";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$reminder_id = $_POST['id'];
$description = $_POST['description'];
$reminder_date = $_POST['reminder_date'];
$reminder_time = $_POST['reminder_time'];
$location = $_POST['location'];

$sql = "UPDATE reminders SET description = ?, reminder_date = ?, reminder_time = ?, location = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssi', $description, $reminder_date, $reminder_time, $location, $reminder_id);

if ($stmt->execute()) {
    echo "Success";
} else {
    echo "Error updating reminder: " . $conn->error;
}
?>
