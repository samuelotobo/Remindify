<?php include 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register │ Remindify</title>
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <div class="form-box">
            <h1>Register</h1>
            <form action="register_handler.php" method="POST" id="register-form" enctype="multipart/form-data">
                <div class="input-group">
                    <span class="material-icons-sharp">person</span>
                    <input type="text" name="firstname" placeholder="First Name" required>
                </div>
                <div class="input-group">
                    <span class="material-icons-sharp">person</span>
                    <input type="text" name="lastname" placeholder="Last Name" required>
                </div>
                <div class="input-group">
                    <span class="material-icons-sharp">email</span>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <span class="material-icons-sharp">lock</span>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <span class="material-icons-sharp">image</span>
                    <input type="file" name="profileimage" accept="image/*" required>
                </div>
                <button type="submit">Register</button>
                <p>Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>
    <script src="./script.js"></script>
</body>
</html>
