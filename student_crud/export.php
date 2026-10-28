<?php
require 'db.php';

list($where, $types, $params) = buildWhere(
  trim($_GET['search'] ?? ''),
  trim($_GET['course'] ?? ''),
  trim($_GET['year'] ?? '')
);
$order = buildOrder(trim($_GET['sort'] ?? ''));

$stmt = $conn->prepare("SELECT id,name,email,phone,course,year FROM students $where $order");
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="students.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['ID','Name','Email','Phone','Course','Year']);
while ($r = $res->fetch_assoc()) {
  fputcsv($out, $r);
}
fclose($out);