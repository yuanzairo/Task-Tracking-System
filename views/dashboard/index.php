<?php
session_start();
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../controllers/task.php';
require_once __DIR__ . '/../../public/database.config.php';

$taskCtrl = new TaskController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
$stats    = $taskCtrl->getStats($_SESSION['user_id']);
$recent   = array_slice($taskCtrl->getAll($_SESSION['user_id']), 0, 5);

$pct = ($stats['total'] > 0)
    ? round(($stats['completed'] / $stats['total']) * 100)
    : 0;
?>
<?php require __DIR__ . '/../partial/header.php'; ?>

<!-- Page Header -->
<div class="page-header animate-fade-up">
    <h1>Good day, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h1>
    <p>Here's your productivity snapshot.</p>
</div>

<!-- Stat Cards -->
<div class="grid grid-3 mb-3 animate-fade-up">
    <div class="stat-card blue">
        <div class="stat-label">Total Tasks</div>
        <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
    </div>
    <div class="stat-card green">
        <div class="stat-label">Completed</div>
        <div class="stat-value"><?= $stats['completed'] ?? 0 ?></div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-label">Pending</div>
        <div class="stat-value"><?= $stats['pending'] ?? 0 ?></div>
    </div>
</div>

<!-- Progress -->
<div class="card mb-3 animate-fade-up">
    <div class="flex-between mb-2">
        <div>
            <h3 style="margin-bottom:0.15rem;">Overall Progress</h3>
            <p style="margin:0;font-size:0.8rem;"><?= $stats['completed'] ?? 0 ?> of <?= $stats['total'] ?? 0 ?> tasks done</p>
        </div>
        <span style="font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;color:var(--accent);">
            <?= $pct ?>%
        </span>
    </div>
    <div class="progress-wrap">
        <div class="progress-bar" style="width:<?= $pct ?>%;"></div>
    </div>
</div>

<!-- Recent Tasks -->
<div class="flex-between mb-2 animate-fade-up">
    <h3>Recent Tasks</h3>
    <a href="/views/tasks/index.php" class="btn btn-secondary">View All →</a>
</div>

<?php if (empty($recent)): ?>
    <div class="card animate-fade-up">
        <div class="empty-state">
            <span class="empty-state-icon">📋</span>
            <p style="margin-bottom:1rem;">No tasks yet. Start by creating one!</p>
            <a href="/views/tasks/create.php" class="btn btn-primary">+ Add First Task</a>
        </div>
    </div>
<?php else: ?>
    <table class="table animate-fade-up">
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
                <td style="font-weight:500;<?= $task['status'] === 'completed' ? 'text-decoration:line-through;color:var(--text-muted);' : '' ?>">
                    <?= htmlspecialchars($task['title']) ?>
                </td>
                <td>
                    <?php if ($task['status'] === 'completed'): ?>
                        <span class="badge badge-completed">✔ Done</span>
                    <?php else: ?>
                        <span class="badge badge-pending">⏳ Pending</span>
                    <?php endif; ?>
                </td>
                <td style="color:var(--text-muted);font-size:0.85rem;">
                    <?= $task['due_date'] ? htmlspecialchars($task['due_date']) : '—' ?>
                </td>
                <td>
                    <div class="flex" style="gap:0.4rem;">
                        <a href="/views/tasks/edit.php?id=<?= $task['id'] ?>" class="btn btn-secondary" style="font-size:0.78rem;padding:0.3rem 0.7rem;">Edit</a>
                        <a href="/views/tasks/toggle.php?id=<?= $task['id'] ?>"
                           class="btn <?= $task['status'] === 'pending' ? 'btn-success' : 'btn-secondary' ?>"
                           style="font-size:0.78rem;padding:0.3rem 0.7rem;">
                            <?= $task['status'] === 'pending' ? '✔ Complete' : '↩ Undo' ?>
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../partial/footer.php'; ?>
