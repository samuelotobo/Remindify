<?php
session_start(); // Start the session

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

$error = ''; // Initialize the error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Debugging: Check received values
    // echo "Email: " . htmlspecialchars($email) . "<br>";
    // echo "Password: " . htmlspecialchars($password) . "<br>";
    // Prepare and execute the SQL statement
    if ($stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?")) {
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login │ Remindify</title>
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <div class="form-box">
            <h1>Login</h1>
            <form action="" method="POST" id="login-form">
                <div class="input-group">
                    <span class="material-icons-sharp">email</span>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <span class="material-icons-sharp">lock</span>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit">Login</button>
                <p><a href="forgotpassword.html">Forgot Password?</a></p>
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </form>
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="./script.js"></script>
</body>
</html>
