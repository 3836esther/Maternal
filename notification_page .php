<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "maternal_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all reminders
$sql = "SELECT * FROM reminders ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reminders & Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f8f8;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        .reminder-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .reminder {
            border-bottom: 1px solid #ddd;
            padding: 15px;
        }

        .reminder:last-child {
            border-bottom: none;
        }

        .patient-name {
            font-weight: bold;
            color: #333;
        }

        .message {
            margin: 5px 0;
        }

        .timestamp {
            font-size: 12px;
            color: #777;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007BFF;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h2>Medication Reminders & Notifications</h2>

    <div class="reminder-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="reminder">
                    <div class="patient-name"><?php echo htmlspecialchars($row['patient_name']); ?></div>
                    <div class="message"><?php echo htmlspecialchars($row['message']); ?></div>
                    <div class="timestamp">Scheduled on <?php echo $row['created_at']; ?></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reminders have been scheduled yet.</p>
        <?php endif; ?>
    </div>

    <a class="back-link" href="home.php">← Back to Home</a>

</body>
</html>
