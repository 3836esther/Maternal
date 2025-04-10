<?php
// Handle registration logic
$success = false;
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $host = "localhost";
    $dbname = "maternal";
    $username = "root";
    $password = "";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $uname = $_POST['username'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $medical_history = $_POST['medical_history'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Check for existing email
        $stmt = $conn->prepare("SELECT * FROM Users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $errorMsg = "Email already registered!";
        } else {
            // Insert user
            $stmt = $conn->prepare("INSERT INTO Users (username, email, phone, medical_history, password) 
                                    VALUES (:username, :email, :phone, :medical_history, :password)");
            $stmt->bindParam(':username', $uname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':medical_history', $medical_history);
            $stmt->bindParam(':password', $pass);

            if ($stmt->execute()) {
                $success = true;
            } else {
                $errorMsg = "Registration failed!";
            }
        }
    } catch (PDOException $e) {
        $errorMsg = "Connection failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Maternal Health System</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .register-container {
      background-color: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 360px;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    input, textarea, button {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 10px;
      box-sizing: border-box;
    }

    button {
      background-color: #4CAF50;
      color: white;
      font-weight: bold;
      cursor: pointer;
      border: none;
    }

    button:hover {
      background-color: #45a049;
    }

    #message {
      text-align: center;
      margin-top: 10px;
      color: red;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h2>User Registration</h2>
    <form method="POST" action="">
      <input type="text" name="username" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="text" name="phone" placeholder="Phone Number" required>
      <textarea name="medical_history" placeholder="Medical History" rows="4" required></textarea>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Register</button>
    </form>
    <div id="message"><?= $errorMsg ?></div>
  </div>

  <?php if ($success): ?>
    <script>
      alert("Registration successful!");
      window.location.href = "login.php"; // Change this if your login page has a different name
    </script>
  <?php endif; ?>
</body>
</html>
