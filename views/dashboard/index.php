<?php
// views/dashboard/index.php — Dashboard
session_start();

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../controllers/task.php';
require_once __DIR__ . '/../../public/database.config.php';

$taskCtrl = new TaskController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
$stats    = $taskCtrl->getStats($_SESSION['user_id']);
$recent   = $taskCtrl->getAll($_SESSION['user_id']); // all, sorted newest first
$recent   = array_slice($recent, 0, 5);              // show latest 5 on dashboard

$pct = ($stats['total'] > 0)
    ? round(($stats['completed'] / $stats['total']) * 100)
    : 0;
?>
<?php require __DIR__ . '/../partial/header.php'; ?>

<h1 style="margin-bottom:0.25rem;">
    Hello, <?= htmlspecialchars($_SESSION['username']) ?> 👋
</h1>
<p style="color:#64748b;margin-bottom:2rem;">Here's your task overview for today.</p>

<!-- Stats Cards -->
<div class="grid grid-3 mb-2">
    <div class="card" style="border-left:4px solid #2563eb;">
        <p style="color:#64748b;font-size:0.85rem;margin-bottom:0.25rem;">Total Tasks</p>
        <h2 style="font-size:2rem;"><?= $stats['total'] ?></h2>
    </div>
    <div class="card" style="border-left:4px solid #16a34a;">
        <p style="color:#64748b;font-size:0.85rem;margin-bottom:0.25rem;">Completed</p>
        <h2 style="font-size:2rem;color:#16a34a;"><?= $stats['completed'] ?></h2>
    </div>
    <div class="card" style="border-left:4px solid #f59e0b;">
        <p style="color:#64748b;font-size:0.85rem;margin-bottom:0.25rem;">Pending</p>
        <h2 style="font-size:2rem;color:#f59e0b;"><?= $stats['pending'] ?></h2>
    </div>
</div>

<!-- Progress bar -->
<div class="card mb-2">
    <div class="flex-between mb-1">
        <span style="font-weight:600;">Overall Progress</span>
        <span style="color:#64748b;"><?= $pct ?>% complete</span>
    </div>
    <div style="background:#e5e7eb;border-radius:999px;height:10px;">
        <div style="background:#2563eb;width:<?= $pct ?>%;height:10px;border-radius:999px;transition:width 0.4s;"></div>
    </div>
</div>

<!-- Recent Tasks -->
<div class="flex-between mb-1">
    <h3>Recent Tasks</h3>
    <a href="/views/tasks/index.php" class="btn btn-secondary">View All</a>
</div>

<?php if (empty($recent)): ?>
    <div class="alert alert-warning">
        No tasks yet. <a href="/views/tasks/create.php">Add your first task →</a>
    </div>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($recent as $task): ?>
            <tr>
                <td><?= htmlspecialchars($task['title']) ?></td>
                <td>
                    <?php if ($task['status'] === 'completed'): ?>
                        <span style="color:#16a34a;font-weight:600;">✔ Completed</span>
                    <?php else: ?>
                        <span style="color:#f59e0b;font-weight:600;">⏳ Pending</span>
                    <?php endif; ?>
                </td>
                <td><?= $task['due_date'] ? htmlspecialchars($task['due_date']) : '—' ?></td>
                <td class="flex" style="gap:0.5rem;">
                    <a href="/views/tasks/edit.php?id=<?= $task['id'] ?>" class="btn btn-secondary">Edit</a>
                    <a href="/views/tasks/toggle.php?id=<?= $task['id'] ?>"
                       class="btn <?= $task['status'] === 'pending' ? 'btn-success' : 'btn-secondary' ?>">
                        <?= $task['status'] === 'pending' ? 'Complete' : 'Undo' ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../partial/footer.php'; ?>
