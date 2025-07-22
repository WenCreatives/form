<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'config.php';
require 'mailer.php';

// Connect to DB
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$message = '';
$message_class = '';
$form_sent = false; // Added this variable to track if the form has been sent

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $message = 'Invalid email address.';
        $message_class = 'error';
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
            $body = '<div style="font-family:Roboto,Arial,sans-serif;background:#f4f8fb;padding:32px 0;">
                <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(25,118,210,0.08);padding:32px 24px;text-align:center;">
                    <h2 style="color:#1976d2;margin-bottom:16px;">OwenCreatives Password Reset</h2>
                    <p style="color:#333;font-size:1.1rem;margin-bottom:24px;">We received a request to reset your password. Click the button below to set a new password. This link will expire in 1 hour.</p>
                    <a href="' . $reset_link . '" style="display:inline-block;padding:12px 32px;background:#1976d2;color:#fff;border-radius:8px;font-weight:700;text-decoration:none;font-size:1.1rem;margin-bottom:24px;">Reset Password</a>
                    <p style="color:#888;font-size:0.95rem;margin-top:32px;">If you did not request a password reset, you can safely ignore this email.<br><br>— OwenCreatives Team</p>
                </div>
            </div>';
            $result = sendMail($email, $subject, $body);
            if ($result === true) {
                $message = 'A reset link has been sent to your email. Please check your mailbox.';
                $message_class = 'success';
                $form_sent = true;
            } else {
                $message = 'Failed to send reset email: ' . htmlspecialchars($result);
                $message_class = 'error';
                $form_sent = false;
            }
        } else {
            $message = 'If that email exists, a reset link has been sent.';
            $message_class = 'info';
            $form_sent = true; // If email doesn't exist, we still show the form
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
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }
        .message.success {
            color: #388e3c;
            background: #e8f5e9;
        }
        .message.error {
            color: #d32f2f;
            background: #ffebee;
        }
        .message.info {
            color: #1976d2;
            background: #e3f2fd;
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
        <?php if ($message) echo '<div class="message ' . $message_class . '">' . htmlspecialchars($message) . '</div>'; ?>
        <?php if (!isset($form_sent) || !$form_sent): ?>
        <form action="forgot-password.php" method="post">
            <label for="email">Enter your email address</label>
            <input type="email" id="email" name="email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <?php endif; ?>
        <a class="back-link" href="login.php">Back to Login</a>
    </div>
</body>
</html> 