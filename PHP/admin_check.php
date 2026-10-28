
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check whether the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../Page/login.html');
    exit;
}

// Allow only administrators
if (
    !isset($_SESSION['role']) ||
    strtolower(trim($_SESSION['role'])) !== 'admin'
) {
    http_response_code(403);
    exit('Access denied. Only administrators can access this page.');
}
?>
