<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('view_students.php');
}
verify_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    flash('danger', 'Invalid student record.');
    redirect('view_students.php');
}

$statement = $pdo->prepare('SELECT profile_picture FROM students WHERE id = :id');
$statement->execute(['id' => $id]);
$student = $statement->fetch();

if (!$student) {
    flash('warning', 'That student record no longer exists.');
    redirect('view_students.php');
}

$delete = $pdo->prepare('DELETE FROM students WHERE id = :id');
$delete->execute(['id' => $id]);
delete_profile_picture($student['profile_picture']);
flash('success', 'Student record deleted successfully.');
redirect('view_students.php');
