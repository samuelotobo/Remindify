<?php
// Start the session to access user ID
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized";
    exit();
}

$user_id = $_SESSION['user_id'];
$group_name = isset($_POST['group_name']) ? trim($_POST['group_name']) : '';

if (empty($group_name)) {
    echo "Group name cannot be empty";
    exit();
}

include 'db_connect.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle group creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupName = $_POST['group_name'];
    $createdBy = $user_id;
    
    // Generate a join code
    $joinCode = generateJoinCode();
    
    // Insert group data
    $sql = "INSERT INTO shared_groups (user_id, group_name, created_by, join_code) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isis", $createdBy, $groupName, $createdBy, $joinCode);
    
    if ($stmt->execute()) {
        // Construct the join link
        $joinLink =$joinCode;
        echo "Group created successfully. Join link:$joinLink";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Function to generate a join code
function generateJoinCode() {
    return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
}

$conn->close();
?>
