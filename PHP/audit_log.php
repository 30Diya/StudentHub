
<?php
require_once __DIR__ . '/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function logAdminAction($pdo, $action, $details = '')
{
    if (empty($_SESSION['user_id']) ||
        ($_SESSION['role'] ?? '') !== 'admin') {
        return;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO audit_logs (user_id, action, details)
         VALUES (?, ?, ?)"
    );

    $stmt->execute([
        $_SESSION['user_id'],
        $action,
        $details
    ]);
}
?>
