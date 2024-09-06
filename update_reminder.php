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

// Get the form data
$id = $_POST['id'];
$description = $_POST['description'];
$reminder_date = $_POST['reminder_date'];
$reminder_time = $_POST['reminder_time'];
$location = $_POST['location'];

// Update the reminder data
$sql = "UPDATE reminders SET description = '$description', reminder_date = '$reminder_date', reminder_time = '$reminder_time', location = '$location' WHERE id = '$id'";

if ($conn->query($sql) === TRUE) {
    echo "Success";
} else {
    echo "Error updating reminder: " . $conn->error;
}
?>
