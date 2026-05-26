<?php
// views/tasks/create.php — Add a new task
session_start();

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../controllers/task.php';
require_once __DIR__ . '/../../public/database.config.php';

$taskCtrl = new TaskController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
$error    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_task'])) {
    $title       = trim($_POST['title']       ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date    = $_POST['due_date'] ?? '';

    $result = $taskCtrl->create(
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
        <h2>Add New Task</h2>
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
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                       placeholder="e.g. Finish project report" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"
                          placeholder="Optional details about this task..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date"
                       value="<?= htmlspecialchars($_POST['due_date'] ?? '') ?>">
            </div>
            <div class="flex" style="gap:0.75rem;margin-top:1rem;">
                <button type="submit" name="create_task" class="btn btn-primary">
                    Create Task
                </button>
                <a href="/views/tasks/index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../partial/footer.php'; ?>
