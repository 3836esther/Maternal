<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}

$userEmail = $_SESSION['user_email'];

// Fetch user info from database
$conn = new mysqli("localhost", "root", "", "maternal_health_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT username, phone, medical_history FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

$conn->close();

// Pass data to JavaScript
echo "<script>
    const userProfile = " . json_encode($userData) . ";
</script>";
?>

<!-- Load the static HTML dashboard -->
<?php include("dashboard.html"); ?>
