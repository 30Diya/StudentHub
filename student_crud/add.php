<?php
require 'db.php';
$errors = []; $name = $email = $phone = $course = ''; $year = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name   = trim($_POST['name'] ?? '');
  $email  = trim($_POST['email'] ?? '');
  $phone  = trim($_POST['phone'] ?? '');
  $course = trim($_POST['course'] ?? '');
  $year   = (int)($_POST['year'] ?? 0);

  if ($name === '') $errors[] = "Name is required.";
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email.";
  if (!preg_match('/^[0-9]{10}$/', $phone)) $errors[] = "Phone must be 10 digits.";
  if (!in_array($course, $COURSES)) $errors[] = "Select a course.";
  if ($year < 1 || $year > 4) $errors[] = "Year must be 1 to 4.";

  if (!$errors) {
    $chk = $conn->prepare("SELECT id FROM students WHERE email = ?");
    $chk->bind_param("s", $email); $chk->execute(); $chk->store_result();
    if ($chk->num_rows > 0) $errors[] = "This email already exists.";
    $chk->close();
  }

  if (!$errors) {
    $stmt = $conn->prepare("INSERT INTO students (name,email,phone,course,year) VALUES (?,?,?,?,?)");
    $stmt->bind_param("ssssi", $name, $email, $phone, $course, $year);
    if ($stmt->execute()) {
      flash('success', 'Student added successfully.');
      header("Location: index.php");
      exit;
    }
    $errors[] = "Insert failed: " . $stmt->error;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Student</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
  <div class="logo">STUDENT<span>HUB</span></div>
  <a href="index.php">Student Management</a>
</nav>

<main class="container">
  <h2>Add Student</h2>
  <?php foreach ($errors as $e) echo "<div class='alert error'>" . htmlspecialchars($e) . "</div>"; ?>

  <form method="post" class="form">
    <label>Name</label>
    <input name="name" value="<?= htmlspecialchars($name) ?>">

    <label>Email</label>
    <input name="email" value="<?= htmlspecialchars($email) ?>">

    <label>Phone (10 digits)</label>
    <input name="phone" value="<?= htmlspecialchars($phone) ?>">

    <label>Course</label>
    <select name="course">
      <option value="">Select</option>
      <?php foreach ($COURSES as $c): ?>
        <option <?= $c === $course ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Year (1 to 4)</label>
    <input type="number" name="year" min="1" max="4" value="<?= $year ?: '' ?>">

    <div>
      <button>Save</button>
      <a class="btn" href="index.php">Cancel</a>
    </div>
  </form>
</main>

<footer>
  <b>STUDENT<span>HUB</span></b>
</footer>
<script>if (window.self !== window.top) document.body.classList.add('embedded');</script>

</body>
</html>