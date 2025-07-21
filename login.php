<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) {
        $message = 'Please enter both email and password.';
    } else {
        $stmt = $conn->prepare('SELECT password FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($hash);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                $message = 'Login successful!'; // Replace with session logic as needed
            } else {
                $message = 'Invalid email or password.';
            }
        } else {
            $message = 'Invalid email or password.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
            min-height: 100vh;
            margin: 0;
            font-family: 'Roboto', Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 350px;
            width: 100%;
            text-align: center;
        }
        h2 {
            color: #0d47a1;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }
        label {
            display: block;
            text-align: left;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1.2rem;
            border: 1px solid #bdbdbd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border 0.2s;
        }
        input[type="email"]:focus, input[type="password"]:focus {
            border: 1.5px solid #1976d2;
            outline: none;
        }
        button {
            width: 100%;
            padding: 0.75rem;
            background: #1976d2;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background: #0d47a1;
        }
        .forgot-link {
            display: block;
            margin-top: 1.2rem;
            color: #d32f2f;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .forgot-link:hover {
            color: #b71c1c;
        }
        .message {
            margin-bottom: 1rem;
            color: #d32f2f;
            font-weight: 500;
        }
        @media (max-width: 500px) {
            .card {
                padding: 1.5rem 1rem 1rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Login</h2>
        <?php if ($message) echo '<div class="message">' . htmlspecialchars($message) . '</div>'; ?>
        <form action="login.php" method="post">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <a class="forgot-link" href="forgot-password.php">Forgot Password?</a>
    </div>
</body>
</html> 