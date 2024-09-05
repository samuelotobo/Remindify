<?php
include 'db_connect.php'; // Ensure you have a connection script

$group_id = $_GET['group_id'];

$sql = "SELECT users.username FROM participants JOIN users ON participants.user_id = users.id WHERE participants.group_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $group_id);
$stmt->execute();
$result = $stmt->get_result();

$participants = [];

while($row = $result->fetch_assoc()) {
    $participants[] = ['username' => $row['username']];
}

echo json_encode($participants);
?>
