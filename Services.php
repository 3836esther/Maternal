<?php
session_start(); // Start the session to manage user authentication

$message = "";

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit(); // Stop the script from running
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Database connection
        $conn = new PDO("mysql:host=localhost;dbname=maternal", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get data from the form
        $full_name = $_POST['full_name'];
        $email = $_SESSION['email']; // Use the session email instead of POST (since it's already logged in)
        $phone = $_POST['phone'];
        $service = $_POST['service'];
        $doctor = $_POST['doctor'];
        $checkup = $_POST['checkup_type'];
        $appointment_date = $_POST['appointment_date'];

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO appointments 
            (full_name, email, phone, service, doctor, checkup_type, appointment_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");

        // Execute the statement
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
        <a href="index.html">Home</a>
        <a href="about.html">About</a>
        <a href="services.php">Services</a>
        <a href="contact.html">Contact</a>
       
        
        <!-- Logout Button -->
        <a href="logout.php" class="logout-button">Logout</a>

        <a href="notification.php">🔔</a>
    </nav>
    

    <div class="container">
        <h2>Book an Appointment</h2>
        <?= $message ?>

        <!-- Display the logged-in user's email with a greeting -->
        <p>Welcome, <?= htmlspecialchars($_SESSION['email']) ?>!</p>

        <form method="POST">
            <input type="text" name="full_name" placeholder="Full Name" required>
            
            <!-- Email field is now read-only, as it should not be modified -->
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

        <!-- Link to view appointments -->
        <div class="view-appointments">
            <a href="view_appointments.php?email=<?= urlencode($_SESSION['email']) ?>" class="button">View My Appointments</a>
        </div>
    </div>
</body>
</html>
