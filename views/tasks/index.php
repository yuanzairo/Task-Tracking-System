<?php
session_start();
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../controllers/task.php';
require_once __DIR__ . '/../../public/database.config.php';

$taskCtrl = new TaskController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);

$filter = $_GET['filter'] ?? 'all';
$tasks  = $taskCtrl->getAll($_SESSION['user_id'], $filter);

if (isset($_GET['delete'])) {
    $taskCtrl->delete((int)$_GET['delete'], $_SESSION['user_id']);
    header('Location: /views/tasks/index.php');
    exit();
}
?>
<?php require __DIR__ . '/../partial/header.php'; ?>

<div class="page-header animate-fade-up">
    <div class="flex-between">
        <div>
            <h1>My Tasks</h1>
            <p style="margin:0;"><?= count($tasks) ?> task<?= count($tasks) !== 1 ? 's' : '' ?> <?= $filter !== 'all' ? "($filter)" : '' ?></p>
        </div>
        <a href="/views/tasks/create.php" class="btn btn-primary">+ New Task</a>
    </div>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs mb-3 animate-fade-up">
    <a href="?filter=all"       class="filter-tab <?= $filter === 'all'       ? 'active' : '' ?>">All</a>
    <a href="?filter=pending"   class="filter-tab <?= $filter === 'pending'   ? 'active' : '' ?>">Pending</a>
    <a href="?filter=completed" class="filter-tab <?= $filter === 'completed' ? 'active' : '' ?>">Completed</a>
</div>

<?php if (empty($tasks)): ?>
    <div class="card animate-fade-up">
        <div class="empty-state">
            <span class="empty-state-icon">🗂</span>
            <p style="margin-bottom:1rem;">
                No <?= $filter !== 'all' ? $filter : '' ?> tasks found.
            </p>
            <?php if ($filter === 'all'): ?>
                <a href="/views/tasks/create.php" class="btn btn-primary">+ Create Task</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <table class="table animate-fade-up">
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($tasks as $i => $task): ?>
            <tr>
                <td style="color:var(--text-dim);font-size:0.8rem;"><?= $i + 1 ?></td>
                <td style="font-weight:500;<?= $task['status'] === 'completed' ? 'text-decoration:line-through;color:var(--text-muted);' : '' ?>">
                    <?= htmlspecialchars($task['title']) ?>
                </td>
                <td style="color:var(--text-muted);font-size:0.82rem;max-width:200px;">
                    <?= $task['description']
                        ? htmlspecialchars(mb_strimwidth($task['description'], 0, 55, '…'))
                        : '<span style="color:var(--text-dim);">—</span>' ?>
                </td>
                <td>
                    <?php if ($task['status'] === 'completed'): ?>
                        <span class="badge badge-completed">✔ Done</span>
                    <?php else: ?>
                        <span class="badge badge-pending">⏳ Pending</span>
                    <?php endif; ?>
                </td>
                <td style="color:var(--text-muted);font-size:0.82rem;">
                    <?= $task['due_date'] ?: '<span style="color:var(--text-dim);">—</span>' ?>
                </td>
                <td style="color:var(--text-dim);font-size:0.78rem;">
                    <?= date('M j, Y', strtotime($task['created_at'])) ?>
                </td>
                <td>
                    <div class="flex" style="gap:0.35rem;flex-wrap:wrap;">
                        <a href="/views/tasks/edit.php?id=<?= $task['id'] ?>"
                           class="btn btn-secondary" style="font-size:0.78rem;padding:0.3rem 0.65rem;">Edit</a>
                        <a href="/views/tasks/toggle.php?id=<?= $task['id'] ?>"
                           class="btn <?= $task['status'] === 'pending' ? 'btn-success' : 'btn-secondary' ?>"
                           style="font-size:0.78rem;padding:0.3rem 0.65rem;">
                            <?= $task['status'] === 'pending' ? '✔' : '↩' ?>
                        </a>
                        <a href="?delete=<?= $task['id'] ?>"
                           class="btn btn-danger" style="font-size:0.78rem;padding:0.3rem 0.65rem;"
                           onclick="return confirm('Delete this task?')">✕</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../partial/footer.php'; ?>
