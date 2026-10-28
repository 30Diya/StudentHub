
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Please open the login page and submit the form.');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    exit('ERROR: Email and password are required.');
}

try {
    $stmt = $pdo->prepare(
        'SELECT id, full_name, email, password, role
         FROM users
         WHERE email = ?
         LIMIT 1'
    );

    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        exit('ERROR: Email or password is incorrect. Check your account in phpMyAdmin.');
    }

    $role = strtolower(trim($user['role'] ?? ''));

    if (!in_array($role, ['admin', 'student'], true)) {
        exit('ERROR: This account has an unrecognized role.');
    }

    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $role;
    $_SESSION['last_activity'] = time();

    session_write_close();

    header('Location:home.php');
    exit;

} catch (PDOException $e) {
    error_log('Login database error: ' . $e->getMessage());
    http_response_code(500);
    exit('Database error. Check your PHP error log.');
}
?>
