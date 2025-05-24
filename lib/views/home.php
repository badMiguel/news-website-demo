<div class="top-news--spacing">
    <div class="main--spacing">
        <?php if ($latestNews === null): ?>
            <p>There are currently no news.</p>
        <?php else: ?>
            <div class="top-news--container">
                <h3>Just In</h3>
                <div class="top-news--card">
                    <?php if (
                        htmlspecialchars($latestNews["image_path"]) &&
                        file_exists(IMAGE_DIR . $latestNews["image_path"])
                    ): ?>
                        <a class="top-news--image-link" href="/news?id=<?= htmlspecialchars($latestNews["news_id"]) ?>">
                            <img class="top-news--image" src="/../images/<?= htmlspecialchars($latestNews["image_path"]) ?>" />
                        </a>
                    <?php endif ?>
                    <div class="top-news--details"
                        <?php if (
                            htmlspecialchars($latestNews["image_path"]) &&
                            file_exists(IMAGE_DIR . $latestNews["image_path"])
                        ): ?>
                        style="width: 60%;"
                        <?php else: ?>
                        style="width: 100%;"
                        <?php endif ?>>
                        <h1 class="top-news--title">
                            <a href="/news?id=<?= htmlspecialchars($latestNews["news_id"]) ?>">
                                <?= htmlspecialchars($latestNews["news_title"]) ?>
                            </a>
                        </h1>
                        <p class="top-news--subtitle"><?= htmlspecialchars($latestNews["news_subtitle"]) ?></p>
                        <p class="top-news--author-time">
                            <?php $news = $latestNews ?>
                            <?php require VIEWS . "partials/time_ago_display.php" ?>
                            <span style="margin: 0 0.3rem;">|</span>
                            <?= htmlspecialchars($latestNews["author"]) ?>
                        </p>

                        <?php $newsDetails = $latestNews ?>
                        <?php require VIEWS . "partials/category_display.php" ?>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>

<?php $counter = 0 ?>

<?php foreach ($recentNewsPerCategory as $newsListKey => $newsList): ?>
    <?php if (count($newsList) > 1): ?>
        <div
            class="news-category--container"
            <?php if ($counter % 2 === 1): ?>
            style="background-color: #dadce8;"
            <?php endif ?>>
            <div class="main--spacing">
                <h1 class="category-title">
                    <a href="/<?= htmlspecialchars(lcfirst($newsListKey)) ?>">
                        <?= htmlspecialchars($newsListKey) ?>
                    </a>
                </h1>
                <div class="news-category--card-container">
                    <?php foreach ($newsList as $news): ?>
                        <div class="news-category--card">
                            <?php if (
                                htmlspecialchars($news["image_path"]) &&
                                file_exists(IMAGE_DIR . $news["image_path"])
                            ): ?>
                                <a href="/news?id=<?= htmlspecialchars($news["news_id"]) ?>">
                                    <img style="width: 100%; max-height: 10rem; object-fit: cover; "
                                        src="/../images/<?= htmlspecialchars($news["image_path"]) ?>" />
                                </a>
                            <?php endif ?>
                            <h2 class="home-news--title">
                                <a href="/news?id=<?= htmlspecialchars($news["news_id"]) ?>">
                                    <?= htmlspecialchars($news["news_title"]) ?>
                                </a>
                            </h2>

                            <?php if (
                                !htmlspecialchars($news["image_path"]) ||
                                !file_exists(IMAGE_DIR . $news["image_path"])
                            ): ?>
                                <p><?= htmlspecialchars($news["news_subtitle"]) ?></p>
                            <?php endif ?>

                            <p class="home-news--time-author">
                                <?php require VIEWS . "partials/time_ago_display.php" ?>
                                <span style="margin: 0 0.3rem;">|</span>
                                <?= htmlspecialchars($news["author"]) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="home-news--view-more">
                    <a href="/<?= htmlspecialchars(lcfirst($newsListKey)) ?>">
                        View More
                    </a>
                </p>
            </div>
        </div>
        <?php $counter++ ?>
    <?php endif; ?>
<?php endforeach; ?>
