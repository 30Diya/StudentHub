
<?php
require_once "db_connect.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    exit("Invalid event ID.");
}

$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    exit("Event not found.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $date = $_POST["event_date"] ?? "";
    $location = trim($_POST["location"] ?? "");
    $seats = filter_var($_POST["seats"] ?? "", FILTER_VALIDATE_INT);
    $category = trim($_POST["category"] ?? "");
    $status = $_POST["status"] ?? "active";

    if ($title === "" || $date === "" || $location === "" ||
        $seats === false || $seats < 1 ||
        !in_array($status, ["active", "inactive"], true)) {
        $message = "Enter valid event details. Seats must be at least 1.";
    } else {
        $poster = $event["poster"];

        try {
            if (isset($_FILES["poster"]) &&
                $_FILES["poster"]["error"] !== UPLOAD_ERR_NO_FILE) {

                if ($_FILES["poster"]["error"] !== UPLOAD_ERR_OK ||
                    $_FILES["poster"]["size"] > 2 * 1024 * 1024) {
                    throw new RuntimeException("Upload failed or poster exceeds 2 MB.");
                }

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($_FILES["poster"]["tmp_name"]);
                $allowed = [
                    "image/jpeg" => "jpg",
                    "image/png" => "png",
                    "image/webp" => "webp"
                ];

                if (!isset($allowed[$mime])) {
                    throw new RuntimeException("Use a JPG, PNG, or WEBP image.");
                }

                $dir = __DIR__ . "/../uploads/event_posters/";
                if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
                    throw new RuntimeException("Cannot create upload folder.");
                }

                $filename = bin2hex(random_bytes(16)) . "." . $allowed[$mime];
                if (!move_uploaded_file($_FILES["poster"]["tmp_name"], $dir . $filename)) {
                    throw new RuntimeException("Could not save poster.");
                }

                $poster = "uploads/event_posters/" . $filename;
            }

            $stmt = $pdo->prepare(
                "UPDATE events SET title=?, description=?, event_date=?,
                 location=?, seats=?, category=?, status=?, poster=?
                 WHERE event_id=?"
            );

            $stmt->execute([
                $title, $description, $date, $location, $seats,
                $category, $status, $poster, $id
            ]);

            header("Location: manage_events.php");
            exit;
        } catch (Throwable $e) {
            $message = $e->getMessage();
        }
    }
}

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Event</title>
</head>
<body>
    <h1>Edit Event</h1>

    <?php if ($message !== ""): ?>
        <p><?= h($message) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <p>Title: <input name="title" required value="<?= h($event["title"]) ?>"></p>

        <p>Description:
            <textarea name="description"><?= h($event["description"] ?? "") ?></textarea>
        </p>

        <p>Date and time:
            <input type="datetime-local" name="event_date" required
                   value="<?= h(date("Y-m-d\TH:i", strtotime($event["event_date"]))) ?>">
        </p>

        <p>Location:
            <input name="location" required value="<?= h($event["location"]) ?>">
        </p>

        <p>Seats:
            <input type="number" name="seats" min="1" required
                   value="<?= (int)$event["seats"] ?>">
        </p>

        <p>Category:
            <input name="category" value="<?= h($event["category"] ?? "") ?>">
        </p>

        <p>Status:
            <select name="status">
                <option value="active" <?= $event["status"] === "active" ? "selected" : "" ?>>Active</option>
                <option value="inactive" <?= $event["status"] === "inactive" ? "selected" : "" ?>>Inactive</option>
            </select>
        </p>

        <?php if (!empty($event["poster"])): ?>
            <p>Current poster:</p>
            <img src="../<?= h($event["poster"]) ?>" alt="Current poster" width="120">
        <?php endif; ?>

        <p>Upload a replacement poster (optional, max 2 MB):
            <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp">
        </p>

        <button type="submit">Save Changes</button>
    </form>

    <p><a href="manage_events.php">Back to Events</a></p>
</body>
</html>
