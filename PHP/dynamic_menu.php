
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? '';

$loggedIn = !empty($_SESSION['user_id']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="../Page/home.html">
        STUDENTHUB
    </a>

    <div class="navbar-nav">
        <a class="nav-link" href="../Page/home.html">Home</a>
        <a class="nav-link" href="../Page/about.html">About Us</a>

        <?php if ($loggedIn && $role === 'admin'): ?>
            <a class="nav-link"
               href="admin_dashboard.php">Admin Dashboard</a>
        <?php elseif ($loggedIn && $role === 'student'): ?>
            <a class="nav-link"
               href="../Page/dashboard.html">Dashboard</a>
        <?php endif; ?>

        <a class="nav-link" href="../Page/event.html">Event</a>
        <a class="nav-link" href="../Page/contact.html">Contact</a>
        <a class="nav-link" href="../Page/profile.html">Profile</a>

        <?php if (!$loggedIn): ?>
            <a class="nav-link" href="../Page/login.html">Login</a>
            <a class="nav-link" href="../Page/register.html">Register</a>
        <?php else: ?>
            <a class="nav-link" href="logout.php">Logout</a>
        <?php endif; ?>
    </div>
</nav>
