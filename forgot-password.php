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
</head>
<body>
    <h2>Forgot Password</h2>
    <?php if ($message) echo '<p>' . htmlspecialchars($message) . '</p>'; ?>
    <form action="forgot-password.php" method="post">
        <label for="email">Enter your email address:</label>
        <input type="email" id="email" name="email" required><br><br>
        <button type="submit">Send Reset Link</button>
    </form>
</body>
</html> 