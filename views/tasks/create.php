<?php
session_start();
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../controllers/task.php';
require_once __DIR__ . '/../../public/database.config.php';

$taskCtrl = new TaskController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME, $PORT);
$error    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_task'])) {
    $title       = trim($_POST['title']       ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date    = $_POST['due_date'] ?? '';

    $result = $taskCtrl->create($_SESSION['user_id'], $title, $description, $due_date);

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
                <h1>New Task</h1>
                <p style="margin:0;">Add something to your list.</p>
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
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                       placeholder="e.g. Finish project report" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"
                          placeholder="Optional details about this task..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date"
                       value="<?= htmlspecialchars($_POST['due_date'] ?? '') ?>">
            </div>
            <div class="flex mt-2" style="gap:0.75rem;">
                <button type="submit" name="create_task" class="btn btn-primary" style="padding:0.65rem 1.5rem;">
                    Create Task →
                </button>
                <a href="/views/tasks/index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../partial/footer.php'; ?>
