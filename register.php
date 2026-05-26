<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: /views/dashboard/index.php');
    exit();
}

require_once __DIR__ . '/models/account.php';
require_once __DIR__ . '/controllers/account.php';
require_once __DIR__ . '/public/database.config.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password']  ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (empty($username) || empty($password) || empty($password2)) {
        $error = 'Please fill in all fields.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $password2) {
        $error = 'Passwords do not match.';
    } else {
        $controller = new AccountController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
        $result     = $controller->register($username, $password);

        if ($result === true) {
            $success = 'Account created! You can now log in.';
        } else {
            $error = $result;
        }
    }
}
?>
<?php require 'views/partial/header.php'; ?>

<div class="auth-container">
    <div class="auth-card animate-fade-up">

        <span class="auth-logo">⚡ TaskFlow</span>

        <h2>Create account</h2>
        <p style="margin-bottom:1.75rem;">Join TaskFlow and start tracking your tasks.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">✔ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       placeholder="Choose a username (min 3 chars)" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="At least 6 characters" required>
            </div>
            <div class="form-group">
                <label for="password2">Confirm Password</label>
                <input type="password" id="password2" name="password2"
                       placeholder="Repeat your password" required>
            </div>
            <button type="submit" name="register" class="btn btn-primary" style="width:100%;padding:0.7rem;">
                Create Account →
            </button>
        </form>

        <p style="margin-top:1.25rem;text-align:center;margin-bottom:0;">
            Already have an account? <a href="index.php">Log in</a>
        </p>
    </div>
</div>

<?php require 'views/partial/footer.php'; ?>
