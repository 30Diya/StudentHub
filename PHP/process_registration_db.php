<?php

require "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

/* Get form data */
$full_name = trim($_POST["full_name"] ?? "");
$student_id = trim($_POST["student_id"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";
$course = trim($_POST["course"] ?? "");

/* Backend validation */

if (
    empty($full_name) ||
    empty($student_id) ||
    empty($email) ||
    empty($mobile) ||
    empty($password) ||
    empty($confirm_password) ||
    empty($course)
) {
    die("Error: All fields are required.");
}

/* Email validation */
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Error: Please enter a valid email address.");
}

/* Mobile validation */
if (!preg_match("/^[0-9]{10}$/", $mobile)) {
    die("Error: Mobile number must contain exactly 10 digits.");
}

/* Password confirmation */
if ($password !== $confirm_password) {
    die("Error: Passwords do not match.");
}

/* Password length */
if (strlen($password) < 6) {
    die("Error: Password must be at least 6 characters long.");
}

/* Check duplicate email */
$check_sql = "SELECT id FROM users WHERE email = ?";

$check_stmt = $conn->prepare($check_sql);

if (!$check_stmt) {
    die("Error preparing duplicate email check: " . $conn->error);
}

$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    $check_stmt->close();
    die("Error: This email is already registered.");
}

$check_stmt->close();

/* Hash password */
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

/* Insert student */
$sql = "INSERT INTO users
        (full_name, email, mobile, password)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error preparing registration: " . $conn->error);
}

$stmt->bind_param(
    "ssss",
    $full_name,
    $email,
    $mobile,
    $hashed_password
);

if ($stmt->execute()) {

    echo "<h2>Registration Successful!</h2>";
    echo "<p>Welcome, " . htmlspecialchars($full_name) . ".</p>";
    echo "<p>Your account has been successfully created.</p>";
    echo "<a href='../pages/register_db.html'>Register Another Student</a>";

} else {

    echo "<h2>Registration Failed</h2>";
    echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
}

$stmt->close();
$conn->close();

?>