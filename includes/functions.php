<?php
declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login.html?error=' . urlencode('Please log in to manage student records.'));
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function is_ajax(): bool
{
    return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
}

function json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        if (is_ajax()) {
            json_response(['success' => false, 'message' => 'Your session expired. Refresh and try again.'], 419);
        }
        http_response_code(419);
        exit('Invalid security token. Please go back, refresh, and try again.');
    }
}

function validate_student_input(array $data): array
{
    $errors = [];
    $name = trim((string) ($data['name'] ?? ''));
    $email = trim((string) ($data['email'] ?? ''));
    $phone = trim((string) ($data['phone'] ?? ''));
    $address = trim((string) ($data['address'] ?? ''));

    if ($name === '') {
        $errors['name'] = 'Full name is required.';
    } elseif (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        $errors['name'] = 'Name must be between 2 and 100 characters.';
    }

    if ($email === '') {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $errors['email'] = 'Enter a valid email address (maximum 100 characters).';
    }

    if ($phone === '') {
        $errors['phone'] = 'Phone number is required.';
    } elseif (!preg_match('/^[0-9+()\-\s]{7,15}$/', $phone)) {
        $errors['phone'] = 'Use 7–15 digits; spaces, +, parentheses, and hyphens are allowed.';
    }

    if ($address === '') {
        $errors['address'] = 'Address is required.';
    } elseif (mb_strlen($address) < 5) {
        $errors['address'] = 'Please enter a complete address.';
    }

    return $errors;
}

function handle_profile_upload(array $file): ?string
{
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($error === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The image could not be uploaded. Please try again.');
    }
    if (!isset($file['tmp_name'], $file['size']) || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Invalid file upload.');
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('Profile picture must be 2 MB or smaller.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime]) || getimagesize($file['tmp_name']) === false) {
        throw new RuntimeException('Only JPG, PNG, GIF, or WebP images are allowed.');
    }

    $directory = __DIR__ . '/../uploads/students/';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('The upload folder could not be created.');
    }

    $filename = 'student_' . bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    $destination = $directory . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('The image could not be saved. Check folder permissions.');
    }

    return 'uploads/students/' . $filename;
}

function delete_profile_picture(?string $path): void
{
    if (!$path || !str_starts_with($path, 'uploads/students/')) {
        return;
    }
    $fullPath = __DIR__ . '/../' . $path;
    if (is_file($fullPath)) {
        unlink($fullPath);
    }
}
