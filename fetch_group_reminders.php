<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "remindify"; // Ensure consistent database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Check if the group_id is set in the GET request
if (isset($_GET['group_id'])) {
    $group_id = intval($_GET['group_id']);

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM group_reminders WHERE group_id = ?");
    if ($stmt === false) {
        echo json_encode(["error" => "Prepare failed: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $reminders = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $reminders[] = $row;
        }
    }

    // Output the reminders in JSON format
    echo json_encode($reminders);

    $stmt->close();
} else {
    echo json_encode(["error" => "No group_id provided"]);
}

$conn->close();
?>
