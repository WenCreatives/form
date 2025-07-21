<?php
require 'config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$message = '';
$show_form = false;
$token = isset($_GET['token']) ? $_GET['token'] : '';

if ($token) {
    $stmt = $conn->prepare('SELECT user_id, expires_at FROM password_resets WHERE token = ?');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $expires_at);
        $stmt->fetch();
        if (strtotime($expires_at) > time()) {
            $show_form = true;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $password = $_POST['password'] ?? '';
                $confirm = $_POST['confirm_password'] ?? '';
                if (strlen($password) < 6) {
                    $message = 'Password must be at least 6 characters.';
                } elseif ($password !== $confirm) {
                    $message = 'Passwords do not match.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt2 = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
                    $stmt2->bind_param('si', $hash, $user_id);
                    $stmt2->execute();
                    // Delete token
                    $stmt3 = $conn->prepare('DELETE FROM password_resets WHERE token = ?');
                    $stmt3->bind_param('s', $token);
                    $stmt3->execute();
                    $message = 'Password has been reset. You can now <a href="login.php">login</a>.';
                    $show_form = false;
                }
            }
        } else {
            $message = 'Token expired. Please request a new password reset.';
        }
    } else {
        $message = 'Invalid token.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <?php if ($message) echo '<p>' . $message . '</p>'; ?>
    <?php if ($show_form): ?>
    <form action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <button type="submit">Reset Password</button>
    </form>
    <?php endif; ?>
</body>
</html> 