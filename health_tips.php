<?php
session_start(); // Start the session to manage user authentication

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit(); // Stop the script from running
}

$message = "";
$advice = "";
$history = isset($_SESSION['advice_history']) ? $_SESSION['advice_history'] : [];

// Check if form is submitted to provide health advice
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validate and sanitize user input
        $weeks_pregnant = filter_var($_POST['weeks_pregnant'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 40]]);
        
        if ($weeks_pregnant === false) {
            throw new Exception("Invalid input. Please enter a number between 1 and 40.");
        }

        // Function to provide health advice based on pregnancy weeks
        function getHealthAdvice($weeks) {
            if ($weeks <= 4) {
                return "First Trimester (1-4 weeks): Rest is crucial during this time. Avoid any strenuous activities. Make sure you stay hydrated and eat well.";
            } elseif ($weeks <= 9) {
                return "First Trimester (5-9 weeks): It's important to take your prenatal vitamins. Be mindful of morning sickness.";
            } elseif ($weeks == 12) {
                return "End of First Trimester (12 weeks): Your baby’s organs are forming. Ensure a balanced diet with folic acid, calcium, and iron.";
            } elseif ($weeks == 20) {
                return "Second Trimester (20 weeks): The baby is growing rapidly. Exercise regularly and monitor your weight gain.";
            } elseif ($weeks > 20 && $weeks <= 26) {
                return "Second Trimester (21-26 weeks): Continue healthy eating habits and begin considering maternity clothes.";
            } elseif ($weeks > 26) {
                return "Third Trimester: Prepare for labor. Stay active, but avoid overexertion. Practice breathing techniques and relax.";
            } else {
                return "Please enter a valid pregnancy duration.";
            }
        }
        
        // Get the health advice based on the pregnancy weeks
        $advice = getHealthAdvice($weeks_pregnant);

        // Save advice history in session
        $history[] = ["weeks" => $weeks_pregnant, "advice" => $advice];
        $_SESSION['advice_history'] = $history;

    } catch (Exception $e) {
        $message = "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Tips | Maternal Health System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <a href="home.php">home</a>
        <a href="services.php">Services</a>
        <a href="health_tips.php">Health Tips</a>
        <!-- Logout Button -->
        <a href="logout.php" class="logout-button">Logout</a>
        <a href="notification.php">🔔</a>
    </nav>
    
    <div class="container">
        <h2>Health Tips for Pregnant Women</h2>
        <?= $message ?>

        <!-- Display the logged-in user's email with a greeting -->
        <p>Welcome, <?= htmlspecialchars($_SESSION['email']) ?>!</p>

        <form method="POST">
            <label for="weeks_pregnant">Enter Weeks of Pregnancy:</label>
            <input type="number" name="weeks_pregnant" id="weeks_pregnant" min="1" max="40" required>
            <button type="submit">Get Health Advice</button>
        </form>

        <?php
        if ($advice != "") {
            echo "<div class='advice'>";
            echo "<h3>Your Health Advice:</h3>";
            echo "<p>$advice</p>";
            echo "</div>";
        }
        ?>

        <!-- Display advice history -->
        <?php if (!empty($history)) : ?>
            <div class="history">
                <h3>Your Health Advice History:</h3>
                <ul>
                    <?php foreach ($history as $entry) : ?>
                        <li>Week <?= $entry['weeks'] ?>: <?= $entry['advice'] ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
