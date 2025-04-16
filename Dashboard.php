<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=Please log in first.");
    exit();
}

// Use full_name if available, otherwise fallback to email or "User"
$displayName = $_SESSION['full_name'] ?? $_SESSION['email'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Maternal Health System</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f2f6f9;
      margin: 0;
      padding: 0;
    }

    nav {
      background-color: #0275d8;
      padding: 15px;
      text-align: center;
    }

    nav a {
      color: white;
      margin: 0 15px;
      text-decoration: none;
      font-weight: bold;
    }

    nav a:hover {
      text-decoration: underline;
    }

    .container {
      max-width: 900px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #333;
    }

    .welcome {
      text-align: center;
      margin-bottom: 30px;
      font-size: 18px;
    }

    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }

    .card {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      transition: 0.3s;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    .card:hover {
      background: #e2e6ea;
    }

    .card a {
      display: block;
      margin-top: 10px;
      text-decoration: none;
      color: #0275d8;
      font-weight: bold;
    }

    .card a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <nav>
    <a href="home.php">Home</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php">Profile</a>
    <a href="services.php">Services</a>
    <a href="notification.php">Notifications</a>
    <a href="logout.php">Logout</a>
  </nav>

  <div class="container">
    <h2>Welcome to Your Dashboard</h2>
    <p class="welcome">Hello, <?= htmlspecialchars($displayName); ?>! Explore your health journey and manage your profile, appointments, and more.</p>

    <div class="card-grid">
      <div class="card">
        <h3>Manage Profile</h3>
        <p>Update your personal and pregnancy details.</p>
        <a href="profile.php">Go to Profile</a>
      </div>

      <div class="card">
        <h3>Book Appointments</h3>
        <p>Schedule and view your checkups.</p>
        <a href="services.php">Go to Services</a>
      </div>

      <div class="card">
        <h3>Reminders</h3>
        <p>Check your medical and weekly pregnancy reminders.</p>
        <a href="notification.php">View Notifications</a>
      </div>

      <div class="card">
        <h3>Logout</h3>
        <p>Securely log out of your account.</p>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </div>

</body>
</html>
