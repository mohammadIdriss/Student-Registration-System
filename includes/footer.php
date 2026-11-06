</main>
<footer class="border-top py-4 mt-auto">
    <div class="container d-flex flex-column flex-sm-row justify-content-between gap-2 text-body-secondary small">
        <span>Campus Registry — Student Registration System</span>
        <span>BWP501 Web Programming II</span>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
<?php if (!empty($pageScripts)): ?>
    <?php foreach ($pageScripts as $script): ?>
        <script src="<?= e($script) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
