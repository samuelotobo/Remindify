<?php
include 'auth.php';
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch user data for display
$sql = "SELECT first_name, username, email, profile_image FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $firstName = htmlspecialchars($row['first_name']);
    $username = htmlspecialchars($row['username']);
    $imageData = !empty($row['profile_image']) ? base64_encode($row['profile_image']) : null;
    $imageSrc = $imageData ? 'data:image/jpeg;base64,' . $imageData : 'default-profile.png';
} else {
    echo "No user found.";
    exit();
}

// Fetch all groups the user has joined or created
$groups = [];
$sql = "SELECT g.group_id, g.group_name, g.created_by, g.created_at, g.join_code
        FROM shared_groups g
        LEFT JOIN participants p ON g.group_id = p.group_id 
        WHERE g.user_id = ? OR p.user_id = ?
        GROUP BY g.group_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id); // Bind user_id twice to check both conditions
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $groups[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- (Your existing head content) -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Calendar │ Remindify</title>
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="shared.css">
</head>
<body>
    <div class="container">
        <aside>
            <!-- (Your existing sidebar content) -->
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
                </a>
                <a href="#" class="active">
                    <span class="material-icons-sharp">schedule_send</span>
                    <h3>Shared Calendar</h3>
                </a>
                <a href="calendar.php">
                    <span class="material-icons-sharp">calendar_month</span>
                    <h3>Calendar</h3>
                </a>
                <a href="recyclebin.php">
                    <span class="material-icons-sharp">delete</span>
                    <h3>Recycle Bin</h3>
                </a>
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
            </div>
        </aside>

        <!-- ----------------end of aside ---------------- -->
        <main>
            <h1>Shared Calendar</h1>

            <div class="date">
                <span id="current-date"></span>
            </div>
            <div class="wrapper">
                <div class="groups-container">
                    <div class="groups-list">
                        <?php foreach ($groups as $group): 
                            $group_id = htmlspecialchars($group['group_id']);
                            $group_name = htmlspecialchars($group['group_name']);
                            $join_code = htmlspecialchars($group['join_code']);
                        ?>
                            <div class='group-item' data-group-id='<?php echo $group_id; ?>'>
                                <header>
                                    <h1><?php echo $group_name; ?></h1>
                                </header>

                                <section class='reminder'>
                                    <h2>Upcoming Event</h2>
                                    <!-- Placeholder for upcoming event or reminders -->
                                    <div id="group-reminder-list-<?php echo $group_id; ?>"></div>
                                </section>

                                <footer>
                                    <div class='dropdown'>
                                        <button class='dropbtn'>⋮</button>
                                        <div class='dropdown-content'>
                                            <button class='add-reminder-btn' data-group-id='<?php echo $group_id; ?>'>Add Reminder</button>
                                            <button class='view-participants-btn' data-group-id='<?php echo $group_id; ?>'>View Participants</button>
                                            <button class='details-btn' data-group-id='<?php echo $group_id; ?>'>Reminder Details</button>
                                            <button class='edit-btn' data-group-id='<?php echo $group_id; ?>'>Edit</button>
                                            <button class='delete-btn' data-group-id='<?php echo $group_id; ?>'>Delete</button>
                                        </div>
                                    </div>
                                </footer>
                            </div>
                            
                            <!-- Modal for Editing Group -->
                            <div id='myModal-<?php echo $group_id; ?>' class='modal'>
                                <div class='plan'>
                                    <div class='inner'>
                                        <h2 class='title'>Edit Group</h2>
                                        <ul class='features'>
                                            <li><span class='icon'>✓</span>Change group name</li>
                                            <li><span class='icon'>✓</span>Manage reminders</li>
                                        </ul>
                                        <button>Save Changes</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($groups)): ?>
                            <p>No groups available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Modals -->
            <div id="modal-container">

                <!-- Add Reminder Modal -->
                <div id="add-reminder-modal" class="modal" style="display:none;">
                    <div class="modal-content">
                        <span class="close">&times;</span> <!-- Close Button -->
                        <h2>Add Reminder</h2>
                        
                        <!-- Add Reminder Form -->
                        <form id="add-reminder-form">
                            <div class="form-group">
                                <input type="hidden" id="add-group-id" name="group_id"> <!-- Hidden Group ID -->

                                <label for="reminder-title">Reminder Title</label>
                                <input type="text" id="reminder-title" name="reminder_title" placeholder="Enter title" required>
                            </div>

                            <div class="form-group">
                                <label for="reminder-date">Reminder Date</label>
                                <input type="date" id="reminder-date" name="reminder_date" required>
                            </div>

                            <div class="form-group">
                                <label for="reminder-time">Reminder Time</label>
                                <input type="time" id="reminder-time" name="reminder_time" required>
                            </div>

                            <div class="form-group">
                                <label for="reminder-description">Description</label>
                                <input type="text" id="reminder-description" name="reminder_description" placeholder="Enter description" required>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="submit-button">Add Reminder</button>
                        </form>
                    </div>
                </div>

                <!-- Reminder Details Modal -->
                <div id="details-modal" class="modal" style="display:none;">
                    <div class="modal-content">
                        <span class="close">&times;</span> <!-- Close Button -->
                        <h2>Reminder Details</h2>

                        <!-- Reminder Information -->
                        <div class="reminder-info">
                            <!-- Table to display reminders -->
                            <table id="reminders-table" border="1">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Reminders will be dynamically inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- View Participants Modal -->
                <div id="view-participants-modal" class="modal" style="display:none;">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>Participants</h2>
                        <ul id="participants-list">
                            <!-- Participants will be dynamically loaded here -->
                        </ul>
                    </div>
                </div>

                <!-- Edit Group Modal -->
                <div id="edit-group-modal" class="modal" style="display:none;">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>Edit Group</h2>
                        <form id="edit-group-form">
                            <input type="hidden" id="edit-group-id" name="group_id">
                            <label for="edit-group-name">Group Name:</label>
                            <input type="text" id="edit-group-name" name="group_name" required>
                            <button type="submit">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notification Modal -->
            <div id="notification-modal" class="modal" style="display:none;">
                <article class="plan card">
                    <div class="inner">
                        <!-- Notification Title and Message -->
                        <h2 class="title" id="notification-title">New Event Notification</h2>
                        <p class="info" id="notification-message">You have been invited to a group event.</p>

                        <!-- Group Participants -->
                        <ul class="features" id="participants-list-notification">
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
                    </div>
                </article>
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
        </div>
    </div>
    
    <script src="./dashboard.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
    // General function to open modals
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';

            // Close the modal if the user clicks outside the modal content
            window.onclick = (event) => {
                if (event.target === modal) {
                    closeModal(modal);
                }
            };
        }
    }

    // Function to close modals
    function closeModal(modal) {
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // General function for button click handlers
    function handleButtonClick(buttonSelector, modalId, callback) {
        document.querySelectorAll(buttonSelector).forEach((button) => {
            button.addEventListener('click', function () {
                if (callback) callback(this); // Execute the callback function if provided
                openModal(modalId);
            });
        });
    }

    // Function to fetch and display participants in the modal
    function fetchParticipants(groupId) {
        fetch(`fetch_participants.php?group_id=${groupId}`)
            .then(response => response.json())
            .then(data => {
                const participantsList = document.getElementById('participants-list');
                participantsList.innerHTML = '';

                if (data.error) {
                    participantsList.innerHTML = `<p>${data.error}</p>`;
                    return;
                }

                data.forEach(participant => {
                    const li = document.createElement('li');
                    li.textContent = `${participant.first_name} ${participant.last_name} - ${participant.email}`;
                    participantsList.appendChild(li);
                });
            })
            .catch(error => console.error('Error fetching participants:', error));
    }

    // Function to fetch and display reminder details in the modal
    function fetchReminderDetails(groupId) {
        console.log(`Fetching reminder details for group ID: ${groupId}`);
        fetch(`fetch_group_reminders.php?group_id=${groupId}`)
            .then(response => response.json())
            .then(reminders => {
                console.log('Fetched reminders:', reminders);
                const remindersTableBody = document.querySelector('#reminders-table tbody');
                remindersTableBody.innerHTML = ''; // Clear existing content

                if (reminders.error) {
                    remindersTableBody.innerHTML = `<tr><td colspan="4">${reminders.error}</td></tr>`;
                    return;
                }

                if (reminders.length === 0) {
                    remindersTableBody.innerHTML = "<tr><td colspan='4'>No reminders found for this group.</td></tr>";
                } else {
                    reminders.forEach(reminder => {
                        const reminderRow = document.createElement('tr');
                        reminderRow.innerHTML = `
                            <td>${reminder.title}</td>
                            <td>${reminder.reminder_date}</td>
                            <td>${reminder.reminder_time}</td>
                            <td>${reminder.description}</td>
                        `;
                        remindersTableBody.appendChild(reminderRow);
                    });
                }
            })
            .catch(error => console.error('Error fetching reminders:', error));
    }

    // Function to delete a group
    function deleteGroup(groupId) {
        fetch('delete_group.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ group_id: groupId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Group deleted successfully!');
                    location.reload(); // Refresh the page after successful deletion
                } else {
                    alert('Error deleting group: ' + data.message);
                }
            })
            .catch(error => console.error('Error deleting group:', error));
    }

    // Function to handle add reminder form submission
    function handleAddReminderFormSubmission() {
        document.getElementById('add-reminder-form').addEventListener('submit', function (e) {
            e.preventDefault();

            const groupId = document.getElementById('add-group-id').value;
            const title = document.getElementById('reminder-title').value;
            const reminderDate = document.getElementById('reminder-date').value;
            const reminderTime = document.getElementById('reminder-time').value;
            const description = document.getElementById('reminder-description').value;

            fetch('add_group_reminder.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    'group_id': groupId,
                    'reminder_title': title,
                    'reminder_date': reminderDate,
                    'reminder_time': reminderTime,
                    'reminder_description': description
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Reminder added successfully!');
                        location.reload(); // Refresh the page to see the updated reminders
                    } else {
                        console.error('Error adding reminder:', data.message);
                        alert('Error adding reminder: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    }

    // Function to handle edit group form submission
    function handleEditGroupFormSubmission() {
        document.getElementById('edit-group-form').addEventListener('submit', function (event) {
            event.preventDefault();

            const groupId = document.getElementById('edit-group-id').value;
            const groupName = document.getElementById('edit-group-name').value;

            const formData = new FormData();
            formData.append('group_id', groupId);
            formData.append('group_name', groupName);

            fetch('update_group.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Group updated successfully!');
                        location.reload(); // Optionally, refresh the page
                    } else {
                        alert('Error updating group: ' + data.message);
                    }
                })
                .catch(error => console.error('Error updating group:', error));
        });
    }

    // Close modals on 'x' click
    document.querySelectorAll('.close').forEach(button => {
        button.addEventListener('click', function () {
            closeModal(this.closest('.modal'));
        });
    });

    // Assign button handlers for various functionalities
    handleButtonClick('.add-reminder-btn', 'add-reminder-modal', function (button) {
        const groupId = button.dataset.groupId;
        document.getElementById('add-group-id').value = groupId;
    });

    handleButtonClick('.view-participants-btn', 'view-participants-modal', function (button) {
        const groupId = button.dataset.groupId;
        fetchParticipants(groupId);
    });

    handleButtonClick('.details-btn', 'details-modal', function (button) {
        const groupId = button.dataset.groupId;
        fetchReminderDetails(groupId);
    });

    handleButtonClick('.edit-btn', 'edit-group-modal', function (button) {
        const groupId = button.dataset.groupId;
        document.getElementById('edit-group-id').value = groupId;
    });

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const groupId = this.dataset.groupId;
            if (confirm('Are you sure you want to delete this group?')) {
                deleteGroup(groupId);
            }
        });
    });

    document.querySelector('.dropbtn').addEventListener('click', function() {
    const dropdownContent = document.querySelector('.dropdown-content');
    dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
});

// Optional: close the dropdown if clicked outside
window.addEventListener('click', function(event) {
    if (!event.target.matches('.dropbtn')) {
        const dropdowns = document.getElementsByClassName("dropdown-content");
        for (let i = 0; i < dropdowns.length; i++) {
            dropdowns[i].style.display = "none";
        }
    }
});


    // Initialize form submission handlers
    handleAddReminderFormSubmission();
    handleEditGroupFormSubmission();
});

    </script>
</body>
</html>
