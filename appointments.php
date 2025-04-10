<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null; // Ensure login logic stores session
if (!$user_id) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new PDO("mysql:host=localhost;dbname=maternal", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $date = $_POST["appointment_date"];
    $service = $_POST["service_type"];

    $stmt = $conn->prepare("INSERT INTO appointments (user_id, appointment_date, service_type) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $date, $service]);

    echo "<script>alert('Appointment booked successfully!');</script>";
}
?>

<form method="POST">
    <label>Appointment Date:</label>
    <input type="date" name="appointment_date" required><br>
    <label>Service Type:</label>
    <select name="service_type">
        <option>Check-up</option>
        <option>Lab Test</option>
        <option>Ultrasound</option>
    </select><br>
    <button type="submit">Book Appointment</button>
</form>
