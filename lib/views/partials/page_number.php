<?php

$display = "";

if (isset($_GET["display"])) {
    $display = "&display={$_GET["display"]}";
}

?>

<div class="page-number--container">
    <?php if ($currentPage !== 1): ?>
        <p class="page-number--next-prev"><a href="?page=<?= htmlspecialchars($currentPage - 1 . $display) ?>">Prev</a></p>
    <?php else: ?>
        <div class="page-number--next-prev"></div>
    <?php endif ?>

    <p class="page-number--number"><a href="?page=1<?= $display ?>">1</a></p>

    <?php for ($i = $pageStart; $i < $pageEnd + 1; $i++): ?>
        <?php if ($i > 1 && $i < $totalPages) : ?>
            <p class="page-number--number"><a href="?page=<?= htmlspecialchars($i . $display) ?>"><?= htmlspecialchars($i) ?></a></p>
        <?php endif ?>
    <?php endfor ?>

    <?php if ($totalPages > 1): ?>
        <p class="page-number--number"><a href="?page=<?= htmlspecialchars($totalPages . $display) ?>"><?= htmlspecialchars($totalPages) ?></a></p>
    <?php endif; ?>

    <?php if ($currentPage !== $totalPages): ?>
        <p class="page-number--next-prev"><a href="?page=<?= htmlspecialchars($currentPage + 1 . $display) ?>">Next</a></p>
    <?php else: ?>
        <div class="page-number--next-prev"></div>
    <?php endif ?>
</div>
