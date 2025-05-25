<?php

$page = "";

if (isset($_GET["page"])) {
    $page = "?page={$_GET["page"]}";
}

?>

<div class="news-amount--container">
    <p>Show by:</p>

    <?php for ($i = 5; $i < 25; $i += 5) : ?>
        <?php if ($page !== ""): ?>
            <p><a href="<?= htmlspecialchars($page) ?>&display=<?= htmlspecialchars($i) ?>"><?= htmlspecialchars($i) ?></a></p>
        <?php else: ?>
            <p><a href="?display=<?= htmlspecialchars($i) ?>"><?= htmlspecialchars($i) ?></a></p>
        <?php endif ?>
    <?php endfor ?>
</div>
