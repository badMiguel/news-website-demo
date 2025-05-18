<?php
if (count($currNewsList) < 1) {
    echo "<p>There are currently no news.</p>";
    exit;
}
?>

<?php require VIEWS . "news_amount.php" ?>

<?php foreach ($currNewsList as $news): ?>
    <div class='news--card'>
        <h2>
            <a href="/news?id=<?= htmlspecialchars($news["news_id"]) ?>">
                <?= htmlspecialchars($news["news_title"]) ?>
            </a>
        </h2>
        <p><?= htmlspecialchars($news["news_subtitle"]) ?></p>

        <div class="news--author-time">
            <p>By: <?= htmlspecialchars($news["author"]) ?></p>
            <p><?php require VIEWS . "time_ago_display.php" ?></p>
        </div>

        <div class="category--container">
            <?php foreach ($news["category"] as $category): ?>
                <p class="category--name"><?= $category ?></p>
            <?php endforeach; ?>
        </div>

    </div>
    <hr>
<?php endforeach; ?>

<?php require VIEWS . "page_number.php" ?>
