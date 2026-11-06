<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Campus Registry student registration system.">
    <title>Campus Registry | Student Registration System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php require __DIR__ . '/includes/header.php'; ?>
<main>
    <section class="hero-section py-4">
    <div class="container">
        <span class="badge hero-badge rounded-pill px-3 py-2 mb-3 fw-normal">الطلاب المشاركين</span>
        
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-light text-dark border p-2 fs-6">Mohammad_298438</span>
            <span class="badge bg-light text-dark border p-2 fs-6">Mohe_aldeen_292755</span>
            <span class="badge bg-light text-dark border p-2 fs-6">Bayan_296890</span>
            <span class="badge bg-light text-dark border p-2 fs-6">Ali_283555</span>
            <span class="badge bg-light text-dark border p-2 fs-6">Nadia_291909</span>
        </div>
    </div>
</section>
    <?php if (!is_logged_in()): ?>
        <section class="hero-section">
            <div class="container">
                <span class="badge hero-badge rounded-pill px-3 py-2 mb-3 fw-normal">Student records, organized</span>
                <h1 class="display-5 mb-3">A simple way to manage student registration.</h1>
                <p class="lead mb-4">Create an account to add student profiles, update contact information, and find records quickly from any device.</p>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="register.html" class="btn btn-primary px-4 py-2">Create an account <i class="bi bi-arrow-right ms-1"></i></a>
                    <a href="login.html" class="btn btn-outline-secondary px-4 py-2">I already have an account</a>
                </div>
            </div>
        </section>
    <?php else: ?>
            <section class="hero-section">
            <div class="container">
                <span class="badge hero-badge rounded-pill px-3 py-2 mb-3 fw-normal">Welcome! <?= e($_SESSION['username'] ?? 'Account') ?></span>
                <h1 class="display-5 mb-3">A simple way to manage student registration.</h1>
                <p class="lead mb-4">Create an account to add student profiles, update contact information, and find records quickly from any device.</p>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="view_students.php" class="btn btn-primary px-4 py-2">View Students Records <i class="bi bi-arrow-right ms-1"></i></a>
                    <a href="logout.php" class="btn btn-outline-secondary px-4 py-2">Logout</a>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <section class="container py-5" aria-labelledby="features-title">
        <div class="mb-4">
            <h2 id="features-title" class="h3 page-title">Everything needed for everyday records</h2>
            <p class="page-subtitle">Straightforward tools for university staff and student administrators.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <article class="feature-card">
                    <span class="feature-icon mb-3"><i class="bi bi-person-plus"></i></span>
                    <h3 class="h5">Register students</h3>
                    <p class="text-body-secondary mb-0">Save essential contact details and an optional profile picture with helpful form validation.</p>
                </article>
            </div>
            <div class="col-md-4">
                <article class="feature-card">
                    <span class="feature-icon mb-3"><i class="bi bi-search"></i></span>
                    <h3 class="h5">Find records quickly</h3>
                    <p class="text-body-secondary mb-0">Search by name, email, or phone number, then sort and browse results ten at a time.</p>
                </article>
            </div>
            <div class="col-md-4">
                <article class="feature-card">
                    <span class="feature-icon mb-3"><i class="bi bi-shield-check"></i></span>
                    <h3 class="h5">Keep access secure</h3>
                    <p class="text-body-secondary mb-0">Password hashing, protected sessions, and prepared database queries help keep information safe.</p>
                </article>
            </div>
        </div>
        <div class="content-card mt-5 p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h2 class="h5 mb-1">Already registered?</h2>
                <p class="text-body-secondary mb-0">Log in to view or update the student directory.</p>
            </div>
            <?php if(!is_logged_in()): ?>
                <a href="login.html" class="btn btn-primary flex-shrink-0">Open student records</a>
            <?php else: ?>
                <a href="view_students.php" class="btn btn-primary flex-shrink-0">Open student records</a>
            <?php endif; ?>
        </div>
    </section>
</main>
<footer class="border-top py-4">
    <div class="container d-flex flex-column flex-sm-row justify-content-between gap-2 text-body-secondary small">
        <span>Campus Registry — Student Registration System</span>
        <span>BWP501 Web Programming II</span>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
