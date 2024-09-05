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

// Get the reminder ID from POST request
$reminder_id = isset($_POST['id']) ? intval($_POST['id']) : null;  // Ensure reminder_id is an integer

// Ensure ID is valid
if (!is_null($reminder_id)) {
    // Prepare the delete statement
    $sql = "DELETE FROM reminders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $reminder_id);

    // Execute the delete query
    if ($stmt->execute()) {
        echo 'Success';
    } else {
        echo 'Error deleting reminder: ' . $conn->error;
    }

    $stmt->close();
} else {
    echo 'Invalid reminder ID.';
}

$conn->close();
?>
