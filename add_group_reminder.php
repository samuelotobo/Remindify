<?php 
header('Content-Type: application/json');

include 'db_connect.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input
    $group_id = intval($_POST['group_id']);
    $title = trim($_POST['reminder_title']);
    $reminder_date = $_POST['reminder_date'];
    $reminder_time = $_POST['reminder_time'];
    $description = trim($_POST['reminder_description']);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO group_reminders (group_id, title, reminder_date, reminder_time, description) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("issss", $group_id, $title, $reminder_date, $reminder_time, $description);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Reminder added successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>
