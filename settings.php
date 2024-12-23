<?php include 'auth.php';
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Check if the user is logged in


// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch user data for display
$sql = "SELECT first_name, username, email, profile_image FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $firstName = htmlspecialchars($row['first_name']); // Fetch the first name
    $username = htmlspecialchars($row['username']);
    $imageData = !empty($row['profile_image']) ? base64_encode($row['profile_image']) : null;
    $imageSrc = $imageData ? 'data:image/jpeg;base64,' . $imageData : 'default-profile.png'; // Default profile image if none is set
} else {
    echo "No user found.";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings │ Remindify</title>
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <aside>
            <div class="top">
                <div class="logo">
                    <img src="logo.png" alt="Remindify Logo">
                    <h2><span class="primary">Remind</span><span class="danger">Ify</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span>
                </div>
            </div>

            <div class="sidebar">
                <a href="dashboard.php">
                    <span class="material-icons-sharp">grid_view</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="AllReminders.php">
                    <span class="material-icons-sharp">today</span>
                    <h3>All Reminders</h3>
                    <!-- <span class="today-count">3</span> -->
                </a>
                <a href="calendar.html">
                    <span class="material-icons-sharp">calendar_month</span>
                    <h3>Calendar</h3>
                </a>
                <a href="balance.html">
                    <span class="material-icons-sharp">savings</span>
                    <h3>My Wallet</h3>
                </a>
                <a href="completed.html">
                    <span class="material-icons-sharp">assignment_turned_in</span>
                    <h3>Completed</h3>
                </a>
                <a href="recyclebin.html">
                    <span class="material-icons-sharp" class="active">delete</span>
                    <h3>Recycle Bin</h3>
                </a>
                <a href="#" class="active">
                    <span class="material-icons-sharp">settings</span>
                    <h3>Settings</h3>
                </a>
                <a href="accountsettings.html">
                    <span class="material-icons-sharp">account_circle</span>
                    <h3>Account Settings</h3>
                </a>
            </div>
        </aside>

        <!-- ----------------end of aside ;))))) ---------------- -->

        <main>
            <h1>Settings</h1>

            <div class="date">
                <input type="date">
            </div>
        </main>
        <!-- ============================ END OF MAIN===================== -->
        <div class="right">
            <div class="top">
                <button id="menu-btn">
                    <span class="material-icons-sharp">menu</span>
                </button>
                <div class="theme-toggler">
                    <span class="material-icons-sharp active">light_mode</span>
                    <span class="material-icons-sharp">dark_mode</span>
                </div>
                <div class="profile">
                    <div class="info">
                        <p>Hey, <b>Sophie</b></p>
                        <small class="text-muted">Admin</small>
                    </div>
                    <div class="profile-photo">
                        <img src="tulip_3255497.png" alt="Admin Photo">
                    </div>
                </div>
            </div>
            <!-- === end of top === -->
        </div>
    </div>
    <script src="./index.js"></script>
</body>

</html>