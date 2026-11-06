<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    flash('danger', 'Invalid student record.');
    redirect('view_students.php');
}

$statement = $pdo->prepare('SELECT * FROM students WHERE id = :id');
$statement->execute(['id' => $id]);
$student = $statement->fetch();
if (!$student) {
    flash('warning', 'The requested student record was not found.');
    redirect('view_students.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $student['name'] = trim((string) ($_POST['name'] ?? ''));
    $student['email'] = trim((string) ($_POST['email'] ?? ''));
    $student['phone'] = trim((string) ($_POST['phone'] ?? ''));
    $student['address'] = trim((string) ($_POST['address'] ?? ''));
    $errors = validate_student_input($student);
    $newPicture = null;

    if (!$errors) {
        try {
            $newPicture = handle_profile_upload($_FILES['profile_picture'] ?? []);
        } catch (RuntimeException $exception) {
            $errors['profile_picture'] = $exception->getMessage();
        }
    }

    if (!$errors) {
        $oldPicture = $student['profile_picture'];
        $picture = $newPicture ?? $oldPicture;
        $update = $pdo->prepare('UPDATE students SET name = :name, email = :email, phone = :phone, address = :address, profile_picture = :profile_picture WHERE id = :id');
        $update->execute([
            'name' => $student['name'],
            'email' => $student['email'],
            'phone' => $student['phone'],
            'address' => $student['address'],
            'profile_picture' => $picture,
            'id' => $id,
        ]);
        if ($newPicture) {
            delete_profile_picture($oldPicture);
        }
        flash('success', 'Student record updated successfully.');
        redirect('view_students.php');
    }
}

$pageTitle = 'Update Student';
$activePage = 'students';
require __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="page-header">
            <a href="view_students.php" class="text-primary small"><i class="bi bi-arrow-left me-1"></i>Back to students</a>
            <h1 class="page-title h2 mt-2 mb-1">Update student record</h1>
            <p class="page-subtitle">Record #<?= (int) $id ?> · Joined <?= e(date('M j, Y', strtotime($student['created_at']))) ?></p>
        </div>
        <div class="form-card">
            <form action="update_student.php?id=<?= (int) $id ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="name" class="form-label">Full name <span class="required">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" maxlength="100" value="<?= e($student['name']) ?>" required>
                        <div class="invalid-feedback"><?= e($errors['name'] ?? '') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email address <span class="required">*</span></label>
                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" maxlength="100" value="<?= e($student['email']) ?>" required>
                        <div class="invalid-feedback"><?= e($errors['email'] ?? '') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone number <span class="required">*</span></label>
                        <input type="tel" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" id="phone" name="phone" maxlength="15" value="<?= e($student['phone']) ?>" required>
                        <div class="invalid-feedback"><?= e($errors['phone'] ?? '') ?></div>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="address" class="form-label">Address <span class="required">*</span></label>
                        <textarea class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" id="address" name="address" rows="3" required><?= e($student['address']) ?></textarea>
                        <div class="invalid-feedback"><?= e($errors['address'] ?? '') ?></div>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="profile_picture" class="form-label">Profile picture</label>
                        <?php if ($student['profile_picture']): ?>
                            <div class="mb-2"><img src="<?= e($student['profile_picture']) ?>" alt="Current profile" class="upload-preview"></div>
                            <div class="form-text mb-2">Leave this empty to keep the current picture, or choose a replacement.</div>
                        <?php endif; ?>
                        <input type="file" class="form-control <?= isset($errors['profile_picture']) ? 'is-invalid' : '' ?>" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/gif,image/webp" onchange="showPreview(event)">
                        <div class="invalid-feedback"><?= e($errors['profile_picture'] ?? '') ?></div>
                        <div class="form-text">JPG, PNG, GIF, or WebP. Maximum size: 2 MB.</div>
                        <img id="previewImage" src="uploads/students/default-avatar.jpg" alt="Profile Preview" width="150" height="150" style="border-radius: 50%; object-fit: cover;">

                    </div>
                </div>
                <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2">
                    <a href="view_students.php" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
