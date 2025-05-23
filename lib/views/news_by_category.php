<?php
if (count($currNewsList) < 1) {
    echo "<p>There are currently no news.</p>";
    exit;
}
?>

<?php require VIEWS . "news_amount.php" ?>

<?php foreach ($currNewsList as $news): ?>
    <div class='news--card-container'>
        <div class="news--card">
            <div class="news--image-container">
                <img class="news--image" src="/../images/<?= htmlspecialchars($news["image_path"]) ?>" />
            </div>
            <div class="news--details-container">
                <h2 class="news--details-title">
                    <a href="/news?id=<?= htmlspecialchars($news["news_id"]) ?>">
                        <?= htmlspecialchars($news["news_title"]) ?>
                    </a>
                </h2>
                <p class="news--details-subtitle"><?= htmlspecialchars($news["news_subtitle"]) ?></p>

                <p class="news--author-time">
                    <?= htmlspecialchars($news["author"]) ?>
                    <span style="margin: 0 0.3rem;">|</span>
                    <?php require VIEWS . "time_ago_display.php" ?>
                </p>

                <?php $newsDetails = $news ?>
                <?php require VIEWS . "category_display.php" ?>
            </div>
        </div>
    </div>
    <hr>
<?php endforeach; ?>

<?php require VIEWS . "page_number.php" ?>
