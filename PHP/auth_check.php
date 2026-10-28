
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Page/login.html');
    exit;
}

if (
    !isset($_SESSION['role']) ||
    strtolower(trim($_SESSION['role'])) !== 'admin'
) {
    http_response_code(403);
    exit('Access denied. Only administrators can access this page.');
}
?>
