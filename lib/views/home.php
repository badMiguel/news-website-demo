<div class="top-news--spacing">
    <div class="main--spacing">
        <?php if ($latestNews === null): ?>
            <p>There are currently no news.</p>
        <?php else: ?>
            <div class="top-news--container">
                <h3>Just In</h3>
                <div class="top-news--card">
                    <h1 class="top-news--title"><a href="/news?id=<?= htmlspecialchars($latestNews["news_id"]) ?>"><?= htmlspecialchars($latestNews["news_title"]) ?></a></h1>
                    <div class="top-news--details">
                        <p class="top-news--subtitle"><?= htmlspecialchars($latestNews["news_subtitle"]) ?></p>
                        <div class="top-news--author-time">
                            <p class="top-news--author">By: <?= htmlspecialchars($latestNews["author"]) ?></p>
                            <?php $news = $latestNews ?>
                            <p class="top-news--time"><?php require VIEWS . "time_ago_display.php" ?></p>
                        </div>

                        <?php $newsDetails = $latestNews ?>
                        <?php require VIEWS . "category_display.php" ?>
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
                <div
                    class="news-category--card">
                    <h1 class="category-title"><?= htmlspecialchars($newsListKey) ?></h1>
                    <?php foreach ($newsList as $news): ?>
                        <h2 class="home-news--title">
                            <a href="/news?id=<?= htmlspecialchars($news["news_id"]) ?>">
                                <?= htmlspecialchars($news["news_title"]) ?>
                            </a>
                        </h2>
                        <p class="home-news--subtitle"><?= htmlspecialchars($news["news_subtitle"]) ?></p>
                        <p class="home-news--author">By: <?= htmlspecialchars($news["author"]) ?></p>
                        <p class="home-news--time"><?php require VIEWS . "time_ago_display.php" ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php $counter++ ?>
    <?php endif; ?>
<?php endforeach; ?>
