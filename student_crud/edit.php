<?php
require 'db.php';
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $conn->prepare("SELECT name,email,phone,course,year FROM students WHERE id=? AND status='active'");
$stmt->bind_param("i", $id); $stmt->execute();
$student = $stmt->get_result()->fetch_assoc(); $stmt->close();
if (!$student) {
  flash('error', 'Student not found.');
  header("Location: index.php");
  exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name   = trim($_POST['name'] ?? '');
  $email  = trim($_POST['email'] ?? '');
  $phone  = trim($_POST['phone'] ?? '');
  $course = trim($_POST['course'] ?? '');
  $year   = (int)($_POST['year'] ?? 0);
  $student = compact('name','email','phone','course','year');

  if ($name === '') $errors[] = "Name is required.";
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email.";
  if (!preg_match('/^[0-9]{10}$/', $phone)) $errors[] = "Phone must be 10 digits.";
  if (!in_array($course, $COURSES)) $errors[] = "Select a course.";
  if ($year < 1 || $year > 4) $errors[] = "Year must be 1 to 4.";

  if (!$errors) {
    $chk = $conn->prepare("SELECT id FROM students WHERE email=? AND id<>?");
    $chk->bind_param("si", $email, $id); $chk->execute(); $chk->store_result();
    if ($chk->num_rows > 0) $errors[] = "This email belongs to another student.";
    $chk->close();
  }

  if (!$errors) {
    $stmt = $conn->prepare("UPDATE students SET name=?,email=?,phone=?,course=?,year=? WHERE id=?");
    $stmt->bind_param("ssssii", $name, $email, $phone, $course, $year, $id);
    if ($stmt->execute()) {
      flash('success', 'Student updated successfully.');
      header("Location: index.php");
      exit;
    }
    $errors[] = "Update failed: " . $stmt->error;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Student</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
  <div class="logo">STUDENT<span>HUB</span></div>
  <a href="index.php">Student Management</a>
</nav>

<main class="container">
  <h2>Edit Student</h2>
  <?php foreach ($errors as $e) echo "<div class='alert error'>" . htmlspecialchars($e) . "</div>"; ?>

  <form method="post" class="form">
    <input type="hidden" name="id" value="<?= $id ?>">

    <label>Name</label>
    <input name="name" value="<?= htmlspecialchars($student['name']) ?>">

    <label>Email</label>
    <input name="email" value="<?= htmlspecialchars($student['email']) ?>">

    <label>Phone (10 digits)</label>
    <input name="phone" value="<?= htmlspecialchars($student['phone']) ?>">

    <label>Course</label>
    <select name="course">
      <?php foreach ($COURSES as $c): ?>
        <option <?= $c === $student['course'] ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Year (1 to 4)</label>
    <input type="number" name="year" min="1" max="4" value="<?= (int)$student['year'] ?>">

    <div>
      <button>Update</button>
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