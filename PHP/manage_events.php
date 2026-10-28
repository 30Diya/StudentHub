
<?php
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/auth_check.php';

// Only admins can manage events
if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied. Admins only.');
}

$message = '';
$messageType = '';
$uploadDir = __DIR__ . '/../uploads/event_posters/';

// Create a CSRF token for form security
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle adding an event
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($csrfToken) ||
        !hash_equals($_SESSION['csrf_token'], $csrfToken)
    ) {
        http_response_code(403);
        exit('Invalid request. Please refresh the page and try again.');
    }

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $seats = filter_var($_POST['seats'] ?? '', FILTER_VALIDATE_INT);
    $category = trim($_POST['category'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $poster = null;

    if (
        $title === '' ||
        $eventDate === '' ||
        $location === '' ||
        $seats === false ||
        $seats === null ||
        $seats < 1
    ) {
        $message = 'Enter valid event details and at least 1 seat.';
    } elseif (!in_array($status, ['active', 'inactive'], true)) {
        $message = 'Please select a valid event status.';
    } else {
        try {
            // Validate and save an optional poster
            if (
                isset($_FILES['poster']) &&
                $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE
            ) {
                $file = $_FILES['poster'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    throw new RuntimeException('Poster upload failed.');
                }

                if ($file['size'] > 2 * 1024 * 1024) {
                    throw new RuntimeException(
                        'Poster must be 2 MB or smaller.'
                    );
                }

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);

                $allowedTypes = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp'
                ];

                if (!isset($allowedTypes[$mime])) {
                    throw new RuntimeException(
                        'Only JPG, PNG, and WEBP posters are allowed.'
                    );
                }

                if (
                    !is_dir($uploadDir) &&
                    !mkdir($uploadDir, 0755, true) &&
                    !is_dir($uploadDir)
                ) {
                    throw new RuntimeException(
                        'Could not create the poster folder.'
                    );
                }

                $filename = bin2hex(random_bytes(16))
                    . '.' . $allowedTypes[$mime];

                if (
                    !move_uploaded_file(
                        $file['tmp_name'],
                        $uploadDir . $filename
                    )
                ) {
                    throw new RuntimeException(
                        'Could not save the poster.'
                    );
                }

                $poster = 'uploads/event_posters/' . $filename;
            }

            // Insert event into the database
            $stmt = $pdo->prepare(
                'INSERT INTO events
                (title, description, event_date, location, seats,
                 category, status, poster)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );

            $stmt->execute([
                $title,
                $description,
                $eventDate,
                $location,
                $seats,
                $category,
                $status,
                $poster
            ]);

            $message = 'Event added successfully!';
            $messageType = 'success';

        } catch (Throwable $e) {
            // Keep technical details in the server error log
            error_log('Manage events error: ' . $e->getMessage());

            // Remove a poster if saving the event failed
            if (
                $poster !== null &&
                is_file(__DIR__ . '/../' . $poster)
            ) {
                unlink(__DIR__ . '/../' . $poster);
            }

            $message = 'Unable to add the event. Please check your details and try again.';
            $messageType = 'error';
        }
    }
}

// Fetch events for the table
try {
    $stmt = $pdo->query(
        'SELECT event_id, title, event_date, location, seats,
                category, status, poster
         FROM events
         ORDER BY event_id DESC'
    );

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Fetch events error: ' . $e->getMessage());
    $events = [];
    $message = 'Unable to load events right now.';
    $messageType = 'error';
}

function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Manage Events</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 25px;
            font-family: Arial, sans-serif;
            background: #f3f5f9;
            color: #222;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        h1, h2 {
            color: #243b64;
        }

        .panel {
            background: white;
            padding: 22px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccd2dc;
            border-radius: 5px;
            font: inherit;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        button, .action-link {
            display: inline-block;
            border: 0;
            border-radius: 5px;
            padding: 10px 15px;
            background: #2457a7;
            color: white;
            text-decoration: none;
            font: inherit;
            cursor: pointer;
        }

        button:hover, .action-link:hover {
            background: #183d79;
        }

        .delete-button {
            background: #c62828;
        }

        .delete-button:hover {
            background: #8e1717;
        }

        .message {
            padding: 12px;
            margin-bottom: 18px;
            border-radius: 5px;
        }

        .success {
            background: #e1f5e7;
            color: #176532;
        }

        .error {
            background: #ffebee;
            color: #a31925;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #e2e6ed;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #eaf0fa;
        }

        .poster {
            width: 75px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .actions form {
            margin: 0;
        }

        @media (max-width: 600px) {
            body {
                padding: 12px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .full-width {
                grid-column: auto;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <h1>StudentHub - Manage Events</h1>
    <p>Add new events and manage existing events below.</p>

    <?php if ($message !== ''): ?>
        <div class="message <?= e($messageType) ?>">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <section class="panel">
        <h2>Add New Event</h2>

        <form method="POST" enctype="multipart/form-data">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION['csrf_token']) ?>"
            >

            <div class="form-grid">

                <div class="field">
                    <label for="title">Event Title *</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        maxlength="200"
                        required
                    >
                </div>

                <div class="field">
                    <label for="event_date">Event Date and Time *</label>
                    <input
                        type="datetime-local"
                        id="event_date"
                        name="event_date"
                        required
                    >
                </div>

                <div class="field">
                    <label for="location">Location *</label>
                    <input
                        type="text"
                        id="location"
                        name="location"
                        maxlength="255"
                        required
                    >
                </div>

                <div class="field">
                    <label for="seats">Available Seats *</label>
                    <input
                        type="number"
                        id="seats"
                        name="seats"
                        min="1"
                        step="1"
                        required
                    >
                </div>

                <div class="field">
                    <label for="category">Category</label>
                    <input
                        type="text"
                        id="category"
                        name="category"
                        maxlength="100"
                    >
                </div>

                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="field full-width">
                    <label for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        maxlength="5000"
                    ></textarea>
                </div>

                <div class="field full-width">
                    <label for="poster">Event Poster (JPG, PNG, WEBP; max 2 MB)</label>
                    <input
                        type="file"
                        id="poster"
                        name="poster"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    >
                </div>

                <div class="field full-width">
                    <button type="submit">Add Event</button>
                </div>

            </div>
        </form>
    </section>

    <section class="panel">
        <h2>Existing Events</h2>

        <?php if (empty($events)): ?>
            <p>No events found.</p>
        <?php else: ?>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Seats</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= (int)$event['event_id'] ?></td>

                            <td>
                                <?php if (!empty($event['poster'])): ?>
                                    <img
                                        class="poster"
                                        src="../<?= e($event['poster']) ?>"
                                        alt="Event poster"
                                    >
                                <?php else: ?>
                                    No poster
                                <?php endif; ?>
                            </td>

                            <td><?= e($event['title']) ?></td>
                            <td><?= e($event['event_date']) ?></td>
                            <td><?= e($event['location']) ?></td>
                            <td><?= (int)$event['seats'] ?></td>
                            <td><?= e($event['category']) ?></td>
                            <td><?= e($event['status']) ?></td>

                            <td>
                                <div class="actions">
                                    <a
                                        class="action-link"
                                        href="edit_event.php?id=<?= (int)$event['event_id'] ?>"
                                    >Edit</a>

                                    <form
                                        method="POST"
                                        action="delete_event.php"
                                        onsubmit="return confirm('Are you sure you want to delete this event?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= e($_SESSION['csrf_token']) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="event_id"
                                            value="<?= (int)$event['event_id'] ?>"
                                        >

                                        <button
                                            class="delete-button"
                                            type="submit"
                                        >Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </section>

</div>
</body>
</html>
