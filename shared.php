<?php include 'auth.php'; 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reminder_app";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
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

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Calendar │ Remindify</title>
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp"
    rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
<style>/* Basic Modal Styles */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
}

.plan {
  padding: 10px;
  background-color: var(--c-white);
  color: var(--c-del-rio);
  max-width: 400px;
  margin: 10% auto;
  border-radius: 16px;
  box-shadow: 0 30px 30px -25px rgba(65, 51, 183, 0.25);
}

.plan .inner {
  padding: 20px;
  padding-top: 40px;
  background-color: var(--c-fair-pink);
  border-radius: 12px;
  position: relative;
  overflow: hidden;
}

.plan .title {
  font-weight: 600;
  font-size: 1.5rem;
  color: var(--c-coffee);
  margin-bottom: 0.75rem;
}

.plan .info {
  color: var(--c-charcoal);
  margin-bottom: 1rem;
}

.plan .features {
  display: flex;
  flex-direction: column;
}

.plan .features li {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
}

.plan .features .icon {
  background-color: var(--c-java);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: var(--c-white);
  border-radius: 50%;
  width: 20px;
  height: 20px;
}

.plan button {
  font: inherit;
  background-color: var(--c-indigo);
  border-radius: 6px;
  color: var(--c-white);
  font-weight: 500;
  font-size: 1.125rem;
  width: 100%;
  border: 0;
  padding: 1em;
  margin-top: 1.25rem;
  cursor: pointer;
}

.plan button:hover,
.plan button:focus {
  background-color: var(--c-governor);
}
</style>
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
                <a href="dashboard.php" >
                    <span class="material-icons-sharp">grid_view</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="AllReminders.php">
                    <span class="material-icons-sharp">today</span>
                    <h3>All Reminders</h3>
                    <!-- <span class="today-count">3</span> -->
                </a>
                <a href="#" class="active">
                <span class="material-icons-sharp">schedule_send</span>
                    <h3>Shared Calendar</h3>
                </a>
                <a href="calendar.php">
                    <span class="material-icons-sharp">calendar_month</span>
                    <h3>Calendar</h3>
                </a>
                <a href="balance.php">
                    <span class="material-icons-sharp">savings</span>
                    <h3>My Wallet</h3>
                </a>
                <a href="completed.php">
                    <span class="material-icons-sharp">assignment_turned_in</span>
                    <h3>Completed</h3>
                </a>
                <a href="recyclebin.php">
                    <span class="material-icons-sharp" class="active">delete</span>
                    <h3>Recycle Bin</h3>
                </a>
                <!-- <a href="settings.html">
                    <span class="material-icons-sharp">settings</span>
                    <h3>Settings</h3>
                </a> -->
                <div class="bottom-buttons">
                    <a href="accountsettings.php">
                        <span class="material-icons-sharp">account_circle</span>
                        <h3>Account Settings</h3>
                    </a>
                    <form action="logout.php" method="post" style="display: inline;">
    <button type="submit" class="red">
        <span class="material-icons-sharp">logout</span>
        <h3>Log Out</h3>
    </button>
</form>

            </div>
        </aside>

        <!-- ----------------end of aside ;))))) ---------------- -->



        <main>


            <h1>Shared Calendar</h1>

            <div class="date">
                <span id="current-date"></span>
            </div>



            <div class="insights upcoming-event">
            <div class="middle">
                <div class="event-details">
                    <!-- Event Name and Details fetched from reminder pages -->
                    <h3 id="event-name"></h3>
                    <p id="event-details"></p>
                </div>
                <div class="view-participants">
                    <button id="view-participants-btn">View Participants</button>
                </div>
            </div>
            <small class="text-muted">Last 24 hours</small>
        </div>

        </main>

<!-- Modal Container -->
<div id="notification-modal" class="modal">
    <article class="plan card">
        <div class="inner">
            <!-- Notification Title and Message -->
            <h2 class="title" id="notification-title">New Event Notification</h2>
            <p class="info" id="notification-message">You have been invited to a group event.</p>

            <!-- Group Participants -->
            <ul class="features" id="participants-list">
                <li>
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <path fill="none" d="M0 0h24v24H0z" />
                            <path d="M10 15.172l9.192-9.193 1.415 1.414L10 18l-6.364-6.364 1.414-1.414z" fill="currentColor" />
                        </svg>
                    </span>
                    <span>Participant Name</span>
                </li>
                <!-- Add more participants dynamically -->
            </ul>

            <!-- Action Buttons -->
            <button id="view-details-btn" class="button">View Details</button>
            <button id="close-btn" class="button">Close</button>
        </div>
    </article>
</div>


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
                        <!-- Dynamically display first name -->
                        <p>Hey, <b><?php echo $firstName; ?></b></p>
                        <small class="text-muted">Admin</small> <!-- This role can be dynamic if needed -->
                    </div>
                    <div class="profile-photo">
                        <!-- Dynamically display profile picture -->
                        <img src="<?php echo $imageSrc; ?>" alt="Profile Photo">
                    </div>
                </div>
            </div>
                <!-- === end of top === -->
        </div>
    </div>
    <script src="./dashboard.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('notification-modal');
    const viewParticipantsBtn = document.getElementById('view-participants-btn');
    const closeBtn = document.getElementById('close-btn');

    // Open modal when button is clicked
    viewParticipantsBtn.addEventListener('click', () => {
        modal.style.display = 'block';
    });

    // Close modal when the close button is clicked
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Optionally, close modal when clicking outside of the modal content
    window.addEventListener('click', (event) => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
});
    </script>
</body>
</html>