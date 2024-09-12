<?php 
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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

$user_id = $_SESSION['user_id'];
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

            if ($attachments) {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($_FILES["event_attachments"]["name"]);
                
                if (!move_uploaded_file($_FILES["event_attachments"]["tmp_name"], $target_file)) {
                    $errorMessages[] = "Error uploading file.";
                }
            }

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

$stmt = $conn->prepare("SELECT * FROM events WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$eventsResult = $stmt->get_result();

if (!$eventsResult) {
    $errorMessages[] = "Error fetching events: " . $conn->error;
}

$stmt = $conn->prepare("SELECT * FROM user_wallet WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$walletResult = $stmt->get_result();

if (!$walletResult) {
    $errorMessages[] = "Error fetching wallet info: " . $conn->error;
} else {
    $wallet = $walletResult->fetch_assoc();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Calendar & Wallet │ Remindify</title>
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <aside>
            <div class="top">
                <div class="logo">
                    <img src="logo.png" alt="Remindify Logo">
                    <h2><span class="primary">Remind</span><span class="danger">Ify</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span>
                </div>
            </div>

            <div class="sidebar">
                <a href="dashboard.php">
                    <span class="material-icons-sharp">grid_view</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="AllReminders.php">
                    <span class="material-icons-sharp">today</span>
                    <h3>All Reminders</h3>
                </a>
                <a href="shared.php">
                    <span class="material-icons-sharp">schedule_send</span>
                    <h3>Shared Calendar</h3>
                </a>
                <a href="calendar.php">
                    <span class="material-icons-sharp">calendar_month</span>
                    <h3>Calendar</h3>
                </a>
                <a href="#" class="active">
                    <span class="material-icons-sharp">savings</span>
                    <h3>My Wallet</h3>
                </a>
                <!-- <a href="completed.php">
                    <span class="material-icons-sharp">assignment_turned_in</span>
                    <h3>Completed</h3>
                </a> -->
                <a href="recyclebin.php">
                    <span class="material-icons-sharp">delete</span>
                    <h3>Recycle Bin</h3>
                </a>
                <div class="bottom-buttons">
                    <a href="accountsettings.php">
                        <span class="material-icons-sharp">account_circle</span>
                        <h3>Account Settings</h3>
                    </a>
                    <form action="logout.php" method="post" style="display: inline;">
                        <button type="submit" class="red">
                            <span class="material-icons-sharp">logout</span>
                            <h3>Log Out</h3>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main>
            <div class="top">
                <button id="menu-btn">
                    <span class="material-icons-sharp">menu</span>
                </button>
                <div class="theme-toggle">
                    <span class="material-icons-sharp">light_mode</span>
                    <span class="material-icons-sharp">dark_mode</span>
                </div>
            </div>

            <h1>My Wallet</h1>

            <div class="cards">
                <!-- Wallet Info -->
                <div class="card">
                    <h3>Balance</h3>
                    <p id="wallet-balance"><?php echo isset($wallet['current_balance']) ? '$' . number_format($wallet['current_balance'], 2) : 'Loading...'; ?></p>
                </div>
                <div class="card">
                    <h3>Spending Limit</h3>
                    <form id="spending-limit-form">
                        <input type="number" id="spending-limit" name="spending_limit" placeholder="Enter new limit">
                        <button type="submit">Set Limit</button>
                    </form>
                </div>
            </div>

            <!-- Expense Form -->
            <div class="card">
                <h3>Add Expense</h3>
                <form id="expense-form" method="POST">
                    <input type="hidden" name="action" value="add_expense">
                    <input type="number" name="expense_amount" placeholder="Amount" required>
                    <input type="text" name="expense_category" placeholder="Category" required>
                    <input type="text" name="expense_description" placeholder="Description" required>
                    <button type="submit">Add Expense</button>
                </form>
            </div>

            <!-- Event Form -->
            <div class="card">
                <h3>Add Event</h3>
                <form id="event-form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_event">
                    <input type="text" name="event_title" placeholder="Event Title" required>
                    <input type="date" name="event_date" required>
                    <input type="time" name="event_time" required>
                    <textarea name="event_description" placeholder="Event Description" required></textarea>
                    <input type="file" name="event_attachments">
                    <button type="submit">Add Event</button>
                </form>
            </div>

            <!-- Event List -->
            <div class="card">
                <h3>Upcoming Events</h3>
                <ul id="event-list">
                    <?php while ($event = $eventsResult->fetch_assoc()): ?>
                        <li><?php echo htmlspecialchars($event['title']) . ' - ' . htmlspecialchars($event['event_time']); ?></li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle form submissions
            document.getElementById('expense-form').addEventListener('submit', function(e) {
                e.preventDefault();
                fetch('', {
                    method: 'POST',
                    body: new URLSearchParams(new FormData(this))
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Expense added successfully');
                        document.getElementById('expense-form').reset();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            document.getElementById('event-form').addEventListener('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Event added successfully');
                        document.getElementById('event-form').reset();
                        // Optionally refresh the event list
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            document.getElementById('spending-limit-form').addEventListener('submit', function(e) {
                e.preventDefault();
                fetch('', {
                    method: 'POST',
                    body: new URLSearchParams(new FormData(this))
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Spending limit updated');
                        document.getElementById('spending-limit-form').reset();
                        document.getElementById('wallet-balance').innerText = `$${data.current_balance.toFixed(2)}`;
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>
</body>
</html>
