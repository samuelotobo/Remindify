<?php
// Enable detailed error reporting for debugging (Disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set the content type to JSON
header('Content-Type: application/json');

// Include the database connection file
include 'db_connect.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize the 'id' from POST data
    $reminder_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    // Validate the reminder ID
    if ($reminder_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid reminder ID provided.']);
        exit;
    }

    // Log the received reminder ID for debugging
    error_log("Received Reminder ID: " . $reminder_id);

    // Begin a database transaction
    $conn->begin_transaction();

    try {
        // Step 1: Fetch the reminder details from the 'reminders' table
        $stmtFetch = $conn->prepare("SELECT * FROM reminders WHERE id = ?");
        if (!$stmtFetch) {
            throw new Exception('Prepare failed (Fetch): ' . $conn->error);
        }

        $stmtFetch->bind_param('i', $reminder_id);
        if (!$stmtFetch->execute()) {
            throw new Exception('Execute failed (Fetch): ' . $stmtFetch->error);
        }

        $result = $stmtFetch->get_result();
        $reminder = $result->fetch_assoc();

        // Check if the reminder exists
        if (!$reminder) {
            throw new Exception('Reminder not found with ID: ' . $reminder_id);
        }

        // Log the fetched reminder details
        error_log("Fetched Reminder: " . json_encode($reminder));

        // Define a default group name or retrieve it as needed
        $default_group_name = 'default_group'; // Change this as per your requirements

        // Step 2: Insert the reminder details into the 'recycle_bin' table
        $stmtInsert = $conn->prepare("
            INSERT INTO recycle_bin (reminder_id, description, reminder_date, reminder_time, location, user_id, deleted_at, group_name) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)
        ");
        if (!$stmtInsert) {
            throw new Exception('Prepare failed (Insert): ' . $conn->error);
        }

        $stmtInsert->bind_param(
            'issssis',
            $reminder['id'],
            $reminder['description'],
            $reminder['reminder_date'],
            $reminder['reminder_time'],
            $reminder['location'],
            $reminder['user_id'],
            $default_group_name
        );

        if (!$stmtInsert->execute()) {
            throw new Exception('Execute failed (Insert): ' . $stmtInsert->error);
        }

        // Log successful insertion into recycle_bin
        error_log("Inserted Reminder ID {$reminder['id']} into recycle_bin successfully.");

        // Step 3: Delete the reminder from the 'reminders' table
        $stmtDelete = $conn->prepare("DELETE FROM reminders WHERE id = ?");
        if (!$stmtDelete) {
            throw new Exception('Prepare failed (Delete): ' . $conn->error);
        }

        $stmtDelete->bind_param('i', $reminder_id);
        if (!$stmtDelete->execute()) {
            throw new Exception('Execute failed (Delete): ' . $stmtDelete->error);
        }

        // Check if the delete operation affected any rows
        if ($stmtDelete->affected_rows === 0) {
            throw new Exception('No rows deleted. Reminder ID may not exist.');
        }

        // Log successful deletion from reminders
        error_log("Deleted Reminder ID {$reminder_id} from reminders successfully.");

        // Commit the transaction
        $conn->commit();
        
        // Respond with a success message
        echo json_encode([
            'success' => true,
            'message' => 'Reminder moved to Recycle Bin and deleted from reminders successfully.'
        ]);

        // Close the prepared statements
        $stmtFetch->close();
        $stmtInsert->close();
        $stmtDelete->close();

    } catch (Exception $e) {
        // Rollback the transaction in case of any errors
        $conn->rollback();

        // Log the error message
        error_log("Transaction failed: " . $e->getMessage());

        // Respond with the error message
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }

    // Close the database connection
    $conn->close();

} else {
    // Respond with an error if the request method is not POST
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Please use POST.'
    ]);
}
?>
