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
    <!-- Your existing HTML code -->

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
                // Hide all reminders
                tableBody.innerHTML = '';
                button.textContent = 'Show All';
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
                        $('#editReminderId').val(reminder.id);
                        $('#editDescription').val(reminder.description);
                        $('#editDate').val(reminder.reminder_date);
                        $('#editTime').val(reminder.reminder_time);
                        $('#editLocation').val(reminder.location);

                        $('#editModal').fadeIn();
                    }
                });
        });

        // Handle delete button click
        $(document).on('click', '.delete-reminder', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            if (confirm('Are you sure you want to delete this reminder?')) {
                fetch('fetch_reminders.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'action': 'delete',
                        'id': id,
                    }),
                })
                .then(response => response.json())
                .then(result => {
                    if (result.message) {
                        alert(result.message);
                        location.reload();
                    } else {
                        alert(result.error);
                    }
                });
            }
        });

        // Handle edit form submission
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('action', 'edit');
            fetch('fetch_reminders.php', {
                method: 'POST',
                body: new URLSearchParams(new FormData(this)),
            })
            .then(response => response.json())
            .then(result => {
                alert(result.message || result.error);
                if (result.message) {
                    document.getElementById('editModal').style.display = 'none';
                    location.reload();
                }
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
