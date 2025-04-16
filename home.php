<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #4CAF50;
            overflow: hidden;
            font-size: 18px;
        }
        .navbar a {
            float: left;
            display: block;
            color: white;
            padding: 14px 20px;
            text-align: center;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .container {
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .welcome-message {
            text-align: center;
            font-size: 24px;
            color: #333;
        }
        .feature-box {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
        }
        .feature-box div {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            width: 30%;
            text-align: center;
        }
        .feature-box div:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }
        .feature-box div h3 {
            color: #4CAF50;
        }
        .message {
            text-align: center;
            color: #4CAF50;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar">
    <a href="home.php">Home</a>
    <a href="About.html">About</a>
    <a href="Contact.html">Contact</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="services.php">Services</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="profile.php">Profile</a>
        <a href="notifications.php">Notifications</a>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</div>

<!-- Main Content -->
<div class="container">
    <h1>Welcome to Maternal Health System</h1>
    <p class="welcome-message">
    <?php if (isset($_SESSION['username'])): ?>
        Hello, <?= htmlspecialchars($_SESSION['username']); ?>! Welcome back.
    <?php else: ?>
        Welcome to the Maternal Health System. Please log in to access personalized features.
    <?php endif; ?>
</p>
 <!-- Feature Boxes -->
    <div class="feature-box">
        <div>
            <h3>Manage Profile</h3>
            <p>Update your profile information and view your medical history.</p>
            <a href="profile.php">Go to Profile</a>
        </div>
        <div>
            <h3>Book Appointments</h3>
            <p>Schedule your health checkups and manage appointments.</p>
            <a href="appointments.php">Book Appointment</a>
        </div>
        <div>
            <h3>Access Services</h3>
            <p>View and book different health services tailored to you.</p>
            <a href="services.php">Go to Services</a>
        </div>
    </div>
</div>

</body>
</html>
