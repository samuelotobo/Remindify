<?php
include 'auth.php';
include 'db_connect.php';


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'get_notes') {
        $week_start = $_POST['week_start'];
        $stmt = $conn->prepare("SELECT * FROM weekly_notes WHERE user_id = ? AND week_start_date = ?");
        $stmt->bind_param("is", $user_id, $week_start);
        $stmt->execute();
        $result = $stmt->get_result();
        $notes = $result->fetch_assoc();
        echo json_encode($notes);
    } elseif ($action === 'save_notes') {
        $week_start = $_POST['week_start'];
        $monday = $_POST['monday'];
        $tuesday = $_POST['tuesday'];
        $wednesday = $_POST['wednesday'];
        $thursday = $_POST['thursday'];
        $friday = $_POST['friday'];
        $saturday = $_POST['saturday'];
        $sunday = $_POST['sunday'];

        // Check if notes for the week already exist
        $stmt = $conn->prepare("SELECT id FROM weekly_notes WHERE user_id = ? AND week_start_date = ?");
        $stmt->bind_param("is", $user_id, $week_start);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update existing notes
            $stmt = $conn->prepare("UPDATE weekly_notes SET monday=?, tuesday=?, wednesday=?, thursday=?, friday=?, saturday=?, sunday=? WHERE user_id=? AND week_start_date=?");
            $stmt->bind_param("sssssssis", $monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $sunday, $user_id, $week_start);
            $stmt->execute();
        } else {
            // Insert new notes
            $stmt = $conn->prepare("INSERT INTO weekly_notes (user_id, week_start_date, monday, tuesday, wednesday, thursday, friday, saturday, sunday) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssss", $user_id, $week_start, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $sunday);
            $stmt->execute();
        }

        echo json_encode(['status' => 'success']);
    }
}

$conn->close();
?>
