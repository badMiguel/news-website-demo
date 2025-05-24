<?php
if (count($currNewsList) < 1) {
    echo "<p>There are currently no news.</p>";
    exit;
}
?>

<?php require VIEWS . "partials/news_amount.php" ?>

<?php foreach ($currNewsList as $news): ?>
    <div class='news--card-container'>
        <div class="news--card">
            <?php if (
                htmlspecialchars($news["image_path"]) &&
                file_exists(IMAGE_DIR . $news["image_path"])
            ): ?>
                <div class="news--image-container">
                    <a href="/news?id=<?= htmlspecialchars($news["news_id"]) ?>">
                        <img class="news--image" src="/../images/<?= htmlspecialchars($news["image_path"]) ?>" />
                    </a>
                </div>
            <?php endif ?>
            <div
                class="news--details-container"
                <?php if (
                    htmlspecialchars($news["image_path"]) &&
                    file_exists(IMAGE_DIR . $news["image_path"])
                ): ?>
                style="width: 75%;"
                <?php else: ?>
                style="width: 100%;"
                <?php endif ?>>
                <h2 class="news--details-title">
                    <a href="/news?id=<?= htmlspecialchars($news["news_id"]) ?>">
                        <?= htmlspecialchars($news["news_title"]) ?>
                    </a>
                </h2>
                <p class="news--details-subtitle"><?= htmlspecialchars($news["news_subtitle"]) ?></p>

                <p class="news--author-time">
                    <?php require VIEWS . "partials/time_ago_display.php" ?>
                    <span style="margin: 0 0.3rem;">|</span>
                    <?= htmlspecialchars($news["author"]) ?>
                </p>

                <?php $newsDetails = $news ?>
                <?php require VIEWS . "partials/category_display.php" ?>
            </div>
        </div>
    </div>
    <hr>
<?php endforeach; ?>

<?php require VIEWS . "partials/page_number.php" ?>
