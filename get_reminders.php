<?php
// Database connection
$host = 'localhost';
$dbname = 'reminder_app';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch reminders that are due in the next few minutes
    $stmt = $conn->prepare("SELECT id, description, date, time FROM reminders");
    $stmt->execute();

    $reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($reminders);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
