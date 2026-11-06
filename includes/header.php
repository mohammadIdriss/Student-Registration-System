<?php
$pageTitle = $pageTitle ?? 'Campus Registry';
$activePage = $activePage ?? '';
$flash = get_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A simple student registration and records management system.">
    <title><?= e($pageTitle) ?> | Campus Registry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-mortarboard-fill me-2"></i>Campus Registry</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <?php if (is_logged_in()): ?>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'students' ? 'active' : '' ?>" href="view_students.php">Students</a></li>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'add' ? 'active' : '' ?>" href="add_student.php">Add student</a></li>
                    <li class="nav-item dropdown ms-lg-3">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i><?= e($_SESSION['username'] ?? 'Account') ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text small text-body-secondary">Signed in</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="logout.php" method="post">
                                    <?= csrf_field() ?>
                                    <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Log out</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.html">Login</a></li>
                    <li class="nav-item ms-lg-2"><a class="btn btn-light btn-sm" href="register.html">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4 py-lg-5">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
