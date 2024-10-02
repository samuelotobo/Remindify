<?php
// Include the database connection
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $profileImage = $_FILES['profileimage'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Validate and handle the profile image
    if ($profileImage['error'] === UPLOAD_ERR_OK) {
        $imageTmpName = $profileImage['tmp_name'];
        $imageName = basename($profileImage['name']);
        $uploadDir = 'uploads/'; // Directory to save the image

        // Ensure the upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory with write permissions
        }

        // Move the uploaded file to the designated directory
        $imagePath = $uploadDir . $imageName;
        if (move_uploaded_file($imageTmpName, $imagePath)) {
            // Insert the user into the database
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, profile_image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $firstname, $lastname, $email, $hashedPassword, $imagePath);

            if ($stmt->execute()) {
                // Registration successful, store user ID in the session
                session_start();
                $_SESSION['user_id'] = $stmt->insert_id; // Store the newly created user ID in the session

                // Redirect to the dashboard or reminders page
                header("Location: dashboard.php"); // Change to your dashboard or reminders page
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error = "Failed to upload image.";
        }
    } else {
        $error = "Image upload error: " . $profileImage['error'];
    }

    // Close the database connection
    $conn->close();
}
?>
