<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$errors = [];
$student = ['name' => '', 'email' => '', 'phone' => '', 'address' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $student = [
        'name' => trim((string) ($_POST['name'] ?? '')),
        'email' => trim((string) ($_POST['email'] ?? '')),
        'phone' => trim((string) ($_POST['phone'] ?? '')),
        'address' => trim((string) ($_POST['address'] ?? '')),
    ];
    $errors = validate_student_input($student);

    $picture = null;
    if (!$errors) {
        try {
            $picture = handle_profile_upload($_FILES['profile_picture'] ?? []);
        } catch (RuntimeException $exception) {
            $errors['profile_picture'] = $exception->getMessage();
        }
    }

    if (!$errors) {
        $statement = $pdo->prepare('INSERT INTO students (name, email, phone, address, profile_picture) VALUES (:name, :email, :phone, :address, :profile_picture)');
        $statement->execute([
            'name' => $student['name'],
            'email' => $student['email'],
            'phone' => $student['phone'],
            'address' => $student['address'],
            'profile_picture' => $picture,
        ]);
        flash('success', 'Student added successfully.');
        if (is_ajax()) {
            json_response(['success' => true, 'redirect' => 'view_students.php']);
        }
        redirect('view_students.php');
    }

    if (is_ajax()) {
        json_response(['success' => false, 'message' => 'Please correct the highlighted fields.', 'errors' => $errors], 422);
    }
}

$pageTitle = 'Add Student';
$activePage = 'add';
$pageScripts = ['assets/js/validation.js'];
require __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="page-header">
            <a href="view_students.php" class="text-primary small"><i class="bi bi-arrow-left me-1"></i>Back to students</a>
            <h1 class="page-title h2 mt-2 mb-1">Add a new student</h1>
            <p class="page-subtitle">Enter the student's contact details. All fields except the picture are required.</p>
        </div>
        <div class="form-card">
            <form id="studentForm" action="add_student.php" method="post" enctype="multipart/form-data" novalidate>
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="name" class="form-label">Full name <span class="required">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" maxlength="100" value="<?= e($student['name']) ?>" required autofocus>
                        <div id="nameError" class="invalid-feedback"><?= e($errors['name'] ?? '') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email address <span class="required">*</span></label>
                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" maxlength="100" value="<?= e($student['email']) ?>" required>
                        <div id="emailError" class="invalid-feedback"><?= e($errors['email'] ?? '') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone number <span class="required">*</span></label>
                        <input type="tel" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" id="phone" name="phone" maxlength="15" value="<?= e($student['phone']) ?>" required>
                        <div id="phoneError" class="invalid-feedback"><?= e($errors['phone'] ?? '') ?></div>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="address" class="form-label">Address <span class="required">*</span></label>
                        <textarea class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" id="address" name="address" rows="3" required><?= e($student['address']) ?></textarea>
                        <div id="addressError" class="invalid-feedback"><?= e($errors['address'] ?? '') ?></div>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="profile_picture" class="form-label">Profile picture <span class="text-body-secondary fw-normal">(optional)</span></label>
                        <input type="file" class="form-control <?= isset($errors['profile_picture']) ? 'is-invalid' : '' ?>" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/gif,image/webp" onchange="showPreview(event)">
                        <div class="invalid-feedback"><?= e($errors['profile_picture'] ?? '') ?></div>
                        <div class="form-text">JPG, PNG, GIF, or WebP. Maximum size: 2 MB.</div>
                        <img id="previewImage" src="uploads/students/default-avatar.jpg" alt="Profile Preview" width="150" height="150" style="border-radius: 50%; object-fit: cover;">
                    </div>
                </div>
                <div id="formStatus" role="alert"></div>
                <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-3">
                    <a href="view_students.php" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" id="submitButton" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Add student</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
