<?php if ($latestNews === null): ?>
    <p>There are currently no news.</p>
<?php else: ?>
    <h2>Just In</h2>
    <div class="top-news--container">
        <h1 class="top-news--title"><a href="/news?id=<?= htmlspecialchars($latestNews[0]["news_id"]) ?>"><?= htmlspecialchars($latestNews[0]["news_title"]) ?></a></h1>
        <div class="top-news--details">
            <p><?= htmlspecialchars($latestNews[0]["news_subtitle"]) ?></p>
            <br>
            <?php if ($latestNews[0]["author"]): ?>
                <p>Author: <?= htmlspecialchars($latestNews[0]["author"]) ?></p>
            <?php endif; ?>
            <div class="category--container">
                <p>Category:</p>
                <?php foreach ($latestNews[0]["category"] as $category): ?>
                    <p><?= $category ?></p>
                <?php endforeach; ?>
            </div>
            <p>Created: <em><?= htmlspecialchars($latestNews[0]["created_date"]) ?></em></p>
            <p>Edited: <em><?= htmlspecialchars($latestNews[0]["edited_date"]) ?></em></p>
        </div>
    </div>
<?php endif ?>

<?php if (count($currNewsList) > 1): ?>
    <?php for ($i = 1; $i < count($currNewsList); $i++): ?>
        <div class='news-card'>
            <h2>
                <a href="/news?id=<?= htmlspecialchars($currNewsList[$i]["news_id"]) ?>">
                    <?= htmlspecialchars($currNewsList[$i]["news_title"]) ?>
                </a>
            </h2>
            <p><?= htmlspecialchars($currNewsList[$i]["news_subtitle"]) ?></p>
            <br>
            <?php if ($currNewsList[$i]["author"]): ?>
                <p>Author: <?= htmlspecialchars($currNewsList[$i]["author"]) ?></p>
            <?php endif; ?>
            <div class="category--container">
                <p>Category:</p>
                <?php foreach ($currNewsList[$i]["category"] as $category): ?>
                    <p><?= $category ?></p>
                <?php endforeach; ?>
            </div>
            <p>Created: <em><?= htmlspecialchars($currNewsList[$i]["created_date"]) ?></em></p>
            <p>Edited: <em><?= htmlspecialchars($currNewsList[$i]["edited_date"]) ?></em></p>
        </div>
        <hr>
    <?php endfor; ?>
<?php endif; ?>
