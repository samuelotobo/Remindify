<?php
// get_group_details.php
include 'auth.php';

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

// Get the group ID from the URL
$groupId = $_GET['group_id'];

// Fetch group details based on the ID
$sql = "SELECT group_name, created_by, join_code, created_at FROM sharedgroups WHERE group_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $groupId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Return the details as a JSON object
    echo json_encode([
        'group_name' => $row['group_name'],
        'created_by' => $row['created_by'],
        'join_code' => $row['join_code'],
        'created_at' => $row['created_at']
    ]);
} else {
    echo json_encode(['error' => 'Group not found']);
}

$stmt->close();
$conn->close();
?>
