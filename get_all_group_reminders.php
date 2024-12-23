<?php
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Check if group_id is provided
if (isset($_POST['group_id'])) {
    $group_id = intval($_POST['group_id']);
    
    // Fetch reminders for the specific group
    $stmt = $conn->prepare("SELECT * FROM group_reminders WHERE group_id = ?");
    if ($stmt === false) {
        echo json_encode(["error" => "Prepare failed: " . $conn->error]);
        exit();
    }
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

    $stmt->close();
} else {
    echo json_encode(['error' => 'Group ID not provided']);
}

$conn->close();
?>
