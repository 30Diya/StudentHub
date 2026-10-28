<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)($_POST['id'] ?? 0);

  $stmt = $conn->prepare("UPDATE students SET status='deleted' WHERE id=?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute() && $stmt->affected_rows > 0) {
    flash('success', 'Student deleted successfully.');
  } else {
    flash('error', 'Delete failed or student not found.');
  }
}

header("Location: index.php");
exit;