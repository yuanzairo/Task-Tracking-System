<?php
// controllers/task.php
// Handles all task-related database operations.

class TaskController {
    private mysqli $conn;

    public function __construct(
        string $server_name,
        string $username,
        string $password,
        string $db_name
    ) {
        $this->conn = new mysqli($server_name, $username, $password, $db_name);

        if ($this->conn->connect_error) {
            die('Database connection failed: ' . $this->conn->connect_error);
        }
    }

    // ── Create ────────────────────────────────────────────────────────────────
    /**
     * Adds a new task for the given account.
     * Returns true on success, or an error string on failure.
     */
    public function create(
        int    $account_id,
        string $title,
        string $description = '',
        string $due_date    = ''
    ): bool|string {
        if (empty($title)) {
            return 'Task title cannot be empty.';
        }

        $due = $due_date ?: null;

        $stmt = $this->conn->prepare(
            'INSERT INTO tasks (account_id, title, description, due_date)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->bind_param('isss', $account_id, $title, $description, $due);
        $success = $stmt->execute();
        $stmt->close();

        return $success ? true : 'Failed to create task.';
    }

    // ── Read All ──────────────────────────────────────────────────────────────
    /**
     * Returns all tasks belonging to the given account,
     * optionally filtered by status.
     */
    public function getAll(int $account_id, string $filter = 'all'): array {
        $sql = 'SELECT * FROM tasks WHERE account_id = ?';
        if ($filter === 'pending' || $filter === 'completed') {
            $sql .= " AND status = '$filter'";
        }
        $sql .= ' ORDER BY created_at DESC';

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $account_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
        $stmt->close();
        return $tasks;
    }

    // ── Read One ──────────────────────────────────────────────────────────────
    /**
     * Returns a single task by ID, only if it belongs to the given account.
     */
    public function getOne(int $id, int $account_id): ?array {
        $stmt = $this->conn->prepare(
            'SELECT * FROM tasks WHERE id = ? AND account_id = ? LIMIT 1'
        );
        $stmt->bind_param('ii', $id, $account_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $task   = $result->fetch_assoc();
        $stmt->close();
        return $task ?: null;
    }

    // ── Update ────────────────────────────────────────────────────────────────
    /**
     * Edits a task's title, description, and due date.
     * Returns true on success, or an error string on failure.
     */
    public function update(
        int    $id,
        int    $account_id,
        string $title,
        string $description = '',
        string $due_date    = ''
    ): bool|string {
        if (empty($title)) {
            return 'Task title cannot be empty.';
        }

        $due = $due_date ?: null;

        $stmt = $this->conn->prepare(
            'UPDATE tasks
             SET title = ?, description = ?, due_date = ?
             WHERE id = ? AND account_id = ?'
        );
        $stmt->bind_param('sssii', $title, $description, $due, $id, $account_id);
        $success = $stmt->execute();
        $stmt->close();

        return $success ? true : 'Failed to update task.';
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    public function delete(int $id, int $account_id): bool {
        $stmt = $this->conn->prepare(
            'DELETE FROM tasks WHERE id = ? AND account_id = ?'
        );
        $stmt->bind_param('ii', $id, $account_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // ── Mark Complete / Pending ───────────────────────────────────────────────
    /**
     * Toggles a task's status between 'pending' and 'completed'.
     */
    public function toggleStatus(int $id, int $account_id): bool {
        // Fetch current status first
        $task = $this->getOne($id, $account_id);
        if (!$task) return false;

        $new_status = ($task['status'] === 'pending') ? 'completed' : 'pending';

        $stmt = $this->conn->prepare(
            'UPDATE tasks SET status = ? WHERE id = ? AND account_id = ?'
        );
        $stmt->bind_param('sii', $new_status, $id, $account_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // ── Stats (for dashboard summary) ─────────────────────────────────────────
    public function getStats(int $account_id): array {
        $stmt = $this->conn->prepare(
            "SELECT
                COUNT(*) AS total,
                SUM(status = 'completed') AS completed,
                SUM(status = 'pending')   AS pending
             FROM tasks
             WHERE account_id = ?"
        );
        $stmt->bind_param('i', $account_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats  = $result->fetch_assoc();
        $stmt->close();
        return $stats;
    }
}
