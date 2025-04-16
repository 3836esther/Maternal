<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=Please log in to update your preferences");
    exit();
}

$userId = $_SESSION['user_id'];
$preferences = $_POST['pref_types'] ?? []; // Get selected preference types

// Database connection
try {
    $conn = new PDO("mysql:host=localhost;dbname=maternal", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Delete old preferences for the user (if any)
    $deleteOldPrefs = $conn->prepare("DELETE FROM user_preferences WHERE user_id = :user_id");
    $deleteOldPrefs->bindParam(':user_id', $userId);
    $deleteOldPrefs->execute();

    // Insert new preferences for the user
    foreach ($preferences as $type) {
        $stmt = $conn->prepare("INSERT INTO user_preferences (user_id, reminder_type) VALUES (:user_id, :type)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':type', $type);
        $stmt->execute();
    }

    // Redirect back to the notifications page with a success message
    header("Location: notification.php?message=Preferences updated successfully");
    exit();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
