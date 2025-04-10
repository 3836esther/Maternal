<?php
$conn = new mysqli("localhost", "root", "", "maternal_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$patient_name = $_POST['patient_name'];
$message = $_POST['message'];

$sql = "INSERT INTO reminders (patient_name, message) VALUES ('$patient_name', '$message')";

if ($conn->query($sql) === TRUE) {
    header("Location: notification_page.php"); // Redirect here
    exit();
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
