<?php
session_start();
$conn = new mysqli("localhost", "root", "", "student_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

$COURSES = ['BCA','BBA','B.Com','B.Sc IT','MCA','B.Tech CSE'];

function flash($type, $msg) { $_SESSION['flash'] = ['type'=>$type, 'msg'=>$msg]; }

function showFlash() {
  if (isset($_SESSION['flash'])) {
    $f = $_SESSION['flash']; unset($_SESSION['flash']);
    echo "<div class='alert {$f['type']}'>" . htmlspecialchars($f['msg']) . "</div>";
  }
}

function buildWhere($search, $course) {
  $where = "WHERE status='active'"; $types = ''; $params = [];
  if ($search !== '') {
    $where .= " AND (name LIKE ? OR email LIKE ? OR course LIKE ?)";
    $like = "%$search%"; $types .= 'sss';
    array_push($params, $like, $like, $like);
  }
  if ($course !== '') { $where .= " AND course = ?"; $types .= 's'; $params[] = $course; }
  return [$where, $types, $params];
}