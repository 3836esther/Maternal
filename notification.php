<?php
// Connect to database
$conn = new mysqli("localhost", "root", "", "maternal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch unread reminders
$sql = "SELECT * FROM reminders WHERE is_read = 0 ORDER BY created_at DESC";
$result = $conn->query($sql);
$reminders = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reminders[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medication Notifications</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4fdfb;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #2f855a;
            color: white;
            padding: 15px 25px;
            font-size: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #notification-icon {
            position: relative;
            font-size: 26px;
            cursor: pointer;
            background-color: #38a169;
            padding: 12px;
            border-radius: 50%;
            color: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        #notification-popup {
            display: none;
            position: absolute;
            top: 65px;
            right: 25px;
            background-color: white;
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            z-index: 999;
        }

        .reminder-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background 0.3s;
        }

        .reminder-item:hover {
            background-color: #f0fff4;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            position: relative;
        }

        .modal h3 {
            margin-top: 0;
            color: #2f855a;
        }

        .modal p {
            font-size: 16px;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            color: #aaa;
            cursor: pointer;
        }

        .close-btn:hover {
            color: #ff5722;
        }

        .no-reminders {
            padding: 15px;
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="header">
    Medication Reminders
    <div id="notification-icon" onclick="togglePopup()">🔔</div>
</div>

<!-- Notification Popup -->
<div id="notification-popup">
    <?php if (count($reminders) > 0): ?>
        <?php foreach ($reminders as $reminder): ?>
            <div class="reminder-item" onclick="openModal('<?php echo $reminder['id']; ?>', '<?php echo htmlspecialchars(addslashes($reminder['patient_name'])); ?>', '<?php echo htmlspecialchars(addslashes($reminder['message'])); ?>')">
                <strong><?php echo htmlspecialchars($reminder['patient_name']); ?></strong><br>
                <small><?php echo date("M d, Y g:i A", strtotime($reminder['created_at'])); ?></small>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-reminders">No new reminders</div>
    <?php endif; ?>
</div>

<!-- Modal for private view -->
<div class="modal" id="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">×</span>
        <h3 id="modal-patient-name"></h3>
        <p id="modal-message"></p>
    </div>
</div>

<script>
    function togglePopup() {
        const popup = document.getElementById('notification-popup');
        popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
    }

    function openModal(id, name, message) {
        document.getElementById('modal-patient-name').innerText = name;
        document.getElementById('modal-message').innerText = message;
        document.getElementById('modal').style.display = 'flex';
        togglePopup();

        // Send AJAX to mark as read
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "mark_as_read.php?id=" + id, true);
        xhr.send();
    }

    function closeModal() {
        document.getElementById('modal').style.display = 'none';
    }

    // Hide popup when clicking outside
    window.onclick = function(e) {
        const popup = document.getElementById('notification-popup');
        const icon = document.getElementById('notification-icon');
        const modal = document.getElementById('modal');
        if (!popup.contains(e.target) && e.target !== icon && e.target.parentNode !== icon) {
            popup.style.display = 'none';
        }

        if (e.target === modal) {
            closeModal();
        }
    }
</script>

</body>
</html>
