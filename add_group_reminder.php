<?php
// Database connection
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if required fields are present
    if (isset($_POST['group_id']) && isset($_POST['description']) && isset($_POST['date']) && isset($_POST['time'])) {
        
        // Escape the input values to prevent SQL injection
        $group_id = $conn->real_escape_string($_POST['group_id']);
        $description = $conn->real_escape_string($_POST['description']);
        $date = $conn->real_escape_string($_POST['date']);
        $time = $conn->real_escape_string($_POST['time']);
        
        // Prepare the SQL statement to insert the new reminder
        $sql = "INSERT INTO group_reminders (group_id, description, reminder_date, reminder_time) 
                VALUES ('$group_id', '$description', '$date', '$time')";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array('success' => true, 'message' => 'Reminder added successfully.'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error adding reminder: ' . $conn->error));
        }

    } else {
        // Missing required POST data
        echo json_encode(array('success' => false, 'message' => 'Invalid input. All fields are required.'));
    }
}

$conn->close();
?>
