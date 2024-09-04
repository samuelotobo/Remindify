<?php
// Set session timeout duration (e.g., 30 minutes)
ini_set('session.gc_maxlifetime', 1800);
session_start();

// Check for inactivity
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > 1800) {
    // Session has timed out
    session_unset();     // Clear session variables
    session_destroy();   // Destroy session
    header("Location: login.php"); // Redirect to login page
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time
?>
