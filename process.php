<?php 
include 'auth.php'; 
include 'db_connect.php';


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data
$user_id = $_SESSION['user_id'];

// Initialize error messages
$errorMessages = [];
$current_balance = 0.00;

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'add_event') {
            $title = $_POST['event_title'];
            $date = $_POST['event_date'];
            $time = $_POST['event_time'];
            $description = $_POST['event_description'];
            $attachments = isset($_FILES['event_attachments']) ? $_FILES['event_attachments']['name'] : '';

            // Handle file upload
            if ($attachments) {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($_FILES["event_attachments"]["name"]);
                
                // Check if file upload is successful
                if (!move_uploaded_file($_FILES["event_attachments"]["tmp_name"], $target_file)) {
                    $errorMessages[] = "Error uploading file.";
                }
            }

            // Prepare and execute the insert statement
            $stmt = $conn->prepare("INSERT INTO events (user_id, title, event_time, description, attachments) VALUES (?, ?, ?, ?, ?)");
            $event_time = $date . ' ' . $time;
            $stmt->bind_param("issss", $user_id, $title, $event_time, $description, $attachments);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                $errorMessages[] = "Error: " . $stmt->error;
                echo json_encode(['success' => false, 'message' => $errorMessages]);
            }
            $stmt->close();
        } elseif ($action == 'edit_event') {
            // Handle event editing (implementation depends on specific requirements)
        } elseif ($action == 'delete_event') {
            $event_id = $_POST['event_id'];

            // Prepare and execute the delete statement
            $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
            $stmt->bind_param("i", $event_id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                $errorMessages[] = "Error: " . $stmt->error;
                echo json_encode(['success' => false, 'message' => $errorMessages]);
            }
            $stmt->close();
        } elseif ($action == 'add_expense') {
            $amount = $_POST['expense_amount'];
            $category = $_POST['expense_category'];
            $description = $_POST['expense_description'];

            // Fetch current balance
            $stmt = $conn->prepare("SELECT IFNULL(current_balance, 0.00) AS current_balance FROM user_wallet WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $wallet = $result->fetch_assoc();
            $current_balance = $wallet['current_balance'];
            $stmt->close();

            $new_balance = $current_balance - $amount;
            if ($new_balance < 0) {
                $errorMessages[] = "Insufficient balance.";
                echo json_encode(['success' => false, 'message' => $errorMessages]);
            } else {
                $stmt = $conn->prepare("UPDATE user_wallet SET current_balance = ? WHERE user_id = ?");
                $stmt->bind_param("di", $new_balance, $user_id);
                if (!$stmt->execute()) {
                    $errorMessages[] = "Error updating balance: " . $conn->error;
                    echo json_encode(['success' => false, 'message' => $errorMessages]);
                }
                $stmt->close();

                // Add expense record
                $stmt = $conn->prepare("INSERT INTO expenses (user_id, amount, category, description) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("idss", $user_id, $amount, $category, $description);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    $errorMessages[] = "Error recording expense: " . $stmt->error;
                    echo json_encode(['success' => false, 'message' => $errorMessages]);
                }
                $stmt->close();
            }
        } elseif ($action == 'set_spending_limit') {
            $spending_limit = $_POST['spending_limit'];
            $stmt = $conn->prepare("UPDATE user_wallet SET spending_limit = ? WHERE user_id = ?");
            $stmt->bind_param("di", $spending_limit, $user_id);
            if (!$stmt->execute()) {
                $errorMessages[] = "Error updating spending limit: " . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Fetch events for the calendar
$stmt = $conn->prepare("SELECT * FROM events WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$eventsResult = $stmt->get_result();

if (!$eventsResult) {
    $errorMessages[] = "Error fetching events: " . $conn->error;
}

// Fetch user wallet info
$stmt = $conn->prepare("SELECT * FROM user_wallet WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$walletResult = $stmt->get_result();

if (!$walletResult) {
    $errorMessages[] = "Error fetching wallet info: " . $conn->error;
} else {
    $wallet = $walletResult->fetch_assoc();
}

// Output any errors
if (!empty($errorMessages)) {
    echo json_encode(['success' => false, 'messages' => $errorMessages]);
} else {
    echo json_encode(['success' => true, 'current_balance' => $wallet['current_balance'] ?? 0.00]);
}

// Close connection
$conn->close();
?>
