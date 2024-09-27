<?php
// Include database connection
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $group_id = $_POST['group_id'];

    // Begin a transaction
    $conn->begin_transaction();

    try {
        // Step 1: Fetch reminder details from sharedgroups
        $stmtFetch = $conn->prepare("SELECT * FROM sharedgroups WHERE group_id = ?");
        $stmtFetch->bind_param('i', $group_id);
        $stmtFetch->execute();
        $result = $stmtFetch->get_result();
        $reminder = $result->fetch_assoc();

        if ($reminder) {
            // Step 2: Insert reminder details into recycle_bin
            $stmtInsert = $conn->prepare("INSERT INTO recycle_bin (reminder_id, description, reminder_date, reminder_time, location, user_id, deleted_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmtInsert->bind_param('issssi', $reminder['id'], $reminder['description'], $reminder['reminder_date'], $reminder['reminder_time'], $reminder['location'], $reminder['user_id']);
            $stmtInsert->execute();
            $stmtInsert->close();

            // Step 3: Delete from sharedgroups
            $stmtDelete = $conn->prepare("DELETE FROM sharedgroups WHERE group_id = ?");
            $stmtDelete->bind_param('i', $group_id);
            $stmtDelete->execute();
            $stmtDelete->close();

            // Commit the transaction
            $conn->commit();

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Reminder not found']);
        }

        $stmtFetch->close();
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
