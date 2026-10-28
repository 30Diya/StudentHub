
<?php
require_once __DIR__ . '/auth_check.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied. Admin login required.');
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Admin Dashboard</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f3ea;
            text-align: center;
            padding: 60px 20px;
        }

        .dashboard {
            max-width: 600px;
            margin: auto;
            padding: 35px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px #0002;
        }

        h1 {
            color: #16302b;
        }

        button {
            background: #3a7563;
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>

<body>

<div class="dashboard">
    <h1>Admin Dashboard</h1>

    <p>
        Welcome,
        <?= htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8') ?>!
    </p>

    <p>You are logged in as an administrator.</p>

    <form action="logout.php" method="POST">
        <input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <button type="submit">Logout</button>
    </form>
</div>

</body>
</html>
