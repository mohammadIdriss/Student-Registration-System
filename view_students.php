<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$search = trim((string) ($_GET['search'] ?? ''));
$allowedSorts = ['name', 'email', 'phone', 'created_at'];
$sort = in_array($_GET['sort'] ?? '', $allowedSorts, true) ? $_GET['sort'] : 'created_at';
$order = strtolower((string) ($_GET['order'] ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;

$where = '';
$params = [];
if ($search !== '') {
    $where = 'WHERE name LIKE :search_name OR email LIKE :search_email OR phone LIKE :search_phone';
    $params = [
        'search_name' => '%' . $search . '%',
        'search_email' => '%' . $search . '%',
        'search_phone' => '%' . $search . '%',
    ];
}

$count = $pdo->prepare("SELECT COUNT(*) FROM students {$where}");
$count->execute($params);
$totalRecords = (int) $count->fetchColumn();
$totalPages = max(1, (int) ceil($totalRecords / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$query = $pdo->prepare("SELECT id, name, email, phone, address, profile_picture, created_at FROM students {$where} ORDER BY {$sort} {$order} LIMIT :limit OFFSET :offset");
foreach ($params as $key => $value) {
    $query->bindValue(':' . $key, $value, PDO::PARAM_STR);
}
$query->bindValue(':limit', $perPage, PDO::PARAM_INT);
$query->bindValue(':offset', $offset, PDO::PARAM_INT);
$query->execute();
$students = $query->fetchAll();

function students_url(array $changes = []): string
{
    global $search, $sort, $order, $page;
    $parameters = array_merge(['search' => $search, 'sort' => $sort, 'order' => strtolower($order), 'page' => $page], $changes);
    return 'view_students.php?' . http_build_query($parameters);
}

$pageTitle = 'Student Records';
$activePage = 'students';
require __DIR__ . '/includes/header.php';
?>
<div class="page-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
    <div>
        <h1 class="page-title h2 mb-1">Student records</h1>
        <p class="page-subtitle"><?= $totalRecords ?> <?= $totalRecords === 1 ? 'student' : 'students' ?><?= $search !== '' ? ' matching your search' : ' in the directory' ?>.</p>
    </div>
    <a href="add_student.php" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Add student</a>
</div>
<div class="content-card">
    <div class="search-panel">
        <form method="get" action="view_students.php" role="search">
            <input type="hidden" name="sort" value="<?= e($sort) ?>">
            <input type="hidden" name="order" value="<?= e(strtolower($order)) ?>">
            <label for="search" class="form-label">Search the directory</label>
            <div class="input-group search-input">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="search" class="form-control" id="search" name="search" maxlength="100" value="<?= e($search) ?>" placeholder="Name, email, or phone number">
                <button type="submit" class="btn btn-primary">Search</button>
                <?php if ($search !== ''): ?>
                    <a href="view_students.php" class="btn btn-outline-secondary" aria-label="Clear search"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <?php if (!$students): ?>
        <div class="empty-state">
            <i class="bi bi-people d-block mb-3"></i>
            <h2 class="h5"><?= $search !== '' ? 'No matching students' : 'No students yet' ?></h2>
            <p class="text-body-secondary"><?= $search !== '' ? 'Try a different name, email, or phone number.' : 'Add your first student to start building the directory.' ?></p>
            <?php if ($search !== ''): ?>
                <a href="view_students.php" class="btn btn-outline-secondary">Clear search</a>
            <?php else: ?>
                <a href="add_student.php" class="btn btn-primary">Add first student</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th><a class="table-sort" href="<?= e(students_url(['sort' => 'name', 'order' => $sort === 'name' && $order === 'ASC' ? 'desc' : 'asc', 'page' => 1])) ?>">Student <i class="bi <?= $sort === 'name' ? ($order === 'ASC' ? 'bi-sort-alpha-down' : 'bi-sort-alpha-up') : 'bi-arrow-down-up' ?>"></i></a></th>
                    <th><a class="table-sort" href="<?= e(students_url(['sort' => 'email', 'order' => $sort === 'email' && $order === 'ASC' ? 'desc' : 'asc', 'page' => 1])) ?>">Email <i class="bi bi-arrow-down-up"></i></a></th>
                    <th><a class="table-sort" href="<?= e(students_url(['sort' => 'phone', 'order' => $sort === 'phone' && $order === 'ASC' ? 'desc' : 'asc', 'page' => 1])) ?>">Phone <i class="bi bi-arrow-down-up"></i></a></th>
                    <th>Address</th>
                    <th><a class="table-sort" href="<?= e(students_url(['sort' => 'created_at', 'order' => $sort === 'created_at' && $order === 'ASC' ? 'desc' : 'asc', 'page' => 1])) ?>">Registered <i class="bi bi-arrow-down-up"></i></a></th>
                    <th><span class="visually-hidden">Actions</span></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($students as $student): ?>
                    <?php
                    $nameParts = preg_split('/\s+/', trim($student['name']));
                    $initials = mb_strtoupper(mb_substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? mb_substr(end($nameParts), 0, 1) : ''));
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if ($student['profile_picture']): ?>
                                    <img class="student-avatar" src="<?= e($student['profile_picture']) ?>" alt="<?= e($student['name']) ?> profile picture">
                                <?php else: ?>
                                    <span class="student-avatar avatar-initials" aria-hidden="true"><?= e($initials) ?></span>
                                <?php endif; ?>
                                <div><span class="student-name"><?= e($student['name']) ?></span><div class="student-meta">ID #<?= (int) $student['id'] ?></div></div>
                            </div>
                        </td>
                        <td><a href="mailto:<?= e($student['email']) ?>" class="text-body-secondary"><?= e($student['email']) ?></a></td>
                        <td class="text-nowrap"><?= e($student['phone']) ?></td>
                        <td><span title="<?= e($student['address']) ?>"><?= e(mb_strlen($student['address']) > 35 ? mb_substr($student['address'], 0, 35) . '…' : $student['address']) ?></span></td>
                        <td class="text-nowrap text-body-secondary"><?= e(date('M j, Y', strtotime($student['created_at']))) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="update_student.php?id=<?= (int) $student['id'] ?>" class="btn btn-sm btn-outline-primary" aria-label="Edit <?= e($student['name']) ?>"><i class="bi bi-pencil"></i></a>
                                <form method="post" action="delete_student.php" data-confirm="Delete <?= e($student['name']) ?>? This action cannot be undone.">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $student['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Delete <?= e($student['name']) ?>"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="pagination-panel d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <span class="small text-body-secondary">Showing <?= $offset + 1 ?>–<?= min($offset + $perPage, $totalRecords) ?> of <?= $totalRecords ?></span>
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Student list pages">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= e(students_url(['page' => max(1, $page - 1)])) ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a></li>
                        <?php
                        $startPage = max(1, min($page - 2, $totalPages - 4));
                        $endPage = min($totalPages, $startPage + 4);
                        for ($number = $startPage; $number <= $endPage; $number++):
                        ?>
                            <li class="page-item <?= $number === $page ? 'active' : '' ?>"><a class="page-link" href="<?= e(students_url(['page' => $number])) ?>" <?= $number === $page ? 'aria-current="page"' : '' ?>><?= $number ?></a></li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>"><a class="page-link" href="<?= e(students_url(['page' => min($totalPages, $page + 1)])) ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a></li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
