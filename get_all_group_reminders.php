<?php
session_start();
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

// Check if group_id is provided
if (isset($_POST['group_id'])) {
    $group_id = intval($_POST['group_id']);
    
    // Fetch reminders for the specific group
    $query = "SELECT * FROM group_reminders WHERE group_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $group_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $reminders = [];
    while ($row = $result->fetch_assoc()) {
        $reminders[] = $row;
    }

    if (empty($reminders)) {
        echo json_encode(['error' => 'No reminders found for this group']);
    } else {
        echo json_encode($reminders);
    }
} else {
    echo json_encode(['error' => 'Group ID not provided']);
}

console.log('Fetching reminders for group ID:', groupId);


$stmt->close();
$conn->close();
?>
