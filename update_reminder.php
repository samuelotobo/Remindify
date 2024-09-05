<?php
include 'auth.php';
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reminder_app";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$reminder_id = $_POST['id'];
$description = $_POST['description'];
$reminder_date = $_POST['reminder_date'];
$reminder_time = $_POST['reminder_time'];
$location = $_POST['location'];

// Fetch existing reminder data
$sql = "SELECT description, reminder_date, reminder_time, location FROM reminders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $reminder_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Initialize an array to hold the fields to update
    $fields_to_update = [];
    $params = [];
    $param_types = '';

    // Compare each field and only update if it has changed
    if ($description !== $row['description']) {
        $fields_to_update[] = "description = ?";
        $params[] = $description;
        $param_types .= 's'; // String
    }

    if ($reminder_date !== $row['reminder_date']) {
        $fields_to_update[] = "reminder_date = ?";
        $params[] = $reminder_date;
        $param_types .= 's'; // String
    }

    if ($reminder_time !== $row['reminder_time']) {
        $fields_to_update[] = "reminder_time = ?";
        $params[] = $reminder_time;
        $param_types .= 's'; // String
    }

    if ($location !== $row['location']) {
        $fields_to_update[] = "location = ?";
        $params[] = $location;
        $param_types .= 's'; // String
    }

    // If there are fields to update, build and execute the query
    if (!empty($fields_to_update)) {
        $param_types .= 'i'; // Add 'i' for the ID
        $params[] = $reminder_id; // Add the reminder ID to the params

        $sql = "UPDATE reminders SET " . implode(', ', $fields_to_update) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        // Use the spread operator to pass the array to bind_param
        $stmt->bind_param($param_types, ...$params);

        if ($stmt->execute()) {
            echo "Success";
        } else {
            echo "Error updating reminder: " . $conn->error;
        }
    } else {
        echo "No changes made.";
    }
} else {
    echo "Reminder not found.";
}

$stmt->close();
$conn->close();
?>
