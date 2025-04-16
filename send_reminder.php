<?php
session_start();

// TEMPORARY: For testing purposes. Remove once login system is working.
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Use a valid user_id from your `users` table
}

$status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "maternal");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $patient_name = $conn->real_escape_string($_POST['patient_name']);
    $message = $conn->real_escape_string($_POST['message']);
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : null;
    $user_id = $_SESSION['user_id'];

    // Insert the reminder into the database
    $sql = "INSERT INTO reminders (patient_name, message, is_read, user_id, email)
            VALUES ('$patient_name', '$message', 0, '$user_id', " . ($email ? "'$email'" : "''") . ")";

    if ($conn->query($sql) === TRUE) {
        $status = "✅ Reminder sent successfully!";
    } else {
        $status = "❌ Error: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Medication Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            background-color: #f5f9f7;
        }

        h2 {
            color: #2f855a;
        }

        form {
            background: #ffffff;
            padding: 25px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        button {
            background: #2f855a;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #276749;
        }

        .status {
            margin-top: 20px;
            font-weight: bold;
            color: #2f855a;
        }

        .status.error {
            color: #e53e3e;
        }
    </style>
</head>
<body>

<h2>Send Medication Reminder</h2>

<form method="POST" action="">
    <label>Patient Name:</label>
    <input type="text" name="patient_name" required>

    <label>Reminder Message:</label>
    <textarea name="message" rows="4" required></textarea>

    <label>Email (Optional):</label>
    <input type="email" name="email" placeholder="Patient's Email">

    <button type="submit">Send Reminder</button>
</form>

<?php if (!empty($status)) : ?>
    <div class="status <?php echo strpos($status, 'Error') !== false ? 'error' : ''; ?>">
        <?php echo $status; ?>
    </div>
<?php endif; ?>

</body>
</html>
