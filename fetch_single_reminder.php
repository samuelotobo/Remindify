<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reminder_app";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $reminder_id = $_GET['id'];

    $sql = "SELECT * FROM reminders WHERE id = '$reminder_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Reminder not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
