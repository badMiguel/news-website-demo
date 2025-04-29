<p>1</p>

<?php for ($i = $pageStart; $i < $pageEnd + 1; $i++): ?>
    <?php if ($i > 1 && $i < $totalPages) : ?>
        <p><?= $i ?></p>
    <?php endif ?>
<?php endfor ?>

<?php if ($totalPages > 1): ?>
    <p><?= $totalPages ?></p>
<?php endif; ?>
