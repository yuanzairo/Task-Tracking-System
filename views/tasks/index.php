<?php
// views/tasks/index.php — All Tasks (with filter)
session_start();

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../controllers/task.php';
require_once __DIR__ . '/../../public/database.config.php';

$taskCtrl = new TaskController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);

$filter = $_GET['filter'] ?? 'all';
$tasks  = $taskCtrl->getAll($_SESSION['user_id'], $filter);

// Handle delete from this page
if (isset($_GET['delete'])) {
    $taskCtrl->delete((int)$_GET['delete'], $_SESSION['user_id']);
    header('Location: /views/tasks/index.php');
    exit();
}
?>
<?php require __DIR__ . '/../partial/header.php'; ?>

<div class="flex-between mb-2">
    <h2>My Tasks</h2>
    <a href="/views/tasks/create.php" class="btn btn-primary">+ Add Task</a>
</div>

<!-- Filter Tabs -->
<div class="flex mb-2" style="gap:0.5rem;">
    <a href="?filter=all"
       class="btn <?= $filter === 'all' ? 'btn-primary' : 'btn-secondary' ?>">
       All
    </a>
    <a href="?filter=pending"
       class="btn <?= $filter === 'pending' ? 'btn-primary' : 'btn-secondary' ?>">
       Pending
    </a>
    <a href="?filter=completed"
       class="btn <?= $filter === 'completed' ? 'btn-primary' : 'btn-secondary' ?>">
       Completed
    </a>
</div>

<?php if (empty($tasks)): ?>
    <div class="alert alert-warning">
        No <?= $filter !== 'all' ? $filter : '' ?> tasks found.
        <?php if ($filter === 'all'): ?>
            <a href="/views/tasks/create.php">Create one →</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <table class="table">
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
            <tr <?= $task['status'] === 'completed' ? 'style="opacity:0.6;"' : '' ?>>
                <td><?= $i + 1 ?></td>
                <td style="font-weight:600;<?= $task['status'] === 'completed' ? 'text-decoration:line-through;' : '' ?>">
                    <?= htmlspecialchars($task['title']) ?>
                </td>
                <td style="color:#64748b;font-size:0.85rem;">
                    <?= $task['description']
                        ? htmlspecialchars(mb_strimwidth($task['description'], 0, 60, '…'))
                        : '—' ?>
                </td>
                <td>
                    <?php if ($task['status'] === 'completed'): ?>
                        <span style="color:#16a34a;font-weight:600;">✔ Done</span>
                    <?php else: ?>
                        <span style="color:#f59e0b;font-weight:600;">⏳ Pending</span>
                    <?php endif; ?>
                </td>
                <td><?= $task['due_date'] ?: '—' ?></td>
                <td style="font-size:0.8rem;color:#94a3b8;">
                    <?= date('M j, Y', strtotime($task['created_at'])) ?>
                </td>
                <td>
                    <div class="flex" style="gap:0.4rem;flex-wrap:wrap;">
                        <a href="/views/tasks/edit.php?id=<?= $task['id'] ?>"
                           class="btn btn-secondary" style="font-size:0.8rem;">Edit</a>
                        <a href="/views/tasks/toggle.php?id=<?= $task['id'] ?>"
                           class="btn <?= $task['status'] === 'pending' ? 'btn-success' : 'btn-secondary' ?>"
                           style="font-size:0.8rem;">
                            <?= $task['status'] === 'pending' ? '✔ Complete' : '↩ Undo' ?>
                        </a>
                        <a href="?delete=<?= $task['id'] ?>"
                           class="btn btn-danger" style="font-size:0.8rem;"
                           onclick="return confirm('Delete this task?')">Delete</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../partial/footer.php'; ?>
