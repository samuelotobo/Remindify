<?php include 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remindify</title>
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <link rel="stylesheet" href="remindify.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="logo.png" alt="Logo" class="logo">
            <h1>Remindify</h1>
        </div>
        <div class="download-container">
            <button class="download-btn">Download Now</button>
        </div>
    </header>

    <main>
        <div class="welcome-message">
            <h2>Welcome to Remindify</h2>
            <p>Your ultimate task management and reminder solution. Keep track of your tasks and stay on top of your schedule effortlessly.</p>
        </div>
        <div class="action-buttons">
            <a href="login.php"><button class="primary-btn">Log In</button></a>
            <a href="register.php"><button class="danger-btn">Register</button></a>
        </div>
    </main>
    
    <script src="animations.js"></script>

</body>
</html>
