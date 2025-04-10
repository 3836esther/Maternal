<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_email'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "maternal_health_db");

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$email = $_SESSION['user_email'];

$stmt = $conn->prepare("SELECT username, email, phone, medical_history FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    echo json_encode(['status' => 'success', 'user' => $user]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}

$conn->close();
?>
 S