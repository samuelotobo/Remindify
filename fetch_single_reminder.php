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

$reminder_id = $_GET['id'];

$sql = "SELECT * FROM reminders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $reminder_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode([]);
}
?>
