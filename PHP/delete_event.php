
<?php
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: manage_events.php");
    exit;
}

$id = filter_input(INPUT_POST, "event_id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid event ID.");
}

$stmt = $pdo->prepare("SELECT poster FROM events WHERE event_id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    exit("Event not found.");
}

$stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
$stmt->execute([$id]);

if (!empty($event["poster"])) {
    $posterPath = __DIR__ . "/../" . $event["poster"];

    if (is_file($posterPath)) {
        unlink($posterPath);
    }
}

header("Location: manage_events.php");
exit;
?>
