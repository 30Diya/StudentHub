<?php
require 'db.php';
requireAdmin();
echo "Welcome admin: " . htmlspecialchars($_SESSION['name']);