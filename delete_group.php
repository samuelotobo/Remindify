<?php
// Include database connection
include 'db_connection.php'; // Replace with your actual database connection file

// Initialize response array
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if group_id is provided
    if (isset($_POST['group_id'])) {
        $group_id = $_POST['group_id'];

        // Validate that group_id is a valid number
        if (is_numeric($group_id)) {
            // Optional: Check if the user has permission to delete the group
            // Assuming you have a session variable for user ID (e.g., $_SESSION['user_id'])
            session_start();
            $user_id = $_SESSION['user_id'];

            // Query to check if the user is the group admin or has deletion rights
            $stmt = $conn->prepare("SELECT * FROM groups WHERE group_id = ?");
            $stmt->bind_param("ii", $group_id, $user_id); // Bind group_id and user_id
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // If user has permission, proceed to delete the group
                $stmt = $conn->prepare("DELETE FROM groups WHERE group_id = ?");
                $stmt->bind_param("i", $group_id);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Group deleted successfully!';
                } else {
                    $response['message'] = 'Error deleting group.';
                }
            } else {
                $response['message'] = 'You do not have permission to delete this group.';
            }

            $stmt->close();
        } else {
            $response['message'] = 'Invalid group ID.';
        }
    } else {
        $response['message'] = 'Group ID not provided.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

?>
