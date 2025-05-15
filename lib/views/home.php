<?php if (count($currNewsList) < 1): ?>
    <p>There are currently no news.</p>
<?php elseif ($startCount === 1): ?>
    <div class="top-news--container">
        <h1 class="top-news--title"><a href="/news?id=<?= htmlspecialchars($currNewsList[0]["news_id"]) ?>"><?= htmlspecialchars($currNewsList[0]["news_title"]) ?></a></h1>
        <div class="top-news--details">
            <p><?= htmlspecialchars($currNewsList[0]["news_subtitle"]) ?></p>
            <br>
            <?php if ($currNewsList[0]["author"]): ?>
                <p>Author: <?= htmlspecialchars($currNewsList[0]["author"]) ?></p>
            <?php endif; ?>
            <div class="category--container">
                <p>Category:</p>
                <?php foreach ($currNewsList[0]["category"] as $c): ?>
                    <p><?= $c["category"] ?></p>
                <?php endforeach; ?>
            </div>
            <p>Created: <em><?= htmlspecialchars($currNewsList[0]["created_date"]) ?></em></p>
            <p>Edited: <em><?= htmlspecialchars($currNewsList[0]["edited_date"]) ?></em></p>
        </div>
    </div>
<?php endif ?>

<?php require VIEWS . "news_amount.php" ?>

<?php if (count($currNewsList) > 1): ?>
    <?php for ($i = $startCount; $i < count($currNewsList); $i++): ?>
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
                <?php foreach ($currNewsList[$i]["category"] as $c): ?>
                    <p><?= $c["category"] ?></p>
                <?php endforeach; ?>
            </div>
            <p>Created: <em><?= htmlspecialchars($currNewsList[$i]["created_date"]) ?></em></p>
            <p>Edited: <em><?= htmlspecialchars($currNewsList[$i]["edited_date"]) ?></em></p>
        </div>
        <hr>
    <?php endfor; ?>
<?php endif; ?>

<?php require VIEWS . "page_number.php" ?>
