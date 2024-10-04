<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "remindify";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_id = $_POST['group-id'];
    $title = $_POST['reminder-title'];
    $reminder_date = $_POST['reminder-date'];
    $reminder_time = $_POST['reminder-time'];
    $description = $_POST['reminder-description'];

    // Insert data into the database
    $sql = "INSERT INTO group_reminders (group_id, title, reminder_date, reminder_time, description) 
            VALUES ('$group_id', '$title', '$reminder_date', '$reminder_time', '$description')";

    if ($conn->query($sql) === TRUE) {
        echo "Reminder added successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
