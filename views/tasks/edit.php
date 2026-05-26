<?php
// views/tasks/edit.php — Edit an existing task
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

    $result = $taskCtrl->update(
        $id,
        $_SESSION['user_id'],
        $title,
        $description,
        $due_date
    );

    if ($result === true) {
        header('Location: /views/tasks/index.php');
        exit();
    } else {
        $error = $result;
    }
}
?>
<?php require __DIR__ . '/../partial/header.php'; ?>

<div style="max-width:600px;margin:0 auto;">
    <div class="flex-between mb-2">
        <h2>Edit Task</h2>
        <a href="/views/tasks/index.php" class="btn btn-secondary">← Back</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card p-2">
        <form method="POST">
            <div class="form-group">
                <label for="title">Task Title <span style="color:#dc2626;">*</span></label>
                <input type="text" id="title" name="title"
                       value="<?= htmlspecialchars($_POST['title'] ?? $task['title']) ?>"
                       required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?=
                    htmlspecialchars($_POST['description'] ?? $task['description'])
                ?></textarea>
            </div>
            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date"
                       value="<?= htmlspecialchars($_POST['due_date'] ?? $task['due_date']) ?>">
            </div>

            <!-- Current status display -->
            <p style="color:#64748b;font-size:0.85rem;">
                Status:
                <strong>
                    <?= $task['status'] === 'completed' ? '✔ Completed' : '⏳ Pending' ?>
                </strong>
                — <a href="/views/tasks/toggle.php?id=<?= $task['id'] ?>">Toggle status</a>
            </p>

            <div class="flex" style="gap:0.75rem;margin-top:1rem;">
                <button type="submit" name="update_task" class="btn btn-primary">
                    Save Changes
                </button>
                <a href="/views/tasks/index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../partial/footer.php'; ?>
