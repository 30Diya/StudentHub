<?php
// Practical 7: PHP form processing with server-side validation and CSV storage

// Remove spaces and HTML tags from input
function clean($value) {
    return trim(strip_tags($value ?? ""));
}

// Stop Excel from running a value as a formula
function csvSafe($value) {
    if (preg_match('/^[=+\-@]/', $value)) {
        return "'" . $value;
    }
    return $value;
}

// Show a success or error page, then stop
function showPage($success, $messages) {
    $color  = $success ? "#2d7a4f" : "#b3261e";
    $bg     = $success ? "#e6f4ea" : "#fdecea";
    $title  = $success ? "Registration Successful" : "Please fix the following";
    $link   = $success ? "../Page/login.html" : "../Page/register.html";
    $text   = $success ? "Go to Login" : "Go back to the form";

    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>
    <title>StudentHub</title>
    <style>
        body{font-family:Arial,sans-serif;background:#f8f5eb;}
        .box{max-width:480px;margin:60px auto;background:$bg;border-left:6px solid $color;padding:25px;border-radius:8px;}
        h2{color:$color;margin-top:0;}
        a{display:inline-block;margin-top:15px;color:white;background:$color;padding:10px 18px;border-radius:6px;text-decoration:none;}
    </style></head><body><div class='box'><h2>$title</h2><ul>";
    foreach ($messages as $m) {
        echo "<li>" . htmlspecialchars($m) . "</li>";
    }
    echo "</ul><a href='$link'>$text</a></div></body></html>";
    exit;
}

// Only accept POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../Page/register.html");
    exit;
}

// Sanitize
$name      = clean($_POST["full_name"] ?? "");
$studentId = clean($_POST["student_id"] ?? "");
$email     = clean($_POST["email"] ?? "");
$mobile    = clean($_POST["mobile"] ?? "");
$course    = clean($_POST["course"] ?? "");
$password  = $_POST["password"] ?? "";
$confirm   = $_POST["confirm_password"] ?? "";

$allowedCourses = [
    "Information Technology",
    "Computer Science",
    "Software Engineering",
    "Business Management"
];

// Validate (collect every error)
$errors = [];

if (!preg_match('/^[A-Za-z ]{2,100}$/', $name)) {
    $errors[] = "Full name must be 2 to 100 letters (letters and spaces only).";
}
if (!preg_match('/^[A-Za-z0-9]{3,20}$/', $studentId)) {
    $errors[] = "Student ID must be 3 to 20 letters or numbers.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}
if (!preg_match('/^[0-9]{10}$/', $mobile)) {
    $errors[] = "Mobile number must be exactly 10 digits.";
}
if (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters.";
}
if ($password !== $confirm) {
    $errors[] = "Password and Confirm Password do not match.";
}
if (!in_array($course, $allowedCourses, true)) {
    $errors[] = "Please select a valid course.";
}
if (!isset($_POST["terms"])) {
    $errors[] = "You must agree to the Terms and Conditions.";
}

if (count($errors) > 0) {
    showPage(false, $errors);
}

// Prepare the CSV file
$folder = __DIR__ . "/../CSV";
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}
$file = $folder . "/registration.csv";

$fp = fopen($file, "a+");
if (!$fp) {
    showPage(false, ["The data file could not be opened. Close it if it is open in Excel."]);
}

flock($fp, LOCK_EX);

// Check for duplicate Student ID or email, and see if the file is empty
rewind($fp);
$isEmpty   = true;
$duplicate = false;
while (($row = fgetcsv($fp, 0, ",", '"', "")) !== false) {
    $isEmpty = false;
    if (isset($row[2], $row[3]) &&
        (strcasecmp($row[2], $studentId) === 0 || strcasecmp($row[3], $email) === 0)) {
        $duplicate = true;
        break;
    }
}

if ($duplicate) {
    flock($fp, LOCK_UN);
    fclose($fp);
    showPage(false, ["This Student ID or email is already registered."]);
}

// Write the header once, then the record
if ($isEmpty) {
    fputcsv($fp, ["Date","Full Name","Student ID","Email","Mobile","Password Hash","Course"], ",", '"', "");
}

fputcsv($fp, [
    date("Y-m-d H:i:s"),
    csvSafe($name),
    csvSafe($studentId),
    csvSafe($email),
    csvSafe($mobile),
    password_hash($password, PASSWORD_DEFAULT),
    csvSafe($course)
], ",", '"', "");

flock($fp, LOCK_UN);
fclose($fp);

showPage(true, ["Your account was created and saved successfully."]);