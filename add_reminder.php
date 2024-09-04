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
    // Get form data
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = isset($_POST['location']) ? $_POST['location'] : '';

    // Insert data into database
    $sql = "INSERT INTO reminders (description, reminder_date, reminder_time, location) 
            VALUES ('$description', '$date', '$time', '$location')";

    if ($conn->query($sql) === TRUE) {
        echo "New reminder added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
