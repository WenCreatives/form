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
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1.2rem;
            border: 1px solid #bdbdbd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border 0.2s;
        }
        input[type="password"]:focus {
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
        <h2>Reset Password</h2>
        <?php if ($message) echo '<div class="message">' . $message . '</div>'; ?>
        <?php if ($show_form): ?>
        <form action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" required>
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <button type="submit">Reset Password</button>
        </form>
        <?php endif; ?>
        <a class="back-link" href="login.php">Back to Login</a>
    </div>
</body>
</html> 