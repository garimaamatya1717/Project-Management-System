<?php
session_start();

$page = $_GET['page'] ?? 'login';

if ($page === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Load DB + data helpers first
require_once 'config/db.php';
require_once 'data/data.php';
require_once 'data/actions.php';

// Auth guards
if (!isset($_SESSION['user']) && !in_array($page, ['login', 'register'])) {
    header('Location: index.php?page=login'); exit;
}
if (isset($_SESSION['user']) && in_array($page, ['login', 'register'])) {
    header('Location: index.php?page=dashboard'); exit;
}

// Refresh session user from DB so role/name changes are reflected
if (isset($_SESSION['user'])) {
    $refreshed = getUserById($_SESSION['user']['id']);
    if ($refreshed) $_SESSION['user'] = $refreshed;
}

$allowed = ['login','register','dashboard','kanban','projects'];
if (!in_array($page, $allowed)) $page = 'login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJEX — Deep-Space Project Management</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include "pages/{$page}.php"; ?>
<script src="js/app.js"></script>
</body>
</html>
