<?php
// models/task.php
// Mirrors the `tasks` table in the database.

class Task {
    public int    $id;
    public int    $account_id;
    public string $title;
    public string $description;
    public string $status;      // 'pending' | 'completed'
    public string $due_date;
    public string $created_at;

    public function __construct(
        int    $account_id,
        string $title,
        string $description = '',
        string $status      = 'pending',
        string $due_date    = '',
        string $created_at  = '',
        int    $id          = 0
    ) {
        $this->id          = $id;
        $this->account_id  = $account_id;
        $this->title       = trim($title);
        $this->description = trim($description);
        $this->status      = $status;
        $this->due_date    = $due_date;
        $this->created_at  = $created_at;
    }
}
