<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupId = $_POST['group_id'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Database connection
    require 'db_connection.php';

    $query = "INSERT INTO group_reminders (group_id, description, reminder_date, reminder_time) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $groupId, $description, $date, $time);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add reminder.']);
    }
}
?>
