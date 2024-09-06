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
    <title>All Reminders │ Remindify</title>
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Modal styles */
        .modal {
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            margin-top: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .dropdown-content a {
            display: block;
            padding: 8px 16px;
            text-decoration: none;
            color: black;
            cursor: pointer;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
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
                <a href="dashboard.php">
                    <span class="material-icons-sharp">grid_view</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="#" class="active">
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
                <a href="balance.php">
                    <span class="material-icons-sharp">savings</span>
                    <h3>My Wallet</h3>
                </a>
                <a href="completed.php">
                    <span class="material-icons-sharp">assignment_turned_in</span>
                    <h3>Completed</h3>
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

        <main>
            <h1>Today</h1>

            <div class="date">
                <span id="current-date"></span>
            </div>

            <div class="recent-activity-wrapper">
                <div class="recent-activity">
                    <table>
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="activity-list">
                            <!-- Rows will be added dynamically here -->
                        </tbody>
                    </table>
                    <button id="toggle-button">Show All</button>
                </div>
            </div>

            <!-- Edit Reminder Modal -->
            <div id="editModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Edit Reminder</h2>
                    <form id="editForm">
                        <input type="hidden" name="id" id="editReminderId">
                        <label for="description">Description:</label>
                        <input type="text" name="description" id="editDescription" required>
                        <label for="reminder_date">Date:</label>
                        <input type="date" name="reminder_date" id="editDate" required>
                        <label for="reminder_time">Time:</label>
                        <input type="time" name="reminder_time" id="editTime" required>
                        <label for="location">Location:</label>
                        <input type="text" name="location" id="editLocation" required>
                        <button type="submit">Save Changes</button>
                    </form>
                </div>
            </div>
        </main>

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
                        <p>Hey, <b><?php echo htmlspecialchars($firstName); ?></b></p>
                        <small class="text-muted">Admin</small>
                    </div>
                    <div class="profile-photo">
                        <img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="Profile Photo">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="scripts.js" defer></script>
    <script>
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
                                <td>
                                    <div class="dropdown">
                                        <button class="dropbtn">Actions</button>
                                        <div class="dropdown-content">
                                            <a href="#" class="edit-reminder" data-id="${reminder.id}">Edit</a>
                                            <a href="#" class="delete-reminder" data-id="${reminder.id}">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            `;
                            tableBody.appendChild(row);
                        });

                        button.textContent = 'Hide All';
                    });
            } else {
<<<<<<< HEAD
                // Hide all reminders
                tableBody.innerHTML = '';
                button.textContent = 'Show All';
=======
                $('#editReminderId').val(reminder.id);
                $('#editDescription').val(reminder.description);
                $('#editDate').val(reminder.reminder_date);
                $('#editTime').val(reminder.reminder_time);
                $('#editLocation').val(reminder.location);

                // Show the modal
                $('#editModal').fadeIn();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching reminder data:', error);
        }
    });
});

// Close the modal
$('.close').on('click', function() {
    $('#editModal').fadeOut();
});

// Handle form submission for editing
$('#editForm').on('submit', function(e) {
    e.preventDefault();

    $.ajax({
        url: 'update_reminder.php',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            alert('Reminder updated successfully!');
            $('#editModal').fadeOut();
            location.reload(); // Reload the page to reflect changes
        },
        error: function(xhr, status, error) {
            console.error('Error updating reminder:', error);
            alert('Failed to update reminder.');
        }
    });
});
$(document).on('click', '.delete-reminder', function(e) {
    e.preventDefault();
    const reminderId = $(this).data('id');
    const row = $(this).closest('tr');  // Get the closest <tr> element (the reminder row)

    if (confirm('Are you sure you want to delete this reminder?')) {
        $.ajax({
            url: 'delete_reminder.php',
            type: 'POST',  // Ensure we're using POST
            data: { id: reminderId },  // Send the reminder ID
            success: function(response) {
                if (response.trim() === 'Success') {
                    alert('Reminder deleted successfully!');
                    row.remove();  // Remove the row from the table
                } else {
                    alert('Failed to delete reminder.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error deleting reminder:', error);
                alert('Error deleting reminder.');
>>>>>>> cb895341b0971b817835ead69ccb384aa55fa87c
            }
        });

        // Handle edit button click
        $(document).on('click', '.edit-reminder', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            fetch(`fetch_single_reminder.php?id=${id}`)
                .then(response => response.json())
                .then(reminder => {
                    if (!reminder.error) {
                        document.getElementById('editReminderId').value = reminder.id;
                        document.getElementById('editDescription').value = reminder.description;
                        document.getElementById('editDate').value = reminder.reminder_date;
                        document.getElementById('editTime').value = reminder.reminder_time;
                        document.getElementById('editLocation').value = reminder.location;

                        document.getElementById('editModal').style.display = 'block';
                    }
                });
        });

        // Handle delete button click
        $(document).on('click', '.delete-reminder', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            if (confirm('Are you sure you want to delete this reminder?')) {
                fetch('delete_reminder.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'id': id,
                    }),
                })
                .then(response => response.text())
                .then(result => {
                    alert(result);
                    location.reload();
                });
            }
        });

        // Handle edit form submission
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            fetch('update_reminder.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.text())
            .then(result => {
                alert(result);
                document.getElementById('editModal').style.display = 'none';
                location.reload();
            });
        });

        // Close modal
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('editModal').style.display = 'none';
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('editModal')) {
                document.getElementById('editModal').style.display = 'none';
            }
        });
    </script>
</body>
</html>
