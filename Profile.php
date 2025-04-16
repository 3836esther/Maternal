<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=Please log in first.");
    exit();
}

$host = "localhost";
$user = "root";
$password = "";
$dbname = "maternal";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userData = [];
$userId = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Update profile
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = $_POST['full_name'];
    $age = $_POST['age'];
    $due_date = $_POST['due_date'];
    $medical_history = $_POST['medical_history'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, age = ?, due_date = ?, medical_history = ?, contact_number = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sissssi", $full_name, $age, $due_date, $medical_history, $contact_number, $address, $userId);

    if ($stmt->execute()) {
        $success = "Profile updated successfully.";
        // Refresh the data
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
    } else {
        $error = "Failed to update profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
            padding: 30px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        input, textarea {
            width: 100%;
            margin: 8px 0;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        label {
            font-weight: bold;
        }
        .btn {
            background: green;
            color: white;
            padding: 10px 20px;
            border: none;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 6px;
        }
        .btn:hover {
            background: darkgreen;
        }
        .message {
            text-align: center;
            color: green;
            font-weight: bold;
        }
        .error {
            text-align: center;
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Your Profile</h2>

        <?php if (isset($success)): ?>
            <p class="message"><?= htmlspecialchars($success) ?></p>
        <?php elseif (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($userData['full_name'] ?? '') ?>" required>

            <label>Age:</label>
            <input type="number" name="age" value="<?= htmlspecialchars($userData['age'] ?? '') ?>" required>

            <label>Due Date:</label>
            <input type="date" name="due_date" value="<?= htmlspecialchars($userData['due_date'] ?? '') ?>" required>

            <label>Medical History:</label>
            <textarea name="medical_history" rows="3"><?= htmlspecialchars($userData['medical_history'] ?? '') ?></textarea>

            <label>Contact Number:</label>
            <input type="text" name="contact_number" value="<?= htmlspecialchars($userData['contact_number'] ?? '') ?>" required>

            <label>Address:</label>
            <input type="text" name="address" value="<?= htmlspecialchars($userData['address'] ?? '') ?>" required>

            <button type="submit" class="btn">Update Profile</button>
        </form>
    </div>
</body>
</html>
