<?php
session_start(); // Start the session to access session variables

include 'db_connect.php';


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = isset($_POST['location']) ? $_POST['location'] : '';

    // Get the user ID from the session
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

    // Check if the user is logged in
    if ($user_id) {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO reminders (description, reminder_date, reminder_time, location, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $description, $date, $time, $location, $user_id); // Bind parameters

        // Execute the statement
        if ($stmt->execute()) {
            echo "New reminder added successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close(); // Close the statement
    } else {
        echo "Error: User not logged in.";
    }
}

$conn->close();
?>