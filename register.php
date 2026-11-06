<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

if (is_logged_in()) {
    redirect('view_students.php');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register.html');
}

$username = trim((string) ($_POST['username'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$confirmation = (string) ($_POST['confirm_password'] ?? '');

$errors = [];
if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
    $errors[] = 'Username must be 3–50 letters, numbers, or underscores.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    $errors[] = 'Enter a valid email address.';
}
if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $errors[] = 'Password must have at least 8 characters, including a letter and a number.';
}
if ($password !== $confirmation) {
    $errors[] = 'The password confirmation does not match.';
}
if ($errors) {
    redirect('register.html?error=' . urlencode(implode(' ', $errors)));
}

try {
    $statement = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
    $statement->execute([
        'username' => $username,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        redirect('register.html?error=' . urlencode('That username or email is already registered.'));
    }
    throw $exception;
}

redirect('login.html?registered=1');
