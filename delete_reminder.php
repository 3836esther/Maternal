<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=Please log in to delete a reminder");
    exit();
}

$userId = $_SESSION['user_id'];
$reminderId = $_POST['id'] ?? null; // Get reminder ID from form submission

// If no reminder ID is provided, redirect
if (!$reminderId) {
    header("Location: notification.php?message=No reminder selected for deletion");
    exit();
}

// Database connection
try {
    $conn = new PDO("mysql:host=localhost;dbname=maternal", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Delete the reminder from the database
    $stmt = $conn->prepare("DELETE FROM reminders WHERE id = :reminder_id AND user_id = :user_id");
    $stmt->bindParam(':reminder_id', $reminderId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    // Redirect back to the notifications page with a success message
    header("Location: notification.php?message=Reminder deleted successfully");
    exit();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
