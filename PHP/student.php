
<?php
require_once __DIR__ . '/auth_check.php';

// Only students can access this dashboard.
if (strtolower($_SESSION['role'] ?? '') !== 'student') {
    http_response_code(403);
    exit('Access denied. Student login required.');
}

// Create a CSRF token for the logout form if missing.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$studentName = $_SESSION['full_name'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Student Dashboard</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f3e9;
            margin: 0;
            padding: 40px 15px;
        }

        .dashboard {
            max-width: 650px;
            margin: 40px auto;
            padding: 35px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
            text-align: center;
        }

        h1 {
            color: #163b3b;
        }

        p {
            font-size: 17px;
            line-height: 1.6;
        }

        button {
            background: #397b68;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #285b4d;
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <h1>Student Dashboard</h1>

        <p>
            Welcome,
            <?= htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') ?>!
        </p>

        <p>You are logged in as a student.</p>

        <form action="logout.php" method="POST">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(
                    $_SESSION['csrf_token'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>
