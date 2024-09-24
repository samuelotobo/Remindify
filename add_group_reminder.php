<?php
// Include database connection
include 'db_connection.php'; // Replace with your actual database connection file

// Initialize response array
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if required fields are provided
    if (isset($_POST['group_id'], $_POST['description'], $_POST['date'], $_POST['time'])) {
        $group_id = $_POST['group_id'];
        $description = $_POST['description'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        
        // Validate inputs
        if (is_numeric($group_id) && !empty($description) && !empty($date) && !empty($time)) {
            // Prepare the SQL statement
            $stmt = $conn->prepare("INSERT INTO group_reminders (group_id, description, reminder_date, reminder_time) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $group_id, $description, $date, $time);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Reminder added successfully!';
            } else {
                $response['message'] = 'Error adding reminder: ' . $conn->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Invalid input data.';
        }
    } else {
        $response['message'] = 'Required fields are missing.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
