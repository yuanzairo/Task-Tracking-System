<?php
// helpers/auth.php
// Include this at the top of any page that requires a logged-in user.

if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit();
}
