<?php
// Include database connection
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from the form
    $group_id = $_POST['group_id'];
    $group_name = $_POST['group_name'];

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE shared_groups SET group_name = ? WHERE group_id = ?");
    $stmt->bind_param("si", $group_name, $group_id);

    if ($stmt->execute()) {
        echo "Group updated successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
