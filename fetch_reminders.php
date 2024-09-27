<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reminder_app";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Check for AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'edit':
                $id = $_POST['id'];
                $description = $_POST['description'];
                $reminder_date = $_POST['reminder_date'];
                $reminder_time = $_POST['reminder_time'];
                $location = $_POST['location'];

                $stmt = $conn->prepare("UPDATE reminders SET description=?, reminder_date=?, reminder_time=?, location=? WHERE id=?");
                $stmt->bind_param('ssssi', $description, $reminder_date, $reminder_time, $location, $id);
                if ($stmt->execute()) {
                    echo json_encode(["message" => "Reminder updated successfully!"]);
                } else {
                    echo json_encode(["error" => "Failed to update reminder."]);
                }
                $stmt->close();
                break;
                case 'delete':
                    $id = $_POST['id'];
    
                    $stmt = $conn->prepare("DELETE FROM reminders WHERE id=?");
                    $stmt->bind_param('i', $id);
                    if ($stmt->execute()) {
                        echo json_encode(["message" => "Reminder deleted successfully!"]);
                    } else {
                        echo json_encode(["error" => "Failed to delete reminder."]);
                    }
                    $stmt->close();
                    break;

            default:
                echo json_encode(["error" => "Invalid action."]);
                break;
        }
    } else {
        echo json_encode(["error" => "No action specified."]);
    }
} else {
    // Fetch reminders
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $query = $user_id ? "SELECT * FROM reminders WHERE user_id = ?" : "SELECT id, description, reminder_date, reminder_time, location FROM reminders";
    $stmt = $conn->prepare($query);

    if ($user_id) {
        $stmt->bind_param('i', $user_id);
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
