<?php
session_start();

$conn = new mysqli("localhost", "root", "", "event_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

// Same categories and courses as your Events page
$CATEGORIES = ['TECHNOLOGY', 'SPORTS', 'WORKSHOP', 'CAMPUS', 'SEMINAR', 'EXHIBITION'];
$COURSES    = ['BCA', 'BBA', 'BSc', 'BA', 'BCom'];

// Poster upload rules
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_POSTER_SIZE', 2 * 1024 * 1024);   // 2 MB

function flash($type, $msg) { $_SESSION['flash'] = ['type' => $type, 'msg' => $msg]; }

function showFlash() {
  if (isset($_SESSION['flash'])) {
    $f = $_SESSION['flash']; unset($_SESSION['flash']);
    $class = $f['type'] === 'success' ? 'alert-success' : 'alert-danger';
    echo "<div class='alert $class'>" . htmlspecialchars($f['msg']) . "</div>";
  }
}

// Only a logged-in ADMIN (from your StudentHub login) can open a page that calls this
function requireAdmin() {
  if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../HTML/login.html");
    exit;
  }
}

// Saves every add / update / delete in the audit_log table
function logAction($conn, $event_id, $action, $details) {
  $admin = $_SESSION['name'] ?? 'unknown';
  $stmt = $conn->prepare("INSERT INTO audit_log (event_id, action, details, admin_name) VALUES (?,?,?,?)");
  $stmt->bind_param("isss", $event_id, $action, $details, $admin);
  $stmt->execute();
  $stmt->close();
}