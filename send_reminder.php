<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "maternal");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $patient_name = $conn->real_escape_string($_POST['patient_name']);
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO reminders (patient_name, message) VALUES ('$patient_name', '$message')";

    if ($conn->query($sql) === TRUE) {
        $status = "Reminder sent successfully!";
    } else {
        $status = "Error: " . $conn->error;
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
        }

        h2 {
            color: #333;
        }

        form {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background: #28a745;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .status {
            margin-top: 15px;
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>

<h2>Send Medication Reminder to Patient</h2>

<form method="POST" action="">
    <label>Patient Name:</label>
    <input type="text" name="patient_name" required>

    <label>Reminder Message:</label>
    <textarea name="message" rows="4" required></textarea>

    <button type="submit">Send Reminder</button>
</form>

<?php if (!empty($status)) : ?>
    <div class="status"><?php echo $status; ?></div>
<?php endif; ?>

</body>
</html>
