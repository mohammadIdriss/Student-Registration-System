<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

if (is_logged_in()) {
    redirect('view_students.php');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.html');
}

$identity = trim((string) ($_POST['identity'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($identity === '' || $password === '') {
    redirect('login.html?error=' . urlencode('Enter your username/email and password.'));
}

$statement = $pdo->prepare('SELECT id, username, password FROM users WHERE username = :username OR email = :email LIMIT 1');
$statement->execute(['username' => $identity, 'email' => $identity]);
$user = $statement->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    redirect('login.html?error=' . urlencode('Invalid login details. Please check your credentials and try again.'));
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

redirect('view_students.php');
