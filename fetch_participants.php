<?php
// Include your DB connection
include 'db_connect.php';

$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;

if ($group_id > 0) {
    $sql = "SELECT p.group_id, u.first_name, u.last_name, u.email
            FROM participants p
            JOIN users u ON p.user_id = u.id
            WHERE p.group_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $participants = [];
    while ($row = $result->fetch_assoc()) {
        $participants[] = $row;
    }

    echo json_encode($participants);
} else {
    echo json_encode([]);
}

?>
