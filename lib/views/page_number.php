<?php if ($currentPage !== 1): ?>
    <p><a href="?page=<?= $currentPage - 1 ?>">Prev</a></p>
<?php endif ?>
<p><a href="?page=1">1</a></p>

<?php for ($i = $pageStart; $i < $pageEnd + 1; $i++): ?>
    <?php if ($i > 1 && $i < $totalPages) : ?>
        <p><a href="?page=<?= $i ?>"><?= $i ?></a></p>
    <?php endif ?>
<?php endfor ?>

<?php if ($totalPages > 1): ?>
    <p><a href="?page=<?= $totalPages ?>"><?= $totalPages ?></a></p>
<?php endif; ?>

<?php if ($currentPage !== $totalPages): ?>
    <p><a href="?page=<?= $currentPage + 1 ?>">Next</a></p>
<?php endif ?>
