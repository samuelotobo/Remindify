<?php 
include 'auth.php';
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle profile picture upload
    if (isset($_FILES['profile-picture']) && $_FILES['profile-picture']['error'] == 0) {
        $image = $_FILES['profile-picture']['tmp_name'];
        $imgContent = addslashes(file_get_contents($image)); // Read the image content

        // Other form data
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Update user data in the database
        $sql = "UPDATE users SET username='$username', email='$email', password='$password', profile_image='$imgContent' WHERE id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Profile updated successfully.');</script>";
        } else {
            echo "<script>alert('Error: " . $sql . "\\n" . $conn->error . "');</script>";
        }
        } else {
            echo "<script>alert('Please upload a valid image.');</script>";
        }
        
}

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
    <title>Account Settings │ Remindify</title>
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
                <a href="dashboard.php" >
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
                    <a href="#" class="active">
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

        <!-- ----------------end of aside ;))))) ---------------- -->

        <main>
            <h1>Account Settings</h1>
            <div class="date">
                <span id="current-date"></span>
            </div>

            <div class="settings-form">
                <form action="#" method="post" enctype="multipart/form-data">
                    <!-- Profile Picture Upload -->
                    <div class="input-group">
                        <label for="profile-picture">Upload Profile Picture</label>
                        <input type="file" id="profile-picture" name="profile-picture" accept="image/*" onchange="previewImage(event)">
                        
                        <!-- Preview of the uploaded image -->
                        <div class="image-preview" id="image-preview">
                            <?php if (!empty($imageData)): ?>
                                <img src="<?php echo $imageSrc; ?>" alt="Profile Picture Preview" id="profile-picture-preview">
                            <?php else: ?>
                                <img src="" alt="Profile Picture Preview" id="profile-picture-preview">
                                <span class="image-preview-text">No image selected</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Username -->
                    <div class="input-group">
                        <label for="username">Change Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" placeholder="Enter new username">
                    </div>

                    <!-- Email -->
                    <div class="input-group">
                        <label for="email">Change Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" placeholder="Enter new email">
                    </div>

                    <!-- Password -->
                    <div class="input-group">
                        <label for="password">Change Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter new password">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="primary">Save Changes</button>
                </form>
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
        </div>
    </div>
    <script src="dashboard.js"></script>
    <script>
        // account settings
document.getElementById('profile-picture').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('image-preview');
    const previewImage = document.getElementById('profile-picture-preview');
    const previewText = previewContainer.querySelector('.image-preview-text');

    if (file) {
        const reader = new FileReader();

        previewText.style.display = "none"; // Hide the preview text
        previewImage.style.display = "block"; // Show the image

        reader.addEventListener('load', function() {
            previewImage.setAttribute('src', reader.result);
        });

        reader.readAsDataURL(file);
    } else {
        previewText.style.display = null;
        previewImage.style.display = null;
        previewImage.setAttribute('src', '');
    }
});


   // Preview image before upload
   function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('profile-picture-preview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
            
            // Hide the "No image selected" text
            document.querySelector('.image-preview-text').style.display = 'none';
        }
    </script>
</body>
</html>