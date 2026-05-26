<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 1) ?>public/styles.css">
    <title>TaskFlow</title>
</head>
<body>
<?php if (isset($_SESSION['username'])): ?>
<nav class="navbar">
    <div class="container">
        <span class="nav-brand">✦ Task<span>Flow</span></span>
        <div class="nav-links">
            <a href="/views/dashboard">Dashboard</a>
            <a href="/views/tasks">My Tasks</a>
            <a href="/views/tasks/create">+ New Task</a>
            <a href="/logout" class="btn btn-danger" style="margin-left:0.5rem;">Logout</a>
        </div>
    </div>
</nav>
<?php endif; ?>
<main class="container section">
