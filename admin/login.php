<?php
// Database connection and session helpers.
require_once '../config/database.php';
require_once '../config/session.php';

// Redirect if already logged in.
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

// Handle login submissions.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)$_POST['username']);
    $password = (string)$_POST['password'];

    // Fetch the user by username.
    $stmt = $conn->prepare('SELECT id, username, password, role FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Validate the password hash.
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header('Location: dashboard.php');
            exit();
        }

        $error = 'Invalid username or password';
    } else {
        $error = 'Invalid username or password';
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - DepEd Library</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');

        :root {
            --bg: #f3f6fb;
            --card: #ffffff;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --accent: #0f766e;
            --accent-strong: #0b5f59;
            --danger-bg: #fee2e2;
            --danger-ink: #991b1b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Manrope', system-ui, -apple-system, Segoe UI, sans-serif;
            background:
                radial-gradient(900px 420px at 18% 0%, #dbeafe 0%, transparent 62%),
                radial-gradient(760px 420px at 90% 0%, #ccfbf1 0%, transparent 58%),
                #f3f6fb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px 14px;
            color: #0f172a;
        }

        .login-container {
            background: #ffffff;
            padding: 22px 18px 18px;
            border-radius: 12px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
            width: 100%;
            max-width: 390px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 16px;
        }

        .login-header h1 {
            font-size: 22px;
            letter-spacing: -0.3px;
            margin-bottom: 2px;
        }

        .login-header p {
            color: #64748b;
            font-size: 12px;
        }

        .form-group {
            margin-bottom: 10px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 12px;
        }

        input[type='text'],
        input[type='password'] {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            font-size: 13px;
            color: #0f172a;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #fff;
        }

        input[type='text']:focus,
        input[type='password']:focus {
            outline: none;
            border-color: #0f766e;
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.14);
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 8px 10px;
            border-radius: 7px;
            margin-bottom: 10px;
            font-size: 12px;
            border: 1px solid rgba(220, 38, 38, 0.3);
        }

        .btn-login {
            width: 100%;
            margin-top: 2px;
            padding: 10px 12px;
            background: #0f766e;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
        }

        .btn-login:hover {
            background: #0b5f59;
            transform: translateY(-1px);
        }

        .btn-login:focus-visible {
            outline: none;
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.18);
        }

        .back-link {
            text-align: center;
            margin-top: 12px;
        }

        .back-link a {
            color: #0f4b6d;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 18px 14px 14px;
            }

            .login-header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Admin Login</h1>
            <p>DepEd Southern Leyte Division Library</p>
        </div>

        <?php if ($error): ?>
            <div class="error">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <div class="back-link">
            <a href="../index.php">&larr; Back to Library Log</a>
        </div>
    </div>
</body>
</html>
