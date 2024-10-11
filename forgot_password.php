<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

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

$message = ''; // Initialize message variable
$email = ''; // Initialize email variable
$password_reset_step = 0; // Step tracker

// Step 1: Check if email is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email is set
    if (isset($_POST['email']) && empty($_POST['new_password'])) {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

        // Step 2: Check if email exists in the database
        if ($stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?")) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($user_id, $existing_password);
            $stmt->fetch();

            if ($stmt->num_rows > 0) {
                // Email found; set password reset step
                $password_reset_step = 1; // Show password reset fields
            } else {
                $message = "No account found with that email.";
            }

            $stmt->close();
        } else {
            $message = "Error preparing SQL statement: " . $conn->error;
        }
    }

    // Step 3: Handle new password submission
    if (isset($_POST['new_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Step 4: Check if new passwords match
        if ($new_password === $confirm_password) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Debug: Log existing and new password for verification
            error_log("Existing Password: " . $existing_password);
            error_log("New Password: " . $new_password);
            
            // Step 5: Update the password in the database
            if ($update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?")) {
                $update_stmt->bind_param("ss", $hashed_password, $email);
                if ($update_stmt->execute()) {
                    // Step 6: Check if any rows were updated
                    if ($update_stmt->affected_rows > 0) {
                        $message = "Password has been updated successfully. You can now log in.";
                        $password_reset_step = 0; // Reset the step to initial
                        $email = ''; // Clear email variable after update
                    } else {
                        // Check if the new password is the same as the existing password
                        if (!empty($existing_password) && password_verify($new_password, $existing_password)) {
                            $message = "The new password cannot be the same as the old password.";
                        } else {
                            $message = "No changes were made. Please try again.";
                        }
                    }
                } else {
                    $message = "Error updating password: " . htmlspecialchars($update_stmt->error);
                }
                $update_stmt->close();
            } else {
                $message = "Error preparing update statement: " . $conn->error;
            }
        } else {
            $message = "Passwords do not match.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="styles.css">
    <script>
        function validatePasswords() {
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const messageBox = document.getElementById('message-box');

            if (newPassword !== confirmPassword) {
                messageBox.innerText = "Passwords do not match.";
                messageBox.style.color = "red";
                return false;
            }
            messageBox.innerText = ""; // Clear message
            return true;
        }

        function togglePasswordFields() {
            const emailField = document.getElementById('email-field');
            const passwordFields = document.getElementById('password-fields');
            const isEmailEntered = emailField.value.trim() !== '';

            if (isEmailEntered) {
                passwordFields.style.display = 'block'; // Show password fields
            } else {
                passwordFields.style.display = 'none'; // Hide password fields
            }
        }
    </script>
</head>
<body>
    <div class="form-container">
        <div class="form-box">
            <h1>Forgot Password</h1>
            <form id="forgot-password-form" method="POST">
                <?php if ($password_reset_step === 0): ?>
                    <div class="input-group" id="email-field">
                        <span class="material-icons-sharp">email</span>
                        <input type="email" name="email" placeholder="Enter your email" required oninput="togglePasswordFields()">
                    </div>
                    <button type="submit">Submit</button>
                <?php elseif ($password_reset_step === 1): ?>
                    <div class="input-group" id="password-fields" style="display: block;">
                        <span class="material-icons-sharp">lock</span>
                        <input type="password" id="new-password" name="new_password" placeholder="New Password" required>
                    </div>
                    <div class="input-group" id="password-fields" style="display: block;">
                        <span class="material-icons-sharp">lock</span>
                        <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" required oninput="validatePasswords()">
                    </div>
                    <div class="message" id="message-box"></div>
                    <button type="submit" onclick="return validatePasswords()">Save Changes</button>
                <?php endif; ?>
            </form>
            <?php if (!empty($message)): ?>
                <div class="message">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <p>Remember your password? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
