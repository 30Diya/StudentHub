
<?php
require_once __DIR__ . '/db_connect.php';

$email = 'aadi@gmail.com';
$newPassword = 'Test@12345';

$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "UPDATE users SET password = ? WHERE email = ?"
);

$stmt->bind_param("ss", $hash, $email);
$stmt->execute();

if ($stmt->affected_rows === 1) {
    echo "Password updated successfully!";
} else {
    echo "No password changed. Check the email.";
}

$stmt->close();
$conn->close();
?>
