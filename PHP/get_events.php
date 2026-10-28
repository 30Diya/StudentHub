
<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db_connect.php';

try {
    $stmt = $pdo->prepare(
        "SELECT event_id, title, description, event_date,
                location, seats, category, poster
         FROM events
         WHERE status = 'active'
         ORDER BY event_date ASC"
    );

    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $events = [];

    foreach ($rows as $row) {
        $timestamp = strtotime($row['event_date']);

        $events[] = [
            'id' => (int) $row['event_id'],
            'title' => $row['title'],
            'description' => $row['description'] ?? '',
            'date' => date('d F Y', $timestamp),
            'year' => date('Y', $timestamp),
            'day' => date('d', $timestamp),
            'month' => date('M', $timestamp),
            'category' => strtoupper($row['category'] ?? 'CAMPUS'),
            'course' => 'All Courses',
            'location' => $row['location'],
            'seats' => (int) $row['seats'],
            'image' => !empty($row['poster'])
                ? '../' . $row['poster']
                : ''
        ];
    }

    echo json_encode(
        ['events' => $events],
        JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
    );

} catch (Throwable $e) {
    error_log('Get events error: ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'error' => 'Unable to load events.'
    ]);
}
?>
