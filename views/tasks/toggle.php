<?php
// views/tasks/toggle.php — Toggle pending ↔ completed
session_start();

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../controllers/task.php';
require_once __DIR__ . '/../../public/database.config.php';

$taskCtrl = new TaskController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME, $PORT);

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $taskCtrl->toggleStatus($id, $_SESSION['user_id']);
}

// Return to wherever the user came from (fallback: task list)
$back = $_SERVER['HTTP_REFERER'] ?? '/views/tasks/index.php';
header('Location: ' . $back);
exit();
