<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congratulations!</title>
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
        .congrats {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            padding: 3rem 2.5rem;
            max-width: 500px;
            width: 100%;
            text-align: center;
            animation: pop 0.7s cubic-bezier(.68,-0.55,.27,1.55);
        }
        @keyframes pop {
            0% { transform: scale(0.7); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .congrats h1 {
            color: #1976d2;
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 1.2rem;
        }
        .congrats .emoji {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }
        .congrats p {
            color: #333;
            font-size: 1.3rem;
            margin-bottom: 2rem;
            font-weight: 500;
        }
        .logout-link {
            display: inline-block;
            margin-top: 1.2rem;
            color: #d32f2f;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: color 0.2s;
        }
        .logout-link:hover {
            color: #b71c1c;
        }
    </style>
</head>
<body>
    <div class="congrats">
        <span class="emoji">🎉🥳</span>
        <h1>Congratulations!</h1>
        <p>Happy Coding!<br>You have successfully learned how to reset your password.</p>
        <a class="logout-link" href="login.php">Logout</a>
    </div>
</body>
</html> 