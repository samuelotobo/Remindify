<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reminder_app";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}
else {
    // Fetch reminders
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $query = $user_id ? "SELECT * FROM reminders WHERE user_id = ?" : "SELECT id, description, reminder_date, reminder_time, location FROM reminders";
    $stmt = $conn->prepare($query);

    if ($user_id) {
        $stmt->bind_param('i', $user_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $reminders = [];

    while ($row = $result->fetch_assoc()) {
        $reminders[] = $row;
    }

    echo json_encode($reminders);
    $stmt->close();
}

$conn->close();
?>
