<?php
// Database connection
$host = 'localhost';
$dbname = 'reminder_app';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the reminder ID from the POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $reminderId = $data['id'];

    // Delete the reminder from the database
    $stmt = $conn->prepare("DELETE FROM reminders WHERE id = :id");
    $stmt->bindParam(':id', $reminderId);
    $stmt->execute();

    echo json_encode(["status" => "success"]);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
