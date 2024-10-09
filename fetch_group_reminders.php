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

// Check if the group-id is set in the GET request
if (isset($_GET['group-id'])) {
    $group_id = $_GET['group-id'];

    // Fetch group reminders based on group-id
    $sql = "SELECT * FROM group_reminders WHERE group_id = '$group_id'";
    $result = $conn->query($sql);

    $reminders = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $reminders[] = $row;
        }
    }

    // Output the reminders in JSON format
    echo json_encode($reminders);
} else {
    echo json_encode(array("error" => "No group-id provided"));
}

$conn->close();
?>
