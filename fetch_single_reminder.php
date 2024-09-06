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

// Get reminder ID from the request
$reminder_id = $_GET['id'];

// Fetch the reminder data
$sql = "SELECT * FROM reminders WHERE id = '$reminder_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $reminder = $result->fetch_assoc();
    echo json_encode($reminder);
} else {
    echo json_encode(['error' => 'Reminder not found']);
}
?>
