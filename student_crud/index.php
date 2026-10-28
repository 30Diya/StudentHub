
<?php
require 'db.php';

$search  = trim($_GET['search'] ?? '');
$course  = trim($_GET['course'] ?? '');
$year    = trim($_GET['year'] ?? '');
$sort    = trim($_GET['sort'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 5;
$offset  = ($page - 1) * $perPage;

list($where, $types, $params) = buildWhere($search, $course, $year);
$order = buildOrder($sort);

$stmt = $conn->prepare("SELECT COUNT(*) FROM students $where");
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();

$pages = max(1, (int)ceil($total / $perPage));

$keep = [
    'search' => $search,
    'course' => $course,
    'year'   => $year,
    'sort'   => $sort
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management | StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">STUDENT<span>HUB</span></div>
    <a href="index.php">Student Management</a>
</nav>

<main class="container">
    <h2 class="page-title">Student Management</h2>

    <?php showFlash(); ?>

    <form method="get" class="bar" id="filterForm">
        <input
            type="text"
            name="search"
            placeholder="Search name, email or course"
            value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
        >

        <select name="course">
            <option value="">All Courses</option>
            <?php foreach ($COURSES as $c): ?>
                <option
                    value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>"
                    <?= $c === $course ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="year">
            <option value="">All Years</option>
            <?php for ($y = 1; $y <= 4; $y++): ?>
                <option
                    value="<?= $y ?>"
                    <?= (string)$y === $year ? 'selected' : '' ?>
                >
                    Year <?= $y ?>
                </option>
            <?php endfor; ?>
        </select>

        <select name="sort">
            <option value="" <?= $sort === '' ? 'selected' : '' ?>>
                Newest First
            </option>
            <option value="az" <?= $sort === 'az' ? 'selected' : '' ?>>
                Name A-Z
            </option>
            <option value="za" <?= $sort === 'za' ? 'selected' : '' ?>>
                Name Z-A
            </option>
            <option value="year" <?= $sort === 'year' ? 'selected' : '' ?>>
                Year
            </option>
            <option value="course" <?= $sort === 'course' ? 'selected' : '' ?>>
                Course
            </option>
        </select>

        <button type="submit">Search</button>
        <a class="btn" href="index.php">Reset</a>
        <a class="btn" href="add.php">+ Add Student</a>
        <a class="btn" href="export.php?<?= htmlspecialchars(http_build_query($keep), ENT_QUOTES, 'UTF-8') ?>">
            Export CSV
        </a>
    </form>

    <p id="statusMessage" role="status" aria-live="polite">
        Loading students...
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Course</th>
                <th>Year</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody id="studentTableBody">
            <tr>
                <td colspan="7">Loading students...</td>
            </tr>
        </tbody>
    </table>

    <div class="pager" id="pagination"></div>

    <p>Total records: <span id="totalRecords"><?= (int)$total ?></span></p>
</main>

<footer>
    <b>STUDENT<span>HUB</span></b>
</footer>

<script>
    if (window.self !== window.top) {
        document.body.classList.add('embedded');
    }
</script>
<script src="app.js"></script>
</body>
</html>
