<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/styles.css">
    <title>TaskFlow</title>
</head>
<body>
<?php if (isset($_SESSION['username'])): ?>
<nav class="navbar">
    <div class="container flex-between">
        <span class="nav-brand">⚡ TaskFlow</span>
        <div class="nav-links">
            <a href="/views/dashboard/index.php">Dashboard</a>
            <a href="/views/tasks/index.php">My Tasks</a>
            <a href="/views/tasks/create.php">+ New Task</a>
            <a href="/logout.php" class="btn btn-danger" style="padding:0.3rem 0.8rem;">Logout</a>
        </div>
    </div>
</nav>
<?php endif; ?>
<main class="container section">
