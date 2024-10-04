<?php
include 'auth.php';

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

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home │ Remindify</title>
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp"
    rel="stylesheet">
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
                <a href="#" class="active">
                    <span class="material-icons-sharp">grid_view</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="AllReminders.php">
                    <span class="material-icons-sharp">today</span>
                    <h3>All Reminders</h3>
                    <!-- <span class="today-count">3</span> -->
                </a>
                <a href="shared.php">
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
                <!-- <a href="completed.php">
                    <span class="material-icons-sharp">assignment_turned_in</span>
                    <h3>Completed</h3>
                </a> -->
                <a href="recyclebin.php">
                    <span class="material-icons-sharp">delete</span>
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
                <form action="logout.php" method="post" >
                <button type="submit" class="red">
                <span class="material-icons-sharp">logout</span>
                <h3>Log Out</h3>
                </button>
                </form>

                </div>
            </div>
        </aside>

        <!-- ----------------end of aside ;))))) ---------------- -->

<main>
            <h1>Dashboard</h1>

            <div class="date">
                <span id="current-date"></span>
            </div>
            <section class="reminder-section"> 
        <h2 class="slogan">Reminders for all, anytime, anywhere!</h2>
        <p class="subline">Never forget a thing with Our app by your side!</p>

        <div class="create-group">
            <form id="create-group-form">
                <input type="text" id="group-name" placeholder="Enter group name" required>
                <button type="submit" id="create-group-btn" class="group-btn">Create Group</button>
            </form>
        </div>

        <div class="join-group">
    <input type="text" id="join-code" placeholder="Enter a code or link" class="input-code">
    <button id="join-btn" class="join-btn">Join</button>
</div>

    </section>

<!-- reminders to show -->          
                <div class="recent-activity-wrapper">
                
        <div class="recent-activity">
            <h2>Recent Reminders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody id="activity-list">
                    <!-- Rows will be added dynamically here -->
                </tbody>
            </table>
            <button id="toggle-button">Show All</button>
           </div>
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
                <div class="add-reminders">
                    <h2>Add Reminders</h2>
                <div class="reminders">
                    <form id="reminder-form" action="add_reminder.php" method="POST">
                        <div class="form-group">
                            <label for="description"><b>Description</b></label>
                            <textarea id="description" name="description" placeholder="Enter reminder description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="date"><b>Date</b></label>
                            <input type="date" id="date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="time"><b>Time</b></label>
                            <input type="time" id="time" name="time" required>
                        </div>
                        <div class="form-group">
                            <label for="location"><b>Location</b></label>
                            <input type="text" id="location" name="location" placeholder="Enter location (optional)">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="submit-button primary"><b>Add Reminder</b></button>
                        </div>
                    </form>
                </div>               
            </div>

                
                 <div class="reminder-of-the-day">
                    <h2>Reminder of the Day</h2>
                    <p id="daily-quote"></p>
                </div>
        </div>
    </div>

    <!-- Popup Notification -->
<div id="popup" class="popup">
    <p id="popup-message"></p>
</div>

<!-- JavaScript -->


    <script src="dashboard.js"></script>
    <script src="notifications.js"></script>
    <script src="scripts.js"></script>
    <script src="quotes.js"></script>
    <script>
        document.getElementById('reminder-form').addEventListener('submit', (event) => {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    const description = document.getElementById('description').value;
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    const location = document.getElementById('location').value;

    // Show popup notification
    const popup = document.getElementById('popup');
    const popupMessage = document.getElementById('popup-message');
    popupMessage.textContent = `Reminder Added: ${description} on ${date} at ${time} in ${location}`;
    popup.classList.add('show');

    // Hide the popup after 6 seconds
    setTimeout(() => {
        popup.classList.remove('show');
    }, 6000);

    // Submit form data using fetch
    fetch('add_reminder.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            description: description,
            date: date,
            time: time,
            location: location,
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Reminder added successfully');
            // Optionally, update the UI or handle success here
        } else {
            console.error('Error adding reminder:', data.message);
            // Optionally, show an error message
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Optionally, show an error message
    });
});

    document.getElementById('toggle-button').addEventListener('click', function() {
        const button = this;
        const tableBody = document.getElementById('activity-list');

        if (button.textContent === 'Show All') {
            // Fetch and show all reminders
            fetch('fetch_reminders.php')
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = ''; // Clear existing rows

                    data.forEach((reminder, index) => {
                        const row = document.createElement('tr');
                        const className = (index % 2 === 0) ? 'list_odd_item' : 'list_even_item';
                        row.className = className;
                        row.innerHTML = `
                            <td>${reminder.description}</td>
                            <td>${reminder.reminder_date}</td>
                            <td>${reminder.reminder_time}</td>
                            <td>${reminder.location}</td>
                        `;
                        tableBody.appendChild(row);
                    });

                    button.textContent = 'Show Less'; // Change button text to "Show Less"
                })
                .catch(error => {
                    console.error('Error fetching reminders:', error);
                });
        } else {
            // Hide the content
            tableBody.innerHTML = ''; // Clear the table body content
            button.textContent = 'Show All'; // Change button text back to "Show All"
        }
    });





    // Optionally, you can also submit the form data to the server here if needed
    // For example, using fetch() or XMLHttpRequest to send 
    
    document.getElementById('create-group-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    const groupName = document.getElementById('group-name').value;

    if (groupName.trim() === '') {
        alert('Group name cannot be empty');
        return;
    }

    // Send group name to PHP using AJAX
    fetch('create_group.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ group_name: groupName })
    })
    .then(response => response.text())
    .then(data => {
        if (data === 'success') {
            alert('Group created successfully!');
            location.reload(); // Refresh page after successful group creation
        } else {
            alert('Error creating group: ' + data);
        }
    })
    .catch(error => console.error('Error:', error));
});

// join-btn
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('join-btn').addEventListener('click', function() {
        const joinCode = document.getElementById('join-code').value;

        if (joinCode.trim() === '') {
            alert('Join code cannot be empty');
            return;
        }

        // Send join code to PHP using AJAX
        fetch('join.php?code=' + encodeURIComponent(joinCode), {
            method: 'GET'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                location.href = 'group_page.php?id=' + data.group_id; // Redirect to the group page
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
});



</script>
</body>
</html>