<?php
session_start();
include 'db_connect.php'; // Ensure you have your DB connection setup

// Initialize response array
$response = ['status' => 'error', 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    $response['message'] = "You must be logged in to join a group.";
    echo json_encode($response);
    exit;
}

if (isset($_GET['code'])) {
    $join_code = $_GET['code'];
    
    // Check if the join code exists
    $stmt = $conn->prepare("SELECT group_id FROM shared_groups WHERE join_code = ?");
    if (!$stmt) {
        $response['message'] = "Database prepare failed: " . $conn->error;
        echo json_encode($response);
        exit;
    }

    $stmt->bind_param("s", $join_code);
    if (!$stmt->execute()) {
        $response['message'] = "Database execute failed: " . $stmt->error;
        echo json_encode($response);
        exit;
    }

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $group = $result->fetch_assoc();
        $group_id = $group['group_id'];
        $user_id = $_SESSION['user_id'];
        
        // Check if the user is already a participant
        $check_stmt = $conn->prepare("SELECT * FROM participants WHERE user_id = ? AND group_id = ?");
        if (!$check_stmt) {
            $response['message'] = "Database prepare failed for check: " . $conn->error;
            echo json_encode($response);
            exit;
        }

        $check_stmt->bind_param("ii", $user_id, $group_id);
        if (!$check_stmt->execute()) {
            $response['message'] = "Check execute failed: " . $check_stmt->error;
            echo json_encode($response);
            exit;
        }

        $check_result = $check_stmt->get_result();
        if ($check_result->num_rows == 0) {
            // Add the user as a participant
            $insert_stmt = $conn->prepare("INSERT INTO participants (user_id, group_id) VALUES (?, ?)");
            if (!$insert_stmt) {
                $response['message'] = "Insert prepare failed: " . $conn->error;
                echo json_encode($response);
                exit;
            }

            $insert_stmt->bind_param("ii", $user_id, $group_id);
            if ($insert_stmt->execute()) {
                http_response_code(200); // OK
                $response['status'] = 'success';
                $response['message'] = "You have successfully joined the group!";
                $response['group_id'] = $group_id;
            } else {
                $response['message'] = "Insert failed: " . $insert_stmt->error;
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
