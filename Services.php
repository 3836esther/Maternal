<?php
session_start();

// Set session timeout duration (in seconds)
$timeoutDuration = 600; // 10 minutes

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['login_reminder'] = "Please log in to access the services page.";
    header("Location: login.php");
    exit();
}

// Check for session timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeoutDuration) {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['login_reminder'] = "Session expired due to inactivity. Please log in again.";
    header("Location: login.php");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn = new PDO("mysql:host=localhost;dbname=maternal", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $full_name = $_POST['full_name'];
        $email = $_SESSION['email'];
        $phone = $_POST['phone'];
        $service = $_POST['service'];
        $doctor = $_POST['doctor'];
        $checkup = $_POST['checkup_type'];
        $appointment_date = $_POST['appointment_date'];

        $stmt = $conn->prepare("INSERT INTO appointments 
            (full_name, email, phone, service, doctor, checkup_type, appointment_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");

        if ($stmt->execute([$full_name, $email, $phone, $service, $doctor, $checkup, $appointment_date])) {
            echo "<script>alert('Appointment submitted successfully! You can check your status later.'); window.location.href='view_appointments.php?email=$email';</script>";
        } else {
            $message = "<script>alert('Failed to submit appointment.');</script>";
        }
    } catch (PDOException $e) {
        $message = "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Services | Maternal Health System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <a href="home.php">home</a>
        <a href="services.php">Services</a>
        <a href="health_tips.php">Health Tips</a>
        <a href="community_support.php">Community Support</a>
        <a href="notification.php">🔔</a>
        <a href="logout.php" class="logout-button">Logout</a>
    </nav>

    <div class="container">
        <h2>Book an Appointment</h2>
        <?= $message ?>
        <p>Welcome, <?= htmlspecialchars($_SESSION['email']) ?>!</p>

        <form method="POST">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" value="<?= $_SESSION['email'] ?>" readonly required>
            <input type="tel" name="phone" placeholder="Phone Number" required>

            <select name="service" required>
                <option value="" disabled selected>Select a Service</option>
                <option value="Antenatal Care">Antenatal Care</option>
                <option value="Postnatal Care">Postnatal Care</option>
                <option value="Family Planning">Family Planning</option>
                <option value="Ultrasound">Pregnancy Ultrasound</option>
                <option value="Nutrition">Nutrition Counseling</option>
            </select>

            <select name="doctor" required>
                <option value="" disabled selected>Select Doctor</option>
                <option value="Terry">Terry</option>
                <option value="John">John</option>
                <option value="Leah">Leah</option>
            </select>

            <select name="checkup_type" required>
                <option value="" disabled selected>Select Checkup Type</option>
                <option value="Routine Antenatal Checkup">Routine Antenatal Checkup</option>
                <option value="High-Risk Pregnancy Consultation">High-Risk Pregnancy Consultation</option>
                <option value="Postnatal Recovery Checkup">Postnatal Recovery Checkup</option>
                <option value="Newborn Checkup">Newborn Checkup</option>
            </select>

            <input type="date" name="appointment_date" required>
            <button type="submit">Book Now</button>
        </form>

        <div class="view-appointments">
            <a href="view_appointments.php?email=<?= urlencode($_SESSION['email']) ?>" class="button">View My Appointments</a>
        </div>
    </div>
</body>
</html>
