<?php
require "db_connect.php";

// Show all events
$stmt = $pdo->query("SELECT event_id, title, event_date, location, seats FROM events");
echo "<h2>Events</h2>";
foreach ($stmt as $row) {
    echo htmlspecialchars($row["title"]) . " - " .
         $row["event_date"] . " - " .
         htmlspecialchars($row["location"]) . "<br>";
}

// Show which student registered for which event (JOIN)
$sql = "SELECT s.name, e.title, r.registered_at
        FROM registrations r
        JOIN students s ON r.student_id = s.student_id
        JOIN events e ON r.event_id = e.event_id";
$stmt = $pdo->query($sql);

echo "<h2>Registrations</h2>";
foreach ($stmt as $row) {
    echo htmlspecialchars($row["name"]) . " registered for " .
         htmlspecialchars($row["title"]) . " on " . $row["registered_at"] . "<br>";
}
?>