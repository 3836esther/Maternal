<?php
session_start();

$loginError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $dbname = "maternal";
    $username = "root";
    $password = "";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $email = $_POST['email'];
        $pass = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM Users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($pass, $user['password'])) {
                $_SESSION['email'] = $email;

                echo "<script>
                        alert('Login successful!');
                        window.location.href = 'Services.php';
                      </script>";
                exit();
            } else {
                $loginError = "Incorrect password. Please try again.";
            }
        } else {
            // If email not found, redirect to Register.php with alert
            echo "<script>
                    alert('Email not found. Please register first.');
                    window.location.href = 'Register.php';
                  </script>";
            exit();
        }
    } catch (PDOException $e) {
        $loginError = "Connection failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Maternal Health System</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
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

    input, button {
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
  <div class="login-container">
    <h2>User Login</h2>
    <form method="POST" action="">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <div id="message"><?= $loginError ?></div>
  </div>
</body>
</html>
