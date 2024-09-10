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
$id = $_POST['id'];

// Delete the reminder
$sql = "DELETE FROM reminders WHERE id = '$id'";

if ($conn->query($sql) === TRUE) {
    echo "Success";
} else {
    echo "Error deleting reminder: " . $conn->error;
}
?>
