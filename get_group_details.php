<?php
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if reminder_id is set
if (isset($_POST['reminder_id'])) {
    $reminder_id = $_POST['reminder_id'];
    
    // Query to fetch reminder details by reminder_id
    $sql = "SELECT * FROM group_reminders WHERE id = '$reminder_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch reminder details
        $row = $result->fetch_assoc();
        echo json_encode($row);  // Send data back to the client as JSON
    } else {
        // No reminder found
        echo json_encode(['error' => 'No reminder found']);
    }
} else {
    // reminder_id not set
    echo json_encode(['error' => 'Reminder ID not provided']);
}

// Close the connection
$conn->close();
?>
