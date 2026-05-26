<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: /views/dashboard/index.php');
    exit();
}

require_once __DIR__ . '/models/account.php';
require_once __DIR__ . '/controllers/account.php';
require_once __DIR__ . '/public/database.config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $controller = new AccountController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
        $result     = $controller->login($username, $password);

        if ($result === true) {
            header('Location: /views/dashboard/index.php');
            exit();
        } else {
            $error = $result;
        }
    }
}
?>
<?php require 'views/partial/header.php'; ?>

<div class="auth-container">
    <div class="auth-card animate-fade-up">

        <span class="auth-logo">TaskFlow</span>

        <h2>Welcome back</h2>
        <p style="margin-bottom:1.75rem;">Log in to your account to continue.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Enter your password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary" style="width:100%;padding:0.7rem;">
                Log In →
            </button>
        </form>

        <p style="margin-top:1.25rem;text-align:center;margin-bottom:0;">
            No account yet? <a href="register.php">Register here</a>
        </p>
    </div>
</div>

<?php require 'views/partial/footer.php'; ?>
