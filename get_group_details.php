<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reminder_app";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['reminder_id'])) {
    $reminder_id = $_POST['reminder_id'];
    
    $sql = "SELECT * FROM group_reminders WHERE id = '$reminder_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'No reminder found']);
    }
}

$conn->close();
?>
