<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

if (!is_logged_in()) {
    json_response(['success' => false, 'message' => 'Please log in again.'], 401);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}
verify_csrf();
json_response(['success' => true, 'errors' => validate_student_input($_POST)]);
