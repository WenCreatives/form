<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        .logout-link {
            display: block;
            margin-top: 1.2rem;
            color: #d32f2f;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .logout-link:hover {
            color: #b71c1c;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Welcome to Your Dashboard!</h2>
        <p>You have successfully reset your password and logged in.</p>
        <a class="logout-link" href="login.php">Logout</a>
    </div>
</body>
</html> 