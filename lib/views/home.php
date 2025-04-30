<h1>this is home</h1>
<h3><a href="/news/create">create news</a></h3>
<?php foreach ($currNewsList as $news): ?>
    <div class='news-card'>
        <h2><a href="/news?id=<?= $news["news_id"] ?>"><?= $news["news_title"] ?></a></h2>
        <p><?= $news["news_summary"] ?></p>
        <br>
        <?php if ($news["author"]): ?>
            <p>Author: <?= $news["author"] ?></p>
        <?php endif; ?>
        <p>Created: <em><?= $news["created_date"] ?></em></p>
        <p>Edited: <em><?= $news["edited_date"] ?></em></p>
    </div>
    <hr>
<?php endforeach ?>
<?php require VIEWS . "page_number.php" ?>
<?php require VIEWS . "news_amount.php" ?>
