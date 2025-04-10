<?php
$host = "localhost";
$dbname = "maternal";
$username = "root";
$password = "";

$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle status update (accept/reject)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['appointment_id'];
    $newStatus = $_POST['status'];

    // Update status in the database to "accepted" or "rejected"
    $stmt = $conn->prepare("UPDATE appointments SET status = :status WHERE id = :id");
    $stmt->bindParam(':status', $newStatus);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Send notification to the patient based on the status (optional, if email integration is needed)
    if ($newStatus == 'accepted') {
        // Code for sending acceptance email to the patient
    } elseif ($newStatus == 'rejected') {
        // Code for sending rejection email to the patient
    }
}

// Fetch all appointments
$stmt = $conn->query("SELECT * FROM appointments ORDER BY appointment_date DESC");
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Appointments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            color: #4CAF50;
            margin: 20px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #e8e8e8;
            font-weight: bold;
        }
        form {
            display: inline;
        }
        select, button {
            padding: 6px 12px;
            font-size: 14px;
            cursor: pointer;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .status-label {
            font-weight: bold;
        }
        em {
            color: #888;
        }
    </style>
</head>
<body>

<h2>Admin - Accept or Reject Appointments</h2>

<table>
    <tr>
        <th>Full Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Service</th>
        <th>Doctor</th>
        <th>Checkup Type</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php foreach ($appointments as $appt): ?>
    <tr>
        <td><?= htmlspecialchars($appt['full_name']) ?></td>
        <td><?= htmlspecialchars($appt['email']) ?></td>
        <td><?= htmlspecialchars($appt['phone']) ?></td>
        <td><?= htmlspecialchars($appt['service']) ?></td>
        <td><?= htmlspecialchars($appt['doctor']) ?></td>
        <td><?= htmlspecialchars($appt['checkup_type']) ?></td>
        <td><?= htmlspecialchars($appt['appointment_date']) ?></td>
        <td>
            <span class="status-label"><?= ucfirst($appt['status']) ?></span>
        </td>
        <td>
            <?php if ($appt['status'] != 'accepted' && $appt['status'] != 'rejected'): ?>
            <form method="POST">
                <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                <select name="status">
                    <option value="accepted">Accept</option>
                    <option value="rejected">Reject</option>
                </select>
                <button type="submit">Update</button>
            </form>
            <?php else: ?>
                <em><?= ucfirst($appt['status']) ?></em>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
