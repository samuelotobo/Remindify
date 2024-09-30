<?php
// Include database connection
include 'db_connect.php'; // Ensure this file contains your connection logic

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reminder_id = intval($_POST['id']); // Ensure you are getting an integer ID

    // Begin a transaction
    $conn->begin_transaction();

    try {
        // Step 1: Fetch reminder details from the reminders table
        $stmtFetch = $conn->prepare("SELECT * FROM reminders WHERE id = ?");
        $stmtFetch->bind_param('i', $reminder_id);
        $stmtFetch->execute();
        $result = $stmtFetch->get_result();
        $reminder = $result->fetch_assoc();

        if ($reminder) {
            // Step 2: Insert reminder details into the recycle_bin
            $stmtInsert = $conn->prepare("INSERT INTO recycle_bin (reminder_id, description, reminder_date, reminder_time, location, user_id, deleted_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmtInsert->bind_param('issssi', $reminder['id'], $reminder['description'], $reminder['reminder_date'], $reminder['reminder_time'], $reminder['location'], $reminder['user_id']);
            $stmtInsert->execute();
            $stmtInsert->close();

            // Step 3: Delete from the reminders table
            $stmtDelete = $conn->prepare("DELETE FROM reminders WHERE id = ?");
            $stmtDelete->bind_param('i', $reminder_id);
            $stmtDelete->execute();
            $stmtDelete->close();

            // Commit the transaction
            $conn->commit();

            echo json_encode(['success' => true, 'message' => 'Reminder deleted successfully and moved to Recycle Bin.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Reminder not found.']);
        }

        $stmtFetch->close();
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
