<?php


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "remindify";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare and execute the SQL statement
    if ($stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?")) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if email exists
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Set session variables and redirect to dashboard
                $_SESSION['user_id'] = $id;
                $_SESSION['email'] = $email;
                header("Location: dashboard.php");
                exit();
            } else {
                // Incorrect password
                $error = "Invalid password.";
            }
        } else {
            // Email not found
            $error = "No account found with that email.";
        }

        $stmt->close();
    } else {
        // SQL prepare error
        $error = "Error preparing SQL statement: " . $conn->error;
    }

    $conn->close();
}
?>