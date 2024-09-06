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

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch all reminders for the logged-in user
$sql = "SELECT * FROM reminders WHERE user_id = '$user_id'";
$result = $conn->query($sql);

$reminders = [];
while ($row = $result->fetch_assoc()) {
    $reminders[] = $row;
}

echo json_encode($reminders);
?>
