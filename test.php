<?php
// Assuming you have started the session and have user's data stored in session variables after login
session_start();

// Example data fetched from session or database
$user_name = $_SESSION['user_name'] ?? 'Guest'; // Default to 'Guest' if not set
$profile_photo = $_SESSION['profile_photo'] ?? 'default-avatar.png'; // Default avatar if no profile picture is set

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .profile {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.info {
    flex-grow: 1;
}

.profile-photo {
    width: 50px;
    height: 50px;
    overflow: hidden;
    border-radius: 50%;
    border: 2px solid var(--color-primary);
}

.profile-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
    </style>
</head>
<body>
<div class="profile">
    <div class="info">
        <p>Hey, <b><?php echo htmlspecialchars($user_name); ?></b></p>
        <small class="text-muted">Admin</small> <!-- Adjust role dynamically if needed -->
    </div>
    <div class="profile-photo">
        <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="User Photo" id="user-photo">
    </div>
</div>

<div class="settings-form">
    <form action="upload_profile_picture.php" method="post" enctype="multipart/form-data" id="profile-form">
        <!-- Profile Picture Upload -->
        <div class="input-group">
            <label for="profile-picture">Upload Profile Picture</label>
            <input type="file" id="profile-picture" name="profile-picture" accept="image/*">
            <!-- Preview of the uploaded image -->
            <div class="image-preview" id="image-preview">
                <img src="" alt="Profile Picture Preview" id="profile-picture-preview">
                <span class="image-preview-text">No image selected</span>
            </div>
        </div>

        <!-- Other input fields -->
        <div class="input-group">
            <label for="username">Change Username</label>
            <input type="text" id="username" name="username" placeholder="Enter new username">
        </div>

        <div class="input-group">
            <label for="email">Change Email</label>
            <input type="email" id="email" name="email" placeholder="Enter new email">
        </div>

        <div class="input-group">
            <label for="password">Change Password</label>
            <input type="password" id="password" name="password" placeholder="Enter new password">
        </div>

        <button type="submit" class="primary">Save Changes</button>
    </form>
</div>
</body>
<script>
    /* Reuse the existing styles for form and image preview */
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
</script>
</html>


