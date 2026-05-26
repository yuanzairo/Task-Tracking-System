<?php
session_start();
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../controllers/task.php';
require_once __DIR__ . '/../../public/database.config.php';

$taskCtrl = new TaskController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
$error    = '';

$id   = (int)($_GET['id'] ?? 0);
$task = $taskCtrl->getOne($id, $_SESSION['user_id']);

if (!$task) {
    header('Location: /views/tasks/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task'])) {
    $title       = trim($_POST['title']       ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date    = $_POST['due_date'] ?? '';

    $result = $taskCtrl->update($id, $_SESSION['user_id'], $title, $description, $due_date);

    if ($result === true) {
        header('Location: /views/tasks/index.php');
        exit();
    } else {
        $error = $result;
    }
}
?>
<?php require __DIR__ . '/../partial/header.php'; ?>

<div style="max-width:580px;margin:0 auto;" class="animate-fade-up">
    <div class="page-header">
        <div class="flex-between">
            <div>
                <h1>Edit Task</h1>
                <p style="margin:0;">Update your task details.</p>
            </div>
            <a href="/views/tasks/index.php" class="btn btn-secondary">← Back</a>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label for="title">Task Title <span style="color:var(--danger);">*</span></label>
                <input type="text" id="title" name="title"
                       value="<?= htmlspecialchars($_POST['title'] ?? $task['title']) ?>"
                       required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?=
                    htmlspecialchars($_POST['description'] ?? $task['description'])
                ?></textarea>
            </div>
            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date"
                       value="<?= htmlspecialchars($_POST['due_date'] ?? $task['due_date']) ?>">
            </div>

            <!-- Status row -->
            <div style="padding:0.75rem;background:var(--bg-3);border-radius:var(--radius-sm);margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:0.82rem;color:var(--text-muted);">Current status:</span>
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <?php if ($task['status'] === 'completed'): ?>
                        <span class="badge badge-completed">✔ Completed</span>
                    <?php else: ?>
                        <span class="badge badge-pending">⏳ Pending</span>
                    <?php endif; ?>
                    <a href="/views/tasks/toggle.php?id=<?= $task['id'] ?>"
                       class="btn <?= $task['status'] === 'pending' ? 'btn-success' : 'btn-secondary' ?>"
                       style="font-size:0.78rem;padding:0.3rem 0.7rem;">
                        <?= $task['status'] === 'pending' ? 'Mark Complete' : 'Mark Pending' ?>
                    </a>
                </div>
            </div>

            <div class="flex" style="gap:0.75rem;">
                <button type="submit" name="update_task" class="btn btn-primary" style="padding:0.65rem 1.5rem;">
                    Save Changes →
                </button>
                <a href="/views/tasks/index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../partial/footer.php'; ?>
