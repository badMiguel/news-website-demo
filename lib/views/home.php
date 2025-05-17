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
                        <p><?= htmlspecialchars($latestNews["news_subtitle"]) ?></p>
                        <br>
                        <?php if ($latestNews["author"]): ?>
                            <p>Author: <?= htmlspecialchars($latestNews["author"]) ?></p>
                        <?php endif; ?>
                        <div class="category--container">
                            <p>Category:</p>
                            <?php foreach ($latestNews["category"] as $category): ?>
                                <p><?= $category ?></p>
                            <?php endforeach; ?>
                        </div>
                        <?php $news = $latestNews ?>
                        <?php require VIEWS . "time_ago_display.php" ?>
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
                        <h2 class="news-title">
                            <a href="/news?id=<?= htmlspecialchars($news["news_id"]) ?>">
                                <?= htmlspecialchars($news["news_title"]) ?>
                            </a>
                        </h2>
                        <p class="news-subtitle"><?= htmlspecialchars($news["news_subtitle"]) ?></p>
                        <?php if ($news["author"]): ?>
                            <p>Author: <?= htmlspecialchars($news["author"]) ?></p>
                        <?php endif; ?>

                        <?php require VIEWS . "time_ago_display.php" ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php $counter++ ?>
    <?php endif; ?>
<?php endforeach; ?>
