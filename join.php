<?php
session_start();
include 'db_connect.php'; // Ensure you have your DB connection setup

$response = ['status' => 'error', 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = "You must be logged in to join a group.";
    echo json_encode($response);
    exit;  
}

if (isset($_GET['code'])) {
    $join_code = $_GET['code'];
    
    // Check if the join code exists
    $stmt = $conn->prepare("SELECT group_id FROM groups WHERE join_code = ?");
    $stmt->bind_param("s", $join_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Group exists
        $group = $result->fetch_assoc();
        $group_id = $group['group_id'];
        $user_id = $_SESSION['user_id']; // Assuming user is logged in and user_id is stored in session
        
        // Check if user is already a participant
        $check_stmt = $conn->prepare("SELECT * FROM participants WHERE user_id = ? AND group_id = ?");
        $check_stmt->bind_param("ii", $user_id, $group_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
            // Add the user as a participant
            $insert_stmt = $conn->prepare("INSERT INTO participants (user_id, group_id) VALUES (?, ?)");
            $insert_stmt->bind_param("ii", $user_id, $group_id);
            
            if ($insert_stmt->execute()) {
                // Successful join
                $response['status'] = 'success';
                $response['message'] = "You have successfully joined the group!";
                $response['group_id'] = $group_id; // Pass group ID for redirection
            } else {
                $response['message'] = "Error joining the group.";
            }
        } else {
            $response['message'] = "You are already a participant in this group.";
        }
    } else {
        $response['message'] = "Invalid join code.";
    }
} else {
    $response['message'] = "No join code provided.";
}

echo json_encode($response);
?>
