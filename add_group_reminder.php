<?php
// Include database connection
include 'db_connection.php';

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
        // Successfully inserted reminder
        $response = array('success' => true, 'message' => 'Reminder added successfully.');
    } else {
        // Error occurred while inserting reminder
        $response = array('success' => false, 'message' => 'Error adding reminder: ' . $conn->error);
    }

} else {
    // Missing required POST data
    $response = array('success' => false, 'message' => 'Invalid input. All fields are required.');
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$conn->close();
?>
