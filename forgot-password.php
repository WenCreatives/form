<?php
require 'config.php';
require 'mailer.php';

// Connect to DB
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $message = 'Invalid email address.';
    } else {
        // Check if user exists
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id);
            $stmt->fetch();
            // Generate token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            // Insert token
            $stmt2 = $conn->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)');
            $stmt2->bind_param('iss', $user_id, $token, $expires);
            $stmt2->execute();
            // Send email
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset-password.php?token=$token";
            $subject = 'Password Reset Request';
            $body = "Click <a href='$reset_link'>here</a> to reset your password. This link expires in 1 hour.";
            if (sendMail($email, $subject, $body)) {
                $message = 'A reset link has been sent to your email.';
            } else {
                $message = 'Failed to send email. Please try again.';
            }
        } else {
            $message = 'If that email exists, a reset link has been sent.';
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
    <title>Forgot Password</title>
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
        input[type="email"] {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1.2rem;
            border: 1px solid #bdbdbd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border 0.2s;
        }
        input[type="email"]:focus {
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
        .message {
            margin-bottom: 1rem;
            color: #d32f2f;
            font-weight: 500;
        }
        .back-link {
            display: block;
            margin-top: 1.2rem;
            color: #1976d2;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #0d47a1;
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
        <h2>Forgot Password</h2>
        <?php if ($message) echo '<div class="message">' . htmlspecialchars($message) . '</div>'; ?>
        <form action="forgot-password.php" method="post">
            <label for="email">Enter your email address</label>
            <input type="email" id="email" name="email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <a class="back-link" href="login.php">Back to Login</a>
    </div>
</body>
</html> 