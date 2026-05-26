<?php
// index.php — Login page
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Already logged in → go to dashboard
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
            $error = $result; // error message string
        }
    }
}
?>
<?php require 'views/partial/header.php'; ?>

<div class="auth-container flex-center" style="min-height:80vh;">
    <div class="card auth-card p-2">
        <h2 style="margin-bottom:0.25rem;">Welcome back 👋</h2>
        <p style="margin-bottom:1.5rem;color:#64748b;">Log in to your TaskFlow account</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       placeholder="Enter username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Enter password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary" style="width:100%;">
                Log In
            </button>
        </form>

        <p style="margin-top:1rem;text-align:center;color:#64748b;">
            Don't have an account?
            <a href="/register.php">Register here</a>
        </p>
    </div>
</div>

<?php require 'views/partial/footer.php'; ?>
