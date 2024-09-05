<?php
include 'db_connect.php';

$group_id = $_POST['group_id'];
$description = $_POST['description'];

$sql = "SELECT users.username, users.email FROM participants JOIN users ON participants.user_id = users.id WHERE participants.group_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $group_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $username = $row['username'];
    $email = $row['email'];

    // Send desktop notification (email, or push notification)
    // You can use any email service provider's API or a push notification service here
}

echo json_encode(['success' => true]);
?>
