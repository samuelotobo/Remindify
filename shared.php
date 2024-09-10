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



        <div class="container">
        <h1>Shared Groups</h1>
        <div class="groups-list">
            <?php
            // Fetch all groups
            $sql = "SELECT * FROM groups";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $group_id = htmlspecialchars($row['group_id']);
                    $group_name = htmlspecialchars($row['group_name']);
                    echo "<div class='group-item' data-group-id='$group_id'>
                    <section class='reminder'>     
                    <h2>$group_name</h2>
                    </section>
                    <footer> 
                    <div class='dropdown'>
                            <button class='add-reminder-btn'>Add Reminder</button>
                            <button class='view-participants-btn'>View Participants</button>
                            <button class='edit-group-btn'>Edit Group</button>
                            <button class='delete-group-btn'>Delete Group</button>
                          </div>
                          </footer>
                    </div>
                          ";
                    
                }
            } else {
                echo "<p>No groups available.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Modals -->
    <div id="modal-container">
        <!-- Add Reminder Modal -->
        <div id="add-reminder-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Add Reminder</h2>
                <form id="add-reminder-form">
                    <input type="hidden" id="add-group-id">
                    <label for="reminder-description">Description:</label>
                    <textarea id="reminder-description" required></textarea>
                    <label for="reminder-date">Date:</label>
                    <input type="date" id="reminder-date" required>
                    <label for="reminder-time">Time:</label>
                    <input type="time" id="reminder-time" required>
                    <button type="submit">Add Reminder</button>
                </form>
            </div>
        </div>

        <!-- View Participants Modal -->
        <div id="view-participants-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Participants</h2>
                <ul id="participants-list">
                    <!-- Participants will be dynamically loaded here -->
                </ul>
            </div>
        </div>

        <!-- Edit Group Modal -->
        <div id="edit-group-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Edit Group</h2>
                <form id="edit-group-form">
                    <input type="hidden" id="edit-group-id">
                    <label for="edit-group-name">Group Name:</label>
                    <input type="text" id="edit-group-name" required>
                    <button type="submit">Save Changes</button>
                </form>
            </div>
        </div>
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


        // Handling modals and form submissions
        document.addEventListener('DOMContentLoaded', () => {
            const modals = document.querySelectorAll('.modal');
            const closeModal = (modal) => modal.style.display = 'none';
            const openModal = (modal) => modal.style.display = 'block';

            // Handle add reminder button click
            document.querySelectorAll('.add-reminder-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const groupId = this.closest('.group-item').dataset.groupId;
                    document.getElementById('add-group-id').value = groupId;
                    openModal(document.getElementById('add-reminder-modal'));
                });
            });

            // Handle view participants button click
            document.querySelectorAll('.view-participants-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const groupId = this.closest('.group-item').dataset.groupId;
                    fetchParticipants(groupId);
                    openModal(document.getElementById('view-participants-modal'));
                });
            });

            // Handle edit group button click
            document.querySelectorAll('.edit-group-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const groupId = this.closest('.group-item').dataset.groupId;
                    document.getElementById('edit-group-id').value = groupId;
                    openModal(document.getElementById('edit-group-modal'));
                });
            });

            // Handle delete group button click
            document.querySelectorAll('.delete-group-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const groupId = this.closest('.group-item').dataset.groupId;
                    if (confirm('Are you sure you want to delete this group?')) {
                        deleteGroup(groupId);
                    }
                });
            });

            // Fetch participants
            function fetchParticipants(groupId) {
                fetch(`fetch_participants.php?group_id=${groupId}`)
                    .then(response => response.json())
                    .then(data => {
                        const participantsList = document.getElementById('participants-list');
                        participantsList.innerHTML = '';
                        data.forEach(participant => {
                            const li = document.createElement('li');
                            li.textContent = participant.username;
                            participantsList.appendChild(li);
                        });
                    })
                    .catch(error => console.error('Error fetching participants:', error));
            }

            // Add reminder
            document.getElementById('add-reminder-form').addEventListener('submit', function(event) {
                event.preventDefault();
                const groupId = document.getElementById('add-group-id').value;
                const description = document.getElementById('reminder-description').value;
                const date = document.getElementById('reminder-date').value;
                const time = document.getElementById('reminder-time').value;

                fetch('add_group_reminder.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        group_id: groupId,
                        description: description,
                        date: date,
                        time: time,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Reminder added successfully!');
                        closeModal(document.getElementById('add-reminder-modal'));
                        sendDesktopNotification(groupId, description); // Notify participants
                    } else {
                        alert('Error adding reminder: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            // Edit group
            document.getElementById('edit-group-form').addEventListener('submit', function(event) {
                event.preventDefault();
                const groupId = document.getElementById('edit-group-id').value;
                const groupName = document.getElementById('edit-group-name').value;

                fetch('edit_group.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        group_id: groupId,
                        group_name: groupName,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Group updated successfully!');
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert('Error updating group: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            // Delete group
            function deleteGroup(groupId) {
                fetch('delete_group.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        group_id: groupId,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Group deleted successfully!');
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert('Error deleting group: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }

            // Close modals
            document.querySelectorAll('.close').forEach(span => {
                span.addEventListener('click', () => closeModal(span.closest('.modal')));
            });

            // Desktop notification for group participants
            function sendDesktopNotification(groupId, description) {
                fetch('notify_participants.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        group_id: groupId,
                        description: description,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Error sending notifications: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });

        document.querySelectorAll('.view-participants-btn').forEach(button => {
    button.addEventListener('click', function () {
        const groupId = this.getAttribute('data-group-id');

        // Fetch participants via AJAX
        fetch('fetch_participants.php', {
            method: 'POST',
            body: new URLSearchParams({group_id: groupId})
        })
        .then(response => response.json())
        .then(data => {
            let participantList = '';
            data.forEach(participant => {
                participantList += `<li><span class="icon">✔</span><span>${participant}</span></li>`;
            });
            document.getElementById('participants-list').innerHTML = participantList;
            document.getElementById('notification-modal').style.display = 'block';
        });
    });
});


if (isset($_POST['delete_group_id'])) {
    $groupId = $_POST['delete_group_id'];
    $deleteQuery = "DELETE FROM groups WHERE id = '$groupId'";
    if ($conn->query($deleteQuery)) {
        echo "Group deleted successfully";
    } else {
        echo "Error deleting group: " . $conn->error;
    }
}

$participantEmails = []; // Fetch all participant emails for the group
foreach ($participantEmails as $email) {
    mail($email, "New Reminder", "A new reminder has been added to your group!", "From: noreply@remindify.com");
}

document.querySelectorAll('.add-reminder-btn').forEach(button => {
    button.addEventListener('click', function () {
        const groupId = this.getAttribute('data-group-id');
        document.getElementById('notification-modal').style.display = 'block';
        document.getElementById('notification-title').innerText = 'Add Reminder to Group ' + groupId;
    });
});

    </script>
</body>
</html>