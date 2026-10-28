
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/admin_check.php';
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/audit_log.php';

logAdminAction(
    $pdo,
    'Viewed Dashboard',
    'Admin opened the dashboard'
);

$totalUsers = (int) $pdo->query(
    "SELECT COUNT(*) FROM users"
)->fetchColumn();

$totalStudents = (int) $pdo->query(
    "SELECT COUNT(*) FROM students"
)->fetchColumn();

$totalEvents = (int) $pdo->query(
    "SELECT COUNT(*) FROM events"
)->fetchColumn();

$totalRegistrations = (int) $pdo->query(
    "SELECT COUNT(*) FROM registrations"
)->fetchColumn();

$latestRegistrations = $pdo->query(
   "SELECT
        r.registration_id,
        s.name AS student_name,
        e.title AS event_name,
        r.registered_at
     FROM registrations r
     LEFT JOIN students s ON r.student_id = s.student_id
     LEFT JOIN events e ON r.event_id = e.event_id
     ORDER BY r.registration_id DESC
     LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

$recentLogs = $pdo->query(
    "SELECT audit_logs.*, users.full_name
     FROM audit_logs
     LEFT JOIN users ON audit_logs.user_id = users.id
     ORDER BY audit_logs.created_at DESC
     LIMIT 10"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudentHub Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">StudentHub Admin</span>
    <span class="text-white">
        Welcome, <?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?>
    </span>
</nav>

<main class="container py-4">
    <h2 class="mb-4">Dashboard Overview</h2>

    <div class="row g-3 mb-4">
        <?php
        $cards = [
            'Total Users' => $totalUsers,
            'Total Students' => $totalStudents,
            'Total Events' => $totalEvents,
            'Total Registrations' => $totalRegistrations
        ];
        foreach ($cards as $label => $value):
        ?>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">
                        <?= htmlspecialchars($label) ?>
                    </p>
                    <h2><?= $value ?></h2>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <section class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            Latest Registrations
        </div>
        <div class="card-body table-responsive">
            <?php if ($latestRegistrations): ?>
            <table class="table table-striped table-bordered">
                <thead><tr>
                    <?php foreach (array_keys($latestRegistrations[0]) as $column): ?>
                    <th><?= htmlspecialchars($column) ?></th>
                    <?php endforeach; ?>
                </tr></thead>
                <tbody>
                <?php foreach ($latestRegistrations as $registration): ?>
                    <tr>
                    <?php foreach ($registration as $value): ?>
                        <td><?= htmlspecialchars((string)($value ?? '')) ?></td>
                    <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No registrations found.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            Recent Admin Activity
        </div>
        <div class="card-body table-responsive">
            <?php if ($recentLogs): ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Date and Time</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recentLogs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['full_name'] ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($log['action'] ?? '') ?></td>
                        <td><?= htmlspecialchars($log['details'] ?? '') ?></td>
                        <td><?= htmlspecialchars($log['created_at'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No audit logs found yet.</p>
            <?php endif; ?>
        </div>
    </section>
</main>
</body>
</html>
