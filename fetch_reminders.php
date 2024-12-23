<?php
session_start(); // Start the session to access session variables
header('Content-Type: application/json');

include 'db_connect.php';


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
} else {
    // Fetch reminders
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    
    // Ensure we only fetch reminders for the logged-in user
    if ($user_id) {
        $query = "SELECT * FROM reminders WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id);
    } else {
        // Optionally handle the case where the user is not logged in
        echo json_encode(["error" => "User not logged in."]);
        exit();
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $reminders = [];

    while ($row = $result->fetch_assoc()) {
        $reminders[] = $row;
    }

    echo json_encode($reminders);
    $stmt->close();
}

$conn->close();
?>
