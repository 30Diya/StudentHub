
<?php
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
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Events page error: ' . $e->getMessage());
    $events = [];
    $error = true;
}

function escapeHtml($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Events</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 25px;
            font-family: Arial, sans-serif;
            background: #f3f5f9;
            color: #222;
        }
        .container { max-width: 1100px; margin: auto; }
        h1 { color: #243b64; }
        .events {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 22px;
        }
        .event {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px #00000014;
        }
        .event img {
            width: 100%;
            height: 190px;
            object-fit: cover;
        }
        .details { padding: 18px; }
        .details h2 { margin-top: 0; color: #2457a7; }
        .details p { line-height: 1.5; overflow-wrap: anywhere; }
        .empty {
            background: white;
            padding: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Upcoming Events</h1>
    <p>Explore events organized by StudentHub.</p>

    <?php if (!empty($error)): ?>
        <p class="empty">Unable to load events right now. Please try again later.</p>
    <?php elseif (empty($events)): ?>
        <p class="empty">No active events available right now.</p>
    <?php else: ?>
        <div class="events">
            <?php foreach ($events as $event): ?>
                <article class="event">
                    <?php if (!empty($event['poster'])): ?>
                        <img
                            src="../<?= escapeHtml($event['poster']) ?>"
                            alt="<?= escapeHtml($event['title']) ?>"
                        >
                    <?php endif; ?>

                    <div class="details">
                        <h2><?= escapeHtml($event['title']) ?></h2>

                        <?php if (!empty($event['description'])): ?>
                            <p><?= nl2br(escapeHtml($event['description'])) ?></p>
                        <?php endif; ?>

                        <p><strong>Date:</strong>
                            <?= escapeHtml($event['event_date']) ?></p>
                        <p><strong>Location:</strong>
                            <?= escapeHtml($event['location']) ?></p>
                        <p><strong>Available seats:</strong>
                            <?= (int)$event['seats'] ?></p>

                        <?php if (!empty($event['category'])): ?>
                            <p><strong>Category:</strong>
                                <?= escapeHtml($event['category']) ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
