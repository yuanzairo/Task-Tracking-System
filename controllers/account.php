<?php
// controllers/account.php
// Handles all account-related database operations.

class AccountController {
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

    // Register 
    /**
     * Creates a new account.
     * Returns true on success, or an error string on failure.
     */
    public function register(string $username, string $password): bool|string {
        // Check for existing username
        $stmt = $this->conn->prepare(
            'SELECT id FROM accounts WHERE username = ? LIMIT 1'
        );
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->close();
            return 'Username is already taken.';
        }
        $stmt->close();

        // Hash password and insert
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare(
            'INSERT INTO accounts (username, password) VALUES (?, ?)'
        );
        $stmt->bind_param('ss', $username, $hash);
        $success = $stmt->execute();
        $stmt->close();

        return $success ? true : 'Registration failed. Please try again.';
    }

    // Login
    /**
     * Validates credentials and, on success, starts a session.
     * Returns true on success, or an error string on failure.
     */
    public function login(string $username, string $password): bool|string {
        $stmt = $this->conn->prepare(
            'SELECT id, password FROM accounts WHERE username = ? LIMIT 1'
        );
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($id, $hash);

        if (!$stmt->fetch()) {
            $stmt->close();
            return 'Invalid username or password.';
        }
        $stmt->close();

        if (!password_verify($password, $hash)) {
            return 'Invalid username or password.';
        }

        // Start session
        $_SESSION['user_id']  = $id;
        $_SESSION['username'] = $username;
        return true;
    }

    // Logout
    public static function logout(): void {
        session_start();
        $_SESSION = [];
        session_destroy();
        header('Location: /index.php');
        exit();
    }

    // Update
    public function update(int $id, string $username, string $password): bool {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare(
            'UPDATE accounts SET username = ?, password = ? WHERE id = ?'
        );
        $stmt->bind_param('ssi', $username, $hash, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Delete
    public function delete(int $id): bool {
        $stmt = $this->conn->prepare('DELETE FROM accounts WHERE id = ?');
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
